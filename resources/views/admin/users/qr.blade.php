@extends('layouts.admin')

@section('content')
<div style="max-width:760px; margin:30px auto; padding:24px; background:#fff; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,.08);">

    <div id="qrCard" style="text-align:center; padding:24px;">
        <h1 style="font-size:26px; font-weight:700; margin-bottom:8px;">
            ARCLUS Student QR Code
        </h1>

        <p style="color:#6b7280; margin-bottom:24px;">
            Personal student identification
        </p>

        <div style="margin:0 auto 20px; width:320px; max-width:100%;">
            {!! $qrCodeSvg !!}
        </div>

        <h2 style="font-size:22px; font-weight:700;">
            {{ $user->first_name }} {{ $user->middle_name }} {{ $user->last_name }}
        </h2>

        <p style="margin-top:10px; font-size:18px;">
            <strong>Student ID:</strong>
            {{ $user->student_number }}
        </p>

        <p style="margin-top:8px; color:#6b7280;">
            Keep this QR code safe. It identifies your student account.
        </p>
    </div>

    <div class="no-print" style="display:flex; justify-content:center; flex-wrap:wrap; gap:12px; margin-top:20px;">
        <button type="button" onclick="window.print()"
            style="padding:11px 18px; background:#2563eb; color:white; border:0; border-radius:6px; cursor:pointer;">
            Print QR Code
        </button>

        <button type="button" onclick="downloadQr()"
            style="padding:11px 18px; background:#059669; color:white; border:0; border-radius:6px; cursor:pointer;">
            Save QR as SVG
        </button>

        <a href="{{ route('admin.users.index') }}"
            style="padding:11px 18px; background:#6b7280; color:white; border-radius:6px; text-decoration:none;">
            Back to Students
        </a>
    </div>
</div>

<script>
function downloadQr() {
    const svg = document.querySelector('#qrCard svg');

    if (!svg) {
        alert('QR code not found.');
        return;
    }

    const copy = svg.cloneNode(true);
    copy.setAttribute('xmlns', 'http://www.w3.org/2000/svg');

    const blob = new Blob(
        [new XMLSerializer().serializeToString(copy)],
        { type: 'image/svg+xml;charset=utf-8' }
    );

    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');

    link.href = url;
    link.download = 'ARCLUS-{{ $user->student_number }}-QR.svg';
    link.click();

    URL.revokeObjectURL(url);
}
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }

    #qrCard, #qrCard * {
        visibility: visible;
    }

    #qrCard {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none;
    }

    .no-print {
        display: none !important;
    }
}
</style>
@endsection