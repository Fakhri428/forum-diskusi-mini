<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $query = User::query();

    // Filter pencarian
    if ($request->has('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    if ($request->has('role') && $request->role !== '') {
        $query->where('role', $request->role);
    }

    // Sorting
    $sortField = $request->sort_by ?? 'created_at';
    $sortDirection = $request->sort_direction ?? 'desc';
    $query->orderBy($sortField, $sortDirection);

    // Pagination
    $users = $query->paginate(15);

    $stats = [
        'all' => User::count(),
        'admin' => User::where('role', 'admin')->count(),
        'moderator' => User::where('role', 'moderator')->count(),
        'user' => User::where('role', 'member')->count(), // ganti ke 'user' kalau kamu pakai role itu
        'banned' => User::whereNotNull('banned_at')->count(),
        'unverified' => User::whereNull('email_verified_at')->count(),
    ];

    return view('admin.users.index', compact('users', 'stats'));
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(['admin', 'moderator', 'member'])],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Pengguna berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Only load existing relationships
        $user->load(['threads', 'comments']);

        // Initialize empty variables
        $activities = [];
        $reports = [];

        // Get user stats
        $stats = [
            'thread_count' => $user->threads()->count(),
            'comment_count' => $user->comments()->count(),
            'joined_date' => $user->created_at->format('d M Y'),
            'last_login' => $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Belum pernah login',
        ];

        return view('admin.users.show', compact('user', 'activities', 'reports', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'moderator', 'member'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Pengguna berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Don't allow admin to delete themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                             ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        // Handle associated data before deletion if needed
        // For example, you might want to reassign or delete threads, comments, etc.

        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'Pengguna berhasil dihapus!');
    }

    /**
     * Toggle user active status.
     */
    public function toggleActive(User $user)
    {
        // Don't allow admin to deactivate themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                             ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri!');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.users.index')
                         ->with('success', "Pengguna berhasil {$status}!");
    }

    /**
     * Update user role.
     */
    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'moderator', 'member'])],
        ]);

        // Don't allow admin to change their own role
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                             ->with('error', 'Anda tidak dapat mengubah peran Anda sendiri!');
        }

        $user->update([
            'role' => $validated['role']
        ]);

        return redirect()->route('admin.users.index')
                         ->with('success', 'Peran pengguna berhasil diperbarui!');
    }

    /**
     * Handle batch actions for users
     */
    public function batchAction(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action' => 'required|string|in:delete,set_role,activate,deactivate',
            'role' => 'nullable|string|in:admin,moderator,member',
        ]);

        $userIds = $validated['user_ids'];
        $action = $validated['action'];
        $count = count($userIds);

        // Don't allow admin to change their own account in batch actions
        if (in_array(auth()->id(), $userIds)) {
            return redirect()->route('admin.users.index')
                            ->with('error', 'Anda tidak dapat mengubah akun Anda sendiri dalam aksi batch!');
        }

        switch ($action) {
            case 'delete':
                User::whereIn('id', $userIds)->delete();
                $message = "{$count} pengguna berhasil dihapus!";
                break;

            case 'set_role':
                if (!isset($validated['role'])) {
                    return redirect()->route('admin.users.index')
                                    ->with('error', 'Role harus dipilih untuk mengubah peran!');
                }

                User::whereIn('id', $userIds)->update(['role' => $validated['role']]);
                $message = "{$count} pengguna berhasil diubah perannya menjadi {$validated['role']}!";
                break;

            case 'activate':
                User::whereIn('id', $userIds)->update(['is_active' => true]);
                $message = "{$count} pengguna berhasil diaktifkan!";
                break;

            case 'deactivate':
                User::whereIn('id', $userIds)->update(['is_active' => false]);
                $message = "{$count} pengguna berhasil dinonaktifkan!";
                break;
        }

        return redirect()->route('admin.users.index')
                        ->with('success', $message);
    }

    /**
     * Verify user email manually.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyEmail(User $user)
    {
        // Pastikan user belum diverifikasi
        if ($user->email_verified_at) {
            return redirect()->route('admin.users.edit', $user->id)
                            ->with('info', 'Email pengguna ini sudah diverifikasi sebelumnya.');
        }

        // Update waktu verifikasi email
        $user->email_verified_at = now();
        $user->save();

        // Kirim notifikasi ke user (opsional)
        if ($user->email) {
            // Implementasi untuk mengirim email pemberitahuan
            // Mail::to($user->email)->send(new EmailManuallyVerified($user));
        }

        return redirect()->route('admin.users.edit', $user->id)
                        ->with('success', 'Email pengguna berhasil diverifikasi.');
    }

    /**
     * Ban a user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ban(User $user)
    {
        // Prevent banning yourself
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.edit', $user->id)
                            ->with('error', 'Anda tidak dapat memblokir akun Anda sendiri.');
        }

        // Ban user
        $user->banned_at = now();
        // $user->ban_reason = 'Manual ban by admin'; // Tambahkan alasan jika diperlukan
        $user->save();

        // Return back
        return redirect()->route('admin.users.edit', $user->id)
                        ->with('success', 'Pengguna berhasil diblokir.');
    }

    /**
     * Unban a user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unban(User $user)
    {
        // Unban user
        $user->banned_at = null;
        $user->ban_reason = null;
        $user->save();

        // Return back
        return redirect()->route('admin.users.edit', $user->id)
                        ->with('success', 'Blokir pengguna berhasil dibuka.');
    }

    /**
     * Toggle ban status for a user.
     *
     * @param  \App\Models\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleBan(User $user, Request $request)
    {
        // Prevent banning yourself
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat memblokir akun Anda sendiri.'
            ], 403);
        }

        try {
            // Toggle ban status
            if ($user->banned_at) {
                // If user is banned, unban them
                $user->banned_at = null;
                $user->ban_reason = null;
                $user->save();

                $message = 'Pengguna berhasil dibuka blokirnya.';
                $status = 'active';
            } else {
                // If user is not banned, ban them
                $banReason = $request->input('reason', 'Diblokir oleh administrator');
                $user->banned_at = now();
                $user->ban_reason = $banReason;
                $user->save();

                $message = 'Pengguna berhasil diblokir.';
                $status = 'banned';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle user's moderator role.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleRole(User $user)
    {
        // Prevent changing your own role
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.show', $user->id)
                            ->with('error', 'Anda tidak dapat mengubah peran Anda sendiri.');
        }

        // Toggle the role between user and moderator
        if ($user->isModerator()) {
            $user->role = 'user';
            $message = 'Peran moderator berhasil dicabut dari pengguna.';
        } else {
            $user->role = 'moderator';
            $message = 'Pengguna berhasil dijadikan moderator.';
        }

        $user->save();

        // Optionally send notification to the user
        // Notification::send($user, new RoleChangedNotification($user));

        return redirect()->route('admin.users.show', $user->id)
                        ->with('success', $message);
    }
}
