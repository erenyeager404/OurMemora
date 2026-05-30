<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OurMemora — @yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    <style>
        :root {
            --sb: 64px;
            --sb-o: 232px
        }

        #sidebar {
            width: var(--sb);
            transition: width .25s cubic-bezier(.4, 0, .2, 1);
            overflow: hidden
        }

        #sidebar:hover {
            width: var(--sb-o)
        }

        .nav-label {
            opacity: 0;
            transform: translateX(-6px);
            transition: opacity .18s, transform .18s;
            white-space: nowrap
        }

        #sidebar:hover .nav-label {
            opacity: 1;
            transform: translateX(0)
        }

        .logo-short {
            transition: opacity .18s
        }

        .logo-full {
            position: absolute;
            opacity: 0;
            transition: opacity .18s;
            white-space: nowrap
        }

        #sidebar:hover .logo-short {
            opacity: 0
        }

        #sidebar:hover .logo-full {
            opacity: 1
        }

        #sidebar:not(:hover) .nav-item:hover::after {
            content: attr(data-tip);
            position: absolute;
            left: calc(100% + 10px);
            top: 50%;
            transform: translateY(-50%);
            background: rgba(15, 18, 30, .96);
            color: #f1f5f9;
            font-size: 11px;
            padding: 5px 10px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, .1);
            white-space: nowrap;
            z-index: 999;
            pointer-events: none;
            backdrop-filter: blur(12px)
        }

        .nav-item {
            position: relative
        }

        .main-content {
            margin-left: var(--sb);
            transition: margin-left .25s cubic-bezier(.4, 0, .2, 1)
        }

        @keyframes blob1 {

            0%,
            100% {
                transform: translate(0, 0) scale(1)
            }

            33% {
                transform: translate(24px, -18px) scale(1.04)
            }

            66% {
                transform: translate(-16px, 12px) scale(.96)
            }
        }

        @keyframes blob2 {

            0%,
            100% {
                transform: translate(0, 0) scale(1)
            }

            40% {
                transform: translate(-20px, 24px) scale(1.06)
            }

            70% {
                transform: translate(16px, -8px) scale(.94)
            }
        }

        @keyframes blob3 {

            0%,
            100% {
                transform: translate(0, 0)
            }

            50% {
                transform: translate(12px, 16px)
            }
        }

        .b1 {
            animation: blob1 16s ease-in-out infinite
        }

        .b2 {
            animation: blob2 20s ease-in-out infinite 3s
        }

        .b3 {
            animation: blob3 13s ease-in-out infinite 6s
        }

        /* Skeleton */
        @keyframes shimmer {
            0% {
                background-position: -800px 0
            }

            100% {
                background-position: 800px 0
            }
        }

        .skeleton {
            background: linear-gradient(90deg, rgba(255, 255, 255, .04) 25%, rgba(255, 255, 255, .09) 50%, rgba(255, 255, 255, .04) 75%);
            background-size: 800px 100%;
            animation: shimmer 1.4s infinite linear
        }

        img.lazy {
            opacity: 0;
            transition: opacity .35s ease
        }

        img.lazy.loaded {
            opacity: 1
        }
    </style>
</head>

<body class="bg-[#090b14] text-white min-h-screen flex">

    {{-- Premium animated background --}}
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0" style="background:linear-gradient(135deg,#09090f 0%,#0d0f1a 50%,#090b14 100%)">
        </div>
        <div class="b1 absolute -top-40 -left-40 w-[560px] h-[560px] rounded-full opacity-[.16]"
            style="background:radial-gradient(circle,#7c3aed,transparent 70%)"></div>
        <div class="b2 absolute -bottom-20 -right-20 w-[480px] h-[480px] rounded-full opacity-[.11]"
            style="background:radial-gradient(circle,#2563eb,transparent 70%)"></div>
        <div class="b3 absolute top-1/2 left-1/3 w-[360px] h-[360px] rounded-full opacity-[.07]"
            style="background:radial-gradient(circle,#db2777,transparent 70%)"></div>
        <div class="absolute inset-0 opacity-[.025]"
            style="background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22><filter id=%22n%22><feTurbulence type=%22fractalNoise%22 baseFrequency=%220.85%22 numOctaves=%224%22/></filter><rect width=%22200%22 height=%22200%22 filter=%22url(%23n)%22/></svg>')">
        </div>
    </div>

    {{-- Sidebar --}}
    <aside id="sidebar" class="fixed left-0 top-0 h-full z-30 flex flex-col"
        style="background:rgba(8,10,20,.9);backdrop-filter:blur(24px);border-right:1px solid rgba(255,255,255,.06)">

        {{-- Logo --}}
        <div class="relative flex items-center justify-center h-16 flex-shrink-0"
            style="border-bottom:1px solid rgba(255,255,255,.05)">
            <a href="{{ route('dashboard') }}" class="relative w-full flex items-center justify-center">
                <span class="logo-short font-black text-xl text-violet-400">M</span>
                <span class="logo-full font-bold text-[15px]">Our<span class="text-violet-400">Memora</span></span>
            </a>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 py-3 space-y-0.5 overflow-hidden">
            @php
                $nav = [
                    [
                        'route' => 'dashboard',
                        'tip' => 'Dashboard',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'
                    ],
                    [
                        'route' => 'search',
                        'tip' => 'Pencarian',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>'
                    ],
                    [
                        'route' => 'events.index',
                        'tip' => 'Events',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>'
                    ],
                    [
                        'route' => 'saved',
                        'tip' => 'Saved',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>'
                    ],
                    [
                        'route' => 'profile',
                        'tip' => 'Profile',
                        'svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>'
                    ],
                ];
            @endphp

            @foreach($nav as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}" data-tip="{{ $item['tip'] }}"
                        class="nav-item flex items-center h-11 mx-2 px-3 rounded-xl transition-all duration-150 cursor-pointer"
                        style="{{ $active
                ? 'background:rgba(124,58,237,.22);border:1px solid rgba(124,58,237,.35);color:#c4b5fd'
                : 'background:transparent;border:1px solid transparent;color:#6b7280' }}
                           outline:none">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $item['svg'] !!}
                        </svg>
                        <span class="nav-label text-[13px] font-medium ml-3">{{ $item['tip'] }}</span>
                    </a>
            @endforeach

            <div class="px-2 pt-2">
                <a href="{{ route('upload') }}" data-tip="Upload Foto"
                    class="nav-item flex items-center h-11 px-3 rounded-xl cursor-pointer transition-all"
                    style="background:rgba(124,58,237,.28);border:1px solid rgba(124,58,237,.4);color:#e9d5ff">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="nav-label text-[13px] font-semibold ml-3">Upload Foto</span>
                </a>
            </div>
        </nav>

        {{-- User + Logout --}}
        <div class="p-2 space-y-0.5" style="border-top:1px solid rgba(255,255,255,.05)">
            <div class="nav-item flex items-center h-11 px-3 rounded-xl" data-tip="{{ auth()->user()->name }}"
                style="color:#6b7280">
                <img src="{{ auth()->user()->avatar_url }}" alt=""
                    class="w-5 h-5 rounded-full flex-shrink-0 object-cover ring-1 ring-violet-500/30">
                <span class="nav-label text-[12px] ml-3 truncate max-w-[140px] text-gray-400">
                    {{ auth()->user()->name }}
                </span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" data-tip="Logout"
                    class="nav-item flex items-center h-11 px-3 rounded-xl w-full transition-all hover:text-red-400"
                    style="background:transparent;border:1px solid transparent;color:#6b7280">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="nav-label text-[13px] ml-3">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content flex-1 min-h-screen p-8 relative z-10">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl text-sm text-green-300 flex items-center gap-3"
                style="background:rgba(6,78,59,.25);border:1px solid rgba(16,185,129,.2)">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl text-sm text-red-300 flex items-center gap-3"
                style="background:rgba(127,29,29,.25);border:1px solid rgba(239,68,68,.2)">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>