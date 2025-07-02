<!-- filepath: resources/views/moderator/dashboard.blade.php -->
@extends('layouts.moderator')

@section('title', 'Moderator Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex">
                    <div class="rounded-circle bg-success bg-gradient d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-comments fa-lg text-white"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-1">{{ $threadCount }}</h3>
                        <p class="text-muted mb-0">Total Thread</p>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('moderator.threads.index') }}" class="btn btn-sm btn-outline-success w-100">
                        <i class="fas fa-list me-1"></i> Kelola Thread
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex">
                    <div class="rounded-circle bg-info bg-gradient d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-reply fa-lg text-white"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-1">{{ $commentCount }}</h3>
                        <p class="text-muted mb-0">Total Komentar</p>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('moderator.comments.index') }}" class="btn btn-sm btn-outline-info w-100">
                        <i class="fas fa-comment-dots me-1"></i> Kelola Komentar
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body d-flex">
                    <div class="rounded-circle bg-warning bg-gradient d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-flag fa-lg text-white"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-1">{{ $reportCount }}</h3>
                        <p class="text-muted mb-0">Laporan Thread</p>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('moderator.reports.index') }}" class="btn btn-sm btn-outline-warning w-100">
                        <i class="fas fa-exclamation-triangle me-1"></i> Lihat Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>Thread yang Perlu Dimoderasi</h5>
                        <a href="{{ route('moderator.threads.index') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-comments me-1"></i> Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Penulis</th>
                                    <th>Kategori</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingThreads as $thread)
                                <tr>
                                    <td>{{ Str::limit($thread->title, 25) }}</td>
                                    <td>{{ $thread->user->name }}</td>
                                    <td>
                                        @if($thread->category)
                                            <span class="badge bg-info">
                                                {{ $thread->category->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Tanpa Kategori</span>
                                        @endif
                                    </td>
                                    <td>{{ $thread->created_at->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge {{ $thread->is_approved ? 'bg-success' : 'bg-warning' }}">
                                            {{ $thread->is_approved ? 'Disetujui' : 'Perlu Review' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('threads.show', $thread->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-success" data-action="approve" data-id="{{ $thread->id }}">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" data-action="reject" data-id="{{ $thread->id }}">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <a href="{{ route('moderator.threads.edit', $thread->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-comment-dots me-2"></i>Komentar Terbaru</h5>
                        <a href="{{ route('moderator.comments.index') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-comments me-1"></i> Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Komentar</th>
                                    <th>Penulis</th>
                                    <th>Thread</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentComments as $comment)
                                <tr>
                                    <td>{{ Str::limit($comment->body, 30) }}</td>
                                    <td>{{ $comment->user->name }}</td>
                                    <td>
                                        <a href="{{ route('threads.show', $comment->thread_id) }}">
                                            {{ Str::limit($comment->thread->title ?? 'Thread tidak ditemukan', 20) }}
                                        </a>
                                    </td>
                                    <td>{{ $comment->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('threads.show', $comment->thread_id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteComment{{ $comment->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <a href="{{ route('moderator.comments.edit', $comment->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>

                                        <!-- Delete Comment Modal -->
                                        <div class="modal fade" id="deleteComment{{ $comment->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Hapus Komentar</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Apakah Anda yakin ingin menghapus komentar ini?</p>
                                                        <div class="alert alert-secondary">
                                                            {{ $comment->body }}
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <form action="{{ route('moderator.comments.destroy', $comment->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Aktivitas Moderasi</h5>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary">Minggu</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary active">Bulan</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="moderationChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Moderation Modal -->
<div class="modal fade" id="moderationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moderationModalTitle">Moderasi Konten</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="moderationForm">
                    <input type="hidden" id="contentId" name="id">
                    <input type="hidden" id="contentType" name="type">
                    <input type="hidden" id="action" name="action">

                    <div class="mb-3">
                        <label for="moderationReason" class="form-label">Alasan (opsional)</label>
                        <textarea class="form-control" id="moderationReason" name="reason" rows="3"
                                  placeholder="Berikan alasan untuk tindakan moderasi ini..."></textarea>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="notifyUser" name="notify" checked>
                        <label class="form-check-label" for="notifyUser">Kirim notifikasi ke pengguna</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submitModeration">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Moderation Activity Chart
        const moderationCtx = document.getElementById('moderationChart').getContext('2d');
        new Chart(moderationCtx, {
            type: 'bar',
            data: {
                labels: ['1 Jun', '5 Jun', '10 Jun', '15 Jun', '20 Jun', '25 Jun', '30 Jun'],
                datasets: [{
                    label: 'Thread Disetujui',
                    data: [5, 8, 6, 10, 7, 9, 11],
                    backgroundColor: 'rgba(40, 167, 69, 0.7)'
                }, {
                    label: 'Thread Ditolak',
                    data: [2, 1, 3, 1, 2, 1, 0],
                    backgroundColor: 'rgba(220, 53, 69, 0.7)'
                }, {
                    label: 'Komentar Dimoderasi',
                    data: [8, 10, 7, 12, 9, 11, 15],
                    backgroundColor: 'rgba(0, 123, 255, 0.7)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Setup modal for moderation actions
        const moderationModal = new bootstrap.Modal(document.getElementById('moderationModal'));

        // Handle approve/reject buttons
        document.querySelectorAll('[data-action]').forEach(button => {
            button.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                const id = this.getAttribute('data-id');
                const type = this.closest('table').querySelector('th').textContent.includes('Komentar') ? 'comment' : 'thread';

                document.getElementById('moderationModalTitle').textContent =
                    `${action === 'approve' ? 'Setujui' : 'Tolak'} ${type === 'comment' ? 'Komentar' : 'Thread'}`;
                document.getElementById('contentId').value = id;
                document.getElementById('contentType').value = type;
                document.getElementById('action').value = action;

                moderationModal.show();
            });
        });

        // Handle moderation form submission
        document.getElementById('submitModeration').addEventListener('click', function() {
            const formData = {
                id: document.getElementById('contentId').value,
                type: document.getElementById('contentType').value,
                action: document.getElementById('action').value,
                reason: document.getElementById('moderationReason').value,
                notify: document.getElementById('notifyUser').checked
            };

            // Send AJAX request to handle moderation
            fetch('/moderator/moderate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    moderationModal.hide();
                    // Show success message and reload page
                    alert('Konten berhasil dimoderasi');
                    location.reload();
                } else {
                    alert('Terjadi kesalahan: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses permintaan');
            });
        });
    });
</script>
@endsection
