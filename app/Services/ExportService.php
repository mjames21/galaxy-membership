<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\{
    Person, MemberRegistration, Stakeholder, Opportunity,
    Initiative, Organization, ExecutiveAssignment,
    Region, District, Constituency, Zone
};

class ExportService
{
    /**
     * Stream a CSV download safely.
     */
    private function streamCsv(string $filename, array $headers, \Closure $rowGenerator): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rowGenerator) {
            // Ensure no buffered output is sent before headers
            while (ob_get_level() > 0) { @ob_end_clean(); }
            @set_time_limit(0);

            $out = fopen('php://output', 'w');

            // Excel-friendly UTF-8 BOM
            fwrite($out, "\xEF\xBB\xBF");

            // header row
            fputcsv($out, $headers);

            try {
                foreach ($rowGenerator() as $row) {
                    // normalize values for CSV
                    foreach ($row as &$v) {
                        if ($v instanceof Carbon) { $v = $v->toDateTimeString(); }
                        elseif (is_bool($v))     { $v = $v ? '1' : '0'; }
                        elseif (is_array($v))    { $v = implode('; ', $v); }
                        elseif ($v === null)     { $v = ''; }
                        else                     { $v = (string) $v; }
                    }
                    unset($v);

                    fputcsv($out, $row);
                }
            } catch (\Throwable $e) {
                // keep response valid CSV even on error
                fputcsv($out, ['ERROR', $e->getMessage()]);
            } finally {
                fflush($out);
                fclose($out);
            }
        }, $filename, [
            'Content-Type'      => 'text/csv; charset=UTF-8',
            'Cache-Control'     => 'no-store, no-cache, must-revalidate',
            'Pragma'            => 'no-cache',
            'X-Accel-Buffering' => 'no', // nginx: disable buffering for streams
        ]);
    }

    /* ========================= PEOPLE ========================= */

    public function exportPeople(array $filters): StreamedResponse
    {
        $filename = 'people_' . now()->format('Ymd_His') . '.csv';
        $headers  = ['Person ID','First Name','Last Name','Other Names','Email','Phone','Address','Members #','Created At'];

        return $this->streamCsv($filename, $headers, function () use ($filters) {
            $q = Person::withCount('memberRegistrations');

            if (!empty($filters['has_members'])) {
                $q->whereHas('memberRegistrations', function ($m) use ($filters) {
                    if (!empty($filters['region_id'])) $m->where('region_id', $filters['region_id']);
                });
            }

            if (!empty($filters['q'])) {
                $like = '%'.$filters['q'].'%';
                $q->where(function($w) use ($like){
                    $w->where('first_name','like',$like)->orWhere('last_name','like',$like)
                      ->orWhere('other_names','like',$like)->orWhere('email','like',$like)
                      ->orWhere('phone','like',$like)->orWhere('address','like',$like);
                });
            }

            if (!empty($filters['created_from']) && !empty($filters['created_to'])) {
                $q->whereBetween('people.created_at', [
                    Carbon::parse($filters['created_from'])->startOfDay(),
                    Carbon::parse($filters['created_to'])->endOfDay(),
                ]);
            }

            $sort = $filters['sort'] ?? 'last_name';
            $dir  = ($filters['dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
            $q->orderBy($sort, $dir);

            return $q->cursor()->map(function ($p) {
                return [
                    $p->id, $p->first_name, $p->last_name, $p->other_names,
                    $p->email, $p->phone, $p->address,
                    $p->member_registrations_count,
                    optional($p->created_at)?->toDateTimeString(),
                ];
            });
        });
    }

    /* ====================== STAKEHOLDERS ====================== */

    public function exportStakeholders(array $filters): StreamedResponse
    {
        $filename = 'stakeholders_' . now()->format('Ymd_His') . '.csv';
        $headers  = [
            'Stakeholder ID','Person','Category','Organization','Affiliation','Willingness',
            'Support Types','Region','District','Constituency','Zone','Created At'
        ];

        return $this->streamCsv($filename, $headers, function () use ($filters) {
            $q = Stakeholder::with([
                'person','category','organization','partyAffiliation','willingness',
                'supportTypes','district.region','constituency','zone'
            ]);

            if (!empty($filters['q'])) {
                $like = '%'.$filters['q'].'%';
                $q->where(function($w) use ($like){
                    $w->whereHas('person', fn($p)=>$p->where('first_name','like',$like)
                        ->orWhere('last_name','like',$like)->orWhere('other_names','like',$like)
                        ->orWhere('phone','like',$like)->orWhere('email','like',$like))
                      ->orWhereHas('organization', fn($o)=>$o->where('name','like',$like));
                });
            }

            if (!empty($filters['category_id']))          $q->where('stakeholder_category_id',$filters['category_id']);
            if (!empty($filters['affiliation_id']))       $q->where('party_affiliation_id',$filters['affiliation_id']);
            if (!empty($filters['willingness_level_id'])) $q->where('willingness_level_id',$filters['willingness_level_id']);
            if (!empty($filters['support_type_id']))      $q->whereHas('supportTypes', fn($s)=>$s->where('support_types.id',$filters['support_type_id']));

            if (!empty($filters['region_id']))       $q->whereHas('district', fn($d)=>$d->where('region_id',$filters['region_id']));
            if (!empty($filters['district_id']))     $q->where('district_id',$filters['district_id']);
            if (!empty($filters['constituency_id'])) $q->where('constituency_id',$filters['constituency_id']);
            if (!empty($filters['zone_id']))         $q->where('zone_id',$filters['zone_id']);

            if (!empty($filters['created_from']) && !empty($filters['created_to'])) {
                $q->whereBetween('stakeholders.created_at', [
                    Carbon::parse($filters['created_from'])->startOfDay(),
                    Carbon::parse($filters['created_to'])->endOfDay(),
                ]);
            }

            $q->latest();

            return $q->cursor()->map(function ($s) {
                return [
                    $s->id,
                    trim(($s->person->last_name ?? '').' '.($s->person->first_name ?? '')),
                    $s->category->name ?? '',
                    $s->organization->name ?? '',
                    $s->partyAffiliation->name ?? '',
                    $s->willingness->name ?? '',
                    $s->supportTypes->pluck('name')->implode('; '),
                    $s->district?->region?->name ?? '',
                    $s->district?->name ?? '',
                    $s->constituency?->name ?? '',
                    $s->zone?->name ?? '',
                    optional($s->created_at)?->toDateTimeString(),
                ];
            });
        });
    }

    /* ===================== OPPORTUNITIES ====================== */

    public function exportOpportunities(array $filters): StreamedResponse
    {
        $filename = 'opportunities_' . now()->format('Ymd_His') . '.csv';
        $headers  = ['Opportunity ID','Name','Type','Organization','Prospect','Contacts #','Updated At','Eligibility'];

        return $this->streamCsv($filename, $headers, function () use ($filters) {
            $q = Opportunity::with(['type','organization','contacts']);

            if (!empty($filters['q'])) {
                $like = '%'.$filters['q'].'%';
                $q->where(fn($w)=>$w->where('name','like',$like)->orWhere('eligibility_criteria','like',$like));
            }
            if (!empty($filters['type_id'])) $q->where('opportunity_type_id',$filters['type_id']);
            if (!empty($filters['org_id']))  $q->where('organization_id',$filters['org_id']);
            if (isset($filters['prospect']) && $filters['prospect']!=='') $q->where('prospect',$filters['prospect']);

            if (!empty($filters['updated_from']) && !empty($filters['updated_to'])) {
                $q->whereBetween('opportunities.updated_at', [
                    Carbon::parse($filters['updated_from'])->startOfDay(),
                    Carbon::parse($filters['updated_to'])->endOfDay(),
                ]);
            }

            $q->latest();

            return $q->cursor()->map(function ($o) {
                return [
                    $o->id,
                    $o->name,
                    $o->type->name ?? '',
                    $o->organization->name ?? '',
                    $o->prospect,
                    $o->contacts->count(),
                    optional($o->updated_at)?->toDateTimeString(),
                    $o->eligibility_criteria,
                ];
            });
        });
    }

    /* ======================= INITIATIVES ====================== */

    public function exportInitiatives(array $filters): StreamedResponse
    {
        $filename = 'initiatives_' . now()->format('Ymd_His') . '.csv';
        $headers  = ['Initiative ID','Name','Category','Status','Lead','Zones #','Secured Sponsors #','Targeted Sponsors #','Created At','Brief'];

        return $this->streamCsv($filename, $headers, function () use ($filters) {
            $q = Initiative::with(['category','status','lead','zones.constituency.district.region','sponsors']);

            if (!empty($filters['q'])) {
                $like = '%'.$filters['q'].'%';
                $q->where(fn($w)=>$w->where('name','like',$like)->orWhere('brief_description','like',$like));
            }
            if (!empty($filters['category_id'])) $q->where('initiative_category_id',$filters['category_id']);
            if (!empty($filters['status_id']))   $q->where('status_id',$filters['status_id']);

            if (!empty($filters['zone_id'])) {
                $q->whereHas('zones', fn($z)=>$z->where('zones.id',$filters['zone_id']));
            } elseif (!empty($filters['constituency_id'])) {
                $q->whereHas('zones', fn($z)=>$z->where('constituency_id',$filters['constituency_id']));
            } elseif (!empty($filters['district_id'])) {
                $q->whereHas('zones.constituency', fn($c)=>$c->where('district_id',$filters['district_id']));
            } elseif (!empty($filters['region_id'])) {
                $q->whereHas('zones.constituency.district', fn($d)=>$d->where('region_id',$filters['region_id']));
            }

            if (!empty($filters['created_from']) && !empty($filters['created_to'])) {
                $q->whereBetween('initiatives.created_at', [
                    Carbon::parse($filters['created_from'])->startOfDay(),
                    Carbon::parse($filters['created_to'])->endOfDay(),
                ]);
            }

            $q->latest();

            return $q->cursor()->map(function ($i) {
                $secured  = $i->sponsors->where('pivot.sponsor_status','secured')->count();
                $targeted = $i->sponsors->where('pivot.sponsor_status','targeted')->count();

                return [
                    $i->id, $i->name,
                    $i->category->name ?? '',
                    $i->status->name ?? '',
                    trim(($i->lead->last_name ?? '').' '.($i->lead->first_name ?? '')),
                    $i->zones->count(),
                    $secured, $targeted,
                    optional($i->created_at)?->toDateTimeString(),
                    $i->brief_description,
                ];
            });
        });
    }

    /* ===================== ORGANIZATIONS ====================== */

    public function exportOrganizations(array $filters): StreamedResponse
    {
        $filename = 'organizations_' . now()->format('Ymd_His') . '.csv';
        $headers  = ['Org ID','Name','Type','Affiliation Level','Contacts #','Email','Phone','Address','Description'];

        return $this->streamCsv($filename, $headers, function () use ($filters) {
            $q = Organization::with(['latestAffiliation.level','contacts']);

            if (!empty($filters['q'])) {
                $like = '%'.$filters['q'].'%';
                $q->where(function($w) use ($like){
                    $w->where('name','like',$like)->orWhere('email','like',$like)
                      ->orWhere('phone','like',$like)->orWhere('address','like',$like);
                });
            }
            if (isset($filters['type']) && $filters['type']!=='') $q->where('type',$filters['type']);
            if (!empty($filters['affiliation_level_id'])) {
                $q->whereHas('latestAffiliation', fn($a)=>$a->where('affiliation_level_id',$filters['affiliation_level_id']));
            }

            $q->orderBy('name');

            return $q->cursor()->map(function ($o) {
                return [
                    $o->id, $o->name, $o->type,
                    optional($o->latestAffiliation?->level)->name,
                    $o->contacts->count(), $o->email, $o->phone, $o->address, $o->description,
                ];
            });
        });
    }

    /* ======================= EXECUTIVES ======================= */

    public function exportExecutives(array $filters): StreamedResponse
    {
        $filename = 'executives_' . now()->format('Ymd_His') . '.csv';
        $headers  = ['Assignment ID','Person','Position','Scope Type','Scope Name','Term #','Start','End','Status'];

        return $this->streamCsv($filename, $headers, function () use ($filters) {
            $today = now()->toDateString();

            $q = ExecutiveAssignment::with(['person','position','scope']);

            if (!empty($filters['q'])) {
                $like = '%'.$filters['q'].'%';
                $q->where(function($qq) use ($like){
                    $qq->whereHas('person', fn($p)=>$p->where('first_name','like',$like)
                        ->orWhere('last_name','like',$like)->orWhere('other_names','like',$like))
                       ->orWhereHas('position', fn($pos)=>$pos->where('name','like',$like));
                });
            }

            $status = $filters['status'] ?? 'active';
            if ($status !== 'all') {
                if ($status === 'active') {
                    $q->where(fn($w)=>$w->whereNull('start_date')->orWhere('start_date','<=',$today))
                      ->where(fn($w)=>$w->whereNull('end_date')->orWhere('end_date','>=',$today));
                } else {
                    $q->whereNotNull('end_date')->where('end_date','<',$today);
                }
            }

            $map = [
                'region'       => Region::class,
                'district'     => District::class,
                'constituency' => Constituency::class,
                'zone'         => Zone::class,
            ];
            if (!empty($filters['scope_level']) && isset($map[$filters['scope_level']])) {
                $type = $map[$filters['scope_level']];
                $q->where('scope_type', $type);
                $idField = $filters[$filters['scope_level'].'_id'] ?? null;
                if ($idField) $q->where('scope_id', $idField);
            }

            $q->latest('start_date');

            return $q->cursor()->map(function ($e) use ($today) {
                $active = (!$e->start_date || $e->start_date <= $today) && (!$e->end_date || $e->end_date >= $today);
                $scopeType = class_basename($e->scope_type ?? '') ?: '';
                return [
                    $e->id,
                    trim(($e->person->last_name ?? '').' '.($e->person->first_name ?? '')),
                    $e->position->name ?? '',
                    $scopeType,
                    optional($e->scope)->name,
                    $e->term_number ?? 1,
                    $e->start_date,
                    $e->end_date,
                    $active ? 'Active' : 'Ended',
                ];
            });
        });
    }
}
