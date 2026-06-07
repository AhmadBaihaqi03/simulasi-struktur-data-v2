<x-app-layout>
    {{-- Kita gunakan style yang sama agar konsisten dengan dashboard kelompok --}}
    <style>
        body { background-color: #f8f9fa; }
        .card-stat { border-radius: 15px; border: none; min-height: 75px; transition: transform 0.2s; }
        .card-stat:hover { transform: translateY(-3px); }
        .text-indigo { color: #5c60f5; }
        .btn-indigo { background-color: #5c60f5; color: white; border-radius: 20px; padding: 10px 20px; transition: 0.3s; display: inline-flex; align-items: center; }
        .btn-indigo:hover { background-color: #4a4ed4; color: white; }
        .status-badge { border-radius: 20px; padding: 5px 12px; font-size: 0.75rem; font-weight: bold; }
        .btn-action-custom { border-radius: 12px; width: 44px; height: 44px; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; }
        .btn-outline-indigo { color: #5c60f5; border: 1.5px solid #5c60f5; background: transparent; }
        .btn-outline-indigo:hover { background-color: #5c60f5; color: white; }
    </style>

    <div class="container py-4 py-md-5">
        <div class="mb-4">
            <h1 class="fw-bold mb-1">Halo, {{ Auth::user()->name }}</h1>
            <p class="text-muted mb-0">Kelola sesi pembelajaran individu Anda</p>
        </div>

        {{-- Stat Cards: 3 Kolom --}}
        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="card card-stat shadow-sm p-3 border-0 h-100">
                    <div class="text-muted small fw-bold">SESI AKTIF</div>
                    <h1 class="fw-bold mt-2 mb-0 display-6 text-indigo">{{ sprintf('%02d', $stats['aktif']) }}</h1>
                </div>
            </div>
            <div class="col-4">
                <div class="card card-stat shadow-sm p-3 border-0 h-100">
                    <div class="text-muted small fw-bold">SESI TIDAK AKTIF</div>
                    <h1 class="fw-bold mt-2 mb-0 display-6 text-indigo">{{ sprintf('%02d', $stats['tidak_aktif']) }}</h1>
                </div>
            </div>
            <div class="col-4">
                <div class="card card-stat shadow-sm p-3 border-0 h-100">
                    <div class="text-muted small fw-bold">TOTAL SESI</div>
                    <h1 class="fw-bold mt-2 mb-0 display-6 text-indigo">{{ sprintf('%02d', $stats['total']) }}</h1>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex gap-2 mb-4">
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari sesi...">
                    <a href="{{ route('sesi-individu.create') }}" class="btn btn-indigo shadow-sm fw-bold">
                        <i class="bi bi-plus-lg me-2"></i> Tambah Sesi
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle" id="sessionTable">
                        <thead class="text-muted small">
                            <tr>
                                <th>NAMA SESI</th>
                                <th>KODE</th>
                                <th>MURID</th>
                                <th>STATUS</th>
                                <th class="text-end">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($individualSessions as $session)
                            <tr>
                                <td><div class="fw-bold session-title">{{ $session->title }}</div></td>
                                <td><span class="badge bg-light text-dark border session-code">{{ $session->access_code }}</span></td>
                                <td><i class="bi bi-people me-1"></i> {{ $session->individual_submissions_count }} Siswa</td>
                                <td>
                                    <span class="status-badge {{ $session->is_active ? 'bg-success-subtle text-success border border-success' : 'bg-light text-muted border' }}">
                                        {{ $session->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        
                                        <form action="{{ route('sesi-individu.toggle', $session) }}" method="POST" class="m-0">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-action-custom shadow-sm {{ $session->is_active ? 'btn-success' : 'btn-secondary' }}" title="Toggle Active">
                                                <i class="bi {{ $session->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }} fs-5"></i>
                                            </button>
                                        </form>

                                        <a href="{{ route('sesi-individu.nilai', $session->id) }}" 
                                        class="btn btn-sm btn-action-custom btn-outline-indigo shadow-sm" 
                                        title="Lihat Nilai">
                                            <i class="bi bi-mortarboard-fill fs-5"></i>
                                        </a>

                                        <a href="{{ route('sesi-individu.edit', $session->id) }}" 
                                        class="btn btn-sm btn-action-custom btn-outline-warning shadow-sm" 
                                        title="Edit Sesi">
                                            <i class="bi bi-pencil-fill fs-6"></i>
                                        </a>

                                        <form action="{{ route('sesi-individu.destroy', $session->id) }}" method="POST" onsubmit="return confirm('Hapus sesi ini?')">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada sesi individu.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script pencarian yang sama
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#sessionTable tbody tr');
            rows.forEach(row => {
                let title = row.querySelector('.session-title')?.textContent.toLowerCase() || "";
                let code = row.querySelector('.session-code')?.textContent.toLowerCase() || "";
                row.style.display = (title.includes(filter) || code.includes(filter)) ? "" : "none";
            });
        });
    </script>
</x-app-layout>