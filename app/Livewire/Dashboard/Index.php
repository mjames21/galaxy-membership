<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\{
    Person,
    MemberRegistration,
    Stakeholder,
    Opportunity,
    Initiative,
    ExecutiveAssignment
};

class Index extends Component
{
    /** Filters */
    public string  $period = 'last_30'; // today|last_7|last_30|quarter|year|custom
    public ?string $from   = null;
    public ?string $to     = null;

    /** KPI cards */
    public array $stats = [
        'people'        => 0,
        'members'       => 0,
        'stakeholders'  => 0,
        'opportunities' => 0,
        'initiatives'   => 0,
        'exec_active'   => 0,
    ];

    /** Charts */
    public array $prospectLabels = [];
    public array $prospectData   = [];

    public array $regionLabels   = [];
    public array $regionData     = [];

    public array $willLabels     = [];
    public array $willData       = [];

    /* -------------------------- Lifecycle -------------------------- */

    public function mount(): void
    {
        [$start, $end] = $this->computeRange();
        $this->from = $start?->toDateString();
        $this->to   = $end?->toDateString();
        $this->hydrateAll();
    }

    public function updatedPeriod(): void
    {
        if ($this->period !== 'custom') {
            [$start, $end] = $this->computeRange();
            $this->from = $start?->toDateString();
            $this->to   = $end?->toDateString();
        }
        $this->hydrateAll();
    }

    public function updatedFrom(): void
    {
        $this->period = 'custom';
        $this->hydrateAll();
    }

    public function updatedTo(): void
    {
        $this->period = 'custom';
        $this->hydrateAll();
    }

    /* -------------------------- Helpers --------------------------- */

    private function computeRange(): array
    {
        $today = Carbon::today();

        return match ($this->period) {
            'today'   => [$today->copy(), $today->copy()],
            'last_7'  => [$today->copy()->subDays(6), $today->copy()],
            'last_30' => [$today->copy()->subDays(29), $today->copy()],
            'quarter' => [$today->copy()->firstOfQuarter(), $today->copy()],
            'year'    => [$today->copy()->firstOfYear(), $today->copy()],
            default   => [
                $this->from ? Carbon::parse($this->from) : null,
                $this->to   ? Carbon::parse($this->to)   : null,
            ],
        };
    }

    private function endOfDay(Carbon $c): Carbon
    {
        return $c->copy()->endOfDay();
    }

    /* ------------------------- Data loader ------------------------ */

    private function hydrateAll(): void
    {
        [$start, $end] = $this->from && $this->to
            ? [Carbon::parse($this->from), Carbon::parse($this->to)]
            : $this->computeRange();

        // 1) KPI cards
        $this->stats = [
            'people'        => Person::when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $this->endOfDay($end)]))->count(),
            'members'       => MemberRegistration::when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $this->endOfDay($end)]))->count(),
            'stakeholders'  => Stakeholder::when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $this->endOfDay($end)]))->count(),
            'opportunities' => Opportunity::when($start && $end, fn($q) => $q->whereBetween('updated_at', [$start, $this->endOfDay($end)]))->count(),
            'initiatives'   => Initiative::when($start && $end, fn($q) => $q->whereBetween('created_at', [$start, $this->endOfDay($end)]))->count(),
            'exec_active'   => $this->countActiveExecutives(),
        ];

        // 2) Opportunities by prospect (fixed bucket order)
        $order = ['certain','high','medium','low','none'];
        $rawPros = Opportunity::when($start && $end, fn($q) => $q->whereBetween('updated_at', [$start, $this->endOfDay($end)]))
            ->select('prospect', DB::raw('COUNT(*) as c'))
            ->groupBy('prospect')
            ->pluck('c', 'prospect')
            ->toArray();

        $this->prospectLabels = array_map('ucfirst', $order);
        $this->prospectData   = array_map(fn($k) => (int)($rawPros[$k] ?? 0), $order);

        // 3) Top regions by new members (Postgres-safe, no ambiguous columns)
        $regions = DB::table('member_registrations as mr')
            ->leftJoin('regions as r', 'r.id', '=', 'mr.region_id')
            ->when($start && $end, fn($q) => $q->whereBetween('mr.created_at', [$start, $this->endOfDay($end)]))
            ->selectRaw('r.id as rid, COALESCE(r.name, ?) as label, COUNT(*) as c', ['Unspecified'])
            ->groupBy('r.id', 'r.name') // qualify to avoid ambiguity
            ->orderByDesc('c')
            ->limit(10)
            ->get();

        $this->regionLabels = $regions->pluck('label')->all();
        $this->regionData   = $regions->pluck('c')->map(fn($v) => (int)$v)->all();

        // 4) Stakeholders by willingness (group by column; render with label fallback)
        $will = DB::table('stakeholders as s')
            ->leftJoin('willingness_levels as w', 'w.id', '=', 's.willingness_level_id')
            ->when($start && $end, fn($q) => $q->whereBetween('s.created_at', [$start, $this->endOfDay($end)]))
            ->select('w.name', DB::raw('COUNT(*) as c'))
            ->groupBy('w.name')
            ->orderByDesc('c')
            ->get()
            ->map(fn($row) => (object)[
                'label' => $row->name ?? 'Unspecified',
                'c'     => (int)$row->c,
            ]);

        $this->willLabels = $will->pluck('label')->all();
        $this->willData   = $will->pluck('c')->all();
    }

    private function countActiveExecutives(): int
    {
        $today = now()->toDateString();

        return ExecutiveAssignment::where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->count();
    }

    public function render()
    {
        return view('livewire.dashboard.index')->layout('layouts.app');

    }
}
