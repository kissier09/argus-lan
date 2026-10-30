
@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <a href="{{ route('dashboard') }}"
    class="inline-flex items-center gap-2 rounded-lg border border-slate-600 bg-slate-800 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-700">
        <svg xmlns="http://www.w3.org/2000/svg"
            class="h-4 w-4"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 19l-7-7 7-7" />
        </svg>
        Back to Dashboard
    </a>
    
    
    <div class="space-y-4">

        {{-- Top-left Add Device button --}}
        <div class="flex justify-start">
            <a href="{{ route('admin.devices.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-500">

                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 4v16m8-8H4"/>
                </svg>

                Add Device
            </a>
        </div>
    </div>


    
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <h1 class="text-2xl font-bold text-white">
                Device Management
            </h1>

            <p class="mt-1 text-sm text-slate-400">
                Monitor registered computers connected to ARGUS-LAN.
            </p>
        </div>

        
        <div class="w-full min-w-0 sm:max-w-2xl">

            {{-- Expanded Search Form --}}
            <form method="GET"
                action="{{ route('admin.devices.index') }}"
                class="flex w-full gap-2">

                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search hostname or IP..."
                    class="min-w-0 flex-1 rounded-lg border border-slate-700 bg-slate-900 px-4 py-2.5 text-sm text-white placeholder-slate-500 focus:border-cyan-500 focus:outline-none"
                >

                <button
                    type="submit"
                    class="shrink-0 rounded-lg bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-500">
                    Search
                </button>

            </form>

        </div>

    </div>


    

    
    {{-- Three Cards in One Horizontal Row --}}
    <div class="flex w-full flex-nowrap gap-3">

        {{-- Total --}}
        <a href="{{ route('admin.devices.index', ['status' => 'all']) }}"
        class="min-w-0 flex-1 rounded-xl border border-slate-700 bg-slate-900 p-3 hover:border-cyan-500">
            <p class="text-xs text-slate-400">Total</p>
            <p class="text-xl font-bold text-white">{{ $totalDevices }}</p>
        </a>

        {{-- Online --}}
        <a href="{{ route('admin.devices.index', ['status' => 'online']) }}"
        class="min-w-0 flex-1 rounded-xl border border-emerald-800 bg-slate-900 p-3 hover:border-emerald-500">
            <p class="text-xs text-slate-400">Online</p>
            <p class="text-xl font-bold text-emerald-400">{{ $onlineDevices }}</p>
        </a>

        {{-- Offline --}}
        <a href="{{ route('admin.devices.index', ['status' => 'offline']) }}"
        class="min-w-0 flex-1 rounded-xl border border-red-900 bg-slate-900 p-3 hover:border-red-500">
            <p class="text-xs text-slate-400">Offline</p>
            <p class="text-xl font-bold text-red-400">{{ $offlineDevices }}</p>
        </a>

    </div>
    <div>
        {{-- Show All Device Logs --}}
        <a href="{{ route('admin.devices.all-logs') }}"
        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg border border-cyan-700 bg-cyan-600/10 px-4 py-2.5 text-sm font-semibold text-cyan-300 transition hover:border-cyan-400 hover:bg-cyan-600/20">

            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.8">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 7h6m-6 4h6"/>
            </svg>

            Show All Device Logs
        </a>
    </div>



    {{-- Device List --}}
    <div>
        <h2 class="mb-4 text-lg font-semibold text-white">Registered Devices</h2>

        @if ($devices->isEmpty())
            <div class="rounded-xl border border-slate-700 bg-slate-900 p-8 text-center">
                <p class="font-medium text-white">No devices found</p>
                <p class="mt-1 text-sm text-slate-400">
                    Try another search or wait for a computer to register.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3">
                
                @foreach ($devices as $device)
                    <div class="relative">

                        {{-- Delete icon inside this device card --}}
                        <form
                            method="POST"
                            action="{{ route('admin.devices.destroy', $device) }}"
                            onsubmit="return confirm('Delete this device? Its historical activity logs will be preserved.');"
                            class="absolute right-3 top-3 z-20"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                title="Delete device"
                                aria-label="Delete device"
                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-500/30 bg-slate-950 text-red-400 transition hover:border-red-500 hover:bg-red-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-red-500"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="1.8">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3m-9 0h12"/>
                                </svg>
                            </button>
                        </form>

                        {{-- Device card --}}
                        <a href="{{ route('admin.devices.logs', $device) }}"
                        class="block rounded-xl border border-slate-700 bg-slate-900 p-5 pt-14 transition hover:border-cyan-500 hover:bg-slate-800">

                            <div class="mb-4 flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h3 class="break-words font-bold text-white">
                                        {{ $device->hostname }}
                                    </h3>
                                    <p class="mt-1 text-sm text-slate-400">
                                        {{ $device->ip_address ?: 'No IP address' }}
                                    </p>
                                </div>

                                @if (strtolower($device->status ?? '') === 'online')
                                    <span class="shrink-0 rounded-full bg-emerald-900/60 px-3 py-1 text-xs font-semibold text-emerald-300">
                                        Online
                                    </span>
                                @else
                                    <span class="shrink-0 rounded-full bg-red-900/60 px-3 py-1 text-xs font-semibold text-red-300">
                                        Offline
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-3 border-t border-slate-800 pt-4">

                                <div class="flex justify-between gap-3 text-sm">
                                    <span class="text-slate-400">Operating System</span>
                                    <span class="text-right text-slate-200">
                                        {{ $device->operating_system ?: 'N/A' }}
                                    </span>
                                </div>

                                <div class="flex justify-between gap-3 text-sm">
                                    <span class="text-slate-400">MAC Address</span>
                                    <span class="break-all text-right text-slate-200">
                                        {{ $device->mac_address ?: 'N/A' }}
                                    </span>
                                </div>

                                <div class="flex justify-between gap-3 text-sm">
                                    <span class="text-slate-400">CPU Usage</span>
                                    <span class="font-semibold text-cyan-300">
                                        {{ $device->cpu_usage !== null ? $device->cpu_usage . '%' : 'N/A' }}
                                    </span>
                                </div>

                                <div class="flex justify-between gap-3 text-sm">
                                    <span class="text-slate-400">RAM Usage</span>
                                    <span class="font-semibold text-cyan-300">
                                        {{ $device->ram_usage !== null ? $device->ram_usage . '%' : 'N/A' }}
                                    </span>
                                </div>

                                {{-- Battery Percentage --}}
                                <div class="flex justify-between gap-3 text-sm">
                                    <span class="text-slate-400">Battery</span>

                                    @if ($device->battery_percentage !== null)
                                        <span class="font-semibold
                                            {{ $device->battery_percentage <= 20
                                                ? 'text-red-400'
                                                : ($device->battery_percentage <= 50
                                                    ? 'text-amber-300'
                                                    : 'text-emerald-400') }}">
                                            {{ $device->battery_percentage }}%
                                        </span>
                                    @else
                                        <span class="text-slate-500">N/A</span>
                                    @endif
                                </div>

                                <div class="flex justify-between gap-3 text-sm">
                                    <span class="text-slate-400">Last Heartbeat</span>
                                    <span class="text-right text-slate-300">
                                        {{ $device->last_seen_at
                                            ? $device->last_seen_at->format('d M Y, h:i A')
                                            : 'Never' }}
                                    </span>
                                </div>

                            </div>
                        </a>
                    </div>
                @endforeach

            </div>

            <div class="mt-6">
                {{ $devices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
