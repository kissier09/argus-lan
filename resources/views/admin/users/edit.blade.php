@extends('layouts.admin')

@section('title', 'Edit Student')
@section('page-heading', 'Edit Student')

@section('content')
    {{-- Page Heading --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold tracking-tight text-white">
            Edit Student
        </h1>
        <p class="mt-1 text-sm text-slate-400">
            Update the student's academic information or recapture their photo.
        </p>
    </div>

    {{-- Edit Student Container --}}
    <div class="max-w-3xl overflow-hidden rounded-2xl border border-slate-800 bg-slate-900">

        {{-- Header --}}
        <div class="border-b border-slate-800 p-5">
            <h2 class="font-semibold text-white">Student Information</h2>
            <p class="mt-1 text-sm text-slate-400">
                Student ID: {{ $user->student_number ?? 'Not assigned' }}
            </p>
            <p class="mt-1 text-xs text-slate-500">
                Only Strand, Year, Section, and Photo can be changed here.
            </p>
        </div>

        {{-- Form --}}
        <form
            id="editStudentForm"
            method="POST"
            action="{{ route('admin.users.update', $user->id) }}"
            class="space-y-5 p-5"
        >
            @csrf
            @method('PUT')

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="rounded-xl border border-rose-500/30 bg-rose-500/10 p-4 text-sm text-rose-300">
                    <p class="mb-2 font-semibold">
                        Please correct the following:
                    </p>

                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Student Identity: Read Only --}}
            <div class="rounded-xl border border-slate-800 bg-slate-800/50 p-4">
                <h3 class="mb-3 text-sm font-semibold text-slate-200">
                    Student Details
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-400">
                            Student Name
                        </label>
                        <div class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-300">
                            {{ $user->name ?: trim(implode(' ', array_filter([
                                $user->first_name,
                                $user->middle_name,
                                $user->last_name,
                            ]))) ?: 'Unnamed Student' }}
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-medium text-slate-400">
                            Student ID
                        </label>
                        <div class="rounded-lg border border-slate-700 bg-slate-900 px-3 py-2.5 text-sm text-slate-300">
                            {{ $user->student_number ?? 'Not assigned' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Photo Recapture --}}
            <div class="rounded-xl border border-slate-800 p-4">
                <h3 class="text-sm font-semibold text-white">
                    Student Photo
                </h3>

                <p class="mt-1 text-xs text-slate-400">
                    The current photo will be retained unless you capture a new one.
                </p>

                {{-- Existing Photo --}}
                <div class="mt-4">
                    <p class="mb-2 text-xs font-medium text-slate-400">
                        Current Photo
                    </p>

                    @if ($user->photo_path)
                        <img
                            src="{{ asset('storage/' . $user->photo_path) }}"
                            alt="Current student photo"
                            class="h-24 w-24 rounded-lg border border-slate-700 object-cover"
                        >
                    @else
                        <div class="flex h-24 w-24 items-center justify-center rounded-lg border border-slate-700 bg-slate-800 text-xs text-slate-500">
                            No photo
                        </div>
                    @endif
                </div>

                {{-- Camera Preview --}}
                <div class="mt-4">
                    <video
                        id="cameraPreview"
                        autoplay
                        playsinline
                        class="hidden w-full max-w-md rounded-lg border border-slate-700 bg-black"
                    ></video>

                    <canvas id="photoCanvas" class="hidden"></canvas>

                    {{-- New Photo Preview --}}
                    <div id="newPhotoContainer" class="mt-3 hidden">
                        <p class="mb-2 text-xs font-medium text-emerald-400">
                            New Captured Photo
                        </p>

                        <img
                            id="photoPreview"
                            alt="New captured student photo"
                            class="h-24 w-24 rounded-lg border border-emerald-500 object-cover"
                        >
                    </div>

                    {{-- Photo data sent to controller --}}
                    <input
                        type="hidden"
                        name="photo_data"
                        id="photoData"
                        value=""
                    >

                    {{-- Camera Buttons --}}
                    <div class="mt-4 flex flex-wrap gap-2">
                        <button
                            type="button"
                            id="startCamera"
                            class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500"
                        >
                            Open Camera
                        </button>

                        <button
                            type="button"
                            id="capturePhoto"
                            disabled
                            class="rounded-lg bg-emerald-400 px-4 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-emerald-300 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            Capture Photo
                        </button>

                        <button
                            type="button"
                            id="retakePhoto"
                            class="hidden rounded-lg border border-slate-600 px-4 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-slate-800"
                        >
                            Retake Photo
                        </button>

                        <button
                            type="button"
                            id="cancelCamera"
                            class="hidden rounded-lg border border-slate-600 px-4 py-2.5 text-sm font-semibold text-slate-300 transition hover:bg-slate-800"
                        >
                            Cancel Camera
                        </button>
                    </div>

                    <p
                        id="cameraMessage"
                        role="status"
                        aria-live="polite"
                        class="mt-3 text-xs text-slate-400"
                    >
                        Open the camera if you want to replace the current photo.
                    </p>
                </div>
            </div>

            {{-- Editable Academic Information --}}
            <div class="rounded-xl border border-slate-800 p-4">
                <h3 class="mb-4 text-sm font-semibold text-white">
                    Academic Information
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                    {{-- Strand --}}
                    <div>
                        <label
                            for="strand"
                            class="mb-1 block text-sm font-medium text-slate-300"
                        >
                            Strand
                        </label>

                        <input
                            id="strand"
                            name="strand"
                            type="text"
                            required
                            maxlength="100"
                            style="background-color: #1e293b !important; color: #ffffff !important; -webkit-text-fill-color: #ffffff !important; border: 1px solid #475569;"
                            value="{{ old('strand', $user->strand) }}"
                            class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20"
                            placeholder="Enter strand"
                        >
                    </div>

                    {{-- Year --}}
                    <div>
                        <label
                            for="year"
                            class="mb-1 block text-sm font-medium text-slate-300"
                        >
                            Year
                        </label>

                        <input
                            id="year"
                            name="year"
                            type="text"
                            required
                            style="background-color: #1e293b !important; color: #ffffff !important; -webkit-text-fill-color: #ffffff !important; border: 1px solid #475569;"
                            maxlength="50"
                            value="{{ old('year', $user->year) }}"
                            class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20"
                            placeholder="Enter year"
                        >
                    </div>

                    {{-- Section --}}
                    <div>
                        <label
                            for="section"
                            class="mb-1 block text-sm font-medium text-slate-300"
                        >
                            Section
                        </label>

                        <input
                            id="section"
                            name="section"
                            type="text"
                            required
                            style="background-color: #1e293b !important; color: #ffffff !important; -webkit-text-fill-color: #ffffff !important; border: 1px solid #475569;"
                            maxlength="100"
                            value="{{ old('section', $user->section) }}"
                            class="w-full rounded-lg border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/20"
                            placeholder="Enter section"
                        >
                    </div>

                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex flex-wrap justify-end gap-3 border-t border-slate-800 pt-5">
                <a
                    href="{{ route('admin.users.index') }}"
                    class="inline-flex items-center rounded-lg border border-slate-600 px-4 py-2.5 text-sm font-semibold text-slate-300 transition hover:bg-slate-800"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    id="saveChanges"
                    class="inline-flex items-center rounded-lg bg-emerald-400 px-5 py-2.5 text-sm font-bold text-slate-950 transition hover:bg-emerald-300"
                >
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- Back to User Management --}}
    <div class="mt-5">
        <a
            href="{{ route('admin.users.index') }}"
            class="text-sm font-medium text-emerald-300 transition hover:text-emerald-200"
        >
            &larr; Back to User Management
        </a>
    </div>

    {{-- Camera Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('editStudentForm');
            const video = document.getElementById('cameraPreview');
            const canvas = document.getElementById('photoCanvas');
            const photoData = document.getElementById('photoData');
            const photoPreview = document.getElementById('photoPreview');
            const newPhotoContainer = document.getElementById('newPhotoContainer');
            const startButton = document.getElementById('startCamera');
            const captureButton = document.getElementById('capturePhoto');
            const retakeButton = document.getElementById('retakePhoto');
            const cancelButton = document.getElementById('cancelCamera');
            const saveButton = document.getElementById('saveChanges');
            const message = document.getElementById('cameraMessage');

            let cameraStream = null;
            let isSubmitting = false;

            async function stopCamera() {
                if (cameraStream) {
                    cameraStream.getTracks().forEach(track => track.stop());
                    cameraStream = null;
                }

                video.srcObject = null;
                video.classList.add('hidden');
                captureButton.disabled = true;
                cancelButton.classList.add('hidden');
            }

            function showMessage(text, isError = false) {
                message.textContent = text;
                message.classList.toggle('text-rose-400', isError);
                message.classList.toggle('text-slate-400', !isError);
            }

            async function openCamera() {
                try {
                    if (!navigator.mediaDevices ||
                        !navigator.mediaDevices.getUserMedia) {
                        throw new Error(
                            'Camera access requires HTTPS or localhost. Use a secure connection.'
                        );
                    }

                    await stopCamera();

                    cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user',
                            width: { ideal: 1280 },
                            height: { ideal: 720 }
                        },
                        audio: false
                    });

                    video.srcObject = cameraStream;
                    video.classList.remove('hidden');

                    await video.play();

                    captureButton.disabled = false;
                    cancelButton.classList.remove('hidden');
                    showMessage('Position the student in the frame, then capture the photo.');
                } catch (error) {
                    await stopCamera();

                    let errorMessage = 'Unable to open the camera.';

                    if (error.name === 'NotAllowedError') {
                        errorMessage = 'Camera permission was denied. Allow camera access in your browser.';
                    } else if (error.name === 'NotFoundError') {
                        errorMessage = 'No camera was found on this device.';
                    } else if (error.name === 'NotReadableError') {
                        errorMessage = 'The camera may already be in use by another application.';
                    } else if (error.message) {
                        errorMessage = error.message;
                    }

                    showMessage(errorMessage, true);
                }
            }

            startButton.addEventListener('click', openCamera);

            captureButton.addEventListener('click', async () => {
                if (!video.videoWidth || !video.videoHeight) {
                    showMessage('Please wait for the camera image to load.', true);
                    return;
                }

                const maxWidth = 1280;
                const scale = Math.min(1, maxWidth / video.videoWidth);

                canvas.width = Math.round(video.videoWidth * scale);
                canvas.height = Math.round(video.videoHeight * scale);

                const context = canvas.getContext('2d');

                if (!context) {
                    showMessage('Unable to process the captured photo.', true);
                    return;
                }

                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                const capturedImage = canvas.toDataURL('image/jpeg', 0.85);

                if (!capturedImage.startsWith('data:image/jpeg;base64,')) {
                    showMessage('The captured photo could not be processed.', true);
                    return;
                }

                photoData.value = capturedImage;
                photoPreview.src = capturedImage;
                newPhotoContainer.classList.remove('hidden');
                retakeButton.classList.remove('hidden');

                await stopCamera();

                showMessage('Photo captured. Click Save Changes to update the student photo.');
            });

            retakeButton.addEventListener('click', async () => {
                photoData.value = '';
                photoPreview.removeAttribute('src');
                newPhotoContainer.classList.add('hidden');
                retakeButton.classList.add('hidden');

                await openCamera();
            });

            cancelButton.addEventListener('click', async () => {
                await stopCamera();
                showMessage('Camera closed. The existing photo will be kept unless you capture a new one.');
            });

            form.addEventListener('submit', async (event) => {
                if (isSubmitting) {
                    event.preventDefault();
                    return;
                }

                isSubmitting = true;
                saveButton.disabled = true;
                saveButton.textContent = 'Saving...';

                await stopCamera();
            });

            window.addEventListener('pagehide', () => {
                if (cameraStream) {
                    cameraStream.getTracks().forEach(track => track.stop());
                }
            });
        });
    </script>
@endsection
