<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/admin/threads/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Daftar Diskusi')

@section('styles')
<link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
<style>
    .badge {
        display: inline-block;
        padding: 0.25em 0.4em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: 0.25rem;
    }
    .bg-success {
        background-color: #28a745!important;
        color: white;
    }
    .bg-warning {
        background-color: #ffc107!important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Diskusi</h1>
        <div>
            <a href="{{ route('admin.threads.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Buat Diskusi Baru
            </a>
        </div>
    </div>

    <!-- Batch Action Form -->
    <form action="{{ route('admin.threads.batch-action') }}" method="POST" id="batchActionForm" class="d-flex align-items-center mb-4">
        @csrf
        <select name="action" class="form-select form-select-sm me-2" id="batchAction" style="width: auto;">
            <option value="">-- Pilih Aksi --</option>
            <option value="delete">Hapus Diskusi</option>
            <option value="pin">Pasang Pin</option>
            <option value="unpin">Cabut Pin</option>
            <option value="lock">Kunci Diskusi</option>
            <option value="unlock">Buka Kunci</option>
            <option value="approve">Setujui</option>
            <option value="reject">Tolak</option>
        </select>

        <button type="submit" class="btn btn-primary btn-sm" id="batchActionBtn" disabled>
            Terapkan
        </button>

        <!-- Container untuk ID yang dipilih -->
        <div id="selectedIdsContainer"></div>
    </form>

    <!-- Data Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Daftar Diskusi</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="threadsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAllThreads">
                            </th>
                            <th>ID</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Penulis</th>
                            <th>Dibuat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($threads as $thread)
                        <tr>
                            <td>
                                <input type="checkbox" class="threadCheckbox" value="{{ $thread->id }}">
                            </td>
                            <td>{{ $thread->id }}</td>
                            <td>{{ $thread->title }}</td>
                            <td>{{ $thread->category->name ?? '-' }}</td>
                            <td>{{ $thread->user->name }}</td>
                            <td>{{ $thread->created_at->format('d M Y H:i') }}</td>
                            <td>
                                @if($thread->is_approved)
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-warning">Belum Disetujui</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.threads.show', $thread->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.threads.edit', $thread->id) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.threads.destroy', $thread->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Anda yakin ingin menghapus diskusi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#threadsTable').DataTable({
            columnDefs: [
                { orderable: false, targets: 0 }, // Disable sorting on checkbox column
                { searchable: false, targets: 0 } // Disable searching on checkbox column
            ]
        });

        // Select/Deselect all threads
        $('#selectAllThreads').on('click', function() {
            var checked = $(this).prop('checked');
            $('.threadCheckbox').prop('checked', checked);
            toggleBatchActionButton();
        });

        // Individual thread checkbox change
        $('.threadCheckbox').on('change', function() {
            var allChecked = $('.threadCheckbox:checked').length === $('.threadCheckbox').length;
            $('#selectAllThreads').prop('checked', allChecked);
            toggleBatchActionButton();
        });

        // Toggle batch action button
        function toggleBatchActionButton() {
            var anyChecked = $('.threadCheckbox:checked').length > 0;
            $('#batchActionBtn').prop('disabled', !anyChecked);
        }

        // Process batch action form
        $('#batchActionForm').submit(function(e) {
            e.preventDefault();

            // Get all checked threads
            var threadIds = [];
            $('.threadCheckbox:checked').each(function() {
                threadIds.push($(this).val());
            });

            if (threadIds.length === 0) {
                alert('Pilih setidaknya satu diskusi.');
                return false;
            }

            // Add thread IDs to form
            threadIds.forEach(function(id) {
                $('#selectedIdsContainer').append('<input type="hidden" name="thread_ids[]" value="' + id + '">');
            });

            // Submit form
            this.submit();
        });
    });
</script>
@endsection
