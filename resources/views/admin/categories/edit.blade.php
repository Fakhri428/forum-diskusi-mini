<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/admin/categories/edit.blade.php -->
@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-iconpicker@2.0.0/dist/css/bootstrap-iconpicker.min.css">
<style>
    .color-preview {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-block;
        margin-left: 15px;
        border: 1px solid #ddd;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Kategori</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Form Edit Kategori</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="parent_id" class="form-label">Induk Kategori</label>
                        <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                            <option value="">Tidak Ada (Kategori Utama)</option>
                            @foreach($allCategories as $parentCategory)
                                <option value="{{ $parentCategory->id }}" {{ old('parent_id', $category->parent_id) == $parentCategory->id ? 'selected' : '' }}>
                                    {{ $parentCategory->name }}
                                </option>
                                @foreach($parentCategory->children as $child)
                                    @if($child->id != $category->id) {{-- Exclude self --}}
                                        <option value="{{ $child->id }}" {{ old('parent_id', $category->parent_id) == $child->id ? 'selected' : '' }}>
                                            &nbsp;&nbsp;&nbsp;└ {{ $child->name }}
                                        </option>
                                    @endif
                                @endforeach
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Berikan deskripsi singkat tentang kategori ini untuk membantu pengguna memahami topik diskusi.
                    </small>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="icon" class="form-label">Icon (Font Awesome)</label>
                        <div class="input-group">
                            <button class="btn btn-outline-secondary" type="button" id="iconpicker"></button>
                            <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', $category->icon) }}">
                        </div>
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Pilih icon dari Font Awesome untuk kategori ini.
                        </small>
                    </div>

                    <div class="col-md-6">
                        <label for="color" class="form-label">Warna</label>
                        <div class="d-flex align-items-center">
                            <input type="color" class="form-control form-control-color" id="color" name="color" value="{{ old('color', $category->color ?? '#6c757d') }}">
                            <div class="color-preview" id="colorPreview" style="background-color: {{ old('color', $category->color ?? '#6c757d') }};"></div>
                        </div>
                        @error('color')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Pilih warna untuk kategori ini.
                        </small>
                    </div>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Kategori Aktif</label>
                    <small class="form-text text-muted d-block">
                        Kategori yang tidak aktif tidak akan ditampilkan kepada pengguna.
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

    <!-- Current Stats -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Informasi Kategori</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th>ID</th>
                            <td>{{ $category->id }}</td>
                        </tr>
                        <tr>
                            <th>Slug</th>
                            <td>{{ $category->slug }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Pada</th>
                            <td>{{ $category->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Diupdate</th>
                            <td>{{ $category->updated_at->format('d M Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th>Jumlah Thread</th>
                            <td>{{ $category->threads_count }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($category->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Urutan</th>
                            <td>{{ $category->position ?? 'Tidak diatur' }}</td>
                        </tr>
                        <tr>
                            <th>Sub-kategori</th>
                            <td>{{ $category->children->count() }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-iconpicker@2.0.0/dist/js/bootstrap-iconpicker.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Color preview
        const colorInput = document.getElementById('color');
        const colorPreview = document.getElementById('colorPreview');

        colorInput.addEventListener('input', function() {
            colorPreview.style.backgroundColor = this.value;
        });

        // Icon picker
        const iconInput = document.getElementById('icon');

        $('#iconpicker').iconpicker({
            align: 'left',
            arrowClass: 'btn-outline-secondary',
            arrowPrevIconClass: 'fas fa-angle-left',
            arrowNextIconClass: 'fas fa-angle-right',
            iconset: 'fontawesome5',
            cols: 10,
            rows: 5,
            selectedClass: 'btn-primary',
            unselectedClass: 'btn-outline-secondary'
        });

        $('#iconpicker').on('change', function(e) {
            iconInput.value = e.icon;
        });

        // Set initial icon if exists
        if (iconInput.value) {
            $('#iconpicker').iconpicker('setIcon', iconInput.value);
        }
    });
</script>
@endsection
