@extends(auth()->check() ? 'layouts.app' : 'layouts.guest')
@section('title', $event->title)

@section('content')
    <div>

        {{-- Back --}}
        <a href="{{ route('events.index') }}"
            class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-sm mb-6 transition-all px-4 py-2 rounded-full"
            style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);">
            ← Semua Events
        </a>

        {{-- Event Header --}}
        <div class="rounded-2xl overflow-hidden mb-8" style="border: 1px solid rgba(255,255,255,.1);">
            @if($event->poster_path)
                <div class="relative h-64 overflow-hidden">
                    <img src="{{ $event->poster_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0"
                        style="background: linear-gradient(to bottom, transparent 40%, rgba(9,11,20,.9) 100%)"></div>
                    <div class="absolute bottom-0 left-0 p-6">
                        <h1 class="text-3xl font-bold mb-2">{{ $event->title }}</h1>
                    </div>
                </div>
            @else
                <div class="h-40 flex items-center justify-center text-6xl"
                    style="background: linear-gradient(135deg, rgba(124,58,237,.3), rgba(59,130,246,.2));">
                    🏆
                </div>
            @endif

            <div class="p-6" style="background: rgba(255,255,255,.04);">
                @if(!$event->poster_path)
                    <h1 class="text-3xl font-bold mb-4">{{ $event->title }}</h1>
                @endif

                {{-- Stats row --}}
                <div class="flex items-center gap-6 mb-5 flex-wrap">
                    <div class="flex items-center gap-2">
                        @if($event->isActive())
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                            <span class="text-green-400 text-sm font-medium">Event Aktif</span>
                        @elseif($event->status === 'ended')
                            <span class="text-gray-500 text-sm">Event Selesai</span>
                        @else
                            <span class="text-yellow-500 text-sm">Belum Dimulai</span>
                        @endif
                    </div>
                    <span class="text-gray-500 text-sm">👥 {{ $totalParticipants }} peserta</span>
                    @if($event->isActive())
                        <span class="text-violet-400 text-sm font-medium">
                            ⏳ {{ $event->daysRemaining() }} hari tersisa
                        </span>
                    @endif
                    <span class="text-gray-600 text-xs">
                        {{ $event->start_date->format('d M Y') }} — {{ $event->end_date->format('d M Y') }}
                    </span>
                </div>

                @if($event->description)
                    <p class="text-gray-300 text-sm leading-relaxed mb-4">{{ $event->description }}</p>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                    @if($event->rules)
                        <div class="rounded-xl p-4"
                            style="background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08);">
                            <p class="text-xs text-gray-400 mb-2 font-medium">📋 Aturan</p>
                            <p class="text-sm text-gray-300 leading-relaxed">{{ $event->rules }}</p>
                        </div>
                    @endif
                    <div class="rounded-xl p-4"
                        style="background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08);">
                        <p class="text-xs text-gray-400 mb-2 font-medium">🏆 Pemenang</p>
                        <p class="text-sm text-gray-300">{{ $event->max_winners }} foto terbaik berdasarkan jumlah like</p>
                        @if($event->auto_tag)
                            <div class="mt-2">
                                <span class="px-2.5 py-1 text-xs rounded-full text-violet-300"
                                    style="background: rgba(124,58,237,.15); border: 1px solid rgba(124,58,237,.3);">
                                    #{{ $event->auto_tag }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                @auth
                    @if($event->isActive())
                        <a href="{{ route('upload') }}"
                            class="inline-flex items-center gap-2 px-8 py-3 rounded-full text-sm font-medium text-white transition-all"
                            style="background: linear-gradient(135deg, #7C3AED, #6D28D9); border: 1px solid rgba(124,58,237,.5);">
                            📷 Ikuti Event — Upload Sekarang
                        </a>
                    @endif
                @else
                    <button onclick="openModal('login')"
                        class="inline-flex items-center gap-2 px-8 py-3 rounded-full text-sm font-medium text-white transition-all"
                        style="background: rgba(124,58,237,.4); border: 1px solid rgba(124,58,237,.5);">
                        Login untuk ikut event
                    </button>
                @endauth
            </div>
        </div>

        {{-- Leaderboard --}}
        @if($leaderboard->isNotEmpty())
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <h2 class="text-xl font-bold">🏆 Top {{ min($event->max_winners, $leaderboard->count()) }} Foto</h2>
                    <span class="text-gray-500 text-sm">Ranking berdasarkan jumlah like</span>
                </div>

                {{-- Top 3 besar --}}
                @if($leaderboard->count() >= 3)
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        @foreach($leaderboard->take(3) as $i => $photo)
                            @if($photo->files->isNotEmpty())
                                <a href="{{ route('photos.show', $photo) }}"
                                    class="rounded-2xl overflow-hidden group transition-all block {{ $i === 0 ? 'ring-2 ring-yellow-400/60' : ($i === 1 ? 'ring-1 ring-gray-400/40' : 'ring-1 ring-orange-700/40') }}"
                                    style="background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1);">
                                    <div class="relative">
                                        <img src="{{ $photo->files->first()->url }}" alt="{{ $photo->caption }}"
                                            class="w-full object-cover group-hover:scale-105 transition-transform duration-500"
                                            style="height: {{ $i === 0 ? '200px' : '160px' }};">
                                        <div class="absolute top-2 left-2">
                                            <span class="px-2.5 py-1 text-xs font-bold rounded-full"
                                                style="{{ $i === 0 ? 'background: rgba(234,179,8,.9); color: #000; box-shadow: 0 0 12px rgba(234,179,8,.5);' : ($i === 1 ? 'background: rgba(156,163,175,.8); color: #000;' : 'background: rgba(180,83,9,.8); color: #fff;') }}">
                                                {{ $i === 0 ? '👑 #1' : ($i === 1 ? '🥈 #2' : '🥉 #3') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        <p class="text-sm font-medium truncate">{{ $photo->caption }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <img src="{{ $photo->user->avatar_url }}" class="w-4 h-4 rounded-full">
                                            <span class="text-gray-400 text-xs">{{ $photo->user->name }}</span>
                                            <span class="ml-auto text-red-400 text-xs font-medium">♥ {{ $photo->likes_count }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif

                {{-- Slider horizontal untuk semua --}}
                <div class="overflow-x-auto pb-4">
                    <div class="flex gap-3" style="min-width: max-content;">
                        @foreach($leaderboard as $i => $photo)
                            @if($photo->files->isNotEmpty() && $i >= 3)
                                <a href="{{ route('photos.show', $photo) }}"
                                    class="flex-shrink-0 w-48 rounded-xl overflow-hidden group block transition-all"
                                    style="background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08);">
                                    <div class="relative h-36 overflow-hidden">
                                        <img src="{{ $photo->files->first()->url }}" alt="{{ $photo->caption }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute top-2 left-2">
                                            <span class="px-2 py-0.5 text-xs rounded-full text-white font-medium"
                                                style="background: rgba(0,0,0,.6); border: 1px solid rgba(255,255,255,.2);">
                                                #{{ $i + 1 }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        <p class="text-xs font-medium truncate">{{ $photo->caption }}</p>
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="text-gray-500 text-xs truncate">{{ $photo->user->name }}</span>
                                            <span class="text-red-400 text-xs">♥ {{ $photo->likes_count }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-16 text-gray-600">
                <p class="text-5xl mb-3">📷</p>
                <p class="text-lg">Belum ada foto di event ini</p>
                @if($event->isActive())
                    <a href="{{ route('upload') }}"
                        class="inline-flex items-center gap-2 mt-4 px-6 py-2.5 rounded-full text-sm text-white"
                        style="background: rgba(124,58,237,.4); border: 1px solid rgba(124,58,237,.5);">
                        Jadilah yang pertama!
                    </a>
                @endif
            </div>
        @endif
    </div>
@endsection