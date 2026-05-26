<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OurMemora — @yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-950 text-white min-h-screen flex">

    {{-- Background --}}
    <div class="dashboard-bg" id="dashBg">
        @if(isset($bgPhoto) && $bgPhoto->files->isNotEmpty())
            <img src="{{ $bgPhoto->files->first()->url }}" alt="" id="bgImg">
        @else
            <div class="w-full h-full bg-gray-950"></div>
        @endif
        <div class="dashboard-bg-overlay"></div>
    </div>

    {{-- ── SIDEBAR (icon only, expand on hover) ── --}}
    <aside class="sidebar" id="sidebar">

        {{-- Logo --}}
        <div class="sidebar-logo-area">
            <a href="{{ route('dashboard') }}" class="relative w-full flex items-center justify-center">
                <span class="sidebar-logo-short">M</span>
                <span class="sidebar-logo-full">
                    Our<span class="text-violet-400">Memora</span>
                </span>
            </a>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 py-4 space-y-1 overflow-hidden">
            @php
                $navItems = [
                    ['route' => 'dashboard', 'icon' => '⊞', 'label' => 'Dashboard'],
                    ['route' => 'search', 'icon' => '⌕', 'label' => 'Pencarian'],
                    ['route' => 'profile', 'icon' => '◉', 'label' => 'Profile'],
                    ['route' => 'saved', 'icon' => '◈', 'label' => 'Saved'],
                ];
            @endphp

            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}" data-tooltip="{{ $item['label'] }}"
                    class="nav-item {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                    <span class="nav-item-icon">{{ $item['icon'] }}</span>
                    <span class="nav-label">{{ $item['label'] }}</span>
                </a>
            @endforeach

            <a href="{{ route('upload') }}" data-tooltip="Upload" class="nav-upload mt-4">
                <span class="nav-item-icon font-bold">＋</span>
                <span class="nav-label">Upload</span>
            </a>
        </nav>

        {{-- User + Logout --}}
        <div class="border-t border-white/8 p-3">
            <div class="nav-item" data-tooltip="{{ auth()->user()->name }}">
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                    class="w-6 h-6 rounded-full flex-shrink-0 object-cover">
                <span class="nav-label text-xs truncate">{{ auth()->user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" data-tooltip="Logout" class="nav-item w-full text-left hover:text-red-400 mt-1">
                    <span class="nav-item-icon">⇥</span>
                    <span class="nav-label">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <main class="main-content relative z-10">
        @if(session('success'))
            <div class="flash-success mb-6">✓ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash-error mb-6">✕ {{ session('error') }}</div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>