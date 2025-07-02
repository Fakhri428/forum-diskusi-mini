<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Show the moderator profile page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get moderator statistics
        $stats = $this->getModeratorStats();

        // Get moderator activities
        try {
            $activities = \DB::table('moderation_logs')
                ->where('moderator_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } catch (\Exception $e) {
            // Create empty paginator if table doesn't exist
            $activities = new \Illuminate\Pagination\LengthAwarePaginator(
                [], 0, 10
            );
        }

        return view('moderator.profile', compact('stats', 'activities'));
    }

    /**
     * Update the moderator's profile.
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

        return redirect()->route('moderator.profile')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update the moderator's password.
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

        return redirect()->route('moderator.profile')
            ->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Get moderator statistics.
     *
     * @return array
     */
    private function getModeratorStats()
    {
        try {
            // Count approved threads by this moderator
            $approvedThreads = \DB::table('moderation_logs')
                ->where('moderator_id', Auth::id())
                ->where('action', 'approve_thread')
                ->count();

            // Count moderated comments
            $moderatedComments = \DB::table('moderation_logs')
                ->where('moderator_id', Auth::id())
                ->whereIn('action', ['delete_comment', 'edit_comment'])
                ->count();

            // Count resolved reports
            $resolvedReports = \DB::table('moderation_logs')
                ->where('moderator_id', Auth::id())
                ->whereIn('action', ['approve_report', 'reject_report'])
                ->count();

            return [
                'approved_threads' => $approvedThreads,
                'moderated_comments' => $moderatedComments,
                'resolved_reports' => $resolvedReports,
            ];
        } catch (\Exception $e) {
            // Return empty stats if the tables don't exist yet
            return [
                'approved_threads' => 0,
                'moderated_comments' => 0,
                'resolved_reports' => 0,
            ];
        }
    }
}
