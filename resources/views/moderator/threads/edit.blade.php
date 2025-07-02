@extends('layouts.moderator')

@section('title', 'Edit Thread')

@section('styles')
<style>
    .metadata-card {
        background-color: #f8f9fa;
        border-left: 4px solid #0d6efd;
        padding: 15px;
        margin-bottom: 20px;
    }

    .category-badge {
        font-size: 0.9rem;
    }

    .thread-content {
        min-height: 200px;
    }
</style>
<!-- Include any CSS for rich text editor if used -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Thread</h1>
        <a href="{{ route('moderator.threads.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Main Card -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Thread #{{ $thread->id }}</h5>
                <a href="{{ route('threads.show', $thread) }}" class="btn btn-sm btn-outline-info" target="_blank">
                    <i class="fas fa-external-link-alt me-1"></i> Lihat di Situs
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Thread Metadata -->
            <div class="metadata-card rounded mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Penulis:</strong> {{ $thread->user->name ?? 'User tidak ditemukan' }}</p>
                        <p class="mb-1"><strong>Dibuat:</strong> {{ $thread->created_at->format('d M Y H:i') }}</p>
                        <p class="mb-0"><strong>Diperbarui:</strong> {{ $thread->updated_at->format('d M Y H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Status:</strong>
                            <span class="badge {{ $thread->is_approved ? 'bg-success' : 'bg-warning' }}">
                                {{ $thread->is_approved ? 'Disetujui' : 'Menunggu' }}
                            </span>
                        </p>
                        <p class="mb-1"><strong>Komentar:</strong> {{ $thread->comments_count ?? '0' }}</p>
                        <p class="mb-0"><strong>Dilihat:</strong> {{ $thread->views_count ?? '0' }}</p>
                    </div>
                </div>
            </div>

            <!-- Edit Form -->
            <form action="{{ route('moderator.threads.update', $thread) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Form errors -->
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Thread Title -->
                <div class="mb-3">
                    <label for="title" class="form-label">Judul Thread</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                           id="title" name="title" value="{{ old('title', $thread->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Thread Category -->
                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select class="form-select @error('category_id') is-invalid @enderror"
                            id="category_id" name="category_id">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $thread->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Thread Body -->
                <div class="mb-3">
                    <label for="body" class="form-label">Konten Thread</label>
                    <textarea class="form-control thread-content @error('body') is-invalid @enderror"
                              id="body" name="body" rows="10">{{ old('body', $thread->body) }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Thread Image (if applicable) -->
                @if($thread->image)
                <div class="mb-3">
                    <label class="form-label">Gambar Saat Ini</label>
                    <div class="mb-2">
                        <img src="{{ asset($thread->image) }}" alt="Thread Image" class="img-fluid rounded" style="max-height: 200px;">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                        <label class="form-check-label" for="remove_image">
                            Hapus gambar ini
                        </label>
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <label for="image" class="form-label">{{ $thread->image ? 'Ganti Gambar' : 'Upload Gambar' }}</label>
                    <input type="file" class="form-control @error('image') is-invalid @enderror"
                           id="image" name="image" accept="image/*">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Format yang didukung: JPG, PNG, GIF. Maks: 2MB</div>
                </div>

                <!-- Thread Status Options -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label d-block">Status Persetujuan</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_approved" id="approveYes" value="1" {{ $thread->is_approved ? 'checked' : '' }}>
                            <label class="form-check-label" for="approveYes">Disetujui</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_approved" id="approveNo" value="0" {{ !$thread->is_approved ? 'checked' : '' }}>
                            <label class="form-check-label" for="approveNo">Ditunda</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label d-block">Status Kunci</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_locked" id="lockYes" value="1" {{ $thread->is_locked ? 'checked' : '' }}>
                            <label class="form-check-label" for="lockYes">Terkunci</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_locked" id="lockNo" value="0" {{ !$thread->is_locked ? 'checked' : '' }}>
                            <label class="form-check-label" for="lockNo">Tidak Terkunci</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label d-block">Status Pin</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_pinned" id="pinYes" value="1" {{ $thread->is_pinned ? 'checked' : '' }}>
                            <label class="form-check-label" for="pinYes">Disematkan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_pinned" id="pinNo" value="0" {{ !$thread->is_pinned ? 'checked' : '' }}>
                            <label class="form-check-label" for="pinNo">Tidak Disematkan</label>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label d-block">Status Flag</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_flagged" id="flagYes" value="1" {{ $thread->is_flagged ? 'checked' : '' }}>
                            <label class="form-check-label" for="flagYes">Ditandai</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_flagged" id="flagNo" value="0" {{ !$thread->is_flagged ? 'checked' : '' }}>
                            <label class="form-check-label" for="flagNo">Tidak Ditandai</label>
                        </div>
                    </div>
                </div>

                <!-- Moderation Notes -->
                <div class="mb-3">
                    <label for="moderation_note" class="form-label">Catatan Moderasi (Tidak akan ditampilkan kepada pengguna)</label>
                    <textarea class="form-control" id="moderation_note" name="moderation_note" rows="3">{{ old('moderation_note', $thread->moderation_note) }}</textarea>
                </div>

                <!-- Notify User -->
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notify_user" name="notify_user" value="1" checked>
                        <label class="form-check-label" for="notify_user">
                            Kirim notifikasi ke penulis thread tentang perubahan ini
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>

                    <a href="{{ route('moderator.threads.index') }}" class="btn btn-secondary me-2">
                        Batal
                    </a>

                    <button type="button" class="btn btn-danger ms-auto" data-bs-toggle="modal" data-bs-target="#deleteThreadModal">
                        <i class="fas fa-trash me-1"></i> Hapus Thread
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Thread Modal -->
<div class="modal fade" id="deleteThreadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Thread</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus thread ini? Semua komentar terkait juga akan dihapus.</p>
                <div class="alert alert-warning">
                    <strong>{{ $thread->title }}</strong>
                    <p class="mb-0">Oleh: {{ $thread->user->name ?? 'User tidak ditemukan' }}</p>
                </div>
                <form id="deleteForm" action="{{ route('moderator.threads.destroy', $thread) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label for="delete_reason" class="form-label">Alasan penghapusan (opsional)</label>
                        <textarea class="form-control" id="delete_reason" name="reason" rows="2"
                                  placeholder="Thread ini melanggar peraturan..."></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="delete_notify_user" name="notify_user" value="1" checked>
                        <label class="form-check-label" for="delete_notify_user">
                            Kirim notifikasi ke penulis thread
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Include any JS for rich text editor if used -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize rich text editor if needed
        // $('#body').summernote({
        //     placeholder: 'Tulis konten thread di sini...',
        //     height: 300,
        //     toolbar: [
        //         ['style', ['style']],
        //         ['font', ['bold', 'underline', 'clear']],
        //         ['color', ['color']],
        //         ['para', ['ul', 'ol', 'paragraph']],
        //         ['table', ['table']],
        //         ['insert', ['link', 'picture']],
        //         ['view', ['fullscreen', 'codeview', 'help']]
        //     ]
        // });

        // Handle delete confirmation
        document.getElementById('confirmDelete').addEventListener('click', function() {
            document.getElementById('deleteForm').submit();
        });
    });
</script>
@endsection
