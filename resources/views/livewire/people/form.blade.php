<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">{{ $person ? 'Edit Person' : 'Create Person' }}</h1>

    @if (session('ok'))
        <div class="mb-3 text-sm bg-green-50 border border-green-200 text-green-800 px-3 py-2 rounded">{{ session('ok') }}</div>
    @endif

    <form wire:submit.prevent="save" class="bg-white border rounded p-4 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">First Name *</label>
                <input type="text" wire:model.defer="first_name" class="border rounded w-full px-3 py-2">
                @error('first_name')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm mb-1">Last Name</label>
                <input type="text" wire:model.defer="last_name" class="border rounded w-full px-3 py-2">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">Other Names</label>
                <input type="text" wire:model.defer="other_names" class="border rounded w-full px-3 py-2">
            </div>

            <div>
                <label class="block text-sm mb-1">Email</label>
                <input type="email" wire:model.defer="email" class="border rounded w-full px-3 py-2">
                @error('email')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-sm mb-1">Phone</label>
                <input type="text" wire:model.defer="phone" class="border rounded w-full px-3 py-2">
                @error('phone')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">Address</label>
                <input type="text" wire:model.defer="address" class="border rounded w-full px-3 py-2">
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            <a href="{{ route('people.index') }}" class="px-4 py-2 border rounded">Cancel</a>
        </div>
    </form>
</div>
