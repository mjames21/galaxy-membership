<div class="max-w-7xl mx-auto p-6" x-data="{ }">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold">People</h1>
        <div class="flex gap-2">
            {{-- Open popup for CREATE (no page nav) --}}
            <a href="#" wire:click.prevent="openCreate"
               class="px-3 py-2 bg-blue-600 text-white rounded">+ New</a>

            <a class="px-3 py-2 border rounded bg-white hover:bg-gray-50"
               href="{{ route('export.people', [
                    'q' => $q, 'sort' => $sort, 'dir' => $dir,
                    'created_from' => $created_from, 'created_to' => $created_to,
                    'has_members' => $has_members, 'region_id' => $region_id
               ]) }}">Export CSV</a>
        </div>
    </div>

    @if (session('ok'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded">
            {{ session('ok') }}
        </div>
    @endif

    <div class="bg-white border rounded p-4 mb-4">
        <div class="flex flex-wrap items-end gap-3">
            <div class="grow">
                <label class="block text-xs text-gray-600">Search</label>
                <input type="text" wire:model.debounce.400ms="q" class="border rounded w-full px-3 py-2" placeholder="Name, email, phone...">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Sort</label>
                <select wire:model="sort" class="border rounded px-3 py-2">
                    <option value="last_name">Last Name</option>
                    <option value="first_name">First Name</option>
                    <option value="email">Email</option>
                    <option value="phone">Phone</option>
                    <option value="created_at">Created</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-600">Dir</label>
                <select wire:model="dir" class="border rounded px-3 py-2">
                    <option value="asc">ASC</option>
                    <option value="desc">DESC</option>
                </select>
            </div>
            <label class="inline-flex items-center gap-2 text-sm">
                <input type="checkbox" value="1" wire:model="has_members" class="border rounded">
                Members only
            </label>
        </div>

        <div class="flex flex-wrap gap-3 mt-3 items-end">
            <div>
                <label class="block text-xs text-gray-600">Created From</label>
                <input type="date" wire:model.lazy="created_from" class="border rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-xs text-gray-600">Created To</label>
                <input type="date" wire:model.lazy="created_to" class="border rounded px-3 py-2">
            </div>
            <div class="text-xs text-gray-500">
                Effective:
                <span class="font-medium">{{ $created_from ?: '—' }} → {{ $created_to ?: '—' }}</span>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto bg-white border rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left">Name</th>
                    <th class="px-3 py-2 text-left">Email</th>
                    <th class="px-3 py-2 text-left">Phone</th>
                    <th class="px-3 py-2 text-left">Address</th>
                    <th class="px-3 py-2 text-right">Members</th>
                    <th class="px-3 py-2 text-left">Created</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $p)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ trim(($p->last_name ?? '').' '.($p->first_name ?? '')) }}</td>
                        <td class="px-3 py-2">{{ $p->email }}</td>
                        <td class="px-3 py-2">{{ $p->phone }}</td>
                        <td class="px-3 py-2">{{ $p->address }}</td>
                        <td class="px-3 py-2 text-right">{{ $p->member_registrations_count }}</td>
                        <td class="px-3 py-2">{{ optional($p->created_at)->format('Y-m-d') }}</td>
                        <td class="px-3 py-2 text-right">
                            {{-- Open popup for EDIT (no nav) --}}
                            <a href="#" wire:click.prevent="openEdit({{ $p->id }})"
                               class="text-blue-700 hover:underline">Edit</a>
                            <span class="mx-1 text-gray-400">|</span>
                            <a class="text-blue-700 hover:underline" href="{{ route('members.index', $p) }}">Memberships</a>
                        </td>
                    </tr>
                @empty
                    <tr><td class="px-3 py-6 text-center text-gray-500" colspan="7">No records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $rows->links() }}</div>

    {{-- =================== POPUP MODAL (inline on this page) =================== --}}
    <style>[x-cloak]{ display:none !important; }</style>
    <div x-data="{ open: @entangle('modalOpen') }" x-cloak>
        {{-- Dark overlay --}}
        <div x-show="open" class="fixed inset-0 bg-black/40 z-40"></div>

        {{-- Panel --}}
        <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div @keydown.escape.window="open=false"
                 class="w-full max-w-2xl bg-white rounded-lg shadow-lg border">
                <div class="px-4 py-3 border-b flex items-center justify-between">
                    <h2 class="font-semibold">
                        {{ $editingId ? 'Edit Person' : 'Create Person' }}
                    </h2>
                    <button class="text-gray-500 hover:text-gray-800" @click="open=false">&times;</button>
                </div>

                <form wire:submit.prevent="savePerson" class="p-4 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm mb-1">First Name *</label>
                            <input type="text" wire:model.defer="first_name" class="border rounded w-full px-3 py-2">
                            @error('first_name')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Last Name</label>
                            <input type="text" wire:model.defer="last_name" class="border rounded w-full px-3 py-2">
                            @error('last_name')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm mb-1">Other Names</label>
                            <input type="text" wire:model.defer="other_names" class="border rounded w-full px-3 py-2">
                            @error('other_names')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>

                        <div>
                            <label class="block text-sm mb-1">Email</label>
                            <input type="email" wire:model.defer="email" class="border rounded w-full px-3 py-2">
                            @error('email')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div>
                            <label class="block text-sm mb-1">Phone</label>
                            <input type="text" wire:model.defer="phone" class="border rounded w-full px-3 py-2">
                            @error('phone')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm mb-1">Address</label>
                            <input type="text" wire:model.defer="address" class="border rounded w-full px-3 py-2">
                            @error('address')<div class="text-red-600 text-xs mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t">
                        <button type="button" class="px-4 py-2 border rounded" @click="open=false">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove>Save</span>
                            <span wire:loading>Saving…</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- ================= END POPUP MODAL ================= --}}
</div>
