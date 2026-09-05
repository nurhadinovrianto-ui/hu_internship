@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Manajemen Pengguna</h4>
            <p class="mb-0">Kelola semua akun pengguna sistem (tambah, edit, ubah status, ganti role).</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Admin</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">User</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Pengguna Sistem</h4>
                <div>
                    <button type="button" class="btn btn-success btn-sm px-4 me-2" data-bs-toggle="modal" data-bs-target="#importUserModal">
                        <i class="la la-file-excel me-1"></i> Import Data
                    </button>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm px-4">
                        <i class="la la-plus me-1"></i> Tambah User Baru
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter & Search -->
                <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="la la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="role" class="form-control form-select">
                            <option value="">Semua Peran / Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control form-select">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="la la-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['search', 'role', 'status']))
                            <a href="{{ route('admin.users.index') }}" class="btn btn-light" title="Reset Filter">
                                <i class="la la-undo"></i>
                            </a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Pengguna</strong></th>
                                <th><strong>Email</strong></th>
                                <th><strong>Role</strong></th>
                                <th><strong>Status</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $usr)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $usr->avatar_url }}" width="35" height="35" class="rounded-circle me-3" style="object-fit: cover;" alt="">
                                            <span class="text-dark" style="font-weight: 600;">{{ $usr->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $usr->email }}</td>
                                    <td>
                                        <span class="badge light badge-primary px-3 py-1">
                                            {{ $usr->getRoleLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $usr->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                                            {{ ucfirst($usr->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <!-- Impersonate -->
                                            @if(auth()->user()->canImpersonate() && $usr->canBeImpersonated())
                                                <a href="{{ route('impersonate', $usr->id) }}" class="btn btn-dark btn-xs" title="Login Sebagai User Ini">
                                                    <i class="la la-user-secret"></i>
                                                </a>
                                            @endif
                                            
                                            <!-- Toggle Status -->
                                            <form action="{{ route('admin.users.toggle-status', $usr->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-warning btn-xs" title="Ubah Status Aktif">
                                                    <i class="la la-power-off"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Reset Password -->
                                            <form action="{{ route('admin.users.reset-password', $usr->id) }}" method="POST" onsubmit="return confirm('Reset password user ini menjadi &quot;password&quot;?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-info btn-xs" title="Reset Password ke Default">
                                                    <i class="la la-key"></i>
                                                </button>
                                            </form>

                                            <!-- Edit -->
                                            <a href="{{ route('admin.users.edit', $usr->id) }}" class="btn btn-primary btn-xs" title="Edit Data User">
                                                <i class="la la-pencil-alt"></i>
                                            </a>

                                            <!-- Delete -->
                                            @if($usr->id !== auth()->id())
                                                <form action="{{ route('admin.users.destroy', $usr->id) }}" method="POST" onsubmit="return confirm('Hapus user ini secara permanen?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-xs" title="Hapus User">
                                                        <i class="la la-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada data pengguna ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} pengguna
                    </small>
                    <div>
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import User Modal -->
<div class="modal fade" id="importUserModal" tabindex="-1" aria-labelledby="importUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importUserModalLabel"><i class="la la-file-excel text-success me-2"></i> Import Data Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Peran (Role) untuk Diimpor <span class="text-danger">*</span></label>
                        <select name="role" id="importRoleSelect" class="form-control" required>
                            <option value="">-- Pilih Peran --</option>
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="dpl">Dosen Pembimbing Lapangan (DPL)</option>
                            <option value="kaprodi">Kepala Program Studi (Kaprodi)</option>
                            <option value="dekan">Dekan</option>
                            <option value="supervisor-industri">Supervisor Industri</option>
                            <option value="staff">Staff Administratif (Super Admin, BAAK, Finance)</option>
                        </select>
                        <small class="text-muted mt-2 d-block">Pilih role untuk mengunduh template yang sesuai.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">1. Unduh Template</label>
                        <div class="d-grid">
                            <a href="#" id="downloadTemplateBtn" class="btn btn-outline-primary disabled">
                                <i class="la la-download me-1"></i> Download Template Excel
                            </a>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">2. Unggah File Excel <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control" accept=".xlsx, .xls, .csv" required>
                        <small class="text-danger d-block mt-1">Pastikan Anda menggunakan template yang telah diunduh di atas.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Import Sekarang</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#importRoleSelect').on('change', function() {
            var role = $(this).val();
            var btn = $('#downloadTemplateBtn');
            
            if(role) {
                // Generate URL based on role
                var url = "{{ url('admin/users/template') }}/" + role;
                btn.attr('href', url);
                btn.removeClass('disabled');
            } else {
                btn.attr('href', '#');
                btn.addClass('disabled');
            }
        });
    });
</script>
@endsection
