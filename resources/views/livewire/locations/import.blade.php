{{-- resources/views/livewire/locations/import.blade.php --}}
<div class="max-w-3xl mx-auto p-6 space-y-4">
    <h1 class="text-xl font-semibold">Locations Import</h1>

    @if (session('ok'))
        <div class="mb-2 text-sm bg-green-50 border border-green-200 text-green-800 px-3 py-2 rounded">
            {{ session('ok') }}
        </div>
    @endif

    <div class="bg-white border rounded p-4 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs text-gray-600 mb-1">Type</label>
                <select wire:model="type" class="border rounded px-3 py-2 w-full">
                    <option value="regions">Regions</option>
                    <option value="districts">Districts</option>
                    <option value="constituencies">Chiefdoms (Constituencies)</option>
                    <option value="zones">Zones</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs text-gray-600 mb-1">CSV</label>
                <input type="file" wire:model="file" class="border rounded px-3 py-2 w-full">
                @error('file') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                <p class="text-xs text-gray-500 mt-1">
                    Regions: <code>code,name</code> — Districts: <code>parent_code,code,name</code> — Chiefdoms: <code>parent_code,code,name</code> — Zones: <code>parent_code,code,name</code>
                </p>
            </div>
        </div>

        @if ($preview)
            <div class="border rounded">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach (array_keys($preview[0]) as $col)
                                <th class="px-3 py-2 text-left">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($preview as $row)
                            <tr class="border-t">
                                @foreach ($row as $val)
                                    <td class="px-3 py-2">{{ $val }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <div class="flex items-center gap-2">
            <button wire:click="import" class="px-4 py-2 bg-blue-600 text-white rounded" @disabled(!$file)>Import</button>
        </div>
    </div>

    <div class="bg-white border rounded p-4">
        <h2 class="font-medium mb-2">Sample CSVs</h2>
        <pre class="text-xs bg-gray-50 p-3 rounded border">
# regions.csv
code,name
EASTERN,Eastern
NORTHERN,Northern
NORTH_WEST,North West
SOUTHERN,Southern
WESTERN_AREA,Western Area

# districts.csv
parent_code,code,name
EASTERN,KAILAHUN,Kailahun
EASTERN,KENEMA,Kenema
EASTERN,KONO,Kono
...
# constituencies.csv  (chiefdoms)
parent_code,code,name
KAILAHUN,KAILAHUN_CH1,Chiefdom 1
...
# zones.csv
parent_code,code,name
KAILAHUN_CH1,KAILAHUN_CH1_Z1,Zone 1
...</pre>
    </div>
</div>
