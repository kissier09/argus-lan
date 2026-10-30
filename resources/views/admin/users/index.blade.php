 @extends('layouts.admin')

@section('title', 'User Management')
@section('page-heading', 'User Management')

@section('content')
<div>
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
</div>
    @if (session('success'))
        <div class="mb-5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-sm text-emerald-300">
            {{ session('success') }}
        </div>
    @endif

    {{-- Page Heading --}}
    
    
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-white">
            User Management
        </h1>
        <p class="mt-1 text-sm text-slate-400">
            View and manage registered student accounts.
        </p>
    </div>
    

    {{-- Registered Users Container --}}
    <div class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900">

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-800 p-5">
            <div>
                <h2 class="font-semibold text-white">Registered Users</h2>
                <p class="mt-1 text-sm text-slate-400">
                    Approved student accounts
                </p>
            </div>

            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-emerald-300 bg-emerald-400 px-4 py-2.5 text-sm font-bold text-slate-950 transition hover:bg-emerald-300">
                <span class="text-lg leading-none">+</span>
                Add User
            </a>
        </div>

        {{-- Search --}}
        <div class="p-5">
            <form method="GET"
                  action="{{ route('admin.users.index') }}"
                  class="flex flex-wrap gap-2">

                <input
                    type="search"
                    name="search"
                    value="{{ $search ?? request('search') }}"
                    placeholder="Search student ID, name, strand, year, section..."
                    aria-label="Search approved students"
                    class="min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                >

                <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500">
                    Search
                </button>

                @if(request('search'))
                    <a href="{{ route('admin.users.index') }}"
                       class="inline-flex items-center rounded-lg bg-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-300">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Compact Student Cards: 3 per row on desktop --}}
        <div class="px-5 pb-5">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">

                @forelse($users as $user)
                    <article class="relative box-border w-full min-w-0 overflow-visible rounded-xl border border-slate-700 bg-slate-800/70 p-3 transition hover:border-slate-500">
                        {{-- Ellipsis Menu --}}
                        <div class="absolute top-2 right-2 z-50 ml-auto">
                            <details class="relative">
                                <summary
                                    class="flex h-7 w-7 cursor-pointer list-none items-center justify-center rounded-md text-xl leading-none text-slate-300 hover:bg-slate-700 hover:text-white"
                                    title="Student options"
                                    aria-label="Student options"
                                >&#8942;</summary>

                                <div class="absolute right-0 top-8 z-50 w-44 overflow-hidden rounded-lg border border-slate-600 bg-slate-900 py-1 shadow-xl">
                                    <a
                                        href="{{ route('admin.users.edit', $user->id) }}"
                                        class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-700"
                                    >
                                        Edit Student
                                    </a>

                                    <form
                                        action="{{ route('admin.users.deactivate', $user->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to deactivate this student?')"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="block w-full px-4 py-2 text-left text-sm text-red-400 hover:bg-slate-700"
                                        >
                                            Deactivate Student
                                        </button>
                                    </form>
                                </div>
                            </details>
                        </div>
                        {{-- Student Information --}}
                        <div class="flex min-w-0 items-center gap-3">

                            {{-- Student Photo --}}
                            <div class="h-14 w-14 flex-shrink-0 overflow-hidden rounded-lg border border-slate-600 bg-slate-700">
                                @if($user->photo_path)
                                    <img
                                        src="{{ asset('storage/' . $user->photo_path) }}"
                                        alt="Student photo"
                                        loading="lazy"
                                        class="h-20 w-20 shrink-0 rounded-xl border border-slate-600 object-cover"
                                    >
                                @else
                                    <div class="flex h-full w-full items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="1.5"
                                             class="h-8 w-8 text-slate-400">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.1a7.5 7.5 0 0 1 15 0A17.9 17.9 0 0 1 12 21.75c-2.7 0-5.25-.6-7.5-1.65Z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Student Details --}}
                            <div class="min-w-0 flex-1 overflow-hidden">
                                <h3 class="truncate text-sm font-bold text-white"
                                    title="{{ $user->name }}">
                                    {{ $user->name ?: trim(implode(' ', array_filter([
                                        $user->first_name,
                                        $user->middle_name,
                                        $user->last_name
                                    ]))) ?: 'Unnamed Student' }}
                                </h3>

                                <p class="mt-0.5 truncate text-xs text-slate-400">
                                    ID: {{ $user->student_number ?? 'Not assigned' }}
                                </p>

                                <p class="mt-1 truncate text-xs text-slate-300">
                                    {{ $user->strand ?? 'No strand' }}
                                    <span class="text-slate-500">·</span>
                                    {{ $user->year ?? 'N/A' }}
                                    <span class="text-slate-500">·</span>
                                    {{ $user->section ?? 'N/A' }}
                                </p>
                            </div>
                        </div>

                        {{-- Account Status --}}
                        <div class="mt-3 border-t border-slate-700 pt-2">
                            @if($user->account_status === 'active')
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-400">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span>
                                    Inactive
                                </span>
                            @endif
                        </div>

                        {{-- Student Actions --}}
                        <div class="mt-3 grid min-w-0 grid-cols-2 gap-2">
                            <a href="{{ route('admin.users.qr', $user) }}"
                               class="inline-flex items-center justify-center gap-1.5 rounded-md border border-slate-600 bg-slate-700 px-2 py-2 text-xs font-semibold text-slate-100 transition hover:bg-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8"
                                     class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3.75 3.75h6v6h-6v-6Zm10.5 0h6v6h-6v-6Zm-10.5 10.5h6v6h-6v-6Zm10.5 0h2.25v2.25h-2.25v-2.25Zm3.75 0h2.25v6h-2.25v-6Zm-3.75 3.75h2.25v2.25h-2.25v-2.25Z"/>
                                </svg>
                                View QR
                            </a>

                            <a href="{{ url('/admin/users/' . $user->id . '/notifications') }}"
                               class="inline-flex items-center justify-center gap-1.5 rounded-md border border-blue-500/40 bg-blue-600/20 px-2 py-2 text-xs font-semibold text-blue-300 transition hover:bg-blue-600/30 hover:text-blue-200">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="1.8"
                                     class="h-4 w-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M14.25 18.75a2.25 2.25 0 0 1-4.5 0m9-5.25V10.5a6.75 6.75 0 1 0-13.5 0v3l-1.5 3h16.5l-1.5-3Z"/>
                                </svg>
                                Activity Logs
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-slate-700 px-4 py-10 text-center">
                        <p class="text-sm font-medium text-slate-300">
                            No approved students found.
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            Try another search or add a student account.
                        </p>
                    </div>
                @endforelse

            </div>

            {{-- Pagination --}}
            @if($users->hasPages())
                <div class="mt-5 border-t border-slate-800 pt-4">
                    {{ $users->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection