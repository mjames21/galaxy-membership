<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">{{ $executiveAssignment ? 'Edit Executive' : 'Create Executive' }}</h1>

    @if (session('ok'))
        <div class="mb-3 text-sm bg-green-50 border border-green-200 text-green-800 px-3 py-2 rounded">{{ session('ok') }}</div>
    @endif

    <form wire:submit.prevent="save" class="bg-white border rounded p-4 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Person *</label>
                <select wire:model="person_id" class="border rounded w-full px-3 py-2">
                    <option value="">Select…</option>
                    @foreach ($people as $p)
                        <option value="{{ $p['id'] }}">{{ trim(($p['last_name'] ?? '').' '.($p['first_name'] ?? '')) }}</option>
                    @endforeach
                </select>
                @error('person_id')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm mb-1">Position *</label>
                <select wire:model="position_id" class="border rounded w-full px-3 py-2">
                    <option value="">Select…</option>
                    @foreach ($positions as $pos)
                        <option value="{{ $pos['id'] }}">{{ $pos['name'] }}</option>
                    @endforeach
                </select>
                @error('position_id')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm mb-1">Scope Level</label>
                <select wire:model="scope_level" class="border rounded w-full px-3 py-2">
                    <option value="">—</option>
                    <option value="region">Region</option>
                    <option value="district">District</option>
                    <option value="constituency">Constituency</option>
                    <option value="zone">Zone</option>
                </select>
            </div>
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
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm mb-1">Zone</label>
                <select wire:model="zone_id" class="border rounded w-full px-3 py-2">
                    <option value="">—</option>
                    @foreach ($zones as $z) <option value="{{ $z['id'] }}">{{ $z['name'] }}</option> @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Start Date</label>
                <input type="date" wire:model.defer="start_date" class="border rounded w-full px-3 py-2">
            </div>
            <div>
                <label class="block text-sm mb-1">End Date</label>
                <input type="date" wire:model.defer="end_date" class="border rounded w-full px-3 py-2">
            </div>
        </div>

        <div>
            <label class="block text-sm mb-1">Term #</label>
            <input type="number" wire:model.defer="term_number" class="border rounded w-full px-3 py-2" min="1" max="10">
        </div>

        <div class="flex items-center gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            <a href="{{ route('executives.index') }}" class="px-4 py-2 border rounded">Cancel</a>
        </div>
    </form>
</div>
