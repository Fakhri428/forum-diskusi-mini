<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/my-comments.blade.php -->
@extends('layouts.app')

@section('styles')
<style>
    .comment-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .comment-card:hover {
        transform: translateY(-3px);
    }

    .thread-info {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        padding: 0.75rem 1rem;
    }

    .page-title {
        color: #4e54c8;
        font-weight: 700;
        border-bottom: 3px solid #4e54c8;
        display: inline-block;
        padding-bottom: 5px;
    }

    .action-btn {
        border-radius: 20px;
        padding: 5px 15px;
    }

    .comment-area {
        background-color: #f8f9fa;
        border-left: 4px solid #6a11cb;
        padding: 1rem;
        border-radius: 0 0.5rem 0.5rem 0;
    }

    .avatar-circle {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: bold;
    }

    .vote-info {
        display: flex;
        align-items: center;
        color: #6c757d;
        font-weight: 500;
    }

    .vote-badge {
        background-color: #f0f2f5;
        padding: 0.25rem 0.5rem;
        border-radius: 1rem;
        margin-right: 0.5rem;
    }

    .vote-positive {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .vote-negative {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    .filter-dropdown {
        border-radius: 20px;
        padding: 0.5rem 1rem;
        background-color: #f0f2f5;
        border: none;
    }

    .filter-dropdown:focus {
        box-shadow: 0 0 0 0.25rem rgba(106, 17, 203, 0.25);
        border-color: #6a11cb;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Komentar Saya</h1>

        <div>
            <select class="form-select filter-dropdown">
                <option value="all">Semua Komentar</option>
                <option value="recent">Terbaru</option>
                <option value="voted">Paling Disukai</option>
                <option value="replied">Dengan Balasan</option>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @forelse(Auth::user()->comments()->with('thread')->latest()->get() as $comment)
                <div class="comment-card">
                    <div class="thread-info d-flex justify-content-between">
                        <div>
                            <a href="{{ route('threads.show', $comment->thread_id) }}" class="text-white text-decoration-none">
                                <strong>{{ $comment->thread->title }}</strong>
                            </a>
                        </div>
                        <div class="small">
                            <i class="far fa-clock me-1"></i>{{ $comment->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <div class="avatar-circle me-3">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="comment-area flex-grow-1">
                                <p class="mb-2">{{ $comment->body }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="vote-info">
                                        <span class="vote-badge {{ $comment->voteScore() > 0 ? 'vote-positive' : ($comment->voteScore() < 0 ? 'vote-negative' : '') }}">
                                            <i class="fas {{ $comment->voteScore() > 0 ? 'fa-thumbs-up' : ($comment->voteScore() < 0 ? 'fa-thumbs-down' : 'fa-thumbs-up') }} me-1"></i>
                                            {{ $comment->voteScore() }}
                                        </span>

                                        @if($comment->children && $comment->children->count() > 0)
                                            <span class="ms-2">
                                                <i class="fas fa-reply me-1"></i>
                                                {{ $comment->children->count() }} balasan
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('threads.show', $comment->thread_id) }}" class="btn btn-sm btn-info action-btn">
                                <i class="fas fa-eye me-1"></i>Lihat Diskusi
                            </a>
                            <button type="button" class="btn btn-sm btn-primary action-btn" onclick="editComment({{ $comment->id }}, '{{ addslashes($comment->body) }}')">
                                <i class="fas fa-edit me-1"></i>Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-danger action-btn" onclick="confirmDelete({{ $comment->id }})">
                                <i class="fas fa-trash-alt me-1"></i>Hapus
                            </button>
                        </div>
                    </div>

                    <!-- If there are replies, show a preview -->
                    @if($comment->children && $comment->children->count() > 0)
                        <div class="card-footer bg-light">
                            <div class="d-flex align-items-center">
                                <div class="small text-muted me-2">
                                    <i class="fas fa-reply me-1"></i>Balasan terakhir:
                                </div>
                                <div class="small fw-bold">
                                    {{ $comment->children->first()->user->name }}:
                                </div>
                                <div class="ms-2 small text-truncate">
                                    {{ Str::limit($comment->children->first()->body, 50) }}
                                </div>
                                <a href="{{ route('threads.show', $comment->thread_id) }}" class="ms-auto small">
                                    Lihat semua
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center p-5 bg-light rounded-3">
                    <div class="p-4">
                        <i class="far fa-comment-dots fa-4x text-muted mb-4"></i>
                        <h4>Belum ada komentar yang kamu tulis</h4>
                        <p class="text-muted">Berikan pendapatmu dalam diskusi untuk berpartisipasi dalam komunitas</p>
                        <a href="{{ route('threads.index') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-comments me-2"></i>Jelajahi Diskusi
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Edit Comment Modal -->
    <div class="modal fade" id="editCommentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Komentar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editCommentForm" method="POST">
                    <div class="modal-body">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="commentBody" class="form-label">Isi Komentar</label>
                            <textarea class="form-control" id="commentBody" name="body" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus komentar ini?</p>
                    <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteCommentForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function editComment(commentId, commentBody) {
        const form = document.getElementById('editCommentForm');
        form.action = `/comments/${commentId}`;
        document.getElementById('commentBody').value = commentBody.replace(/\\'/g, "'");
        const modal = new bootstrap.Modal(document.getElementById('editCommentModal'));
        modal.show();
    }

    function confirmDelete(commentId) {
        const form = document.getElementById('deleteCommentForm');
        form.action = `/comments/${commentId}`;
        const modal = new bootstrap.Modal(document.getElementById('deleteCommentModal'));
        modal.show();
    }
</script>
@endpush
