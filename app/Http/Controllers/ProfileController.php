<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile');
    }

    /**
     * Tampilkan form edit profile
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update profile pengguna
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update basic profile fields
        $user->name = $validated['name'];
        $user->phone = $validated['phone'];
        $user->location = $validated['location'];
        $user->bio = $validated['bio'];

        // Handle avatar upload if provided
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                unlink(public_path($user->avatar));
            }

            // Store new avatar
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = 'uploads/avatars';

            $avatar->move(public_path($avatarPath), $avatarName);
            $user->avatar = '/' . $avatarPath . '/' . $avatarName;
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Menampilkan profil pengguna
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $user = Auth::user();

        // Ambil data thread dan comments untuk ditampilkan di profil
        $threads = $user->threads()->latest()->take(5)->get();
        $comments = $user->comments()->latest()->take(5)->get();

        // Hitung statistik
        $stats = [
            'thread_count' => $user->threads()->count(),
            'comment_count' => $user->comments()->count(),
            'join_date' => $user->created_at->format('d M Y'),
            'last_login' => $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Belum pernah login',
        ];

        return view('profile', compact('user', 'threads', 'comments', 'stats'));
    }
}
