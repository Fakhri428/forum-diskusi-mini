<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/admin/threads/create.blade.php -->
@extends('layouts.admin')

@section('title', 'Tambah Thread')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<style>
    .note-editor.note-frame {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .note-editor .note-toolbar {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Thread</h1>
        <a href="{{ route('admin.threads.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Form Tambah Thread</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.threads.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="title" class="form-label">Judul Thread <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Buat judul yang informatif dan menarik (maks. 255 karakter).
                    </small>
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="body" class="form-label">Konten Thread <span class="text-danger">*</span></label>
                    <textarea class="form-control summernote @error('body') is-invalid @enderror" id="body" name="body" rows="10">{{ old('body') }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_approved" name="is_approved" value="1" {{ old('is_approved', '1') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_approved">Thread Disetujui</label>
                            <small class="form-text text-muted d-block">
                                Thread yang tidak disetujui tidak akan terlihat oleh pengguna biasa.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_pinned" name="is_pinned" value="1" {{ old('is_pinned') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_pinned">Thread Disematkan</label>
                            <small class="form-text text-muted d-block">
                                Thread yang disematkan akan muncul di bagian atas daftar thread.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_locked" name="is_locked" value="1" {{ old('is_locked') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_locked">Thread Dikunci</label>
                            <small class="form-text text-muted d-block">
                                Thread yang dikunci tidak bisa menerima komentar baru.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Thread
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('.summernote').summernote({
            placeholder: 'Tulis konten thread di sini...',
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'italic', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onImageUpload: function(files) {
                    // Custom image upload logic could be added here
                    let editor = $(this);
                    let file = files[0];

                    alert('Fitur upload gambar langsung tidak tersedia. Gunakan tautan gambar eksternal.');
                }
            }
        });
    });
</script>
@endsection
