<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/my-threads.blade.php -->
@extends('layouts.app')

@section('styles')
<style>
    .thread-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s;
        margin-bottom: 1.5rem;
    }

    .thread-card:hover {
        transform: translateY(-3px);
    }

    .thread-header {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 1rem;
    }

    .thread-title {
        font-weight: 600;
    }

    .tag-badge {
        transition: all 0.2s;
        cursor: pointer;
        margin-right: 5px;
    }

    .tag-badge:hover {
        transform: scale(1.05);
    }

    .category-badge {
        background-color: #4e54c8;
        color: white;
        font-weight: 500;
    }

    .vote-btn {
        border-radius: 50%;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        margin-right: 8px;
    }

    .action-btn {
        border-radius: 20px;
        padding: 5px 15px;
    }

    .page-title {
        color: #4e54c8;
        font-weight: 700;
        border-bottom: 3px solid #4e54c8;
        display: inline-block;
        padding-bottom: 5px;
    }

    .create-btn {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        padding: 8px 20px;
        border-radius: 30px;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .create-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }

    .status-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.35rem 0.65rem;
    }

    .tab-button {
        padding: 0.75rem 1.5rem;
        border-radius: 1.5rem;
        font-weight: 600;
        transition: all 0.3s;
        margin-right: 0.5rem;
    }

    .tab-button.active {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Diskusi Saya</h1>
        <a href="{{ route('threads.create') }}" class="btn create-btn">
            <i class="fas fa-plus-circle me-2"></i>Buat Thread Baru
        </a>
    </div>

    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="btn-group">
                <button type="button" class="btn tab-button active" data-tab="all">Semua</button>
                <button type="button" class="btn tab-button" data-tab="popular">Populer</button>
                <button type="button" class="btn tab-button" data-tab="oldest">Terlama</button>
            </div>

            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="threadStatus" checked>
                <label class="form-check-label" for="threadStatus">Tampilkan Thread Aktif</label>
            </div>
        </div>
    </div>

    <div class="thread-list">
        @forelse (Auth::user()->threads as $thread)
            <div class="card thread-card mb-4">
                <div class="thread-header d-flex justify-content-between align-items-center">
                    <div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-light text-dark me-2">Thread #{{ $thread->id }}</span>
                            <span class="text-white">{{ $thread->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                    <div>
                        <span class="badge {{ $thread->created_at->diffInDays() < 7 ? 'bg-success' : 'bg-secondary' }} status-badge">
                            {{ $thread->created_at->diffInDays() < 7 ? 'Baru' : 'Lama' }}
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    <h5 class="thread-title mb-3">{{ $thread->title }}</h5>

                    <div class="d-flex align-items-center mb-3">
                        @if ($thread->category)
                            <span class="badge category-badge me-2">
                                <i class="fas fa-folder me-1"></i>{{ $thread->category->name }}
                            </span>
                        @endif

                        @if ($thread->tags && $thread->tags->count() > 0)
                            @foreach ($thread->tags as $tag)
                                <span class="badge bg-secondary tag-badge">
                                    <i class="fas fa-tag me-1"></i>{{ $tag->name }}
                                </span>
                            @endforeach
                        @endif
                    </div>

                    <p class="thread-content">{{ Str::limit($thread->body, 150) }}</p>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="d-flex align-items-center">
                            <span class="me-3">
                                <i class="fas fa-thumbs-up me-1 text-success"></i>{{ $thread->voteScore() }} skor
                            </span>
                            <span class="me-3">
                                <i class="fas fa-eye me-1 text-primary"></i>230 dilihat
                            </span>
                            <span>
                                <i class="fas fa-comment-dots me-1 text-info"></i>{{ $thread->comments->count() }} komentar
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('threads.show', $thread->id) }}" class="btn btn-info btn-sm action-btn">
                                <i class="fas fa-eye me-1"></i>Lihat
                            </a>
                            <a href="{{ route('threads.edit', $thread->id) }}" class="btn btn-primary btn-sm action-btn">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <button type="button" class="btn btn-danger btn-sm action-btn" onclick="confirmDelete({{ $thread->id }})">
                                <i class="fas fa-trash-alt me-1"></i>Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center p-5 bg-light rounded-3">
                <div class="p-4">
                    <i class="fas fa-comment-slash fa-4x text-muted mb-4"></i>
                    <h4>Belum ada diskusi yang kamu buat</h4>
                    <p class="text-muted">Mulai berbagi ide dan pemikiran dengan komunitas</p>
                    <a href="{{ route('threads.create') }}" class="btn btn-primary mt-2">
                        <i class="fas fa-plus-circle me-2"></i>Buat Thread Baru
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteThreadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus thread ini?</p>
                    <p class="text-muted small">Tindakan ini tidak dapat dibatalkan dan semua komentar terkait akan ikut terhapus.</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteThreadForm" method="POST">
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
    function confirmDelete(threadId) {
        const form = document.getElementById('deleteThreadForm');
        form.action = `/threads/${threadId}`;
        const modal = new bootstrap.Modal(document.getElementById('deleteThreadModal'));
        modal.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const tabButtons = document.querySelectorAll('.tab-button');
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                tabButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Implement filtering logic here
                const tab = this.getAttribute('data-tab');
                console.log(`Filter threads by: ${tab}`);
                // You could use AJAX to load filtered threads or filter the existing ones with JS
            });
        });
    });
</script>
@endpush
