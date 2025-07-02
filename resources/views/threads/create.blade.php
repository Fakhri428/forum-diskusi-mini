<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/admin/threads/create.blade.php -->
@extends('layouts.app')

@section('title', 'Buat Thread Baru')

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

    .form-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }

    .form-body {
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

    .required-asterisk {
        color: #e53e3e;
    }

    .help-text {
        font-size: 0.875rem;
        color: #718096;
        margin-top: 5px;
    }

    .community-rules {
        background-color: #f7fafc;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
        border-left: 4px solid #667eea;
    }

    .rules-list {
        margin-bottom: 0;
        padding-left: 20px;
    }

    .rules-list li {
        margin-bottom: 8px;
        color: #4a5568;
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

    /* Fix for Summernote invalid state */
    .is-invalid + .note-editor {
        border-color: #dc3545;
    }

    .loading-spinner {
        display: none;
    }

    .loading-spinner.show {
        display: inline-block;
    }

    /* Tag input styling */
    .tag-input-container {
        position: relative;
    }

    .tag-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e2e8f0;
        border-top: none;
        border-radius: 0 0 8px 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        max-height: 200px;
        overflow-y: auto;
        display: none;
    }

    .tag-suggestion-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f1f5f9;
    }

    .tag-suggestion-item:hover {
        background-color: #f8fafc;
    }

    .tag-suggestion-item:last-child {
        border-bottom: none;
    }

    .tag-preview {
        margin-top: 10px;
    }

    .tag-preview .badge {
        margin-right: 5px;
        margin-bottom: 5px;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('threads.index') }}">Thread</a></li>
            <li class="breadcrumb-item active" aria-current="page">Buat Thread Baru</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="form-card">
                <!-- Header -->
                <div class="form-header">
                    <h1 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        Buat Thread Baru
                    </h1>
                    <p class="mb-0 mt-2 opacity-75">Bagikan topik menarik dengan komunitas</p>
                </div>

                <!-- Form Body -->
                <div class="form-body">
                    <!-- Community Rules -->
                    <div class="community-rules">
                        <h6><i class="fas fa-gavel me-2"></i>Aturan Komunitas</h6>
                        <ul class="rules-list">
                            <li>Gunakan bahasa yang sopan dan menghormati sesama member</li>
                            <li>Pastikan topik thread sesuai dengan kategori yang dipilih</li>
                            <li>Tidak diperkenankan spam, promosi berlebihan, atau konten ilegal</li>
                            <li>Gunakan judul yang deskriptif dan mudah dipahami</li>
                        </ul>
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

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('threads.store') }}" method="POST" enctype="multipart/form-data" id="threadForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <!-- Category Selection -->
                                <div class="mb-4">
                                    <label for="category_id" class="form-label">
                                        <i class="fas fa-folder me-2"></i>Kategori <span class="required-asterisk">*</span>
                                    </label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                        <option value="">Pilih Kategori Thread</option>
                                        @if(isset($categories) && $categories->count() > 0)
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}"
                                                        {{ old('category_id') == $category->id ? 'selected' : '' }}
                                                        data-description="{{ $category->description ?? '' }}">
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="" disabled>Tidak ada kategori tersedia</option>
                                        @endif
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
                                    <div class="tag-input-container">
                                        <input type="text"
                                               class="form-control @error('tags') is-invalid @enderror"
                                               id="tags"
                                               name="tags"
                                               value="{{ old('tags') }}"
                                               placeholder="Pisahkan dengan koma, contoh: programming, php, web"
                                               autocomplete="off">
                                        <div class="tag-suggestions" id="tagSuggestions"></div>
                                    </div>
                                    @error('tags')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="help-text">
                                        Tambahkan tag untuk memudahkan pencarian. Maksimal 5 tag, pisahkan dengan koma.
                                    </div>
                                    <!-- Tag Preview -->
                                    <div class="tag-preview" id="tagPreview"></div>
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
                                   value="{{ old('title') }}"
                                   required
                                   maxlength="255"
                                   placeholder="Tulis judul thread yang menarik dan deskriptif">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">
                                <span id="titleCounter">0</span>/255 karakter
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
                                      rows="10">{{ old('body') }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">
                                Tulis konten thread Anda dengan detail. Anda dapat menggunakan formatting, menambahkan link, dan menyisipkan gambar.
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="mb-4">
                            <label for="image" class="form-label">
                                <i class="fas fa-image me-2"></i>Upload Gambar (Opsional)
                            </label>
                            <input type="file"
                                   class="form-control @error('image') is-invalid @enderror"
                                   id="image"
                                   name="image"
                                   accept="image/jpeg,image/png,image/jpg,image/gif">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="help-text">
                                Upload gambar pendukung (JPG, PNG, GIF, maks. 2MB)
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

                        <!-- Terms Agreement -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input @error('agree_terms') is-invalid @enderror"
                                       type="checkbox"
                                       id="agree_terms"
                                       name="agree_terms"
                                       value="1"
                                       {{ old('agree_terms') ? 'checked' : '' }}>
                                <label class="form-check-label" for="agree_terms">
                                    Saya menyetujui <a href="#" data-bs-toggle="modal" data-bs-target="#rulesModal">aturan komunitas</a> dan bersedia bertanggung jawab atas konten yang saya posting <span class="required-asterisk">*</span>
                                </label>
                                @error('agree_terms')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <span class="loading-spinner spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        <i class="fas fa-paper-plane me-2"></i>Posting Thread
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-secondary" id="saveDraft">
                                        <i class="fas fa-save me-2"></i>Simpan Draft
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('threads.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Daftar Thread
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Rules Modal -->
<div class="modal fade" id="rulesModal" tabindex="-1" aria-labelledby="rulesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rulesModalLabel">
                    <i class="fas fa-gavel me-2"></i>Aturan Komunitas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Pedoman Umum:</h6>
                <ol>
                    <li><strong>Hormati sesama member</strong> - Gunakan bahasa yang sopan dan tidak menyinggung</li>
                    <li><strong>Posting di kategori yang tepat</strong> - Pastikan thread Anda sesuai dengan kategori</li>
                    <li><strong>Konten berkualitas</strong> - Berikan informasi yang bermanfaat dan relevan</li>
                    <li><strong>Tidak ada spam</strong> - Hindari posting berulang atau promosi berlebihan</li>
                    <li><strong>Patuhi hukum</strong> - Tidak diperkenankan konten ilegal atau melanggar hak cipta</li>
                </ol>

                <h6 class="mt-4">Konsekuensi Pelanggaran:</h6>
                <ul>
                    <li>Peringatan untuk pelanggaran ringan</li>
                    <li>Pembatasan akses untuk pelanggaran berulang</li>
                    <li>Banned permanen untuk pelanggaran berat</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
$(document).ready(function() {
    // Popular tags for suggestions
    const popularTags = [
        'programming', 'php', 'javascript', 'laravel', 'web-development',
        'database', 'mysql', 'css', 'html', 'react', 'vue', 'nodejs',
        'python', 'java', 'android', 'ios', 'mobile', 'api', 'backend',
        'frontend', 'ui-ux', 'design', 'tutorial', 'tips', 'help',
        'question', 'discussion', 'news', 'announcement', 'beginner'
    ];

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
        placeholder: 'Tulis isi thread Anda di sini...',
        callbacks: {
            onImageUpload: function(files) {
                console.log('Image uploaded:', files);
            }
        }
    });

    // Title character counter
    $('#title').on('input', function() {
        var length = $(this).val().length;
        $('#titleCounter').text(length);

        $('#titleCounter').removeClass('text-warning text-danger');
        if (length > 200) {
            $('#titleCounter').addClass('text-warning');
        }
        if (length > 240) {
            $('#titleCounter').removeClass('text-warning').addClass('text-danger');
        }
    });

    // Initialize counter on page load
    var initialLength = $('#title').val().length;
    $('#titleCounter').text(initialLength);

    // Tag handling
    function updateTagPreview() {
        const tagsValue = $('#tags').val();
        const preview = $('#tagPreview');

        if (tagsValue.trim() === '') {
            preview.empty();
            return;
        }

        const tags = tagsValue.split(',').map(tag => tag.trim()).filter(tag => tag !== '');
        const limitedTags = tags.slice(0, 5); // Limit to 5 tags

        let previewHtml = '';
        limitedTags.forEach(tag => {
            previewHtml += `<span class="badge bg-primary">${tag}</span>`;
        });

        if (tags.length > 5) {
            previewHtml += `<span class="badge bg-warning">+${tags.length - 5} lainnya (maks 5 tag)</span>`;
        }

        preview.html(previewHtml);
    }

    // Tag suggestions
    $('#tags').on('input', function() {
        const value = $(this).val();
        const lastCommaIndex = value.lastIndexOf(',');
        const currentTag = value.substring(lastCommaIndex + 1).trim().toLowerCase();

        updateTagPreview();

        if (currentTag.length >= 2) {
            const suggestions = popularTags.filter(tag =>
                tag.toLowerCase().includes(currentTag) &&
                !value.toLowerCase().includes(tag.toLowerCase())
            ).slice(0, 5);

            if (suggestions.length > 0) {
                let suggestionsHtml = '';
                suggestions.forEach(tag => {
                    suggestionsHtml += `<div class="tag-suggestion-item" data-tag="${tag}">${tag}</div>`;
                });

                $('#tagSuggestions').html(suggestionsHtml).show();
            } else {
                $('#tagSuggestions').hide();
            }
        } else {
            $('#tagSuggestions').hide();
        }
    });

    // Handle tag suggestion click
    $(document).on('click', '.tag-suggestion-item', function() {
        const selectedTag = $(this).data('tag');
        const currentValue = $('#tags').val();
        const lastCommaIndex = currentValue.lastIndexOf(',');

        let newValue;
        if (lastCommaIndex === -1) {
            newValue = selectedTag;
        } else {
            newValue = currentValue.substring(0, lastCommaIndex + 1) + ' ' + selectedTag;
        }

        $('#tags').val(newValue + ', ').focus();
        $('#tagSuggestions').hide();
        updateTagPreview();
    });

    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.tag-input-container').length) {
            $('#tagSuggestions').hide();
        }
    });

    // Image preview functionality
    $('#image').on('change', function() {
        const file = this.files[0];
        if (file) {
            // Validate file size
            if (file.size > 2048000) { // 2MB in bytes
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                $(this).val('');
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Format file tidak didukung. Gunakan JPG, PNG, atau GIF.');
                $(this).val('');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#imagePreview').removeClass('d-none');
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

        if (description && description !== '') {
            $(this).parent().append('<div class="category-description help-text mt-1"><i class="fas fa-info-circle me-1"></i>' + description + '</div>');
        }
    });

    // Form validation
    $('#threadForm').on('submit', function(e) {
        let isValid = true;

        // Show loading spinner
        $('#submitBtn .loading-spinner').addClass('show');
        $('#submitBtn').prop('disabled', true);

        // Check title
        if ($('#title').val().trim() === '') {
            isValid = false;
            $('#title').addClass('is-invalid');
        } else {
            $('#title').removeClass('is-invalid');
        }

        // Check category
        if ($('#category_id').val() === '' || $('#category_id').val() === null) {
            isValid = false;
            $('#category_id').addClass('is-invalid');
        } else {
            $('#category_id').removeClass('is-invalid');
        }

        // Check body content
        const bodyContent = $('.summernote').summernote('code').trim();
        if (bodyContent === '' || bodyContent === '<p><br></p>' || bodyContent === '<p></p>') {
            isValid = false;
            $('#body').addClass('is-invalid');

            if (!$('.body-error').length) {
                $('.summernote').parent().append('<div class="body-error invalid-feedback d-block">Isi thread harus diisi.</div>');
            }
        } else {
            $('#body').removeClass('is-invalid');
            $('.body-error').remove();
        }

        // Check terms agreement
        if (!$('#agree_terms').is(':checked')) {
            isValid = false;
            $('#agree_terms').addClass('is-invalid');
        } else {
            $('#agree_terms').removeClass('is-invalid');
        }

        // Validate tags (max 5)
        const tagsValue = $('#tags').val().trim();
        if (tagsValue !== '') {
            const tags = tagsValue.split(',').map(tag => tag.trim()).filter(tag => tag !== '');
            if (tags.length > 5) {
                isValid = false;
                $('#tags').addClass('is-invalid');
                if (!$('.tags-error').length) {
                    $('#tags').parent().append('<div class="tags-error invalid-feedback d-block">Maksimal 5 tag diperbolehkan.</div>');
                }
            } else {
                $('#tags').removeClass('is-invalid');
                $('.tags-error').remove();
            }
        }

        if (!isValid) {
            e.preventDefault();

            // Hide loading spinner
            $('#submitBtn .loading-spinner').removeClass('show');
            $('#submitBtn').prop('disabled', false);

            // Scroll to first error
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 500);
        } else {
            // Clear draft on successful submission
            localStorage.removeItem('thread_draft');
        }
    });

    // Auto-save draft functionality
    let autoSaveTimer;
    $('#threadForm input, #threadForm textarea, #threadForm select').on('input change', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(function() {
            saveDraft(false); // Auto save without notification
        }, 30000); // Auto-save every 30 seconds
    });

    // Manual save draft
    $('#saveDraft').on('click', function() {
        saveDraft(true); // Manual save with notification
    });

    function saveDraft(showNotification = true) {
        const draftData = {
            title: $('#title').val(),
            body: $('.summernote').summernote('code'),
            category_id: $('#category_id').val(),
            tags: $('#tags').val(),
            timestamp: new Date().toISOString()
        };

        // Save to localStorage
        localStorage.setItem('thread_draft', JSON.stringify(draftData));

        if (showNotification) {
            // Show notification
            const button = $('#saveDraft');
            const originalText = button.html();
            button.html('<i class="fas fa-check me-2"></i>Draft Tersimpan');
            button.removeClass('btn-secondary').addClass('btn-success');

            setTimeout(function() {
                button.html(originalText);
                button.removeClass('btn-success').addClass('btn-secondary');
            }, 2000);
        }
    }

    // Load draft on page load
    function loadDraft() {
        const draft = localStorage.getItem('thread_draft');
        if (draft) {
            try {
                const draftData = JSON.parse(draft);

                // Check if draft is not too old (24 hours)
                const draftTime = new Date(draftData.timestamp);
                const now = new Date();
                const hoursDiff = (now - draftTime) / (1000 * 60 * 60);

                if (hoursDiff < 24) {
                    $('#title').val(draftData.title || '');
                    $('.summernote').summernote('code', draftData.body || '');
                    $('#category_id').val(draftData.category_id || '');
                    $('#tags').val(draftData.tags || '');

                    // Update character counter and tag preview
                    $('#titleCounter').text((draftData.title || '').length);
                    updateTagPreview();

                    // Show notification that draft was loaded
                    if (draftData.title || draftData.body) {
                        console.log('Draft loaded from localStorage');
                    }
                } else {
                    // Remove old draft
                    localStorage.removeItem('thread_draft');
                }
            } catch (e) {
                console.error('Error loading draft:', e);
                localStorage.removeItem('thread_draft');
            }
        }
    }

    // Load draft if exists
    loadDraft();
});
</script>
@endsection
