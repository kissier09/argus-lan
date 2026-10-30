@extends('layouts.admin')

@section('content')
@php
    $studentName = $user->name ?: trim(implode(' ', array_filter([
        $user->first_name,
        $user->middle_name,
        $user->last_name,
    ]))) ?: 'Unnamed Student';
@endphp

<div class="min-w-0 space-y-6 text-slate-100">

    {{-- Page heading --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-400">
                ARGUS-LAN / User Management
            </p>

            <h1 class="mt-2 text-2xl font-bold text-white">
                Student Activity
            </h1>

            <p class="mt-1 text-sm text-slate-400">
                Review recorded activity and access events for this student.
            </p>
        </div>

        <a href="{{ route('admin.users.index') }}"
           class="inline-flex items-center rounded-lg border border-slate-600 bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:border-cyan-500 hover:text-cyan-300">
            &larr; Back to Users
        </a>
    </div>

    {{-- Student information --}}
    <div class="rounded-2xl border border-slate-700 bg-slate-900/80 p-5">
        <div class="flex min-w-0 items-center gap-4">
            @if ($user->photo_path)
                <img
                    src="{{ asset('storage/' . ltrim($user->photo_path, '/')) }}"
                    alt="{{ $studentName }}"
                    class="h-16 w-16 shrink-0 rounded-xl border border-slate-600 object-cover"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                >

                <div
                    style="display:none"
                    class="h-16 w-16 shrink-0 items-center justify-center rounded-xl border border-slate-600 bg-slate-800 text-xl font-bold text-cyan-300"
                >
                    {{ strtoupper(substr($studentName, 0, 1)) }}
                </div>
            @else
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-xl border border-slate-600 bg-slate-800 text-xl font-bold text-cyan-300">
                    {{ strtoupper(substr($studentName, 0, 1)) }}
                </div>
            @endif

            <div class="min-w-0 flex-1">
                <h2 class="break-words text-lg font-bold text-white">
                    {{ $studentName }}
                </h2>

                <p class="mt-1 text-sm text-slate-400">
                    Student ID:
                    <span class="text-slate-200">
                        {{ $user->student_number ?? 'Not assigned' }}
                    </span>
                </p>

                <p class="mt-1 text-xs text-slate-400">
                    {{ $user->strand ?? 'No strand' }}
                    &middot; {{ $user->year ?? 'N/A' }}
                    &middot; {{ $user->section ?? 'N/A' }}
                </p>
            </div>
        </div>
    </div>
    
    {{-- Search activity logs --}}
    <form method="GET"
        action="{{ route('admin.users.notifications', $user) }}"
        class="border-b border-slate-700 p-4">

        <div class="flex flex-col gap-3 sm:flex-row">
            <input
                type="text"
                name="search"
                value="{{ $search ?? request('search', '') }}"
                placeholder="Search activity, description, PC hostname, or IP..."
                class="w-full min-w-0 rounded-lg border border-slate-600 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder-slate-400 focus:border-cyan-400 focus:outline-none focus:ring-2 focus:ring-cyan-400/30"
            >

            <button
                type="submit"
                class="rounded-lg bg-cyan-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-500">
                Search
            </button>

            @if (($search ?? '') !== '')
                <a
                    href="{{ route('admin.users.notifications', $user) }}"
                    class="rounded-lg border border-slate-600 px-5 py-2.5 text-center text-sm font-semibold text-slate-300 transition hover:bg-slate-800">
                    Clear
                </a>
            @endif
        </div>
    </form>


    {{-- Activity log --}}
    <div class="overflow-hidden rounded-2xl border border-slate-700 bg-slate-900/80">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-700 px-5 py-4">
            <div>
                <h2 class="font-bold text-white">Activity History</h2>
                <p class="mt-1 text-xs text-slate-400">
                    Recorded events for this student, newest first.
                </p>
            </div>

            <span class="rounded-full border border-cyan-500/30 bg-cyan-500/10 px-3 py-1 text-xs font-semibold text-cyan-300">
                {{ $activities->total() }} records
            </span>
        </div>

        @if ($activities->count())
            <div class="divide-y divide-slate-800">
                @foreach ($activities as $activity)
                    <div class="flex flex-col gap-3 px-5 py-4 transition hover:bg-slate-800/40 sm:flex-row sm:items-start sm:justify-between">
                        <div class="flex min-w-0 gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-700 bg-slate-800 text-cyan-300">
                                <span aria-hidden="true">&#9679;</span>
                            </div>

                            <div class="min-w-0">
                                <h3 class="break-words text-sm font-semibold text-white">
                                    {{ \Illuminate\Support\Str::headline($activity->activity_type) }}
                                </h3>

                                <p class="mt-1 break-words text-sm text-slate-400">
                                    {{ $activity->description ?: 'No additional details recorded.' }}
                                </p>

                                <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                                    <span>
                                        PC:
                                        {{ $activity->device?->hostname ?? 'Unknown device' }}
                                    </span>
                                </div>

                                <p class="mt-2 text-xs text-slate-500">
                                    {{ $activity->created_at?->timezone('Asia/Manila')->format('d M Y, h:i:s A') ?? 'Time not recorded' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-slate-700 px-5 py-4">
                {{ $activities->links() }}
            </div>
        @else
            <div class="px-5 py-14 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl border border-slate-700 bg-slate-800 text-2xl text-cyan-300">
                    &#128203;
                </div>

                <h3 class="mt-4 font-semibold text-white">
                    No activity recorded yet
                </h3>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-400">
                    Activity for this student will appear here when the application records events such as QR scans, face verification attempts, and login or logout activity.
                </p>
            </div>
        @endif
    </div>

</div>
@endsection
