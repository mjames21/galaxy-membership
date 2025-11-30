{{-- resources/views/livewire/members/index.blade.php --}}
<div
    x-data="{
        show: @entangle('showFormModal').live,
        close(){ this.show=false; },
    }"
    x-on:keydown.escape.window="close()"
    x-on:close-form-modal.window="close()"
    class="max-w-7xl mx-auto p-6"
>
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">
            Memberships — {{ trim(($person->last_name ?? '').' '.($person->first_name ?? '')) }}
        </h1>

        {{-- IMPORTANT: open modal, do NOT navigate --}}
        <button type="button"
                wire:click="openCreate"
                class="px-3 py-2 bg-blue-600 text-white rounded">
            + New
        </button>
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
                        {{-- IMPORTANT: open modal, do NOT navigate --}}
                        <button type="button"
                                wire:click="openEdit({{ $m->id }})"
                                class="text-blue-700 hover:underline">
                            Edit
                        </button>
                    </td>
                </tr>
            @empty
                <tr><td class="px-3 py-6 text-center text-gray-500" colspan="8">No records.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $rows->links() }}</div>

    {{-- MODAL --}}
    <div
        x-show="show"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center"
        aria-modal="true" role="dialog"
    >
        <div class="fixed inset-0 bg-black/40" x-on:click="close()"></div>

        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-3xl mx-4">
            <div class="flex items-center justify-between border-b px-4 py-3">
                <h2 class="font-semibold">
                    <span wire:loading.remove>
                        {{ $editingId ? 'Edit Membership' : 'New Membership' }}
                    </span>
                    <span wire:loading>Loading…</span>
                </h2>
                <button type="button" class="p-2" x-on:click="close()">&times;</button>
            </div>

            <div class="p-4">
                @if ($showFormModal)
                    @livewire('members.form', [
                        'person' => $person,
                        // pass ID; the Form will resolve the model in mount
                        'memberRegistrationId' => $editingId,
                        // hint to Form that it's inline (no redirect)
                        'asModal' => true,
                    ], key('members-form-'.($editingId ?? 'new')))
                @endif
            </div>
        </div>
    </div>
</div>
