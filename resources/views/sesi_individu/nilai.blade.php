<x-app-layout>
    <style>
        body { background-color: #f8f9fa; }
        .card-soft { border: none; border-radius: 18px; }
        .btn-indigo { background-color: #5c60f5; color: #fff; border-radius: 16px; }
        .btn-indigo:hover { background-color: #4a4ed4; color: #fff; }
        .badge-score { border-radius: 999px; padding: 0.45rem 0.75rem; }
    </style>

    <div class="container py-4 py-md-5">
        <div class="mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h1 class="fw-bold mb-1">Sesi: {{ $session->title }}</h1>
                <p class="text-muted mb-0">Daftar murid yang sudah mengerjakan soal dan nilai yang didapat.</p>
            </div>
            <a href="{{ route('dashboard.individu') }}" class="btn btn-light border">Kembali ke Dashboard</a>
        </div>

        <div class="card card-soft shadow-sm">
            <div class="card-body p-3 p-md-4">
                <div class="mb-3">
                    <input type="text" id="submissionSearchInput" class="form-control" placeholder="Cari murid, kelas, atau nomor absen...">
                </div>

                @if($session->individualSubmissions->isEmpty())
                    <div class="text-center py-5 text-muted">
                        Belum ada murid yang mengerjakan sesi ini.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="text-muted small">
                                <tr>
                                    <th>Murid</th>
                                    <th>Kelas</th>
                                    <th>No. Absen</th>
                                    <th>Jumlah Jawaban</th>
                                    <th>Nilai</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="submissionTableBody">
                                @foreach($session->individualSubmissions as $submission)
                                    <tr data-search="{{ strtolower($submission->student_name . ' ' . $submission->class_name . ' ' . $submission->student_number) }}">
                                        <td class="fw-bold">{{ $submission->student_name }}</td>
                                        <td>{{ $submission->class_name }}</td>
                                        <td>{{ $submission->student_number }}</td>
                                        <td>{{ $submission->individual_answers_count }} / {{ $session->individualQuestions->count() }}</td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success badge-score border border-success">
                                                {{ $submission->total_score }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('sesi-individu.nilai.detail', $submission) }}" class="btn btn-sm btn-indigo">
                                                Lihat Jawaban
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('submissionSearchInput');
            const rows = Array.from(document.querySelectorAll('#submissionTableBody tr'));

            if (!input || !rows.length) {
                return;
            }

            input.addEventListener('input', function () {
                const keyword = this.value.trim().toLowerCase();

                rows.forEach(row => {
                    const searchable = row.dataset.search || '';
                    row.style.display = searchable.includes(keyword) ? '' : 'none';
                });
            });
        });
    </script>
</x-app-layout>
