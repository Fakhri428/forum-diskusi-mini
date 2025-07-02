<!-- filepath: resources/views/admin/dashboard.blade.php -->
@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex">
                    <div class="rounded-circle bg-primary bg-gradient d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-users fa-lg text-white"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-1">{{ $stats['users'] }}</h3>
                        <p class="text-muted mb-0">Total Pengguna</p>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="fas fa-user-cog me-1"></i> Kelola Pengguna
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex">
                    <div class="rounded-circle bg-success bg-gradient d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-comments fa-lg text-white"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-1">{{ $stats['threads'] }}</h3>
                        <p class="text-muted mb-0">Total Thread</p>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('admin.threads.index') }}" class="btn btn-sm btn-outline-success w-100">
                        <i class="fas fa-list me-1"></i> Kelola Thread
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex">
                    <div class="rounded-circle bg-info bg-gradient d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-reply fa-lg text-white"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-1">{{ $stats['comments'] }}</h3>
                        <p class="text-muted mb-0">Total Komentar</p>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('admin.comments.index') }}" class="btn btn-sm btn-outline-info w-100">
                        <i class="fas fa-comment-dots me-1"></i> Kelola Komentar
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex">
                    <div class="rounded-circle bg-warning bg-gradient d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 60px; height: 60px;">
                        <i class="fas fa-folder fa-lg text-white"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-1">{{ $stats['categories'] }}</h3>
                        <p class="text-muted mb-0">Total Kategori</p>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-warning w-100">
                        <i class="fas fa-folder-plus me-1"></i> Kelola Kategori
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-user-clock me-2"></i>Pengguna Terbaru</h5>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-users me-1"></i> Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Bergabung</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestUsers as $user)

                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'moderator' ? 'warning' : 'primary') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Thread Terbaru</h5>
                        <a href="{{ route('admin.threads.index') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-comments me-1"></i> Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Penulis</th>
                                    <th>Kategori</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestThreads as $thread)
                                <tr>
                                    <td>{{ Str::limit($thread->title, 25) }}</td>
                                    <td>{{ $thread->user->name }}</td>
                                    <td>
                                        @if($thread->category)
                                            <span class="badge bg-info">
                                                {{ $thread->category->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Tanpa Kategori</span>
                                        @endif
                                    </td>
                                    <td>{{ $thread->created_at->format('d M Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('threads.show', $thread->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.threads.edit', $thread->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Aktivitas Forum</h5>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary">Minggu</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary active">Bulan</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary">Tahun</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="activityChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-folder me-2"></i>Distribusi Kategori</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryPieChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Activity Chart
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Thread',
                    data: [12, 19, 3, 5, 2, 3, 7, 8, 10, 12, 15, 17],
                    borderColor: 'rgba(40, 167, 69, 1)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.3,
                    fill: true
                }, {
                    label: 'Komentar',
                    data: [30, 29, 13, 15, 22, 30, 17, 28, 20, 22, 35, 37],
                    borderColor: 'rgba(0, 123, 255, 1)',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.3,
                    fill: true
                }, {
                    label: 'Pengguna',
                    data: [5, 10, 15, 20, 22, 25, 28, 30, 35, 40, 45, 47],
                    borderColor: 'rgba(220, 53, 69, 1)',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Category Pie Chart
        const categoryCtx = document.getElementById('categoryPieChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Teknologi', 'Kesehatan', 'Pendidikan', 'Hiburan', 'Lainnya'],
                datasets: [{
                    data: [35, 20, 15, 20, 10],
                    backgroundColor: [
                        'rgba(0, 123, 255, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(108, 117, 125, 0.8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    });
</script>
@endsection
