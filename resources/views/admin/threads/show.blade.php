<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/admin/threads/show.blade.php -->
@extends('layouts.admin')

@section('title', 'Detail Thread')

@section('styles')
<style>
    .thread-header {
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }

    .thread-content {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .comment-item {
        border-left: 3px solid #e3e6f0;
        padding-left: 15px;
        margin-bottom: 15px;
    }

    .comment-item:hover {
        border-left-color: #4e73df;
    }

    .avatar-sm {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .status-badge {
        margin-right: 5px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Thread</h1>
        <div>
            <a href="{{ route('admin.threads.edit', $thread->id) }}" class="btn btn-primary">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            <div class="btn-group ml-2">
                <a href="{{ route('admin.threads.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <form action="{{ route('admin.threads.toggle-approval', $thread->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="dropdown-item">
                                @if($thread->is_approved)
                                    <i class="fas fa-times-circle text-warning mr-2"></i> Batalkan Persetujuan
                                @else
                                    <i class="fas fa-check-circle text-success mr-2"></i> Setujui Thread
                                @endif
                            </button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('admin.threads.toggle-pinned', $thread->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="dropdown-item">
                                @if($thread->is_pinned)
                                    <i class="fas fa-thumbtack fa-rotate-90 text-secondary mr-2"></i> Lepas Sematan
                                @else
                                    <i class="fas fa-thumbtack text-info mr-2"></i> Sematkan Thread
                                @endif
                            </button>
                        </form>
                    </li>
                    <li>
                        <form action="{{ route('admin.threads.toggle-locked', $thread->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="dropdown-item">
                                @if($thread->is_locked)
                                    <i class="fas fa-unlock text-success mr-2"></i> Buka Kunci Thread
                                @else
                                    <i class="fas fa-lock text-danger mr-2"></i> Kunci Thread
                                @endif
                            </button>
                        </form>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash-alt mr-2"></i> Hapus Thread
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Thread Content -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="thread-header">
                        <h4 class="mb-3">{{ $thread->title }}</h4>
                        <div class="mb-2">
                            @if($thread->is_pinned)
                                <span class="badge bg-info status-badge"><i class="fas fa-thumbtack mr-1"></i> Disematkan</span>
                            @endif

                            @if($thread->is_locked)
                                <span class="badge bg-danger status-badge"><i class="fas fa-lock mr-1"></i> Dikunci</span>
                            @endif

                            @if($thread->is_approved)
                                <span class="badge bg-success status-badge"><i class="fas fa-check-circle mr-1"></i> Disetujui</span>
                            @else
                                <span class="badge bg-warning status-badge"><i class="fas fa-clock mr-1"></i> Menunggu Persetujuan</span>
                            @endif

                            <span class="badge bg-secondary">{{ $thread->comments->count() }} Komentar</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($thread->user->name) }}&background=random" class="avatar-sm mr-2" alt="{{ $thread->user->name }}">
                            <div>
                                <div>
                                    <a href="{{ route('admin.users.show', $thread->user->id) }}" class="font-weight-bold">
                                        {{ $thread->user->name }}
                                    </a>
                                </div>
                                <div class="small text-muted">
                                    <span title="{{ $thread->created_at->format('d M Y H:i:s') }}">
                                        {{ $thread->created_at->diffForHumans() }}
                                    </span>
                                    @if($thread->created_at != $thread->updated_at)
                                        (diubah {{ $thread->updated_at->diffForHumans() }})
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="thread-content">
                        {!! $thread->body !!}
                    </div>

                    <div class="text-muted small">
                        <div><strong>Kategori:</strong>
                            <a href="{{ route('admin.categories.show', $thread->category->id) }}" style="color: {{ $thread->category->color ?? '#6c757d' }};">
                                {{ $thread->category->name }}
                            </a>
                        </div>
                        <div><strong>ID Thread:</strong> {{ $thread->id }}</div>
                        <div><strong>Slug:</strong> {{ $thread->slug }}</div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Komentar ({{ $thread->comments->count() }})</h6>

                    @if(!$thread->is_locked)
                        <a href="{{ route('admin.comments.create', ['thread_id' => $thread->id]) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus mr-1"></i> Tambah Komentar
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($thread->is_locked)
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-lock mr-1"></i> Thread ini dikunci. Komentar baru tidak dapat ditambahkan.
                        </div>
                    @endif

                    @if($thread->comments->count() > 0)
                        @foreach($thread->comments as $comment)
                            <div class="comment-item" id="comment-{{ $comment->id }}">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->name) }}&background=random" class="avatar-sm mr-2" alt="{{ $comment->user->name }}">
                                        <div>
                                            <div>
                                                <a href="{{ route('admin.users.show', $comment->user->id) }}" class="font-weight-bold">
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
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="{{ route('admin.comments.edit', $comment->id) }}" class="dropdown-item">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <button type="button" class="dropdown-item text-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteCommentModal"
                                                data-comment-id="{{ $comment->id }}">
                                                <i class="fas fa-trash-alt mr-1"></i> Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    {{ $comment->body }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <img src="{{ asset('images/empty-comments.svg') }}" alt="Tidak ada komentar" class="img-fluid mb-3" style="max-width: 150px;">
                            <h6>Belum ada komentar pada thread ini</h6>
                            @if(!$thread->is_locked)
                                <p class="text-muted">Jadilah yang pertama memberikan komentar.</p>
                                <a href="{{ route('admin.comments.create', ['thread_id' => $thread->id]) }}" class="btn btn-primary">
                                    <i class="fas fa-plus mr-1"></i> Tambah Komentar
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Thread Status -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Status Thread</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Status Persetujuan</h6>
                        <form action="{{ route('admin.threads.toggle-approval', $thread->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="d-grid">
                                <button type="submit" class="btn btn-{{ $thread->is_approved ? 'warning' : 'success' }}">
                                    @if($thread->is_approved)
                                        <i class="fas fa-times-circle mr-1"></i> Batalkan Persetujuan
                                    @else
                                        <i class="fas fa-check-circle mr-1"></i> Setujui Thread
                                    @endif
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="mb-3">
                        <h6>Status Sematan</h6>
                        <form action="{{ route('admin.threads.toggle-pinned', $thread->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="d-grid">
                                <button type="submit" class="btn btn-{{ $thread->is_pinned ? 'secondary' : 'info' }}">
                                    @if($thread->is_pinned)
                                        <i class="fas fa-thumbtack fa-rotate-90 mr-1"></i> Lepas Sematan
                                    @else
                                        <i class="fas fa-thumbtack mr-1"></i> Sematkan Thread
                                    @endif
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="mb-3">
                        <h6>Status Kunci</h6>
                        <form action="{{ route('admin.threads.toggle-locked', $thread->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="d-grid">
                                <button type="submit" class="btn btn-{{ $thread->is_locked ? 'success' : 'danger' }}">
                                    @if($thread->is_locked)
                                        <i class="fas fa-unlock mr-1"></i> Buka Kunci Thread
                                    @else
                                        <i class="fas fa-lock mr-1"></i> Kunci Thread
                                    @endif
                                </button>
                            </div>
                        </form>
                    </div>

                    <hr>

                    <div class="d-grid">
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash-alt mr-1"></i> Hapus Thread
                        </button>
                    </div>
                </div>
            </div>

            <!-- Thread Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Informasi Thread</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>ID:</th>
                            <td>{{ $thread->id }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat pada:</th>
                            <td>{{ $thread->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir diupdate:</th>
                            <td>{{ $thread->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                        @if($thread->moderated_by)
                            <tr>
                                <th>Dimoderasi oleh:</th>
                                <td>
                                    <a href="{{ route('admin.users.show', $thread->moderated_by) }}">
                                        {{ $thread->moderator->name ?? 'Unknown' }}
                                    </a>
                                </td>
                            </tr>
                        @endif
                        @if($thread->moderated_at)
                            <tr>
                                <th>Dimoderasi pada:</th>
                                <td>{{ \Carbon\Carbon::parse($thread->moderated_at)->format('d M Y H:i') }}</td>
                            </tr>
                        @endif
                        @if($thread->moderation_reason)
                            <tr>
                                <th>Alasan moderasi:</th>
                                <td>{{ $thread->moderation_reason }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Related Threads -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold">Thread Terkait</h6>
                </div>
                <div class="card-body">
                    @php
                        $relatedThreads = \App\Models\Thread::where('category_id', $thread->category_id)
                            ->where('id', '!=', $thread->id)
                            ->latest()
                            ->take(5)
                            ->get();
                    @endphp

                    @if($relatedThreads->count() > 0)
                        <div class="list-group">
                            @foreach($relatedThreads as $relatedThread)
                                <a href="{{ route('admin.threads.show', $relatedThread->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 thread-title">{{ $relatedThread->title }}</h6>
                                        <small>{{ $relatedThread->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Oleh: {{ $relatedThread->user->name }}</small>
                                        <span class="badge bg-secondary rounded-pill">{{ $relatedThread->comments_count ?? 0 }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-muted py-3">Tidak ada thread terkait</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Thread Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Thread</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus thread ini?</p>
                <p class="text-danger"><strong>Perhatian:</strong> Semua komentar dalam thread ini juga akan dihapus. Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.threads.destroy', $thread->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus Thread</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Comment Modal -->
<div class="modal fade" id="deleteCommentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus Komentar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus komentar ini?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteCommentForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus Komentar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set up comment deletion modal
        const deleteCommentModal = document.getElementById('deleteCommentModal');
        if (deleteCommentModal) {
            deleteCommentModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const commentId = button.getAttribute('data-comment-id');
                const form = document.getElementById('deleteCommentForm');
                form.action = `{{ url('admin/comments') }}/${commentId}`;
            });
        }
    });
</script>
@endsection
