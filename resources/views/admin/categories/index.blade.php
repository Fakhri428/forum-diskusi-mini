<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/admin/categories/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Kelola Kategori')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.css">
<style>
    .category-item {
        cursor: move;
    }
    .category-color {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        display: inline-block;
    }
    .table th, .table td {
        vertical-align: middle;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola Kategori</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Tambah Kategori
        </a>
    </div>

    <!-- Filter & Search -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari kategori..." name="search" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="parent_id" class="form-select">
                        <option value="">Semua Kategori</option>
                        <option value="parent" {{ request('parent_id') == 'parent' ? 'selected' : '' }}>Hanya Kategori Utama</option>
                        <option value="child" {{ request('parent_id') == 'child' ? 'selected' : '' }}>Hanya Sub-kategori</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-grid">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Category List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold">Daftar Kategori</h6>
            <div>
                <button class="btn btn-sm btn-outline-primary me-2" id="reorderBtn" style="display: none;">
                    Simpan Urutan
                </button>
                <button class="btn btn-sm btn-outline-secondary" id="toggleReorderBtn">
                    <i class="fas fa-sort"></i> Ubah Urutan
                </button>
            </div>
        </div>
        <div class="card-body">
            @if($categories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50px">#</th>
                                <th width="50px">Warna</th>
                                <th>Nama</th>
                                <th>Deskripsi</th>
                                <th>Induk</th>
                                <th>Thread</th>
                                <th>Status</th>
                                <th width="150px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="categoryList">
                            @foreach($categories as $category)
                                <tr class="category-item" data-id="{{ $category->id }}" data-position="{{ $category->position ?? $loop->index }}">
                                    <td>{{ ($categories->currentpage()-1) * $categories->perpage() + $loop->index + 1 }}</td>
                                    <td>
                                        <div class="category-color" style="background-color: {{ $category->color ?? '#6c757d' }}"></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($category->icon)
                                                <i class="{{ $category->icon }} me-2"></i>
                                            @endif
                                            <span>{{ $category->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($category->description, 50) }}</td>
                                    <td>
                                        @if($category->parent)
                                            {{ $category->parent->name }}
                                        @else
                                            <span class="badge bg-secondary">Kategori Utama</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $category->threads_count }}</span>
                                    </td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-{{ $category->is_active ? 'warning' : 'success' }} btn-toggle-status" data-id="{{ $category->id }}" data-status="{{ $category->is_active ? 1 : 0 }}">
                                                <i class="fas fa-{{ $category->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-delete" data-id="{{ $category->id }}" data-name="{{ $category->name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $categories->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <img src="{{ asset('images/empty-data.svg') }}" alt="Tidak ada data" class="img-fluid mb-3" style="max-width: 200px;">
                    <h5>Tidak ada kategori yang ditemukan</h5>
                    <p class="text-muted">Mulai dengan membuat kategori baru untuk forum Anda.</p>
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Kategori
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kategori <strong id="delete-category-name"></strong>?</p>
                <p>Penghapusan ini tidak dapat dibatalkan. Kategori hanya dapat dihapus jika tidak memiliki thread atau sub-kategori.</p>
            </div>
            <div class="modal-footer">
                <form id="delete-form" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toggle Status Form -->
<form id="toggle-status-form" action="" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete confirmation
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-id');
                const categoryName = this.getAttribute('data-name');

                document.getElementById('delete-category-name').textContent = categoryName;
                document.getElementById('delete-form').setAttribute('action', `{{ url('admin/categories') }}/${categoryId}`);

                deleteModal.show();
            });
        });

        // Toggle status
        document.querySelectorAll('.btn-toggle-status').forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.getAttribute('data-id');
                const isActive = parseInt(this.getAttribute('data-status'));

                const message = isActive ? 'Nonaktifkan' : 'Aktifkan';

                if (confirm(`${message} kategori ini?`)) {
                    const form = document.getElementById('toggle-status-form');
                    form.setAttribute('action', `{{ url('admin/categories') }}/${categoryId}/toggle-active`);
                    form.submit();
                }
            });
        });

        // Reordering functionality
        const categoryList = document.getElementById('categoryList');
        const reorderBtn = document.getElementById('reorderBtn');
        const toggleReorderBtn = document.getElementById('toggleReorderBtn');
        let reorderMode = false;
        let drake;

        toggleReorderBtn.addEventListener('click', function() {
            reorderMode = !reorderMode;

            if (reorderMode) {
                this.textContent = 'Batalkan';
                this.classList.replace('btn-outline-secondary', 'btn-outline-danger');
                reorderBtn.style.display = 'inline-block';

                // Initialize dragula
                drake = dragula([categoryList]);
            } else {
                this.textContent = 'Ubah Urutan';
                this.classList.replace('btn-outline-danger', 'btn-outline-secondary');
                reorderBtn.style.display = 'none';

                // Destroy dragula
                if (drake) {
                    drake.destroy();
                }
            }
        });

        reorderBtn.addEventListener('click', function() {
            const categories = [];

            // Get all categories with their new positions
            document.querySelectorAll('#categoryList .category-item').forEach((item, index) => {
                categories.push({
                    id: item.getAttribute('data-id'),
                    position: index
                });
            });

            // Send AJAX request to update positions
            fetch('{{ route('admin.categories.reorder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ categories })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Urutan kategori berhasil disimpan');
                    location.reload();
                } else {
                    alert('Terjadi kesalahan saat menyimpan urutan');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan pada server');
            });
        });
    });
</script>
@endsection
