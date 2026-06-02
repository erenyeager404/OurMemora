@extends(auth()->check() ? 'layouts.app' : 'layouts.guest')
@section('title', $event->title)
@section('content')
    <div>

        <a href="{{ route('events.index') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white mb-6 px-4 py-2 rounded-xl transition-all"
            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Semua Events
        </a>

        {{-- Event header --}}
        <div class="rounded-2xl overflow-hidden mb-8" style="border:1px solid rgba(255,255,255,.1)">
            @if($event->poster_path)
                <div class="relative h-56 overflow-hidden">
                    <img src="{{ $event->poster_url }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0"
                        style="background:linear-gradient(to bottom,transparent 30%,rgba(9,11,20,.92) 100%)"></div>
                    <div class="absolute bottom-0 left-0 p-6">
                        <h1 class="text-3xl font-bold">{{ $event->title }}</h1>
                    </div>
                </div>
            @else
                <div class="h-32 flex items-center justify-center"
                    style="background:linear-gradient(135deg,rgba(124,58,237,.25),rgba(59,130,246,.15))">
                </div>
            @endif

            <div class="p-6" style="background:rgba(255,255,255,.03)">
                @if(!$event->poster_path)
                    <h1 class="text-2xl font-bold mb-4">{{ $event->title }}</h1>
                @endif

                {{-- Status + stats --}}
                <div class="flex items-center flex-wrap gap-4 mb-5 text-sm">
                    @if($event->status === 'active')
                        <span class="flex items-center gap-1.5 text-green-400">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                            Berlangsung
                        </span>
                    @elseif($event->status === 'voting')
                        <span class="flex items-center gap-1.5 text-yellow-400">
                            <span class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></span>
                            Fase Voting
                        </span>
                    @elseif($event->status === 'ended')
                        <span class="text-gray-500">Selesai</span>
                    @endif

                    <span class="flex items-center gap-1.5 text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ $totalParticipants }} peserta
                    </span>

                    @if($event->status === 'active' && $event->daysRemaining() > 0)
                        <span class="text-violet-400 font-medium">
                            ⏳ {{ $event->daysRemaining() }} hari tersisa
                        </span>
                    @elseif($event->status === 'active')
                        <span class="text-orange-400 font-medium">⚠ Berakhir hari ini!</span>
                    @endif

                    <span class="text-gray-600 text-xs">
                        {{ $event->start_date->format('d M Y') }} — {{ $event->end_date->format('d M Y') }}
                    </span>
                </div>

                @if($event->description)
                    <p class="text-gray-300 text-sm leading-relaxed mb-5">{{ $event->description }}</p>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    @if($event->rules)
                        <div class="p-4 rounded-xl"
                            style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08)">
                            <p class="text-[11px] text-gray-500 font-semibold uppercase tracking-wide mb-2">Aturan</p>
                            <p class="text-sm text-gray-300 leading-relaxed whitespace-pre-line">{{ $event->rules }}</p>
                        </div>
                    @endif
                    <div class="p-4 rounded-xl"
                        style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08)">
                        <p class="text-[11px] text-gray-500 font-semibold uppercase tracking-wide mb-2">Hadiah & Pemenang
                        </p>
                        @if($event->prize_description)
                            <p class="text-sm text-gray-300 leading-relaxed mb-2">{{ $event->prize_description }}</p>
                        @endif
                        <p class="text-sm text-gray-400">🏆 Top {{ $event->max_winners }} foto terbanyak like</p>
                        @if($event->auto_tag)
                            <div class="mt-2">
                                <span class="px-2.5 py-1 text-[11px] rounded-full text-violet-300"
                                    style="background:rgba(124,58,237,.12);border:1px solid rgba(124,58,237,.25)">
                                    #{{ $event->auto_tag }}
                                </span>
                                <span class="ml-1 px-2.5 py-1 text-[11px] rounded-full text-violet-300"
                                    style="background:rgba(124,58,237,.12);border:1px solid rgba(124,58,237,.25)">
                                    #EventMemora
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- User sudah join --}}
                @if($userHasJoined && $userPhoto)
                    <div class="flex items-center gap-3 p-4 rounded-xl mb-4"
                        style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25)">
                        <img src="{{ $userPhoto->files->first()->thumb_url }}" class="w-12 h-12 rounded-lg object-cover">
                        <div>
                            <p class="text-sm font-medium text-green-400">✓ Kamu sudah ikut event ini</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $userPhoto->caption }}</p>
                        </div>
                        <a href="{{ route('photos.show', $userPhoto) }}"
                            class="ml-auto text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg transition-all"
                            style="background:rgba(255,255,255,.08)">
                            Lihat Foto
                        </a>
                    </div>
                @elseif($event->canSubmit())
                    @auth
                        <a href="{{ route('upload') }}"
                            class="inline-flex items-center gap-2 px-7 py-3 rounded-xl text-sm font-medium text-white transition-all"
                            style="background:rgba(124,58,237,.45);border:1px solid rgba(124,58,237,.55)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Upload Foto untuk Event Ini
                        </a>
                    @else
                        <button onclick="openModal('login')"
                            class="inline-flex items-center gap-2 px-7 py-3 rounded-xl text-sm font-medium text-white transition-all"
                            style="background:rgba(124,58,237,.35);border:1px solid rgba(124,58,237,.45)">
                            Login untuk ikut event
                        </button>
                    @endauth
                @endif
            </div>
        </div>

        {{-- Leaderboard --}}
        <h3 class="font-semibold mb-4 text-gray-300">Leaderboard ({{ $leaderboard->count() }} foto)</h3>
        <div class="space-y-2">
            @forelse($leaderboard as $i => $photo)
                @if($photo->files->isNotEmpty())
                    <div class="flex items-center gap-4 p-4 rounded-2xl transition-all"
                        style="background:{{ $i === 0 ? 'rgba(234,179,8,.07)' : ($i === 1 ? 'rgba(156,163,175,.04)' : 'rgba(255,255,255,.02)') }};border:1px solid {{ $i === 0 ? 'rgba(234,179,8,.2)' : 'rgba(255,255,255,.06)' }}">

                        <div class="w-8 text-center flex-shrink-0">
                            @if($i === 0) <span class="text-lg">👑</span>
                            @elseif($i === 1) <span class="text-lg">🥈</span>
                            @elseif($i === 2) <span class="text-lg">🥉</span>
                            @else <span class="text-sm text-gray-500 font-medium">#{{ $i + 1 }}</span>
                            @endif
                        </div>

                        <img src="{{ $photo->files->first()->thumb_url }}" class="w-14 h-14 rounded-xl object-cover flex-shrink-0">

                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm truncate">{{ $photo->caption }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <img src="{{ $photo->user->avatar_url }}" class="w-4 h-4 rounded-full">
                                <span class="text-gray-500 text-xs">{{ $photo->user->name }}</span>
                            </div>
                        </div>

                        <span class="text-red-400 font-bold text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            {{ $photo->likes_count }}
                        </span>

                        <a href="{{ route('photos.show', $photo) }}"
                            class="flex items-center px-3 py-1.5 rounded-lg text-xs text-gray-400 hover:text-white transition-all"
                            style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1)">
                            Lihat
                        </a>
                    </div>
                @endif
            @empty
                <p class="text-center text-gray-600 py-10">Belum ada foto</p>
            @endforelse
        </div>
    </div>
@endsection