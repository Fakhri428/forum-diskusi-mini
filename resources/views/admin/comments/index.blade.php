{{-- filepath: resources/views/admin/comments/index.blade.php --}}

@extends('layouts.admin')

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
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola Komentar</h1>

        {{-- PERBAIKAN: Dropdown untuk pilihan navigasi --}}
        <div class="dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-plus-circle me-1"></i> Tambah Komentar
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('threads.index') }}">
                        <i class="fas fa-comment me-1"></i> Lihat Threads (Dashboard)
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('threads.index') }}" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i> Buka Tab Baru
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.threads.index') }}">
                        <i class="fas fa-cogs me-1"></i> Kelola Threads
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Filter & Pencarian</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.comments.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari komentar..." name="search" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="thread_id" class="form-select">
                        <option value="">Semua Thread</option>
                        @foreach($threads as $thread)
                            <option value="{{ $thread->id }}" {{ request('thread_id') == $thread->id ? 'selected' : '' }}>
                                {{ Str::limit($thread->title, 50) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="is_approved" class="form-select">
                        <option value="">Status Persetujuan</option>
                        <option value="1" {{ request('is_approved') == '1' ? 'selected' : '' }}>Disetujui</option>
                        <option value="0" {{ request('is_approved') == '0' ? 'selected' : '' }}>Belum Disetujui</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="has_reports" class="form-select">
                        <option value="">Laporan</option>
                        <option value="1" {{ request('has_reports') == '1' ? 'selected' : '' }}>Dilaporkan</option>
                        <option value="0" {{ request('has_reports') == '0' ? 'selected' : '' }}>Tidak Dilaporkan</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <div class="d-grid">
                        <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card shadow mb-4">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                        <label class="form-check-label" for="selectAll">Pilih Semua</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        <div class="btn-group" id="bulkActions" style="display: none;">
                            <button class="btn btn-success btn-sm bulk-action" data-action="approve">
                                <i class="fas fa-check-circle me-1"></i> Setujui
                            </button>
                            <button class="btn btn-warning btn-sm bulk-action" data-action="unapprove">
                                <i class="fas fa-times-circle me-1"></i> Batalkan Persetujuan
                            </button>
                            <button class="btn btn-danger btn-sm bulk-action" data-action="delete">
                                <i class="fas fa-trash-alt me-1"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comment List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Daftar Komentar ({{ $comments->total() }})</h6>
        </div>
        <div class="card-body">
            @if($comments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="40px">#</th>
                                <th>Komentar</th>
                                <th>Thread</th>
                                <th>Penulis</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th width="200px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comments as $comment)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input comment-checkbox" type="checkbox" value="{{ $comment->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="comment-body">
                                            {{ $comment->body }}
                                        </div>
                                        {{-- Preview tooltip --}}
                                        @if(strlen($comment->body) > 100)
                                            <small class="text-muted d-block mt-1">
                                                <i class="fas fa-info-circle me-1"></i>Klik untuk lihat lengkap
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- PERBAIKAN: Dropdown untuk pilihan view thread --}}
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <a href="{{ route('threads.show', $comment->thread) }}" class="text-decoration-none">
                                                    {{ Str::limit($comment->thread->title, 30) }}
                                                </a>
                                            </div>
                                            <div class="dropdown ms-2">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                        type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('threads.show', $comment->thread) }}">
                                                            <i class="fas fa-eye me-1"></i> Lihat di Dashboard
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('threads.show', $comment->thread) }}" target="_blank">
                                                            <i class="fas fa-external-link-alt me-1"></i> Buka Tab Baru
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('threads.show', $comment->thread) }}#comment-{{ $comment->id }}">
                                                            <i class="fas fa-anchor me-1"></i> Langsung ke Komentar
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $comment->user->id) }}" class="text-decoration-none">
                                            {{ $comment->user->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <small>{{ $comment->created_at->format('d M Y H:i') }}</small>
                                        <br>
                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        @if($comment->is_approved)
                                            <span class="badge bg-success">Disetujui</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif

                                        @if($comment->reports_count > 0)
                                            <span class="badge bg-danger" title="{{ $comment->reports_count }} laporan">
                                                <i class="fas fa-flag"></i> {{ $comment->reports_count }}
                                            </span>
                                        @endif

                                        {{-- Reply indicator --}}
                                        @if($comment->parent_id)
                                            <span class="badge bg-info" title="Balasan">
                                                <i class="fas fa-reply"></i>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            {{-- Quick View Button --}}
                                            <button type="button" class="btn btn-info quick-view-btn"
                                                    data-bs-toggle="modal" data-bs-target="#commentModal"
                                                    data-comment-id="{{ $comment->id }}"
                                                    data-comment-body="{{ $comment->body }}"
                                                    data-comment-user="{{ $comment->user->name }}"
                                                    data-comment-date="{{ $comment->created_at->format('d M Y H:i') }}"
                                                    data-thread-title="{{ $comment->thread->title }}"
                                                    title="Quick View">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            {{-- PERBAIKAN: Edit tanpa target="_blank" sebagai default --}}
                                            <a href="{{ route('comments.edit', $comment) }}"
                                               class="btn btn-primary" title="Edit Comment">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            {{-- Quick Actions dengan Dropdown --}}
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-secondary dropdown-toggle"
                                                        data-bs-toggle="dropdown" title="Actions">
                                                    <i class="fas fa-cog"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    {{-- View Options --}}
                                                    <li>
                                                        <h6 class="dropdown-header">Lihat Komentar</h6>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('threads.show', $comment->thread) }}#comment-{{ $comment->id }}"
                                                           class="dropdown-item">
                                                            <i class="fas fa-anchor text-info"></i> Lihat di Thread
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('threads.show', $comment->thread) }}#comment-{{ $comment->id }}"
                                                           class="dropdown-item" target="_blank">
                                                            <i class="fas fa-external-link-alt text-primary"></i> Buka Tab Baru
                                                        </a>
                                                    </li>

                                                    {{-- Edit Options --}}
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <h6 class="dropdown-header">Edit Komentar</h6>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('comments.edit', $comment) }}"
                                                           class="dropdown-item">
                                                            <i class="fas fa-edit text-warning"></i> Edit di Dashboard
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('comments.edit', $comment) }}"
                                                           class="dropdown-item" target="_blank">
                                                            <i class="fas fa-external-link-alt text-warning"></i> Edit Tab Baru
                                                        </a>
                                                    </li>

                                                    {{-- Admin Actions --}}
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <h6 class="dropdown-header">Admin Actions</h6>
                                                    </li>

                                                    {{-- Toggle Approval --}}
                                                    <li>
                                                        <button type="button" class="dropdown-item btn-approval"
                                                                data-id="{{ $comment->id }}"
                                                                data-status="{{ $comment->is_approved ? 1 : 0 }}">
                                                            @if($comment->is_approved)
                                                                <i class="fas fa-times-circle text-warning"></i> Batalkan Persetujuan
                                                            @else
                                                                <i class="fas fa-check-circle text-success"></i> Setujui
                                                            @endif
                                                        </button>
                                                    </li>

                                                    {{-- View User Profile --}}
                                                    <li>
                                                        <a href="{{ route('admin.users.show', $comment->user->id) }}"
                                                           class="dropdown-item">
                                                            <i class="fas fa-user text-primary"></i> Lihat Profil User
                                                        </a>
                                                    </li>

                                                    {{-- View Thread in Admin --}}
                                                    <li>
                                                        <a href="{{ route('admin.threads.show', $comment->thread->id) }}"
                                                           class="dropdown-item">
                                                            <i class="fas fa-cogs text-info"></i> Kelola Thread
                                                        </a>
                                                    </li>

                                                    <li><hr class="dropdown-divider"></li>

                                                    {{-- Delete --}}
                                                    <li>
                                                        <button type="button" class="dropdown-item text-danger btn-delete"
                                                                data-id="{{ $comment->id }}">
                                                            <i class="fas fa-trash"></i> Hapus Comment
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $comments->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-comments fa-4x text-muted"></i>
                    </div>
                    <h5>Tidak ada komentar yang ditemukan</h5>
                    <p class="text-muted">Komentar akan muncul di sini ketika user menambahkan komentar ke threads.</p>

                    {{-- PERBAIKAN: Dropdown untuk opsi navigasi --}}
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-external-link-alt me-1"></i> Lihat Threads
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('threads.index') }}">
                                    <i class="fas fa-list me-1"></i> Lihat di Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('threads.index') }}" target="_blank">
                                    <i class="fas fa-external-link-alt me-1"></i> Buka Tab Baru
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.threads.index') }}">
                                    <i class="fas fa-cogs me-1"></i> Kelola Threads
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Quick View Comment Modal -->
<div class="modal fade" id="commentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Komentar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6>Thread:</h6>
                    <p id="modal-thread-title" class="text-muted"></p>
                </div>
                <div class="mb-3">
                    <h6>Penulis:</h6>
                    <p id="modal-comment-user"></p>
                </div>
                <div class="mb-3">
                    <h6>Tanggal:</h6>
                    <p id="modal-comment-date" class="text-muted"></p>
                </div>
                <div class="mb-3">
                    <h6>Komentar:</h6>
                    <div id="modal-comment-body" class="bg-light p-3 rounded" style="white-space: pre-wrap;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <div class="btn-group">
                    <a href="#" id="modal-view-thread" class="btn btn-info">
                        <i class="fas fa-eye me-1"></i> Lihat Thread
                    </a>
                    <a href="#" id="modal-edit-comment" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionTitle">Tindakan Massal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bulkActionForm" action="{{ route('admin.comments.batch-action') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="action" id="bulkActionType">
                    <div id="selectedComments"></div>

                    <div class="mb-3" id="reasonField">
                        <label for="reason" class="form-label">Alasan:</label>
                        <textarea class="form-control" name="reason" id="reason" rows="3"></textarea>
                        <small class="form-text text-muted">Alasan ini mungkin akan dikirimkan ke penulis komentar.</small>
                    </div>

                    <div class="mb-3 form-check" id="notifyField">
                        <input type="checkbox" class="form-check-input" id="notify_users" name="notify_users" value="1">
                        <label class="form-check-label" for="notify_users">Kirim notifikasi ke penulis komentar</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="confirmBulkAction">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus komentar ini?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <form id="delete-form" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toggle Status Form -->
<form id="approval-form" action="" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select All Checkbox
        const selectAll = document.getElementById('selectAll');
        const commentCheckboxes = document.querySelectorAll('.comment-checkbox');
        const bulkActions = document.getElementById('bulkActions');

        selectAll.addEventListener('change', function() {
            const checked = this.checked;

            commentCheckboxes.forEach(checkbox => {
                checkbox.checked = checked;
            });

            updateBulkActionsVisibility();
        });

        commentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateBulkActionsVisibility();

                // Update "select all" checkbox
                if (!this.checked) {
                    selectAll.checked = false;
                } else {
                    const allChecked = Array.from(commentCheckboxes).every(c => c.checked);
                    selectAll.checked = allChecked;
                }
            });
        });

        function updateBulkActionsVisibility() {
            const anyChecked = Array.from(commentCheckboxes).some(checkbox => checkbox.checked);
            bulkActions.style.display = anyChecked ? 'flex' : 'none';
        }

        // Quick View Modal
        const commentModal = new bootstrap.Modal(document.getElementById('commentModal'));

        document.querySelectorAll('.quick-view-btn').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.getAttribute('data-comment-id');
                const commentBody = this.getAttribute('data-comment-body');
                const commentUser = this.getAttribute('data-comment-user');
                const commentDate = this.getAttribute('data-comment-date');
                const threadTitle = this.getAttribute('data-thread-title');

                // Update modal content
                document.getElementById('modal-comment-body').textContent = commentBody;
                document.getElementById('modal-comment-user').textContent = commentUser;
                document.getElementById('modal-comment-date').textContent = commentDate;
                document.getElementById('modal-thread-title').textContent = threadTitle;

                // Update modal action buttons
                const viewThreadBtn = document.getElementById('modal-view-thread');
                const editCommentBtn = document.getElementById('modal-edit-comment');

                // These hrefs should be set based on the comment data
                // You might need to add data attributes for thread-id and comment-id
                viewThreadBtn.href = `{{ url('threads') }}/${this.getAttribute('data-thread-id')}#comment-${commentId}`;
                editCommentBtn.href = `{{ url('comments') }}/${commentId}/edit`;
            });
        });

        // Bulk Actions
        const bulkActionModal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
        const bulkActionButtons = document.querySelectorAll('.bulk-action');
        const bulkActionForm = document.getElementById('bulkActionForm');
        const selectedCommentsContainer = document.getElementById('selectedComments');
        const bulkActionType = document.getElementById('bulkActionType');
        const bulkActionTitle = document.getElementById('bulkActionTitle');
        const reasonField = document.getElementById('reasonField');
        const notifyField = document.getElementById('notifyField');
        const confirmBulkAction = document.getElementById('confirmBulkAction');

        bulkActionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                bulkActionType.value = action;

                // Update modal based on action
                switch(action) {
                    case 'approve':
                        bulkActionTitle.textContent = 'Setujui Komentar Terpilih';
                        confirmBulkAction.textContent = 'Setujui';
                        confirmBulkAction.className = 'btn btn-success';
                        reasonField.style.display = 'none';
                        notifyField.style.display = 'block';
                        break;
                    case 'unapprove':
                        bulkActionTitle.textContent = 'Batalkan Persetujuan Komentar Terpilih';
                        confirmBulkAction.textContent = 'Batalkan';
                        confirmBulkAction.className = 'btn btn-warning';
                        reasonField.style.display = 'block';
                        notifyField.style.display = 'block';
                        break;
                    case 'delete':
                        bulkActionTitle.textContent = 'Hapus Komentar Terpilih';
                        confirmBulkAction.textContent = 'Hapus';
                        confirmBulkAction.className = 'btn btn-danger';
                        reasonField.style.display = 'block';
                        notifyField.style.display = 'block';
                        break;
                }

                // Add selected comment IDs to form
                selectedCommentsContainer.innerHTML = '';
                const selectedComments = Array.from(commentCheckboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);

                selectedComments.forEach(commentId => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'comment_ids[]';
                    input.value = commentId;
                    selectedCommentsContainer.appendChild(input);
                });

                // Show the modal
                bulkActionModal.show();
            });
        });

        // Delete modal
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.getAttribute('data-id');
                document.getElementById('delete-form').setAttribute('action', `{{ url('admin/comments') }}/${commentId}`);
                deleteModal.show();
            });
        });

        // Toggle approval
        document.querySelectorAll('.btn-approval').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.getAttribute('data-id');
                const isApproved = parseInt(this.getAttribute('data-status'));

                const message = isApproved ? 'Batalkan persetujuan komentar ini?' : 'Setujui komentar ini?';

                if (confirm(message)) {
                    const form = document.getElementById('approval-form');
                    form.setAttribute('action', `{{ url('admin/comments') }}/${commentId}/toggle-approval`);
                    form.submit();
                }
            });
        });
    });
</script>
@endsection
