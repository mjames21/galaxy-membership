<div class="max-w-7xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Stakeholders</h1>
        <div class="flex gap-2">
            <a href="{{ route('stakeholders.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">+ New</a>
            <a class="px-3 py-2 border rounded bg-white hover:bg-gray-50"
               href="{{ route('export.stakeholders', [
                    'q'=>$q,'category_id'=>$category_id,'affiliation_id'=>$affiliation_id,
                    'willingness_level_id'=>$willingness_level_id,'support_type_id'=>$support_type_id,
                    'region_id'=>$region_id,'district_id'=>$district_id,'constituency_id'=>$constituency_id,'zone_id'=>$zone_id,
                    'created_from'=>$created_from,'created_to'=>$created_to
               ]) }}">Export CSV</a>
        </div>
    </div>

    <div class="bg-white border rounded p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs text-gray-600">Search (person/org)</label>
                <input type="text" wire:model.debounce.400ms="q" class="border rounded w-full px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Category</label>
                <input type="text" wire:model.lazy="category_id" class="border rounded w-full px-3 py-2" placeholder="Category ID">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Party Affiliation</label>
                <input type="text" wire:model.lazy="affiliation_id" class="border rounded w-full px-3 py-2" placeholder="Affiliation ID">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Willingness</label>
                <input type="text" wire:model.lazy="willingness_level_id" class="border rounded w-full px-3 py-2" placeholder="Level ID">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Support Type</label>
                <input type="text" wire:model.lazy="support_type_id" class="border rounded w-full px-3 py-2" placeholder="Type ID">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
            <div>
                <label class="block text-xs text-gray-600">Region</label>
                <input type="text" wire:model.lazy="region_id" class="border rounded w-full px-3 py-2" placeholder="Region ID">
            </div>
            <div>
                <label class="block text-xs text-gray-600">District</label>
                <input type="text" wire:model.lazy="district_id" class="border rounded w-full px-3 py-2" placeholder="District ID">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Constituency</label>
                <input type="text" wire:model.lazy="constituency_id" class="border rounded w-full px-3 py-2" placeholder="Constituency ID">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Zone</label>
                <input type="text" wire:model.lazy="zone_id" class="border rounded w-full px-3 py-2" placeholder="Zone ID">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mt-3">
            <div>
                <label class="block text-xs text-gray-600">Created From</label>
                <input type="date" wire:model.lazy="created_from" class="border rounded w-full px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Created To</label>
                <input type="date" wire:model.lazy="created_to" class="border rounded w-full px-3 py-2">
            </div>
            <div class="md:col-span-2 flex items-end text-xs text-gray-500">
                Effective: <span class="font-medium ml-1">{{ $created_from ?: '—' }} → {{ $created_to ?: '—' }}</span>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto bg-white border rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">Person</th>
                    <th class="px-3 py-2 text-left">Category</th>
                    <th class="px-3 py-2 text-left">Organization</th>
                    <th class="px-3 py-2 text-left">Affiliation</th>
                    <th class="px-3 py-2 text-left">Willingness</th>
                    <th class="px-3 py-2 text-left">Support Types</th>
                    <th class="px-3 py-2 text-left">Geo</th>
                    <th class="px-3 py-2 text-left">Created</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $s)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ trim(($s->person->last_name ?? '').' '.($s->person->first_name ?? '')) }}</td>
                        <td class="px-3 py-2">{{ $s->category->name ?? '—' }}</td>
                        <td class="px-3 py-2">{{ $s->organization->name ?? '—' }}</td>
                        <td class="px-3 py-2">{{ $s->partyAffiliation->name ?? '—' }}</td>
                        <td class="px-3 py-2">{{ $s->willingness->name ?? '—' }}</td>
                        <td class="px-3 py-2">{{ $s->supportTypes->pluck('name')->implode(', ') }}</td>
                        <td class="px-3 py-2">
                            {{ $s->district?->region?->name }} / {{ $s->district?->name }} / {{ $s->constituency?->name }} / {{ $s->zone?->name }}
                        </td>
                        <td class="px-3 py-2">{{ optional($s->created_at)->format('Y-m-d') }}</td>
                        <td class="px-3 py-2 text-right">
                            <a class="text-blue-700 hover:underline" href="{{ route('stakeholders.edit', $s) }}">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td class="px-3 py-6 text-center text-gray-500" colspan="9">No records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $rows->links() }}</div>
</div>
