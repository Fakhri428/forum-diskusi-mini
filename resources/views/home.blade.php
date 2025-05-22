@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body p-0">
                    <div class="p-5 bg-primary text-white rounded-top" style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%) !important;">
                        <h1 class="display-5 fw-bold mb-3">Selamat Datang, {{ Auth::user()->name }}!</h1>
                        <p class="lead">Bergabunglah dalam diskusi dan bertukar pikiran dengan pengguna lainnya.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Activity Stats Cards -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column">
                    <div class="rounded-circle bg-primary mx-auto d-flex align-items-center justify-content-center mb-3"
                        style="width: 80px; height: 80px; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%) !important;">
                        <i class="fas fa-comments fa-2x text-white"></i>
                    </div>
                    <h3 class="fs-2 mb-2">{{ App\Models\Thread::count() }}</h3>
                    <h5 class="card-title mb-0">Total Thread</h5>
                    <div class="mt-auto pt-3">
                        <a href="{{ route('threads.index') }}" class="btn btn-sm btn-outline-primary w-100">Lihat Semua</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column">
                    <div class="rounded-circle bg-success mx-auto d-flex align-items-center justify-content-center mb-3"
                        style="width: 80px; height: 80px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;">
                        <i class="fas fa-users fa-2x text-white"></i>
                    </div>
                    <h3 class="fs-2 mb-2">{{ App\Models\User::count() }}</h3>
                    <h5 class="card-title mb-0">Pengguna</h5>
                    <div class="mt-auto pt-3">
                        <a href="#" class="btn btn-sm btn-outline-success w-100">Komunitas</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column">
                    <div class="rounded-circle bg-warning mx-auto d-flex align-items-center justify-content-center mb-3"
                        style="width: 80px; height: 80px; background: linear-gradient(to right, #f46b45, #eea849) !important;">
                        <i class="fas fa-comment-dots fa-2x text-white"></i>
                    </div>
                    <h3 class="fs-2 mb-2">{{ Auth::user()->threads()->count() }}</h3>
                    <h5 class="card-title mb-0">Thread Saya</h5>
                    <div class="mt-auto pt-3">
                        <a href="{{ url('/diskusi-saya') }}" class="btn btn-sm btn-outline-warning w-100">Lihat Thread Saya</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card text-center h-100">
                <div class="card-body d-flex flex-column">
                    <div class="rounded-circle bg-info mx-auto d-flex align-items-center justify-content-center mb-3"
                        style="width: 80px; height: 80px; background: linear-gradient(to right, #4facfe, #00f2fe) !important;">
                        <i class="fas fa-reply fa-2x text-white"></i>
                    </div>
                    <h3 class="fs-2 mb-2">{{ Auth::user()->comments()->count() }}</h3>
                    <h5 class="card-title mb-0">Komentar Saya</h5>
                    <div class="mt-auto pt-3">
                        <a href="{{ url('/komentar-saya') }}" class="btn btn-sm btn-outline-info w-100">Lihat Komentar Saya</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <!-- Recent Threads -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-fire me-2 text-danger"></i>Thread Terbaru</h5>
                    <a href="{{ route('threads.index') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse(App\Models\Thread::with('user', 'category', 'tags')->latest()->take(5)->get() as $thread)
                            <a href="{{ route('threads.show', $thread->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $thread->title }}</h6>
                                        <p class="mb-1 text-muted small">
                                            <span class="me-2"><i class="fas fa-user-circle me-1"></i>{{ $thread->user->name }}</span>
                                            @if($thread->category)
                                                <span class="badge bg-primary me-1">{{ $thread->category->name }}</span>
                                            @endif
                                            @foreach($thread->tags as $tag)
                                                <span class="badge bg-secondary me-1">{{ $tag->name }}</span>
                                            @endforeach
                                        </p>
                                    </div>
                                    <small class="text-muted"><i class="far fa-clock me-1"></i>{{ $thread->created_at->diffForHumans() }}</small>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-5">
                                <p class="text-muted mb-0">Belum ada thread yang tersedia.</p>
                                <a href="{{ route('threads.create') }}" class="btn btn-primary mt-3">Buat Thread Baru</a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side -->
        <div class="col-lg-4 mb-4">
            <!-- User Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Profil Saya</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" class="rounded-circle" width="80" height="80">
                        @else
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 80px; height: 80px; font-size: 32px; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%) !important;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                    <p class="text-muted small mb-3">{{ Auth::user()->email }}</p>
                    <div class="d-grid">
                        <a href="{{ route('profile') }}" class="btn btn-outline-primary">Edit Profil</a>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Aksi Cepat</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('threads.create') }}" class="list-group-item list-group-item-action py-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center me-3"
                                    style="width: 40px; height: 40px; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important;">
                                    <i class="fas fa-plus text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Buat Thread Baru</h6>
                                    <small class="text-muted">Mulai diskusi baru</small>
                                </div>
                            </div>
                        </a>
                        <a href="{{ url('/diskusi-saya') }}" class="list-group-item list-group-item-action py-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center me-3"
                                    style="width: 40px; height: 40px; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%) !important;">
                                    <i class="fas fa-list text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Thread Saya</h6>
                                    <small class="text-muted">Lihat thread yang telah dibuat</small>
                                </div>
                            </div>
                        </a>
                        <a href="{{ url('/notifikasi') }}" class="list-group-item list-group-item-action py-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center me-3"
                                    style="width: 40px; height: 40px; background: linear-gradient(to right, #f46b45, #eea849) !important;">
                                    <i class="fas fa-bell text-white"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Notifikasi</h6>
                                    <small class="text-muted">Cek notifikasi baru</small>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
