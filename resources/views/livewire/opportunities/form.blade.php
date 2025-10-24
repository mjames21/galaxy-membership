<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">{{ $opportunity ? 'Edit' : 'Create' }} Opportunity</h1>

    @if (session('ok'))
        <div class="mb-3 text-sm bg-green-50 border border-green-200 text-green-800 px-3 py-2 rounded">{{ session('ok') }}</div>
    @endif

    <form wire:submit.prevent="save" class="bg-white border rounded p-4 space-y-6">
        <div>
            <label class="block text-sm mb-1">Name *</label>
            <input type="text" wire:model.defer="name" class="border rounded w-full px-3 py-2">
            @error('name')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm mb-1">Type</label>
                <select wire:model="opportunity_type_id" class="border rounded w-full px-3 py-2">
                    <option value="">—</option>
                    @foreach ($types as $t) <option value="{{ $t['id'] }}">{{ $t['name'] }}</option> @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Organization</label>
                <select wire:model="organization_id" class="border rounded w-full px-3 py-2">
                    <option value="">—</option>
                    @foreach ($orgs as $o) <option value="{{ $o['id'] }}">{{ $o['name'] }}</option> @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm mb-1">Prospect</label>
                <select wire:model="prospect" class="border rounded w-full px-3 py-2">
                    <option value="">—</option>
                    <option value="certain">Certain</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                    <option value="none">None</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm mb-1">Eligibility Criteria</label>
            <textarea wire:model.defer="eligibility_criteria" rows="4" class="border rounded w-full px-3 py-2"></textarea>
        </div>

        <div>
            <label class="block text-sm mb-1">Contacts (People)</label>
            <select wire:model="contact_ids" multiple size="8" class="border rounded w-full px-3 py-2">
                @foreach ($people as $p)
                    <option value="{{ $p['id'] }}">{{ trim(($p['last_name'] ?? '').' '.($p['first_name'] ?? '')) }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            <a href="{{ route('opportunities.index') }}" class="px-4 py-2 border rounded">Cancel</a>
        </div>
    </form>
</div>
