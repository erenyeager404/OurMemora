<div id="authModal" class="fixed inset-0 z-50 hidden items-center justify-center"
    style="background:rgba(0,0,0,.75);backdrop-filter:blur(12px)" onclick="if(event.target===this) closeModal()">

    <div class="w-full max-w-sm mx-4 rounded-2xl overflow-hidden shadow-2xl"
        style="background:#0f111a;border:1px solid rgba(255,255,255,.1)">

        <div class="px-6 pt-6 pb-0">
            <div class="flex items-center justify-between mb-5">
                <span class="font-bold">Our<span class="text-violet-400">Memora</span></span>
                <button onclick="closeModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-500 hover:text-white transition-colors"
                    style="background:rgba(255,255,255,.06)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div id="modalCtx" class="hidden mb-4 px-4 py-3 rounded-xl text-sm text-violet-300"
                style="background:rgba(124,58,237,.12);border:1px solid rgba(124,58,237,.25)"></div>

            <div class="flex gap-1 p-1 rounded-xl mb-5" style="background:rgba(255,255,255,.06)">
                <button id="tLogin" onclick="switchTab('login')"
                    class="flex-1 py-2 rounded-xl text-sm font-medium transition-all"
                    style="background:rgba(124,58,237,.4);border:1px solid rgba(124,58,237,.6);color:#e9d5ff">
                    Masuk
                </button>
                <button id="tReg" onclick="switchTab('register')"
                    class="flex-1 py-2 rounded-xl text-sm font-medium transition-all"
                    style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:#9ca3af">
                    Daftar
                </button>
            </div>
        </div>

        {{-- Login --}}
        <div id="fLogin" class="px-6 pb-6">
            <form method="POST" action="{{ route('login') }}" class="space-y-3">
                @csrf
                <div>
                    <label
                        class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@kamu.com"
                        class="w-full px-4 py-2.5 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none"
                        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                    @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label
                        class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="lPw" placeholder="••••••••"
                            class="w-full px-4 py-2.5 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none pr-10"
                            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                        <button type="button" onclick="togglePw('lPw')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit"
                    class="w-full py-2.5 rounded-xl text-sm font-medium text-white transition-all hover:opacity-90"
                    style="background:rgba(124,58,237,.5);border:1px solid rgba(124,58,237,.6)">
                    Masuk
                </button>
            </form>

            <div class="flex items-center gap-3 my-4">
                <hr class="flex-1" style="border-color:rgba(255,255,255,.07)">
                <span class="text-[11px] text-gray-600">atau</span>
                <hr class="flex-1" style="border-color:rgba(255,255,255,.07)">
            </div>

            <a href="{{ route('auth.google') }}"
                class="flex items-center justify-center gap-2.5 py-2.5 rounded-xl text-sm text-gray-300 transition-all hover:bg-white/10"
                style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                <svg width="16" height="16" viewBox="0 0 48 48">
                    <path fill="#EA4335"
                        d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z" />
                    <path fill="#4285F4"
                        d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z" />
                    <path fill="#FBBC05"
                        d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z" />
                    <path fill="#34A853"
                        d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z" />
                </svg>
                Lanjutkan dengan Google
            </a>
            <p class="text-center text-[12px] text-gray-600 mt-4">
                Belum punya akun?
                <button onclick="switchTab('register')" class="text-violet-400 hover:underline">Daftar</button>
            </p>
        </div>

        {{-- Register --}}
        <div id="fReg" class="px-6 pb-6 hidden">
            <form method="POST" action="{{ route('register') }}" class="space-y-3">
                @csrf
                <div>
                    <label
                        class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama kamu"
                        class="w-full px-4 py-2.5 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none"
                        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                    @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label
                        class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="email@kamu.com"
                        class="w-full px-4 py-2.5 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none"
                        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                    @error('email') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label
                        class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="rPw" placeholder="Min. 6 karakter"
                            oninput="checkStrength(this.value)"
                            class="w-full px-4 py-2.5 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none pr-10"
                            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                        <button type="button" onclick="togglePw('rPw')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex gap-1 mt-1.5">
                        <div id="pw1" class="h-1 flex-1 rounded-full bg-white/10 transition-colors"></div>
                        <div id="pw2" class="h-1 flex-1 rounded-full bg-white/10 transition-colors"></div>
                        <div id="pw3" class="h-1 flex-1 rounded-full bg-white/10 transition-colors"></div>
                    </div>
                    <p id="pwLabel" class="text-[11px] mt-1"></p>
                    @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label
                        class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Konfirmasi
                        Password</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password"
                        class="w-full px-4 py-2.5 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none"
                        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                </div>
                <button type="submit"
                    class="w-full py-2.5 rounded-xl text-sm font-medium text-white transition-all hover:opacity-90"
                    style="background:rgba(124,58,237,.5);border:1px solid rgba(124,58,237,.6)">
                    Daftar Sekarang
                </button>
            </form>
            <p class="text-center text-[12px] text-gray-600 mt-4">
                Sudah punya akun?
                <button onclick="switchTab('login')" class="text-violet-400 hover:underline">Masuk</button>
            </p>
        </div>
    </div>
</div>