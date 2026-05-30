<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OurMemora')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="bg-[#090b14] text-white min-h-screen">

    <nav class="fixed top-0 w-full z-40"
        style="background:rgba(9,11,20,.88);backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,.05)">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="text-[18px] font-bold tracking-tight">
                Our<span class="text-violet-400">Memora</span>
            </a>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 text-sm font-medium text-white rounded-xl transition-all"
                        style="background:rgba(124,58,237,.4);border:1px solid rgba(124,58,237,.5)">
                        Dashboard
                    </a>
                @else
                    <button onclick="openModal('login')"
                        class="px-4 py-2 text-sm text-gray-300 hover:text-white rounded-xl transition-all"
                        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                        Masuk
                    </button>
                    <button onclick="openModal('register')"
                        class="px-4 py-2 text-sm font-medium text-white rounded-xl transition-all"
                        style="background:rgba(124,58,237,.4);border:1px solid rgba(124,58,237,.5)">
                        Daftar Gratis
                    </button>
                @endauth
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="fixed top-20 left-1/2 -translate-x-1/2 z-50 px-5 py-3 rounded-xl text-sm text-green-200 shadow-xl whitespace-nowrap"
            style="background:rgba(6,78,59,.9);border:1px solid rgba(16,185,129,.3);backdrop-filter:blur(12px)">
            {{ session('success') }}
        </div>
    @endif

    <main>@yield('content')</main>
    @yield('modals')

    @guest
        @include('layouts.partials.auth-modal')
    @endguest

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const CTX = {
            like: '♡  Masuk dulu untuk memberi like',
            save: '  Masuk dulu untuk menyimpan foto',
            comment: '  Masuk dulu untuk menulis komentar',
        };

        function openModal(tab = 'login', action = null) {
            const m = document.getElementById('authModal');
            const ctx = document.getElementById('modalCtx');
            if (!m) return;
            if (action && CTX[action]) { ctx.textContent = CTX[action]; ctx.classList.remove('hidden'); }
            else ctx.classList.add('hidden');
            switchTab(tab);
            m.classList.remove('hidden'); m.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        function closeModal() {
            const m = document.getElementById('authModal');
            if (!m) return;
            m.classList.add('hidden'); m.classList.remove('flex');
            document.body.style.overflow = '';
        }
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
        function switchTab(tab) {
            const isL = tab === 'login';
            document.getElementById('fLogin')?.classList.toggle('hidden', !isL);
            document.getElementById('fReg')?.classList.toggle('hidden', isL);
            const on = 'background:rgba(124,58,237,.4);border-color:rgba(124,58,237,.6);color:#e9d5ff';
            const off = 'background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.1);color:#9ca3af';
            const tl = document.getElementById('tLogin');
            const tr = document.getElementById('tReg');
            if (tl) tl.style.cssText = isL ? on : off;
            if (tr) tr.style.cssText = !isL ? on : off;
        }
        function togglePw(id) {
            const el = document.getElementById(id);
            if (el) el.type = el.type === 'password' ? 'text' : 'password';
        }
        function checkStrength(val) {
            let s = 0;
            if (val.length >= 6) s++;
            if (val.length >= 10) s++;
            if (/[A-Z]/.test(val) && /[0-9]/.test(val)) s++;
            const c = ['', 'bg-red-500', 'bg-yellow-500', 'bg-green-500'];
            const l = ['', 'Lemah', 'Sedang', 'Kuat'];
            [1, 2, 3].forEach(i => {
                const el = document.getElementById('pw' + i);
                if (el) el.className = `h-1 flex-1 rounded-full transition-colors ${i <= s ? c[s] : 'bg-white/10'}`;
            });
            const lbl = document.getElementById('pwLabel');
            if (lbl) { lbl.textContent = val ? l[s] : ''; lbl.style.color = s === 1 ? '#ef4444' : s === 2 ? '#eab308' : '#22c55e'; }
        }
        @if($errors->any())
                    document.addEventListener('DOMContentLoaded', () => {
                @if($errors->has('name')) openModal('register');
                @elseopenModal('login');
                        @endif
            });
        @endif
    </script>
    @stack('scripts')
</body>

</html>