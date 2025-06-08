@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Thread Content would go here -->

    <!-- Discussion Section -->
    <div class="comment-section p-4 rounded-3 shadow-sm mt-4">
        <div class="d-flex align-items-center mb-4">
            <div class="comment-icon-container me-3">
                <div class="comment-icon-circle">
                    <i class="fas fa-comments"></i>
                </div>
            </div>
            <h4 class="mb-0 gradient-text">Diskusi ({{ $thread->comments->count() }})</h4>
        </div>

        @if($thread->comments->where('parent_id', null)->count() > 0)
            <div class="comments-container">
                @include('threads.partials.comments', [
                    'comments' => $thread->comments()->whereNull('parent_id')->with('user', 'votes', 'children.user', 'children.votes')->latest()->get()
                ])
            </div>
        @else
            <div class="empty-comments text-center py-5">
                <div class="empty-icon mb-3">
                    <i class="far fa-comments fa-3x text-muted"></i>
                </div>
                <p class="text-muted">Belum ada komentar dalam diskusi ini.</p>
                <p class="small">Jadilah yang pertama untuk berkomentar!</p>
            </div>
        @endif

        @auth
            <div class="new-comment-form mt-4">
                <div class="d-flex mb-3">
                    <div class="user-avatar me-3">
                        <div class="avatar-circle">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                    <h5 class="mb-0 pt-2">Tambahkan Komentar</h5>
                </div>

                <form action="{{ route('comments.store', $thread) }}" method="POST">
                    @csrf
                    <div class="mb-3 position-relative">
                        <textarea name="body" class="form-control comment-textarea"
                            placeholder="Bagikan pendapat Anda tentang diskusi ini..."
                            rows="3" required></textarea>
                        <div class="emoji-picker position-absolute bottom-0 end-0 p-2">
                            <button type="button" class="btn btn-sm btn-light rounded-circle border-0">
                                <i class="far fa-smile"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Komentar yang baik membantu diskusi menjadi lebih berkualitas
                        </div>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Komentar
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="auth-prompt mt-4 p-4 rounded-3 text-center bg-light border">
                <i class="fas fa-lock me-2 text-muted"></i>
                <span>Untuk berpartisipasi dalam diskusi, silahkan</span>
                <a href="{{ route('login') }}" class="btn btn-sm btn-primary mx-2">Login</a>
                <span>atau</span>
                <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary mx-2">Register</a>
            </div>
        @endauth
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Thread styles would go here */

    /* Comment Section Styles */
    .comment-section {
        background-color: #ffffff;
        border: 1px solid rgba(0,0,0,0.08);
    }

    .gradient-text {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    .comment-icon-container {
        position: relative;
    }

    .comment-icon-circle {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        box-shadow: 0 4px 10px rgba(106, 17, 203, 0.3);
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

    .comment-textarea {
        border-radius: 15px;
        padding: 15px;
        resize: none;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
        box-shadow: none;
    }

    .comment-textarea:focus {
        border-color: #6a11cb;
        box-shadow: 0 0 0 0.2rem rgba(106, 17, 203, 0.15);
    }

    .emoji-picker button {
        width: 36px;
        height: 36px;
        opacity: 0.7;
        transition: all 0.2s;
    }

    .emoji-picker button:hover {
        opacity: 1;
        background-color: #f0f2f5;
    }

    .empty-comments {
        border: 2px dashed rgba(106, 17, 203, 0.2);
        border-radius: 10px;
        background-color: rgba(106, 17, 203, 0.02);
    }

    .empty-icon {
        color: rgba(106, 17, 203, 0.4);
    }
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Reply animation functionality
        document.querySelectorAll('.reply-btn').forEach(button => {
            button.addEventListener('click', () => {
                // Hide all other reply forms first
                document.querySelectorAll('.reply-form').forEach(form => {
                    form.style.display = 'none';
                    form.classList.add('d-none');
                });

                // Hide all edit forms
                document.querySelectorAll('.edit-comment-form').forEach(form => {
                    form.style.display = 'none';
                    form.classList.add('d-none');
                    const commentId = form.id.replace('edit-form-', '');
                    document.getElementById('comment-body-' + commentId).classList.remove('d-none');
                });

                // Show this reply form with animation
                const id = button.dataset.commentId;
                const form = document.getElementById('reply-form-' + id);
                if (form) {
                    form.classList.remove('d-none');
                    form.style.display = 'block';
                    form.style.height = '0';
                    form.style.overflow = 'hidden';
                    form.style.transition = 'height 0.3s ease';
                    setTimeout(() => {
                        form.style.height = form.scrollHeight + 'px';
                        setTimeout(() => {
                            form.style.height = 'auto';
                            form.style.overflow = 'visible';
                            form.querySelector('textarea').focus();
                        }, 300);
                    }, 10);
                }
            });
        });

        // Cancel reply animation
        document.querySelectorAll('.cancel-reply').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.commentId;
                const form = document.getElementById('reply-form-' + id);
                if (form) {
                    // Slide up animation
                    form.style.height = form.scrollHeight + 'px';
                    form.style.overflow = 'hidden';
                    setTimeout(() => {
                        form.style.height = '0';
                        setTimeout(() => {
                            form.classList.add('d-none');
                            form.style = '';
                            form.querySelector('textarea').value = '';
                        }, 300);
                    }, 10);
                }
            });
        });

        // Edit comment animation
        document.querySelectorAll('.edit-comment-btn').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.commentId;
                const commentBody = document.getElementById('comment-body-' + id);
                const editForm = document.getElementById('edit-form-' + id);

                // Hide all reply forms first with animation
                document.querySelectorAll('.reply-form:not(.d-none)').forEach(form => {
                    form.style.height = form.scrollHeight + 'px';
                    form.style.overflow = 'hidden';
                    setTimeout(() => {
                        form.style.height = '0';
                        setTimeout(() => {
                            form.classList.add('d-none');
                            form.style = '';
                        }, 300);
                    }, 10);
                });

                // Hide all other edit forms
                document.querySelectorAll('.edit-comment-form').forEach(form => {
                    if (form.id !== 'edit-form-' + id && !form.classList.contains('d-none')) {
                        const otherCommentId = form.id.replace('edit-form-', '');
                        document.getElementById('comment-body-' + otherCommentId).classList.remove('d-none');
                        form.classList.add('d-none');
                    }
                });

                if (commentBody && editForm) {
                    // Fade out comment body
                    commentBody.style.opacity = '1';
                    commentBody.style.transition = 'opacity 0.2s ease';
                    setTimeout(() => {
                        commentBody.style.opacity = '0';
                        setTimeout(() => {
                            commentBody.classList.add('d-none');

                            // Fade in edit form
                            editForm.classList.remove('d-none');
                            editForm.style.opacity = '0';
                            editForm.style.transition = 'opacity 0.2s ease';
                            setTimeout(() => {
                                editForm.style.opacity = '1';
                                editForm.querySelector('textarea').focus();
                            }, 10);
                        }, 200);
                    }, 10);
                }
            });
        });

        // Cancel edit animation
        document.querySelectorAll('.cancel-edit').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.commentId;
                const commentBody = document.getElementById('comment-body-' + id);
                const editForm = document.getElementById('edit-form-' + id);

                if (commentBody && editForm) {
                    // Fade out edit form
                    editForm.style.opacity = '1';
                    editForm.style.transition = 'opacity 0.2s ease';
                    setTimeout(() => {
                        editForm.style.opacity = '0';
                        setTimeout(() => {
                            editForm.classList.add('d-none');
                            // Reset to original content
                            editForm.querySelector('textarea').value = commentBody.textContent.trim();

                            // Fade in comment body
                            commentBody.classList.remove('d-none');
                            commentBody.style.opacity = '0';
                            commentBody.style.transition = 'opacity 0.2s ease';
                            setTimeout(() => {
                                commentBody.style.opacity = '1';
                            }, 10);
                        }, 200);
                    }, 10);
                }
            });
        });

        // Auto-resize textarea functionality
        const autoResizeTextarea = function(textarea) {
            textarea.style.height = 'auto';
            const newHeight = Math.max(textarea.scrollHeight, 80); // Minimum height
            textarea.style.height = newHeight + 'px';
        };

        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', function() {
                autoResizeTextarea(this);
            });

            // Initialize textarea height
            textarea.addEventListener('focus', function() {
                autoResizeTextarea(this);
            });
        });

        // Button hover effect
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('mouseenter', function() {
                if (!this.classList.contains('btn-link')) {
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
                    this.style.transition = 'all 0.3s ease';
                }
            });

            button.addEventListener('mouseleave', function() {
                if (!this.classList.contains('btn-link')) {
                    this.style.transform = '';
                    this.style.boxShadow = '';
                }
            });
        });
    });
</script>
@endpush
