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
        /* Sidebar collapse/expand — tidak bisa pure Tailwind */
        :root {
            --sb: 68px;
            /* collapsed */
            --sb-o: 240px;
            /* expanded */
        }

        #sidebar {
            width: var(--sb);
            transition: width .25s ease;
            overflow: hidden;
        }

        #sidebar:hover {
            width: var(--sb-o);
        }

        .nav-label {
            opacity: 0;
            transform: translateX(-8px);
            transition: opacity .2s ease, transform .2s ease;
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

        /* Tooltip saat collapsed */
        #sidebar:not(:hover) .nav-item:hover::after {
            content: attr(data-tip);
            position: absolute;
            left: calc(100% + 10px);
            top: 50%;
            transform: translateY(-50%);
            background: #1f2937;
            color: #f9fafb;
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 8px;
            border: 1px solid #374151;
            white-space: nowrap;
            z-index: 999;
            pointer-events: none;
        }

        .nav-item {
            position: relative;
        }

        .main-content {
            margin-left: var(--sb);
            transition: margin-left .25s ease;
        }
    </style>
</head>

<body class="bg-gray-950 text-white min-h-screen flex">

    {{-- Background --}}
    <div class="fixed inset-0 z-0">
        @if(isset($bgPhoto) && $bgPhoto->files->isNotEmpty())
            <img id="bgImg" src="{{ $bgPhoto->files->first()->url }}" alt="" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gray-950"></div>
        @endif
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
    </div>

    {{-- Sidebar --}}
    <aside id="sidebar"
        class="fixed left-0 top-0 h-full z-30 bg-gray-900/95 backdrop-blur-xl border-r border-white/8 flex flex-col">

        {{-- Logo --}}
        <div class="relative flex items-center justify-center h-16 border-b border-white/8 px-3 flex-shrink-0">
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
                    ['route' => 'profile', 'icon' => '◉', 'label' => 'Profile'],
                    ['route' => 'saved', 'icon' => '◈', 'label' => 'Saved'],
                ];
            @endphp

            @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" data-tip="{{ $item['label'] }}" class="nav-item flex items-center h-11 mx-2 px-3 rounded-xl transition-all duration-200 cursor-pointer
                              {{ request()->routeIs($item['route'])
                ? 'bg-violet-600/80 text-white'
                : 'text-gray-400 hover:text-white hover:bg-white/8' }}">
                        <span
                            class="text-base w-5 h-5 flex items-center justify-center flex-shrink-0">{{ $item['icon'] }}</span>
                        <span class="nav-label text-sm font-medium ml-3">{{ $item['label'] }}</span>
                    </a>
            @endforeach

            <div class="px-2 pt-3">
                <a href="{{ route('upload') }}" data-tip="Upload"
                    class="nav-item flex items-center h-11 px-3 rounded-xl bg-violet-600/60 hover:bg-violet-600 text-white transition-all cursor-pointer">
                    <span class="text-lg w-5 h-5 flex items-center justify-center flex-shrink-0 font-bold">＋</span>
                    <span class="nav-label text-sm font-medium ml-3">Upload</span>
                </a>
            </div>
        </nav>

        {{-- User & Logout --}}
        <div class="border-t border-white/8 p-2 space-y-0.5">
            <div class="nav-item flex items-center h-11 mx-0 px-3 rounded-xl" data-tip="{{ auth()->user()->name }}">
                <img src="{{ auth()->user()->avatar_url }}" alt=""
                    class="w-5 h-5 rounded-full flex-shrink-0 object-cover">
                <span class="nav-label text-xs ml-3 truncate max-w-[130px]">{{ auth()->user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" data-tip="Logout"
                    class="nav-item flex items-center h-11 mx-0 px-3 rounded-xl w-full text-left text-gray-400 hover:text-red-400 hover:bg-red-900/20 transition-all">
                    <span class="w-5 h-5 flex items-center justify-center flex-shrink-0">⇥</span>
                    <span class="nav-label text-sm ml-3">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <main class="main-content flex-1 min-h-screen p-8 relative z-10">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-900/40 border border-green-700/50 text-green-300 rounded-xl text-sm">
                ✓ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-900/40 border border-red-700/50 text-red-300 rounded-xl text-sm">
                ✕ {{ session('error') }}
            </div>
        @endif
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>