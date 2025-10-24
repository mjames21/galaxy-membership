<div class="max-w-7xl mx-auto p-6 space-y-6">
    <div class="flex items-end justify-between gap-4">
        <h1 class="text-xl font-semibold">Dashboard</h1>

        {{-- DATE CONTROLS --}}
        <div class="bg-white border rounded p-3">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-xs mb-1">Preset</label>
                    <select wire:model.live="period" class="border rounded px-2 py-1">
                        <option value="today">Today</option>
                        <option value="last_7">Last 7 days</option>
                        <option value="last_30">Last 30 days</option>
                        <option value="quarter">This quarter</option>
                        <option value="year">This year</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs mb-1">From</label>
                    <input type="date" wire:model.lazy="from" class="border rounded px-2 py-1" @disabled($period !== 'custom')>
                </div>
                <div>
                    <label class="block text-xs mb-1">To</label>
                    <input type="date" wire:model.lazy="to" class="border rounded px-2 py-1" @disabled($period !== 'custom')>
                </div>
                <div class="text-xs text-gray-600">
                    <div>Effective:
                        <span class="font-medium">
                            {{ $from ?: '—' }} → {{ $to ?: '—' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DASHBOARD EXPORT TOOLBAR --}}
    @php $fromEff = $from; $toEff = $to; @endphp
    <div class="flex flex-wrap gap-2 bg-white border rounded p-3">
        <a class="px-3 py-2 border rounded hover:bg-gray-50" href="{{ route('export.people',        ['created_from'=>$fromEff,'created_to'=>$toEff]) }}">Export People</a>
        <a class="px-3 py-2 border rounded hover:bg-gray-50" href="{{ route('export.people',        ['has_members'=>1,'created_from'=>$fromEff,'created_to'=>$toEff]) }}">Export Members</a>
        <a class="px-3 py-2 border rounded hover:bg-gray-50" href="{{ route('export.stakeholders',  ['created_from'=>$fromEff,'created_to'=>$toEff]) }}">Export Stakeholders</a>
        <a class="px-3 py-2 border rounded hover:bg-gray-50" href="{{ route('export.opportunities', ['updated_from'=>$fromEff,'updated_to'=>$toEff]) }}">Export Opportunities</a>
        <a class="px-3 py-2 border rounded hover:bg-gray-50" href="{{ route('export.initiatives',   ['created_from'=>$fromEff,'created_to'=>$toEff]) }}">Export Initiatives</a>
        <a class="px-3 py-2 border rounded hover:bg-gray-50" href="{{ route('export.organizations') }}">Export Organizations</a>
        <a class="px-3 py-2 border rounded hover:bg-gray-50" href="{{ route('export.executives',    ['status'=>'active']) }}">Export Active Execs</a>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="relative bg-white border rounded p-4">
            <div class="text-sm text-gray-500">People</div>
            <div class="text-2xl font-semibold mt-1">{{ number_format($stats['people'] ?? 0) }}</div>
        </div>

        <div class="relative bg-white border rounded p-4">
            <div class="text-sm text-gray-500">Members</div>
            <div class="text-2xl font-semibold mt-1">{{ number_format($stats['members'] ?? 0) }}</div>
        </div>

        <div class="relative bg-white border rounded p-4">
            <div class="text-sm text-gray-500">Stakeholders</div>
            <div class="text-2xl font-semibold mt-1">{{ number_format($stats['stakeholders'] ?? 0) }}</div>
        </div>

        <div class="relative bg-white border rounded p-4">
            <div class="text-sm text-gray-500">Opportunities</div>
            <div class="text-2xl font-semibold mt-1">{{ number_format($stats['opportunities'] ?? 0) }}</div>
        </div>

        <div class="relative bg-white border rounded p-4">
            <div class="text-sm text-gray-500">Initiatives</div>
            <div class="text-2xl font-semibold mt-1">{{ number_format($stats['initiatives'] ?? 0) }}</div>
        </div>

        <div class="relative bg-white border rounded p-4">
            <div class="text-sm text-gray-500">Active Executives</div>
            <div class="text-2xl font-semibold mt-1">{{ number_format($stats['exec_active'] ?? 0) }}</div>
        </div>
    </div>

    {{-- CHARTS --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white border rounded p-4">
            <div class="flex items-center justify-between mb-2">
                <h2 class="font-semibold">Opportunities by Prospect</h2>
                <a class="text-sm text-blue-700 hover:underline"
                   href="{{ route('export.opportunities', ['updated_from'=>$fromEff,'updated_to'=>$toEff]) }}">Export CSV</a>
            </div>
            <canvas id="chartProspects" height="160"></canvas>
        </div>

        <div class="bg-white border rounded p-4">
            <div class="flex items-center justify-between mb-2">
                <h2 class="font-semibold">Stakeholders by Willingness</h2>
                <a class="text-sm text-blue-700 hover:underline"
                   href="{{ route('export.stakeholders', ['created_from'=>$fromEff,'created_to'=>$toEff]) }}">Export CSV</a>
            </div>
            <canvas id="chartWillingness" height="160"></canvas>
        </div>

        <div class="bg-white border rounded p-4">
            <div class="flex items-center justify-between mb-2">
                <h2 class="font-semibold">Members by Region (Top 10)</h2>
                <a class="text-sm text-blue-700 hover:underline"
                   href="{{ route('export.people', ['has_members'=>1,'created_from'=>$fromEff,'created_to'=>$toEff]) }}">Export CSV</a>
            </div>
            <canvas id="chartMembersRegion" height="200"></canvas>
        </div>
    </div>
</div>

{{-- Charts (no custom colors/styles; simple & safe) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('livewire:navigated', renderCharts);
document.addEventListener('DOMContentLoaded', renderCharts);

function renderCharts() {
  // destroy old charts if any
  window.__charts = window.__charts || [];
  window.__charts.forEach(c => { try { c.destroy(); } catch(e){} });
  window.__charts = [];

  // Prospects doughnut
  const el1 = document.getElementById('chartProspects');
  if (el1) {
    const c1 = new Chart(el1, {
      type: 'doughnut',
      data: { labels: @json($prospectLabels ?? []), datasets: [{ data: @json($prospectData ?? []) }] },
      options: { plugins: { legend: { position: 'bottom' } } }
    });
    window.__charts.push(c1);
  }

  // Willingness bar
  const el2 = document.getElementById('chartWillingness');
  if (el2) {
    const c2 = new Chart(el2, {
      type: 'bar',
      data: { labels: @json($willLabels ?? []), datasets: [{ data: @json($willData ?? []) }] },
      options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
    window.__charts.push(c2);
  }

  // Members by Region bar (horizontal)
  const el3 = document.getElementById('chartMembersRegion');
  if (el3) {
    const c3 = new Chart(el3, {
      type: 'bar',
      data: { labels: @json($regionLabels ?? []), datasets: [{ data: @json($regionData ?? []) }] },
      options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
    });
    window.__charts.push(c3);
  }
}
</script>
