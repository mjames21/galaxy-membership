<div class="max-w-7xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Organizations</h1>
        <div class="flex gap-2">
            <a href="{{ route('organizations.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">+ New</a>
            <a class="px-3 py-2 border rounded bg-white hover:bg-gray-50"
               href="{{ route('export.organizations', ['q'=>$q,'type'=>$type,'affiliation_level_id'=>$affiliation_level_id]) }}">
                Export CSV
            </a>
        </div>
    </div>

    <div class="bg-white border rounded p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs text-gray-600">Search</label>
                <input type="text" wire:model.debounce.400ms="q" class="border rounded w-full px-3 py-2" placeholder="Name/email/phone/address">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Type</label>
                <input type="text" wire:model.lazy="type" class="border rounded w-full px-3 py-2" placeholder="e.g., NGO">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Affiliation Level ID</label>
                <input type="text" wire:model.lazy="affiliation_level_id" class="border rounded w-full px-3 py-2">
            </div>
        </div>
    </div>

    <div class="overflow-x-auto bg-white border rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">Name</th>
                    <th class="px-3 py-2 text-left">Type</th>
                    <th class="px-3 py-2 text-left">Affiliation</th>
                    <th class="px-3 py-2 text-left">Contacts</th>
                    <th class="px-3 py-2 text-left">Email</th>
                    <th class="px-3 py-2 text-left">Phone</th>
                    <th class="px-3 py-2 text-left">Address</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $o)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $o->name }}</td>
                        <td class="px-3 py-2">{{ $o->type }}</td>
                        <td class="px-3 py-2">{{ optional($o->latestAffiliation?->level)->name }}</td>
                        <td class="px-3 py-2">{{ $o->contacts->count() }}</td>
                        <td class="px-3 py-2">{{ $o->email }}</td>
                        <td class="px-3 py-2">{{ $o->phone }}</td>
                        <td class="px-3 py-2">{{ $o->address }}</td>
                        <td class="px-3 py-2 text-right">
                            <a class="text-blue-700 hover:underline" href="{{ route('organizations.edit', $o) }}">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td class="px-3 py-6 text-center text-gray-500" colspan="8">No records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $rows->links() }}</div>
</div>
