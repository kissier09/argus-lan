<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\Storage;


class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $users = User::where('registration_status', 'approved')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('student_number', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('strand', 'like', "%{$search}%")
                        ->orWhere('year', 'like', "%{$search}%")
                        ->orWhere('section', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'search'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],

            'strand' => ['required', 'string', 'max:100'],
            'year' => ['required', 'string', 'max:50'],
            'section' => ['required', 'string', 'max:100'],

            'student_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'student_number'),
            ],

            'photo_data' => [
                'required',
                'string',
                'max:3000000',
                'regex:/^data:image\/jpeg;base64,[A-Za-z0-9+\/]+={0,2}$/',
            ],
        ]);

        $nameParts = [
            $validated['first_name'],
            $validated['middle_name'] ?? null,
            $validated['last_name'],
        ];

        $imageData = substr(
            $validated['photo_data'],
            strlen('data:image/jpeg;base64,')
        );

        $imageBinary = base64_decode($imageData, true);

        if ($imageBinary === false) {
            return back()
                ->withErrors(['photo_data' => 'Invalid captured photo.'])
                ->withInput($request->except('photo_data'));
        }

        $imageInfo = @getimagesizefromstring($imageBinary);

        if (
            $imageInfo === false ||
            ($imageInfo['mime'] ?? '') !== 'image/jpeg' ||
            strlen($imageBinary) > 2 * 1024 * 1024
        ) {
            return back()
                ->withErrors([
                    'photo_data' => 'The captured photo is invalid or exceeds 2 MB.'
                ])
                ->withInput($request->except('photo_data'));
        }

        $photoPath = 'student-photos/' . \Illuminate\Support\Str::uuid() . '.jpg';

        \Illuminate\Support\Facades\Storage::disk('public')
            ->put($photoPath, $imageBinary);

        $user = new User();

        $user->first_name = $validated['first_name'];
        $user->middle_name = $validated['middle_name'] ?? null;
        $user->last_name = $validated['last_name'];

        $user->name = implode(
            ' ',
            array_filter($nameParts, fn ($part) => $part !== null && trim($part) !== '')
        );

        $user->strand = $validated['strand'];
        $user->year = $validated['year'];
        $user->section = $validated['section'];
        $user->student_number = $validated['student_number'];

        $user->photo_path = $photoPath;
        $user->qr_code_uuid = (string) Str::uuid();

        $user->registration_status = 'pending';

        $user->save();

        return redirect()
            ->route('admin.users.qr', ['user' => $user->id])
            ->with('success', 'Student created successfully. Personal QR code generated.');
    }
    public function showQr(User $user)
    {
        $result = Builder::create()
            ->writer(new SvgWriter())
            ->data($user->student_number)
            ->size(300)
            ->margin(10)
            ->build();

        $qrCodeSvg = $result->getString();

        return view('admin.users.qr', compact('user', 'qrCodeSvg'));
    }
    public function pending(Request $request)
    {
        $search = trim($request->query('search', ''));

        $users = User::where('registration_status', 'pending')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('student_number', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('strand', 'like', "%{$search}%")
                        ->orWhere('year', 'like', "%{$search}%")
                        ->orWhere('section', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.pending', compact('users', 'search'));
    }

    public function approve(User $user)
    {
        if ($user->registration_status !== 'pending') {
            return redirect()
                ->route('admin.users.pending')
                ->with('error', 'This application is no longer pending.');
        }

        $user->registration_status = 'approved';
        $user->save();

        return redirect()
            ->route('admin.users.pending')
            ->with('success', 'Student approved successfully.');
    }

    public function reject(User $user)
    {
        if ($user->registration_status !== 'pending') {
            return redirect()
                ->route('admin.users.pending')
                ->with('error', 'This application is no longer pending.');
        }

        $user->registration_status = 'rejected';
        $user->save();

        return redirect()
            ->route('admin.users.pending')
            ->with('success', 'Student application rejected.');
    }
    public function deactivate(User $user)
    {
        if ($user->registration_status !== 'approved') {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Only approved students can be deactivated.');
        }

        $user->registration_status = 'deactivated';
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Student deactivated successfully.');
    }
    public function edit(User $user)
    {
        if ($user->registration_status !== 'approved') {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Only approved students can be edited.');
        }

        return view('admin.users.edit', compact('user'));
    }

    
    
    public function update(Request $request, User $user)
    {
        if ($user->registration_status !== 'approved') {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Only approved students can be edited.');
        }

        $validated = $request->validate([
            'strand' => ['required', 'string', 'max:100'],
            'year' => ['required', 'string', 'max:50'],
            'section' => ['required', 'string', 'max:100'],
            'photo_data' => [
                'nullable',
                'string',
                'max:3000000',
                'regex:/^data:image\/jpeg;base64,[A-Za-z0-9+\/]+={0,2}$/',
            ],
        ]);

        $newPhotoPath = null;

        if (!empty($validated['photo_data'])) {
            $imageData = substr(
                $validated['photo_data'],
                strlen('data:image/jpeg;base64,')
            );

            $imageBinary = base64_decode($imageData, true);

            if ($imageBinary === false) {
                return back()
                    ->withErrors(['photo_data' => 'Invalid captured photo.'])
                    ->withInput($request->except('photo_data'));
            }

            $imageInfo = @getimagesizefromstring($imageBinary);

            if (
                $imageInfo === false ||
                ($imageInfo['mime'] ?? '') !== 'image/jpeg' ||
                strlen($imageBinary) > 2 * 1024 * 1024
            ) {
                return back()
                    ->withErrors([
                        'photo_data' => 'The captured photo is invalid or exceeds 2 MB.'
                    ])
                    ->withInput($request->except('photo_data'));
            }

            $newPhotoPath = 'student-photos/' . Str::uuid() . '.jpg';

            Storage::disk('public')->put($newPhotoPath, $imageBinary);
        }

        $user->strand = $validated['strand'];
        $user->year = $validated['year'];
        $user->section = $validated['section'];

        if ($newPhotoPath !== null) {
            $oldPhotoPath = $user->photo_path;
            $user->photo_path = $newPhotoPath;
        }

        $user->save();

        if ($newPhotoPath !== null && !empty($oldPhotoPath)) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Student information and photo updated successfully.');
    }


}