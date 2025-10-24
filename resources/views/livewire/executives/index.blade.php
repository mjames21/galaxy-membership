<div class="max-w-7xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">Executives</h1>
        <div class="flex gap-2">
            <a href="{{ route('executives.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">+ New</a>
            <a class="px-3 py-2 border rounded bg-white hover:bg-gray-50"
               href="{{ route('export.executives', [
                    'q'=>$q, 'status'=>$status, 'scope_level'=>$scope_level,
                    'region_id'=>$region_id, 'district_id'=>$district_id,
                    'constituency_id'=>$constituency_id, 'zone_id'=>$zone_id
               ]) }}">Export CSV</a>
        </div>
    </div>

    <div class="bg-white border rounded p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
            <div class="md:col-span-2">
                <label class="block text-xs text-gray-600">Search (person/position)</label>
                <input type="text" wire:model.debounce.400ms="q" class="border rounded w-full px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Status</label>
                <select wire:model="status" class="border rounded w-full px-3 py-2">
                    <option value="active">Active</option>
                    <option value="ended">Ended</option>
                    <option value="all">All</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-600">Scope Level</label>
                <select wire:model="scope_level" class="border rounded w-full px-3 py-2">
                    <option value="">—</option>
                    <option value="region">Region</option>
                    <option value="district">District</option>
                    <option value="constituency">Constituency</option>
                    <option value="zone">Zone</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-600">Region</label>
                <input type="text" wire:model.lazy="region_id" class="border rounded w-full px-3 py-2" placeholder="ID">
            </div>
            <div>
                <label class="block text-xs text-gray-600">District/Const/Zone IDs</label>
                <div class="flex gap-2">
                    <input type="text" wire:model.lazy="district_id" class="border rounded w-full px-2 py-1" placeholder="District ID">
                    <input type="text" wire:model.lazy="constituency_id" class="border rounded w-full px-2 py-1" placeholder="Const ID">
                    <input type="text" wire:model.lazy="zone_id" class="border rounded w-full px-2 py-1" placeholder="Zone ID">
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto bg-white border rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">Person</th>
                    <th class="px-3 py-2 text-left">Position</th>
                    <th class="px-3 py-2 text-left">Scope</th>
                    <th class="px-3 py-2 text-left">Term #</th>
                    <th class="px-3 py-2 text-left">Start</th>
                    <th class="px-3 py-2 text-left">End</th>
                    <th class="px-3 py-2 text-left">Status</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $e)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ trim(($e->person->last_name ?? '').' '.($e->person->first_name ?? '')) }}</td>
                        <td class="px-3 py-2">{{ $e->position->name ?? '—' }}</td>
                        <td class="px-3 py-2">
                            @php
                                $map = [
                                    App\Models\Region::class => 'Region',
                                    App\Models\District::class => 'District',
                                    App\Models\Constituency::class => 'Constituency',
                                    App\Models\Zone::class => 'Zone'
                                ];
                            @endphp
                            {{ $map[$e->scope_type] ?? '—' }} — {{ optional($e->scope)->name }}
                        </td>
                        <td class="px-3 py-2">{{ $e->term_number ?? 1 }}</td>
                        <td class="px-3 py-2">{{ $e->start_date }}</td>
                        <td class="px-3 py-2">{{ $e->end_date }}</td>
                        <td class="px-3 py-2">
                            @php
                                $today = now()->toDateString();
                                $active = (!$e->start_date || $e->start_date <= $today) && (!$e->end_date || $e->end_date >= $today);
                            @endphp
                            <span class="px-2 py-1 rounded text-xs {{ $active ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-50 text-gray-700 border' }}">{{ $active ? 'Active' : 'Ended' }}</span>
                        </td>
                        <td class="px-3 py-2 text-right">
                            <a class="text-blue-700 hover:underline" href="{{ route('executives.edit', $e) }}">Edit</a>
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
