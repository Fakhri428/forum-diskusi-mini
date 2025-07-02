@extends('layouts.moderator')

@section('title', 'Profil Moderator')

@section('styles')
<style>
    .profile-header {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid white;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .profile-stats {
        background-color: white;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }

    .profile-stats h4 {
        color: #495057;
        font-size: 1.1rem;
        margin-bottom: 10px;
    }

    .profile-stats p {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 0;
    }

    .profile-stats .text-muted {
        font-size: 0.85rem;
    }

    .activity-item {
        padding: 15px;
        border-left: 3px solid #0d6efd;
        margin-bottom: 10px;
        background-color: white;
        border-radius: 5px;
    }

    .tab-content {
        padding: 20px 0;
    }

    .form-label {
        font-weight: 500;
    }

    .avatar-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
        border: 5px solid white;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .upload-btn-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
    }

    .upload-btn-wrapper input[type=file] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Profil Moderator</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Profile Section -->
    <div class="row">
        <!-- Left column - Profile info -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <!-- Avatar -->
                    @if(Auth::user()->avatar)
                        <img src="{{ asset(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="profile-avatar mb-3">
                    @else
                        <div class="profile-avatar d-flex align-items-center justify-content-center bg-primary text-white mx-auto mb-3">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif

                    <!-- User info -->
                    <h4 class="mb-0">{{ Auth::user()->name }}</h4>
                    <p class="text-muted mb-2">{{ Auth::user()->email }}</p>
                    <span class="badge bg-primary mb-3">Moderator</span>

                    @if(Auth::user()->bio)
                        <p class="mb-3">{{ Auth::user()->bio }}</p>
                    @endif

                    <div class="d-flex justify-content-center mb-3">
                        <div class="mx-2 text-center">
                            <h5 class="mb-0">{{ $stats['approved_threads'] ?? 0 }}</h5>
                            <small class="text-muted">Thread Disetujui</small>
                        </div>
                        <div class="mx-2 text-center">
                            <h5 class="mb-0">{{ $stats['moderated_comments'] ?? 0 }}</h5>
                            <small class="text-muted">Komentar Dimoderasi</small>
                        </div>
                        <div class="mx-2 text-center">
                            <h5 class="mb-0">{{ $stats['resolved_reports'] ?? 0 }}</h5>
                            <small class="text-muted">Laporan Diselesaikan</small>
                        </div>
                    </div>

                    <!-- Contact info -->
                    <div class="text-start">
                        @if(Auth::user()->phone)
                            <div class="mb-2">
                                <i class="fas fa-phone-alt me-2 text-primary"></i> {{ Auth::user()->phone }}
                            </div>
                        @endif

                        @if(Auth::user()->location)
                            <div class="mb-2">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i> {{ Auth::user()->location }}
                            </div>
                        @endif

                        <div class="mb-2">
                            <i class="fas fa-calendar-alt me-2 text-primary"></i> Bergabung {{ Auth::user()->created_at->format('M Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right column - Tabs -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs">
                        <li class="nav-item">
                            <a class="nav-link active" id="activity-tab" data-bs-toggle="tab" href="#activity">Aktivitas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="edit-profile-tab" data-bs-toggle="tab" href="#edit-profile">Edit Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="change-password-tab" data-bs-toggle="tab" href="#change-password">Ubah Password</a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        <!-- Activity Tab -->
                        <div class="tab-pane fade show active" id="activity">
                            <h5 class="mb-3">Riwayat Aktivitas</h5>

                            @if(count($activities) > 0)
                                @foreach($activities as $activity)
                                    <div class="activity-item">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="mb-0">
                                                @if(strpos($activity->action, 'approve') !== false)
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                @elseif(strpos($activity->action, 'reject') !== false)
                                                    <i class="fas fa-times-circle text-danger me-2"></i>
                                                @elseif(strpos($activity->action, 'delete') !== false)
                                                    <i class="fas fa-trash text-danger me-2"></i>
                                                @elseif(strpos($activity->action, 'edit') !== false)
                                                    <i class="fas fa-edit text-primary me-2"></i>
                                                @else
                                                    <i class="fas fa-cog text-secondary me-2"></i>
                                                @endif
                                                {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                            </h6>
                                            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-0 text-muted">{{ $activity->content }}</p>
                                    </div>
                                @endforeach

                                <div class="d-flex justify-content-center mt-3">
                                    {{ $activities->links() }}
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                    <h5>Tidak ada aktivitas</h5>
                                    <p class="text-muted">Aktivitas moderasi Anda akan muncul di sini</p>
                                </div>
                            @endif
                        </div>

                        <!-- Edit Profile Tab -->
                        <div class="tab-pane fade" id="edit-profile">
                            <h5 class="mb-3">Informasi Profil</h5>
                            <form action="{{ route('moderator.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row mb-4">
                                    <div class="col-md-12 text-center">
                                        <!-- Avatar Preview -->
                                        @if(Auth::user()->avatar)
                                            <img id="avatar-preview" src="{{ asset(Auth::user()->avatar) }}" alt="Avatar" class="avatar-preview">
                                        @else
                                            <div id="avatar-preview-placeholder" class="avatar-preview d-flex align-items-center justify-content-center bg-primary text-white">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                            </div>
                                            <img id="avatar-preview" src="" alt="Avatar" class="avatar-preview d-none">
                                        @endif

                                        <!-- Avatar Upload -->
                                        <div class="upload-btn-wrapper">
                                            <button class="btn btn-outline-primary" type="button">
                                                <i class="fas fa-camera me-2"></i>Ubah Foto
                                            </button>
                                            <input type="file" name="avatar" id="avatar" accept="image/*">
                                        </div>

                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" id="remove_avatar" name="remove_avatar" value="1">
                                            <label class="form-check-label" for="remove_avatar">
                                                Hapus foto profil
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Nama</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', Auth::user()->name) }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" value="{{ Auth::user()->email }}" readonly>
                                        <small class="text-muted">Email tidak dapat diubah.</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Nomor Telepon</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="location" class="form-label">Lokasi</label>
                                        <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', Auth::user()->location) }}">
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label for="bio" class="form-label">Bio</label>
                                        <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="4">{{ old('bio', Auth::user()->bio) }}</textarea>
                                        @error('bio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Change Password Tab -->
                        <div class="tab-pane fade" id="change-password">
                            <h5 class="mb-3">Ubah Password</h5>
                            <form action="{{ route('moderator.profile.password') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Password Saat Ini</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Password Baru</label>
                                    <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required>
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                    <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-key me-2"></i>Ubah Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Avatar preview functionality
        const avatarInput = document.getElementById('avatar');
        const avatarPreview = document.getElementById('avatar-preview');
        const avatarPreviewPlaceholder = document.getElementById('avatar-preview-placeholder');
        const removeAvatarCheckbox = document.getElementById('remove_avatar');

        avatarInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (avatarPreviewPlaceholder) {
                        avatarPreviewPlaceholder.classList.add('d-none');
                    }
                    avatarPreview.classList.remove('d-none');
                    avatarPreview.src = e.target.result;
                    removeAvatarCheckbox.checked = false;
                }
                reader.readAsDataURL(file);
            }
        });

        // Handle remove avatar checkbox
        removeAvatarCheckbox.addEventListener('change', function() {
            if (this.checked) {
                avatarInput.value = '';
                if (avatarPreviewPlaceholder) {
                    avatarPreviewPlaceholder.classList.remove('d-none');
                    avatarPreview.classList.add('d-none');
                } else {
                    avatarPreview.src = '';
                }
            }
        });

        // Remember active tab
        const hash = window.location.hash;
        if (hash) {
            const triggerEl = document.querySelector(`#profileTabs a[href="${hash}"]`);
            if (triggerEl) {
                new bootstrap.Tab(triggerEl).show();
            }
        }

        // Update URL hash when tab changes
        const tabEls = document.querySelectorAll('#profileTabs a');
        tabEls.forEach(tabEl => {
            tabEl.addEventListener('shown.bs.tab', function (event) {
                window.location.hash = event.target.getAttribute('href');
            });
        });
    });
</script>
@endsection
