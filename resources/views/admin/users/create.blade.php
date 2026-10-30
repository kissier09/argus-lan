
@extends('layouts.admin')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; padding: 24px; color: #1f2937;">

    <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 24px; flex-wrap: wrap;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700; margin: 0;">Register Student</h1>
            <p style="color: #6b7280; margin-top: 6px;">Enter the student's information to create an ARCLUS account.</p>
        </div>

        <a href="{{ route('admin.users.index') }}"
           style="padding: 10px 16px; background: #e5e7eb; color: #111827; text-decoration: none; border-radius: 6px;">
            Back to Students
        </a>
    </div>

    @if ($errors->any())
        <div style="padding: 14px; margin-bottom: 20px; background: #fee2e2; color: #991b1b; border-radius: 6px;">
            <strong>Please correct the following errors:</strong>
            <ul style="margin: 8px 0 0 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.users.store') }}"
          method="POST"
          enctype="multipart/form-data"
          style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 24px;">

        @csrf

        <h2 style="font-size: 18px; font-weight: 700; margin: 0 0 18px;">Student Information</h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px;">

            <div>
                <label for="first_name" style="display: block; font-weight: 600; margin-bottom: 7px;">First Name *</label>
                <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required maxlength="100"
                       style="box-sizing: border-box; width: 100%; padding: 11px; border: 1px solid #d1d5db; border-radius: 6px;">
            </div>

            <div>
                <label for="middle_name" style="display: block; font-weight: 600; margin-bottom: 7px;">Middle Name</label>
                <input id="middle_name" name="middle_name" type="text" value="{{ old('middle_name') }}" maxlength="100"
                       style="box-sizing: border-box; width: 100%; padding: 11px; border: 1px solid #d1d5db; border-radius: 6px;">
            </div>

            <div>
                <label for="last_name" style="display: block; font-weight: 600; margin-bottom: 7px;">Last Name *</label>
                <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" required maxlength="100"
                       style="box-sizing: border-box; width: 100%; padding: 11px; border: 1px solid #d1d5db; border-radius: 6px;">
            </div>

            <div>
                <label for="strand" style="display: block; font-weight: 600; margin-bottom: 7px;">Strand *</label>
                <input id="strand" name="strand" type="text" value="{{ old('strand') }}" required maxlength="100" placeholder="e.g. STEM, ABM, HUMSS, TVL"
                       style="box-sizing: border-box; width: 100%; padding: 11px; border: 1px solid #d1d5db; border-radius: 6px;">
            </div>

            <div>
                <label for="year" style="display: block; font-weight: 600; margin-bottom: 7px;">Year Level *</label>
                <select id="year" name="year" required
                        style="box-sizing: border-box; width: 100%; padding: 11px; border: 1px solid #d1d5db; border-radius: 6px; background: white;">
                    <option value="">Select year level</option>
                    @foreach (['Grade 11', 'Grade 12'] as $year)
                        <option value="{{ $year }}" {{ old('year') === $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="section" style="display: block; font-weight: 600; margin-bottom: 7px;">Section *</label>
                <input id="section" name="section" type="text" value="{{ old('section') }}" required maxlength="100" placeholder="e.g. STEM 11-A"
                       style="box-sizing: border-box; width: 100%; padding: 11px; border: 1px solid #d1d5db; border-radius: 6px;">
            </div>

            <div>
                <label for="student_number" style="display: block; font-weight: 600; margin-bottom: 7px;">Student Number *</label>
                <input id="student_number" name="student_number" type="text" value="{{ old('student_number') }}" required maxlength="50"
                       style="box-sizing: border-box; width: 100%; padding: 11px; border: 1px solid #d1d5db; border-radius: 6px;">
            </div>

            
            <div style="grid-column: 1 / -1; margin-top: 10px;">
                <label style="display:block; font-weight:600; margin-bottom:10px;">
                    Student Face Reference Photo *
                </label>

                <div style="max-width:520px;">
                    <video id="cameraPreview"
                        autoplay
                        playsinline
                        muted
                        style="display:block; width:100%; background:#111827; border-radius:8px;">
                    </video>

                    <canvas id="captureCanvas" style="display:none;"></canvas>

                    <img id="capturedPreview"
                        alt="Captured student photo"
                        style="display:none; width:100%; max-width:320px; margin-top:12px; border-radius:8px;">

                    <input type="hidden" name="photo_data" id="photo_data">

                    <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:14px;">
                        <button type="button" id="startCamera"
                                style="padding:10px 16px; border:0; border-radius:6px; background:#2563eb; color:white; cursor:pointer;">
                            Start Camera
                        </button>

                        <button type="button" id="capturePhoto" disabled
                                style="padding:10px 16px; border:0; border-radius:6px; background:#059669; color:white; cursor:pointer;">
                            Capture Photo
                        </button>

                        <button type="button" id="retakePhoto" disabled
                                style="padding:10px 16px; border:0; border-radius:6px; background:#6b7280; color:white; cursor:pointer;">
                            Retake Photo
                        </button>
                    </div>

                    <p id="cameraMessage" role="status"
                    style="margin-top:10px; color:#6b7280;">
                        Click Start Camera and allow camera access.
                    </p>
                </div>
            </div>


        </div>


        <div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 12px; flex-wrap: wrap;">
            <button type="submit"
                    style="border: 0; cursor: pointer; padding: 12px 22px; background: #2563eb; color: white; font-weight: 600; border-radius: 6px;">
                Register Student
            </button>

            <a href="{{ route('admin.users.index') }}"
               style="padding: 12px 22px; background: #e5e7eb; color: #111827; text-decoration: none; border-radius: 6px;">
                Cancel
            </a>
        </div>

        <p style="margin-top: 18px; color: #6b7280; font-size: 13px;">
            New student accounts will be marked as pending approval. A unique QR identifier will be assigned when the student is saved.
        </p>

    </form>
</div>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const video = document.getElementById('cameraPreview');
        const canvas = document.getElementById('captureCanvas');
        const photoData = document.getElementById('photo_data');
        const preview = document.getElementById('capturedPreview');
        const startButton = document.getElementById('startCamera');
        const captureButton = document.getElementById('capturePhoto');
        const retakeButton = document.getElementById('retakePhoto');
        const message = document.getElementById('cameraMessage');

        if (!video || !canvas || !photoData || !preview ||
            !startButton || !captureButton || !retakeButton || !message) {
            console.error('ARCLUS webcam elements are missing.');
            return;
        }

        let stream = null;

        async function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }

            video.srcObject = null;
            captureButton.disabled = true;
        }

        startButton.addEventListener('click', async () => {
            try {
                message.textContent = 'Starting camera...';
                startButton.disabled = true;

                await stopCamera();

                if (!navigator.mediaDevices?.getUserMedia) {
                    throw new Error(
                        'Camera requires localhost or a secure HTTPS connection.'
                    );
                }

                stream = await navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: false
                });

                video.srcObject = stream;
                video.style.display = 'block';
                preview.style.display = 'none';
                photoData.value = '';

                await video.play();

                captureButton.disabled = false;
                retakeButton.disabled = true;
                message.textContent =
                    'Camera started. Position the student and click Capture Photo.';
            } catch (error) {
                message.textContent = error.name + ': ' + error.message;
                console.error('ARCLUS camera error:', error);

                await stopCamera();
            } finally {
                startButton.disabled = false;
            }
        });

        captureButton.addEventListener('click', () => {
            if (!stream || !video.videoWidth) {
                message.textContent = 'Start the camera and wait for the video first.';
                return;
            }

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            photoData.value = canvas.toDataURL('image/jpeg', 0.85);
            preview.src = photoData.value;
            preview.style.display = 'block';

            stopCamera();
            video.style.display = 'none';

            captureButton.disabled = true;
            retakeButton.disabled = false;
            message.textContent = 'Photo captured. Review it before saving.';
        });

        retakeButton.addEventListener('click', () => {
            photoData.value = '';
            preview.removeAttribute('src');
            preview.style.display = 'none';
            startButton.click();
        });

        window.addEventListener('pagehide', stopCamera);
    });
</script>

