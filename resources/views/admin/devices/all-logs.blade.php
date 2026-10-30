@extends('layouts.admin')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <a href="{{ route('admin.devices.index') }}"
               class="text-sm text-cyan-400 hover:text-cyan-300">
                &larr; Back to Device Management
            </a>

            <h1 class="mt-3 text-2xl font-bold text-white">
                All Device Logs
            </h1>

            <p class="mt-1 text-sm text-slate-400">
                Activity history from all registered devices.
            </p>
        </div>

        <form method="GET"
              action="{{ route('admin.devices.all-logs') }}"
              class="flex gap-2">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search device or activity..."
                class="w-full rounded-lg border border-slate-700 bg-slate-900 px-4 py-2 text-sm text-white placeholder-slate-500 focus:border-cyan-500 focus:outline-none sm:w-72"
            >

            <button type="submit"
                    class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-500">
                Search
            </button>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-700 bg-slate-900">
        <div class="border-b border-slate-700 p-4">
            <h2 class="font-semibold text-white">Activity History</h2>
            <p class="mt-1 text-sm text-slate-400">
                {{ $activities->total() }} total log records
            </p>
        </div>

        @if ($activities->isEmpty())
            <div class="p-10 text-center">
                <p class="font-semibold text-white">No device logs found</p>
                <p class="mt-2 text-sm text-slate-400">
                    Activity logs will appear here once records are saved.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-950 text-xs uppercase text-slate-400">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-3">Date &amp; Time</th>
                            <th class="whitespace-nowrap px-4 py-3">Device</th>
                            <th class="whitespace-nowrap px-4 py-3">IP Address</th>
                            <th class="whitespace-nowrap px-4 py-3">Activity</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="whitespace-nowrap px-4 py-3">User    </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-800 text-slate-300">
                        @foreach ($activities as $activity)
                            <tr class="hover:bg-slate-800/60">
                                <td class="whitespace-nowrap px-4 py-4 text-xs text-slate-400">
                                    {{ $activity->created_at?->format('d M Y, h:i:s A') ?? 'N/A' }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    <p class="font-semibold text-white">
                                        {{ $activity->device?->hostname ?? 'Unknown Device' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $activity->device?->operating_system ?? 'N/A' }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    {{ $activity->ip_address ?? $activity->device?->ip_address ?? 'N/A' }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    {{ $activity->activity_type ?? 'Activity' }}
                                </td>

                                <td class="min-w-64 px-4 py-4">
                                    {{ $activity->description ?? 'No description provided.' }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    <p class="font-semibold text-white">
                                        {{ $activity->user?->name ?? 'Unknown User' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Strand:
                                        {{ $activity->user?->strand ?? 'N/A' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Year:
                                        {{ $activity->user?->year ?? 'N/A' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-400">
                                        Section:
                                        {{ $activity->user?->section ?? 'N/A' }}
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-700 p-4">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
