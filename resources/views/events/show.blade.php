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
        @if($leaderboard->isNotEmpty())
            <h2 class="text-xl font-bold mb-5">
                🏆 Leaderboard
                <span class="text-gray-500 text-base font-normal ml-2">Top {{ $leaderboard->count() }} berdasarkan like</span>
            </h2>

            {{-- Top 3 --}}
            @if($leaderboard->count() >= 3)
                <div class="grid grid-cols-3 gap-4 mb-6">
                    @foreach($leaderboard->take(3) as $i => $photo)
                        @if($photo->files->isNotEmpty())
                            <a href="{{ route('photos.show', $photo) }}"
                                class="rounded-2xl overflow-hidden group block transition-all {{ $i === 0 ? 'ring-2 ring-yellow-400/50' : '' }}"
                                style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09)">
                                <div class="relative overflow-hidden" style="height:{{ $i === 0 ? '180px' : '144px' }}">
                                    <img src="{{ $photo->files->first()->thumb_url }}" alt="{{ $photo->caption }}"
                                        class="w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-400">
                                    <div class="absolute top-2 left-2">
                                        <span class="px-2.5 py-1 text-[11px] font-bold rounded-full"
                                            style="{{ $i === 0
                                        ? 'background:rgba(234,179,8,.9);color:#000;box-shadow:0 0 10px rgba(234,179,8,.4)'
                                        : ($i === 1 ? 'background:rgba(156,163,175,.8);color:#000' : 'background:rgba(180,83,9,.85);color:#fff') }}">
                                            {{ $i === 0 ? '👑 #1' : ($i === 1 ? '🥈 #2' : '🥉 #3') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <p class="text-[13px] font-medium truncate">{{ $photo->caption }}</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-gray-500 text-xs truncate">{{ $photo->user->name }}</span>
                                        <span class="text-red-400 text-xs font-medium flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                            {{ $photo->likes_count }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif

            {{-- Slider horizontal sisa --}}
            @if($leaderboard->count() > 3)
                <div class="overflow-x-auto pb-3">
                    <div class="flex gap-3" style="min-width:max-content">
                        @foreach($leaderboard as $i => $photo)
                            @if($i >= 3 && $photo->files->isNotEmpty())
                                <a href="{{ route('photos.show', $photo) }}"
                                    class="flex-shrink-0 w-44 rounded-xl overflow-hidden group block transition-all"
                                    style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08)">
                                    <div class="relative h-32 overflow-hidden">
                                        <img src="{{ $photo->files->first()->thumb_url }}" alt="{{ $photo->caption }}"
                                            class="w-full h-full object-cover group-hover:scale-[1.04] transition-transform duration-300">
                                        <div class="absolute top-2 left-2">
                                            <span class="px-2 py-0.5 text-[10px] font-medium rounded-full text-white"
                                                style="background:rgba(0,0,0,.6);border:1px solid rgba(255,255,255,.15)">
                                                #{{ $i + 1 }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        <p class="text-xs font-medium truncate">{{ $photo->caption }}</p>
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="text-gray-600 text-[11px] truncate">{{ $photo->user->name }}</span>
                                            <span class="text-red-400 text-[11px] flex items-center gap-0.5">
                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                                {{ $photo->likes_count }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-14 text-gray-600 rounded-2xl"
                style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06)">
                <svg class="w-14 h-14 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-gray-500 text-lg">Belum ada foto di event ini</p>
                @if($event->canSubmit())
                    <a href="{{ route('upload') }}"
                        class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl text-sm text-white transition-all"
                        style="background:rgba(124,58,237,.4);border:1px solid rgba(124,58,237,.5)">
                        Jadilah yang pertama!
                    </a>
                @endif
            </div>
        @endif
    </div>
@endsection