{{-- filepath: resources/views/admin/threads/index.blade.php --}}

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
            {{-- HAPUS target="_blank" untuk tetap di dashboard --}}
            <a href="{{ route('threads.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Buat Diskusi Baru
            </a>

            {{-- Atau buat route khusus admin untuk create thread --}}
            {{-- <a href="{{ route('admin.threads.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle mr-1"></i> Buat Diskusi Baru
            </a> --}}
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
                            <td>
                                {{-- Dropdown untuk pilihan view --}}
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <strong>{{ $thread->title }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ Str::limit($thread->body, 100) }}
                                        </small>
                                    </div>
                                    <div class="dropdown ms-2">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('threads.show', $thread) }}">
                                                    <i class="fas fa-eye me-1"></i> Lihat di Dashboard
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('threads.show', $thread) }}" target="_blank">
                                                    <i class="fas fa-external-link-alt me-1"></i> Buka Tab Baru
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $thread->category->name ?? '-' }}</td>
                            <td>{{ $thread->user->name }}</td>
                            <td>{{ $thread->created_at->format('d M Y H:i') }}</td>
                            <td>
                                @if($thread->is_approved)
                                    <span class="badge bg-success">Disetujui</span>
                                @else
                                    <span class="badge bg-warning">Belum Disetujui</span>
                                @endif

                                @if($thread->is_pinned)
                                    <span class="badge bg-info">Pinned</span>
                                @endif

                                @if($thread->is_locked)
                                    <span class="badge bg-secondary">Locked</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    {{-- Edit Button - HAPUS target="_blank" --}}
                                    <a href="{{ route('threads.edit', $thread) }}" class="btn btn-primary btn-sm" title="Edit Thread">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- Quick Actions Dropdown --}}
                                    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        {{-- View Options --}}
                                        <li>
                                            <a class="dropdown-item" href="{{ route('threads.show', $thread) }}">
                                                <i class="fas fa-eye text-info"></i> Lihat Thread
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('threads.show', $thread) }}" target="_blank">
                                                <i class="fas fa-external-link-alt text-primary"></i> Buka Tab Baru
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>

                                        {{-- Toggle Approval --}}
                                        <li>
                                            <form action="{{ route('admin.threads.toggle-approval', $thread) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="dropdown-item">
                                                    @if($thread->is_approved)
                                                        <i class="fas fa-times-circle text-warning"></i> Tolak
                                                    @else
                                                        <i class="fas fa-check-circle text-success"></i> Setujui
                                                    @endif
                                                </button>
                                            </form>
                                        </li>

                                        {{-- Toggle Pin --}}
                                        <li>
                                            <form action="{{ route('admin.threads.toggle-pinned', $thread) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="dropdown-item">
                                                    @if($thread->is_pinned)
                                                        <i class="fas fa-thumbtack text-muted"></i> Cabut Pin
                                                    @else
                                                        <i class="fas fa-thumbtack text-info"></i> Pasang Pin
                                                    @endif
                                                </button>
                                            </form>
                                        </li>

                                        {{-- Toggle Lock --}}
                                        <li>
                                            <form action="{{ route('admin.threads.toggle-locked', $thread) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="dropdown-item">
                                                    @if($thread->is_locked)
                                                        <i class="fas fa-unlock text-success"></i> Buka Kunci
                                                    @else
                                                        <i class="fas fa-lock text-warning"></i> Kunci
                                                    @endif
                                                </button>
                                            </form>
                                        </li>

                                        <li><hr class="dropdown-divider"></li>

                                        {{-- Delete --}}
                                        <li>
                                            <form action="{{ route('admin.threads.destroy', $thread) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus diskusi ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
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
            ],
            order: [[1, 'desc']], // Order by ID desc (newest first)
            pageLength: 25
        });

        // Select/Deselect all threads
        $('#selectAllThreads').on('click', function() {
            var checked = $(this).prop('checked');
            $('.threadCheckbox:visible').prop('checked', checked);
            toggleBatchActionButton();
        });

        // Individual thread checkbox change
        $(document).on('change', '.threadCheckbox', function() {
            var visibleCheckboxes = $('.threadCheckbox:visible');
            var allChecked = visibleCheckboxes.length === $('.threadCheckbox:visible:checked').length;
            $('#selectAllThreads').prop('checked', allChecked);
            toggleBatchActionButton();
        });

        // Toggle batch action button
        function toggleBatchActionButton() {
            var anyChecked = $('.threadCheckbox:checked').length > 0;
            $('#batchActionBtn').prop('disabled', !anyChecked);

            if (anyChecked) {
                $('#batchActionBtn').text('Terapkan (' + $('.threadCheckbox:checked').length + ')');
            } else {
                $('#batchActionBtn').text('Terapkan');
            }
        }

        // Process batch action form
        $('#batchActionForm').submit(function(e) {
            e.preventDefault();

            var action = $('#batchAction').val();
            if (!action) {
                alert('Pilih aksi yang ingin dilakukan.');
                return false;
            }

            // Get all checked threads
            var threadIds = [];
            $('.threadCheckbox:checked').each(function() {
                threadIds.push($(this).val());
            });

            if (threadIds.length === 0) {
                alert('Pilih setidaknya satu diskusi.');
                return false;
            }

            // Confirm action
            var actionText = $('#batchAction option:selected').text();
            if (!confirm(`Anda yakin ingin ${actionText.toLowerCase()} ${threadIds.length} diskusi yang dipilih?`)) {
                return false;
            }

            // Clear previous hidden inputs
            $('#selectedIdsContainer').empty();

            // Add thread IDs to form
            threadIds.forEach(function(id) {
                $('#selectedIdsContainer').append('<input type="hidden" name="thread_ids[]" value="' + id + '">');
            });

            // Submit form
            this.submit();
        });

        // Reset form after successful action
        @if(session('success'))
            $('.threadCheckbox').prop('checked', false);
            $('#selectAllThreads').prop('checked', false);
            $('#batchAction').val('');
            toggleBatchActionButton();
        @endif
    });
</script>
@endsection
