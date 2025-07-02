@extends('layouts.moderator')

@section('title', 'Kelola Laporan')

@section('styles')
<style>
    .report-content {
        max-height: 80px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
    }
    .report-reason {
        font-style: italic;
        color: #6c757d;
    }
    .status-badge {
        border-radius: 20px;
        font-weight: normal;
    }
    .filter-card {
        border-radius: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola Laporan</h1>
    </div>

    <!-- Filter and Search -->
    <div class="card shadow-sm filter-card mb-4">
        <div class="card-body">
            <form action="{{ route('moderator.reports.index') }}" method="GET" class="row g-3">
                <div class="col-lg-3">
                    <select class="form-select" name="status">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <select class="form-select" name="type">
                        <option value="">Semua Tipe</option>
                        <option value="thread" {{ request('type') === 'thread' ? 'selected' : '' }}>Thread</option>
                        <option value="comment" {{ request('type') === 'comment' ? 'selected' : '' }}>Komentar</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <select class="form-select" name="sort_by">
                        <option value="created_at" {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>Tanggal Dibuat</option>
                        <option value="updated_at" {{ request('sort_by') === 'updated_at' ? 'selected' : '' }}>Tanggal Diperbarui</option>
                    </select>
                </div>
                <div class="col-lg-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Batch Action Form -->
    <form id="batchForm" action="{{ route('moderator.reports.batch-update') }}" method="POST">
        @csrf
        <input type="hidden" name="action" id="batchAction">
        <input type="hidden" name="resolution" id="batchResolution">

        <!-- Data Table -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Laporan</h5>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="batchActionDropdown" data-bs-toggle="dropdown" aria-expanded="false" disabled>
                            <i class="fas fa-cogs me-1"></i> Aksi Masal
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="batchActionDropdown">
                            <li><h6 class="dropdown-header">Tindakan</h6></li>
                            <li><button type="button" class="dropdown-item batch-action-btn" data-action="approve" data-resolution="delete">
                                <i class="fas fa-trash me-2 text-danger"></i>Hapus Konten
                            </button></li>
                            <li><button type="button" class="dropdown-item batch-action-btn" data-action="approve" data-resolution="flag">
                                <i class="fas fa-flag me-2 text-warning"></i>Tandai Konten
                            </button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button type="button" class="dropdown-item batch-action-btn" data-action="reject" data-resolution="no_action">
                                <i class="fas fa-times me-2 text-secondary"></i>Tolak Laporan
                            </button></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th>Konten yang Dilaporkan</th>
                                <th>Alasan</th>
                                <th>Pelapor</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $report)
                            <tr>
                                <td class="px-4">
                                    <div class="form-check">
                                        <input class="form-check-input report-checkbox" type="checkbox" name="report_ids[]" value="{{ $report->id }}" {{ $report->status !== 'pending' ? 'disabled' : '' }}>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="mb-1">
                                            <span class="badge {{ $report->reportable_type === 'App\\Models\\Thread' ? 'bg-primary' : 'bg-info' }}">
                                                {{ $report->reportable_type === 'App\\Models\\Thread' ? 'Thread' : 'Komentar' }}
                                            </span>
                                        </div>
                                        @if($report->reportable)
                                            <div class="report-content">
                                                @if($report->reportable_type === 'App\\Models\\Thread')
                                                    <strong>{{ $report->reportable->title }}</strong>
                                                    <br>{{ Str::limit(strip_tags($report->reportable->body), 100) }}
                                                @else
                                                    {{ $report->reportable->body }}
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Konten tidak tersedia (mungkin sudah dihapus)</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="report-reason">{{ $report->reason }}</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2 bg-secondary text-white rounded-circle text-center d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">
                                            {{ strtoupper(substr($report->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>{{ $report->user->name ?? 'User Tidak Ditemukan' }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($report->status === 'pending')
                                        <span class="badge status-badge bg-warning">Menunggu</span>
                                    @elseif($report->status === 'approved')
                                        <span class="badge status-badge bg-success">Disetujui</span>
                                    @else
                                        <span class="badge status-badge bg-secondary">Ditolak</span>
                                    @endif
                                </td>
                                <td>{{ $report->created_at->format('d M Y H:i') }}</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="{{ route('moderator.reports.show', $report) }}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Detail Laporan">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if($report->status === 'pending')
                                            <button type="button" class="btn btn-sm btn-outline-success single-action" data-action="approve" data-id="{{ $report->id }}" data-bs-toggle="tooltip" title="Setujui">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger single-action" data-action="reject" data-id="{{ $report->id }}" data-bs-toggle="tooltip" title="Tolak">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif

                                        @if($report->reportable_type === 'App\\Models\\Thread' && $report->reportable)
                                            <a href="{{ route('threads.show', $report->reportable->id) }}" class="btn btn-sm btn-outline-primary" target="_blank" data-bs-toggle="tooltip" title="Lihat Thread">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @elseif($report->reportable_type === 'App\\Models\\Comment' && $report->reportable && $report->reportable->thread)
                                            <a href="{{ route('threads.show', $report->reportable->thread->id) }}" class="btn btn-sm btn-outline-primary" target="_blank" data-bs-toggle="tooltip" title="Lihat Thread">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-flag fa-3x text-muted mb-3"></i>
                                        <h5>Tidak ada laporan yang ditemukan</h5>
                                        <p class="text-muted">Laporan yang sesuai dengan filter akan muncul di sini</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        Menampilkan {{ $reports->firstItem() ?? 0 }} - {{ $reports->lastItem() ?? 0 }} dari {{ $reports->total() }} laporan
                    </div>
                    <div>
                        {{ $reports->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Action Modal -->
<div class="modal fade" id="actionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="actionModalTitle">Tindakan untuk Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="approveOptions" class="mb-3 d-none">
                    <label class="form-label">Tindakan yang akan diambil:</label>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="resolution" id="resolutionDelete" value="delete" checked>
                        <label class="form-check-label" for="resolutionDelete">
                            <i class="fas fa-trash me-2 text-danger"></i>Hapus konten yang dilaporkan
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="resolution" id="resolutionEdit" value="edit">
                        <label class="form-check-label" for="resolutionEdit">
                            <i class="fas fa-edit me-2 text-primary"></i>Tandai dan edit konten
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="resolution" id="resolutionFlag" value="flag">
                        <label class="form-check-label" for="resolutionFlag">
                            <i class="fas fa-flag me-2 text-warning"></i>Tandai konten untuk ditinjau lebih lanjut
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="actionReason" class="form-label">Alasan Tindakan</label>
                    <textarea class="form-control" id="actionReason" name="reason" rows="3" placeholder="Berikan alasan untuk tindakan ini..."></textarea>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notifyReporter" name="notify_reporter" checked>
                        <label class="form-check-label" for="notifyReporter">
                            Kirim notifikasi ke pelapor
                        </label>
                    </div>
                    <div class="form-check" id="notifyContentOwnerContainer">
                        <input class="form-check-input" type="checkbox" id="notifyContentOwner" name="notify_content_owner" checked>
                        <label class="form-check-label" for="notifyContentOwner">
                            Kirim notifikasi ke pemilik konten
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submitAction">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Select All checkbox
        const selectAllCheckbox = document.getElementById('selectAll');
        const reportCheckboxes = document.querySelectorAll('.report-checkbox:not([disabled])');
        const batchActionDropdown = document.getElementById('batchActionDropdown');

        // Update batch action dropdown state
        function updateBatchActionButtonState() {
            const checkedBoxes = document.querySelectorAll('.report-checkbox:checked').length;
            batchActionDropdown.disabled = checkedBoxes === 0;
        }

        // Handle select all change
        selectAllCheckbox.addEventListener('change', function() {
            reportCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateBatchActionButtonState();
        });

        // Handle individual checkbox changes
        reportCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBatchActionButtonState);
        });

        // Action modal setup
        const actionModal = new bootstrap.Modal(document.getElementById('actionModal'));
        const actionForm = document.getElementById('batchForm');
        const singleActionButtons = document.querySelectorAll('.single-action');
        const batchActionButtons = document.querySelectorAll('.batch-action-btn');
        const submitActionButton = document.getElementById('submitAction');

        let currentAction = '';
        let currentResolution = '';
        let isBatchAction = false;
        let singleReportId = null;

        // Setup single action buttons
        singleActionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                singleReportId = this.getAttribute('data-id');
                isBatchAction = false;

                setupModalForAction(action);
                actionModal.show();
            });
        });

        // Setup batch action buttons
        batchActionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                const resolution = this.getAttribute('data-resolution');
                currentAction = action;
                currentResolution = resolution;
                isBatchAction = true;
                singleReportId = null;

                setupModalForAction(action, resolution);
                actionModal.show();
            });
        });

        // Setup modal based on action
        function setupModalForAction(action, resolution = null) {
            currentAction = action;

            if (resolution) {
                currentResolution = resolution;
            }

            // Set modal title based on action
            document.getElementById('actionModalTitle').textContent =
                action === 'approve' ? 'Setujui Laporan' : 'Tolak Laporan';

            // Toggle approve-specific options
            const approveOptions = document.getElementById('approveOptions');
            if (action === 'approve') {
                approveOptions.classList.remove('d-none');
                // If resolution is set, select the radio button
                if (resolution) {
                    document.querySelector(`input[name="resolution"][value="${resolution}"]`).checked = true;
                }
            } else {
                approveOptions.classList.add('d-none');
                currentResolution = 'no_action'; // For reject action
            }

            // Toggle content owner notification option
            const notifyContentOwnerContainer = document.getElementById('notifyContentOwnerContainer');
            notifyContentOwnerContainer.classList.toggle('d-none', action === 'reject');

            // Reset fields
            document.getElementById('actionReason').value = '';
            document.getElementById('notifyReporter').checked = true;
            document.getElementById('notifyContentOwner').checked = true;
        }

        // Handle action submission
        submitActionButton.addEventListener('click', function() {
            const reason = document.getElementById('actionReason').value;
            const notifyReporter = document.getElementById('notifyReporter').checked;
            const notifyContentOwner = document.getElementById('notifyContentOwner').checked;

            // For approve action, get selected resolution
            if (currentAction === 'approve') {
                currentResolution = document.querySelector('input[name="resolution"]:checked').value;
            }

            // Set the action and resolution on the form
            document.getElementById('batchAction').value = currentAction;
            document.getElementById('batchResolution').value = currentResolution;

            // Add hidden fields to the form
            let reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'reason';
            reasonInput.value = reason;
            actionForm.appendChild(reasonInput);

            let notifyReporterInput = document.createElement('input');
            notifyReporterInput.type = 'hidden';
            notifyReporterInput.name = 'notify_reporter';
            notifyReporterInput.value = notifyReporter ? '1' : '0';
            actionForm.appendChild(notifyReporterInput);

            if (currentAction === 'approve') {
                let notifyContentOwnerInput = document.createElement('input');
                notifyContentOwnerInput.type = 'hidden';
                notifyContentOwnerInput.name = 'notify_content_owner';
                notifyContentOwnerInput.value = notifyContentOwner ? '1' : '0';
                actionForm.appendChild(notifyContentOwnerInput);
            }

            // For single report action, add report_ids[] with the single ID
            if (!isBatchAction && singleReportId) {
                let reportInput = document.createElement('input');
                reportInput.type = 'hidden';
                reportInput.name = 'report_ids[]';
                reportInput.value = singleReportId;
                actionForm.appendChild(reportInput);
            }

            // Submit the form
            actionForm.submit();
        });
    });
</script>
@endsection
