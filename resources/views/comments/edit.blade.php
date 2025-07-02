{{-- filepath: resources/views/comments/edit.blade.php --}}

@extends('layouts.app')

@section('title', 'Edit Komentar')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Edit Komentar
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Thread Info -->
                    <div class="alert alert-info d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>
                            <strong>Thread:</strong>
                            <a href="{{ route('threads.show', $comment->thread) }}"
                               target="_blank"
                               class="text-decoration-none fw-bold">
                                {{ $comment->thread->title }}
                            </a>
                        </div>
                    </div>

                    <!-- Comment Info -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                 style="width: 32px; height: 32px; font-weight: bold; font-size: 0.8rem;">
                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <strong>{{ $comment->user->name }}</strong>
                                <div class="text-muted small">
                                    Dibuat: {{ $comment->created_at->format('d M Y H:i') }}
                                    @if($comment->updated_at != $comment->created_at)
                                        | Diupdate: {{ $comment->updated_at->format('d M Y H:i') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Form -->
                    <form action="{{ route('comments.update', $comment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="body" class="form-label">
                                Komentar <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('body') is-invalid @enderror"
                                      id="body"
                                      name="body"
                                      rows="6"
                                      placeholder="Tulis komentar Anda..."
                                      required>{{ old('body', $comment->body) }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Minimal 5 karakter, maksimal 1000 karakter.
                            </div>
                        </div>

                        <!-- Parent Comment Info (if reply) -->
                        @if($comment->parent)
                            <div class="mb-3">
                                <label class="form-label">Membalas komentar:</label>
                                <div class="border p-3 rounded bg-light">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                             style="width: 24px; height: 24px; font-weight: bold; font-size: 0.7rem;">
                                            {{ strtoupper(substr($comment->parent->user->name, 0, 1)) }}
                                        </div>
                                        <strong class="small">{{ $comment->parent->user->name }}</strong>
                                    </div>
                                    <p class="mb-0 small text-muted">
                                        {{ Str::limit($comment->parent->body, 100) }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('threads.show', $comment->thread) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Thread
                                </a>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-secondary me-2" onclick="history.back()">
                                    <i class="fas fa-times me-1"></i>Batal
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Update Komentar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Comment Preview (Optional) -->
            <div class="card shadow mt-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-eye me-2"></i>Preview Komentar Saat Ini
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                             style="width: 40px; height: 40px; font-weight: bold;">
                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong>{{ $comment->user->name }}</strong>
                                    <div class="text-muted small">{{ $comment->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div id="comment-preview" class="mb-2">
                                {{ $comment->body }}
                            </div>
                            <div class="d-flex align-items-center text-muted small">
                                <span class="me-3">
                                    <i class="fas fa-thumbs-up me-1"></i>Skor: {{ $comment->voteScore() }}
                                </span>
                                @if($comment->children && $comment->children->count() > 0)
                                    <span>
                                        <i class="fas fa-reply me-1"></i>{{ $comment->children->count() }} balasan
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('body');
        const preview = document.getElementById('comment-preview');

        // Auto-resize textarea
        function autoResize() {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        // Update preview as user types
        function updatePreview() {
            const content = textarea.value.trim();
            preview.textContent = content || '(Preview akan muncul di sini)';
        }

        // Initialize
        autoResize();

        // Event listeners
        textarea.addEventListener('input', function() {
            autoResize();
            updatePreview();
        });

        textarea.addEventListener('focus', autoResize);

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const content = textarea.value.trim();
            if (content.length < 5) {
                e.preventDefault();
                alert('Komentar minimal 5 karakter.');
                textarea.focus();
                return false;
            }
            if (content.length > 1000) {
                e.preventDefault();
                alert('Komentar maksimal 1000 karakter.');
                textarea.focus();
                return false;
            }
        });

        // Character counter
        const charCounter = document.createElement('div');
        charCounter.className = 'form-text text-end';
        charCounter.id = 'char-counter';
        textarea.parentNode.appendChild(charCounter);

        function updateCharCounter() {
            const length = textarea.value.length;
            charCounter.textContent = `${length}/1000 karakter`;
            charCounter.className = length > 1000 ? 'form-text text-end text-danger' : 'form-text text-end text-muted';
        }

        textarea.addEventListener('input', updateCharCounter);
        updateCharCounter(); // Initialize
    });
</script>
@endpush
