@extends('layouts.moderator')

@section('title', 'Edit Komentar')

@section('styles')
<style>
    .comment-metadata {
        font-size: 0.9rem;
    }

    .thread-info {
        background-color: #f8f9fa;
        border-left: 4px solid #0d6efd;
        padding: 15px;
        margin-bottom: 20px;
    }

    .original-content {
        background-color: #fff8e1;
        border-left: 4px solid #ffb300;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Komentar</h1>
        <a href="{{ route('moderator.comments.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Main Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Informasi Komentar</h5>
        </div>
        <div class="card-body">
            <!-- Thread Info -->
            <div class="thread-info rounded mb-4">
                <h6 class="mb-2">Thread Asal:</h6>
                <a href="{{ route('threads.show', $comment->thread_id) }}" class="h5 mb-2 d-block text-decoration-none">
                    {{ $comment->thread->title ?? 'Thread tidak ditemukan' }}
                </a>
                <div class="comment-metadata text-muted">
                    <i class="fas fa-user me-1"></i> Oleh: {{ $comment->thread->user->name ?? 'Unknown User' }}
                    <i class="fas fa-calendar ms-3 me-1"></i> {{ $comment->thread->created_at ? $comment->thread->created_at->format('d M Y H:i') : 'N/A' }}
                    <i class="fas fa-comments ms-3 me-1"></i> {{ $comment->thread->comments_count ?? 0 }} Komentar
                </div>
            </div>

            <!-- Original Comment Content -->
            <div class="original-content rounded">
                <h6 class="mb-2">Konten Komentar Asli:</h6>
                <div class="mb-0">{{ $comment->body }}</div>
                <div class="mt-3 comment-metadata text-muted">
                    <i class="fas fa-user me-1"></i> Oleh: {{ $comment->user->name ?? 'Unknown User' }}
                    <i class="fas fa-calendar ms-3 me-1"></i> {{ $comment->created_at->format('d M Y H:i') }}

                    @if($comment->parent_id)
                        <i class="fas fa-reply ms-3 me-1"></i> Balasan untuk komentar #{{ $comment->parent_id }}
                    @endif

                    @if($comment->is_approved)
                        <span class="badge bg-success ms-2">Disetujui</span>
                    @else
                        <span class="badge bg-warning ms-2">Belum Disetujui</span>
                    @endif

                    @if($comment->is_flagged)
                        <span class="badge bg-danger ms-2">Ditandai</span>
                    @endif
                </div>
            </div>

            <!-- Edit Form -->
            <form action="{{ route('moderator.comments.update', $comment) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Form errors -->
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Comment Body -->
                <div class="mb-3">
                    <label for="body" class="form-label">Konten Komentar</label>
                    <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="5">{{ old('body', $comment->body) }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Approval Status -->
                <div class="mb-3">
                    <label class="form-label">Status Persetujuan</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_approved" id="approveYes" value="1" {{ $comment->is_approved ? 'checked' : '' }}>
                        <label class="form-check-label" for="approveYes">
                            <i class="fas fa-check-circle me-1 text-success"></i> Disetujui
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_approved" id="approveNo" value="0" {{ !$comment->is_approved ? 'checked' : '' }}>
                        <label class="form-check-label" for="approveNo">
                            <i class="fas fa-times-circle me-1 text-danger"></i> Ditolak
                        </label>
                    </div>
                </div>

                <!-- Flag Status -->
                <div class="mb-3">
                    <label class="form-label">Status Flag</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_flagged" id="flagYes" value="1" {{ $comment->is_flagged ? 'checked' : '' }}>
                        <label class="form-check-label" for="flagYes">
                            <i class="fas fa-flag me-1 text-danger"></i> Ditandai
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_flagged" id="flagNo" value="0" {{ !$comment->is_flagged ? 'checked' : '' }}>
                        <label class="form-check-label" for="flagNo">
                            <i class="fas fa-flag me-1 text-muted"></i> Tidak Ditandai
                        </label>
                    </div>
                </div>

                <!-- Moderation Notes -->
                <div class="mb-3">
                    <label for="moderationNote" class="form-label">Catatan Moderasi (Tidak akan ditampilkan kepada pengguna)</label>
                    <textarea class="form-control" id="moderationNote" name="moderation_note" rows="3">{{ old('moderation_note', $comment->moderation_note) }}</textarea>
                </div>

                <!-- Notify User -->
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notifyUser" name="notify_user" value="1" checked>
                        <label class="form-check-label" for="notifyUser">
                            Kirim notifikasi ke penulis komentar tentang perubahan ini
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>

                    <a href="{{ route('moderator.comments.index') }}" class="btn btn-secondary me-2">
                        Batal
                    </a>

                    <button type="button" class="btn btn-danger ms-auto" data-bs-toggle="modal" data-bs-target="#deleteCommentModal">
                        <i class="fas fa-trash me-1"></i> Hapus Komentar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Comment Modal -->
<div class="modal fade" id="deleteCommentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Komentar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus komentar ini? Tindakan ini tidak dapat dibatalkan.</p>
                <div class="alert alert-secondary">
                    {{ $comment->body }}
                </div>
                <form id="deleteForm" action="{{ route('moderator.comments.destroy', $comment) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label for="deleteReason" class="form-label">Alasan penghapusan (opsional)</label>
                        <textarea class="form-control" id="deleteReason" name="reason" rows="2" placeholder="Komentar melanggar ketentuan..."></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notifyUserOnDelete" name="notify_user" value="1" checked>
                        <label class="form-check-label" for="notifyUserOnDelete">
                            Kirim notifikasi ke penulis komentar
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete confirmation
        document.getElementById('confirmDelete').addEventListener('click', function() {
            document.getElementById('deleteForm').submit();
        });
    });
</script>
@endsection
