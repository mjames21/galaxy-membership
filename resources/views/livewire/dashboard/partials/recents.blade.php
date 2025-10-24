<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="bg-white border rounded p-4">
        <h2 class="font-semibold mb-3">Recent People</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-3 py-2">Name</th>
                        <th class="text-left px-3 py-2">Email</th>
                        <th class="text-left px-3 py-2">Phone</th>
                        <th class="text-left px-3 py-2">Added</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentPeople as $p)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ trim(($p['last_name'] ?? '').' '.($p['first_name'] ?? '')) }}</td>
                            <td class="px-3 py-2">{{ $p['email'] }}</td>
                            <td class="px-3 py-2">{{ $p['phone'] }}</td>
                            <td class="px-3 py-2">{{ \Illuminate\Support\Carbon::parse($p['created_at'])->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-3 py-6 text-gray-500" colspan="4">No records.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white border rounded p-4">
        <h2 class="font-semibold mb-3">Recent Opportunities</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-3 py-2">Name</th>
                        <th class="text-left px-3 py-2">Type</th>
                        <th class="text-left px-3 py-2">Org</th>
                        <th class="text-left px-3 py-2">Prospect</th>
                        <th class="text-left px-3 py-2">Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentOpportunities as $o)
                        <tr class="border-t">
                            <td class="px-3 py-2">{{ $o['name'] }}</td>
                            <td class="px-3 py-2">{{ $o['type']['name'] ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $o['organization']['name'] ?? '—' }}</td>
                            <td class="px-3 py-2 capitalize">{{ $o['prospect'] }}</td>
                            <td class="px-3 py-2">{{ \Illuminate\Support\Carbon::parse($o['updated_at'])->format('Y-m-d') }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-3 py-6 text-gray-500" colspan="5">No records.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
