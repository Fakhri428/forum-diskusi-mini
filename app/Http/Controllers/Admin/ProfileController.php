<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Show the admin profile page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get admin statistics
        $stats = $this->getAdminStats();

        // Get admin activities
        try {
            $activities = \DB::table('admin_logs')
                ->where('admin_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } catch (\Exception $e) {
            // Create empty paginator if table doesn't exist
            $activities = new \Illuminate\Pagination\LengthAwarePaginator(
                [], 0, 10
            );
        }

        return view('admin.profile', compact('stats', 'activities'));
    }

    /**
     * Update the admin's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'remove_avatar' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        // Update basic info
        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? null;
        $user->location = $validated['location'] ?? null;
        $user->bio = $validated['bio'] ?? null;

        // Handle avatar update
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                @unlink(public_path($user->avatar));
            }

            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();

            // Make sure directory exists
            $uploadPath = public_path('uploads/avatars');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $avatar->move($uploadPath, $avatarName);
            $user->avatar = 'uploads/avatars/' . $avatarName;
        }
        // Remove avatar if requested
        elseif ($request->has('remove_avatar') && $request->remove_avatar) {
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                @unlink(public_path($user->avatar));
            }
            $user->avatar = null;
        }

        $user->save();

        // Log activity
        try {
            \DB::table('admin_logs')->insert([
                'admin_id' => Auth::id(),
                'action' => 'update_profile',
                'description' => 'Admin updated their profile',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail if table doesn't exist
        }

        return redirect()->route('admin.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update the admin's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($validated['new_password']);
        $user->save();

        // Log activity
        try {
            \DB::table('admin_logs')->insert([
                'admin_id' => Auth::id(),
                'action' => 'change_password',
                'description' => 'Admin changed their password',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail if table doesn't exist
        }

        return redirect()->route('admin.profile')
            ->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Get admin statistics.
     *
     * @return array
     */
    private function getAdminStats()
    {
        return [
            'total_users' => User::count(),
            'total_threads' => \App\Models\Thread::count(),
            'total_comments' => \App\Models\Comment::count(),
            'total_categories' => \App\Models\Category::count(),
            'total_moderators' => User::where('role', 'moderator')->count(),
            'total_admins' => User::where('role', 'admin')->count(),
        ];
    }
}
