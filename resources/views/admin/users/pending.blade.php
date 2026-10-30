 @extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-7xl space-y-4 px-3 py-4">

    {{-- Header --}}
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
    
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-white">Pending Applications</h1>
            <p class="mt-1 text-xs text-slate-400">Review and manage student registrations.</p>
        </div>

        <span class="rounded-full border border-amber-500/30 bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-300">
            {{ $users->total() }} Pending
        </span>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.users.pending') }}" class="flex gap-2">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search name or school ID..."
            class="w-full rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white placeholder-slate-500 focus:border-cyan-500 focus:outline-none"
        >

        <button type="submit"
            class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white hover:bg-cyan-500">
            Search
        </button>
    </form>

    {{-- Student Cards --}}
    @if ($users->count())
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">

            @foreach ($users as $user)
                @php
                    $studentName = trim(
                        $user->first_name . ' ' .
                        ($user->middle_name ? $user->middle_name . ' ' : '') .
                        $user->last_name
                    );

                    if ($studentName === '') {
                        $studentName = $user->name ?? 'Unnamed Student';
                    }

                    $photoPath = $user->photo_path
                        ? asset('storage/' . ltrim($user->photo_path, '/'))
                        : null;
                @endphp

                <article class="overflow-hidden rounded-xl border border-slate-700 bg-slate-900 shadow-sm transition hover:border-cyan-500/60">

                    {{-- Top accent --}}
                    <div class="h-1 bg-gradient-to-r from-cyan-500 via-blue-500 to-violet-500"></div>

                    <div class="space-y-3 p-3">

                        {{-- Photo and student details --}}
                        <div class="flex items-center gap-3">
                            @if ($photoPath)
                                <img
                                    src="{{ $photoPath }}"
                                    alt="{{ $studentName }}"
                                    class="h-14 w-14 shrink-0 rounded-lg border border-slate-600 object-cover"
                                    onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                                >
                                <div class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-lg bg-slate-800 text-lg font-bold text-cyan-300">
                                    {{ strtoupper(substr($studentName, 0, 1)) }}
                                </div>
                            @else
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg border border-slate-700 bg-slate-800 text-lg font-bold text-cyan-300">
                                    {{ strtoupper(substr($studentName, 0, 1)) }}
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <h2 class="break-words text-sm font-bold text-white">
                                    {{ $studentName }}
                                </h2>

                                <p class="mt-1 break-all text-xs text-slate-400">
                                    School ID: {{ $user->student_number ?? 'Not provided' }}
                                </p>

                                <span class="mt-1 inline-block rounded-full bg-amber-500/10 px-2 py-0.5 text-[10px] font-semibold text-amber-300">
                                    Awaiting Approval
                                </span>
                            </div>
                        </div>

                        {{-- Student information --}}
                        <div class="grid grid-cols-3 gap-2">
                            <div class="rounded-lg border border-slate-700/80 bg-slate-800/70 p-2">
                                <p class="text-[10px] text-slate-400">Strand</p>
                                <p class="mt-1 break-words text-xs font-semibold text-slate-200">
                                    {{ $user->strand ?: 'N/A' }}
                                </p>
                            </div>

                            <div class="rounded-lg border border-slate-700/80 bg-slate-800/70 p-2">
                                <p class="text-[10px] text-slate-400">Year</p>
                                <p class="mt-1 break-words text-xs font-semibold text-slate-200">
                                    {{ $user->year ?: 'N/A' }}
                                </p>
                            </div>

                            <div class="rounded-lg border border-slate-700/80 bg-slate-800/70 p-2">
                                <p class="text-[10px] text-slate-400">Section</p>
                                <p class="mt-1 break-words text-xs font-semibold text-slate-200">
                                    {{ $user->section ?: 'N/A' }}
                                </p>
                            </div>
                        </div>

                        {{-- Approve / Reject --}}
                        <div class="flex gap-2 border-t border-slate-700 pt-3">
                            <form
                                method="POST"
                                action="{{ route('admin.users.approve', $user) }}"
                                class="flex-1"
                                onsubmit="return confirm('Approve this student registration?');"
                            >
                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                    class="w-full rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-500">
                                    Approve
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('admin.users.reject', $user) }}"
                                class="flex-1"
                                onsubmit="return confirm('Reject this student registration?');"
                            >
                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                    class="w-full rounded-lg border border-red-500/40 bg-red-500/10 px-3 py-1.5 text-xs font-semibold text-red-300 transition hover:bg-red-500 hover:text-white">
                                    Reject
                                </button>
                            </form>
                        </div>

                    </div>
                </article>
            @endforeach

        </div>

        {{-- Pagination --}}
        <div class="pt-2">
            {{ $users->withQueryString()->links() }}
        </div>
    @else
        <div class="rounded-xl border border-dashed border-slate-700 bg-slate-900 px-4 py-10 text-center">
            <div class="text-3xl">📋</div>
            <h2 class="mt-2 text-sm font-semibold text-white">No pending applications</h2>
            <p class="mt-1 text-xs text-slate-400">
                No students match your search, or all applications have been reviewed.
            </p>
        </div>
    @endif

</div>
@endsection