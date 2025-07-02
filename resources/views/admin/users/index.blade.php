<!-- filepath: /home/helixstars/LAMPP/www/disquseria/resources/views/admin/users/index.blade.php -->
@extends('layouts.admin')

@section('title', 'Kelola Pengguna')

@section('styles')
<style>
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .role-badge {
        font-size: 0.75rem;
        padding: 0.25em 0.5em;
        border-radius: 20px;
    }

    .table td {
        vertical-align: middle;
    }

    .btn-filter.active {
        background-color: #4e73df !important;
        color: white !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kelola Pengguna</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus me-1"></i> Tambah Pengguna
        </a>
    </div>

    <!-- Filter & Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Filter & Pencarian</h6>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <div class="btn-group w-100">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-filter {{ !request('role') ? 'active' : '' }}">
                        Semua
                        <span class="badge bg-secondary ms-1">{{ $stats['all'] }}</span>
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" class="btn btn-outline-danger btn-filter {{ request('role') == 'admin' ? 'active' : '' }}">
                        Admin
                        <span class="badge bg-danger ms-1">{{ $stats['admin'] }}</span>
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'moderator']) }}" class="btn btn-outline-info btn-filter {{ request('role') == 'moderator' ? 'active' : '' }}">
                        Moderator
                        <span class="badge bg-info ms-1">{{ $stats['moderator'] }}</span>
                    </a>
                    <a href="{{ route('admin.users.index', ['role' => 'user']) }}" class="btn btn-outline-success btn-filter {{ request('role') == 'user' ? 'active' : '' }}">
                        Pengguna
                        <span class="badge bg-success ms-1">{{ $stats['user'] }}</span>
                    </a>
                    <a href="{{ route('admin.users.index', ['status' => 'banned']) }}" class="btn btn-outline-dark btn-filter {{ request('status') == 'banned' ? 'active' : '' }}">
                        Diblokir
                        <span class="badge bg-dark ms-1">{{ $stats['banned'] }}</span>
                    </a>
                    <a href="{{ route('admin.users.index', ['verified' => '0']) }}" class="btn btn-outline-warning btn-filter {{ request('verified') == '0' ? 'active' : '' }}">
                        Belum Terverifikasi
                        <span class="badge bg-warning ms-1">{{ $stats['unverified'] }}</span>
                    </a>
                </div>
            </div>

            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-3">
                <!-- Preserve active filters -->
                @if(request('role'))
                    <input type="hidden" name="role" value="{{ request('role') }}">
                @endif
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('verified'))
                    <input type="hidden" name="verified" value="{{ request('verified') }}">
                @endif

                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari nama, email, atau username..." name="search" value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="sort" class="form-select">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                        <option value="threads" {{ request('sort') == 'threads' ? 'selected' : '' }}>Jumlah Thread</option>
                        <option value="comments" {{ request('sort') == 'comments' ? 'selected' : '' }}>Jumlah Komentar</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="perPage" class="form-select">
                        <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10 per halaman</option>
                        <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25 per halaman</option>
                        <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50 per halaman</option>
                        <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100 per halaman</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <div class="d-grid">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card shadow mb-4">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                        <label class="form-check-label" for="selectAll">Pilih Semua</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        <div class="btn-group" id="bulkActions" style="display: none;">
                            <button class="btn btn-outline-secondary btn-sm bulk-action" data-action="ban">
                                <i class="fas fa-ban me-1"></i> Blokir
                            </button>
                            <button class="btn btn-outline-success btn-sm bulk-action" data-action="unban">
                                <i class="fas fa-user-check me-1"></i> Buka Blokir
                            </button>
                            <button class="btn btn-outline-info btn-sm bulk-action" data-action="verify">
                                <i class="fas fa-check-circle me-1"></i> Verifikasi
                            </button>
                            <button class="btn btn-outline-primary btn-sm bulk-action" data-action="export">
                                <i class="fas fa-download me-1"></i> Ekspor
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold">Daftar Pengguna ({{ $users->total() }})</h6>
        </div>
        <div class="card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Peran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" class="user-checkbox" value="{{ $user->id }}">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}" class="user-avatar me-2" alt="{{ $user->name }}">
                                        <div>
                                            <div class="font-weight-bold">{{ $user->name }}</div>
                                            <small class="text-muted">{{ '@' . $user->username }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $user->email }}
                                    @unless($user->email_verified_at)
                                        <span class="badge bg-warning ms-1">Belum Terverifikasi</span>
                                    @endunless
                                </td>
                                <td>
                                    @if($user->isAdmin())
                                        <span class="badge bg-danger role-badge">Admin</span>
                                    @elseif($user->isModerator())
                                        <span class="badge bg-info role-badge">Moderator</span>
                                    @else
                                        <span class="badge bg-success role-badge">Pengguna</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->banned_at)
                                        <span class="badge bg-dark">Diblokir</span>
                                    @else
                                        <span class="badge bg-success">Aktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($user->id != auth()->id())
                                            @if($user->banned_at)
                                                <button type="button" class="btn btn-success btn-ban-toggle"
                                                    data-id="{{ $user->id }}"
                                                    data-banned="1"
                                                    title="Buka Blokir">
                                                    <i class="fas fa-user-check"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-warning btn-ban-toggle"
                                                    data-id="{{ $user->id }}"
                                                    data-banned="0"
                                                    title="Blokir">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $users->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <img src="{{ asset('images/empty-users.svg') }}" alt="Tidak ada pengguna" class="img-fluid mb-3" style="max-width: 200px;">
                    <h5>Tidak ada pengguna yang ditemukan</h5>
                    <p class="text-muted">Coba ubah filter pencarian Anda atau tambahkan pengguna baru.</p>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-1"></i> Tambah Pengguna
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionTitle">Tindakan Massal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="bulkActionForm" action="{{ route('admin.users.batch-action') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="action" id="bulkActionType">
                    <div id="selectedUsers"></div>

                    <div class="mb-3" id="banReasonField" style="display: none;">
                        <label for="ban_reason" class="form-label">Alasan Pemblokiran:</label>
                        <textarea class="form-control" name="ban_reason" id="ban_reason" rows="3"></textarea>
                        <small class="form-text text-muted">Alasan ini akan dikirimkan ke pengguna.</small>
                    </div>

                    <div class="mb-3" id="banDurationField" style="display: none;">
                        <label for="ban_duration" class="form-label">Durasi Pemblokiran:</label>
                        <select class="form-select" name="ban_duration" id="ban_duration">
                            <option value="temporary">Sementara (7 hari)</option>
                            <option value="permanent">Permanen</option>
                            <option value="custom">Kustom</option>
                        </select>
                    </div>

                    <div class="mb-3" id="customDurationField" style="display: none;">
                        <label for="custom_days" class="form-label">Jumlah Hari:</label>
                        <input type="number" class="form-control" name="custom_days" id="custom_days" min="1" value="14">
                    </div>

                    <div class="mb-3 form-check" id="notifyField">
                        <input type="checkbox" class="form-check-input" id="notify_users" name="notify_users" value="1" checked>
                        <label class="form-check-label" for="notify_users">Kirim notifikasi ke pengguna</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="confirmBulkAction">Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Ban User Modal -->
<div class="modal fade" id="banUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="banModalTitle">Blokir Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="banUserForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div id="unbanConfirmation" style="display: none;">
                        <p>Apakah Anda yakin ingin membuka blokir pengguna ini?</p>
                    </div>

                    <div id="banFields">
                        <div class="mb-3">
                            <label for="single_ban_reason" class="form-label">Alasan Pemblokiran:</label>
                            <textarea class="form-control" name="ban_reason" id="single_ban_reason" rows="3"></textarea>
                            <small class="form-text text-muted">Alasan ini akan dikirimkan ke pengguna.</small>
                        </div>

                        <div class="mb-3">
                            <label for="single_ban_duration" class="form-label">Durasi Pemblokiran:</label>
                            <select class="form-select" name="ban_duration" id="single_ban_duration">
                                <option value="temporary">Sementara (7 hari)</option>
                                <option value="permanent">Permanen</option>
                                <option value="custom">Kustom</option>
                            </select>
                        </div>

                        <div class="mb-3" id="singleCustomDurationField" style="display: none;">
                            <label for="single_custom_days" class="form-label">Jumlah Hari:</label>
                            <input type="number" class="form-control" name="custom_days" id="single_custom_days" min="1" value="14">
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="single_notify_user" name="notify_user" value="1" checked>
                            <label class="form-check-label" for="single_notify_user">Kirim notifikasi ke pengguna</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning" id="confirmBanAction">Blokir Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select All Checkbox
        const selectAll = document.getElementById('selectAll');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');
        const bulkActions = document.getElementById('bulkActions');

        selectAll.addEventListener('change', function() {
            const checked = this.checked;

            userCheckboxes.forEach(checkbox => {
                checkbox.checked = checked;
            });

            updateBulkActionsVisibility();
        });

        userCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateBulkActionsVisibility();

                // Update "select all" checkbox
                if (!this.checked) {
                    selectAll.checked = false;
                } else {
                    const allChecked = Array.from(userCheckboxes).every(c => c.checked);
                    selectAll.checked = allChecked;
                }
            });
        });

        function updateBulkActionsVisibility() {
            const anyChecked = Array.from(userCheckboxes).some(checkbox => checkbox.checked);
            bulkActions.style.display = anyChecked ? 'flex' : 'none';
        }

        // Bulk Actions
        const bulkActionModal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
        const bulkActionButtons = document.querySelectorAll('.bulk-action');
        const bulkActionForm = document.getElementById('bulkActionForm');
        const selectedUsersContainer = document.getElementById('selectedUsers');
        const bulkActionType = document.getElementById('bulkActionType');
        const bulkActionTitle = document.getElementById('bulkActionTitle');
        const banReasonField = document.getElementById('banReasonField');
        const banDurationField = document.getElementById('banDurationField');
        const customDurationField = document.getElementById('customDurationField');
        const notifyField = document.getElementById('notifyField');
        const confirmBulkAction = document.getElementById('confirmBulkAction');
        const banDuration = document.getElementById('ban_duration');

        bulkActionButtons.forEach(button => {
            button.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                bulkActionType.value = action;

                // Reset fields
                banReasonField.style.display = 'none';
                banDurationField.style.display = 'none';
                customDurationField.style.display = 'none';
                notifyField.style.display = 'block';

                // Update modal based on action
                switch(action) {
                    case 'ban':
                        bulkActionTitle.textContent = 'Blokir Pengguna Terpilih';
                        confirmBulkAction.textContent = 'Blokir Pengguna';
                        confirmBulkAction.className = 'btn btn-warning';
                        banReasonField.style.display = 'block';
                        banDurationField.style.display = 'block';
                        break;
                    case 'unban':
                        bulkActionTitle.textContent = 'Buka Blokir Pengguna Terpilih';
                        confirmBulkAction.textContent = 'Buka Blokir';
                        confirmBulkAction.className = 'btn btn-success';
                        break;
                    case 'verify':
                        bulkActionTitle.textContent = 'Verifikasi Email Pengguna Terpilih';
                        confirmBulkAction.textContent = 'Verifikasi Email';
                        confirmBulkAction.className = 'btn btn-info';
                        break;
                    case 'export':
                        bulkActionTitle.textContent = 'Ekspor Data Pengguna Terpilih';
                        confirmBulkAction.textContent = 'Ekspor Data';
                        confirmBulkAction.className = 'btn btn-primary';
                        notifyField.style.display = 'none';
                        break;
                }

                // Add selected user IDs to form
                selectedUsersContainer.innerHTML = '';
                const selectedUsers = Array.from(userCheckboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);

                selectedUsers.forEach(userId => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'user_ids[]';
                    input.value = userId;
                    selectedUsersContainer.appendChild(input);
                });

                // Show the modal
                bulkActionModal.show();
            });
        });

        // Ban duration toggle for bulk action
        banDuration.addEventListener('change', function() {
            customDurationField.style.display = this.value === 'custom' ? 'block' : 'none';
        });

        // Individual user ban/unban
        const banUserModal = new bootstrap.Modal(document.getElementById('banUserModal'));
        const banUserForm = document.getElementById('banUserForm');
        const banModalTitle = document.getElementById('banModalTitle');
        const banFields = document.getElementById('banFields');
        const unbanConfirmation = document.getElementById('unbanConfirmation');
        const confirmBanAction = document.getElementById('confirmBanAction');
        const singleBanDuration = document.getElementById('single_ban_duration');
        const singleCustomDurationField = document.getElementById('singleCustomDurationField');

        document.querySelectorAll('.btn-ban-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                const isBanned = parseInt(this.getAttribute('data-banned'));

                // Setup form action
                banUserForm.action = `{{ url('admin/users') }}/${userId}/${isBanned ? 'unban' : 'ban'}`;

                // Update modal UI based on action
                if (isBanned) {
                    banModalTitle.textContent = 'Buka Blokir Pengguna';
                    banFields.style.display = 'none';
                    unbanConfirmation.style.display = 'block';
                    confirmBanAction.textContent = 'Buka Blokir';
                    confirmBanAction.className = 'btn btn-success';
                } else {
                    banModalTitle.textContent = 'Blokir Pengguna';
                    banFields.style.display = 'block';
                    unbanConfirmation.style.display = 'none';
                    confirmBanAction.textContent = 'Blokir Pengguna';
                    confirmBanAction.className = 'btn btn-warning';
                }

                // Show the modal
                banUserModal.show();
            });
        });

        // Ban duration toggle for single user
        singleBanDuration.addEventListener('change', function() {
            singleCustomDurationField.style.display = this.value === 'custom' ? 'block' : 'none';
        });

        const checkboxes = document.querySelectorAll('.user-checkbox');
        const selectAllCheckbox = document.getElementById('selectAll');
        const batchActionSelect = document.getElementById('batchAction');
        const batchActionBtn = document.getElementById('batchActionBtn');
        const roleSelectContainer = document.getElementById('roleSelectContainer');
        const form = document.getElementById('batchActionForm');
        const selectedIdsContainer = document.getElementById('selectedIdsContainer');

        // Toggle semua checkbox
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateBatchActionButton();
            });
        }

        // Update button status ketika checkbox diubah
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBatchActionButton);
        });

        // Tampilkan/sembunyikan pilihan role berdasarkan aksi yang dipilih
        batchActionSelect.addEventListener('change', function() {
            if (this.value === 'set_role') {
                roleSelectContainer.style.display = 'block';
            } else {
                roleSelectContainer.style.display = 'none';
            }
            updateBatchActionButton();
        });

        // Update status tombol Terapkan
        function updateBatchActionButton() {
            const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
            batchActionBtn.disabled = checkedCount === 0 || batchActionSelect.value === '';
        }

        // Proses form sebelum submit untuk menambahkan user_ids
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Hapus input hidden sebelumnya
            while (selectedIdsContainer.firstChild) {
                selectedIdsContainer.removeChild(selectedIdsContainer.firstChild);
            }

            // Tambahkan input hidden untuk setiap checkbox yang dicentang
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'user_ids[]';
                    input.value = checkbox.value;
                    selectedIdsContainer.appendChild(input);
                }
            });

            // Submit form
            this.submit();
        });
    });
</script>
@endsection
