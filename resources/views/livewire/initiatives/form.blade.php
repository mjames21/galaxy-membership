<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">{{ $initiative ? 'Edit' : 'Create' }} Initiative</h1>

    @if (session('ok'))
        <div class="mb-3 text-sm bg-green-50 border border-green-200 text-green-800 px-3 py-2 rounded">{{ session('ok') }}</div>
    @endif

    <form wire:submit.prevent="save" class="bg-white border rounded p-4 space-y-6">
        <div>
            <label class="block text-sm mb-1">Name *</label>
            <input type="text" wire:model.defer="name" class="border rounded w-full px-3 py-2">
            @error('name')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm mb-1">Brief Description</label>
            <textarea wire:model.defer="brief_description" rows="3" class="border rounded w-full px-3 py-2"></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm mb-1">Category</label>
                <select wire:model="initiative_category_id" class="border rounded w-full px-3 py-2">
                    <option value="">—</option>
                    @foreach ($categories as $c) <option value="{{ $c['id'] }}">{{ $c['name'] }}</option> @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Status</label>
                <select wire:model="status_id" class="border rounded w-full px-3 py-2">
                    <option value="">—</option>
                    @foreach ($statuses as $s) <option value="{{ $s['id'] }}">{{ $s['name'] }}</option> @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Lead</label>
                <select wire:model="lead_id" class="border rounded w-full px-3 py-2">
                    <option value="">—</option>
                    @foreach ($people as $p)
                        <option value="{{ $p['id'] }}">{{ trim(($p['last_name'] ?? '').' '.($p['first_name'] ?? '')) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">Zones (multi)</label>
                <select wire:model="zone_ids" multiple size="8" class="border rounded w-full px-3 py-2">
                    @foreach ($zones as $z) <option value="{{ $z['id'] }}">{{ $z['name'] }}</option> @endforeach
                </select>
            </div>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm mb-1">Sponsors — Targeted</label>
                    <select wire:model="sponsors_targeted" multiple size="4" class="border rounded w-full px-3 py-2">
                        @foreach ($orgs as $o) <option value="{{ $o['id'] }}">{{ $o['name'] }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm mb-1">Sponsors — Secured</label>
                    <select wire:model="sponsors_secured" multiple size="4" class="border rounded w-full px-3 py-2">
                        @foreach ($orgs as $o) <option value="{{ $o['id'] }}">{{ $o['name'] }}</option> @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            <a href="{{ route('initiatives.index') }}" class="px-4 py-2 border rounded">Cancel</a>
        </div>
    </form>
</div>
