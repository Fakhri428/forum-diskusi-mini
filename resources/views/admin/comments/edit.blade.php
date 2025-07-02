<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/admin/comments/edit.blade.php -->
@extends('layouts.admin')

@section('title', 'Edit Komentar')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Komentar</h1>
        <div>
            <a href="{{ route('admin.comments.show', $comment->id) }}" class="btn btn-info">
                <i class="fas fa-eye mr-1"></i> Lihat
            </a>
            <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Komentar</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.comments.update', $comment->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Thread</label>
                    <input type="text" class="form-control" value="{{ $comment->thread->title }}" readonly disabled>
                    <small class="form-text text-muted">
                        <a href="{{ route('admin.threads.show', $comment->thread_id) }}" target="_blank">
                            Buka thread dalam tab baru <i class="fas fa-external-link-alt"></i>
                        </a>
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Penulis</label>
                    <input type="text" class="form-control" value="{{ $comment->user->name }}" readonly disabled>
                    <small class="form-text text-muted">
                        <a href="{{ route('admin.users.show', $comment->user_id) }}" target="_blank">
                            Lihat profil pengguna <i class="fas fa-external-link-alt"></i>
                        </a>
                    </small>
                </div>

                @if($comment->parent_id)
                    <div class="mb-3">
                        <label class="form-label">Komentar Induk</label>
                        <div class="card">
                            <div class="card-body">
                                <p class="mb-1">{{ $comment->parent->body }}</p>
                                <small class="text-muted">- {{ $comment->parent->user->name }}</small>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mb-3">
                    <label for="body" class="form-label">Isi Komentar <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="5" required>{{ old('body', $comment->body) }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="is_approved" name="is_approved" value="1" {{ old('is_approved', $comment->is_approved) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_approved">Komentar Disetujui</label>
                    <small class="form-text text-muted d-block">
                        Komentar yang tidak disetujui tidak akan terlihat oleh pengguna biasa.
                    </small>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Comment Information -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Komentar</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th>ID:</th>
                            <td>{{ $comment->id }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat:</th>
                            <td>{{ $comment->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diupdate:</th>
                            <td>{{ $comment->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($comment->is_approved)
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-warning">Belum Disetujui</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Dilaporkan:</th>
                            <td>
                                @if($comment->reports_count > 0)
                                    <span class="badge bg-danger">{{ $comment->reports_count }} laporan</span>
                                @else
                                    <span class="badge bg-secondary">Tidak ada laporan</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Balasan:</th>
                            <td>{{ $comment->replies_count ?? 0 }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
