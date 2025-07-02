{{-- filepath: resources/views/threads/partials/comments.blade.php --}}

{{-- Tambahkan default value untuk $level di awal file --}}
@php
$level = $level ?? 0; // Set default value jika $level tidak didefinisikan
@endphp

@foreach ($comments as $comment)
    <div id="comment-{{ $comment->id }}"
         class="comment-item mb-3 p-3 border rounded"
         style="margin-left: {{ min($level * 20, 100) }}px;
                background: {{ $level > 0 ? '#f8f9fa' : '#ffffff' }};">

        {{-- Comment Header --}}
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="d-flex align-items-center">
                {{-- Avatar --}}
                <div class="avatar-circle me-2"
                     style="width: {{ max(32 - $level * 2, 24) }}px;
                            height: {{ max(32 - $level * 2, 24) }}px;
                            font-size: {{ max(0.8 - $level * 0.05, 0.6) }}rem;">
                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                </div>

                {{-- User Info --}}
                <div>
                    <div class="d-flex align-items-center">
                        <strong class="comment-author">{{ $comment->user->name }}</strong>

                        {{-- Depth indicator for deep nesting --}}
                        @if(isset($comment->depth) && $comment->depth > 0)
                            <span class="badge bg-secondary ms-2" style="font-size: 0.6rem;">
                                Level {{ $comment->depth }}
                            </span>
                        @endif

                        {{-- OP Badge --}}
                        @if($comment->user_id === $comment->thread->user_id)
                            <span class="badge bg-primary ms-2" style="font-size: 0.6rem;">OP</span>
                        @endif
                    </div>

                    <div class="text-muted small">
                        {{ $comment->created_at->diffForHumans() }}
                        @if($comment->created_at != $comment->updated_at)
                            <span class="text-info">(edited)</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Comment Actions Dropdown --}}
            @if(Auth::check() && (Auth::id() == $comment->user_id || (method_exists(Auth::user(), 'isAdmin') && Auth::user()->isAdmin())))
                <div class="dropdown">
                    <button class="btn btn-sm text-muted" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if(Auth::id() == $comment->user_id)
                            <li>
                                <button type="button" class="dropdown-item edit-comment-btn"
                                        data-comment-id="{{ $comment->id }}"
                                        data-comment-body="{{ $comment->body }}">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </button>
                            </li>
                            <li>
                                <form action="{{ route('comments.destroy', $comment->id) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus komentar ini?{{ $comment->hasChildren() ? ' Semua balasan juga akan terhapus.' : '' }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-trash-alt me-1"></i> Hapus
                                        @if($comment->hasChildren())
                                            <small>(+{{ $comment->getTotalRepliesCount() }} balasan)</small>
                                        @endif
                                    </button>
                                </form>
                            </li>
                        @endif

                        {{-- Report Option --}}
                        @if(Auth::id() != $comment->user_id)
                            <li>
                                <button type="button" class="dropdown-item text-warning report-comment-btn"
                                        data-comment-id="{{ $comment->id }}">
                                    <i class="fas fa-flag me-1"></i> Laporkan
                                </button>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>

        {{-- Comment Body --}}
        <div class="comment-content mb-2">
            {{-- Show parent context for deep replies --}}
            @if(isset($comment->depth) && $comment->depth > 2 && $comment->parent)
                <div class="parent-context mb-2 p-2 bg-light rounded border-start border-3 border-info">
                    <small class="text-muted">
                        <i class="fas fa-reply me-1"></i>
                        Membalas <strong>{{ $comment->parent->user->name }}</strong>:
                    </small>
                    <div class="small text-truncate" style="max-height: 2em; overflow: hidden;">
                        {{ Str::limit($comment->parent->body, 100) }}
                    </div>
                </div>
            @endif

            <div class="comment-body" id="comment-body-{{ $comment->id }}">
                <p class="mb-1">{{ $comment->body }}</p>
            </div>

            {{-- Edit Form (Hidden by default) --}}
            <form id="edit-form-{{ $comment->id }}" class="d-none edit-comment-form"
                  action="{{ route('comments.update', $comment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <textarea name="body" class="form-control mb-2" rows="3" required>{{ $comment->body }}</textarea>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i>Simpan
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm cancel-edit"
                            data-comment-id="{{ $comment->id }}">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                </div>
            </form>
        </div>

        {{-- Comment Footer --}}
        <div class="comment-footer">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                {{-- Voting Section --}}
                @auth
                    <div class="d-flex align-items-center mb-2">
                        <form action="{{ route('vote.comment', $comment->id) }}" method="POST" class="me-1 vote-form">
                            @csrf
                            <input type="hidden" name="value" value="1">
                            <button type="submit" class="btn btn-sm btn-outline-success vote-btn upvote-btn"
                                    style="width: 28px; height: 28px; border-radius: 50%; padding: 0;"
                                    title="Suka">
                                <i class="fas fa-thumbs-up"></i>
                            </button>
                        </form>

                        <span class="vote-score me-2 fw-bold text-primary">{{ $comment->voteScore() }}</span>

                        <form action="{{ route('vote.comment', $comment->id) }}" method="POST" class="me-3 vote-form">
                            @csrf
                            <input type="hidden" name="value" value="-1">
                            <button type="submit" class="btn btn-sm btn-outline-danger vote-btn downvote-btn"
                                    style="width: 28px; height: 28px; border-radius: 50%; padding: 0;"
                                    title="Tidak Suka">
                                <i class="fas fa-thumbs-down"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="d-flex align-items-center mb-2">
                        <span class="text-muted me-3">
                            <i class="fas fa-thumbs-up me-1"></i>{{ $comment->voteScore() }}
                        </span>
                    </div>
                @endauth

                {{-- Reply Button --}}
                <div class="mb-2">
                    @auth
                        @php
                            $maxDepth = defined('App\Http\Controllers\CommentController::MAX_DEPTH') ?
                                       App\Http\Controllers\CommentController::MAX_DEPTH : 5;
                            $currentDepth = $comment->depth ?? $level;
                        @endphp

                        @if($currentDepth < $maxDepth)
                            <button type="button" class="btn btn-sm btn-outline-primary reply-btn"
                                    data-comment-id="{{ $comment->id }}"
                                    data-username="{{ $comment->user->name }}">
                                <i class="fas fa-reply me-1"></i>Balas
                            </button>
                        @else
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>Tingkat balasan maksimum tercapai
                            </small>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-sign-in-alt me-1"></i>Login untuk balas
                        </a>
                    @endauth
                </div>
            </div>

            {{-- Reply Count --}}
            @if($comment->hasChildren())
                <div class="text-muted small mb-2">
                    <i class="fas fa-comments me-1"></i>
                    {{ $comment->getTotalRepliesCount() }} balasan
                </div>
            @endif
        </div>

        {{-- Reply Form (Hidden by default) --}}
        @auth
            @php
                $maxDepth = defined('App\Http\Controllers\CommentController::MAX_DEPTH') ?
                           App\Http\Controllers\CommentController::MAX_DEPTH : 5;
                $currentDepth = $comment->depth ?? $level;
            @endphp

            @if($currentDepth < $maxDepth)
                <form action="{{ route('comments.store', $comment->thread) }}" method="POST"
                      class="reply-form d-none mt-3 mb-2" id="reply-form-{{ $comment->id }}">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                    <div class="d-flex align-items-start">
                        {{-- User Avatar --}}
                        <div class="avatar-circle me-2 flex-shrink-0" style="width: 28px; height: 28px; font-size: 0.7rem;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>

                        {{-- Reply Input --}}
                        <div class="flex-grow-1">
                            <div class="position-relative">
                                <textarea name="body" class="form-control comment-textarea reply-textarea"
                                          placeholder="Balas {{ $comment->user->name }}..."
                                          style="border-radius: 15px; resize: none; min-height: 60px;"
                                          rows="2" required></textarea>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Level {{ $currentDepth + 1 }} balasan (max: {{ $maxDepth }})
                                </small>
                                <div>
                                    <button type="button" class="btn btn-sm btn-light cancel-reply me-1"
                                            data-comment-id="{{ $comment->id }}">
                                        <i class="fas fa-times me-1"></i>Batal
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-primary submit-reply">
                                        <i class="fas fa-paper-plane me-1"></i>Kirim Balasan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            @endif
        @endauth

        {{-- Child Comments (Recursive) --}}
        @if ($comment->children && $comment->children->count())
            <div class="child-comments mt-3">
                {{-- Visual nesting indicator --}}
                <div class="position-relative">
                    @if($level < 3)
                        <div class="nesting-line position-absolute"
                             style="left: 10px; top: 0; bottom: 0; width: 2px;
                                    background: linear-gradient(to bottom, #dee2e6, transparent);"></div>
                    @endif

                    <div class="ms-3">
                        {{-- Increment level for nested comments --}}
                        @include('threads.partials.comments', [
                            'comments' => $comment->children,
                            'level' => $level + 1
                        ])
                    </div>
                </div>
            </div>
        @endif
    </div>
@endforeach

{{-- Styles --}}
@once
@push('styles')
<style>
.avatar-circle {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    flex-shrink: 0;
}

.comment-item {
    transition: all 0.2s ease;
    position: relative;
}

.comment-item:hover {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.comment-item:target {
    animation: highlight 2s ease-in-out;
}

@keyframes highlight {
    0% { background-color: #fff3cd; }
    100% { background-color: transparent; }
}

.parent-context {
    font-size: 0.85rem;
}

.nesting-line {
    opacity: 0.3;
}

.vote-btn {
    transition: all 0.15s ease;
}

.vote-btn:hover {
    transform: scale(1.1);
}

.reply-textarea {
    min-height: 38px;
}

.comment-body {
    word-wrap: break-word;
    white-space: pre-wrap;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .comment-item {
        margin-left: 0 !important;
    }

    .child-comments .ms-3 {
        margin-left: 1rem !important;
    }
}
</style>
@endpush
@endonce

{{-- JavaScript --}}
@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Comments JavaScript loaded');

    // Initialize tooltips if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // Enhanced Reply functionality
    function setupReplyButtons() {
        document.querySelectorAll('.reply-btn').forEach((button, index) => {
            // Remove existing listeners to prevent duplication
            button.removeEventListener('click', handleReplyClick);
            button.addEventListener('click', handleReplyClick);
        });
    }

    function handleReplyClick(e) {
        e.preventDefault();
        e.stopPropagation();

        const button = this;
        const commentId = button.dataset.commentId;
        const username = button.dataset.username;

        console.log('Reply button clicked for comment:', commentId);

        try {
            // Hide all other reply forms first
            hideAllForms();

            // Show target reply form
            const targetForm = document.getElementById('reply-form-' + commentId);

            if (targetForm) {
                targetForm.classList.remove('d-none');
                const textarea = targetForm.querySelector('textarea');

                if (textarea) {
                    // Update placeholder with username
                    textarea.placeholder = `Balas ${username}...`;

                    // Focus with delay to ensure form is visible
                    setTimeout(() => {
                        textarea.focus();
                        autoResizeTextarea(textarea);

                        // Smooth scroll to form
                        targetForm.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });
                    }, 100);
                }

                // Update button state
                button.innerHTML = '<i class="fas fa-times me-1"></i>Tutup';
                button.classList.replace('btn-outline-primary', 'btn-outline-secondary');

                console.log('Reply form shown for comment:', commentId);
            } else {
                console.error('Reply form not found for comment:', commentId);
                showAlert('Error: Reply form tidak ditemukan');
            }

        } catch (error) {
            console.error('Error in reply button click:', error);
            showAlert('Terjadi kesalahan: ' + error.message);
        }
    }

    // Hide all forms (reply and edit)
    function hideAllForms() {
        // Hide all reply forms
        document.querySelectorAll('.reply-form').forEach(form => {
            form.classList.add('d-none');
            const textarea = form.querySelector('textarea');
            if (textarea) {
                textarea.value = '';
            }
        });

        // Hide all edit forms
        document.querySelectorAll('.edit-comment-form').forEach(form => {
            form.classList.add('d-none');
            const formCommentId = form.id.replace('edit-form-', '');
            const commentBody = document.getElementById('comment-body-' + formCommentId);
            if (commentBody) {
                commentBody.classList.remove('d-none');
            }
        });

        // Reset all reply buttons
        document.querySelectorAll('.reply-btn').forEach(btn => {
            btn.innerHTML = '<i class="fas fa-reply me-1"></i>Balas';
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-outline-primary');
        });
    }

    // Cancel reply functionality
    function setupCancelButtons() {
        document.querySelectorAll('.cancel-reply').forEach(button => {
            button.removeEventListener('click', handleCancelReply);
            button.addEventListener('click', handleCancelReply);
        });
    }

    function handleCancelReply(e) {
        e.preventDefault();
        e.stopPropagation();

        const button = this;
        const commentId = button.dataset.commentId;

        try {
            const form = document.getElementById('reply-form-' + commentId);
            if (form) {
                form.classList.add('d-none');
                const textarea = form.querySelector('textarea');
                if (textarea) {
                    textarea.value = '';
                }

                // Reset reply button
                const replyBtn = document.querySelector(`[data-comment-id="${commentId}"].reply-btn`);
                if (replyBtn) {
                    replyBtn.innerHTML = '<i class="fas fa-reply me-1"></i>Balas';
                    replyBtn.classList.remove('btn-outline-secondary');
                    replyBtn.classList.add('btn-outline-primary');
                }

                console.log('Reply cancelled for comment:', commentId);
            }
        } catch (error) {
            console.error('Error in cancel reply:', error);
        }
    }

    // Form submission handling
    function setupFormSubmissions() {
        document.querySelectorAll('.reply-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('.submit-reply');
                const textarea = this.querySelector('textarea');

                if (textarea && textarea.value.trim().length < 5) {
                    e.preventDefault();
                    showAlert('Balasan minimal 5 karakter');
                    textarea.focus();
                    return false;
                }

                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Mengirim...';
                    submitBtn.disabled = true;
                }
            });
        });
    }

    // Auto-resize textarea function
    function autoResizeTextarea(textarea) {
        if (textarea) {
            textarea.style.height = 'auto';
            const newHeight = Math.min(Math.max(textarea.scrollHeight, 60), 200);
            textarea.style.height = newHeight + 'px';
        }
    }

    // Setup auto-resize for all textareas
    function setupTextareas() {
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', function() {
                autoResizeTextarea(this);
            });

            textarea.addEventListener('focus', function() {
                autoResizeTextarea(this);
            });

            // Set initial height
            autoResizeTextarea(textarea);
        });
    }

    // Enhanced voting with visual feedback
    function setupVoting() {
        document.querySelectorAll('.vote-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('.vote-btn');
                if (button) {
                    const originalContent = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    button.disabled = true;

                    // Re-enable button after delay if form submission fails
                    setTimeout(() => {
                        if (button.disabled) {
                            button.innerHTML = originalContent;
                            button.disabled = false;
                        }
                    }, 5000);
                }
            });
        });
    }

    // Edit functionality
    function setupEditButtons() {
        document.querySelectorAll('.edit-comment-btn').forEach(button => {
            button.removeEventListener('click', handleEditClick);
            button.addEventListener('click', handleEditClick);
        });
    }

    function handleEditClick(e) {
        e.preventDefault();
        e.stopPropagation();

        const button = this;
        const commentId = button.dataset.commentId;

        try {
            hideAllForms();

            const commentBody = document.getElementById('comment-body-' + commentId);
            const editForm = document.getElementById('edit-form-' + commentId);

            if (commentBody && editForm) {
                commentBody.classList.add('d-none');
                editForm.classList.remove('d-none');

                const textarea = editForm.querySelector('textarea');
                if (textarea) {
                    setTimeout(() => {
                        textarea.focus();
                        autoResizeTextarea(textarea);
                    }, 100);
                }
            }
        } catch (error) {
            console.error('Error in edit button click:', error);
        }
    }

    // Cancel edit functionality
    function setupCancelEditButtons() {
        document.querySelectorAll('.cancel-edit').forEach(button => {
            button.removeEventListener('click', handleCancelEdit);
            button.addEventListener('click', handleCancelEdit);
        });
    }

    function handleCancelEdit(e) {
        e.preventDefault();
        e.stopPropagation();

        const button = this;
        const commentId = button.dataset.commentId;

        try {
            const commentBody = document.getElementById('comment-body-' + commentId);
            const editForm = document.getElementById('edit-form-' + commentId);

            if (commentBody && editForm) {
                commentBody.classList.remove('d-none');
                editForm.classList.add('d-none');

                // Reset to original content
                const originalContent = button.closest('.comment-item').querySelector('.edit-comment-btn').dataset.commentBody;
                const textarea = editForm.querySelector('textarea');
                if (textarea && originalContent) {
                    textarea.value = originalContent;
                }
            }
        } catch (error) {
            console.error('Error in cancel edit:', error);
        }
    }

    // Alert helper function
    function showAlert(message, type = 'warning') {
        if (typeof bootstrap !== 'undefined') {
            // Use Bootstrap toast if available
            const toastHtml = `
                <div class="toast align-items-center text-white bg-${type === 'warning' ? 'warning' : 'primary'} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;

            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                document.body.appendChild(toastContainer);
            }

            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            const toast = new bootstrap.Toast(toastContainer.lastElementChild);
            toast.show();
        } else {
            alert(message);
        }
    }

    // Initialize all functionalities
    function initializeComments() {
        setupReplyButtons();
        setupCancelButtons();
        setupEditButtons();
        setupCancelEditButtons();
        setupTextareas();
        setupVoting();
        setupFormSubmissions();

        console.log('All comment functionalities initialized');
    }

    // Run initialization
    initializeComments();

    // Smooth scroll to comment when accessing via anchor
    if (window.location.hash) {
        const targetComment = document.querySelector(window.location.hash);
        if (targetComment) {
            setTimeout(() => {
                targetComment.scrollIntoView({ behavior: 'smooth', block: 'center' });
                targetComment.style.background = '#fff3cd';
                setTimeout(() => {
                    targetComment.style.background = '';
                }, 2000);
            }, 500);
        }
    }

    // Global escape key handler
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideAllForms();
        }
    });

    // Debug function
    window.debugComments = function() {
        console.log('=== COMMENT DEBUG INFO ===');
        console.log('Reply buttons:', document.querySelectorAll('.reply-btn').length);
        console.log('Reply forms:', document.querySelectorAll('.reply-form').length);
        console.log('Routes check:');
        console.log('- comments.store exists:', document.querySelector('form[action*="comments"]') !== null);
        console.log('- vote.comment exists:', document.querySelector('form[action*="vote/comment"]') !== null);
    };
});
</script>
@endpush
@endonce
