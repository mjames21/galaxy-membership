<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">
        {{ $memberRegistration ? 'Edit' : 'Create' }} Membership — {{ trim(($person->last_name ?? '').' '.($person->first_name ?? '')) }}
    </h1>

    @if (session('ok'))
        <div class="mb-3 text-sm bg-green-50 border border-green-200 text-green-800 px-3 py-2 rounded">{{ session('ok') }}</div>
    @endif

    <form wire:submit.prevent="save" class="bg-white border rounded p-4 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Reg Number</label>
                <input type="text" wire:model.defer="registration_number" class="border rounded w-full px-3 py-2">
                @error('registration_number')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm mb-1">Reg Year</label>
                <input type="number" wire:model.defer="registration_year" class="border rounded w-full px-3 py-2">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm mb-1">Region</label>
                <select wire:model="region_id" class="border rounded w-full px-3 py-2">
                    <option value="">—</option>
                    @foreach ($regions as $r) <option value="{{ $r['id'] }}">{{ $r['name'] }}</option> @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">District</label>
                <select wire:model="district_id" class="border rounded w-full px-3 py-2">
                    <option value="">—</option>
                    @foreach ($districts as $d) <option value="{{ $d['id'] }}">{{ $d['name'] }}</option> @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Constituency</label>
                <select wire:model="constituency_id" class="border rounded w-full px-3 py-2">
                    <option value="">—</option>
                    @foreach ($constituencies as $c) <option value="{{ $c['id'] }}">{{ $c['name'] }}</option> @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Zone</label>
                <select wire:model="zone_id" class="border rounded w-full px-3 py-2">
                    <option value="">—</option>
                    @foreach ($zones as $z) <option value="{{ $z['id'] }}">{{ $z['name'] }}</option> @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            <a href="{{ route('members.index', $person) }}" class="px-4 py-2 border rounded">Cancel</a>
        </div>
    </form>
</div>
