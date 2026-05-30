<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — OurMemora</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --sb: 64px;
            --sb-o: 232px;
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
            transform: translateX(-6px);
            transition: opacity .18s, transform .18s;
            white-space: nowrap;
        }

        #sidebar:hover .nav-label {
            opacity: 1;
            transform: translateX(0);
        }

        .logo-short {
            transition: opacity .18s;
        }

        .logo-full {
            position: absolute;
            opacity: 0;
            transition: opacity .18s;
            white-space: nowrap;
        }

        #sidebar:hover .logo-short {
            opacity: 0;
        }

        #sidebar:hover .logo-full {
            opacity: 1;
        }

        /* Tooltip saat collapsed */
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
            backdrop-filter: blur(12px);
        }

        .nav-item {
            position: relative;
        }

        .main-content {
            margin-left: var(--sb);
            transition: margin-left .25s cubic-bezier(.4, 0, .2, 1);
        }
    </style>
</head>

<body class="bg-[#090b14] text-white min-h-screen flex">

    <div class="fixed inset-0 z-0 bg-[#090b14]"></div>

    {{-- Sidebar --}}
    <aside id="sidebar" class="fixed left-0 top-0 h-full z-30 flex flex-col"
        style="background:rgba(8,10,20,.95);backdrop-filter:blur(24px);border-right:1px solid rgba(220,38,38,.12)">

        {{-- Logo --}}
        <div class="relative flex items-center justify-center h-16 flex-shrink-0"
            style="border-bottom:1px solid rgba(220,38,38,.1)">
            <a href="{{ route('admin.dashboard') }}" class="relative w-full flex items-center justify-center">
                <span class="logo-short font-black text-xl text-red-400">M</span>
                <span class="logo-full font-bold text-[15px]">
                    Our<span class="text-red-400">Memora</span>
                </span>
            </a>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 py-3 space-y-0.5 overflow-hidden">
            @php
                $navItems = [
                    [
                        'route' => 'admin.dashboard',
                        'tip' => 'Dashboard',
                        'path' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                    ],
                    [
                        'route' => 'admin.engagement',
                        'tip' => 'Engagement',
                        'path' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                    ],
                    [
                        'route' => 'admin.events.index',
                        'tip' => 'Events',
                        'path' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
                    ],
                    [
                        'route' => 'profile',
                        'tip' => 'Profile',
                        'path' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                    ],
                ];
            @endphp

            @foreach($navItems as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}" data-tip="{{ $item['tip'] }}"
                        class="nav-item flex items-center h-11 mx-2 px-3 rounded-xl transition-all duration-150 cursor-pointer"
                        style="{{ $active
                ? 'background:rgba(220,38,38,.18);border:1px solid rgba(220,38,38,.35);color:#fca5a5'
                : 'background:transparent;border:1px solid transparent;color:#6b7280' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['path'] }}" />
                        </svg>
                        <span class="nav-label text-[13px] font-medium ml-3">{{ $item['tip'] }}</span>
                    </a>
            @endforeach
        </nav>

        {{-- Bottom: badge admin + logout --}}
        <div class="p-2 space-y-0.5" style="border-top:1px solid rgba(220,38,38,.1)">

            {{-- Admin badge --}}
            <div class="nav-item flex items-center h-11 px-3 rounded-xl" data-tip="Administrator" style="color:#9ca3af">
                <svg class="w-5 h-5 flex-shrink-0 text-red-400" fill="none" stroke="currentColor" stroke-width="1.75"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span class="nav-label text-[12px] ml-3 text-red-400 font-medium">Administrator</span>
            </div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" data-tip="Logout"
                    class="nav-item flex items-center h-11 px-3 rounded-xl w-full text-left transition-all hover:text-red-400"
                    style="background:transparent;border:1px solid transparent;color:#6b7280">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.75"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="nav-label text-[13px] ml-3">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
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