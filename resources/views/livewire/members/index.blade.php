<div class="max-w-7xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">
            Memberships — {{ trim(($person->last_name ?? '').' '.($person->first_name ?? '')) }}
        </h1>
        <a href="{{ route('members.create', $person) }}" class="px-3 py-2 bg-blue-600 text-white rounded">+ New</a>
    </div>

    <div class="bg-white border rounded p-4 mb-4">
        <div class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-600">Search Reg No.</label>
                <input type="text" wire:model.debounce.400ms="q" class="border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Created From</label>
                <input type="date" wire:model.lazy="created_from" class="border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Created To</label>
                <input type="date" wire:model.lazy="created_to" class="border rounded px-3 py-2">
            </div>
        </div>
    </div>

    <div class="overflow-x-auto bg-white border rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">Reg No.</th>
                    <th class="px-3 py-2 text-left">Year</th>
                    <th class="px-3 py-2 text-left">Region</th>
                    <th class="px-3 py-2 text-left">District</th>
                    <th class="px-3 py-2 text-left">Constituency</th>
                    <th class="px-3 py-2 text-left">Zone</th>
                    <th class="px-3 py-2 text-left">Created</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $m)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $m->registration_number }}</td>
                        <td class="px-3 py-2">{{ $m->registration_year }}</td>
                        <td class="px-3 py-2">{{ optional($m->region)->name }}</td>
                        <td class="px-3 py-2">{{ optional($m->district)->name }}</td>
                        <td class="px-3 py-2">{{ optional($m->constituency)->name }}</td>
                        <td class="px-3 py-2">{{ optional($m->zone)->name }}</td>
                        <td class="px-3 py-2">{{ optional($m->created_at)->format('Y-m-d') }}</td>
                        <td class="px-3 py-2 text-right">
                            <a class="text-blue-700 hover:underline" href="{{ route('members.edit', [$person, $m]) }}">Edit</a>
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
