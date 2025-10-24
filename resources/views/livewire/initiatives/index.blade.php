<div class="max-w-7xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Initiatives</h1>
        <div class="flex gap-2">
            <a href="{{ route('initiatives.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">+ New</a>
            <a class="px-3 py-2 border rounded bg-white hover:bg-gray-50"
               href="{{ route('export.initiatives', [
                    'q'=>$q,'category_id'=>$category_id,'status_id'=>$status_id,
                    'region_id'=>$region_id,'district_id'=>$district_id,'constituency_id'=>$constituency_id,'zone_id'=>$zone_id,
                    'created_from'=>$created_from,'created_to'=>$created_to
               ]) }}">Export CSV</a>
        </div>
    </div>

    <div class="bg-white border rounded p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs text-gray-600">Search (name/brief)</label>
                <input type="text" wire:model.debounce.400ms="q" class="border rounded w-full px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Category ID</label>
                <input type="text" wire:model.lazy="category_id" class="border rounded w-full px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Status ID</label>
                <input type="text" wire:model.lazy="status_id" class="border rounded w-full px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Zone ID</label>
                <input type="text" wire:model.lazy="zone_id" class="border rounded w-full px-3 py-2">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
            <div>
                <label class="block text-xs text-gray-600">Region ID</label>
                <input type="text" wire:model.lazy="region_id" class="border rounded w-full px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-600">District ID</label>
                <input type="text" wire:model.lazy="district_id" class="border rounded w-full px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Constituency ID</label>
                <input type="text" wire:model.lazy="constituency_id" class="border rounded w-full px-3 py-2">
            </div>
            <div class="md:col-span-1">
                <label class="block text-xs text-gray-600">Created From</label>
                <input type="date" wire:model.lazy="created_from" class="border rounded w-full px-3 py-2">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
            <div class="md:col-span-3"></div>
            <div>
                <label class="block text-xs text-gray-600">Created To</label>
                <input type="date" wire:model.lazy="created_to" class="border rounded w-full px-3 py-2">
            </div>
        </div>
    </div>

    <div class="overflow-x-auto bg-white border rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">Name</th>
                    <th class="px-3 py-2 text-left">Category</th>
                    <th class="px-3 py-2 text-left">Status</th>
                    <th class="px-3 py-2 text-left">Lead</th>
                    <th class="px-3 py-2 text-left">Zones</th>
                    <th class="px-3 py-2 text-left">Sponsors (Sec/Target)</th>
                    <th class="px-3 py-2 text-left">Created</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $i)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $i->name }}</td>
                        <td class="px-3 py-2">{{ $i->category->name ?? '—' }}</td>
                        <td class="px-3 py-2">{{ $i->status->name ?? '—' }}</td>
                        <td class="px-3 py-2">{{ trim(($i->lead->last_name ?? '').' '.($i->lead->first_name ?? '')) }}</td>
                        <td class="px-3 py-2">{{ $i->zones->count() }}</td>
                        <td class="px-3 py-2">{{ $i->sponsors->where('pivot.sponsor_status','secured')->count() }} / {{ $i->sponsors->where('pivot.sponsor_status','targeted')->count() }}</td>
                        <td class="px-3 py-2">{{ optional($i->created_at)->format('Y-m-d') }}</td>
                        <td class="px-3 py-2 text-right">
                            <a class="text-blue-700 hover:underline" href="{{ route('initiatives.edit', $i) }}">Edit</a>
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
