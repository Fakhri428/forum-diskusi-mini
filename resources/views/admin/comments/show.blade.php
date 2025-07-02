<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/admin/comments/show.blade.php -->
@extends('layouts.admin')

@section('title', 'Detail Komentar')

@section('styles')
<style>
    .comment-container {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .parent-comment {
        border-left: 3px solid #4e73df;
        background-color: #f1f3ff;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
    }

    .reply-comment {
        border-left: 3px solid #1cc88a;
        background-color: #f1fff8;
        padding: 15px;
        margin-top: 15px;
        border-radius: 5px;
    }

    .avatar-sm {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .report-item {
        border-left: 3px solid #e74a3b;
        padding-left: 15px;
        margin-bottom: 15px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Komentar</h1>
        <div>
            <a href="{{ route('admin.comments.edit', $comment->id) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <div class="btn-group ml-2">
                <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <form action="{{ route('admin.comments.toggle-approval', $comment->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="dropdown-item">
                                @if($comment->is_approved)
                                    <i class="fas fa-times-circle text-warning mr-2"></i> Batalkan Persetujuan
                                @else
                                    <i class="fas fa-check-circle text-success mr-2"></i> Setujui Komentar
                                @endif
                            </button>
                        </form>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash-alt mr-2"></i> Hapus Komentar
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Thread Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thread</h6>
                </div>
                <div class="card-body">
                    <h5>
                        <a href="{{ route('admin.threads.show', $comment->thread_id) }}">
                            {{ $comment->thread->title }}
                        </a>
                    </h5>
                    <p class="text-muted">
                        Kategori:
                        <a href="{{ route('admin.categories.show', $comment->thread->category_id) }}" style="color: {{ $comment->thread->category->color ?? '#6c757d' }};">
                            {{ $comment->thread->category->name }}
                        </a>
                    </p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Oleh:
                                <a href="{{ route('admin.users.show', $comment->thread->user_id) }}">
                                    {{ $comment->thread->user->name }}
                                </a>
                            </small>
                        </div>
                        <div>
                            @if($comment->thread->is_locked)
                                <span class="badge bg-danger">Thread Dikunci</span>
                            @endif

                            @if(!$comment->thread->is_approved)
                                <span class="badge bg-warning">Thread Belum Disetujui</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comment Chain -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Komentar</h6>
                </div>
                <div class="card-body">
                    @if($comment->parent)
                        <div class="parent-comment">
                            <div class="d-flex align-items-center mb-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($comment->parent->user->name) }}&background=random" class="avatar-sm mr-2" alt="{{ $comment->parent->user->name }}">
                                <div>
                                    <div>
                                        <a href="{{ route('admin.users.show', $comment->parent->user_id) }}" class="font-weight-bold">
                                            {{ $comment->parent->user->name }}
                                        </a>
                                    </div>
                                    <div class="small text-muted">
                                        {{ $comment->parent->created_at->format('d M Y H:i') }}
                                    </div>
                                </div>
                            </div>
                            <p>{{ $comment->parent->body }}</p>
                        </div>
                    @endif

                    <div class="comment-container">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->name) }}&background=random" class="avatar-sm mr-2" alt="{{ $comment->user->name }}">
                                <div>
                                    <div>
                                        <a href="{{ route('admin.users.show', $comment->user_id) }}" class="font-weight-bold">
                                            {{ $comment->user->name }}
                                        </a>
                                    </div>
                                    <div class="small text-muted">
                                        <span title="{{ $comment->created_at->format('d M Y H:i:s') }}">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </span>
                                        @if($comment->created_at != $comment->updated_at)
                                            (diubah {{ $comment->updated_at->diffForHumans() }})
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div>
                                @if($comment->is_approved)
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif

                                @if(isset($comment->reports_count) && $comment->reports_count > 0)
                                    <span class="badge bg-danger">{{ $comment->reports_count }} Laporan</span>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <p class="lead">{{ $comment->body }}</p>
                        </div>

                        <hr>

                        <div class="text-muted small">
                            <div>ID: {{ $comment->id }}</div>
                            <div>Dibuat: {{ $comment->created_at->format('d M Y H:i') }}</div>
                            <div>Diupdate: {{ $comment->updated_at->format('d M Y H:i') }}</div>
                        </div>
                    </div>

                    @if(isset($comment->replies) && $comment->replies->count() > 0)
                        <h6 class="mt-4 mb-3">Balasan ({{ $comment->replies->count() }})</h6>

                        @foreach($comment->replies as $reply)
                            <div class="reply-comment">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($reply->user->name) }}&background=random" class="avatar-sm mr-2" alt="{{ $reply->user->name }}">
                                        <div>
                                            <div>
                                                <a href="{{ route('admin.users.show', $reply->user_id) }}" class="font-weight-bold">
                                                    {{ $reply->user->name }}
                                                </a>
                                            </div>
                                            <div class="small text-muted">
                                                {{ $reply->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.comments.show', $reply->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </div>

                                <p>{{ $reply->body }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Comment Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tindakan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Status Persetujuan</h6>
                        <form action="{{ route('admin.comments.toggle-approval', $comment->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="d-grid">
                                <button type="submit" class="btn btn-{{ $comment->is_approved ? 'warning' : 'success' }}">
                                    @if($comment->is_approved)
                                        <i class="fas fa-times-circle mr-1"></i> Batalkan Persetujuan
                                    @else
                                        <i class="fas fa-check-circle mr-1"></i> Setujui Komentar
                                    @endif
                                </button>
                            </div>
                        </form>
                    </div>

                    <hr>

                    <div class="d-grid">
                        <a href="{{ route('admin.comments.create', ['thread_id' => $comment->thread_id, 'parent_id' => $comment->id]) }}" class="btn btn-info mb-2">
                            <i class="fas fa-reply mr-1"></i> Balas Komentar
                        </a>

                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash-alt mr-1"></i> Hapus Komentar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Reports Section (if any) -->
            @if(isset($comment->reports) && $comment->reports->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-danger">Laporan ({{ $comment->reports->count() }})</h6>
                    </div>
                    <div class="card-body">
                        @foreach($comment->reports as $report)
                            <div class="report-item">
                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        <strong>{{ $report->reason }}</strong>
                                    </div>
                                    <div>
                                        <span class="badge {{ $report->status == 'pending' ? 'bg-warning' : ($report->status == 'approved' ? 'bg-success' : 'bg-danger') }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </div>
                                </div>

                                <p>{{ $report->details }}</p>

                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        Dilaporkan oleh
                                        <a href="{{ route('admin.users.show', $report->user_id) }}">
                                            {{ $report->user->name }}
                                        </a>
                                        {{ $report->created_at->diffForHumans() }}
                                    </small>
                                    <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-sm btn-outline-danger">
                                        Tindak Lanjuti
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- User Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Penulis</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->name) }}&background=random&size=100" class="img-fluid rounded-circle mb-2" alt="{{ $comment->user->name }}">
                        <h5>{{ $comment->user->name }}</h5>
                        <p class="text-muted">{{ '@' . $comment->user->username }}</p>
                    </div>

                    <hr>

                    <div class="row text-center">
                        <div class="col-6">
                            <div class="h5 mb-0 font-weight-bold">{{ $comment->user->threads_count ?? 0 }}</div>
                            <div class="small text-muted">Thread</div>
                        </div>
                        <div class="col-6">
                            <div class="h5 mb-0 font-weight-bold">{{ $comment->user->comments_count ?? 0 }}</div>
                            <div class="small text-muted">Komentar</div>
                        </div>
                    </div>

                    <div class="d-grid mt-3">
                        <a href="{{ route('admin.users.show', $comment->user_id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-user mr-1"></i> Lihat Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Komentar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus komentar ini?</p>
                <p class="text-danger"><strong>Perhatian:</strong> Semua balasan terhadap komentar ini juga akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus Komentar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
