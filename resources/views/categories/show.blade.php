{{-- filepath: resources/views/categories/show.blade.php --}}

@extends('layouts.app')

@section('title', $category->name . ' - Kategori')

@section('content')
<div class="container py-4">
    <!-- Category Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0" style="background: linear-gradient(135deg, {{ $category->color ?? '#667eea' }} 0%, {{ $category->color ?? '#764ba2' }} 100%);">
                <div class="card-body text-white py-5">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center me-4"
                                     style="width: 80px; height: 80px;">
                                    @if($category->icon)
                                        <i class="{{ $category->icon }} fa-2x text-white"></i>
                                    @else
                                        <i class="fas fa-folder fa-2x text-white"></i>
                                    @endif
                                </div>
                                <div>
                                    <h1 class="display-6 fw-bold mb-2">{{ $category->name }}</h1>
                                    <p class="lead mb-0 opacity-90">{{ $category->description ?? 'Kategori diskusi ' . $category->name }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="bg-white bg-opacity-20 rounded-3 p-3 d-inline-block">
                                <div class="h3 mb-1 fw-bold">{{ $threads->total() }}</div>
                                <div class="small opacity-90">Total Thread</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('categories.show', $category) }}" method="GET" class="row g-3">
                        <!-- Search -->
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search"
                                       value="{{ request('search') }}"
                                       placeholder="Cari thread...">
                            </div>
                        </div>

                        <!-- Tag Filter -->
                        <div class="col-md-3">
                            <select name="tag" class="form-select">
                                <option value="">Semua Tag</option>
                                @foreach($popularTags as $tag)
                                    <option value="{{ $tag }}" {{ request('tag') == $tag ? 'selected' : '' }}>
                                        {{ $tag }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sort -->
                        <div class="col-md-3">
                            <select name="sort" class="form-select">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                                <option value="trending" {{ request('sort') == 'trending' ? 'selected' : '' }}>Trending</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            </select>
                        </div>

                        <!-- Submit -->
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Thread List -->
    <div class="row">
        <div class="col-lg-8">
            @forelse($threads as $thread)
                <div class="card mb-3 thread-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                                <h5 class="card-title mb-2">
                                    <a href="{{ route('threads.show', $thread) }}" class="text-decoration-none">
                                        {{ $thread->title }}
                                    </a>
                                    @if($thread->is_pinned)
                                        <span class="badge bg-warning ms-2">
                                            <i class="fas fa-thumbtack me-1"></i>Pinned
                                        </span>
                                    @endif
                                    @if($thread->is_locked)
                                        <span class="badge bg-secondary ms-2">
                                            <i class="fas fa-lock me-1"></i>Locked
                                        </span>
                                    @endif
                                </h5>

                                <p class="card-text text-muted mb-2">
                                    {{ Str::limit(strip_tags($thread->body), 150) }}
                                </p>

                                <!-- Thread Meta -->
                                <div class="d-flex flex-wrap align-items-center text-muted small">
                                    <span class="me-3">
                                        <i class="fas fa-user-circle me-1"></i>{{ $thread->user->name }}
                                    </span>
                                    <span class="me-3">
                                        <i class="far fa-clock me-1"></i>{{ $thread->created_at->diffForHumans() }}
                                    </span>
                                    @if($thread->views_count > 0)
                                        <span class="me-3">
                                            <i class="fas fa-eye me-1"></i>{{ $thread->views_count }} views
                                        </span>
                                    @endif
                                    <span class="me-3">
                                        <i class="fas fa-comments me-1"></i>{{ $thread->comments()->count() }} komentar
                                    </span>
                                </div>

                                <!-- Tags -->
                                @if($thread->tags)
                                    <div class="mt-2">
                                        @foreach($thread->formatted_tags as $tag)
                                            <a href="{{ route('categories.show', [$category, 'tag' => $tag]) }}"
                                               class="badge bg-light text-dark text-decoration-none me-1">
                                                {{ $tag }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="col-md-3 text-md-end">
                                <!-- Vote Score -->
                                @if($thread->vote_score != 0)
                                    <div class="mb-2">
                                        <span class="badge {{ $thread->vote_score > 0 ? 'bg-success' : 'bg-danger' }} fs-6">
                                            <i class="fas fa-arrow-{{ $thread->vote_score > 0 ? 'up' : 'down' }} me-1"></i>
                                            {{ abs($thread->vote_score) }}
                                        </span>
                                    </div>
                                @endif

                                <!-- Thread Image -->
                                @if($thread->image)
                                    <img src="{{ asset('storage/' . $thread->image) }}"
                                         alt="Thread image"
                                         class="img-fluid rounded"
                                         style="max-height: 80px; max-width: 100px;">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-comments fa-4x text-muted"></i>
                    </div>
                    <h4 class="text-muted mb-3">Belum Ada Thread</h4>
                    @if(request('search') || request('tag'))
                        <p class="text-muted mb-3">Tidak ada thread yang sesuai dengan filter yang dipilih.</p>
                        <a href="{{ route('categories.show', $category) }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-refresh me-2"></i>Reset Filter
                        </a>
                    @else
                        <p class="text-muted mb-3">Jadilah yang pertama untuk memulai diskusi di kategori ini!</p>
                    @endif
                    @auth
                        <a href="{{ route('threads.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Buat Thread Baru
                        </a>
                    @endauth
                </div>
            @endforelse

            <!-- Pagination -->
            @if($threads->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $threads->links() }}
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Category Info -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Tentang Kategori
                    </h6>
                </div>
                <div class="card-body">
                    @if($category->description)
                        <p class="card-text">{{ $category->description }}</p>
                    @endif
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="h5 mb-1 text-primary">{{ $threads->total() }}</div>
                                <small class="text-muted">Thread</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h5 mb-1 text-success">
                                {{ \App\Models\Comment::whereHas('thread', function($q) use ($category) {
                                    $q->where('category_id', $category->id);
                                })->count() }}
                            </div>
                            <small class="text-muted">Komentar</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popular Tags -->
            @if($popularTags->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-tags me-2"></i>Tag Populer
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($popularTags as $tag)
                            <a href="{{ route('categories.show', [$category, 'tag' => $tag]) }}"
                               class="badge bg-primary text-decoration-none me-1 mb-1">
                                {{ $tag }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Aksi Cepat
                    </h6>
                </div>
                <div class="card-body">
                    @auth
                        <a href="{{ route('threads.create') }}" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-plus me-2"></i>Buat Thread Baru
                        </a>
                    @endauth
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="fas fa-folder me-2"></i>Semua Kategori
                    </a>
                    <a href="{{ route('threads.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-list me-2"></i>Semua Thread
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.thread-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.thread-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
</style>
@endsection
