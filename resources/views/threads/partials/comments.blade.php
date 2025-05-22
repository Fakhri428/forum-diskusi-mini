@foreach ($comments as $comment)
    <div style="margin-left: {{ ($level ?? 0) * 20 }}px;" class="mb-3 p-2 border rounded bg-light">
        <strong>{{ $comment->user->name }}</strong>
        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
        <p class="mb-1">{{ $comment->body }}</p>

        {{-- Voting --}}
        <div class="d-flex align-items-center mb-2">
            <form action="{{ route('vote.comment', $comment->id) }}" method="POST" class="me-1">
                @csrf
                <input type="hidden" name="value" value="1">
                <button type="submit" class="btn btn-sm btn-outline-success">👍</button>
            </form>
            <form action="{{ route('vote.comment', $comment->id) }}" method="POST" class="me-2">
                @csrf
                <input type="hidden" name="value" value="-1">
                <button type="submit" class="btn btn-sm btn-outline-danger">👎</button>
            </form>
            <span>Skor: {{ $comment->voteScore() }}</span>
        </div>

        {{-- Tombol Balas --}}
        @auth
            <button type="button" class="btn btn-sm btn-link reply-btn" data-comment-id="{{ $comment->id }}">
                Balas
            </button>

            <form action="{{ route('comments.store', $comment->thread) }}" method="POST"
    class="reply-form d-none mt-2 mb-3 ajax-reply-form" id="reply-form-{{ $comment->id }}">
    @csrf
    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
    <textarea name="body" rows="2" class="form-control" placeholder="Tulis balasan..." required></textarea>
    <div class="mt-2 d-flex align-items-center gap-2">
        <button type="submit" class="btn btn-primary btn-sm">Kirim Balasan</button>
        <button type="button" class="btn btn-secondary btn-sm cancel-reply" data-comment-id="{{ $comment->id }}">Batal</button>
    </div>
</form>

        @endauth

        {{-- Anak Komentar (Recursive) --}}
        @if ($comment->children->count())
            @include('threads.partials.comments', [
                'comments' => $comment->children,
                'level' => ($level ?? 0) + 1,
            ])
        @endif
    </div>
@endforeach

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.reply-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.dataset.commentId;
                    const form = document.getElementById('reply-form-' + id);
                    if (form) form.classList.toggle('d-none');
                });
            });

            document.querySelectorAll('.cancel-reply').forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.dataset.commentId;
                    const form = document.getElementById('reply-form-' + id);
                    if (form) form.classList.add('d-none');
                });
            });
        });
    </script>
@endpush
