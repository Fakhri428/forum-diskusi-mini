@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-4">
            <!-- Profile Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Profil Pengguna</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" class="rounded-circle img-fluid" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 150px; height: 150px; font-size: 60px;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h5 class="my-3">{{ Auth::user()->name }}</h5>
                    <p class="text-muted mb-1">{{ Auth::user()->role }}</p>
                    <p class="text-muted mb-4">Bergabung: {{ Auth::user()->created_at->format('d M Y') }}</p>
                    <div class="d-flex justify-content-center mb-2">
                        <a href="#" class="btn btn-primary me-2">Edit Profil</a>
                        <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Yakin ingin logout?');">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">Logout</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Contact Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informasi Kontak</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-envelope me-2 text-primary"></i>
                            <p class="mb-0">Email</p>
                        </div>
                        <p class="text-muted mb-0">{{ Auth::user()->email }}</p>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-phone me-2 text-primary"></i>
                            <p class="mb-0">Telepon</p>
                        </div>
                        <p class="text-muted mb-0">{{ Auth::user()->phone ?? 'Belum ditambahkan' }}</p>
                    </div>
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                            <p class="mb-0">Lokasi</p>
                        </div>
                        <p class="text-muted mb-0">{{ Auth::user()->location ?? 'Belum ditambahkan' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Bio Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Tentang Saya</h5>
                </div>
                <div class="card-body">
                    <p>{{ Auth::user()->bio ?? 'Belum ada informasi tentang pengguna ini.' }}</p>
                </div>
            </div>

            <!-- Activity Stats Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Statistik Aktivitas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card text-center py-3 border-0 bg-light">
                                <h3 class="mb-0">{{ Auth::user()->threads()->count() }}</h3>
                                <div class="text-muted">Thread Dibuat</div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card text-center py-3 border-0 bg-light">
                                <h3 class="mb-0">{{ Auth::user()->comments()->count() }}</h3>
                                <div class="text-muted">Komentar</div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card text-center py-3 border-0 bg-light">
                                <h3 class="mb-0">{{ Auth::user()->votes()->count() }}</h3>
                                <div class="text-muted">Vote Diberikan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Card -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Aktivitas Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse(Auth::user()->threads()->latest()->take(3)->get() as $thread)
                            <a href="{{ route('threads.show', $thread) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $thread->title }}</h6>
                                    <small>{{ $thread->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 text-truncate">{{ $thread->body }}</p>
                                <small class="text-muted">Thread</small>
                            </a>
                        @empty
                            <div class="text-center py-3">
                                <p class="text-muted mb-0">Belum ada thread yang dibuat.</p>
                            </div>
                        @endforelse
                    </div>

                    <hr>

                    <div class="list-group">
                        @forelse(Auth::user()->comments()->latest()->take(3)->get() as $comment)
                            <a href="{{ route('threads.show', $comment->thread_id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">Komentar pada: {{ $comment->thread->title }}</h6>
                                    <small>{{ $comment->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 text-truncate">{{ $comment->body }}</p>
                                <small class="text-muted">Komentar</small>
                            </a>
                        @empty
                            <div class="text-center py-3">
                                <p class="text-muted mb-0">Belum ada komentar yang ditulis.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
