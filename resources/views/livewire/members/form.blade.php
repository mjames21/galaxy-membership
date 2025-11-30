<div class="max-w-3xl mx-auto p-0">
    <form wire:submit.prevent="save" class="bg-white rounded p-0 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Reg Number</label>
                <input type="text" wire:model.defer="registration_number" class="border rounded w-full px-3 py-2">
                @error('registration_number')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm mb-1">Reg Year</label>
                <input type="number" wire:model.defer="registration_year" class="border rounded w-full px-3 py-2">
                @error('registration_year')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

       {{-- replace your 4 dropdowns with this --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
  <div>
    <label class="block text-sm mb-1">Region</label>
    <select
      wire:model.live="region_id"
      wire:key="region"
      class="border rounded w-full px-3 py-2"
    >
      <option value="">—</option>
      @foreach ($regions as $r)
        <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>
      @endforeach
    </select>
    @error('region_id')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm mb-1">District</label>
    <select
      wire:model.live="district_id"
      wire:key="district-{{ (int) $region_id }}"
      @disabled(empty($districts))
      wire:loading.attr="disabled"
      class="border rounded w-full px-3 py-2"
    >
      <option value="">—</option>
      @foreach ($districts as $d)
        <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
      @endforeach
    </select>
    @error('district_id')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm mb-1">Constituency</label>
    <select
      wire:model.live="constituency_id"
      wire:key="const-{{ (int) $district_id }}"
      @disabled(empty($constituencies))
      wire:loading.attr="disabled"
      class="border rounded w-full px-3 py-2"
    >
      <option value="">—</option>
      @foreach ($constituencies as $c)
        <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
      @endforeach
    </select>
    @error('constituency_id')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm mb-1">Zone</label>
    <select
      wire:model.live="zone_id"
      wire:key="zone-{{ (int) $constituency_id }}"
      @disabled(empty($zones))
      wire:loading.attr="disabled"
      class="border rounded w-full px-3 py-2"
    >
      <option value="">—</option>
      @foreach ($zones as $z)
        <option value="{{ $z['id'] }}">{{ $z['name'] }}</option>
      @endforeach
    </select>
    @error('zone_id')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
  </div>
</div>


        <div class="flex items-center gap-2 pt-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            @if (!$asModal)
                <a href="{{ route('members.index', $person) }}" class="px-4 py-2 border rounded">Cancel</a>
            @endif
        </div>
    </form>
</div>
