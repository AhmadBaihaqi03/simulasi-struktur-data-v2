<x-app-layout>
    <div class="container py-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <div class="mb-4">
                    <h3 class="card-title mb-2 fw-bold">Edit Sesi Individu</h3>
                    <p class="text-muted mb-0">Ubah judul sesi, status aktif, dan detail soal yang sudah dibuat.</p>
                </div>

                <form action="{{ route('sesi-individu.update', $individualSession) }}" method="POST" id="session-form">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Sesi</label>
                        <input type="text" name="title" class="form-control form-control-lg" value="{{ old('title', $individualSession->title) }}" required>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $individualSession->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="is_active">Sesi Aktif</label>
                    </div>

                    <div id="questions-container"></div>

                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <button type="button" class="btn btn-outline-secondary" onclick="addQuestion()">+ Tambah Soal</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="question-template">
        <div class="card p-3 mb-3 shadow-sm question-card">
            <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <div class="fw-bold">Soal <span class="question-number"></span></div>
                    <div class="text-muted small">Pilih tipe soal, lalu lengkapi input jawabannya.</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-question-btn">Hapus</button>
            </div>

            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Jenis Soal</label>
                    <select class="form-select question-type" required>
                        <option value="">Pilih jenis soal</option>
                        <option value="multiple_choice">Pilihan Ganda</option>
                        <option value="checkbox">Checkbox / Pilihan Banyak</option>
                        <option value="drag_drop">Mengurutkan</option>
                        <option value="grouping">Mengelompokkan</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Pertanyaan</label>
                    <input type="text" class="form-control question-text" placeholder="Tulis pertanyaan..." required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Poin</label>
                    <input type="number" class="form-control question-points" value="10" min="0" step="1" required>
                </div>
            </div>

            <div class="question-config mt-3 d-none"></div>
        </div>
    </template>

    <template id="option-input-template">
        <div class="input-group mb-2 option-row">
            <input type="text" class="form-control option-input" placeholder="Opsi...">
            <button type="button" class="btn btn-outline-danger remove-option-btn">Hapus</button>
        </div>
    </template>

    <template id="group-input-template">
        <div class="input-group mb-2 group-row">
            <input type="text" class="form-control group-input" placeholder="Nama kelompok...">
            <button type="button" class="btn btn-outline-danger remove-group-btn">Hapus</button>
        </div>
    </template>

    <template id="item-input-template">
        <div class="row g-2 align-items-center mb-2 item-row">
            <div class="col-md-6">
                <input type="text" class="form-control item-label" placeholder="Item...">
            </div>
            <div class="col-md-6">
                <select class="form-select item-group-select">
                    <option value="">Pilih kelompok</option>
                </select>
            </div>
        </div>
    </template>

    <script>
        let questionIndex = 0;
        @php
            $existingQuestions = $individualSession->individualQuestions->map(function ($question) {
                return [
                    'type' => $question->type,
                    'question_text' => $question->question_text,
                    'points' => $question->points,
                    'options' => $question->options,
                    'correct_answer' => $question->correct_answer,
                ];
            })->values()->all();
        @endphp
        const existingQuestions = @json($existingQuestions);

        function addQuestion(prefill = null) {
            const template = document.getElementById('question-template');
            const clone = template.content.cloneNode(true);
            const card = clone.querySelector('.question-card');
            const index = questionIndex++;

            card.dataset.index = index;
            clone.querySelector('.question-number').textContent = index + 1;
            clone.querySelector('.remove-question-btn').addEventListener('click', () => card.remove());
            clone.querySelector('.question-type').addEventListener('change', (event) => renderQuestionConfig(card, event.target.value));

            if (prefill) {
                clone.querySelector('.question-type').value = prefill.type || '';
                clone.querySelector('.question-text').value = prefill.question_text || '';
                clone.querySelector('.question-points').value = prefill.points ?? 10;
            }

            document.getElementById('questions-container').appendChild(clone);
            const createdCard = document.querySelectorAll('.question-card')[document.querySelectorAll('.question-card').length - 1];
            const typeValue = prefill ? prefill.type : '';
            if (typeValue) {
                renderQuestionConfig(createdCard, typeValue, prefill);
            }
        }

        function renderQuestionConfig(card, type, prefill = null) {
            const config = card.querySelector('.question-config');
            config.classList.remove('d-none');
            config.innerHTML = '';

            if (type === 'multiple_choice' || type === 'checkbox') {
                config.innerHTML = `
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="fw-bold">Opsi Jawaban</div>
                                <div class="text-muted small">Tambahkan opsi dan pilih kunci jawaban yang benar.</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary add-option-btn">+ Tambah Opsi</button>
                        </div>
                        <div class="options-list"></div>
                        <div class="mt-3 answer-key-area"></div>
                    </div>`;

                const options = Array.isArray(prefill?.options) ? prefill.options : ['',''];
                options.forEach(option => addOptionRow(card, option));
                wireOptionButtons(card);
                updateAnswerKeyArea(card, type, prefill);
                return;
            }

            if (type === 'drag_drop') {
                config.innerHTML = `
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="fw-bold">Urutan Item</div>
                                <div class="text-muted small">Tulis item lalu susun urutan benar sebagai kunci jawaban.</div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary add-option-btn">+ Tambah Item</button>
                        </div>
                        <div class="options-list"></div>
                        <div class="mt-3">
                            <label class="form-label fw-bold">Kunci Urutan</label>
                            <textarea class="form-control correct-order-input" rows="2" placeholder="Contoh: Langkah 1, Langkah 2, Langkah 3"></textarea>
                            <div class="text-muted small mt-2">Pisahkan dengan koma atau isi sesuai urutan akhir yang benar.</div>
                        </div>
                    </div>`;

                const options = Array.isArray(prefill?.options) ? prefill.options : ['',''];
                options.forEach(option => addOptionRow(card, option));
                wireOptionButtons(card);
                if (prefill?.correct_answer) {
                    const value = Array.isArray(prefill.correct_answer)
                        ? prefill.correct_answer.join(', ')
                        : String(prefill.correct_answer);
                    card.querySelector('.correct-order-input').value = value;
                }
                return;
            }

            if (type === 'grouping') {
                config.innerHTML = `
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="row g-3">
                            <div class="col-lg-5">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <div class="fw-bold">Kelompok</div>
                                        <div class="text-muted small">Tambahkan nama-nama kelompok yang akan dipakai.</div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary add-group-btn">+ Tambah Kelompok</button>
                                </div>
                                <div class="groups-list"></div>
                            </div>
                            <div class="col-lg-7">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <div class="fw-bold">Item yang Dikelompokkan</div>
                                        <div class="text-muted small">Tambahkan item lalu pilih kelompok yang benar untuk tiap item.</div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary add-item-btn">+ Tambah Item</button>
                                </div>
                                <div class="items-list"></div>
                            </div>
                        </div>
                    </div>`;

                const groups = Array.isArray(prefill?.options) ? prefill.options : [];
                const groupedItems = Array.isArray(prefill?.correct_answer) ? prefill.correct_answer : [];
                const groupList = groups.length ? groups : ['Kelompok 1', 'Kelompok 2'];
                groupList.forEach(group => addGroupRow(card, group));
                const items = groupedItems.length ? groupedItems : [{ item: '', group: '' }, { item: '', group: '' }];
                items.forEach(item => addItemRow(card, item));
                wireGroupButtons(card);
                wireItemButtons(card);
                syncGroupingSelects(card);
                return;
            }
        }

        function addOptionRow(card, value = '') {
            const template = document.getElementById('option-input-template');
            const clone = template.content.cloneNode(true);
            const row = clone.querySelector('.option-row');
            row.querySelector('.option-input').value = value || '';
            row.querySelector('.remove-option-btn').addEventListener('click', () => {
                row.remove();
                refreshAnswerKey(card);
            });
            row.querySelector('.option-input').addEventListener('input', () => refreshAnswerKey(card));
            card.querySelector('.options-list').appendChild(clone);
        }

        function wireOptionButtons(card) {
            const addButton = card.querySelector('.add-option-btn');
            if (addButton) {
                addButton.addEventListener('click', () => {
                    addOptionRow(card);
                    refreshAnswerKey(card);
                });
            }
        }

        function updateAnswerKeyArea(card, type, prefill = null) {
            const area = card.querySelector('.answer-key-area');
            const options = getOptionTexts(card);

            if (type === 'multiple_choice') {
                const selected = prefill && typeof prefill.correct_answer !== 'undefined' ? String(prefill.correct_answer) : '0';
                const radios = options.map((label, index) => `
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="questions[${card.dataset.index}][correct_index]" value="${index}" ${selected === String(index) ? 'checked' : ''}>
                        <label class="form-check-label">${escapeHtml(label || `Opsi ${index + 1}`)}</label>
                    </div>
                `).join('');

                area.innerHTML = `
                    <label class="form-label fw-bold">Kunci Jawaban</label>
                    <div class="answer-key-list">${radios}</div>
                `;
                return;
            }

            if (type === 'checkbox') {
                const selected = Array.isArray(prefill?.correct_answer) ? prefill.correct_answer.map(String) : [];
                const checks = options.map((label, index) => `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="questions[${card.dataset.index}][correct_answer][]" value="${index}" ${selected.includes(String(index)) ? 'checked' : ''}>
                        <label class="form-check-label">${escapeHtml(label || `Opsi ${index + 1}`)}</label>
                    </div>
                `).join('');

                area.innerHTML = `
                    <label class="form-label fw-bold">Kunci Jawaban</label>
                    <div class="answer-key-list">${checks}</div>
                `;
            }
        }

        function refreshAnswerKey(card) {
            const type = card.querySelector('.question-type').value;
            if (type === 'multiple_choice' || type === 'checkbox') {
                updateAnswerKeyArea(card, type);
            }
        }

        function getOptionTexts(card) {
            return Array.from(card.querySelectorAll('.option-input')).map(input => input.value.trim());
        }

        function addGroupRow(card, value = '') {
            const template = document.getElementById('group-input-template');
            const clone = template.content.cloneNode(true);
            const row = clone.querySelector('.group-row');
            row.querySelector('.group-input').value = value || '';
            row.querySelector('.remove-group-btn').addEventListener('click', () => {
                row.remove();
                syncGroupingSelects(card);
            });
            row.querySelector('.group-input').addEventListener('input', () => syncGroupingSelects(card));
            card.querySelector('.groups-list').appendChild(clone);
        }

        function addItemRow(card, value = { item: '', group: '' }) {
            const template = document.getElementById('item-input-template');
            const clone = template.content.cloneNode(true);
            const row = clone.querySelector('.item-row');
            row.querySelector('.item-label').value = value.item || '';
            row.querySelector('.item-group-select').dataset.selected = value.group || '';
            card.querySelector('.items-list').appendChild(clone);
            syncGroupingSelects(card);
        }

        function wireGroupButtons(card) {
            const addButton = card.querySelector('.add-group-btn');
            if (addButton) {
                addButton.addEventListener('click', () => {
                    addGroupRow(card);
                    syncGroupingSelects(card);
                });
            }
        }

        function wireItemButtons(card) {
            const addButton = card.querySelector('.add-item-btn');
            if (addButton) {
                addButton.addEventListener('click', () => {
                    addItemRow(card);
                    syncGroupingSelects(card);
                });
            }
        }

        function syncGroupingSelects(card) {
            const groupInputs = Array.from(card.querySelectorAll('.group-input'));
            const groups = groupInputs.map(input => input.value.trim()).filter(Boolean);
            const selects = Array.from(card.querySelectorAll('.item-group-select'));

            selects.forEach(select => {
                const selected = select.dataset.selected || select.value || '';
                select.innerHTML = '<option value="">Pilih kelompok</option>' + groups.map(group => `<option value="${escapeHtml(group)}">${escapeHtml(group)}</option>`).join('');
                if (groups.includes(selected)) {
                    select.value = selected;
                }
            });
        }

        document.addEventListener('change', function (event) {
            if (event.target.classList.contains('question-type')) {
                const card = event.target.closest('.question-card');
                renderQuestionConfig(card, event.target.value);
            }
        });

        document.addEventListener('input', function (event) {
            if (event.target.classList.contains('group-input') || event.target.classList.contains('item-label') || event.target.classList.contains('option-input')) {
                const card = event.target.closest('.question-card');
                if (card && card.querySelector('.question-type').value === 'grouping') {
                    syncGroupingSelects(card);
                }
                if (card && (card.querySelector('.question-type').value === 'multiple_choice' || card.querySelector('.question-type').value === 'checkbox')) {
                    refreshAnswerKey(card);
                }
            }
        });

        document.getElementById('session-form').addEventListener('submit', function () {
            document.querySelectorAll('.question-card').forEach(card => {
                const index = card.dataset.index;
                const type = card.querySelector('.question-type').value;
                const questionText = card.querySelector('.question-text').value;
                const points = card.querySelector('.question-points').value;

                card.querySelectorAll('input.generated-field, textarea.generated-field').forEach(el => el.remove());

                const typeInput = document.createElement('input');
                typeInput.type = 'hidden';
                typeInput.name = `questions[${index}][type]`;
                typeInput.value = type;
                typeInput.className = 'generated-field';
                card.appendChild(typeInput);

                const questionInput = document.createElement('input');
                questionInput.type = 'hidden';
                questionInput.name = `questions[${index}][question_text]`;
                questionInput.value = questionText;
                questionInput.className = 'generated-field';
                card.appendChild(questionInput);

                const pointsInput = document.createElement('input');
                pointsInput.type = 'hidden';
                pointsInput.name = `questions[${index}][points]`;
                pointsInput.value = points;
                pointsInput.className = 'generated-field';
                card.appendChild(pointsInput);

                if (type === 'multiple_choice' || type === 'checkbox') {
                    const optionInputs = Array.from(card.querySelectorAll('.option-input'));
                    optionInputs.forEach((input) => {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = `questions[${index}][options][]`;
                        hidden.value = input.value;
                        hidden.className = 'generated-field';
                        card.appendChild(hidden);
                    });

                    if (type === 'multiple_choice') {
                        const radio = card.querySelector(`input[name="questions[${index}][correct_index]"]:checked`);
                        if (radio) {
                            const answerHidden = document.createElement('input');
                            answerHidden.type = 'hidden';
                            answerHidden.name = `questions[${index}][correct_index]`;
                            answerHidden.value = radio.value;
                            answerHidden.className = 'generated-field';
                            card.appendChild(answerHidden);
                        }
                    } else {
                        const checked = card.querySelectorAll(`input[name="questions[${index}][correct_answer][]"]:checked`);
                        checked.forEach(item => {
                            const answerHidden = document.createElement('input');
                            answerHidden.type = 'hidden';
                            answerHidden.name = `questions[${index}][correct_answer][]`;
                            answerHidden.value = item.value;
                            answerHidden.className = 'generated-field';
                            card.appendChild(answerHidden);
                        });
                    }
                }

                if (type === 'drag_drop') {
                    const optionInputs = Array.from(card.querySelectorAll('.option-input'));
                    optionInputs.forEach(input => {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = `questions[${index}][options][]`;
                        hidden.value = input.value;
                        hidden.className = 'generated-field';
                        card.appendChild(hidden);
                    });

                    const orderInput = card.querySelector('.correct-order-input');
                    const hidden = document.createElement('textarea');
                    hidden.name = `questions[${index}][correct_answer]`;
                    hidden.value = orderInput ? orderInput.value : '';
                    hidden.className = 'generated-field d-none';
                    card.appendChild(hidden);
                }

                if (type === 'grouping') {
                    const groups = Array.from(card.querySelectorAll('.group-input'))
                        .map(input => input.value.trim())
                        .filter(Boolean);

                    const items = Array.from(card.querySelectorAll('.item-row')).map(row => {
                        return {
                            item: row.querySelector('.item-label')?.value.trim() || '',
                            group: row.querySelector('.item-group-select')?.value || '',
                        };
                    }).filter(entry => entry.item !== '');

                    const groupsHidden = document.createElement('input');
                    groupsHidden.type = 'hidden';
                    groupsHidden.name = `questions[${index}][options][]`;
                    groupsHidden.value = JSON.stringify(groups);
                    groupsHidden.className = 'generated-field';
                    card.appendChild(groupsHidden);

                    const groupingHidden = document.createElement('input');
                    groupingHidden.type = 'hidden';
                    groupingHidden.name = `questions[${index}][correct_answer]`;
                    groupingHidden.value = JSON.stringify(items);
                    groupingHidden.className = 'generated-field';
                    card.appendChild(groupingHidden);
                }
            });
        });

        function escapeHtml(value) {
            return String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        if (existingQuestions && existingQuestions.length) {
            existingQuestions.forEach(question => addQuestion(question));
        } else {
            addQuestion();
        }
    </script>
</x-app-layout>
