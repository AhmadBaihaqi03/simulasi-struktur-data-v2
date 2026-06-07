<x-app-layout>
    <style>
        body { background-color: #f8fafc; }
        .text-indigo { color: #5c60f5 !important; }
        .bg-indigo { background-color: #5c60f5 !important; }
        .bg-indigo-subtle { background-color: #eef0ff !important; }

        .card-eval { border-radius: 18px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.03); background: white; }

        /* Panel fixed — hanya desktop */
        .sticky-panel {
            position: fixed;
            top: 80px;
            z-index: 1000;
            border-top: 5px solid #5c60f5;
            border-radius: 20px;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
        }

        @media (max-width: 991px) {
            .sticky-panel {
                display: none !important;
            }
        }

        .question-text {
            font-size: 0.95rem;
            font-weight: 600;
            color: #475569;
            white-space: pre-line;
            text-align: justify;
            line-height: 1.5;
        }

        .content-text {
            white-space: pre-line;
            word-wrap: break-word;
            word-break: break-word;
            text-align: justify;
            color: #475569;
            font-weight: 400;
            line-height: 1.6;
            display: block;
            font-size: 0.95rem;
        }

        .phase-title {
            font-size: 1.05rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #5c60f5;
            display: block;
        }

        .answer-box {
            background-color: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 15px;
            padding: 16px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }

        .label-mini {
            font-weight: 800;
            color: #4a5568;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 0.6rem;
            display: block;
        }

        .phase-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
        .icon-box {
            width: 48px; height: 48px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 10px; background-color: #eef0ff; color: #5c60f5;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .member-tag {
            background: #f1f5f9;
            color: #475569;
            padding: 5px 12px;
            border-radius: 8px;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            font-size: 0.85rem;
        }

        .hover-lift { transition: transform 0.2s; }
        .hover-lift:hover { transform: translateY(-2px); }

        @media (max-width: 991px) {
            .content-bottom-spacer { padding-bottom: 100px; }
        }

        @media (max-width: 575px) {
            .solution-link-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
                padding: 16px;
            }
            .solution-link-card .ms-auto { margin-left: 0 !important; }
        }

        /* Textarea umpan balik - Mobile dan Desktop */
        .feedback-textarea {
            border-radius: 18px;
            font-size: 1rem;
            min-height: 130px;
            resize: vertical;
            border: 2px solid #e2e8f0;
            padding: 14px;
            width: 100%;
            outline: none;
            transition: all 0.2s ease-in-out;
            background-color: #ffffff;
            color: #2d3748;
            font-family: inherit;
        }
        .feedback-textarea::placeholder {
            color: #a0aec0;
        }
        .feedback-textarea:focus {
            border-color: #5c60f5;
            box-shadow: 0 0 0 4px rgba(92,96,245,0.08);
            background-color: white;
        }

        .btn-submit-feedback {
            position: relative;
            transition: all 0.3s ease;
        }

        .btn-submit-feedback:disabled {
            opacity: 0.7;
            pointer-events: none;
        }

        .btn-submit-feedback.loading {
            pointer-events: none;
        }

        .spinner-mini {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>

    <div class="container py-4 py-md-5">
        {{-- Breadcrumb --}}
        <div class="mb-4">
            <a href="{{ url()->previous() }}" class="text-decoration-none border-end pe-3 me-3" style="color: #5c60f5; min-height: 44px; display: inline-flex; align-items: center;">
                <i class="bi bi-grid-1x2-fill me-1"></i> Kembali Ke Panel Evaluasi
            </a>

            <div class="d-flex align-items-center flex-wrap gap-1 mt-3 mb-2">
                <span style="font-size: 10px; font-weight: 900; color: #64748b; text-transform: uppercase; letter-spacing: 0.2em;">Evaluasi | Sesi: </span>
                <span style="font-size: 11px; font-weight: 900; color: #5c60f5; letter-spacing: 0.1em; text-transform: uppercase; font-style: italic;">{{ $session->session_code }}</span>
                <span style="font-size: 10px; font-weight: 900; color: #64748b; text-transform: uppercase; letter-spacing: 0.2em;">| Kelompok:</span>
                <span style="font-size: 11px; font-weight: 900; color: #5c60f5; letter-spacing: 0.1em; text-transform: uppercase; font-style: italic;">{{ $group->group_name }}</span>
            </div>

            <h1 class="font-black text-slate-900 tracking-tight leading-tight"
                style="font-size: clamp(1.4rem, 5vw, 2rem); font-weight: 700; color: #0f172a;">
                {{ $session->title }}
            </h1>
        </div>

        {{-- Form evaluasi mencakup seluruh konten --}}
        <form action="{{ route('groups.evaluate', $group->id) }}" method="POST" id="evalForm">
            @csrf

            <div class="row g-4">

                {{-- Konten jawaban kelompok (KIRI) --}}
                <div class="col-12 col-lg-8 content-bottom-spacer">

                    {{-- Fase 1: Orientasi --}}
                    <div class="card card-eval p-3 p-md-4 mb-4">
                        <div class="phase-header">
                            <div class="icon-box"><i class="bi bi-rocket-takeoff-fill"></i></div>
                            <h5 class="phase-title m-0">ORIENTASI MASALAH & KELOMPOK SISWA</h5>
                        </div>

                        <div class="mb-4 px-1 px-md-2">
                            <label class="label-mini mb-2">Konteks Masalah:</label>
                            <div class="answer-box">
                                <div class="content-text">{{ $session->f1_context }}</div>
                            </div>
                        </div>

                        <div class="mb-4 px-1 px-md-2">
                            <label class="label-mini mb-2">Tujuan Pembelajaran:</label>
                            <div class="row g-2">
                                @forelse($session->f1_learning_objectives ?? [] as $outcome)
                                    <div class="col-md-6">
                                        <div class="bg-indigo-subtle p-3 h-100 shadow-sm" style="border-radius: 12px; border-left: 4px solid #5c60f5;">
                                            <div class="content-text">{{ $outcome }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 text-muted small fst-italic">Belum ada tujuan pembelajaran.</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="mb-4 px-1 px-md-2 mt-3 pt-3 border-top">
                            <label class="label-mini mb-2">Anggota Kelompok:</label>
                            <div class="d-flex flex-wrap gap-2">
                                @php
                                    $members = is_array($group->student_data) ? ($group->student_data['members'] ?? $group->student_data) : json_decode($group->student_data, true);
                                @endphp
                                @foreach($members ?? [] as $name)
                                    <span class="member-tag"><i class="bi bi-person-fill me-1"></i> {{ $name }}</span>
                                @endforeach
                            </div>
                        </div>

                        <div class="px-1 px-md-2">
                            <label class="label-mini mb-2">KELAS:</label>
                            <span class="member-tag d-inline-flex align-items-center"
                                style="background-color: #eef0ff; color: #5c60f5; border: 1.5px solid #d0d7ff;">
                                <i class="bi bi-door-open-fill me-2" style="font-size: 0.85rem;"></i>
                                {{ $group->class_name ?? '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- Fase 3: Penyelidikan --}}
                    <div class="card card-eval p-3 p-md-4 mb-4">
                        <div class="phase-header">
                            <div class="icon-box"><i class="bi bi-card-checklist"></i></div>
                            <h5 class="phase-title m-0">PENYELIDIKAN</h5>
                        </div>
                        @foreach($session->f3_questions ?? [] as $index => $q)
                            <div class="mb-4 px-1 px-md-2">
                                <div class="question-text mb-2">{{ $q }}</div>
                                <div class="answer-box">
                                    <span class="label-mini d-block mb-2">Jawaban Kelompok:</span>
                                    <div class="content-text">{{ $group->f3_answers[$index] ?? 'Kosong.' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Fase 4: Solusi --}}
                    <div class="card card-eval p-3 p-md-4 mb-4 border-0 shadow-sm" style="border-radius: 20px;">
                        <div class="phase-header mb-1">
                            <div class="icon-box"><i class="bi bi-code-slash"></i></div>
                            <h5 class="phase-title m-0">MENGEMBANGKAN & MENYAJIKAN SOLUSI</h5>
                        </div>

                        <div class="px-1 px-md-2">
                            <div class="mb-3">
                                <div class="question-text">{{ $session->f4_instruction ?? 'Implementasikan solusi koding di bawah ini.' }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="label-mini mb-2">TAUTAN EKSTERNAL SISWA:</label>
                                @if($group->f4_link)
                                <a href="{{ $group->f4_link }}" target="_blank"
                                class="answer-box d-flex align-items-center gap-3 text-decoration-none hover-lift w-100"
                                style="transition: all 0.2s; border-color: #5c60f5; background: #f8faff; padding: 16px; flex-wrap: wrap;">
                                    <div class="icon-box shadow-sm" style="width: 52px; height: 52px; min-width: 52px; font-size: 1.6rem; background: #eef0ff; border-radius: 14px; color: #5c60f5;">
                                        <i class="bi bi-link-45deg"></i>
                                    </div>
                                    <div class="overflow-hidden flex-grow-1" style="min-width: 0;">
                                        <div class="text-indigo fw-bold text-truncate" style="font-size: 1rem; margin-bottom: 4px;">
                                            {{ $group->f4_link }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.85rem; font-weight: 500;">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Klik untuk meninjau hasil kode siswa
                                        </div>
                                    </div>
                                </a>
                                @else
                                    <div class="answer-box text-muted fst-italic" style="font-size: 0.9rem;">Belum ada tautan yang dilampirkan.</div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <div class="question-text mb-3" style="font-size: 0.95rem;">
                                    {{ $session->f4_question ?? 'Jelaskan bagaimana sistem yang Anda buat bekerja.' }}
                                </div>
                                <div class="answer-box">
                                    <small class="label-mini d-block mb-2 text-muted">Jawaban Kelompok:</small>
                                    <div class="content-text">{{ $group->f4_answers ?? 'Siswa tidak memberikan penjelasan.' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Fase 5: Evaluasi --}}
                    <div class="card card-eval p-3 p-md-4 mb-4">
                        <div class="phase-header">
                            <div class="icon-box"><i class="bi bi-chat-dots-fill"></i></div>
                            <h5 class="phase-title m-0">EVALUASI</h5>
                        </div>
                        @foreach($session->f5_questions ?? [] as $index => $r)
                            <div class="mb-4 px-1 px-md-2">
                                <div class="question-text mb-2">{{ $r }}</div>
                                <div class="answer-box">
                                    <small class="label-mini d-block mb-2">Jawaban Kelompok:</small>
                                    <div class="content-text">{{ $group->f5_answers[$index] ?? 'Kosong.' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- ===== FORM UMPAN BALIK — tampil di mobile setelah semua jawaban ===== --}}
                    <div class="d-lg-none card card-eval p-4 mb-2" style="border-top: 4px solid #5c60f5;">
                        <div class="phase-header">
                            <div class="icon-box" style="background:#eef0ff; color:#5c60f5;">
                                <i class="bi bi-chat-square-text-fill"></i>
                            </div>
                            <div>
                                <h5 class="phase-title m-0">UMPAN BALIK GURU</h5>
                                <p class="text-muted mb-0 mt-1" style="font-size: 0.85rem;">untuk {{ $group->group_name }}</p>
                            </div>
                        </div>

                        <div class="mt-2">
                            <label class="label-mini d-block mb-2">Tulis Umpan Balik Anda</label>
                            @php
                                $hasFeedback = $group->GroupEvaluation && $group->GroupEvaluation->feedback_comment;
                                $feedbackContent = $hasFeedback ? $group->GroupEvaluation->feedback_comment : '';
                            @endphp
                            @if($hasFeedback)
                                <div class="alert alert-info d-flex align-items-center mb-2" style="border-radius: 12px; border-left: 4px solid #0dcaf0; background: #cfe2ff; color: #084298; font-size: 0.85rem; padding: 0.6rem 0.8rem;">
                                    <i class="bi bi-check-circle me-1" style="font-size: 0.8rem;"></i>
                                    <span><strong>Tersimpan</strong> - dapat diedit</span>
                                </div>
                            @endif
                            <textarea name="feedback_comment" class="feedback-textarea" id="feedbackMobile"
                                      placeholder="Tulis feedback untuk kelompok ini setelah membaca seluruh jawaban di atas..."
                                      required>{{ $feedbackContent }}</textarea>
                        </div>
                    </div>

                </div>

                {{-- Panel Evaluasi Guru: desktop only, sticky on right --}}
                <div class="col-lg-4 d-none d-lg-block">
                    <div class="sticky-panel card card-eval p-4 shadow-lg border-0">
                        <div class="text-center mb-4 pb-3 border-bottom">
                            <h6 class="mb-1" style="font-size: 1rem; font-weight:700;">EVALUASI GURU</h6>
                            <p class="mb-0 text-muted" style="font-size: 0.9rem;">{{ $group->group_name }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="label-mini d-block mb-2">Umpan Balik</label>
                            @php
                                $hasFeedback = $group->GroupEvaluation && $group->GroupEvaluation->feedback_comment;
                                $feedbackContent = $hasFeedback ? $group->GroupEvaluation->feedback_comment : '';
                            @endphp
                            @if($hasFeedback)
                                <div class="alert alert-info d-flex align-items-center mb-2" style="border-radius: 12px; border-left: 4px solid #0dcaf0; background: #cfe2ff; color: #084298; font-size: 0.9rem; padding: 0.75rem 1rem;">
                                    <i class="bi bi-check-circle me-2"></i>
                                    <span><strong>Tersimpan:</strong> Umpan balik dapat diedit. Perubahan akan disimpan setelah Anda klik tombol.</span>
                                </div>
                            @endif
                            <textarea name="feedback_comment" class="feedback-textarea" id="feedbackDesktop"
                                rows="6"
                                placeholder="Tulis feedback untuk kelompok ini..." required>{{ $feedbackContent }}</textarea>
                        </div>

                        <button type="submit" class="btn-submit-feedback btn bg-indigo text-white w-100 py-3 fw-bold shadow-lg mb-3"
                                style="border-radius: 15px; font-size: 0.9rem; min-height: 44px;">
                            <span class="btn-text"><i class="bi bi-send-fill me-2"></i> SIMPAN UMPAN BALIK</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- ===== FIXED BOTTOM BAR — hanya mobile/tablet (< lg) ===== --}}
    <div class="d-lg-none fixed-bottom border-top shadow-lg"
         style="background: rgba(255,255,255,0.97); backdrop-filter: blur(12px); z-index: 1050; padding: 12px 16px;">
        <div class="d-flex gap-2 align-items-center" style="max-width: 600px; margin: 0 auto;">
            <div class="text-muted" style="font-size: 0.75rem; font-weight: 700; line-height: 1.2; flex-shrink: 0;">
                <i class="bi bi-check2-circle text-success"></i><br>
                <span>Sudah baca?</span>
            </div>
            <button type="submit" form="evalForm" class="btn-submit-feedback btn bg-indigo text-white fw-bold flex-grow-1 shadow-sm"
                    style="border-radius: 14px; font-size: 0.9rem; min-height: 52px; letter-spacing: 0.03em;">
                <span class="btn-text"><i class="bi bi-send-fill me-2"></i> SIMPAN UMPAN BALIK</span>
            </button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const evalForm = document.getElementById('evalForm');
            const feedbackMobileEl = document.getElementById('feedbackMobile');
            const feedbackDesktopEl = document.getElementById('feedbackDesktop');
            const stickyPanel = document.querySelector('.sticky-panel');
            let isSubmitting = false;
            let submitTimer = null;

            console.log('=== FEEDBACK FORM INIT ===');
            console.log('Mobile textarea:', feedbackMobileEl?.value || 'NOT FOUND');
            console.log('Desktop textarea:', feedbackDesktopEl?.value || 'NOT FOUND');

            // ===== FIX PANEL POSITION =====
            function fixPanelPosition() {
                if (!stickyPanel) {
                    console.warn('sticky-panel not found');
                    return;
                }

                const colLg4 = stickyPanel.closest('.col-lg-4');
                if (!colLg4) {
                    console.warn('col-lg-4 parent not found');
                    return;
                }

                const rect = colLg4.getBoundingClientRect();

                // Find first card to align panel top with it
                const firstCard = document.querySelector('.card-eval');
                let topValue = 60; // Default fallback

                if (firstCard) {
                    const cardRect = firstCard.getBoundingClientRect();
                    topValue = cardRect.top + window.scrollY - window.scrollY;
                    // Simpler: just use the viewport distance from first card
                    topValue = Math.max(cardRect.top, 60);
                }

                // Set fixed positioning with calculated left position
                stickyPanel.style.position = 'fixed';
                stickyPanel.style.left = rect.left + 'px';
                stickyPanel.style.width = rect.width + 'px';
                stickyPanel.style.top = topValue + 'px';

                console.log('Panel fixed at:', {
                    left: rect.left + 'px',
                    width: rect.width + 'px',
                    top: topValue + 'px'
                });
            }

            // Initial positioning
            setTimeout(fixPanelPosition, 100);

            // Fix position on all scroll/resize events
            window.addEventListener('scroll', fixPanelPosition, true);
            window.addEventListener('resize', fixPanelPosition);
            window.addEventListener('orientationchange', fixPanelPosition);

            // Helper function to check if element is truly visible
            function isElementVisible(element) {
                if (!element) return false;
                const style = window.getComputedStyle(element);
                return style.display !== 'none' &&
                       style.visibility !== 'hidden' &&
                       style.opacity !== '0' &&
                       element.offsetParent !== null;
            }

            // Initialize textareas with same content from server
            function initializeTextareas() {
                if (!feedbackMobileEl || !feedbackDesktopEl) {
                    console.warn('Warning: One or both textareas not found');
                    return;
                }

                const mobileContent = feedbackMobileEl.value.trim();
                const desktopContent = feedbackDesktopEl.value.trim();

                // Use the one that has content, or sync both if one is empty
                const contentToSync = mobileContent || desktopContent;

                if (contentToSync) {
                    feedbackMobileEl.value = contentToSync;
                    feedbackDesktopEl.value = contentToSync;
                    console.log('Textareas synced with content:', contentToSync.substring(0, 50) + '...');
                } else {
                    console.log('No existing feedback found');
                }
            }

            // Reset button state
            function resetButtonState() {
                const submitButtons = document.querySelectorAll('.btn-submit-feedback');
                submitButtons.forEach(btn => {
                    btn.disabled = false;
                    btn.classList.remove('loading');
                    const btnText = btn.querySelector('.btn-text');
                    if (btnText) {
                        btnText.innerHTML = '<i class="bi bi-send-fill me-2"></i> SIMPAN UMPAN BALIK';
                    }
                });
                isSubmitting = false;
                if (submitTimer) clearTimeout(submitTimer);
                console.log('Button state reset');
            }

            // Set loading state
            function setLoadingState() {
                const submitButtons = document.querySelectorAll('.btn-submit-feedback');
                submitButtons.forEach(btn => {
                    btn.disabled = true;
                    btn.classList.add('loading');
                    const btnText = btn.querySelector('.btn-text');
                    if (btnText) {
                        btnText.innerHTML = '<span class="spinner-mini"></span>Menyimpan...';
                    }
                });
                console.log('Loading state active');
            }

            // Form submission handler
            evalForm.addEventListener('submit', function(e) {
                console.log('=== FORM SUBMIT ===');

                // Prevent double submissions
                if (isSubmitting) {
                    console.warn('Already submitting, preventing duplicate');
                    e.preventDefault();
                    return false;
                }

                // Determine which textarea is visible
                let visibleTextarea = null;
                let feedbackContent = '';

                if (feedbackMobileEl && isElementVisible(feedbackMobileEl)) {
                    visibleTextarea = feedbackMobileEl;
                    console.log('Mobile textarea is visible');
                } else if (feedbackDesktopEl && isElementVisible(feedbackDesktopEl)) {
                    visibleTextarea = feedbackDesktopEl;
                    console.log('Desktop textarea is visible');
                }

                // Get and validate feedback
                if (visibleTextarea) {
                    feedbackContent = visibleTextarea.value.trim();
                    console.log('Feedback content length:', feedbackContent.length);
                } else {
                    console.warn('No visible textarea found');
                }

                // Validate feedback is filled
                if (!feedbackContent || feedbackContent.length < 5) {
                    e.preventDefault();
                    alert('⚠️ Mohon isi umpan balik minimal 5 karakter sebelum menyimpan.');
                    if (visibleTextarea) {
                        visibleTextarea.focus();
                        visibleTextarea.style.borderColor = '#dc3545';
                        setTimeout(() => {
                            visibleTextarea.style.borderColor = '';
                        }, 2000);
                    }
                    return false;
                }

                // Sync feedback to BOTH textareas before submission
                if (feedbackMobileEl) feedbackMobileEl.value = feedbackContent;
                if (feedbackDesktopEl) feedbackDesktopEl.value = feedbackContent;

                console.log('Feedback synced to both textareas:', feedbackContent.substring(0, 50) + '...');

                // Set submitting state
                isSubmitting = true;
                setLoadingState();

                // Timeout recovery (30 seconds)
                submitTimer = setTimeout(function() {
                    console.warn('Form submission timeout after 30 seconds');
                    resetButtonState();
                    alert('⚠️ Penyimpanan membutuhkan waktu lebih lama. Silakan coba lagi.');
                }, 30000);

                // Form will proceed with submission
                return true;
            });

            // Real-time sync between textareas when user types
            if (feedbackMobileEl) {
                feedbackMobileEl.addEventListener('input', function() {
                    if (feedbackDesktopEl) {
                        feedbackDesktopEl.value = this.value;
                    }
                });
            }

            if (feedbackDesktopEl) {
                feedbackDesktopEl.addEventListener('input', function() {
                    if (feedbackMobileEl) {
                        feedbackMobileEl.value = this.value;
                    }
                });
            }

            // Recovery handlers for when user navigates away or tab visibility changes
            window.addEventListener('pageshow', function() {
                if (isSubmitting) {
                    console.log('Page shown again, resetting state');
                    resetButtonState();
                }
            });

            window.addEventListener('focus', function() {
                if (isSubmitting && document.querySelectorAll('.btn-submit-feedback.loading').length > 0) {
                    console.log('Window focused, resetting state');
                    resetButtonState();
                }
            });

            document.addEventListener('visibilitychange', function() {
                if (!document.hidden && isSubmitting) {
                    console.log('Tab became visible, resetting state');
                    resetButtonState();
                }
            });

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                if (submitTimer) clearTimeout(submitTimer);
            });

            // Initialize on page load
            initializeTextareas();
            console.log('=== INIT COMPLETE ===');
        });
    </script>

</x-app-layout>
