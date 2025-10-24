{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', config('app.name', 'App'))</title>

  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @livewireStyles
</head>
<body class="bg-gray-100 text-gray-900 antialiased">
  <div class="min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-72 bg-white border-r flex flex-col">
      <div class="h-16 px-4 flex items-center justify-between border-b">
        <a href="{{ route('dashboard') }}" class="font-semibold text-lg">
          {{ config('app.name', 'App') }}
        </a>
      </div>

      <nav class="flex-1 py-4 text-sm">

        {{-- Executive / Overview --}}
        <div class="mt-2 px-4 text-xs uppercase tracking-wide text-gray-500">Overview</div>

        <a href="{{ route('dashboard') }}"
           class="block px-4 py-2 hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'bg-gray-200 font-semibold' : '' }}">
          Dashboard
        </a>

        {{-- People & Membership --}}
        <div class="mt-4 px-4 text-xs uppercase tracking-wide text-gray-500">People & Membership</div>

        <a href="{{ route('people.index') }}"
           class="block px-4 py-2 hover:bg-gray-100 {{ request()->routeIs('people.*') ? 'bg-gray-200 font-semibold' : '' }}">
          People
        </a>

        {{-- Executives --}}
        <a href="{{ route('executives.index') }}"
           class="block px-4 py-2 hover:bg-gray-100 {{ request()->routeIs('executives.*') ? 'bg-gray-200 font-semibold' : '' }}">
          Executives
        </a>

        {{-- Stakeholders --}}
        <div class="mt-4 px-4 text-xs uppercase tracking-wide text-gray-500">Stakeholders & Outreach</div>

        <a href="{{ route('stakeholders.index') }}"
           class="block px-4 py-2 hover:bg-gray-100 {{ request()->routeIs('stakeholders.*') ? 'bg-gray-200 font-semibold' : '' }}">
          Stakeholders
        </a>

        {{-- Opportunities & Initiatives --}}
        <div class="mt-4 px-4 text-xs uppercase tracking-wide text-gray-500">Programs</div>

        <a href="{{ route('opportunities.index') }}"
           class="block px-4 py-2 hover:bg-gray-100 {{ request()->routeIs('opportunities.*') ? 'bg-gray-200 font-semibold' : '' }}">
          Opportunities
        </a>

        <a href="{{ route('initiatives.index') }}"
           class="block px-4 py-2 hover:bg-gray-100 {{ request()->routeIs('initiatives.*') ? 'bg-gray-200 font-semibold' : '' }}">
          Initiatives
        </a>

        {{-- Organizations --}}
        <div class="mt-4 px-4 text-xs uppercase tracking-wide text-gray-500">Organizations</div>

        <a href="{{ route('organizations.index') }}"
           class="block px-4 py-2 hover:bg-gray-100 {{ request()->routeIs('organizations.*') ? 'bg-gray-200 font-semibold' : '' }}">
          Organizations
        </a>

        {{-- Imports (CSV) --}}
        <div class="mt-4 px-4 text-xs uppercase tracking-wide text-gray-500">Data Imports</div>

        <a href="{{ route('imports.people') }}"
           class="block px-4 py-2 hover:bg-gray-100 {{ request()->routeIs('imports.people') ? 'bg-gray-200 font-semibold' : '' }}">
          Import People
        </a>
        <a href="{{ route('imports.members') }}"
           class="block px-4 py-2 hover:bg-gray-100 {{ request()->routeIs('imports.members') ? 'bg-gray-200 font-semibold' : '' }}">
          Import Members
        </a>
        <a href="{{ route('imports.stakeholders') }}"
           class="block px-4 py-2 hover:bg-gray-100 {{ request()->routeIs('imports.stakeholders') ? 'bg-gray-200 font-semibold' : '' }}">
          Import Stakeholders
        </a>
        <a href="{{ route('imports.opportunities') }}"
           class="block px-4 py-2 hover:bg-gray-100 {{ request()->routeIs('imports.opportunities') ? 'bg-gray-200 font-semibold' : '' }}">
          Import Opportunities
        </a>

        {{-- (Optional) Quick export section: these routes download files so no active state needed --}}
        <div class="mt-4 px-4 text-xs uppercase tracking-wide text-gray-500">Quick Exports</div>

        <a href="{{ route('export.people') }}"
           class="block px-4 py-2 hover:bg-gray-100">
          Export People
        </a>
        <a href="{{ route('export.stakeholders') }}"
           class="block px-4 py-2 hover:bg-gray-100">
          Export Stakeholders
        </a>
        <a href="{{ route('export.opportunities') }}"
           class="block px-4 py-2 hover:bg-gray-100">
          Export Opportunities
        </a>
        <a href="{{ route('export.initiatives') }}"
           class="block px-4 py-2 hover:bg-gray-100">
          Export Initiatives
        </a>
        <a href="{{ route('export.organizations') }}"
           class="block px-4 py-2 hover:bg-gray-100">
          Export Organizations
        </a>
        <a href="{{ route('export.executives', ['status' => 'active']) }}"
           class="block px-4 py-2 hover:bg-gray-100">
          Export Active Execs
        </a>

      </nav>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col bg-white">

      {{-- Topbar (optional Livewire component) --}}
      @if (class_exists(\App\Livewire\NavigationMenu::class))
        @livewire('navigation-menu')
      @else
        <div class="h-16 bg-white border-b px-6 flex items-center justify-end">
          <span class="text-sm text-gray-600">Signed in</span>
        </div>
      @endif

      {{-- Page Content --}}
      <main class="flex-1 bg-gray-50 px-6 py-4">
        {{ $slot }}
      </main>

      {{-- Footer --}}
      <footer class="bg-white border-t px-6 py-4 text-sm text-gray-500">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <span class="mb-2 md:mb-0">
            © {{ date('Y') }} {{ config('app.name', 'App') }}. All Rights Reserved.
          </span>
          <ul class="flex space-x-4">
            <li><a href="#" class="hover:underline text-gray-500">Privacy Policy</a></li>
          </ul>
        </div>
      </footer>

    </div>
  </div>

  @livewireScripts
  @stack('modals')
</body>
</html>
