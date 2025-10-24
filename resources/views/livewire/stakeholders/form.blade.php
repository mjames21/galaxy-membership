<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">{{ $stakeholder ? 'Edit' : 'Create' }} Stakeholder</h1>

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
                <label class="block text-sm mb-1">Category *</label>
                <select wire:model="stakeholder_category_id" class="border rounded w-full px-3 py-2">
                    <option value="">Select…</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                    @endforeach
                </select>
                @error('stakeholder_category_id')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>

            <div>
                <label class="block text-sm mb-1">Organization</label>
                <select wire:model="organization_id" class="border rounded w-full px-3 py-2">
                    <option value="">—</option>
                    @foreach ($orgs as $o) <option value="{{ $o['id'] }}">{{ $o['name'] }}</option> @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Party Affiliation</label>
                <select wire:model="party_affiliation_id" class="border rounded w-full px-3 py-2">
                    <option value="">—</option>
                    @foreach ($affils as $a) <option value="{{ $a['id'] }}">{{ $a['name'] }}</option> @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm mb-1">Willingness</label>
                <select wire:model="willingness_level_id" class="border rounded w-full px-3 py-2">
                    <option value="">—</option>
                    @foreach ($levels as $l) <option value="{{ $l['id'] }}">{{ $l['name'] }}</option> @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm mb-1">Support Types</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                @foreach ($supportTypes as $t)
                    <label class="inline-flex items-center gap-2 text-sm">
                        <input type="checkbox" value="{{ $t['id'] }}" wire:model="support_type_ids">
                        {{ $t['name'] }}
                    </label>
                @endforeach
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
            <a href="{{ route('stakeholders.index') }}" class="px-4 py-2 border rounded">Cancel</a>
        </div>
    </form>
</div>
