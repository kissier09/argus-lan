@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">

    <div>
        <a href="{{ route('admin.devices.index') }}"
           class="text-sm font-medium text-cyan-400 hover:text-cyan-300">
            &larr; Back to Device Management
        </a>

        <h1 class="mt-4 text-2xl font-bold text-white">
            Add Device
        </h1>

        <p class="mt-1 text-sm text-slate-400">
            Register a computer in the ARGUS-LAN monitoring system.
        </p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-800 bg-red-950/40 p-4 text-sm text-red-300">
            <p class="font-semibold">Please correct the following:</p>
            <ul class="mt-2 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('admin.devices.store') }}"
          class="space-y-5 rounded-xl border border-slate-700 bg-slate-900 p-6">
        @csrf

        <div>
            <label for="hostname" class="mb-2 block text-sm font-medium text-slate-200">
                Hostname <span class="text-red-400">*</span>
            </label>
            <input
                id="hostname"
                name="hostname"
                type="text"
                value="{{ old('hostname') }}"
                required
                maxlength="255"
                placeholder="e.g. OFFICE-PC-01"
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white placeholder-slate-500 focus:border-cyan-500 focus:outline-none"
            >
        </div>

        <div>
            <label for="ip_address" class="mb-2 block text-sm font-medium text-slate-200">
                IP Address
            </label>
            <input
                id="ip_address"
                name="ip_address"
                type="text"
                value="{{ old('ip_address') }}"
                placeholder="e.g. 192.168.1.100"
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white placeholder-slate-500 focus:border-cyan-500 focus:outline-none"
            >
        </div>

        <div>
            <label for="operating_system" class="mb-2 block text-sm font-medium text-slate-200">
                Operating System
            </label>
            <select
                id="operating_system"
                name="operating_system"
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white focus:border-cyan-500 focus:outline-none"
            >
                <option value="">Select operating system</option>
                @foreach (['Windows 11', 'Windows 10', 'Windows Xp', 'Windows 7', 'Other'] as $os)
                    <option value="{{ $os }}" @selected(old('operating_system') === $os)>
                        {{ $os }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="rounded-lg border border-slate-700 bg-slate-950/60 p-4">
            <p class="text-sm font-semibold text-white">Initial Status</p>
            <p class="mt-1 text-sm text-slate-400">
                The device will be registered as Offline until its agent reports in.
            </p>
        </div>

       
        <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:justify-end">
            <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-500">
                Register Device
            </button>

            <a href="{{ route('admin.devices.index') }}"
            class="inline-flex items-center justify-center rounded-lg border border-slate-700 px-5 py-3 text-sm font-semibold text-slate-300 hover:bg-slate-800">
                Cancel
            </a>
        
        </div>

    </form>
</div>
@endsection
