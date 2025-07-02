{{-- filepath: resources/views/categories/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Semua Kategori')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;">
                <div class="card-body text-center py-5">
                    <h1 class="display-5 fw-bold mb-3">
                        <i class="fas fa-folder-open me-3"></i>Kategori Thread
                    </h1>
                    <p class="lead mb-0">Jelajahi thread berdasarkan kategori yang Anda minati</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="row">
        @forelse($categories as $category)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 border-0 shadow-sm category-card">
                    <div class="card-body d-flex flex-column">
                        <!-- Category Icon & Name -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                 style="width: 60px; height: 60px; background-color: {{ $category->color ?? '#007bff' }};">
                                @if($category->icon)
                                    <i class="{{ $category->icon }} fa-lg text-white"></i>
                                @else
                                    <i class="fas fa-folder fa-lg text-white"></i>
                                @endif
                            </div>
                            <div>
                                <h5 class="card-title mb-1">{{ $category->name }}</h5>
                                <span class="badge bg-light text-dark">
                                    {{ $category->threads_count }} thread
                                </span>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($category->description)
                            <p class="card-text text-muted flex-grow-1">
                                {{ Str::limit($category->description, 100) }}
                            </p>
                        @endif

                        <!-- Action Button -->
                        <div class="mt-auto">
                            <a href="{{ route('categories.show', $category) }}"
                               class="btn btn-outline-primary w-100">
                                <i class="fas fa-eye me-2"></i>Lihat Thread
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-folder-open fa-4x text-muted"></i>
                    </div>
                    <h4 class="text-muted mb-3">Belum Ada Kategori</h4>
                    <p class="text-muted">Kategori thread akan muncul di sini setelah dibuat oleh administrator.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Back Button -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="{{ route('threads.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Semua Thread
            </a>
        </div>
    </div>
</div>

<style>
.category-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection
