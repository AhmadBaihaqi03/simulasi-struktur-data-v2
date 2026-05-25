<x-app-layout>
    <style>
        body { background-color: #f0f2f5; }
        .text-indigo { color: #5c60f5; }
        .bg-indigo { background-color: #5c60f5 !important; }
        .card-custom { border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table-hover tbody tr:hover { background-color: #f8fafc; transition: 0.2s; }

        .session-code-box {
            background-color: #ffffff;
            border: 2px dashed #5c60f5;
            border-radius: 10px;
            padding: 4px 12px;
            display: inline-block;
        }

        .filter-row {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 15px;
            padding: 16px;
            margin-bottom: 20px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .filter-row:hover {
            border-color: #5c60f5;
            box-shadow: 0 4px 12px rgba(92, 96, 245, 0.08);
        }

        .search-wrapper-full {
            position: relative;
            flex-grow: 1;
            display: flex;
            align-items: center;
            background-color: #ffffff;
            border-radius: 12px;
            border: 2px solid #cbd5e1;
            transition: all 0.3s ease;
            padding: 0 12px;
            height: 50px;
            width: 100%;
        }

        .search-wrapper-full:hover {
            border-color: #a0aec0;
        }

        .search-wrapper-full:focus-within {
            border-color: #5c60f5;
            box-shadow: 0 0 0 4px rgba(92, 96, 245, 0.12);
        }

        .search-icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            color: #64748b;
            margin-right: 12px;
        }

        .search-input-full {
            flex-grow: 1;
            border: none;
            font-size: 0.95rem;
            font-weight: 500;
            color: #1a202c;
            background-color: transparent;
            outline: none;
            padding: 0;
        }

        .search-input-full::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .search-input-full:focus {
            outline: none;
        }

        .btn-open-task {
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            border-radius: 20px;
            padding: 0 16px;
            font-weight: 700;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        /* Table Responsive Styling */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
        }
    </style>

    <div class="container py-4 py-md-5" style="max-width: 900px;">
        {{-- Header: stack di mobile --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-0" style="font-size: clamp(1.4rem, 5vw, 2rem); letter-spacing: -1px; font-weight: 700 !important;">Panel Evaluasi</h2>
                <p class="text-muted mb-0" style="font-size: 1rem;">Sesi: <span class="text-dark fw-bold">{{ $session->title }}</span></p>
            </div>
            <div class="text-start text-sm-end flex-shrink-0">
                <span class="d-block text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.85rem;">Kode Sesi</span>
                <div class="session-code-box shadow-sm">
                    <h4 class="fw-bold text-indigo font-monospace mb-0" style="letter-spacing: 1px; font-size: 1.1rem;">
                        {{ $session->session_code }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="card card-custom p-3 p-md-4">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
                <h5 class="fw-bold mb-0 text-dark" style="font-size: 1rem;">
                    <i class="bi bi-people-fill text-indigo me-2"></i>Daftar Kelompok Siswa
                </h5>
                <span class="badge bg-opacity-10 text-indigo px-3 py-2 rounded-pill fw-bold" style="font-size: 0.85rem; background-color: #eef0ff;">
                    {{ $groups->count() }} Kelompok Terdaftar
                </span>
            </div>

            <div class="filter-row">
                <div class="search-wrapper-full">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="groupSearch" class="search-input-full" placeholder="Cari nama kelompok...">
                </div>
            </div>

            @if($groups->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="groupTable" style="min-width: 480px;">
                        <thead class="text-muted small text-uppercase">
                            <tr>
                                <th class="py-3 px-3 border-0">Nama Kelompok</th>
                                <th class="py-3 border-0">Status</th>
                                <th class="py-3 text-end border-0 px-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groups as $group)
                            <tr class="group-row border-top">
                                <td class="px-3 py-3 border-0">
                                    <div class="fw-bold text-dark group-name" style="font-size: 0.9rem;">{{ $group->group_name }}</div>
                                    <small class="text-muted">ID: #{{ str_pad($group->id, 4, '0', STR_PAD_LEFT) }}</small>
                                </td>
                                <td class="border-0">
                                    @if($group->evaluation && !empty($group->evaluation->feedback_comment))
                                        <div class="d-flex align-items-center fw-bold" style="color: #10b981; font-size: 0.85rem;">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            <span>Sudah Feedback</span>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center fw-bold" style="color: #f59e0b; font-size: 0.85rem;">
                                            <i class="bi bi-hourglass-split me-1"></i>
                                            <span>Menunggu</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-end px-3 border-0">
                                    <a href="{{ route('groups.work', $group->id) }}" class="btn btn-indigo btn-sm btn-open-task shadow-sm">
                                        Buka <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            <tr id="noResults" style="display: none;">
                                <td colspan="3" class="text-center py-5">
                                    <i class="bi bi-search text-muted mb-2 d-block" style="font-size: 1.5rem;"></i>
                                    <span class="text-muted">Kelompok tidak ditemukan.</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted">Belum ada aktivitas kelompok di sesi ini.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.getElementById('groupSearch').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('.group-row');
            let hasResults = false;

            rows.forEach(row => {
                let name = row.querySelector('.group-name').textContent.toLowerCase();
                if (name.includes(filter)) {
                    row.style.display = "";
                    hasResults = true;
                } else {
                    row.style.display = "none";
                }
            });

            document.getElementById('noResults').style.display = hasResults ? "none" : "";
        });
    </script>
</x-app-layout>
