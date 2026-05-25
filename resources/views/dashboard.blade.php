<x-app-layout>
    <style>
        body { background-color: #f8f9fa; }

        .card-stat {
            border-radius: 15px;
            border: none;
            min-height: 75px;
            transition: transform 0.2s;
        }
        .card-stat:hover { transform: translateY(-3px); }
        .text-indigo { color: #5c60f5; }
        .bg-indigo { background-color: #5c60f5; }
        .btn-indigo {
            background-color: #5c60f5;
            color: white;
            border-radius: 20px;
            padding: 10px 20px;
            transition: 0.3s;
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
        }
        .btn-indigo:hover { background-color: #4a4ed4; color: white; box-shadow: 0 4px 12px rgba(92, 96, 245, 0.3); }
        .status-badge { border-radius: 20px; padding: 5px 12px; font-size: 0.75rem; font-weight: bold; }

        /* Tombol aksi lonjong, minimum 44x44 */
        .btn-action-custom {
            border-radius: 12px;
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .btn-outline-indigo { color: #5c60f5; border: 1.5px solid #5c60f5; background: transparent; }
        .btn-outline-indigo:hover { background-color: #5c60f5; color: white; }

        /* Table Responsive Styling */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }

        /* Pagination Styling */
        .pagination-wrapper nav svg { width: 20px; }
        .pagination-wrapper .relative.z-0.inline-flex { border-radius: 10px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }

        /* Mobile: search dan tombol stack */
        @media (max-width: 575px) {
            .search-action-row {
                flex-direction: column !important;
                gap: 0.75rem !important;
            }
            .search-action-row .btn-indigo {
                width: 100%;
                justify-content: center;
            }
            .session-table-actions {
                justify-content: flex-end;
            }
        }
    </style>

    <div class="container py-4 py-md-5">
        @if(session('success'))
            <div id="successAlert" 
                class="alert border-0 shadow-sm mb-4 d-flex align-items-center gap-3 fade show"
                style="border-radius: 15px; background-color: #eef0ff; border-left: 4px solid #5c60f5; color: #4c2a85; position: relative; transition: opacity 0.5s ease;">
                
                <i class="bi bi-check-circle-fill" style="color: #5c60f5; font-size: 1.2rem;"></i>
                <span style="font-weight: 600; flex: 1;">{{ session('success') }}</span>
                
                <button type="button" 
                        class="btn-close" 
                        onclick="closeAlert()" 
                        style="opacity: 0.7; cursor: pointer; z-index: 10;" 
                        aria-label="Close">
                </button>
            </div>
        @endif

        <div class="mb-4">
            <h1 class="fw-bold mb-1" style="font-size: clamp(1.4rem, 5vw, 2rem);">Halo, {{ Auth::user()->name }}</h1>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Kelola sesi pembelajaran berbasis masalah (Problem-Based Learning) Anda</p>
        </div>

        {{-- Stat Cards: 2-kolom di mobile, 3-kolom di md --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-4">
                <div class="card card-stat shadow-sm p-3 border-0 h-100">
                    <div class="d-flex justify-content-between text-muted small fw-bold">
                        <span>BELUM PENILAIAN</span>
                        <i class="bi bi-pencil-square text-warning"></i>
                    </div>
                    <h1 class="fw-bold mt-2 mb-0 display-6 text-indigo">{{ sprintf('%02d', $stats['pending']) }}</h1>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card card-stat shadow-sm p-3 border-0 h-100">
                    <div class="d-flex justify-content-between text-muted small fw-bold">
                        <span>SUDAH DINILAI</span>
                        <i class="bi bi-check-all text-success"></i>
                    </div>
                    <h1 class="fw-bold mt-2 mb-0 display-6 text-indigo">{{ sprintf('%02d', $stats['graded']) }}</h1>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card card-stat shadow-sm p-3 border-0 h-100">
                    <div class="d-flex justify-content-between text-muted small fw-bold">
                        <span>TOTAL KELOMPOK</span>
                        <i class="bi bi-people text-primary"></i>
                    </div>
                    <h1 class="fw-bold mt-2 mb-0 display-6 text-indigo">{{ sprintf('%02d', $stats['total_groups']) }}</h1>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card card-stat shadow-sm p-3 border-0 h-100">
                    <div class="d-flex justify-content-between text-muted small fw-bold">
                        <span>SESI AKTIF</span>
                        <i class="bi bi-broadcast text-success"></i>
                    </div>
                    <h1 class="fw-bold mt-2 mb-0 display-6 text-indigo">{{ sprintf('%02d', $stats['active']) }}</h1>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card card-stat shadow-sm p-3 border-0 h-100">
                    <div class="d-flex justify-content-between text-muted small fw-bold">
                        <span>SESI TIDAK AKTIF</span>
                        <i class="bi bi-pause-circle text-secondary"></i>
                    </div>
                    <h1 class="fw-bold mt-2 mb-0 display-6 text-indigo">{{ sprintf('%02d', $stats['total'] - $stats['active']) }}</h1>
                </div>
            </div>
            <div class="col-6 col-md-4">
                <div class="card card-stat shadow-sm p-3 border-0 h-100">
                    <div class="d-flex justify-content-between text-muted small fw-bold">
                        <span>TOTAL SESI</span>
                        <i class="bi bi-layers text-indigo"></i>
                    </div>
                    <h1 class="fw-bold mt-2 mb-0 display-6 text-indigo">{{ sprintf('%02d', $stats['total']) }}</h1>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <div class="card-body p-3 p-md-4">
                {{-- Search + Tombol: stack di mobile, row di sm+ --}}
                <div class="d-flex align-items-stretch align-items-sm-center gap-2 mb-4 flex-column flex-sm-row search-action-row">
                    <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                        <span class="input-group-text bg-white border-0 ps-3">
                            <i class="bi bi-search text-indigo"></i>
                        </span>
                        <input type="text" id="searchInput"
                               class="form-control bg-white border-0 py-2 fw-medium text-indigo"
                               placeholder="Cari nama atau kode sesi..."
                               style="box-shadow: none; min-height: 44px;">
                    </div>

                    <div class="flex-shrink-0">
                        <a href="{{ route('sessions.create') }}" class="btn btn-indigo shadow-sm fw-bold w-100 w-sm-auto">
                            <i class="bi bi-plus-lg me-2"></i> Tambah Sesi
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle" id="sessionTable" style="min-width: 500px;">
                        <thead class="text-muted small">
                            <tr>
                                <th>NAMA SESI</th>
                                <th>KODE</th>
                                <th class="d-none d-sm-table-cell">MURID</th>
                                <th>STATUS</th>
                                <th class="text-end">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sessions as $session)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark session-title" style="font-size: 0.9rem;">{{ $session->title }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1 font-monospace session-code" style="font-size: 0.75rem;">{{ $session->session_code }}</span>
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    <span class="text-muted small">
                                        <i class="bi bi-people me-1"></i> {{ $session->total_submitted_groups }} Kelompok
                                    </span>
                                </td>
                                <td>
                                    @if($session->is_active)
                                        <span class="status-badge bg-success-subtle text-success border border-success">Aktif</span>
                                    @else
                                        <span class="status-badge bg-light text-muted border">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end align-items-center gap-1 session-table-actions">
                                        <form action="{{ route('sessions.toggle', $session) }}" method="POST" class="m-0">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-action-custom shadow-sm {{ $session->is_active ? 'btn-success' : 'btn-secondary' }}" title="Toggle Active">
                                                <i class="bi {{ $session->is_active ? 'bi-toggle-on' : 'bi-toggle-off' }} fs-5"></i>
                                            </button>
                                        </form>

                                        <a href="{{ route('sessions.evaluations', $session) }}" class="btn btn-sm btn-action-custom btn-outline-indigo position-relative shadow-sm" title="Grading">
                                            <i class="bi bi-mortarboard-fill fs-5"></i>
                                            
                                            @if($session->real_pending_count > 0)
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.65rem;">
                                                    {{ $session->real_pending_count }}
                                                </span>
                                            @endif
                                        </a>

                                        <a href="{{ route('sessions.edit', $session) }}" class="btn btn-sm btn-action-custom btn-outline-warning shadow-sm" title="Edit">
                                            <i class="bi bi-pencil-fill fs-6"></i>
                                        </a>

                                        <form action="{{ route('sessions.destroy', $session) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus sesi ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-action-custom btn-outline-danger shadow-sm" title="Delete">
                                                <i class="bi bi-trash3-fill fs-6"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada sesi. Buat sesi pertama Anda!</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pagination-wrapper d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 px-1">
                    <div class="text-muted small">
                        Showing {{ $sessions->firstItem() ?? 0 }} to {{ $sessions->lastItem() ?? 0 }} of {{ $sessions->total() }} entries
                    </div>
                    <div>
                        {{ $sessions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('#sessionTable tbody tr');

            rows.forEach(row => {
                let title = row.querySelector('.session-title')?.textContent.toLowerCase() || "";
                let code = row.querySelector('.session-code')?.textContent.toLowerCase() || "";

                if (title.includes(filter) || code.includes(filter)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });

        // untuk alert sukses yang muncul di atas, dengan tombol close dan auto-hide setelah 4 detik
        function closeAlert() {
            let alertEl = document.getElementById('successAlert');
            if (alertEl) {
                alertEl.style.opacity = '0'; // Efek fade out
                setTimeout(() => {
                    alertEl.remove(); // Hapus dari DOM setelah fade out selesai
                }, 500);
            }
        }

        // Fungsi otomatis hilang dalam 4 detik
        setTimeout(() => {
            closeAlert();
        }, 4000);
    </script>
</x-app-layout>
