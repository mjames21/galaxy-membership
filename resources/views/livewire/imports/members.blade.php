<div class="max-w-xl mx-auto p-6">
    <h1 class="text-xl font-semibold mb-4">Import: Members</h1>
    @if (session('ok')) <div class="mb-3 text-sm bg-green-50 border border-green-200 text-green-800 px-3 py-2 rounded">{{ session('ok') }}</div> @endif
    <form wire:submit.prevent="import" class="bg-white border rounded p-4 space-y-4" enctype="multipart/form-data">
        <div>
            <label class="block text-sm mb-1">CSV File</label>
            <input type="file" wire:model="file" class="border rounded w-full px-3 py-2">
            @error('file')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <button class="px-4 py-2 bg-blue-600 text-white rounded">Upload & Import</button>
    </form>
    @if ($report)
        <div class="mt-4 bg-white border rounded p-4"><h2 class="font-semibold mb-2">Import Report</h2><pre class="text-xs">{{ json_encode($report, JSON_PRETTY_PRINT) }}</pre></div>
    @endif
</div>
