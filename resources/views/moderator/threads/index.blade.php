@extends('layouts.moderator')

@section('title', 'Kelola Thread')

@section('styles')
<style>
    .badge-pill {
        border-radius: 20px;
        padding: 0.35em 0.65em;
    }
    .thread-title {
        font-weight: 500;
        color: #333;
    }
    .thread-excerpt {
        max-height: 60px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .filter-card {
        border-radius: 10px;
    }
    .action-column {
        width: 160px;
    }
    .status-badge {
        font-weight: normal;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola Thread</h1>
    </div>

    <!-- Filter and Search -->
    <div class="card shadow-sm filter-card mb-4">
        <div class="card-body">
            <form action="{{ route('moderator.threads.index') }}" method="GET" class="row g-3">
                <div class="col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Cari judul thread..." name="search" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-2">
                    <select class="form-select" name="category_id">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <select class="form-select" name="is_approved">
                        <option value="">Status Persetujuan</option>
                        <option value="1" {{ request('is_approved') === '1' ? 'selected' : '' }}>Disetujui</option>
                        <option value="0" {{ request('is_approved') === '0' ? 'selected' : '' }}>Ditunda</option>
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
    <form id="batchForm" action="{{ route('moderator.threads.batch-moderate') }}" method="POST">
        @csrf
        <input type="hidden" name="action" id="batchAction">

        <!-- Data Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Thread</h5>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cogs me-1"></i> Aksi Masal
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><button type="button" class="dropdown-item" data-action="approve"><i class="fas fa-check-circle me-2 text-success"></i>Setujui Thread</button></li>
                            <li><button type="button" class="dropdown-item" data-action="reject"><i class="fas fa-times-circle me-2 text-danger"></i>Tolak Thread</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button type="button" class="dropdown-item" data-action="lock"><i class="fas fa-lock me-2 text-warning"></i>Kunci Thread</button></li>
                            <li><button type="button" class="dropdown-item" data-action="unlock"><i class="fas fa-unlock me-2 text-info"></i>Buka Thread</button></li>
                        </ul>
                    </div>
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
                                <th>Thread</th>
                                <th>Penulis</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($threads as $thread)
                            <tr>
                                <td class="px-4">
                                    <div class="form-check">
                                        <input class="form-check-input thread-checkbox" type="checkbox" name="thread_ids[]" value="{{ $thread->id }}">
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="{{ route('moderator.threads.show', $thread) }}" class="thread-title">
                                            {{ $thread->title }}
                                        </a>
                                        <div class="thread-excerpt text-muted small mt-1">
                                            {{ Str::limit(strip_tags($thread->body), 100) }}
                                        </div>
                                        <div class="mt-1">
                                            <span class="badge bg-secondary">
                                                <i class="far fa-clock me-1"></i>{{ $thread->created_at->format('d M Y') }}
                                            </span>
                                            @if($thread->is_locked)
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-lock me-1"></i>Terkunci
                                                </span>
                                            @endif
                                            @if($thread->is_pinned)
                                                <span class="badge bg-info">
                                                    <i class="fas fa-thumbtack me-1"></i>Disematkan
                                                </span>
                                            @endif
                                            @if($thread->is_flagged)
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-flag me-1"></i>Ditandai
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2 bg-secondary text-white rounded-circle text-center d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">
                                            {{ strtoupper(substr($thread->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>{{ $thread->user->name ?? 'User Tidak Ditemukan' }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($thread->category)
                                        <span class="badge bg-info">{{ $thread->category->name }}</span>
                                    @else
                                        <span class="badge bg-secondary">Tanpa Kategori</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge status-badge {{ $thread->is_approved ? 'bg-success' : 'bg-warning' }}">
                                        {{ $thread->is_approved ? 'Disetujui' : 'Menunggu' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('threads.show', $thread) }}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Lihat Thread">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('moderator.threads.edit', $thread) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit Thread">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-success single-action" data-action="approve" data-id="{{ $thread->id }}" data-bs-toggle="tooltip" title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger single-action" data-action="reject" data-id="{{ $thread->id }}" data-bs-toggle="tooltip" title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                        <h5>Tidak ada thread yang ditemukan</h5>
                                        <p class="text-muted">Thread yang sesuai dengan filter akan muncul di sini</p>
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
                        Menampilkan {{ $threads->firstItem() ?? 0 }} - {{ $threads->lastItem() ?? 0 }} dari {{ $threads->total() }} thread
                    </div>
                    <div>
                        {{ $threads->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Moderation Modal -->
<div class="modal fade" id="moderationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moderationModalTitle">Moderasi Thread</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="moderationReason" class="form-label">Alasan Moderasi</label>
                    <textarea class="form-control" id="moderationReason" name="reason" rows="3" placeholder="Berikan alasan untuk tindakan moderasi ini..."></textarea>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="notifyUsers" name="notify_users" checked>
                    <label class="form-check-label" for="notifyUsers">
                        Kirim notifikasi kepada pengguna
                    </label>
                </div>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Select All checkbox
        const selectAllCheckbox = document.getElementById('selectAll');
        const threadCheckboxes = document.querySelectorAll('.thread-checkbox');

        selectAllCheckbox.addEventListener('change', function() {
            threadCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });

        // Moderation modal setup
        const moderationModal = new bootstrap.Modal(document.getElementById('moderationModal'));
        const moderationForm = document.getElementById('batchForm');
        const actionButtons = document.querySelectorAll('[data-action]');
        const submitButton = document.getElementById('submitModeration');

        let currentAction = '';
        let isBatchAction = false;
        let singleThreadId = null;

        // Setup batch actions
        actionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                currentAction = action;

                if (this.classList.contains('single-action')) {
                    // Single thread action
                    isBatchAction = false;
                    singleThreadId = this.getAttribute('data-id');
                    document.getElementById('moderationModalTitle').textContent =
                        `${action === 'approve' ? 'Setujui' : 'Tolak'} Thread`;
                } else {
                    // Batch action
                    isBatchAction = true;
                    singleThreadId = null;
                    document.getElementById('moderationModalTitle').textContent =
                        `${action === 'approve' ? 'Setujui' : action === 'reject' ? 'Tolak' : action === 'lock' ? 'Kunci' : 'Buka'} Thread Terpilih`;
                }

                moderationModal.show();
            });
        });

        // Handle moderation submission
        submitButton.addEventListener('click', function() {
            const reason = document.getElementById('moderationReason').value;
            const notifyUsers = document.getElementById('notifyUsers').checked;

            // Set the action
            document.getElementById('batchAction').value = currentAction;

            // Add hidden fields to the form
            let reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'reason';
            reasonInput.value = reason;
            moderationForm.appendChild(reasonInput);

            let notifyInput = document.createElement('input');
            notifyInput.type = 'hidden';
            notifyInput.name = 'notify_users';
            notifyInput.value = notifyUsers ? '1' : '0';
            moderationForm.appendChild(notifyInput);

            if (!isBatchAction && singleThreadId) {
                // For single thread action, add thread_ids[] with the single ID
                let threadInput = document.createElement('input');
                threadInput.type = 'hidden';
                threadInput.name = 'thread_ids[]';
                threadInput.value = singleThreadId;
                moderationForm.appendChild(threadInput);
            }

            // Submit the form
            moderationForm.submit();
        });
    });
</script>
@endsection
