{{-- filepath: resources/views/statistics.blade.php --}}

@extends('layouts.app')

@section('title', 'Statistik Forum')

@section('styles')
<style>
    .stats-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s;
        margin-bottom: 1.5rem;
    }

    .stats-card:hover {
        transform: translateY(-5px);
    }

    .stats-header {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 1.5rem;
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .progress {
        height: 10px;
        border-radius: 5px;
    }

    .chart-container {
        position: relative;
        height: 300px;
    }

    .activity-badge {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
    }

    .badge-thread {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    }

    .badge-comment {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .badge-vote {
        background: linear-gradient(to right, #f46b45, #eea849);
    }

    .timeline-item {
        position: relative;
        padding-left: 45px;
        padding-bottom: 20px;
    }

    .timeline-item:before {
        content: "";
        position: absolute;
        left: 20px;
        top: 40px;
        height: 100%;
        width: 2px;
        background-color: #e0e0e0;
    }

    .timeline-item:last-child:before {
        display: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <h1 class="mb-4">
        <i class="fas fa-chart-bar me-2"></i>Statistik Forum
    </h1>

    <!-- Overview Stats Row -->
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="stats-card h-100">
                <div class="stats-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @php
                                $totalThreads = 0;
                                try {
                                    $totalThreads = \App\Models\Thread::count();
                                } catch (\Exception $e) {
                                    $totalThreads = 0;
                                }
                            @endphp
                            <h3 class="mb-0">{{ $totalThreads }}</h3>
                            <p class="mb-0">Total Thread</p>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">Peningkatan sejak bulan lalu</p>
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1 me-3">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 65%"></div>
                        </div>
                        <span class="text-success"><i class="fas fa-arrow-up me-1"></i>65%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stats-card h-100">
                <div class="stats-header" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%)">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @php
                                $totalComments = 0;
                                try {
                                    $totalComments = \App\Models\Comment::count();
                                } catch (\Exception $e) {
                                    $totalComments = 0;
                                }
                            @endphp
                            <h3 class="mb-0">{{ $totalComments }}</h3>
                            <p class="mb-0">Total Komentar</p>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-reply"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">Peningkatan sejak bulan lalu</p>
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1 me-3">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 82%"></div>
                        </div>
                        <span class="text-success"><i class="fas fa-arrow-up me-1"></i>82%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stats-card h-100">
                <div class="stats-header" style="background: linear-gradient(to right, #f46b45, #eea849)">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @php
                                $totalUsers = 0;
                                try {
                                    $totalUsers = \App\Models\User::count();
                                } catch (\Exception $e) {
                                    $totalUsers = 0;
                                }
                            @endphp
                            <h3 class="mb-0">{{ $totalUsers }}</h3>
                            <p class="mb-0">Total Pengguna</p>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">Peningkatan sejak bulan lalu</p>
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1 me-3">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 45%"></div>
                        </div>
                        <span class="text-success"><i class="fas fa-arrow-up me-1"></i>45%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="stats-card h-100">
                <div class="stats-header" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @php
                                $totalVotes = 0;
                                try {
                                    if (class_exists('\App\Models\Vote')) {
                                        $totalVotes = \App\Models\Vote::count();
                                    } else {
                                        $totalVotes = \App\Models\Thread::sum('vote_score');
                                    }
                                } catch (\Exception $e) {
                                    $totalVotes = 0;
                                }
                            @endphp
                            <h3 class="mb-0">{{ $totalVotes }}</h3>
                            <p class="mb-0">Total Vote</p>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-thumbs-up"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">Peningkatan sejak bulan lalu</p>
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1 me-3">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 75%"></div>
                        </div>
                        <span class="text-success"><i class="fas fa-arrow-up me-1"></i>75%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Aktivitas Forum 30 Hari Terakhir</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="activityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Distribusi Kategori</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Aktivitas Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @php
                            $recentThreads = collect();
                            $recentComments = collect();

                            try {
                                $recentThreads = \App\Models\Thread::with('user')->latest()->take(3)->get();
                            } catch (\Exception $e) {
                                $recentThreads = collect();
                            }

                            try {
                                $recentComments = \App\Models\Comment::with(['user', 'thread'])->latest()->take(3)->get();
                            } catch (\Exception $e) {
                                $recentComments = collect();
                            }
                        @endphp

                        @forelse($recentThreads as $thread)
                        <div class="list-group-item timeline-item">
                            <div class="activity-badge badge-thread position-absolute start-0 top-0">
                                <i class="fas fa-comments"></i>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-1">{{ $thread->title ?? 'Thread Tanpa Judul' }}</h6>
                                <small class="text-muted">{{ $thread->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1 text-muted">{{ Str::limit($thread->body ?? '', 80) }}</p>
                            <small>oleh {{ $thread->user->name ?? 'Unknown User' }}</small>
                        </div>
                        @empty
                        <div class="list-group-item">
                            <p class="text-muted text-center mb-0">Belum ada thread terbaru</p>
                        </div>
                        @endforelse

                        @forelse($recentComments as $comment)
                        <div class="list-group-item timeline-item">
                            <div class="activity-badge badge-comment position-absolute start-0 top-0">
                                <i class="fas fa-reply"></i>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-1">Komentar pada "{{ $comment->thread->title ?? 'Thread' }}"</h6>
                                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1 text-muted">{{ Str::limit($comment->body ?? '', 80) }}</p>
                            <small>oleh {{ $comment->user->name ?? 'Unknown User' }}</small>
                        </div>
                        @empty
                        <div class="list-group-item">
                            <p class="text-muted text-center mb-0">Belum ada komentar terbaru</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Pengguna Paling Aktif</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        @php
                            $activeUsers = collect();
                            try {
                                $activeUsers = \App\Models\User::withCount(['threads', 'comments'])
                                    ->orderByDesc('threads_count')
                                    ->take(5)
                                    ->get();
                            } catch (\Exception $e) {
                                $activeUsers = collect();
                            }
                        @endphp

                        @forelse($activeUsers as $user)
                        <li class="mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                                    style="width: 50px; height: 50px; font-weight: bold; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%) !important;">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $user->name ?? 'Unknown User' }}</h6>
                                    <div class="d-flex text-muted small">
                                        <span class="me-3"><i class="fas fa-comments me-1"></i>{{ $user->threads_count ?? 0 }} thread</span>
                                        <span><i class="fas fa-reply me-1"></i>{{ $user->comments_count ?? 0 }} komentar</span>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="text-center text-muted">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <p>Belum ada data pengguna aktif</p>
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Activity Chart
        const activityCtx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: ['1 Jun', '5 Jun', '10 Jun', '15 Jun', '20 Jun', '25 Jun', '30 Jun'],
                datasets: [
                    {
                        label: 'Thread',
                        data: [12, 19, 15, 25, 22, 30, 35],
                        borderColor: '#6a11cb',
                        backgroundColor: 'rgba(106, 17, 203, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Komentar',
                        data: [30, 45, 35, 60, 48, 75, 85],
                        borderColor: '#11998e',
                        backgroundColor: 'rgba(17, 153, 142, 0.1)',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Vote',
                        data: [42, 55, 60, 78, 80, 95, 110],
                        borderColor: '#f46b45',
                        backgroundColor: 'rgba(244, 107, 69, 0.1)',
                        tension: 0.4,
                        fill: true
                    }
                ]
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

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Teknologi', 'Hiburan', 'Pendidikan', 'Kuliner', 'Lainnya'],
                datasets: [{
                    data: [35, 25, 20, 10, 10],
                    backgroundColor: [
                        '#6a11cb',
                        '#11998e',
                        '#f46b45',
                        '#4facfe',
                        '#800080'
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>
@endpush
