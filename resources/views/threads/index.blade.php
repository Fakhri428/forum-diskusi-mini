@extends('layouts.app')

@section('title', 'Forum Diskusi - Disquseria')

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
        background: white;
    }

    .thread-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    }

    .thread-header {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 1rem;
    }

    .thread-title {
        font-weight: 600;
        color: #2d3748;
        text-decoration: none;
    }

    .thread-title:hover {
        color: #667eea;
        text-decoration: none;
    }

    .tag-badge {
        transition: all 0.2s;
        cursor: pointer;
        margin-right: 5px;
        margin-bottom: 5px;
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
        transition: all 0.2s;
    }

    .vote-btn:hover {
        transform: scale(1.1);
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
        padding: 10px 24px;
        border-radius: 30px;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        color: white;
        text-decoration: none;
        transition: all 0.3s;
    }

    .create-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        color: white;
    }

    .action-btn {
        border-radius: 20px;
        padding: 6px 16px;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .action-btn:hover {
        transform: translateY(-1px);
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
        transition: all 0.3s;
    }

    .search-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }

    .filter-select {
        border-radius: 30px;
        border: 1px solid #e0e0e0;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        min-width: 150px;
    }

    .filter-select:focus {
        border-color: #6a11cb;
        box-shadow: 0 0 0 0.2rem rgba(106, 17, 203, 0.15);
    }

    .search-result-highlight {
        background-color: rgba(255, 215, 0, 0.3);
        padding: 0 2px;
        border-radius: 3px;
    }

    .thread-content {
        color: #4a5568;
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    .thread-meta {
        font-size: 0.875rem;
        color: #718096;
    }

    .vote-score {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .stats-badge {
        background-color: #f7fafc;
        color: #4a5568;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.875rem;
        border: 1px solid #e2e8f0;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
    }

    .empty-state {
        background: white;
        border-radius: 12px;
        padding: 3rem;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .pagination-container {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-top: 2rem;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title mb-0">Forum Diskusi - Disquseria</h1>
        @auth
            <a href="{{ route('threads.create') }}" class="btn create-btn">
                <i class="fas fa-plus-circle me-2"></i>Buat Thread Baru
            </a>
        @else
            <a href="{{ route('login') }}" class="btn create-btn">
                <i class="fas fa-sign-in-alt me-2"></i>Login untuk Diskusi
            </a>
        @endauth
    </div>

    <!-- Search and Filter Section -->
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
                        <i class="fas fa-search me-2"></i>Cari Thread
                    </button>
                </div>
            </div>

            <!-- Filter Options -->
            <div class="mt-3">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <span class="text-muted fw-medium">Filter:</span>
                    </div>

                    <!-- Category Filter -->
                    <div class="col-auto">
                        <select name="category" class="form-select filter-select">
                            <option value="">Semua Kategori</option>
                            @if(isset($categories) && $categories->count() > 0)
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Sort Options -->
                    <div class="col-auto">
                        <select name="sort" class="form-select filter-select">
                            <option value="latest" {{ request('sort') == 'latest' || !request('sort') ? 'selected' : '' }}>
                                Terbaru
                            </option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                                Terlama
                            </option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>
                                Terpopuler
                            </option>
                            <option value="comments" {{ request('sort') == 'comments' ? 'selected' : '' }}>
                                Banyak Komentar
                            </option>
                        </select>
                    </div>

                    @if(request()->hasAny(['search', 'category', 'sort']))
                        <div class="col-auto">
                            <a href="{{ route('threads.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-times me-1"></i>Reset
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Search Results Info -->
    @if(request('search') || request('category'))
        <div class="alert alert-info mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-search me-2"></i>
                    Menampilkan hasil pencarian
                    @if(request('search'))
                        untuk "<strong>{{ request('search') }}</strong>"
                    @endif
                    @if(request('category') && isset($categories))
                        @php
                            $selectedCategory = $categories->find(request('category'));
                        @endphp
                        @if($selectedCategory)
                            dalam kategori "<strong>{{ $selectedCategory->name }}</strong>"
                        @endif
                    @endif
                    ({{ $threads->total() }} hasil)
                </div>
                <a href="{{ route('threads.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-times me-1"></i>Reset Filter
                </a>
            </div>
        </div>
    @endif

    <!-- Threads List -->
    @forelse ($threads as $thread)
        <div class="card thread-card">
            <!-- Thread Header -->
            <div class="thread-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="user-avatar me-3">
                        {{ strtoupper(substr($thread->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <strong>{{ $thread->user->name }}</strong>
                        <div class="small opacity-75">
                            <i class="far fa-clock me-1"></i>{{ $thread->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>

                @if($thread->is_pinned)
                    <span class="badge bg-warning">
                        <i class="fas fa-thumbtack me-1"></i>Pinned
                    </span>
                @endif
            </div>

            <!-- Thread Content -->
            <div class="card-body">
                <!-- Thread Title -->
                <h5 class="mb-3">
                    <a href="{{ route('threads.show', $thread) }}" class="thread-title">
                        {{ $thread->title }}
                    </a>
                    @if($thread->is_locked)
                        <span class="badge bg-secondary ms-2">
                            <i class="fas fa-lock me-1"></i>Locked
                        </span>
                    @endif
                </h5>

                <!-- Category and Tags -->
                <div class="d-flex flex-wrap align-items-center mb-3">
                    @if($thread->category)
                        <span class="badge category-badge me-2">
                            <i class="fas fa-folder me-1"></i>{{ $thread->category->name }}
                        </span>
                    @endif

                    @if($thread->tags)
                        @foreach(explode(',', $thread->tags) as $tag)
                            @if(trim($tag))
                                <span class="badge bg-secondary tag-badge">
                                    <i class="fas fa-tag me-1"></i>{{ trim($tag) }}
                                </span>
                            @endif
                        @endforeach
                    @endif
                </div>

                <!-- Thread Content Preview -->
                <div class="thread-content">
                    {{ Str::limit(strip_tags($thread->body), 200, '...') }}
                </div>

                <!-- Thread Stats and Actions -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <!-- Left Side: Voting and Stats -->
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        @auth
                            <!-- Vote Buttons -->
                            <div class="d-flex align-items-center me-3">
                                <form action="{{ route('vote.thread', $thread) }}" method="POST" class="me-1">
                                    @csrf
                                    <input type="hidden" name="value" value="1">
                                    <button type="submit" class="btn btn-outline-success vote-btn" title="Upvote">
                                        <i class="fas fa-arrow-up"></i>
                                    </button>
                                </form>

                                <span class="vote-score mx-2">{{ $thread->vote_score ?? 0 }}</span>

                                <form action="{{ route('vote.thread', $thread) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="value" value="-1">
                                    <button type="submit" class="btn btn-outline-danger vote-btn" title="Downvote">
                                        <i class="fas fa-arrow-down"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="d-flex align-items-center me-3">
                                <span class="vote-score">{{ $thread->vote_score ?? 0 }} votes</span>
                            </div>
                        @endauth

                        <!-- Thread Stats -->
                        <div class="d-flex align-items-center gap-2">
                            <span class="stats-badge">
                                <i class="far fa-comment me-1"></i>
                                {{ $thread->comments()->count() }} komentar
                            </span>

                            @if($thread->views_count > 0)
                                <span class="stats-badge">
                                    <i class="fas fa-eye me-1"></i>
                                    {{ $thread->views_count }} views
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Right Side: Action Buttons -->
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('threads.show', $thread) }}" class="btn btn-info btn-sm action-btn">
                            <i class="fas fa-eye me-1"></i>Lihat
                        </a>

                        @auth
                            @if(Auth::id() == $thread->user_id)
                                <a href="{{ route('threads.edit', $thread) }}" class="btn btn-primary btn-sm action-btn">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>

                                <form action="{{ route('threads.destroy', $thread) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus thread ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm action-btn">
                                        <i class="fas fa-trash-alt me-1"></i>Hapus
                                    </button>
                                </form>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Quick Comment Link -->
                <div class="text-center mt-3 pt-3 border-top">
                    <a href="{{ route('threads.show', $thread) }}#comments" class="btn btn-outline-primary btn-sm action-btn">
                        <i class="fas fa-comments me-1"></i>
                        Lihat Diskusi ({{ $thread->comments()->count() }} komentar)
                    </a>
                </div>
            </div>
        </div>
    @empty
        <!-- Empty State -->
        <div class="empty-state">
            <div class="mb-4">
                <i class="fas fa-comments fa-4x text-muted"></i>
            </div>
            <h4 class="text-muted mb-3">
                @if(request()->hasAny(['search', 'category']))
                    Tidak Ada Thread yang Ditemukan
                @else
                    Belum Ada Thread
                @endif
            </h4>
            <p class="text-muted mb-4">
                @if(request()->hasAny(['search', 'category']))
                    Coba ubah kata kunci pencarian atau filter yang Anda gunakan.
                @else
                    Jadilah yang pertama memulai diskusi di komunitas ini!
                @endif
            </p>

            @if(request()->hasAny(['search', 'category']))
                <a href="{{ route('threads.index') }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-refresh me-2"></i>Reset Filter
                </a>
            @endif

            @auth
                <a href="{{ route('threads.create') }}" class="btn create-btn">
                    <i class="fas fa-plus-circle me-2"></i>Buat Thread Baru
                </a>
            @else
                <a href="{{ route('login') }}" class="btn create-btn">
                    <i class="fas fa-sign-in-alt me-2"></i>Login untuk Membuat Thread
                </a>
            @endauth
        </div>
    @endforelse

    <!-- Pagination -->
    @if($threads->hasPages())
        <div class="pagination-container">
            <div class="d-flex justify-content-center">
                {{ $threads->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form when filters change
    $('.filter-select').on('change', function() {
        $(this).closest('form').submit();
    });

    // Highlight search terms
    @if(request('search'))
        const searchTerm = '{{ request('search') }}';
        $('.thread-content, .thread-title').each(function() {
            const text = $(this).html();
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            const highlighted = text.replace(regex, '<span class="search-result-highlight">$1</span>');
            $(this).html(highlighted);
        });
    @endif

    // Smooth scroll to comments
    $('a[href*="#comments"]').on('click', function(e) {
        // Let the default navigation happen, smooth scroll will be handled on the target page
    });

    // Vote button loading state
    $('.vote-btn').on('click', function() {
        const btn = $(this);
        const originalHtml = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i>');
        btn.prop('disabled', true);

        // Re-enable after form submission
        setTimeout(function() {
            btn.html(originalHtml);
            btn.prop('disabled', false);
        }, 2000);
    });
});
</script>
@endsection
