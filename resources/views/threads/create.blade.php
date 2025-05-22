@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Buat Thread Baru</h1>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('threads.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Judul Thread</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
        </div>

        <div class="mb-3">
            <label for="body" class="form-label">Isi Thread</label>
            <textarea class="form-control" id="body" name="body" rows="10" required>{{ old('body') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Buat Thread</button>
        <a href="{{ route('threads.index') }}" class="btn btn-secondary ms-2">Batal</a>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('tinymce/tinymce.min.js') }}"></script>
<script>
  tinymce.init({
    selector: '#body',
    menubar: false,
    plugins: 'lists link image preview',
    toolbar: 'undo redo | formatselect | bold italic underline | bullist numlist | link image | preview',
    branding: false,
    height: 300,
  });
</script>
@endpush

