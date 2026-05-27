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
            --sb: 68px;
            --sb-o: 240px;
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
            transition: margin-left .25s;
        }
    </style>
</head>

<body class="bg-gray-950 text-white min-h-screen flex">

    <div class="fixed inset-0 z-0 bg-gray-950"></div>

    <aside id="sidebar" class="fixed left-0 top-0 h-full z-30 bg-gray-900 border-r border-red-900/20 flex flex-col">
        <div class="relative flex items-center justify-center h-16 border-b border-red-900/20 px-3 flex-shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="relative w-full flex items-center justify-center">
                <span class="logo-short text-red-400 font-black text-xl">M</span>
                <span class="logo-full text-white font-bold text-base">Our<span
                        class="text-red-400">Memora</span></span>
            </a>
        </div>

        <nav class="flex-1 py-3 space-y-0.5 overflow-hidden">
            @php
                $nav = [
                    ['route' => 'admin.dashboard', 'icon' => '⊞', 'label' => 'Dashboard'],
                    ['route' => 'admin.engagement', 'icon' => '⎇', 'label' => 'Engagement'],
                    ['route' => 'profile', 'icon' => '◉', 'label' => 'Profile'],
                ];
            @endphp
            @foreach($nav as $item)
                    <a href="{{ route($item['route']) }}" data-tip="{{ $item['label'] }}" class="nav-item flex items-center h-11 mx-2 px-3 rounded-xl transition-all cursor-pointer
                              {{ request()->routeIs($item['route'])
                ? 'bg-red-600/80 text-white'
                : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <span
                            class="text-base w-5 h-5 flex items-center justify-center flex-shrink-0">{{ $item['icon'] }}</span>
                        <span class="nav-label text-sm font-medium ml-3">{{ $item['label'] }}</span>
                    </a>
            @endforeach
        </nav>

        <div class="border-t border-red-900/20 p-2 space-y-0.5">
            <div class="nav-item flex items-center h-11 px-3" data-tip="Administrator">
                <span class="w-5 h-5 flex items-center justify-center text-red-400 flex-shrink-0">⬡</span>
                <span class="nav-label text-xs ml-3 text-red-400">Administrator</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" data-tip="Logout"
                    class="nav-item flex items-center h-11 px-3 rounded-xl w-full text-left text-gray-400 hover:text-red-400 hover:bg-red-900/20 transition-all">
                    <span class="w-5 h-5 flex items-center justify-center flex-shrink-0">⇥</span>
                    <span class="nav-label text-sm ml-3">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="main-content flex-1 min-h-screen p-8 relative z-10">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-900/40 border border-green-700/50 text-green-300 rounded-xl text-sm">✓
                {{ session('success') }}</div>
        @endif
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>