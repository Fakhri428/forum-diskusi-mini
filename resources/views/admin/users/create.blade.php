<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/admin/users/create.blade.php -->
@extends('layouts.admin')

@section('title', 'Tambah Pengguna')

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
        <h1 class="h3 mb-0 text-gray-800">Tambah Pengguna</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Form Tambah Pengguna</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Avatar Section -->
                <div class="row justify-content-center mb-4">
                    <div class="col-md-6 text-center">
                        <div class="avatar-preview">
                            <img id="avatarPreview" src="https://ui-avatars.com/api/?name=New+User&background=random" alt="Avatar Preview">
                        </div>

                        <div class="mb-3">
                            <label for="avatar" class="form-label">Avatar</label>
                            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*">
                            @error('avatar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Gambar harus berformat JPG, PNG atau GIF dengan ukuran maksimal 2MB.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="form-section">
                    <h5 class="mb-3">Informasi Dasar</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}" required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">
                                Username harus unik, min. 3 karakter, hanya boleh mengandung huruf, angka, dan underscore.
                            </small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
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
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Peran <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Pengguna</option>
                                <option value="moderator" {{ old('role') == 'moderator' ? 'selected' : '' }}>Moderator</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <strong>Admin:</strong> Akses penuh ke semua fitur. <strong>Moderator:</strong> Akses ke moderasi konten.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="form-section">
                    <h5 class="mb-3">Informasi Profil</h5>
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="3">{{ old('bio') }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Biografi singkat tentang pengguna (maks. 500 karakter).
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Lokasi</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website') }}" placeholder="https://">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="form-section">
                    <h5 class="mb-3">Pengaturan Akun</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="email_verified" name="email_verified" value="1" {{ old('email_verified') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_verified">Email Terverifikasi</label>
                                <small class="form-text text-muted d-block">
                                    Jika dicentang, pengguna tidak perlu melakukan verifikasi email.
                                </small>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="send_welcome_email" name="send_welcome_email" value="1" {{ old('send_welcome_email', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="send_welcome_email">Kirim Email Selamat Datang</label>
                                <small class="form-text text-muted d-block">
                                    Kirim email selamat datang dengan informasi login ke pengguna.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus mr-1"></i> Tambah Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Avatar preview
        const avatarInput = document.getElementById('avatar');
        const avatarPreview = document.getElementById('avatarPreview');

        avatarInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

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

        // Auto generate username from name
        const nameInput = document.getElementById('name');
        const usernameInput = document.getElementById('username');

        nameInput.addEventListener('input', function() {
            // Only suggest username if the field is empty
            if (!usernameInput.value) {
                const suggestedUsername = this.value
                    .toLowerCase()
                    .replace(/[^a-z0-9]/g, '_')
                    .replace(/_+/g, '_')
                    .replace(/^_|_$/g, '');

                usernameInput.value = suggestedUsername;
            }
        });
    });
</script>
@endsection
