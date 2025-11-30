{{-- file: resources/views/livewire/locations/manage.blade.php --}}
<div class="max-w-7xl mx-auto p-6">
    {{-- Header --}}
    <div class="flex items-center gap-3 mb-4">
        <h1 class="text-xl font-semibold">Locations</h1>
        @if (session('ok'))
            <div class="ml-2 text-sm bg-green-50 border border-green-200 text-green-800 px-3 py-1.5 rounded">
                {{ session('ok') }}
            </div>
        @endif
    </div>

    {{-- Tabs + actions --}}
    <div class="flex flex-wrap items-center gap-2 mb-3">
        <button class="px-3 py-1.5 rounded border @if($tab==='regions') bg-gray-900 text-white @endif" wire:click="$set('tab','regions')">Regions</button>
        <button class="px-3 py-1.5 rounded border @if($tab==='districts') bg-gray-900 text-white @endif" wire:click="$set('tab','districts')">Districts</button>
        <button class="px-3 py-1.5 rounded border @if($tab==='constituencies') bg-gray-900 text-white @endif" wire:click="$set('tab','constituencies')">Constituencies</button>
        <button class="px-3 py-1.5 rounded border @if($tab==='zones') bg-gray-900 text-white @endif" wire:click="$set('tab','zones')">Zones</button>

        <div class="ml-auto flex flex-wrap items-center gap-2">
            <input type="text" placeholder="Search name/code..." wire:model.debounce.300ms="q" class="border rounded px-3 py-2">
            <button wire:click="createNew" class="px-3 py-2 bg-blue-600 text-white rounded">+ New</button>

            <button wire:click="exportCsv" class="px-3 py-2 border rounded">Export CSV</button>
            <button wire:click="downloadTemplate" class="px-3 py-2 border rounded">Template CSV</button>
            <label class="px-3 py-2 border rounded cursor-pointer">
                <span>Import CSV</span>
                <input type="file" class="hidden" wire:model="csvFile" accept=".csv,text/csv">
            </label>
            @error('csvFile')<div class="text-xs text-red-600">{{ $message }}</div>@enderror
            @if($csvFile)
                <button wire:click="importCsv" class="px-3 py-2 bg-green-600 text-white rounded">Upload</button>
            @endif

            @if ($tab==='zones')
                <button
                    class="px-3 py-2 border rounded"
                    wire:click="regenerateZones(true)"
                    @if(!$constituency_id) disabled @endif
                    onclick="return confirm('Regenerate Zones 1–5 for the selected constituency? This will remove extra zones if any.');"
                >
                    Regenerate Zones 1–5
                </button>
            @endif
        </div>
    </div>

    {{-- Parent filters (single source of truth) --}}
    <div class="bg-white border rounded p-3 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs text-gray-600">Region</label>
                <div class="flex gap-2">
                    <select
                        wire:model.live="region_id"
                        wire:key="region"
                        class="border rounded px-3 py-2 w-full"
                        @if($tab==='regions') disabled @endif
                    >
                        <option value="">—</option>
                        @foreach ($regions as $r)
                            <option value="{{ $r['id'] }}">{{ $r['name'] }}</option>
                        @endforeach
                    </select>
                    <button class="px-2 border rounded" type="button" wire:click="clearRegion">✕</button>
                </div>
                @if($tab==='districts') @error('region_id')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror @endif
            </div>

            <div>
                <label class="block text-xs text-gray-600">District</label>
                <div class="flex gap-2">
                    <select
                        wire:model.live="district_id"
                        wire:key="district-{{ (int) $region_id }}"
                        class="border rounded px-3 py-2 w-full"
                        @if(in_array($tab,['regions','districts'])) disabled @endif
                    >
                        <option value="">—</option>
                        @foreach ($districts as $d)
                            <option value="{{ $d['id'] }}">{{ $d['name'] }}</option>
                        @endforeach
                    </select>
                    <button class="px-2 border rounded" type="button" wire:click="clearDistrict">✕</button>
                </div>
                @if($tab==='constituencies') @error('district_id')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror @endif
            </div>

            <div>
                <label class="block text-xs text-gray-600">Constituency</label>
                <div class="flex gap-2">
                    <select
                        wire:model.live="constituency_id"
                        wire:key="const-{{ (int) $district_id }}"
                        class="border rounded px-3 py-2 w-full"
                        @if($tab!=='zones') disabled @endif
                    >
                        <option value="">—</option>
                        @foreach ($constituencies as $c)
                            <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                        @endforeach
                    </select>
                    <button class="px-2 border rounded" type="button" wire:click="clearConstituency">✕</button>
                </div>
                @if($tab==='zones') @error('constituency_id')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Table --}}
        <div class="lg:col-span-2 bg-white border rounded">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left">Name</th>
                        <th class="px-3 py-2 text-left">Code</th>
                        @if($tab!=='regions') <th class="px-3 py-2 text-left">Parent</th> @endif
                        <th class="px-3 py-2 text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $row)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ $row->name }}</td>
                            <td class="px-3 py-2">{{ $row->code }}</td>
                            @if($tab==='districts')
                                <td class="px-3 py-2">{{ optional($row->region)->name }}</td>
                            @elseif($tab==='constituencies')
                                <td class="px-3 py-2">{{ optional($row->district)->name }}</td>
                            @elseif($tab==='zones')
                                <td class="px-3 py-2">{{ optional($row->constituency)->name }}</td>
                            @endif
                            <td class="px-3 py-2 text-right">
                                <button class="text-blue-700 hover:underline" wire:click="edit({{ $row->id }})">Edit</button>
                                <button class="text-red-700 hover:underline ml-3" wire:click="delete({{ $row->id }})" onclick="return confirm('Delete this record?')">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="px-3 py-6 text-center text-gray-500" colspan="4">No records.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-3">{{ $rows->links() }}</div>
        </div>

        {{-- Form --}}
        <div class="bg-white border rounded p-4">
            <h2 class="font-semibold mb-3">
                {{ $editingId ? 'Edit' : 'Create' }}
                @if($tab==='regions') Region
                @elseif($tab==='districts') District
                @elseif($tab==='constituencies') Constituency
                @else Zone
                @endif
            </h2>

            {{-- Context pills --}}
            @if($tab!=='regions')
                <div class="flex flex-wrap gap-2 mb-3">
                    @if(in_array($tab, ['districts','constituencies','zones']))
                        <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-gray-100 border">
                            Region: <span class="ml-1 font-medium">{{ optional(collect($regions)->firstWhere('id',$region_id))['name'] ?? '—' }}</span>
                        </span>
                    @endif
                    @if(in_array($tab, ['constituencies','zones']))
                        <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-gray-100 border">
                            District: <span class="ml-1 font-medium">{{ optional(collect($districts)->firstWhere('id',$district_id))['name'] ?? '—' }}</span>
                        </span>
                    @endif
                    @if($tab==='zones')
                        <span class="inline-flex items-center px-2 py-1 text-xs rounded-full bg-gray-100 border">
                            Constituency: <span class="ml-1 font-medium">{{ optional(collect($constituencies)->firstWhere('id',$constituency_id))['name'] ?? '—' }}</span>
                        </span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mb-3">Change the selection in the filters above to set the parent.</p>
            @endif

            <div class="space-y-3">
                <div>
                    <label class="block text-xs text-gray-600">Name</label>
                    <input type="text" wire:model.defer="name" wire:keyup.debounce.200ms="updatedName" class="border rounded px-3 py-2 w-full">
                    @error('name')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                <div>
                    <div class="flex justify-between items-center">
                        <label class="block text-xs text-gray-600">Code</label>
                        <label class="text-xs flex items-center gap-1">
                            <input type="checkbox" wire:model="autoCode">
                            Auto from name
                        </label>
                    </div>
                    <input type="text" wire:model.defer="code" class="border rounded px-3 py-2 w-full" placeholder="Auto if blank">
                    @error('code')<div class="text-xs text-red-600 mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <button wire:click="save" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
                    <button wire:click="createNew" type="button" class="px-4 py-2 border rounded">Reset</button>
                </div>
            </div>
        </div>
    </div>
</div>
