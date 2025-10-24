<div class="max-w-7xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Opportunities</h1>
        <div class="flex gap-2">
            <a href="{{ route('opportunities.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">+ New</a>
            <a class="px-3 py-2 border rounded bg-white hover:bg-gray-50"
               href="{{ route('export.opportunities', [
                    'q'=>$q,'type_id'=>$type_id,'org_id'=>$org_id,'prospect'=>$prospect,
                    'updated_from'=>$updated_from,'updated_to'=>$updated_to
               ]) }}">Export CSV</a>
        </div>
    </div>

    <div class="bg-white border rounded p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs text-gray-600">Search (name/eligibility)</label>
                <input type="text" wire:model.debounce.400ms="q" class="border rounded w-full px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Type ID</label>
                <input type="text" wire:model.lazy="type_id" class="border rounded w-full px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Organization ID</label>
                <input type="text" wire:model.lazy="org_id" class="border rounded w-full px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Prospect</label>
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

        <div class="grid grid-cols-1 md:grid-cols-5 gap-3 mt-3">
            <div>
                <label class="block text-xs text-gray-600">Updated From</label>
                <input type="date" wire:model.lazy="updated_from" class="border rounded w-full px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Updated To</label>
                <input type="date" wire:model.lazy="updated_to" class="border rounded w-full px-3 py-2">
            </div>
        </div>
    </div>

    <div class="overflow-x-auto bg-white border rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">Name</th>
                    <th class="px-3 py-2 text-left">Type</th>
                    <th class="px-3 py-2 text-left">Organization</th>
                    <th class="px-3 py-2 text-left">Prospect</th>
                    <th class="px-3 py-2 text-left">Contacts</th>
                    <th class="px-3 py-2 text-left">Updated</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $o)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $o->name }}</td>
                        <td class="px-3 py-2">{{ $o->type->name ?? '—' }}</td>
                        <td class="px-3 py-2">{{ $o->organization->name ?? '—' }}</td>
                        <td class="px-3 py-2 capitalize">{{ $o->prospect }}</td>
                        <td class="px-3 py-2">{{ $o->contacts->count() }}</td>
                        <td class="px-3 py-2">{{ optional($o->updated_at)->format('Y-m-d') }}</td>
                        <td class="px-3 py-2 text-right">
                            <a class="text-blue-700 hover:underline" href="{{ route('opportunities.edit', $o) }}">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td class="px-3 py-6 text-center text-gray-500" colspan="7">No records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $rows->links() }}</div>
</div>
