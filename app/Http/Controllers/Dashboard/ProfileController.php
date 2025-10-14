<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        $user = Auth::user();
        $users = User::with(['orders' => function ($query) {
            $query->latest()->take(5);
        }])
        ->select(['id', 'uuid', 'name', 'email', 'phone', 'address', 'role', 'profile_image', 'email_verified_at', 'last_login_at', 'created_at'])
        ->get()
        ->map(function ($user) {
            // Ensure profile_image_url is available
            $user->profile_image_url = $user->profile_image_url;
            return $user;
        });

        return Inertia::render('Dashboard/Profile', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail && ! $request->user()->hasVerifiedEmail(),
            'status' => session('status'),
            'user' => $user,
            'users' => $users,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old profile image if exists
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // Store new image
            $imagePath = $request->file('profile_image')->store('profile-images', 'public');
            $validated['profile_image'] = $imagePath;
            
        }

        $user->fill($validated);
        $user->save();

        return redirect()->route('dashboard.admin.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    /**
     * Get all users for admin management
     */
    public function getUsers(Request $request)
    {
        $users = User::with(['orders' => function ($query) {
            $query->latest()->take(5);
        }])
        ->select(['id', 'uuid', 'name', 'email', 'phone', 'address', 'role', 'profile_image', 'email_verified_at', 'last_login_at', 'created_at'])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($user) {
            // Ensure profile_image_url is available
            $user->profile_image_url = $user->profile_image_url;
            return $user;
        });

        return response()->json([
            'success' => true,
            'users' => $users,
        ]);
    }

    /**
     * Get user statistics
     */
    public function getUserStats(Request $request)
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::whereNotNull('email_verified_at')->count(),
            'recent_users' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'admin_users' => User::where('role', 'admin')->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }
}