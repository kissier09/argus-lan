@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-heading', 'Network Overview')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-white">
            Welcome to ARCLUS
        </h1>
        <p class="mt-2 text-sm text-slate-400">
            Monitor registered users and computers from your local network.
        </p>
    </div>

    <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
        
        <a href="{{ route('admin.users.index') }}"
            class="block rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-lg shadow-black/10 transition hover:border-emerald-400/50 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-400">

            <div class="flex items-center justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-400/10 text-emerald-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5V4H2v16h5m10 0v-2a4 4 0 00-4-4h-2a4 4 0 00-4 4v2m5-10a3 3 0 100-6 3 3 0 000 6z"/>
                    </svg>
                </div>
                <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-300">
                    APPROVED
                </span>
            </div>
            <p class="mt-5 text-sm font-medium text-slate-400">Total Enrolled Users</p>
            <p class="mt-2 text-4xl font-bold text-white">{{ $enrolledUsers }}</p>
            <p class="mt-2 text-xs text-slate-500">Users approved by an administrator</p>
        </a>

        <a href="{{ route('admin.users.pending') }}"
            class="block rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-lg shadow-black/10 transition hover:border-emerald-400/50 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            <div class="flex items-center justify-between">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-400/10 text-amber-300">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v5l3 2"/>
                    </svg>
                </div>
                <span class="rounded-full bg-amber-400/10 px-3 py-1 text-xs font-semibold text-amber-300">
                    REVIEW
                </span>
            </div>
            <p class="mt-5 text-sm font-medium text-slate-400">Pending Registration</p>
            <p class="mt-2 text-4xl font-bold text-white">{{ $pendingUsers }}</p>
            <p class="mt-2 text-xs text-slate-500">Registrations awaiting review</p>
        </a>

        <a href="{{ route('admin.devices.index') }}"
            class="block rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-lg shadow-black/10 transition duration-200 hover:border-cyan-500 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">

                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-400/10 text-blue-300">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="13" rx="2" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21h8m-4-4v4"/>
                        </svg>
                    </div>

                    <span class="rounded-full bg-blue-400/10 px-3 py-1 text-xs font-semibold text-blue-300">
                        DEVICES
                    </span>
                </div>

                <p class="mt-5 text-sm font-medium text-slate-400">Total Computers</p>

                <p class="mt-2 text-4xl font-bold text-white">
                    {{ $totalComputers }}
                </p>

                <p class="mt-2 text-xs text-slate-500">
                    Registered device records
                </p>
            </a>
    </div>

    
    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-5">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-400/10 text-emerald-300">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
            <div>
                <h3 class="font-semibold text-white">ARCLUS Admin Console</h3>
                <p class="mt-1 text-sm text-slate-400">
                    Dashboard statistics are loaded from your database.
                </p>
            </div>
        </div>
    </div>
@endsection

