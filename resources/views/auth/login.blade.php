<x-guest-layout>
    @section('title', 'Vilogic - Login')
    <div class="min-h-screen flex items-center justify-center bg-[#e0e7ff] p-4 lg:p-8 relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-indigo-100/40 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-purple-100/40 rounded-full blur-3xl"></div>

        <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 gap-12 items-center relative z-10">

            <div class="hidden lg:flex flex-col space-y-8 pl-10">
                <div class="flex items-center gap-2 cursor-pointer" onclick="window.location='{{ route('beranda') }}'">
                    <svg width="32" height="32" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="6" y="22" width="10" height="10" rx="2" fill="#4F46E5"/>
                        <rect x="24" y="8" width="10" height="10" rx="2" fill="#1E293B"/>
                        <path d="M16 27C22 27 20 13 24 13" stroke="#6366F1" stroke-width="2" stroke-linecap="round" stroke-dasharray="2 2"/>
                        <path d="M22 15L24 13L22 11" stroke="#6366F1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="text-xl font-bold tracking-tighter text-slate-900">
                        Vi<span class="text-indigo-600">logic</span>
                    </span>
                </div>

                <div class="space-y-4">
                    <h2 class="text-5xl font-extrabold text-slate-900 leading-[1.1] tracking-tighter">
                        Pantau Progres <br> <span class="text-indigo-600">Logika Siswa</span> Anda.
                    </h2>
                    <p class="text-lg text-slate-500 leading-relaxed max-w-md">
                        Masuk sebagai guru untuk mengelola sesi belajar, dan memberikan umpan balik kepada siswa.
                    </p>
                </div>
            </div>

            <div class="w-full max-w-md mx-auto bg-white border border-indigo-50 rounded-[2.5rem] p-10 shadow-[0_25px_70px_rgba(79,70,229,0.07)]">
                <div class="mb-10 text-center lg:text-left">
                    <h2 class="text-2xl font-bold text-slate-900">Selamat Datang Kembali</h2>
                    <p class="text-sm text-slate-500 mt-2 font-medium">Khusus akses Guru</p>
                </div>

                <x-auth-session-status class="mb-6" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div class="space-y-2">
                        <label for="email" class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Email</label>
                        <x-text-input id="email" class="block w-full px-5 py-4 bg-white border-slate-100 rounded-2xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm font-semibold"
                            style="color: #757575 !important;" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@gmail.com" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs font-bold text-red-500" />
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Password</label>
                        <x-text-input id="password" class="block w-full px-5 py-4 bg-white border-slate-100 rounded-2xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm font-semibold"
                            style="color: #757575 !important;" type="password" name="password" required placeholder="••••••••" />

                        <div class="flex items-center justify-between mt-4 px-1">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4" name="remember">
                                <span class="ms-2 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Ingat Saya</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 transition-colors uppercase tracking-widest">
                                    Lupa Password?
                                </a>
                            @endif
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs font-bold text-red-500" />
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-indigo-500 py-4 text-white rounded-2xl text-[13px] font-black hover:shadow-2xl hover:shadow-indigo-500/40 transition-all active:scale-[0.98] uppercase tracking-[0.2em] shadow-lg shadow-indigo-100">
                            Masuk Ke Dashboard
                        </button>
                    </div>
                </form>

                <div class="mt-10 text-center pt-8 border-t border-slate-50">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-indigo-600 hover:underline ml-1">Daftar</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
