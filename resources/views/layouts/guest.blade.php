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

<body class="bg-gray-950 text-white min-h-screen">

    {{-- Navbar --}}
    <nav class="fixed top-0 w-full z-40 bg-gray-950/85 backdrop-blur-xl border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="text-xl font-bold tracking-tight">
                Our<span class="text-violet-400">Memora</span>
            </a>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 rounded-xl transition-colors">
                        Dashboard
                    </a>
                @else
                    <button onclick="openModal('login')"
                        class="px-4 py-2 text-sm text-gray-300 hover:text-white border border-white/10 hover:border-white/20 rounded-xl transition-colors">
                        Masuk
                    </button>
                    <button onclick="openModal('register')"
                        class="px-4 py-2 text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 rounded-xl transition-colors">
                        Daftar Gratis
                    </button>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Flash --}}
    @if(session('success'))
        <div
            class="fixed top-20 left-1/2 -translate-x-1/2 z-50 px-5 py-3 bg-green-900/90 border border-green-700/50 text-green-200 rounded-xl text-sm shadow-xl whitespace-nowrap backdrop-blur-md">
            ✓ {{ session('success') }}
        </div>
    @endif

    <main>@yield('content')</main>

    @yield('modals')

    @guest
        @include('layouts.partials.auth-modal')
    @endguest

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

        const CTX_MSG = {
            like: '♡  Login dulu untuk memberi like',
            save: '◇  Login dulu untuk menyimpan foto',
            comment: '◯  Login dulu untuk menulis komentar',
        };

        function openModal(tab = 'login', action = null) {
            const m = document.getElementById('authModal');
            const ctx = document.getElementById('modalCtx');
            if (!m) return;
            if (action && CTX_MSG[action]) {
                ctx.textContent = CTX_MSG[action];
                ctx.classList.remove('hidden');
            } else {
                ctx.classList.add('hidden');
            }
            switchTab(tab);
            m.classList.remove('hidden');
            m.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const m = document.getElementById('authModal');
            if (!m) return;
            m.classList.add('hidden');
            m.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        function switchTab(tab) {
            const isL = tab === 'login';
            document.getElementById('fLogin')?.classList.toggle('hidden', !isL);
            document.getElementById('fReg')?.classList.toggle('hidden', isL);

            const btnClass = (active) =>
                `flex-1 py-2 rounded-lg text-sm font-medium transition-all ${active ? 'bg-violet-600 text-white' : 'text-gray-400 hover:text-white'}`;

            const tl = document.getElementById('tLogin');
            const tr = document.getElementById('tReg');
            if (tl) tl.className = btnClass(isL);
            if (tr) tr.className = btnClass(!isL);
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
                if (el) el.className = `h-1 flex-1 rounded-full transition-colors ${i <= s ? c[s] : 'bg-gray-700'}`;
            });
            const lbl = document.getElementById('pwLabel');
            if (lbl) {
                lbl.textContent = val ? l[s] : '';
                lbl.style.color = s === 1 ? '#ef4444' : s === 2 ? '#eab308' : '#22c55e';
            }
        }

        @if($errors->any())
                document.addEventListener('DOMContentLoaded', () => {
                    @if($errors->has('name'))
                        openModal('register');
                    @else
                        openModal('login');
                    @endif
            });
        @endif
    </script>

    @stack('scripts')
</body>

</html>