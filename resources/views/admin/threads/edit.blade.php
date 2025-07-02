<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/admin/threads/edit.blade.php -->
@extends('layouts.admin')

@section('title', 'Edit Diskusi')

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
        <h1 class="h3 mb-0 text-gray-800">Edit Diskusi</h1>
        <a href="{{ route('admin.threads.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Diskusi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.threads.update', $thread->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label">Judul Diskusi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                           id="title" name="title" value="{{ old('title', $thread->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select class="form-control @error('category_id') is-invalid @enderror"
                            id="category_id" name="category_id" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $thread->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Konten Diskusi <span class="text-danger">*</span></label>
                    <textarea class="summernote @error('content') is-invalid @enderror"
                              id="content" name="content">{{ old('content', $thread->content) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_pinned" name="is_pinned"
                                   value="1" {{ old('is_pinned', $thread->is_pinned) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_pinned">
                                Pin Diskusi
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_locked" name="is_locked"
                                   value="1" {{ old('is_locked', $thread->is_locked) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_locked">
                                Kunci Diskusi
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_approved" name="is_approved"
                                   value="1" {{ old('is_approved', $thread->is_approved) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_approved">
                                Setujui Diskusi
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Summernote
        $('.summernote').summernote({
            placeholder: 'Tulis konten diskusi di sini...',
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'italic', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });
</script>
@endsection

