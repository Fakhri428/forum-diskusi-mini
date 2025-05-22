@extends('layouts.app')

@section('styles')
<style>
    body {
        background-color: #f0f2f5;
    }

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

    .comment-section {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
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

    .comment-form textarea {
        border-radius: 20px;
        padding: 15px;
        resize: none;
        border: 1px solid #e0e0e0;
    }

    .action-btn {
        border-radius: 20px;
        padding: 5px 15px;
    }

    /* Search styles */
    .search-container {
        margin-bottom: 2rem;
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .search-input {
        border-radius: 30px;
        padding: 0.75rem 1.25rem;
        border: 1px solid #e0e0e0;
        box-shadow: none;
        transition: all 0.3s;
    }

    .search-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(106, 17, 203, 0.15);
        border-color: #6a11cb;
    }

    .search-btn {
        border-radius: 30px;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        border: none;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .search-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }

    .filter-badge {
        cursor: pointer;
        background-color: #f0f2f5;
        color: #444;
        padding: 0.5rem 1rem;
        border-radius: 30px;
        margin-right: 0.5rem;
        transition: all 0.2s;
    }

    .filter-badge:hover, .filter-badge.active {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
    }

    .search-result-highlight {
        background-color: rgba(255, 215, 0, 0.3);
        padding: 0 2px;
        border-radius: 3px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Forum Diskusi - Disquseria</h1>
        <a href="{{ route('threads.create') }}" class="btn create-btn">
            <i class="fas fa-plus-circle me-2"></i>Buat Thread Baru
        </a>
    </div>

    <!-- Search Section -->
    <div class="search-container">
        <form action="{{ route('threads.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text border-0 bg-transparent">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control search-input"
                               placeholder="Cari thread berdasarkan judul, konten, atau nama pengguna..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn search-btn w-100">
                        <i class="fas fa-search me-2"></i>Cari
                    </button>
                </div>
            </div>

            <div class="mt-3">
                <div class="d-flex flex-wrap align-items-center">
                    <span class="me-2 text-muted">Filter:</span>

                    <!-- Category Filter -->
                    <select name="category" class="form-select me-2" style="width: auto; border-radius: 30px;">
                        <option value="">Semua Kategori</option>
                        @foreach(App\Models\Category::all() as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Tag Filter -->
                    <select name="tag" class="form-select me-2" style="width: auto; border-radius: 30px;">
                        <option value="">Semua Tag</option>
                        @foreach(App\Models\Tag::all() as $tag)
                            <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>
                                {{ $tag->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Sort Options -->
                    <select name="sort" class="form-select" style="width: auto; border-radius: 30px;">
                        <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                        <option value="comments" {{ request('sort') == 'comments' ? 'selected' : '' }}>Banyak Komentar</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <!-- Search Results Info -->
    @if(request('search') || request('category') || request('tag'))
        <div class="alert alert-info mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-search me-2"></i>
                    Menampilkan hasil pencarian
                    @if(request('search'))
                        untuk "<strong>{{ request('search') }}</strong>"
                    @endif
                    @if(request('category'))
                        dalam kategori "<strong>{{ App\Models\Category::find(request('category'))->name }}</strong>"
                    @endif
                    @if(request('tag'))
                        dengan tag "<strong>{{ App\Models\Tag::find(request('tag'))->name }}</strong>"
                    @endif
                    ({{ $threads->total() }} hasil)
                </div>
                <a href="{{ route('threads.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-times me-1"></i>Reset Filter
                </a>
            </div>
        </div>
    @endif

    @forelse ($threads as $thread)
        <div class="card thread-card mb-4">
            <div class="thread-header d-flex justify-content-between align-items-center">
                <div>
                    <div class="d-flex align-items-center">
                        <!-- Avatar placeholder -->
                        <div class="bg-light text-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                            style="width: 40px; height: 40px; font-weight: bold;">
                            {{ strtoupper(substr($thread->user->name, 0, 1)) }}
                        </div>
                        <strong>{{ $thread->user->name }}</strong>
                    </div>
                </div>
                <div>
                    <small><i class="far fa-clock me-1"></i>{{ $thread->created_at->diffForHumans() }}</small>
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

                <p class="thread-content">{{ $thread->body }}</p>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="d-flex align-items-center">
                        <form action="{{ route('vote.thread', $thread->id) }}" method="POST" class="me-1">
                            @csrf
                            <input type="hidden" name="value" value="1">
                            <button type="submit" class="btn btn-outline-success vote-btn" title="Upvote">👍</button>
                        </form>
                        <form action="{{ route('vote.thread', $thread->id) }}" method="POST" class="me-2">
                            @csrf
                            <input type="hidden" name="value" value="-1">
                            <button type="submit" class="btn btn-outline-danger vote-btn" title="Downvote">👎</button>
                        </form>
                        <span class="fw-bold">Skor: <span class="text-primary">{{ $thread->voteScore() }}</span></span>
                        <span class="ms-3 text-muted">
                            <i class="far fa-comment me-1"></i>{{ $thread->comments->count() }} komentar
                        </span>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('threads.show', $thread->id) }}" class="btn btn-info btn-sm action-btn">
                            <i class="fas fa-eye me-1"></i>Lihat
                        </a>

                        @if(Auth::check() && Auth::id() == $thread->user_id)
                            <a href="{{ route('threads.edit', $thread->id) }}" class="btn btn-primary btn-sm action-btn">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>

                            <form action="{{ route('threads.destroy', $thread->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus thread ini?');" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm action-btn">
                                    <i class="fas fa-trash-alt me-1"></i>Hapus
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Comment section moved to show page for cleanliness -->
                <div class="text-center mt-4">
                    <a href="{{ route('threads.show', $thread->id) }}" class="btn btn-outline-primary btn-sm action-btn">
                        <i class="fas fa-comments me-1"></i>Lihat {{ $thread->comments->count() }} Komentar
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info text-center p-5">
            <i class="fas fa-comments fa-3x mb-3"></i>
            <h4>Belum ada thread yang tersedia.</h4>
            <p>Jadilah yang pertama memulai diskusi!</p>
            <a href="{{ route('threads.create') }}" class="btn btn-primary mt-2">Buat Thread Baru</a>
        </div>
    @endforelse

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $threads->appends(request()->query())->links() }}
    </div>
</div>
@endsection
