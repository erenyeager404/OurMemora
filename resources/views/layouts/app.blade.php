<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OurMemora — @yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --sb: 68px;
            --sb-o: 240px;
        }

        #sidebar {
            width: var(--sb);
            transition: width .25s cubic-bezier(.4, 0, .2, 1);
            overflow: hidden;
        }

        #sidebar:hover {
            width: var(--sb-o);
        }

        .nav-label {
            opacity: 0;
            transform: translateX(-8px);
            transition: opacity .2s, transform .2s;
            white-space: nowrap;
        }

        #sidebar:hover .nav-label {
            opacity: 1;
            transform: translateX(0);
        }

        .logo-short {
            transition: opacity .2s;
        }

        .logo-full {
            position: absolute;
            opacity: 0;
            transition: opacity .2s;
            white-space: nowrap;
        }

        #sidebar:hover .logo-short {
            opacity: 0;
        }

        #sidebar:hover .logo-full {
            opacity: 1;
        }

        #sidebar:not(:hover) .nav-item:hover::after {
            content: attr(data-tip);
            position: absolute;
            left: calc(100% + 12px);
            top: 50%;
            transform: translateY(-50%);
            background: rgba(17, 24, 39, .95);
            color: #f9fafb;
            font-size: 12px;
            padding: 5px 12px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, .1);
            white-space: nowrap;
            z-index: 999;
            pointer-events: none;
            backdrop-filter: blur(12px);
        }

        .nav-item {
            position: relative;
        }

        .main-content {
            margin-left: var(--sb);
            transition: margin-left .25s cubic-bezier(.4, 0, .2, 1);
        }

        /* Premium animated background */
        @keyframes float1 {

            0%,
            100% {
                transform: translate(0, 0) scale(1)
            }

            33% {
                transform: translate(30px, -20px) scale(1.05)
            }

            66% {
                transform: translate(-20px, 15px) scale(.95)
            }
        }

        @keyframes float2 {

            0%,
            100% {
                transform: translate(0, 0) scale(1)
            }

            33% {
                transform: translate(-25px, 30px) scale(1.08)
            }

            66% {
                transform: translate(20px, -10px) scale(.92)
            }
        }

        @keyframes float3 {

            0%,
            100% {
                transform: translate(0, 0) scale(1)
            }

            50% {
                transform: translate(15px, 20px) scale(1.03)
            }
        }

        .blob-1 {
            animation: float1 15s ease-in-out infinite;
        }

        .blob-2 {
            animation: float2 18s ease-in-out infinite 2s;
        }

        .blob-3 {
            animation: float3 12s ease-in-out infinite 4s;
        }
    </style>
</head>

<body class="bg-gray-950 text-white min-h-screen flex">

    {{-- Premium Gradient Background (bukan foto dari DB) --}}
    <div class="fixed inset-0 z-0 overflow-hidden">
        {{-- Base gradient --}}
        <div class="absolute inset-0 bg-gradient-to-br from-[#0a0e1a] via-[#0f172a] to-[#0d1117]"></div>

        {{-- Floating glow blobs --}}
        <div class="blob-1 absolute -top-32 -left-32 w-[600px] h-[600px] rounded-full opacity-[0.18]"
            style="background: radial-gradient(circle, #7C3AED 0%, transparent 70%)"></div>
        <div class="blob-2 absolute bottom-0 right-0 w-[500px] h-[500px] rounded-full opacity-[0.12]"
            style="background: radial-gradient(circle, #2563EB 0%, transparent 70%)"></div>
        <div class="blob-3 absolute top-1/2 left-1/2 w-[400px] h-[400px] -translate-x-1/2 -translate-y-1/2 rounded-full opacity-[0.08]"
            style="background: radial-gradient(circle, #DB2777 0%, transparent 70%)"></div>

        {{-- Glassmorphism overlay --}}
        <div class="absolute inset-0 backdrop-blur-[1px]"></div>

        {{-- Subtle noise texture --}}
        <div class="absolute inset-0 opacity-[0.03]"
            style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22><filter id=%22n%22><feTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 stitchTiles=%22stitch%22/></filter><rect width=%22200%22 height=%22200%22 filter=%22url(%23n)%22 opacity=%221%22/></svg>')">
        </div>
    </div>

    {{-- Sidebar --}}
    <aside id="sidebar" class="fixed left-0 top-0 h-full z-30 flex flex-col"
        style="background: rgba(9,11,20,.85); backdrop-filter: blur(20px); border-right: 1px solid rgba(255,255,255,.06);">

        {{-- Logo --}}
        <div class="relative flex items-center justify-center h-16 flex-shrink-0"
            style="border-bottom: 1px solid rgba(255,255,255,.06)">
            <a href="{{ route('dashboard') }}" class="relative w-full flex items-center justify-center">
                <span class="logo-short text-violet-400 font-black text-xl">M</span>
                <span class="logo-full text-white font-bold text-base">Our<span
                        class="text-violet-400">Memora</span></span>
            </a>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 py-3 space-y-0.5 overflow-hidden">
            @php
                $navItems = [
                    ['route' => 'dashboard', 'icon' => '⊞', 'label' => 'Dashboard'],
                    ['route' => 'search', 'icon' => '⌕', 'label' => 'Pencarian'],
                    ['route' => 'events.index', 'icon' => '◎', 'label' => 'Events'],
                    ['route' => 'saved', 'icon' => '◈', 'label' => 'Saved'],
                    ['route' => 'profile', 'icon' => '◉', 'label' => 'Profile'],
                ];
            @endphp

            @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" data-tip="{{ $item['label'] }}" class="nav-item flex items-center h-11 mx-2 px-3 rounded-xl transition-all duration-200 cursor-pointer
                              {{ request()->routeIs($item['route'])
                ? 'text-white'
                : 'text-gray-500 hover:text-white' }}" style="{{ request()->routeIs($item['route'])
                ? 'background: rgba(124,58,237,.25); border: 1px solid rgba(124,58,237,.3);'
                : 'background: transparent; border: 1px solid transparent;' }}">
                        <span
                            class="text-base w-5 h-5 flex items-center justify-center flex-shrink-0">{{ $item['icon'] }}</span>
                        <span class="nav-label text-sm font-medium ml-3">{{ $item['label'] }}</span>
                    </a>
            @endforeach

            <div class="px-2 pt-3">
                <a href="{{ route('upload') }}" data-tip="Upload"
                    class="nav-item flex items-center h-11 px-3 rounded-full cursor-pointer transition-all"
                    style="background: linear-gradient(135deg, rgba(109,40,217,.6), rgba(124,58,237,.4)); border: 1px solid rgba(124,58,237,.4);">
                    <span
                        class="text-lg w-5 h-5 flex items-center justify-center flex-shrink-0 font-bold text-white">＋</span>
                    <span class="nav-label text-sm font-medium ml-3 text-white">Upload</span>
                </a>
            </div>
        </nav>

        {{-- User & Logout --}}
        <div class="p-2 space-y-0.5" style="border-top: 1px solid rgba(255,255,255,.06)">
            <div class="nav-item flex items-center h-11 px-3 rounded-xl" data-tip="{{ auth()->user()->name }}">
                <img src="{{ auth()->user()->avatar_url }}" alt=""
                    class="w-5 h-5 rounded-full flex-shrink-0 object-cover ring-1 ring-violet-500/30">
                <span
                    class="nav-label text-xs ml-3 truncate max-w-[130px] text-gray-300">{{ auth()->user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" data-tip="Logout"
                    class="nav-item flex items-center h-11 px-3 rounded-xl w-full text-left text-gray-500 hover:text-red-400 transition-all"
                    style="border: 1px solid transparent;">
                    <span class="w-5 h-5 flex items-center justify-center flex-shrink-0">⇥</span>
                    <span class="nav-label text-sm ml-3">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <main class="main-content flex-1 min-h-screen p-8 relative z-10">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl text-sm text-green-300"
                style="background: rgba(6,78,59,.3); border: 1px solid rgba(16,185,129,.2);">
                ✓ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl text-sm text-red-300"
                style="background: rgba(127,29,29,.3); border: 1px solid rgba(239,68,68,.2);">
                ✕ {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>