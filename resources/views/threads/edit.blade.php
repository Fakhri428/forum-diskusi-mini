@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Thread</h2>

    {{-- Tampilkan error jika ada --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('threads.update', $thread->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">Judul Thread</label>
            <input type="text" name="title" id="title" class="form-control"
                value="{{ old('title', $thread->title) }}" required>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">Isi Thread</label>
            <textarea name="content" id="content" class="form-control" rows="5" required>{{ old('content', $thread->content) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="{{ route('threads.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
