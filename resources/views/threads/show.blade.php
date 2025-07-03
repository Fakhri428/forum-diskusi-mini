@extends('layouts.app')

@section('title', $thread->title)

@section('meta')
<meta name="description" content="{{ Str::limit(strip_tags($thread->body), 160) }}">
<meta property="og:title" content="{{ $thread->title }}">
<meta property="og:description" content="{{ Str::limit(strip_tags($thread->body), 160) }}">
<meta property="og:type" content="article">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="csrf-token" content="{{ csrf_token() }}">
@if($thread->image)
    <meta property="og:image" content="{{ asset('storage/' . $thread->image) }}">
@endif
@endsection

@section('styles')
<style>
    /* Thread Header Styles */
    .thread-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
    }

    .thread-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .author-avatar {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .author-name {
        font-weight: 600;
        color: white;
    }

    .category-badge {
        background: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* Thread Container Improvements */
    .thread-container {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .thread-meta {
        background: #f8f9fa;
        padding: 20px 30px;
        border-bottom: 1px solid #dee2e6;
    }

    .thread-stats {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
        font-weight: 500;
    }

    .stat-item i {
        color: #6a11cb;
    }

    /* Thread Content Improvements */
    .thread-content {
        padding: 30px;
    }

    .thread-body {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333;
    }

    .thread-body img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 20px 0;
    }

    /* Thread Actions Improvements */
    .thread-actions {
        background: #f8f9fa;
        padding: 20px 30px;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }

    .vote-buttons {
        display: flex;
        align-items: center;
        gap: 10px;
        background: white;
        padding: 8px 15px;
        border-radius: 25px;
        border: 1px solid #dee2e6;
    }

    .vote-btn {
        background: none;
        border: none;
        color: #6c757d;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 5px;
        border-radius: 50%;
    }

    .vote-btn:hover {
        color: #6a11cb;
        background: rgba(106, 17, 203, 0.1);
    }

    .vote-btn.active {
        color: #6a11cb;
        background: rgba(106, 17, 203, 0.2);
    }

    .vote-count {
        font-weight: 600;
        color: #333;
        min-width: 30px;
        text-align: center;
    }

    /* Comment Section Improvements */
    .comment-section {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .comment-header {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        padding: 30px;
    }

    .comment-body {
        padding: 30px;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .thread-title {
            font-size: 1.8rem;
        }

        .thread-header,
        .thread-content,
        .thread-actions,
        .comment-header,
        .comment-body {
            padding: 20px;
        }

        .thread-stats {
            gap: 15px;
        }

        .action-buttons {
            width: 100%;
            justify-content: center;
        }

        .thread-actions {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Thread Header -->
    <div class="thread-header">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div class="flex-grow-1">
                <h1 class="thread-title">{{ $thread->title }}</h1>
                <div class="thread-meta d-flex align-items-center flex-wrap gap-3 mt-2">
                    <div class="author-info d-flex align-items-center">
                        <div class="author-avatar me-2">
                            {{ strtoupper(substr($thread->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <span class="author-name">{{ $thread->user->name }}</span>
                            <small class="text-muted d-block">{{ $thread->created_at->diffForHumans() }}</small>
                        </div>
                    </div>

                    @if($thread->category)
                        <span class="badge category-badge">
                            <i class="fas fa-folder me-1"></i>{{ $thread->category->name }}
                        </span>
                    @endif

                    @if($thread->is_pinned)
                        <span class="badge bg-warning">
                            <i class="fas fa-thumbtack me-1"></i>Pinned
                        </span>
                    @endif

                    @if($thread->is_locked)
                        <span class="badge bg-secondary">
                            <i class="fas fa-lock me-1"></i>Locked
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="thread-container">
        {{-- existing thread-meta section --}}
        <div class="thread-meta">
            <div class="thread-stats">
                <div class="stat-item">
                    <i class="fas fa-eye"></i>
                    <span>{{ $thread->views_count ?? 0 }} dilihat</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-comments"></i>
                    <span>{{ $totalComments ?? $thread->comments->count() }} komentar</span>
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
                    <img src="{{ asset('storage/' . $thread->image) }}"
                         alt="Thread Image"
                         class="img-fluid rounded"
                         style="max-height: 500px; width: auto; object-fit: cover;"
                         onerror="this.style.display='none'">
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
                        <button class="vote-btn" data-type="up" data-thread="{{ $thread->id }}" title="Upvote">
                            <i class="fas fa-chevron-up"></i>
                        </button>
                        <span class="vote-count">{{ $thread->votes_sum ?? 0 }}</span>
                        <button class="vote-btn" data-type="down" data-thread="{{ $thread->id }}" title="Downvote">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#shareModal">
                        <i class="fas fa-share-alt me-1"></i>Bagikan
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
                    {{-- UPDATE: Gunakan $totalComments dari controller --}}
                    <h3 class="mb-0 gradient-text">Diskusi ({{ $totalComments ?? 0 }})</h3>
                    <p class="mb-0 mt-1 opacity-75">Berpartisipasilah dalam diskusi konstruktif</p>
                </div>
            </div>
        </div>

        <div class="comment-body">
            @if($thread->is_locked ?? false)
                <div class="alert alert-warning">
                    <i class="fas fa-lock me-2"></i>
                    Thread ini telah dikunci. Komentar baru tidak dapat ditambahkan.
                </div>
            @endif

            {{-- SINGLE Comments Display Section --}}
            @if(isset($comments) && $comments->count() > 0)
                <div class="comments-container">
                    @include('threads.partials.comments', [
                        'comments' => $comments,
                        'level' => 0,
                        'thread' => $thread
                    ])
                </div>
            @elseif(isset($thread->comments) && $thread->comments->where('parent_id', null)->count() > 0)
                {{-- Fallback: Gunakan relationship jika $comments tidak ada --}}
                <div class="comments-container">
                    @include('threads.partials.comments', [
                        'comments' => $thread->comments()->whereNull('parent_id')->with('user', 'children.user')->orderBy('created_at', 'asc')->get(),
                        'level' => 0,
                        'thread' => $thread
                    ])
                </div>
            @else
                {{-- Empty state --}}
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-comments fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Belum ada komentar</h5>
                    <p class="text-muted">Jadilah yang pertama berkomentar!</p>

                    @auth
                        @if(!($thread->is_locked ?? false))
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('commentForm').scrollIntoView({behavior: 'smooth'})">
                                <i class="fas fa-comment me-1"></i>Tulis Komentar
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt me-1"></i>Login untuk Berkomentar
                        </a>
                    @endauth
                </div>
            @endif

            {{-- Main Comment Form --}}
            @auth
                @if(!($thread->is_locked ?? false))
                    <div class="comment-form mt-4" id="comment-form">
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
                                         required>{{ old('body') }}</textarea>

                                @error('body')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Komentar yang baik membantu diskusi menjadi lebih berkualitas
                                </div>
                                <button type="submit" class="btn btn-primary" id="submitCommentBtn">
                                    <i class="fas fa-paper-plane me-2"></i>Kirim Komentar
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            @else
                <div class="auth-prompt mt-4">
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
                <h5 class="modal-title" id="shareModalLabel">
                    <i class="fas fa-share-alt me-2"></i>Bagikan Thread
                </h5>
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
                    <a href="https://wa.me/?text={{ urlencode($thread->title . ' - ' . url()->current()) }}"
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
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2 text-danger"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus thread ini?</p>
                <div class="alert alert-warning">
                    <small><i class="fas fa-info-circle me-1"></i>Tindakan ini tidak dapat dibatalkan dan akan menghapus semua komentar terkait.</small>
                </div>
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
    console.log('Thread show page loaded');

    // Copy URL functionality
    const copyBtn = document.getElementById('copyUrl');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
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
    }

    // Main Comment Form submission
    const commentForm = document.getElementById('commentForm');
    if (commentForm) {
        console.log('Main comment form found:', commentForm.action);

        commentForm.addEventListener('submit', function(e) {
            console.log('Main comment form submitted');

            const textarea = this.querySelector('textarea[name="body"]');
            const submitBtn = document.getElementById('submitCommentBtn');

            // Validation
            if (textarea && textarea.value.trim().length < 5) {
                e.preventDefault();
                alert('Komentar minimal 5 karakter');
                textarea.focus();
                return false;
            }

            // Loading state
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
                submitBtn.disabled = true;
            }
        });
    } else {
        console.error('Main comment form NOT found');
    }

    // Reply Button Functionality
    function setupReplyButtons() {
        document.querySelectorAll('.reply-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Reply button clicked');

                const commentId = this.dataset.commentId;
                const replyFormId = `reply-form-${commentId}`;
                const replyForm = document.getElementById(replyFormId);

                console.log('Comment ID:', commentId);
                console.log('Reply form ID:', replyFormId);
                console.log('Reply form found:', replyForm !== null);

                if (replyForm) {
                    // Toggle visibility
                    const isHidden = replyForm.style.display === 'none' || replyForm.classList.contains('d-none');

                    if (isHidden) {
                        replyForm.style.display = 'block';
                        replyForm.classList.remove('d-none');

                        // Focus on textarea
                        const textarea = replyForm.querySelector('textarea[name="body"]');
                        if (textarea) {
                            setTimeout(() => textarea.focus(), 100);
                        }

                        // Update button text
                        this.innerHTML = '<i class="fas fa-times me-1"></i>Batal';
                        this.classList.remove('btn-outline-primary');
                        this.classList.add('btn-outline-secondary');
                    } else {
                        replyForm.style.display = 'none';
                        replyForm.classList.add('d-none');

                        // Reset button text
                        this.innerHTML = '<i class="fas fa-reply me-1"></i>Balas';
                        this.classList.remove('btn-outline-secondary');
                        this.classList.add('btn-outline-primary');
                    }
                } else {
                    console.error('Reply form not found for comment:', commentId);
                }
            });
        });
    }

    // Cancel Reply Button Functionality
    function setupCancelButtons() {
        document.querySelectorAll('.cancel-reply-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const commentId = this.dataset.commentId;
                const replyForm = document.getElementById(`reply-form-${commentId}`);
                const replyBtn = document.querySelector(`[data-comment-id="${commentId}"].reply-btn`);

                if (replyForm) {
                    replyForm.style.display = 'none';
                    replyForm.classList.add('d-none');

                    // Reset textarea
                    const textarea = replyForm.querySelector('textarea[name="body"]');
                    if (textarea) {
                        textarea.value = '';
                    }
                }

                if (replyBtn) {
                    replyBtn.innerHTML = '<i class="fas fa-reply me-1"></i>Balas';
                    replyBtn.classList.remove('btn-outline-secondary');
                    replyBtn.classList.add('btn-outline-primary');
                }
            });
        });
    }

    // Reply Form Submission
    function setupReplyFormSubmissions() {
        document.querySelectorAll('form[id^="reply-form-"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                console.log('Reply form submitted:', this.action);

                const textarea = this.querySelector('textarea[name="body"]');
                const submitBtn = this.querySelector('button[type="submit"]');

                // Validation
                if (textarea && textarea.value.trim().length < 5) {
                    e.preventDefault();
                    alert('Balasan minimal 5 karakter');
                    textarea.focus();
                    return false;
                }

                // Loading state
                if (submitBtn) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Mengirim...';
                    submitBtn.disabled = true;

                    // Reset after timeout if something goes wrong
                    setTimeout(() => {
                        if (submitBtn.disabled) {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }
                    }, 10000);
                }
            });
        });
    }

    // Auto-resize textarea
    function setupTextareas() {
        document.querySelectorAll('.comment-textarea').forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = Math.max(this.scrollHeight, 80) + 'px';
            });
        });
    }

    // Vote functionality with improved error handling
    function setupVoting() {
        document.querySelectorAll('.vote-btn').forEach(button => {
            button.addEventListener('click', function() {
                const type = this.dataset.type;
                const threadId = this.dataset.thread;
                const commentId = this.dataset.comment;

                // Disable button during request
                this.disabled = true;
                const originalContent = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                let url;
                if (threadId) {
                    url = `/vote/thread/${threadId}`;
                } else if (commentId) {
                    url = `/vote/comment/${commentId}`;
                } else {
                    console.error('No thread or comment ID found');
                    this.disabled = false;
                    this.innerHTML = originalContent;
                    return;
                }

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({ value: type === 'up' ? 1 : -1 })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const voteCountEl = this.parentElement.querySelector('.vote-count');
                        if (voteCountEl) {
                            voteCountEl.textContent = data.total || 0;
                        }

                        // Update button states
                        const voteButtons = this.parentElement.querySelectorAll('.vote-btn');
                        voteButtons.forEach(btn => btn.classList.remove('active'));

                        if (data.user_vote) {
                            const activeBtn = this.parentElement.querySelector(`[data-type="${data.user_vote > 0 ? 'up' : 'down'}"]`);
                            if (activeBtn) {
                                activeBtn.classList.add('active');
                            }
                        }
                    } else {
                        throw new Error(data.message || 'Vote failed');
                    }
                })
                .catch(error => {
                    console.error('Vote error:', error);
                    alert('Terjadi kesalahan saat voting: ' + error.message);
                })
                .finally(() => {
                    // Re-enable button
                    this.disabled = false;
                    this.innerHTML = originalContent;
                });
            });
        });
    }

    // Initialize all functionalities
    function initializeComments() {
        setupReplyButtons();
        setupCancelButtons();
        setupReplyFormSubmissions();
        setupTextareas();
        setupVoting();

        console.log('All comment functionalities initialized');
    }

    // Initial setup
    initializeComments();

    // Re-initialize after dynamic content is loaded
    window.reinitializeComments = initializeComments;
});
</script>
@endpush
