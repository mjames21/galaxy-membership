<?php

namespace App\Livewire\Members;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\{Person, MemberRegistration};

#[Layout('layouts.app')]
class Form extends Component
{
    /** Hardcoded cascading map. Keys are IDs/values shown in dropdowns. */
    private const LOCATION_MAP = [
        // region_id => ['name' => '...', 'districts' => [ district_id => ['name'=>..., 'constituencies' => [ constituency_id => ['name'=>..., 'zones' => [ zone_id => 'Zone Name', ... ] ] ] ] ] ]
        1 => [
            'name' => 'Eastern',
            'districts' => [
                11 => [
                    'name' => 'Alpha District',
                    'constituencies' => [
                        111 => [
                            'name' => 'Alpha North',
                            'zones' => [
                                1111 => 'Zone A1',
                                1112 => 'Zone A2',
                            ],
                        ],
                        112 => [
                            'name' => 'Alpha South',
                            'zones' => [
                                1121 => 'Zone A3',
                            ],
                        ],
                    ],
                ],
                12 => [
                    'name' => 'Beta District',
                    'constituencies' => [
                        121 => [
                            'name' => 'Beta Central',
                            'zones' => [
                                1211 => 'Zone B1',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        2 => [
            'name' => 'Western',
            'districts' => [
                21 => [
                    'name' => 'Gamma District',
                    'constituencies' => [
                        211 => [
                            'name' => 'Gamma East',
                            'zones' => [
                                2111 => 'Zone G1',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    public Person $person;
    public ?MemberRegistration $memberRegistration = null;

    /** optional when embedding as modal */
    public ?int $memberRegistrationId = null;
    public bool $asModal = false;

    #[Validate('required|string|max:60|unique:member_registrations,registration_number')]
    public ?string $registration_number = null;

    #[Validate('required|integer|min:1900|max:2100')]
    public ?int $registration_year = null;

    // these will hold the selected IDs from the map
    #[Validate('nullable|integer')]
    public ?int $region_id = null;

    #[Validate('nullable|integer')]
    public ?int $district_id = null;

    #[Validate('nullable|integer')]
    public ?int $constituency_id = null;

    #[Validate('nullable|integer')]
    public ?int $zone_id = null;

    /** dropdown option arrays for the view */
    public array $regions = [];
    public array $districts = [];
    public array $constituencies = [];
    public array $zones = [];

    protected array $rules = []; // dynamic unique on update

    public function mount(Person $person, ?MemberRegistration $memberRegistration = null): void
    {
        $this->person = $person;

        if (!$memberRegistration && $this->memberRegistrationId) {
            $memberRegistration = MemberRegistration::where('person_id', $person->id)
                ->find($this->memberRegistrationId);
        }
        $this->memberRegistration = $memberRegistration;

        // seed region options from the map
        $this->regions = $this->mapRegions();

        if ($memberRegistration) {
            $this->registration_number = $memberRegistration->registration_number;
            $this->registration_year   = $memberRegistration->registration_year;

            $this->region_id       = $memberRegistration->region_id;
            $this->district_id     = $memberRegistration->district_id;
            $this->constituency_id = $memberRegistration->constituency_id;
            $this->zone_id         = $memberRegistration->zone_id;

            $this->districts      = $this->mapDistricts($this->region_id);
            $this->constituencies = $this->mapConstituencies($this->region_id, $this->district_id);
            $this->zones          = $this->mapZones($this->region_id, $this->district_id, $this->constituency_id);
        } else {
            $this->registration_year ??= (int) now()->year;
        }
    }

    /** ---- Cascading updates (no DB) ---- */
    public function updatedRegionId(): void
    {
        $this->districts      = $this->mapDistricts($this->region_id);
        $this->district_id = $this->constituency_id = $this->zone_id = null;
        $this->constituencies = [];
        $this->zones = [];
    }

    public function updatedDistrictId(): void
    {
        $this->constituencies = $this->mapConstituencies($this->region_id, $this->district_id);
        $this->constituency_id = $this->zone_id = null;
        $this->zones = [];
    }

    public function updatedConstituencyId(): void
    {
        $this->zones = $this->mapZones($this->region_id, $this->district_id, $this->constituency_id);
        $this->zone_id = null;
    }

    /** ---- Save ---- */
    public function save()
    {
        // relax unique on update
        if ($this->memberRegistration) {
            $this->rules['registration_number'] =
                'required|string|max:60|unique:member_registrations,registration_number,' .
                $this->memberRegistration->id . ',id';
        }

        // Ensure selected IDs exist in map (simple guard)
        $this->validate();
        if ($this->region_id && !isset(self::LOCATION_MAP[$this->region_id])) $this->addError('region_id', 'Invalid region.');
        if ($this->district_id && !isset(self::LOCATION_MAP[$this->region_id]['districts'][$this->district_id])) $this->addError('district_id', 'Invalid district.');
        if ($this->constituency_id && !isset(self::LOCATION_MAP[$this->region_id]['districts'][$this->district_id]['constituencies'][$this->constituency_id])) $this->addError('constituency_id', 'Invalid constituency.');
        if ($this->zone_id && !isset(self::LOCATION_MAP[$this->region_id]['districts'][$this->district_id]['constituencies'][$this->constituency_id]['zones'][$this->zone_id])) $this->addError('zone_id', 'Invalid zone.');
        if ($this->getErrorBag()->isNotEmpty()) return;

        $payload = [
            'person_id'           => $this->person->id,
            'registration_number' => $this->registration_number,
            'registration_year'   => $this->registration_year,
            'region_id'           => $this->region_id,
            'district_id'         => $this->district_id,
            'constituency_id'     => $this->constituency_id,
            'zone_id'             => $this->zone_id,
        ];

        if ($this->memberRegistration) {
            $this->memberRegistration->update($payload);
        } else {
            $this->memberRegistration = MemberRegistration::create($payload);
        }

        session()->flash('ok','Membership saved.');

        if ($this->asModal) {
            $this->dispatch('close-form-modal');   // close overlay (Alpine listens)
            $this->dispatch('membership-saved');   // notify parent (Index listens)
            return;
        }

        return redirect()->route('members.index', $this->person);
    }

    public function render()
    {
        return view('livewire.members.form')
            ->title(($this->memberRegistration ? 'Edit' : 'Create') . ' Membership');
    }

    /** ---- Mapping helpers ---- */
    private function mapRegions(): array
    {
        $out = [];
        foreach (self::LOCATION_MAP as $id => $data) $out[] = ['id' => $id, 'name' => $data['name']];
        return $out;
    }

    private function mapDistricts(?int $regionId): array
    {
        if (!$regionId || !isset(self::LOCATION_MAP[$regionId])) return [];
        $out = [];
        foreach (self::LOCATION_MAP[$regionId]['districts'] as $id => $d) $out[] = ['id' => $id, 'name' => $d['name']];
        return $out;
    }

    private function mapConstituencies(?int $regionId, ?int $districtId): array
    {
        if (!$regionId || !$districtId) return [];
        $d = self::LOCATION_MAP[$regionId]['districts'][$districtId] ?? null;
        if (!$d) return [];
        $out = [];
        foreach ($d['constituencies'] as $id => $c) $out[] = ['id' => $id, 'name' => $c['name']];
        return $out;
    }

    private function mapZones(?int $regionId, ?int $districtId, ?int $constituencyId): array
    {
        if (!$regionId || !$districtId || !$constituencyId) return [];
        $c = self::LOCATION_MAP[$regionId]['districts'][$districtId]['constituencies'][$constituencyId] ?? null;
        if (!$c) return [];
        $out = [];
        foreach ($c['zones'] as $id => $name) $out[] = ['id' => $id, 'name' => $name];
        return $out;
    }
}
