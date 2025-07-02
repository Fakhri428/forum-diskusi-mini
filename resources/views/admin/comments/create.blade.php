<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/admin/comments/create.blade.php -->
@extends('layouts.admin')

@section('title', 'Tambah Komentar')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Komentar</h1>
        <a href="{{ route('admin.comments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Tambah Komentar</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.comments.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="thread_id" class="form-label">Thread <span class="text-danger">*</span></label>
                    <select class="form-select @error('thread_id') is-invalid @enderror" id="thread_id" name="thread_id" required>
                        <option value="">Pilih Thread</option>
                        @foreach($threads as $thread)
                            <option value="{{ $thread->id }}" {{ old('thread_id', request('thread_id')) == $thread->id ? 'selected' : '' }}>
                                {{ $thread->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('thread_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="user_id" class="form-label">Penulis <span class="text-danger">*</span></label>
                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                        <option value="">Pilih Pengguna</option>
                        <option value="{{ auth()->id() }}" {{ old('user_id') == auth()->id() ? 'selected' : '' }}>
                            Saya ({{ auth()->user()->name }})
                        </option>

                        <optgroup label="Pengguna Lain">
                            @foreach($users as $user)
                                @if($user->id != auth()->id())
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endif
                            @endforeach
                        </optgroup>
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="parent_id" class="form-label">Komentar Induk (Opsional)</label>
                    <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                        <option value="">Tidak Ada (Komentar Utama)</option>
                        <option value="placeholder" disabled>Pilih Thread terlebih dahulu untuk melihat komentar yang tersedia</option>
                    </select>
                    @error('parent_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        Jika komentar ini adalah balasan dari komentar lain, pilih komentar induknya.
                    </small>
                </div>

                <div class="mb-3">
                    <label for="body" class="form-label">Isi Komentar <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="5" required>{{ old('body') }}</textarea>
                    @error('body')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="is_approved" name="is_approved" value="1" {{ old('is_approved', '1') == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_approved">Komentar Disetujui</label>
                    <small class="form-text text-muted d-block">
                        Komentar yang tidak disetujui tidak akan terlihat oleh pengguna biasa.
                    </small>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Komentar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const threadSelect = document.getElementById('thread_id');
        const parentCommentSelect = document.getElementById('parent_id');

        // Load comments when thread is selected
        threadSelect.addEventListener('change', function() {
            const threadId = this.value;

            if (threadId) {
                // Clear current options
                parentCommentSelect.innerHTML = '<option value="">Tidak Ada (Komentar Utama)</option>';

                // Add loading option
                const loadingOption = document.createElement('option');
                loadingOption.disabled = true;
                loadingOption.selected = true;
                loadingOption.textContent = 'Memuat komentar...';
                parentCommentSelect.appendChild(loadingOption);

                // Fetch comments for the selected thread
                fetch(`{{ url('admin/comments/get-by-thread') }}/${threadId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Remove loading option
                        parentCommentSelect.removeChild(loadingOption);

                        // Add comments as options
                        if (data.length > 0) {
                            data.forEach(comment => {
                                const option = document.createElement('option');
                                option.value = comment.id;
                                option.textContent = `${comment.user.name}: ${comment.body.substring(0, 50)}${comment.body.length > 50 ? '...' : ''}`;
                                parentCommentSelect.appendChild(option);
                            });
                        } else {
                            const noCommentsOption = document.createElement('option');
                            noCommentsOption.disabled = true;
                            noCommentsOption.textContent = 'Tidak ada komentar pada thread ini';
                            parentCommentSelect.appendChild(noCommentsOption);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        parentCommentSelect.innerHTML = '<option value="">Tidak Ada (Komentar Utama)</option><option disabled>Terjadi kesalahan saat memuat komentar</option>';
                    });
            } else {
                // Reset to default if no thread selected
                parentCommentSelect.innerHTML = '<option value="">Tidak Ada (Komentar Utama)</option><option value="placeholder" disabled>Pilih Thread terlebih dahulu untuk melihat komentar yang tersedia</option>';
            }
        });

        // Trigger change event if thread is pre-selected
        if (threadSelect.value) {
            const event = new Event('change');
            threadSelect.dispatchEvent(event);
        }
    });
</script>
@endsection
