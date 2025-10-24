<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">{{ $organization ? 'Edit Organization' : 'Create Organization' }}</h1>

    @if (session('ok'))
        <div class="mb-3 text-sm bg-green-50 border border-green-200 text-green-800 px-3 py-2 rounded">
            {{ session('ok') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-6">
        <!-- Core -->
        <div class="bg-white border rounded p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm mb-1">Name</label>
                    <input type="text" wire:model.defer="name" class="border rounded w-full px-3 py-2">
                    @error('name')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm mb-1">Type</label>
                    <input type="text" wire:model.defer="type" class="border rounded w-full px-3 py-2" placeholder="e.g., NGO, Company, Agency">
                </div>
                <div>
                    <label class="block text-sm mb-1">Email</label>
                    <input type="email" wire:model.defer="email" class="border rounded w-full px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm mb-1">Phone</label>
                    <input type="text" wire:model.defer="phone" class="border rounded w-full px-3 py-2">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-1">Address</label>
                    <input type="text" wire:model.defer="address" class="border rounded w-full px-3 py-2">
                </div>
            </div>
            <div>
                <label class="block text-sm mb-1">Description</label>
                <textarea wire:model.defer="description" rows="3" class="border rounded w-full px-3 py-2"></textarea>
            </div>
        </div>

        <!-- Contacts -->
        <div class="bg-white border rounded p-4 space-y-3">
            <h2 class="font-semibold">Contacts</h2>
            <div class="space-y-2">
                @foreach ($contacts as $i => $row)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                        <div>
                            <label class="block text-sm mb-1">Person</label>
                            <select wire:model="contacts.{{ $i }}.person_id" class="border rounded w-full px-3 py-2">
                                <option value="">Select…</option>
                                @foreach ($people as $p)
                                    <option value="{{ $p['id'] }}">{{ trim(($p['last_name'] ?? '').' '.($p['first_name'] ?? '')) }}</option>
                                @endforeach
                            </select>
                            @error('contacts.'.$i.'.person_id')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Designation</label>
                            <input type="text" wire:model="contacts.{{ $i }}.designation" class="border rounded w-full px-3 py-2">
                            @error('contacts.'.$i.'.designation')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="flex items-end">
                            <button type="button" wire:click="removeContactRow({{ $i }})" class="px-3 py-2 border rounded">Remove</button>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="button" wire:click="addContactRow" class="px-3 py-2 bg-gray-100 border rounded">+ Add contact</button>
        </div>

        <!-- Affiliation -->
        <div class="bg-white border rounded p-4 space-y-3">
            <h2 class="font-semibold">Affiliation</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm mb-1">Level</label>
                    <select wire:model="affiliation_level_id" class="border rounded w-full px-3 py-2">
                        <option value="">—</option>
                        @foreach ($levels as $l) <option value="{{ $l['id'] }}">{{ $l['name'] }}</option> @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm mb-1">Description</label>
                    <input type="text" wire:model.defer="affiliation_description" class="border rounded w-full px-3 py-2" placeholder="e.g., aligned with X">
                </div>
            </div>
            <div>
                <label class="block text-sm mb-1">Notes</label>
                <textarea wire:model.defer="affiliation_notes" rows="3" class="border rounded w-full px-3 py-2"></textarea>
            </div>
            <p class="text-xs text-gray-500">A new snapshot will be recorded if the level/description/notes changed.</p>
        </div>

        <div class="flex items-center gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            <a href="{{ route('organizations.index') }}" class="px-4 py-2 border rounded">Cancel</a>
        </div>
    </form>
</div>
