<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - ARCLUS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 antialiased">
    <div class="min-h-screen md:flex">

        <aside class="w-full border-b border-slate-800 bg-slate-900 md:min-h-screen md:w-64 md:border-b-0 md:border-r">
            <div class="flex items-center gap-3 border-b border-slate-800 px-6 py-5">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-400 text-slate-950">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7l8-4z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2" d="M9 12l2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold tracking-wide">ARGUS-LAN</h1>
                    <p class="text-xs tracking-widest text-slate-400">ADMIN CENTER</p>
                </div>
            </div>

            <div class="px-4 py-5">
                <p class="mb-3 px-3 text-xs font-semibold uppercase tracking-widest text-slate-500">
                    Main Menu
                </p>

                <nav class="space-y-2">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition
                       {{ request()->routeIs('dashboard') ? 'bg-emerald-400/10 text-emerald-300 ring-1 ring-emerald-400/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <span>▦</span> Dashboard
                    </a>

                </nav>

                <div class="mt-8 rounded-xl border border-slate-800 bg-slate-950 p-4">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                        <span class="text-sm font-semibold">Admin Console</span>
                    </div>
                    <p class="mt-2 text-xs leading-5 text-slate-400">
                        Manage registered users and monitor LAN devices.
                    </p>
                </div>
            </div>

            <div class="border-t border-slate-800 p-4 md:mt-8">
                <a href="{{ route('logout') }}"
                   onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();"
                   class="block rounded-lg px-3 py-3 text-sm font-medium text-rose-300 hover:bg-rose-500/10">
                    ↪ Sign Out
                </a>

                <form id="admin-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>
            </div>
        </aside>

        <main class="min-w-0 flex-1">
            <header class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 bg-slate-900/60 px-6 py-5 lg:px-10">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-emerald-400">
                        Network Operations Center
                    </p>
                    <h2 class="mt-1 text-xl font-bold text-white">
                        @yield('page-heading', 'Dashboard')
                    </h2>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden text-right sm:block">
                        <p class="text-sm font-semibold text-white">
                            {{ auth('admin')->user()?->name ?? 'Administrator' }}
                        </p>
                        <p class="text-xs text-slate-400">System Administrator</p>
                    </div>
                    <div class="flex h-10 w-10 items-center justify-center rounded-full border border-emerald-400/30 bg-emerald-400/10 font-bold text-emerald-300">
                        {{ strtoupper(substr(auth('admin')->user()?->name ?? 'A', 0, 1)) }}
                    </div>
                </div>
            </header>

            <div class="p-5 sm:p-8 lg:p-10">
                @if (session('success'))
                    <div class="mb-6 rounded-lg border border-emerald-500/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-300">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 rounded-lg border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-sm text-rose-300">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

    </div>
</body>
</html>
