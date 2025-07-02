@extends('layouts.app')

@section('title', $thread->title)

@section('meta')
<meta name="description" content="{{ Str::limit(strip_tags($thread->body), 160) }}">
<meta property="og:title" content="{{ $thread->title }}">
<meta property="og:description" content="{{ Str::limit(strip_tags($thread->body), 160) }}">
<meta property="og:type" content="article">
<meta property="og:url" content="{{ url()->current() }}">
@if($thread->image)
    <meta property="og:image" content="{{ asset($thread->image) }}">
@endif
@endsection

@section('styles')
<style>
    .thread-container {
        background-color: #ffffff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .thread-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        position: relative;
    }

    .thread-header::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 20px;
        background: white;
        border-radius: 20px 20px 0 0;
    }

    .thread-meta {
        background-color: #f8f9fa;
        padding: 20px 30px;
        border-bottom: 1px solid #e9ecef;
    }

    .thread-content {
        padding: 30px;
        background-color: #ffffff;
    }

    .thread-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 15px 0;
    }

    .author-info {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .author-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 18px;
        margin-right: 15px;
        border: 3px solid white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .thread-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 15px;
    }

    .thread-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-pinned {
        background-color: #ffeaa7;
        color: #fdcb6e;
    }

    .badge-locked {
        background-color: #fab1a0;
        color: #e17055;
    }

    .badge-category {
        background-color: #74b9ff;
        color: #0984e3;
    }

    .thread-stats {
        display: flex;
        align-items: center;
        gap: 20px;
        color: #6c757d;
        font-size: 14px;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .thread-actions {
        padding: 20px 30px;
        background-color: #f8f9fa;
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .vote-buttons {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
    }

    .vote-btn {
        border: none;
        background: none;
        font-size: 18px;
        color: #6c757d;
        transition: all 0.3s ease;
        padding: 5px;
        border-radius: 5px;
    }

    .vote-btn:hover {
        background-color: #e9ecef;
        color: #495057;
    }

    .vote-btn.active {
        color: #667eea;
    }

    .vote-count {
        font-weight: bold;
        font-size: 16px;
    }

    /* Comment Section Styles */
    .comment-section {
        background-color: #ffffff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-top: 30px;
    }

    .comment-header {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        padding: 25px 30px;
        position: relative;
    }

    .comment-header::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 20px;
        background: white;
        border-radius: 20px 20px 0 0;
    }

    .comment-body {
        padding: 30px;
    }

    .comment-item {
        background-color: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }

    .comment-item:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .comment-meta {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 10px;
    }

    .comment-author {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .comment-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 14px;
    }

    .comment-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .comment-form {
        background-color: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        margin-top: 25px;
    }

    .comment-textarea {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        padding: 15px;
        resize: none;
        transition: all 0.3s ease;
    }

    .comment-textarea:focus {
        border-color: #6a11cb;
        box-shadow: 0 0 0 0.2rem rgba(106, 17, 203, 0.25);
    }

    .empty-comments {
        text-align: center;
        padding: 60px 30px;
        color: #6c757d;
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .auth-prompt {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 12px;
        text-align: center;
        margin-top: 25px;
    }

    .reply-form {
        background-color: #ffffff;
        border-radius: 8px;
        padding: 20px;
        margin-top: 15px;
        border: 2px solid #e9ecef;
    }

    .nested-comment {
        margin-left: 30px;
        border-left: 2px solid #6a11cb;
        padding-left: 20px;
        margin-top: 15px;
    }

    .gradient-text {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
        font-weight: bold;
    }

    @media (max-width: 768px) {
        .thread-header, .thread-meta, .thread-content, .thread-actions {
            padding: 20px;
        }

        .comment-header, .comment-body {
            padding: 20px;
        }

        .thread-stats {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .action-buttons {
            width: 100%;
            justify-content: space-between;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('threads.index') }}">Thread</a></li>
            <li class="breadcrumb-item"><a href="{{ route('categories.show', $thread->category) }}">{{ $thread->category->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($thread->title, 50) }}</li>
        </ol>
    </nav>

    <!-- Thread Content -->
    <div class="thread-container">
        <!-- Thread Header -->
        <div class="thread-header">
            <h1 class="mb-3">{{ $thread->title }}</h1>

            <div class="thread-badges">
                @if($thread->is_pinned)
                    <span class="thread-badge badge-pinned">
                        <i class="fas fa-thumbtack me-1"></i>Disematkan
                    </span>
                @endif

                @if($thread->is_locked)
                    <span class="thread-badge badge-locked">
                        <i class="fas fa-lock me-1"></i>Dikunci
                    </span>
                @endif

                <span class="thread-badge badge-category">
                    <i class="fas fa-folder me-1"></i>{{ $thread->category->name }}
                </span>
            </div>
        </div>

        <!-- Thread Meta Information -->
        <div class="thread-meta">
            <div class="author-info">
                <div class="author-avatar">
                    {{ strtoupper(substr($thread->user->name, 0, 1)) }}
                </div>
                <div>
                    <h6 class="mb-0">{{ $thread->user->name }}</h6>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Dibuat {{ $thread->created_at->diffForHumans() }}
                        @if($thread->created_at != $thread->updated_at)
                            <span class="ms-2">
                                <i class="fas fa-edit me-1"></i>
                                Diubah {{ $thread->updated_at->diffForHumans() }}
                            </span>
                        @endif
                    </small>
                </div>
            </div>

            <div class="thread-stats">
                <div class="stat-item">
                    <i class="fas fa-eye"></i>
                    <span>{{ $thread->views_count ?? 0 }} dilihat</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-comments"></i>
                    <span>{{ $thread->comments->count() }} komentar</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-thumbs-up"></i>
                    <span>{{ $thread->votes_count ?? 0 }} vote</span>
                </div>
                @if($thread->tags)
                    <div class="stat-item">
                        <i class="fas fa-tags"></i>
                        <span>{{ $thread->tags }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Thread Content -->
        <div class="thread-content">
            @if($thread->image)
                <div class="mb-4">
                    <img src="{{ asset($thread->image) }}" alt="Thread Image" class="img-fluid rounded">
                </div>
            @endif

            <div class="thread-body">
                {!! $thread->body !!}
            </div>
        </div>

        <!-- Thread Actions -->
        <div class="thread-actions">
            <div class="action-buttons">
                @auth
                    <!-- Vote Buttons -->
                    <div class="vote-buttons">
                        <button class="vote-btn" data-type="up" data-thread="{{ $thread->id }}">
                            <i class="fas fa-chevron-up"></i>
                        </button>
                        <span class="vote-count">{{ $thread->votes_sum ?? 0 }}</span>
                        <button class="vote-btn" data-type="down" data-thread="{{ $thread->id }}">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#shareModal">
                        <i class="fas fa-share-alt me-1"></i>Bagikan
                    </button>

                    <button class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-bookmark me-1"></i>Simpan
                    </button>

                    @if(Auth::id() === $thread->user_id)
                        <a href="{{ route('threads.edit', $thread) }}" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>

                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-1"></i>Hapus
                        </button>
                    @endif
                @else
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#shareModal">
                        <i class="fas fa-share-alt me-1"></i>Bagikan
                    </button>
                @endauth
            </div>

            <div class="thread-id">
                <small class="text-muted">Thread ID: #{{ $thread->id }}</small>
            </div>
        </div>
    </div>

    <!-- Discussion Section -->
    <div class="comment-section">
        <div class="comment-header">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <div style="width: 50px; height: 50px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-comments text-primary" style="font-size: 24px;"></i>
                    </div>
                </div>
                <div>
                    <h3 class="mb-0 gradient-text">Diskusi ({{ $thread->comments->where('parent_id', null)->count() }})</h3>
                    <p class="mb-0 mt-1 opacity-75">Berpartisipasilah dalam diskusi konstruktif</p>
                </div>
            </div>
        </div>

        <div class="comment-body">
            @if($thread->is_locked)
                <div class="alert alert-warning">
                    <i class="fas fa-lock me-2"></i>
                    Thread ini telah dikunci. Komentar baru tidak dapat ditambahkan.
                </div>
            @endif

            @if($thread->comments->where('parent_id', null)->count() > 0)
                <div class="comments-container">
                    @include('threads.partials.comments', [
                        'comments' => $thread->comments()->whereNull('parent_id')->with('user', 'votes', 'children.user', 'children.votes')->latest()->get()
                    ])
                </div>
            @else
                <div class="empty-comments">
                    <div class="empty-icon">
                        <i class="far fa-comments"></i>
                    </div>
                    <h5>Belum ada komentar dalam diskusi ini</h5>
                    <p class="text-muted">Jadilah yang pertama untuk berkomentar dan mulai diskusi!</p>
                </div>
            @endif

            @auth
                @if(!$thread->is_locked)
                    <div class="comment-form">
                        <div class="d-flex mb-3">
                            <div class="comment-avatar me-3">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <h5 class="mb-0 pt-2">Tambahkan Komentar</h5>
                        </div>

                        <form action="{{ route('comments.store', $thread) }}" method="POST" id="commentForm">
                            @csrf
                            <div class="mb-3">
                                <textarea name="body"
                                         class="form-control comment-textarea"
                                         placeholder="Bagikan pendapat Anda tentang diskusi ini..."
                                         rows="4"
                                         required></textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Komentar yang baik membantu diskusi menjadi lebih berkualitas
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Komentar
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            @else
                <div class="auth-prompt">
                    <h5><i class="fas fa-lock me-2"></i>Bergabung dalam Diskusi</h5>
                    <p class="mb-3">Untuk berpartisipasi dalam diskusi, silahkan login atau daftar terlebih dahulu</p>
                    <div>
                        <a href="{{ route('login') }}" class="btn btn-light me-2">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-light">
                            <i class="fas fa-user-plus me-1"></i>Daftar
                        </a>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</div>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">Bagikan Thread</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="shareUrl" class="form-label">URL Thread:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="shareUrl" value="{{ url()->current() }}" readonly>
                        <button class="btn btn-outline-primary" type="button" id="copyUrl">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-center">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                       target="_blank" class="btn btn-primary">
                        <i class="fab fa-facebook-f me-1"></i>Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($thread->title) }}"
                       target="_blank" class="btn btn-info">
                        <i class="fab fa-twitter me-1"></i>Twitter
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($thread->title . ' ' . url()->current()) }}"
                       target="_blank" class="btn btn-success">
                        <i class="fab fa-whatsapp me-1"></i>WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if(Auth::check() && Auth::id() === $thread->user_id)
<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Hapus Thread</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus thread ini?</p>
                <p class="text-danger small">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Tindakan ini tidak dapat dibatalkan dan semua komentar akan ikut terhapus.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('threads.destroy', $thread) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Hapus Thread
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Copy URL functionality
    document.getElementById('copyUrl').addEventListener('click', function() {
        const urlInput = document.getElementById('shareUrl');
        urlInput.select();
        document.execCommand('copy');

        // Show feedback
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-check"></i>';
        this.classList.add('btn-success');
        this.classList.remove('btn-outline-primary');

        setTimeout(() => {
            this.innerHTML = originalText;
            this.classList.remove('btn-success');
            this.classList.add('btn-outline-primary');
        }, 2000);
    });

    // Vote functionality (if you have voting system)
    document.querySelectorAll('.vote-btn').forEach(button => {
        button.addEventListener('click', function() {
            const type = this.dataset.type;
            const threadId = this.dataset.thread;

            // Send AJAX request to vote endpoint
            // This is a placeholder - implement according to your voting system
            fetch(`/threads/${threadId}/vote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ type: type })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector('.vote-count').textContent = data.total;
                    // Update button states
                    document.querySelectorAll('.vote-btn').forEach(btn => btn.classList.remove('active'));
                    if (data.user_vote) {
                        document.querySelector(`[data-type="${data.user_vote}"]`).classList.add('active');
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Auto-resize textarea
    const textarea = document.querySelector('.comment-textarea');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.max(this.scrollHeight, 100) + 'px';
        });
    }

    // Reply functionality (implement according to your comment system)
    document.querySelectorAll('.reply-btn').forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const replyForm = document.getElementById(`reply-form-${commentId}`);

            if (replyForm) {
                replyForm.classList.toggle('d-none');
                if (!replyForm.classList.contains('d-none')) {
                    replyForm.querySelector('textarea').focus();
                }
            }
        });
    });
});
</script>
@endpush
