@extends('layouts.moderator')

@section('title', 'Kelola Komentar')

@section('styles')
<style>
    .comment-body {
        max-height: 80px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }
    .table td {
        vertical-align: middle;
    }
    .badge-pill {
        border-radius: 20px;
        padding: 0.35em 0.65em;
    }
    .filter-card {
        border-radius: 10px;
    }
    .action-column {
        width: 160px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola Komentar</h1>
    </div>

    <!-- Filter and Search -->
    <div class="card shadow-sm filter-card mb-4">
        <div class="card-body">
            <form action="{{ route('moderator.comments.index') }}" method="GET" class="row g-3">
                <div class="col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cari isi komentar..." name="search" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-3">
                    <select class="form-select" name="thread_id">
                        <option value="">Semua Thread</option>
                        @foreach($threads ?? [] as $thread)
                            <option value="{{ $thread->id }}" {{ request('thread_id') == $thread->id ? 'selected' : '' }}>
                                {{ Str::limit($thread->title ?? '', 30) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <select class="form-select" name="is_flagged">
                        <option value="">Status Flag</option>
                        <option value="1" {{ request('is_flagged') === '1' ? 'selected' : '' }}>Ditandai</option>
                        <option value="0" {{ request('is_flagged') === '0' ? 'selected' : '' }}>Tidak Ditandai</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Batch Action Form -->
    <form id="batchForm" action="{{ route('moderator.comments.batch-delete') }}" method="POST">
        @csrf
        <input type="hidden" name="action" id="batchAction" value="delete">

        <!-- Data Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Komentar</h5>
                    <button type="button" class="btn btn-danger" id="batchDeleteBtn" disabled>
                        <i class="fas fa-trash me-1"></i> Hapus Komentar Terpilih
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th>Komentar</th>
                                <th>Penulis</th>
                                <th>Thread</th>
                                <th>Tanggal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($comments as $comment)
                            <tr>
                                <td class="px-4">
                                    <div class="form-check">
                                        <input class="form-check-input comment-checkbox" type="checkbox" name="comment_ids[]" value="{{ $comment->id }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="comment-body">{{ $comment->body }}</div>
                                    <div class="mt-1">
                                        @if($comment->is_flagged)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-flag me-1"></i>Ditandai
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2 bg-secondary text-white rounded-circle text-center d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">
                                            {{ strtoupper(substr($comment->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>{{ $comment->user->name ?? 'User Tidak Ditemukan' }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($comment->thread)
                                        <a href="{{ route('threads.show', $comment->thread_id) }}" class="text-decoration-none">
                                            {{ Str::limit($comment->thread->title, 30) }}
                                        </a>
                                    @else
                                        <span class="text-muted">Thread tidak ditemukan</span>
                                    @endif
                                </td>
                                <td>{{ $comment->created_at->format('d M Y H:i') }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('threads.show', $comment->thread_id) }}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Lihat di Thread">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('moderator.comments.edit', $comment) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit Komentar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteComment{{ $comment->id }}" title="Hapus Komentar">
                                            <i class="fas fa-trash"></i>
                                        </button>
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
                                                    <div class="mb-3">
                                                        <label for="deleteReason{{ $comment->id }}" class="form-label">Alasan penghapusan (opsional)</label>
                                                        <textarea class="form-control" id="deleteReason{{ $comment->id }}" rows="2" placeholder="Komentar melanggar ketentuan..."></textarea>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="notifyUser{{ $comment->id }}" checked>
                                                        <label class="form-check-label" for="notifyUser{{ $comment->id }}">
                                                            Kirim notifikasi ke penulis komentar
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <form action="{{ route('moderator.comments.destroy', $comment->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="reason" id="reasonInput{{ $comment->id }}">
                                                        <input type="hidden" name="notify_user" id="notifyInput{{ $comment->id }}" value="1">
                                                        <button type="submit" class="btn btn-danger delete-comment" data-id="{{ $comment->id }}">Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-comment fa-3x text-muted mb-3"></i>
                                        <h5>Tidak ada komentar yang ditemukan</h5>
                                        <p class="text-muted">Komentar yang sesuai dengan filter akan muncul di sini</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        Menampilkan {{ $comments->firstItem() ?? 0 }} - {{ $comments->lastItem() ?? 0 }} dari {{ $comments->total() }} komentar
                    </div>
                    <div>
                        {{ $comments->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Batch Delete Modal -->
<div class="modal fade" id="batchDeleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Komentar Terpilih</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus semua komentar yang dipilih?</p>
                <div class="mb-3">
                    <label for="batchDeleteReason" class="form-label">Alasan penghapusan (opsional)</label>
                    <textarea class="form-control" id="batchDeleteReason" name="reason" rows="3" placeholder="Berikan alasan untuk penghapusan ini..."></textarea>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="batchNotifyUsers" name="notify_users" checked>
                    <label class="form-check-label" for="batchNotifyUsers">
                        Kirim notifikasi kepada semua penulis komentar
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="submitBatchDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Select All checkbox
        const selectAllCheckbox = document.getElementById('selectAll');
        const commentCheckboxes = document.querySelectorAll('.comment-checkbox');
        const batchDeleteBtn = document.getElementById('batchDeleteBtn');
        const batchForm = document.getElementById('batchForm');
        const batchDeleteModal = new bootstrap.Modal(document.getElementById('batchDeleteModal'));

        // Update batch delete button state
        function updateBatchDeleteButtonState() {
            const checkedBoxes = document.querySelectorAll('.comment-checkbox:checked').length;
            batchDeleteBtn.disabled = checkedBoxes === 0;
        }

        // Handle select all change
        selectAllCheckbox.addEventListener('change', function() {
            commentCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateBatchDeleteButtonState();
        });

        // Handle individual checkbox changes
        commentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBatchDeleteButtonState);
        });

        // Show batch delete modal
        batchDeleteBtn.addEventListener('click', function() {
            batchDeleteModal.show();
        });

        // Handle batch delete submission
        document.getElementById('submitBatchDelete').addEventListener('click', function() {
            const reason = document.getElementById('batchDeleteReason').value;
            const notifyUsers = document.getElementById('batchNotifyUsers').checked;

            // Add hidden fields to the form
            let reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'reason';
            reasonInput.value = reason;
            batchForm.appendChild(reasonInput);

            let notifyInput = document.createElement('input');
            notifyInput.type = 'hidden';
            notifyInput.name = 'notify_users';
            notifyInput.value = notifyUsers ? '1' : '0';
            batchForm.appendChild(notifyInput);

            // Submit the form
            batchForm.submit();
        });

        // Handle single comment delete
        document.querySelectorAll('.delete-comment').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.getAttribute('data-id');
                const reasonValue = document.getElementById('deleteReason' + commentId).value;
                const notifyValue = document.getElementById('notifyUser' + commentId).checked ? '1' : '0';

                document.getElementById('reasonInput' + commentId).value = reasonValue;
                document.getElementById('notifyInput' + commentId).value = notifyValue;
            });
        });
    });
</script>
@endsection
