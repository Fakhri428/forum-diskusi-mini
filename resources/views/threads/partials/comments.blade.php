@foreach ($comments as $comment)
    <div style="margin-left: {{ ($level ?? 0) * 20 }}px;" class="comment-item mb-3 p-3 border rounded bg-light">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                    style="width: 32px; height: 32px; font-weight: bold; font-size: 0.8rem; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%) !important;">
                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                </div>
                <div>
                    <strong>{{ $comment->user->name }}</strong>
                    <div class="text-muted small">{{ $comment->created_at->diffForHumans() }}</div>
                </div>
            </div>

            @if(Auth::check() && Auth::id() == $comment->user_id)
                <div class="dropdown">
                    <button class="btn btn-sm text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <button type="button" class="dropdown-item edit-comment-btn"
                                data-comment-id="{{ $comment->id }}"
                                data-comment-body="{{ $comment->body }}">
                                <i class="fas fa-edit me-1"></i> Edit
                            </button>
                        </li>
                        <li>
                            <form action="{{ route('comments.destroy', $comment->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus komentar ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-trash-alt me-1"></i> Hapus
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endif
        </div>

        <div class="comment-content mb-2">
            <p class="mb-1" id="comment-body-{{ $comment->id }}">{{ $comment->body }}</p>

            <!-- Edit form (hidden by default) -->
            <form id="edit-form-{{ $comment->id }}" class="d-none edit-comment-form"
                action="{{ route('comments.update', $comment->id) }}" method="POST">
                @csrf
                @method('PUT')
                <textarea name="body" class="form-control mb-2" rows="2" required>{{ $comment->body }}</textarea>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                    <button type="button" class="btn btn-secondary btn-sm cancel-edit" data-comment-id="{{ $comment->id }}">Batal</button>
                </div>
            </form>
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap">
            {{-- Voting --}}
            <div class="d-flex align-items-center mb-2">
                <form action="{{ route('vote.comment', $comment->id) }}" method="POST" class="me-1 vote-form">
                    @csrf
                    <input type="hidden" name="value" value="1">
                    <button type="submit" class="btn btn-sm btn-outline-success vote-btn" style="width: 32px; height: 32px; border-radius: 50%; padding: 0;" title="Suka">
                        <i class="fas fa-thumbs-up"></i>
                    </button>
                </form>
                <form action="{{ route('vote.comment', $comment->id) }}" method="POST" class="me-2 vote-form">
                    @csrf
                    <input type="hidden" name="value" value="-1">
                    <button type="submit" class="btn btn-sm btn-outline-danger vote-btn" style="width: 32px; height: 32px; border-radius: 50%; padding: 0;" title="Tidak Suka">
                        <i class="fas fa-thumbs-down"></i>
                    </button>
                </form>
                <span class="me-3">Skor: <strong class="text-primary">{{ $comment->voteScore() }}</strong></span>
            </div>

            {{-- Tombol Balas --}}
            <div class="mb-2">
                @auth
                    <button type="button" class="btn btn-sm btn-outline-primary reply-btn" data-comment-id="{{ $comment->id }}">
                        <i class="fas fa-reply me-1"></i>Balas
                    </button>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-sign-in-alt me-1"></i>Login untuk balas
                    </a>
                @endauth
            </div>
        </div>

        {{-- Form Balasan (hidden by default) --}}
        @auth
            <form action="{{ route('comments.store', $comment->thread) }}" method="POST"
                class="reply-form d-none mt-2 mb-3" id="reply-form-{{ $comment->id }}">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <div class="position-relative">
                    <textarea name="body" class="form-control comment-textarea"
                        placeholder="Balasan untuk {{ $comment->user->name }}..."
                        style="padding-right: 60px; border-radius: 15px;" rows="2" required></textarea>
                    <div class="position-absolute bottom-0 end-0 p-2">
                        <button type="submit" class="btn btn-sm btn-primary rounded-circle" title="Kirim">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
                <div class="text-end mt-1">
                    <button type="button" class="btn btn-sm btn-light cancel-reply" data-comment-id="{{ $comment->id }}">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                </div>
            </form>
        @endauth

        {{-- Child Comments (Recursive) --}}
        @if ($comment->children && $comment->children->count())
            <div class="child-comments mt-3 ps-2 border-start border-2" style="border-color: rgba(106, 17, 203, 0.2) !important;">
                @include('threads.partials.comments', [
                    'comments' => $comment->children,
                    'level' => ($level ?? 0) + 1,
                ])
            </div>
        @endif
    </div>
@endforeach

@once
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Reply functionality
        document.querySelectorAll('.reply-btn').forEach(button => {
            button.addEventListener('click', () => {
                // Hide all other reply forms first
                document.querySelectorAll('.reply-form').forEach(form => {
                    form.classList.add('d-none');
                });

                // Hide all edit forms
                document.querySelectorAll('.edit-comment-form').forEach(form => {
                    form.classList.add('d-none');
                    const commentId = form.id.replace('edit-form-', '');
                    document.getElementById('comment-body-' + commentId).classList.remove('d-none');
                });

                // Show this reply form
                const id = button.dataset.commentId;
                const form = document.getElementById('reply-form-' + id);
                if (form) {
                    form.classList.remove('d-none');
                    form.querySelector('textarea').focus();
                }
            });
        });

        document.querySelectorAll('.cancel-reply').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.commentId;
                const form = document.getElementById('reply-form-' + id);
                if (form) {
                    form.classList.add('d-none');
                    form.querySelector('textarea').value = '';
                }
            });
        });

        // Edit functionality
        document.querySelectorAll('.edit-comment-btn').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.commentId;
                const commentBody = document.getElementById('comment-body-' + id);
                const editForm = document.getElementById('edit-form-' + id);

                // Hide all reply forms first
                document.querySelectorAll('.reply-form').forEach(form => {
                    form.classList.add('d-none');
                });

                // Hide all other edit forms
                document.querySelectorAll('.edit-comment-form').forEach(form => {
                    if (form.id !== 'edit-form-' + id) {
                        form.classList.add('d-none');
                        const otherCommentId = form.id.replace('edit-form-', '');
                        document.getElementById('comment-body-' + otherCommentId).classList.remove('d-none');
                    }
                });

                if (commentBody && editForm) {
                    commentBody.classList.add('d-none');
                    editForm.classList.remove('d-none');
                    editForm.querySelector('textarea').focus();
                }
            });
        });

        document.querySelectorAll('.cancel-edit').forEach(button => {
            button.addEventListener('click', () => {
                const id = button.dataset.commentId;
                const commentBody = document.getElementById('comment-body-' + id);
                const editForm = document.getElementById('edit-form-' + id);

                if (commentBody && editForm) {
                    commentBody.classList.remove('d-none');
                    editForm.classList.add('d-none');
                    // Reset to original content
                    editForm.querySelector('textarea').value = commentBody.textContent.trim();
                }
            });
        });

        // Enhance voting with AJAX (optional)
        document.querySelectorAll('.vote-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                // Uncomment the following lines if you want to implement AJAX voting later
                /*
                e.preventDefault();

                fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        value: this.querySelector('input[name="value"]').value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update the score without page reload
                        const commentId = this.action.split('/').pop();
                        const scoreEl = this.closest('.comment-item').querySelector('.text-primary');
                        scoreEl.textContent = data.newScore;
                    }
                });
                */
            });
        });

        // Auto-resize textarea
        const autoResizeTextarea = function(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
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
    });
</script>
@endpush
@endonce
