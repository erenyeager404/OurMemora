@extends('layouts.app')
@section('title', 'Dashboard')

@push('head')
    <style>
        .photo-card {
            position: relative;
            background: rgba(255, 255, 255, .055);
            border: 1px solid rgba(255, 255, 255, .09);
            border-radius: 1.25rem;
            overflow: hidden;
            transition: background .15s;
        }

        .photo-card:hover {
            background: rgba(255, 255, 255, .08);
        }

        .action-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 99px;
            font-size: 11px;
            border: 1px solid rgba(255, 255, 255, .09);
            background: rgba(255, 255, 255, .055);
            transition: all .15s;
            cursor: pointer;
        }

        .action-pill:hover {
            background: rgba(255, 255, 255, .1);
        }

        .action-pill.liked {
            background: rgba(239, 68, 68, .15);
            border-color: rgba(239, 68, 68, .35);
            color: #f87171;
        }

        .action-pill.saved {
            background: rgba(124, 58, 237, .15);
            border-color: rgba(124, 58, 237, .35);
            color: #c4b5fd;
        }
    </style>
@endpush

@section('content')
    <div>
        <div class="flex items-center justify-between mb-7">
            <div>
                <h2 class="text-2xl font-bold">Semua Foto </h2>
                <p class="text-gray-500 text-sm mt-0.5">Foto-foto terbaru dari komunitas</p>
            </div>
            <a href="{{ route('upload') }}"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-white transition-all"
                style="background:rgba(124,58,237,.4);border:1px solid rgba(124,58,237,.5)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Upload
            </a>
        </div>

        @if($photos->isEmpty())
            <div class="text-center py-32 text-gray-600">
                <svg class="w-20 h-20 mx-auto mb-5 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-xl text-gray-400">Jadi lah yang pertama</p>
            </div>
        @else
            <!-- Skeleton -->
            <div id="skGrid" class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
                @for($i = 0; $i < 8; $i++)
                    <div class="break-inside-avoid rounded-2xl overflow-hidden" style="border:1px solid rgba(255,255,255,.07)">
                        <div class="skeleton" style="height:{{ 140 + ($i % 4) * 40 }}px"></div>
                        <div class="p-4 space-y-2">
                            <div class="skeleton h-3.5 w-3/4 rounded-lg"></div>
                            <div class="skeleton h-3 w-1/2 rounded-lg"></div>
                            <div class="skeleton h-3 w-1/3 rounded-lg mt-4"></div>
                        </div>
                    </div>
                @endfor
            </div>

            <!-- Real Grid -->
            <div id="realGrid" class="hidden columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
                @foreach($photos as $photo)
                    @if($photo->files->isNotEmpty())
                        @php
                            // Cek kemenangan event (selesai)
                            $win = $photo->eventParticipation ? $photo->getFinishedEventWinAttribute() : null;
                            // Jika tidak menang, cek peringkat event aktif
                            $eventRank = null;
                            if (!$win) {
                                $ep = $photo->eventParticipation;
                                if ($ep && $ep->event && in_array($ep->event->status, ['active', 'voting'])) {
                                    $lb = $ep->event->getLeaderboard();
                                    $pos = $lb->search(fn($p) => $p->id === $photo->id);
                                    if ($pos !== false && $pos < $ep->event->max_winners) {
                                        $eventRank = $pos + 1;
                                    }
                                }
                            }
                            // Nama event untuk badge kemenangan
                            $eventName = $win ? ($win['event_name'] ?? $photo->eventParticipation?->event?->name ?? 'Event') : null;
                            $tagEventName = $eventName ? '#Pemenang' . str_replace(' ', '', $eventName) : '';
                        @endphp

                        <div class="photo-card break-inside-avoid cursor-pointer"
                            onclick="window.location.href='{{ route('photos.show', $photo) }}'">
                            @if($win)
                                <div class="absolute top-2 left-2 z-10">
                                    <div class="flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold"
                                        style="background:rgba(234,179,8,.92);color:#000;box-shadow:0 0 10px rgba(234,179,8,.5)">
                                        @if($win['rank'] === 1) 👑
                                        @elseif($win['rank'] === 2) 🥈
                                        @else 🥉
                                        @endif
                                        #{{ $win['rank'] }}
                                        @if($tagEventName)
                                            <span class="opacity-90">{{ $tagEventName }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <div class="relative overflow-hidden">
                                @if($photo->files->count() > 1)
                                    <div class="flex transition-transform duration-300" id="st-{{ $photo->id }}"
                                        style="width:{{ $photo->files->count() * 100 }}%">
                                        @foreach($photo->files as $f)
                                            <div style="width:{{ 100 / $photo->files->count() }}%">
                                                <img data-src="{{ $f->thumb_url }}"
                                                    src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 3'%3E%3C/svg%3E"
                                                    alt="{{ $photo->caption }}" class="lazy w-full object-cover" style="max-height:320px">
                                            </div>
                                        @endforeach
                                    </div>
                                    <button onclick="event.stopPropagation(); slideC('{{ $photo->id }}',-1)"
                                        class="absolute left-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full flex items-center justify-center text-white z-10"
                                        style="background:rgba(0,0,0,.55);border:1px solid rgba(255,255,255,.15);backdrop-filter:blur(8px)">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button onclick="event.stopPropagation(); slideC('{{ $photo->id }}',1)"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full flex items-center justify-center text-white z-10"
                                        style="background:rgba(0,0,0,.55);border:1px solid rgba(255,255,255,.15);backdrop-filter:blur(8px)">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                    <div class="absolute bottom-2 left-0 right-0 flex justify-center gap-1 z-10">
                                        @foreach($photo->files as $i => $__)
                                            <div id="sd-{{ $photo->id }}-{{ $i }}"
                                                class="rounded-full transition-all duration-200 {{ $i === 0 ? 'w-3 h-1.5 bg-white' : 'w-1.5 h-1.5 bg-white/40' }}">
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <img data-src="{{ $photo->files->first()->thumb_url }}"
                                        src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 3'%3E%3C/svg%3E"
                                        alt="{{ $photo->caption }}"
                                        class="lazy w-full object-cover group-hover:scale-[1.02] transition-transform duration-400"
                                        style="max-height:360px">
                                @endif

                                @if($eventRank)
                                    <div class="absolute top-2 left-2 z-10">
                                        <span class="flex items-center gap-1 px-2 py-1 rounded-full text-[11px] font-bold"
                                            style="background:rgba(234,179,8,.9);color:#000;box-shadow:0 0 10px rgba(234,179,8,.5)">
                                            @if($eventRank === 1) 👑
                                            @elseif($eventRank === 2) 🥈
                                            @else 🥉
                                            @endif
                                            #{{ $eventRank }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <div class="p-4">
                                <p class="font-semibold text-[13px] mb-0.5 truncate">{{ $photo->caption }}</p>
                                @if($photo->description)
                                    <p class="text-gray-500 text-xs mb-2 line-clamp-1">{{ $photo->description }}</p>
                                @endif

                                <div class="flex items-center gap-2 mb-3">
                                    <img data-src="{{ $photo->user->avatar_url }}"
                                        src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E"
                                        class="lazy w-5 h-5 rounded-full object-cover flex-shrink-0">
                                    <span class="text-gray-400 text-[12px] truncate">{{ $photo->user->name }}</span>
                                    <span class="text-gray-600 text-[11px] ml-auto">{{ $photo->created_at->diffForHumans() }}</span>
                                </div>

                                @if($photo->tags->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 mb-3">
                                        @foreach($photo->tags->take(3) as $tag)
                                            <a href="{{ route('search') }}?q={{ $tag->name }}" onclick="event.stopPropagation()"
                                                class="px-2 py-0.5 text-[11px] rounded-full text-gray-500 hover:text-violet-400 transition-colors"
                                                style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08)">#{{ $tag->name }}</a>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="flex items-center gap-1.5 pt-3" style="border-top:1px solid rgba(255,255,255,.07)">
                                    <button onclick="event.stopPropagation(); doLike({{ $photo->id }},this)"
                                        class="action-pill {{ $photo->isLikedBy(auth()->id()) ? 'liked' : '' }}">
                                        <svg class="w-3.5 h-3.5" fill="{{ $photo->isLikedBy(auth()->id()) ? 'currentColor' : 'none' }}"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        <span class="lc">{{ $photo->likes->count() }}</span>
                                    </button>
                                    <button onclick="event.stopPropagation(); doSave({{ $photo->id }},this)"
                                        class="action-pill {{ $photo->isSavedBy(auth()->id()) ? 'saved' : '' }}">
                                        <svg class="w-3.5 h-3.5" fill="{{ $photo->isSavedBy(auth()->id()) ? 'currentColor' : 'none' }}"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                        </svg>
                                    </button>
                                    <button
                                        onclick="event.stopPropagation(); window.location.href='{{ route('photos.show', $photo) }}#comments'"
                                        class="action-pill">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        {{ $photo->comments->count() }}
                                    </button>
                                    <a href="{{ route('photos.download', $photo) }}" onclick="event.stopPropagation()"
                                        class="action-pill ml-auto hover:!text-green-400 hover:!border-green-400/30 hover:!bg-green-400/10">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                    </a>
                                    <span class="flex items-center gap-1 text-[11px] text-gray-600">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{ number_format($photo->views) }}
                                    </span>
                                    <div class="relative">
                                        <button onclick="event.stopPropagation(); toggleDD(this)"
                                            class="action-pill w-7 h-7 !p-0 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                    d="M5 12h.01M12 12h.01M19 12h.01" />
                                            </svg>
                                        </button>
                                        <div class="hidden absolute right-0 bottom-full mb-2 rounded-xl overflow-hidden shadow-2xl z-50"
                                            style="background:#0f111a;border:1px solid rgba(255,255,255,.1);min-width:150px">
                                            @if($photo->user_id === auth()->id())
                                                <form method="POST" action="{{ route('photos.destroy', $photo) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        onclick="event.stopPropagation(); return confirm('Hapus foto ini?')"
                                                        class="flex items-center gap-2.5 px-4 py-2.5 text-[12px] text-red-400 hover:bg-red-900/20 transition-colors w-full text-left">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Hapus Foto
                                                    </button>
                                                </form>
                                            @endif
                                            <button onclick="event.stopPropagation(); copyLink('{{ route('photos.show', $photo) }}')"
                                                class="flex items-center gap-2.5 px-4 py-2.5 text-[12px] text-gray-300 hover:bg-white/5 transition-colors w-full text-left">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                                Salin Link
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div id="paginationArea" class="hidden mt-8">
                {{ $photos->links() }}
            </div>
        @endif
    </div>

    <div id="toast"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] px-5 py-3 rounded-xl text-sm text-white shadow-xl hidden"
        style="background:#0f111a;border:1px solid rgba(255,255,255,.12);backdrop-filter:blur(12px)">
        Link disalin!
    </div>
@endsection

@push('scripts')
    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const SC = {};

        function showRealGrid() {
            document.getElementById('skGrid')?.classList.add('hidden');
            document.getElementById('realGrid')?.classList.remove('hidden');
            document.getElementById('paginationArea')?.classList.remove('hidden');
        }
        window.addEventListener('load', showRealGrid);
        setTimeout(showRealGrid, 1200);

        const lazyImgs = document.querySelectorAll('img.lazy');
        const obs = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    const img = e.target;
                    img.src = img.dataset.src;
                    img.addEventListener('load', () => img.classList.add('loaded'), { once: true });
                    obs.unobserve(img);
                }
            });
        }, { rootMargin: '250px' });
        lazyImgs.forEach(img => obs.observe(img));

        function slideC(id, dir) {
            const t = document.getElementById(`st-${id}`);
            const dots = document.querySelectorAll(`[id^="sd-${id}-"]`);
            const n = dots.length;
            SC[id] = ((SC[id] || 0) + dir + n) % n;
            t.style.transform = `translateX(-${SC[id] * (100 / n)}%)`;
            dots.forEach((d, i) => {
                d.className = i === SC[id] ? 'rounded-full bg-white w-3 h-1.5 transition-all duration-200' : 'rounded-full bg-white/40 w-1.5 h-1.5 transition-all duration-200';
            });
        }

        async function doLike(id, btn) {
            const r = await fetch(`/photos/${id}/like`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' } });
            const d = await r.json();
            const svg = btn.querySelector('svg');
            btn.querySelector('.lc').textContent = d.total;
            svg.setAttribute('fill', d.liked ? 'currentColor' : 'none');
            btn.classList.toggle('liked', d.liked);
        }

        async function doSave(id, btn) {
            const r = await fetch(`/photos/${id}/save`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' } });
            const d = await r.json();
            const svg = btn.querySelector('svg');
            svg.setAttribute('fill', d.saved ? 'currentColor' : 'none');
            btn.classList.toggle('saved', d.saved);
        }

        function toggleDD(btn) {
            const m = btn.nextElementSibling;
            document.querySelectorAll('.hidden.absolute').forEach(x => { if (x !== m) x.classList.add('hidden'); });
            m.classList.toggle('hidden');
            const close = e => {
                if (!btn.contains(e.target) && !m.contains(e.target)) {
                    m.classList.add('hidden');
                    document.removeEventListener('click', close);
                }
            };
            setTimeout(() => document.addEventListener('click', close), 0);
        }

        function copyLink(url) {
            navigator.clipboard.writeText(url).then(() => {
                const t = document.getElementById('toast');
                t.classList.remove('hidden');
                setTimeout(() => t.classList.add('hidden'), 2000);
            });
        }
    </script>
@endpush