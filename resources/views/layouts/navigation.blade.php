<nav x-data="{ open: false }" class="bg-white border-b border-slate-100 shadow-sm">
    <style>
        /* ========================================
           NAVIGATION MOBILE OPTIMIZATION
           ======================================== */

        /* Navbar container improvements */
        nav {
            position: relative;
        }

        /* Smooth animations */
        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Desktop dropdown */
        .premium-dropdown [x-show="open"] {
            margin-top: 12px !important;
            border-radius: 16px !important;
            border: none !important;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.12) !important;
            overflow: hidden;
            padding: 0 !important;
            background: white !important;
            animation: dropdownFadeIn 0.3s ease-out;
            z-index: 10000 !important;
        }

        .premium-dropdown div,
        .premium-dropdown button:focus,
        .premium-dropdown div:focus {
            outline: none !important;
            box-shadow: none !important;
            --tw-ring-color: transparent !important;
        }

        /* Active nav link indicator */
        .nav-link-active {
            color: #4F46E5 !important;
            position: relative;
        }

        .nav-link-active::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 50%;
            transform: translateX(-50%);
            width: 16px;
            height: 4px;
            background: #4F46E5;
            border-radius: 4px 4px 0 0;
            transition: all 0.2s ease;
        }

        /* Mobile menu slide animation */
        .mobile-menu-panel {
            animation: slideDown 0.25s ease-out;
        }

        /* ========================================
           MOBILE OPTIMIZATION (max-width: 575px)
           ======================================== */
        @media (max-width: 575px) {
            .max-w-7xl {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            nav .max-w-7xl {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            /* Navbar height and padding */
            nav .flex.justify-between.h-16 {
                height: 56px;
                padding: 0;
            }

            /* Logo and branding */
            .shrink-0.flex.items-center.gap-2 {
                gap: 0.5rem;
            }

            .shrink-0.flex.items-center.gap-2 svg {
                width: 24px;
                height: 24px;
            }

            .shrink-0.flex.items-center.gap-2 span {
                font-size: 1rem;
                letter-spacing: -0.5px;
            }

            /* Desktop nav links hidden on mobile */
            .hidden.space-x-8.sm\:-my-px.sm\:ms-10.sm\:flex {
                display: none !important;
            }

            /* Hamburger button improvements */
            button[aria-label="Menu navigasi"] {
                width: 44px;
                height: 44px;
                min-height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 10px;
                margin-right: -0.5rem;
                transition: all 0.2s ease;
                -webkit-tap-highlight-color: transparent;
            }

            button[aria-label="Menu navigasi"]:active {
                background-color: #f1f5f9;
                transform: scale(0.95);
            }

            button[aria-label="Menu navigasi"] svg {
                width: 24px;
                height: 24px;
            }

            /* Mobile menu panel */
            .sm\:hidden.border-t.border-slate-100.bg-white.shadow-lg {
                position: relative;
                border-radius: 0;
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
                animation: slideDown 0.25s ease-out;
            }

            /* User info section */
            .px-4.pt-4.pb-3.border-b.border-slate-50.bg-slate-50\/50 {
                padding: 1rem 1rem;
                gap: 0.75rem;
            }

            .px-4.pt-4.pb-3.border-b.border-slate-50.bg-slate-50\/50 .h-10.w-10 {
                height: 44px;
                width: 44px;
                min-height: 44px;
                min-width: 44px;
            }

            .px-4.pt-4.pb-3.border-b.border-slate-50.bg-slate-50\/50 p {
                margin-bottom: 0;
                line-height: 1.3;
            }

            /* Navigation links section */
            .px-4.pt-2.pb-2.space-y-1 {
                padding: 0.75rem 1rem;
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }

            /* Menu items */
            .px-4.pt-2.pb-2.space-y-1 a {
                padding: 0.75rem 1rem;
                height: auto;
                min-height: 48px;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                transition: all 0.2s ease;
                -webkit-tap-highlight-color: transparent;
                margin: 0;
            }

            .px-4.pt-2.pb-2.space-y-1 a:active {
                transform: scale(0.97);
            }

            .px-4.pt-2.pb-2.space-y-1 a i {
                font-size: 16px;
                width: 20px;
                display: flex;
                align-items: center;
            }

            /* Active link state */
            .bg-indigo-50.text-indigo-600 {
                background-color: #eef0ff;
                color: #4F46E5;
                box-shadow: inset 0 0 0 1.5px #c7d2fe;
            }

            /* Hover state */
            a.hover\:bg-slate-50:hover {
                background-color: #f1f5f9;
            }

            /* Logout section */
            .px-4.pb-4.pt-2.border-t.border-slate-100 {
                padding: 1rem 1rem;
                border-top: 1px solid #e2e8f0;
            }

            .px-4.pb-4.pt-2.border-t.border-slate-100 button {
                padding: 0.75rem 1rem;
                height: auto;
                min-height: 48px;
                width: 100%;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                transition: all 0.2s ease;
                -webkit-tap-highlight-color: transparent;
            }

            .px-4.pb-4.pt-2.border-t.border-slate-100 button:active {
                transform: scale(0.97);
            }

            .px-4.pb-4.pt-2.border-t.border-slate-100 button:hover {
                background-color: #fef2f2;
            }

            .px-4.pb-4.pt-2.border-t.border-slate-100 button i {
                font-size: 16px;
            }

            /* Icon improvements */
            i.bi {
                flex-shrink: 0;
            }
        }

        /* Tablet and larger */
        @media (min-width: 768px) {
            .max-w-7xl {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }

            nav .max-w-7xl {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }

            /* Desktop dropdown improvements */
            .premium-dropdown [x-show="open"] {
                min-width: 280px;
            }

            /* Desktop nav links */
            .hidden.space-x-8.sm\:-my-px.sm\:ms-10.sm\:flex {
                display: flex !important;
            }

            /* Dropdown content */
            .bg-white {
                min-width: 250px;
            }
        }

        /* Accessibility */
        @media (prefers-reduced-motion: reduce) {
            .mobile-menu-panel,
            .premium-dropdown [x-show="open"],
            button,
            a {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Focus visible for keyboard navigation */
        button:focus-visible,
        a:focus-visible {
            outline: 2px solid #4F46E5;
            outline-offset: 2px;
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center gap-2">
                    <svg width="28" height="28" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="6" y="22" width="10" height="10" rx="2" fill="#4F46E5"/>
                        <rect x="24" y="8" width="10" height="10" rx="2" fill="#1E293B"/>
                        <path d="M16 27C22 27 20 13 24 13" stroke="#6366F1" stroke-width="2" stroke-linecap="round" stroke-dasharray="2 2"/>
                        <path d="M22 15L24 13L22 11" stroke="#6366F1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-lg font-bold tracking-tighter text-slate-900">
                        Vi<span class="text-indigo-600">logic</span>
                    </span>
                </div>

               
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard.individu') }}"
                    class="inline-flex items-center px-1 pt-1 text-sm font-bold no-underline transition-colors {{ request()->routeIs('dashboard.individu') ? 'nav-link-active' : 'text-slate-400 hover:text-slate-600' }}">
                        {{ __('Sesi Individu') }}
                    </a>
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center px-1 pt-1 text-sm font-bold no-underline transition-colors {{ request()->routeIs('dashboard') ? 'nav-link-active' : 'text-slate-400 hover:text-slate-600' }}">
                        {{ __('Sesi Kelompok') }}
                    </a>
                </div>
            </div>

            {{-- Desktop User Dropdown --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="ms-3 relative premium-dropdown">
                    <x-dropdown align="right" width="60">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-2 py-1 border-none text-sm font-bold rounded-2xl text-slate-700 bg-transparent hover:bg-slate-50 transition-all duration-200 gap-3" style="min-height:44px;">
                                <div class="h-9 w-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 border border-slate-200">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div class="text-start hidden md:block leading-tight">
                                    <p class="mb-0 text-[14px] font-bold text-slate-800">{{ Auth::user()->name }}</p>
                                    <p class="mb-0 text-[10px] text-indigo-500 font-bold uppercase tracking-widest">Guru</p>
                                </div>
                                <i class="bi bi-chevron-down text-slate-300" style="font-size: 10px;"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="bg-white">
                                <div class="px-5 py-4 bg-slate-50/50">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Email Anda</p>
                                    <p class="text-sm font-bold text-slate-700 truncate mb-0">{{ Auth::user()->email }}</p>
                                </div>

                                <div class="p-2">
                                    <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-3 px-3 py-3 rounded-2xl font-bold text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 border-none transition-all duration-200">
                                        <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                                            <i class="bi bi-person-circle"></i>
                                        </div>
                                        <span>Profil Saya</span>
                                    </x-dropdown-link>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                                class="flex items-center gap-3 px-3 py-3 rounded-2xl font-bold text-red-500 hover:bg-red-50 border-none transition-all duration-200"
                                                onclick="event.preventDefault(); this.closest('form').submit();">
                                            <div class="w-8 h-8 rounded-xl bg-red-50 flex items-center justify-center text-red-500">
                                                <i class="bi bi-power"></i>
                                            </div>
                                            <span>Keluar Sistem</span>
                                        </x-dropdown-link>
                                    </form>
                                </div>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            {{-- Mobile Hamburger Button --}}
            <div class="flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center w-11 h-11 rounded-xl text-slate-400 hover:bg-slate-50 transition-colors"
                        aria-label="Menu navigasi">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu Panel --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="sm:hidden border-t border-slate-100 bg-white shadow-lg">

        {{-- User Info --}}
        <div class="px-4 pt-4 pb-3 border-b border-slate-50 bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 border border-slate-200 shrink-0">
                    <i class="bi bi-person-fill text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-800 mb-0 leading-tight">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-widest mb-0">Guru</p>
                </div>
            </div>
        </div>

        {{-- Navigation Links --}}
        <div class="px-4 pt-2 pb-2 space-y-1">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-600 hover:bg-slate-50' }}"
               @click="open = false">
                <i class="bi bi-grid-1x2-fill {{ request()->routeIs('dashboard') ? 'text-indigo-500' : 'text-slate-400' }}"></i>
                Dashboard
            </a>

            <a href="{{ route('profile.edit') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm text-slate-600 hover:bg-slate-50 transition-colors"
               @click="open = false">
                <i class="bi bi-person-circle text-slate-400"></i>
                Profil Saya
            </a>
        </div>

        {{-- Logout --}}
        <div class="px-4 pb-4 pt-2 border-t border-slate-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm text-red-500 hover:bg-red-50 transition-colors">
                    <i class="bi bi-power"></i>
                    Keluar Sistem
                </button>
            </form>
        </div>
    </div>
</nav>
