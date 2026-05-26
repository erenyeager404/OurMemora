<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OurGallery')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="bg-gray-950 text-white min-h-screen">

    {{-- Navbar --}}
    <nav class="fixed top-0 w-full z-40 bg-gray-950/80 backdrop-blur-md border-b border-white/5">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ route('landing') }}" class="text-xl font-bold tracking-tight">
                My<span class="text-violet-400">Gallery</span>
            </a>
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 text-sm bg-violet-600 hover:bg-violet-700 rounded-lg transition-colors font-medium">
                        Dashboard
                    </a>
                @else
                    <button onclick="openAuthModal('login')"
                        class="px-4 py-2 text-sm text-gray-300 hover:text-white border border-white/10 rounded-lg transition-colors">
                        Masuk
                    </button>
                    <button onclick="openAuthModal('register')"
                        class="px-4 py-2 text-sm bg-violet-600 hover:bg-violet-700 rounded-lg transition-colors font-medium">
                        Daftar Gratis
                    </button>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Flash --}}
    @if(session('success'))
        <div class="fixed top-20 left-1/2 -translate-x-1/2 z-50 px-5 py-3
                        bg-green-900/80 backdrop-blur-sm border border-green-700/50
                        text-green-300 rounded-xl text-sm shadow-xl whitespace-nowrap">
            &#10003; {{ session('success') }}
        </div>
    @endif

    <main>@yield('content')</main>

    @yield('modals')

    {{-- Auth modal tersedia di semua halaman guest --}}
    @guest
        @include('layouts.partials.auth-modal')
    @endguest

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

        const contextMessages = {
            like: '&#10084; Login dulu untuk memberi like',
            save: '&#128278; Login dulu untuk menyimpan foto',
            comment: '&#128172; Login dulu untuk menulis komentar',
        };

        function openAuthModal(tab = 'login', action = null) {
            const modal = document.getElementById('authModal');
            const ctx = document.getElementById('modalContextMsg');
            if (!modal) return;
            if (action && contextMessages[action]) {
                ctx.innerHTML = contextMessages[action];
                ctx.classList.remove('hidden');
            } else {
                ctx.classList.add('hidden');
            }
            switchTab(tab);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeAuthModal() {
            const modal = document.getElementById('authModal');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        function handleModalBackdrop(e) {
            if (e.target === document.getElementById('authModal')) closeAuthModal();
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeAuthModal();
        });

        function switchTab(tab) {
            const isLogin = tab === 'login';
            document.getElementById('formLogin')?.classList.toggle('hidden', !isLogin);
            document.getElementById('formRegister')?.classList.toggle('hidden', isLogin);
            const tl = document.getElementById('tabLogin');
            const tr = document.getElementById('tabRegister');
            if (tl) tl.className = `flex-1 py-2 rounded-lg text-sm font-medium transition-all ${isLogin ? 'bg-violet-600 text-white' : 'text-gray-400 hover:text-white'}`;
            if (tr) tr.className = `flex-1 py-2 rounded-lg text-sm font-medium transition-all ${!isLogin ? 'bg-violet-600 text-white' : 'text-gray-400 hover:text-white'}`;
        }

        function togglePw(id, btn) {
            const input = document.getElementById(id);
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
            btn.style.opacity = input.type === 'text' ? '1' : '0.5';
        }

        function checkStrength(val) {
            let s = 0;
            if (val.length >= 6) s++;
            if (val.length >= 10) s++;
            if (/[A-Z]/.test(val) && /[0-9]/.test(val)) s++;
            const c = ['', 'bg-red-500', 'bg-yellow-500', 'bg-green-500'];
            const l = ['', 'Lemah', 'Sedang', 'Kuat'];
            for (let i = 1; i <= 3; i++) {
                const el = document.getElementById('str' + i);
                if (el) el.className = `h-1 flex-1 rounded-full transition-colors ${i <= s ? c[s] : 'bg-gray-800'}`;
            }
            const lbl = document.getElementById('strLabel');
            if (lbl) {
                lbl.textContent = val.length > 0 ? l[s] : '';
                lbl.style.color = s === 1 ? '#ef4444' : s === 2 ? '#eab308' : s === 3 ? '#22c55e' : '';
            }
        }

        @if($errors->any())
            document.addEventListener('DOMContentLoaded', () => {
                @if($errors->has('name'))
                    openAuthModal('register');
                @else
                    openAuthModal('login');
                @endif
                });
        @endif
    </script>

    @stack('scripts')
</body>

</html>