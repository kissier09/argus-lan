@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <a href="{{ route('admin.devices.index') }}"
               class="text-lg text-cyan-400 hover:text-cyan-300">
                &larr; Back to Device Management
            </a>

            <h1 class="mt-3 text-2xl font-bold text-white">
                Device Logs
            </h1>

            <p class="mt-2 text-xl font-semibold text-slate-200">
                {{ $device->hostname }}
                &middot; {{ $device->ip_address ?: 'No IP address' }}
            </p>
        </div>

        <form method="GET"
              action="{{ route('admin.devices.logs', $device) }}"
              class="flex gap-2">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search device logs..."
                class="w-full rounded-lg border border-slate-700 bg-slate-900 px-4 py-2 text-sm text-white placeholder-slate-500 focus:border-cyan-500 focus:outline-none sm:w-64"
            >

            <button type="submit"
                    class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-500">
                Search
            </button>
        </form>
    </div>

    {{-- Activity History --}}
    <div class="rounded-xl border border-slate-700 bg-slate-900 p-4 sm:p-6">

        <h2 class="mb-4 text-lg font-semibold text-white">
            Activity History
        </h2>

        @if ($activities->isEmpty())
            <div class="rounded-lg border border-slate-800 p-8 text-center">
                <p class="font-medium text-white">No device logs found</p>
                <p class="mt-2 text-sm text-slate-400">
                    Activity recorded for this device will appear here.
                </p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($activities as $activity)
                    <div class="rounded-lg border border-slate-800 bg-slate-950/50 p-4">

                        {{-- Activity Details --}}
                        <div class="flex flex-col justify-between gap-2 sm:flex-row">
                            <div>
                                <p class="font-semibold text-white">
                                    {{ $activity->activity_type ?: 'Activity' }}
                                </p>

                            </div>

                        </div>

                        {{-- User Information --}}
                        <div class="mt-4 rounded-lg border border-cyan-900/60 bg-slate-900 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-400">
                                User Who Logged In
                            </p>

                            <p class="mt-2 font-semibold text-white">
                                {{ $activity->user?->name ?? 'Unknown User' }}
                            </p>

                            <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                                <div>
                                    <p class="text-xs text-slate-500">Strand</p>
                                    <p class="mt-1 text-sm text-slate-200">
                                        {{ $activity->user?->strand ?? 'N/A' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-slate-500">Year Level</p>
                                    <p class="mt-1 text-sm text-slate-200">
                                        {{ $activity->user?->year ?? 'N/A' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-slate-500">Section</p>
                                    <p class="mt-1 text-sm text-slate-200">
                                        {{ $activity->user?->section ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- IP Address and Timestamp --}}
                        <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-xs text-slate-500">
                            <span>
                                {{ $activity->created_at
                                    ? $activity->created_at->format('d M Y, h:i:s A')
                                    : 'Unknown date' }}
                            </span>
                        </div>

                    </div>
                @endforeach
            </div>

            <div class="mt-5">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
