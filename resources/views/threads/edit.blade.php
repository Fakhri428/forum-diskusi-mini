@extends('layouts.app')

@section('title', 'Edit Thread: ' . $thread->title)

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

    .edit-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .edit-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }

    .edit-body {
        padding: 40px;
        background-color: #ffffff;
    }

    .form-label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 8px;
    }

    .form-control, .form-select {
        border-radius: 8px;
        padding: 12px 15px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background-color: #6c757d;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        transform: translateY(-2px);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
    }

    .required-asterisk {
        color: #e53e3e;
    }

    .help-text {
        font-size: 0.875rem;
        color: #718096;
        margin-top: 5px;
    }

    .thread-info {
        background-color: #f7fafc;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        border-left: 4px solid #667eea;
    }

    .current-image {
        max-width: 300px;
        border-radius: 8px;
        margin-bottom: 15px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .image-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 15px;
    }

    .change-log {
        background-color: #fff8e1;
        border: 1px solid #ffcc02;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }

    .breadcrumb {
        background-color: transparent;
        padding: 0;
        margin-bottom: 20px;
    }

    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        font-size: 16px;
        color: #6c757d;
    }

    .breadcrumb-item a {
        color: #667eea;
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    .alert {
        border-radius: 8px;
        border: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }

    .image-preview-container {
        position: relative;
        display: inline-block;
        margin-top: 10px;
    }

    .remove-preview-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: rgba(255, 255, 255, 0.9);
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('threads.index') }}">Thread</a></li>
            <li class="breadcrumb-item"><a href="{{ route('threads.show', $thread) }}">{{ Str::limit($thread->title, 30) }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="edit-card">
                <!-- Header -->
                <div class="edit-header">
                    <h1 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Edit Thread
                    </h1>
                    <p class="mb-0 mt-2 opacity-75">Perbarui informasi thread Anda</p>
                </div>

                <!-- Form Body -->
                <div class="edit-body">
                    <!-- Thread Info -->
                    <div class="thread-info">
                        <h6><i class="fas fa-info-circle me-2"></i>Informasi Thread</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Dibuat:</strong> {{ $thread->created_at->format('d M Y H:i') }}</p>
                                <p class="mb-1"><strong>Kategori:</strong> {{ $thread->category->name ?? 'Tidak ada kategori' }}</p>
                                <p class="mb-0"><strong>Status:</strong>
                                    @if($thread->is_approved)
                                        <span class="badge bg-success">Disetujui</span>
                                    @else
                                        <span class="badge bg-warning">Menunggu Persetujuan</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Dilihat:</strong> {{ $thread->views_count ?? 0 }} kali</p>
                                <p class="mb-1"><strong>Komentar:</strong> {{ $thread->comments->count() }}</p>
                                <p class="mb-0"><strong>Terakhir diperbarui:</strong> {{ $thread->updated_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Terdapat kesalahan:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('threads.update', $thread) }}" method="POST" enctype="multipart/form-data" id="editForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Category Selection -->
                                <div class="mb-4">
                                    <label for="category_id" class="form-label">
                                        <i class="fas fa-folder me-2"></i>Kategori <span class="required-asterisk">*</span>
                                    </label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                        <option value="">Pilih Kategori Thread</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                    {{ old('category_id', $thread->category_id) == $category->id ? 'selected' : '' }}
                                                    data-description="{{ $category->description ?? '' }}">
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="help-text">Pilih kategori yang paling sesuai dengan topik thread Anda</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Tags -->
                                <div class="mb-4">
                                    <label for="tags" class="form-label">
                                        <i class="fas fa-tags me-2"></i>Tag (Opsional)
                                    </label>
                                    <input type="text"
                                           class="form-control"
                                           id="tags"
                                           name="tags"
                                           value="{{ old('tags', $thread->tags) }}"
                                           placeholder="Pisahkan dengan koma, contoh: programming, php, web">
                                    <div class="help-text">
                                        Update tag untuk memudahkan pencarian. Maksimal 5 tag, pisahkan dengan koma.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thread Title -->
                        <div class="mb-4">
                            <label for="title" class="form-label">
                                <i class="fas fa-heading me-2"></i>Judul Thread <span class="required-asterisk">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title"
                                   name="title"
                                   value="{{ old('title', $thread->title) }}"
                                   required
                                   maxlength="255"
                                   placeholder="Tulis judul thread yang menarik dan deskriptif">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">
                                <span id="titleCounter">{{ strlen($thread->title) }}</span>/255 karakter
                            </div>
                        </div>

                        <!-- Thread Body -->
                        <div class="mb-4">
                            <label for="body" class="form-label">
                                <i class="fas fa-edit me-2"></i>Isi Thread <span class="required-asterisk">*</span>
                            </label>
                            <textarea class="form-control summernote @error('body') is-invalid @enderror"
                                      id="body"
                                      name="body"
                                      rows="10">{{ old('body', $thread->body) }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">
                                Perbarui konten thread Anda. Anda dapat menggunakan formatting, menambahkan link, dan menyisipkan gambar.
                            </div>
                        </div>

                        <!-- Current Image Display -->
                        @if($thread->image)
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-image me-2"></i>Gambar Saat Ini
                                </label>
                                <div>
                                    <img src="{{ asset($thread->image) }}" alt="Current thread image" class="current-image img-fluid">
                                    <div class="image-actions">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image" value="1">
                                            <label class="form-check-label" for="remove_image">
                                                <i class="fas fa-trash-alt me-1"></i> Hapus gambar ini
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Image Upload -->
                        <div class="mb-4">
                            <label for="image" class="form-label">
                                <i class="fas fa-image me-2"></i>{{ $thread->image ? 'Ganti Gambar' : 'Upload Gambar' }} (Opsional)
                            </label>
                            <input type="file"
                                   class="form-control @error('image') is-invalid @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">
                                Upload gambar baru (JPG, PNG, GIF, maks. 2MB)
                            </div>

                            <!-- Image Preview -->
                            <div id="imagePreview" class="d-none">
                                <div class="image-preview-container">
                                    <img id="previewImg" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                    <button type="button" class="remove-preview-btn" id="removePreview">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Change Log -->
                        <div class="change-log">
                            <h6><i class="fas fa-history me-2"></i>Catatan Perubahan (Opsional)</h6>
                            <textarea class="form-control" name="change_log" id="change_log" rows="3" placeholder="Catatan tentang perubahan yang Anda buat pada thread ini...">{{ old('change_log') }}</textarea>
                            <div class="help-text">
                                Berikan catatan singkat tentang perubahan yang Anda lakukan, jika perlu.
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('threads.show', $thread) }}" class="btn btn-secondary">
                                        <i class="fas fa-eye me-2"></i>Lihat Thread
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('threads.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-list me-2"></i>Kembali ke Daftar Thread
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="fas fa-trash-alt me-2"></i>Hapus Thread
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>Konfirmasi Hapus Thread
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus thread ini?</p>
                <div class="alert alert-warning">
                    <strong>Perhatian:</strong> Tindakan ini tidak dapat dibatalkan. Semua komentar yang terkait dengan thread ini juga akan dihapus.
                </div>
                <div class="bg-light p-3 rounded">
                    <strong>{{ $thread->title }}</strong>
                    <br>
                    <small class="text-muted">Dibuat: {{ $thread->created_at->format('d M Y H:i') }}</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('threads.destroy', $thread) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-1"></i>Ya, Hapus Thread
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Summernote
        $('.summernote').summernote({
            height: 300,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            placeholder: 'Perbarui isi thread Anda di sini...',
            callbacks: {
                onImageUpload: function(files) {
                    // Handle image upload if needed
                    console.log('Image uploaded:', files);
                }
            }
        });

        // Title character counter
        $('#title').on('input', function() {
            var length = $(this).val().length;
            $('#titleCounter').text(length);

            if (length > 200) {
                $('#titleCounter').addClass('text-warning');
            } else if (length > 240) {
                $('#titleCounter').addClass('text-danger');
            } else {
                $('#titleCounter').removeClass('text-warning text-danger');
            }
        });

        // Image preview functionality
        $('#image').on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImg').attr('src', e.target.result);
                    $('#imagePreview').removeClass('d-none');
                    // Hide remove current image checkbox when new image is selected
                    $('#remove_image').prop('checked', false);
                };
                reader.readAsDataURL(file);
            }
        });

        // Remove preview functionality
        $('#removePreview').on('click', function() {
            $('#image').val('');
            $('#imagePreview').addClass('d-none');
            $('#previewImg').attr('src', '');
        });

        // Category description display
        $('#category_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const description = selectedOption.data('description');

            // Remove existing description
            $('.category-description').remove();

            if (description) {
                $(this).parent().append('<div class="category-description help-text mt-1"><i class="fas fa-info-circle me-1"></i>' + description + '</div>');
            }
        });

        // Form validation
        $('#editForm').on('submit', function(e) {
            let isValid = true;

            // Check title
            if ($('#title').val().trim() === '') {
                isValid = false;
                $('#title').addClass('is-invalid');
            } else {
                $('#title').removeClass('is-invalid');
            }

            // Check category
            if ($('#category_id').val() === '') {
                isValid = false;
                $('#category_id').addClass('is-invalid');
            } else {
                $('#category_id').removeClass('is-invalid');
            }

            // Check body content
            if ($('.summernote').summernote('code').trim() === '' || $('.summernote').summernote('code') === '<p><br></p>') {
                isValid = false;
                $('.summernote').next('.note-editor').addClass('is-invalid');

                if (!$('.body-error').length) {
                    $('.summernote').parent().append('<div class="body-error invalid-feedback d-block">Isi thread harus diisi.</div>');
                }
            } else {
                $('.summernote').next('.note-editor').removeClass('is-invalid');
                $('.body-error').remove();
            }

            if (!isValid) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $('.is-invalid').first().offset().top - 100
                }, 500);
            }
        });

        // Auto-save draft functionality (optional)
        let autoSaveTimer;
        $('#editForm input, #editForm textarea, #editForm select').on('input change', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                // Auto-save logic here if needed
                console.log('Auto-saving draft...');
            }, 10000); // Auto-save every 10 seconds
        });
    });
</script>
@endsection
