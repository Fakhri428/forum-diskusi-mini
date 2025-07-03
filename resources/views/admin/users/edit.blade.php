<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/admin/users/edit.blade.php -->
@extends('layouts.admin')

@section('title', 'Edit Pengguna')

@section('styles')
<style>
    .avatar-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 20px;
        border: 3px solid #e3e6f0;
    }

    .avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .form-section {
        background-color: #f8f9fc;
        border-radius: 0.35rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border-left: 5px solid #4e73df;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Pengguna</h1>
        <div>
            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info">
                <i class="fas fa-eye mr-1"></i> Detail
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">Edit Pengguna: {{ $user->name }}</h6>
            @if($user->banned_at)
                <span class="badge bg-danger">Akun Diblokir</span>
            @endif
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Avatar Section -->
                <div class="row justify-content-center mb-4">
                    <div class="col-md-6 text-center">
                        <div class="avatar-preview">
                            <img id="avatarPreview" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}" alt="Avatar Preview">
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label">Avatar</label>
                            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Gambar harus berformat JPG, PNG atau GIF dengan ukuran maksimal 2MB. Biarkan kosong untuk tidak mengubah.
                            </small>
                        </div>

                        @if($user->avatar)
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="remove_avatar" name="remove_avatar" value="1">
                                <label class="form-check-label" for="remove_avatar">
                                    Hapus avatar saat ini
                                </label>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="form-section">
                    <h5 class="mb-3">Informasi Dasar</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @unless($user->email_verified_at)
                                <div class="mt-2">
                                    <span class="badge bg-warning">Belum Terverifikasi</span>
                                    <a href="{{ route('admin.users.verify-email', $user->id) }}" class="btn btn-sm btn-outline-primary ms-2">
                                        Verifikasi Sekarang
                                    </a>
                                </div>
                            @endunless
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Peran <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required {{ $user->id == auth()->id() ? 'disabled' : '' }}>
                                {{-- FIX: Use 'member' instead of 'user' to match database --}}
                                <option value="member" {{ (old('role', $user->role) == 'member' || !$user->role) ? 'selected' : '' }}>Pengguna</option>
                                <option value="moderator" {{ old('role', $user->role) == 'moderator' ? 'selected' : '' }}>Moderator</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @if($user->id == auth()->id())
                                <input type="hidden" name="role" value="{{ $user->role }}">
                                <small class="form-text text-warning">
                                    Anda tidak dapat mengubah peran akun Anda sendiri.
                                </small>
                            @endif
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Password Section -->
                <div class="form-section">
                    <h5 class="mb-3">Ubah Password</h5>
                    <p class="text-muted mb-3">Biarkan kosong untuk tidak mengubah password.</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Password minimal 8 karakter dan harus mengandung huruf dan angka.
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="send_password_notification" name="send_password_notification" value="1" {{ old('send_password_notification') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="send_password_notification">
                            Kirim notifikasi password baru ke email pengguna
                        </label>
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="form-section">
                    <h5 class="mb-3">Informasi Profil</h5>
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="3">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Lokasi</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $user->location) }}">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website', $user->website) }}" placeholder="https://">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Account Settings -->
                @if($user->id != auth()->id())
                    <div class="form-section bg-light border-left-warning">
                        <h5 class="mb-3">Tindakan Akun</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                @if($user->banned_at)
                                    <a href="{{ route('admin.users.unban', $user->id) }}" class="btn btn-success" onclick="return confirm('Apakah Anda yakin ingin membuka blokir pengguna ini?')">
                                        <i class="fas fa-user-check mr-1"></i> Buka Blokir Pengguna
                                    </a>
                                    <small class="form-text text-muted d-block mt-2">
                                        Diblokir pada: {{ \Carbon\Carbon::parse($user->banned_at)->format('d M Y H:i') }}
                                        @if($user->ban_reason)
                                            <br>Alasan: {{ $user->ban_reason }}
                                        @endif
                                    </small>
                                @else
                                    <a href="{{ route('admin.users.ban', $user->id) }}" class="btn btn-warning" onclick="return confirm('Apakah Anda yakin ingin memblokir pengguna ini?')">
                                        <i class="fas fa-ban mr-1"></i> Blokir Pengguna
                                    </a>
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-user-times mr-1"></i> Hapus Pengguna
                                </button>
                                <small class="form-text text-danger d-block mt-2">
                                    Menghapus pengguna akan menghapus semua konten yang dimilikinya. Tindakan ini tidak dapat dibatalkan.
                                </small>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="d-flex justify-content-between">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
@if($user->id != auth()->id())
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus pengguna <strong>{{ $user->name }}</strong>?</p>
                    <p class="text-danger">
                        <strong>Perhatian:</strong> Tindakan ini akan menghapus semua thread, komentar, dan data lainnya yang dibuat oleh pengguna ini.
                        <br>
                        <strong>Tindakan ini tidak dapat dibatalkan!</strong>
                    </p>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirm_delete" required>
                        <label class="form-check-label" for="confirm_delete">
                            Saya mengerti bahwa tindakan ini tidak dapat dibatalkan.
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                            Hapus Pengguna
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Avatar preview
        const avatarInput = document.getElementById('avatar');
        const avatarPreview = document.getElementById('avatarPreview');
        const removeAvatarCheck = document.getElementById('remove_avatar');

        avatarInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;

                    // If user uploads a new avatar, uncheck the remove avatar box
                    if (removeAvatarCheck) {
                        removeAvatarCheck.checked = false;
                    }
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        if (removeAvatarCheck) {
            removeAvatarCheck.addEventListener('change', function() {
                if (this.checked) {
                    avatarPreview.src = 'https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random';
                    avatarInput.value = ''; // Clear the file input
                } else {
                    avatarPreview.src = "{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}";
                }
            });
        }

        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Toggle icon
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });

        // Enable delete button only when checkbox is checked
        const confirmDeleteCheck = document.getElementById('confirm_delete');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        if (confirmDeleteCheck && confirmDeleteBtn) {
            confirmDeleteCheck.addEventListener('change', function() {
                confirmDeleteBtn.disabled = !this.checked;
            });
        }
    });
</script>
@endsection
