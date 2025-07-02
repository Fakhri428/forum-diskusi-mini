<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/admin/categories/show.blade.php -->
@extends('layouts.admin')

@section('title', 'Detail Kategori')

@section('styles')
<style>
    .category-badge {
        display: inline-flex;
        align-items: center;
        padding: 8px 15px;
        border-radius: 30px;
        color: white;
        margin-bottom: 15px;
    }

    .thread-item {
        border-left: 3px solid #eee;
        padding-left: 15px;
        margin-bottom: 15px;
    }

    .thread-item:hover {
        border-left-color: #4e73df;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Kategori</h1>
        <div>
            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Category Information -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Informasi Kategori</h6>
                    <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-danger' }}">
                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="category-badge" style="background-color: {{ $category->color ?? '#6c757d' }}">
                            @if($category->icon)
                                <i class="{{ $category->icon }} mr-2"></i>
                            @endif
                            <span class="h5 mb-0">{{ $category->name }}</span>
                        </div>

                        @if($category->parent)
                            <div class="mt-2">
                                <span class="text-muted">Induk Kategori:</span>
                                <a href="{{ route('admin.categories.show', $category->parent->id) }}">
                                    {{ $category->parent->name }}
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <h6 class="font-weight-bold">Deskripsi:</h6>
                        <p>{{ $category->description ?? 'Tidak ada deskripsi' }}</p>
                    </div>

                    <hr>

                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h5 mb-0 font-weight-bold text-primary">{{ $category->threads_count }}</div>
                            <div class="small text-muted">Thread</div>
                        </div>
                        <div class="col-6">
                            <div class="h5 mb-0 font-weight-bold text-info">{{ $category->children->count() }}</div>
                            <div class="small text-muted">Sub-kategori</div>
                        </div>
                    </div>

                    <hr>

                    <table class="table table-sm">
                        <tr>
                            <th>ID:</th>
                            <td>{{ $category->id }}</td>
                        </tr>
                        <tr>
                            <th>Slug:</th>
                            <td>{{ $category->slug }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat:</th>
                            <td>{{ $category->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Diupdate:</th>
                            <td>{{ $category->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Category Content -->
        <div class="col-lg-8">
            <!-- Sub-Categories -->
            @if($category->children->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold">Sub-kategori ({{ $category->children->count() }})</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($category->children as $child)
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                @if($child->icon)
                                                    <i class="{{ $child->icon }} mr-2" style="color: {{ $child->color ?? '#6c757d' }}"></i>
                                                @endif
                                                <h6 class="mb-0 font-weight-bold">{{ $child->name }}</h6>

                                                @if(!$child->is_active)
                                                    <span class="badge bg-danger ml-2">Nonaktif</span>
                                                @endif
                                            </div>

                                            <p class="text-muted small">
                                                {{ Str::limit($child->description, 100) }}
                                            </p>

                                            <div class="d-flex justify-content-between">
                                                <span class="badge bg-info">{{ $child->threads_count ?? 0 }} Thread</span>
                                                <a href="{{ route('admin.categories.show', $child->id) }}" class="btn btn-sm btn-outline-primary">
                                                    Detail
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Threads -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Thread Terbaru ({{ $category->threads_count }})</h6>
                    <a href="{{ route('admin.threads.index', ['category_id' => $category->id]) }}" class="btn btn-sm btn-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if($category->threads->count() > 0)
                        @foreach($category->threads as $thread)
                            <div class="thread-item">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">
                                        <a href="{{ route('admin.threads.show', $thread->id) }}">
                                            {{ $thread->title }}
                                        </a>
                                    </h6>
                                    <div>
                                        @if(!$thread->is_approved)
                                            <span class="badge bg-warning">Menunggu Persetujuan</span>
                                        @endif

                                        @if($thread->is_pinned)
                                            <span class="badge bg-info">Disematkan</span>
                                        @endif

                                        @if($thread->is_locked)
                                            <span class="badge bg-danger">Dikunci</span>
                                        @endif
                                    </div>
                                </div>

                                <p class="text-muted mb-1">
                                    {{ Str::limit(strip_tags($thread->body), 150) }}
                                </p>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="small text-muted">
                                        Oleh: {{ $thread->user->name }} | {{ $thread->created_at->format('d M Y H:i') }}
                                    </div>
                                    <div>
                                        <span class="badge bg-secondary">{{ $thread->comments_count ?? 0 }} Komentar</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <img src="{{ asset('images/empty-threads.svg') }}" alt="Tidak ada thread" class="img-fluid mb-3" width="150">
                            <h6>Belum ada thread di kategori ini</h6>
                            <p class="text-muted">Thread yang dibuat di kategori ini akan ditampilkan di sini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
