<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/admin/users/show.blade.php -->
@extends('layouts.admin')

@section('title', 'Detail Pengguna')

@section('styles')
<style>
    .user-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #f8f9fc;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }

    .user-header {
        background-color: #4e73df;
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0.5rem;
    }

    .user-stats .card {
        transition: all 0.3s;
    }

    .user-stats .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .activity-item {
        padding: 1rem;
        border-left: 3px solid #e3e6f0;
        margin-bottom: 0.5rem;
        transition: all 0.2s;
    }

    .activity-item:hover {
        border-left-color: #4e73df;
        background-color: #f8f9fc;
    }

    .badge-role {
        font-size: 0.8rem;
        padding: 0.5em 1em;
        border-radius: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pengguna</h1>
        <div>
            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- User Header -->
    <div class="user-header card shadow mb-4">
        <div class="card-body text-center">
            <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=150&background=random' }}" class="user-avatar mb-3" alt="{{ $user->name }}">

            <h3 class="mb-1">{{ $user->name }}</h3>
            <p class="mb-2">{{ '@' . $user->username }}</p>

            <div class="mb-3">
                @if($user->isAdmin())
                    <span class="badge bg-danger badge-role">Admin</span>
                @elseif($user->isModerator())
                    <span class="badge bg-warning badge-role">Moderator</span>
                @else
                    <span class="badge bg-info badge-role">Pengguna</span>
                @endif

                @if($user->is_verified)
                    <span class="badge bg-success badge-role">Terverifikasi</span>
                @endif

                @if($user->is_banned)
                    <span class="badge bg-dark badge-role">Diblokir</span>
                @endif
            </div>

            <p>{{ $user->bio ?? 'Tidak ada bio' }}</p>
        </div>
    </div>

    <!-- User Stats -->
    <div class="row user-stats mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Thread</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->threads ? $user->threads->count() : 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Komentar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->comments ? $user->comments->count() : 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-reply fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Laporan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ optional($user)->reports ? $user->reports->count() : 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-flag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Bergabung</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-5 mb-4">
            <!-- User Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pengguna</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>ID</th>
                            <td>{{ $user->id }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Bergabung</th>
                            <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Login Terakhir</th>
                            <td>{{ $user->last_login_at ? $user->last_login_at->format('d M Y H:i') : 'Belum pernah' }}</td>
                        </tr>
                        <tr>
                            <th>IP Terakhir</th>
                            <td>{{ $user->last_login_ip ?? 'Tidak diketahui' }}</td>
                        </tr>
                        <tr>
                            <th>Status Verifikasi</th>
                            <td>
                                @if($user->is_verified)
                                    <span class="badge bg-success">Terverifikasi</span>
                                @else
                                    <span class="badge bg-warning">Belum Diverifikasi</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Status Akun</th>
                            <td>
                                @if($user->is_banned)
                                    <span class="badge bg-danger">Diblokir</span>
                                    <small class="d-block text-muted">
                                        Alasan: {{ $user->ban_reason ?? 'Tidak disebutkan' }}
                                    </small>
                                    <small class="d-block text-muted">
                                        Hingga: {{ $user->banned_until ? $user->banned_until->format('d M Y') : 'Permanen' }}
                                    </small>
                                @else
                                    <span class="badge bg-success">Aktif</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <form action="{{ route('admin.users.toggle-ban', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="button" class="btn btn-{{ $user->is_banned ? 'success' : 'danger' }} btn-sm btn-toggle-ban" data-id="{{ $user->id }}" data-banned="{{ $user->is_banned }}">
                                @if($user->is_banned)
                                    <i class="fas fa-user-check mr-1"></i> Cabut Blokir
                                @else
                                    <i class="fas fa-user-slash mr-1"></i> Blokir Pengguna
                                @endif
                            </button>
                        </form>

                        <form action="{{ route('admin.users.toggle-role', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-warning btn-sm">
                                @if($user->isModerator())
                                    <i class="fas fa-user-minus mr-1"></i> Cabut Moderator
                                @else
                                    <i class="fas fa-user-shield mr-1"></i> Jadikan Moderator
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Reported Content -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Konten Dilaporkan</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($reports as $report)
                            <div class="activity-item">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-{{ $report->status == 'pending' ? 'warning' : ($report->status == 'approved' ? 'danger' : 'secondary') }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                    <small>{{ $report->created_at->format('d M Y') }}</small>
                                </div>

                                <h6 class="mb-1">
                                    @if($report->reportable_type == 'App\Models\Thread')
                                        <i class="fas fa-comment-alt mr-1"></i> Thread:
                                    @else
                                        <i class="fas fa-reply mr-1"></i> Komentar:
                                    @endif

                                    <a href="{{ route('admin.reports.show', $report->id) }}">
                                        {{ Str::limit($report->reportable->title ?? $report->reportable->body, 50) }}
                                    </a>
                                </h6>

                                <p class="mb-1 small">
                                    <strong>Alasan:</strong> {{ $report->reason }}
                                </p>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Tidak ada konten yang dilaporkan.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <!-- Recent Threads -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Thread Terbaru</h6>
                    <a href="{{ route('admin.threads.index', ['user_id' => $user->id]) }}" class="btn btn-sm btn-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($user->threads()->latest()->take(5)->get() as $thread)
                        <a href="{{ route('admin.threads.show', $thread->id) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $thread->title }}</h6>
                                <small>{{ $thread->created_at->format('d M Y') }}</small>
                            </div>
                            <div class="mb-1">{{ Str::limit(strip_tags($thread->body), 150) }}</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge" style="background-color: {{ $thread->category->color ?? '#6c757d' }}">
                                    {{ $thread->category->name }}
                                </span>
                                <div>
                                    <span class="badge bg-secondary">{{ $thread->comments->count() }} komentar</span>
                                    @if(!$thread->is_approved)
                                        <span class="badge bg-warning">Belum Disetujui</span>
                                    @endif
                                    @if($thread->is_pinned)
                                        <span class="badge bg-info">Disematkan</span>
                                    @endif
                                    @if($thread->is_locked)
                                        <span class="badge bg-danger">Dikunci</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="list-group-item">
                            <p class="mb-0 text-muted">Pengguna ini belum membuat thread.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Comments -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Komentar Terbaru</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($user->comments()->latest()->take(5)->get() as $comment)
                        <a href="{{ route('admin.threads.show', $comment->thread_id) }}#comment-{{ $comment->id }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">Pada thread: {{ $comment->thread->title }}</h6>
                                <small>{{ $comment->created_at->format('d M Y') }}</small>
                            </div>
                            <p class="mb-1">{{ Str::limit($comment->body, 150) }}</p>
                        </a>
                        @empty
                        <div class="list-group-item">
                            <p class="mb-0 text-muted">Pengguna ini belum membuat komentar.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Log Aktivitas</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse($activities as $activity)
                            <div class="activity-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <i class="fas {{ getActivityIcon($activity->type) }} mr-1"></i>
                                        {{ getActivityDescription($activity) }}
                                    </div>
                                    <small title="{{ $activity->created_at }}">{{ $activity->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">Tidak ada aktivitas tercatat.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ban User Modal -->
<div class="modal fade" id="banModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Blokir Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.ban', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ban_reason" class="form-label">Alasan Blokir</label>
                        <textarea class="form-control" id="ban_reason" name="ban_reason" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="banned_until" class="form-label">Durasi Blokir</label>
                        <select class="form-select" id="banned_until" name="banned_until">
                            <option value="1">1 hari</option>
                            <option value="3">3 hari</option>
                            <option value="7">7 hari</option>
                            <option value="30">30 hari</option>
                            <option value="permanent">Permanen</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="notify_user" name="notify_user" value="1" checked>
                        <label class="form-check-label" for="notify_user">
                            Kirim notifikasi ke pengguna
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Blokir Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus akun pengguna <strong>{{ $user->name }}</strong>?</p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <strong>Perhatian!</strong> Seluruh data pengguna termasuk thread, komentar, dan aktivitas lainnya akan dihapus. Tindakan ini tidak dapat dibatalkan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Akun</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ban button logic
        const banBtn = document.querySelector('.btn-danger[data-action="ban"]');
        if (banBtn) {
            banBtn.addEventListener('click', function() {
                const banModal = new bootstrap.Modal(document.getElementById('banModal'));
                banModal.show();
            });
        }

        $('.btn-toggle-ban').click(function() {
            const userId = $(this).data('id');
            const isBanned = $(this).data('banned');
            const reason = isBanned ? '' : prompt('Masukkan alasan pemblokiran:');

            // If not banned and no reason provided, cancel the operation
            if (!isBanned && !reason) {
                return;
            }

            $.ajax({
                url: `/admin/users/${userId}/toggle-ban`,
                type: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    reason: reason
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        // Refresh the page or update UI
                        location.reload();
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('Terjadi kesalahan saat memproses permintaan.');
                }
            });
        });
    });
</script>
@endsection

@php
function getActivityIcon($type) {
    $icons = [
        'login' => 'fa-sign-in-alt',
        'logout' => 'fa-sign-out-alt',
        'create_thread' => 'fa-comment-alt',
        'update_thread' => 'fa-edit',
        'delete_thread' => 'fa-trash',
        'create_comment' => 'fa-reply',
        'update_comment' => 'fa-edit',
        'delete_comment' => 'fa-trash',
        'create_report' => 'fa-flag',
        'profile_update' => 'fa-user-edit',
        'default' => 'fa-history'
    ];

    return $icons[$type] ?? $icons['default'];
}

function getActivityDescription($activity) {
    switch ($activity->type) {
        case 'login':
            return 'Login ke sistem';
        case 'logout':
            return 'Logout dari sistem';
        case 'create_thread':
            return 'Membuat thread baru';
        case 'update_thread':
            return 'Mengubah thread';
        case 'delete_thread':
            return 'Menghapus thread';
        case 'create_comment':
            return 'Menambahkan komentar';
        case 'update_comment':
            return 'Mengubah komentar';
        case 'delete_comment':
            return 'Menghapus komentar';
        case 'create_report':
            return 'Melaporkan konten';
        case 'profile_update':
            return 'Mengubah profil';
        default:
            return 'Melakukan aktivitas';
    }
}
@endphp
