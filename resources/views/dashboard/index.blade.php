@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold">Semua Kenangan</h2>
                <p class="text-gray-500 text-sm mt-1">Temukan foto-foto terbaik dari komunitas</p>
            </div>
            <a href="{{ route('upload') }}"
                class="flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-medium text-white transition-all"
                style="background: linear-gradient(135deg, #7C3AED, #6D28D9); border: 1px solid rgba(124,58,237,.5);">
                ＋ Upload
            </a>
        </div>

        @if($photos->isEmpty())
            <div class="text-center py-32 text-gray-600">
                <p class="text-7xl mb-4">📷</p>
                <p class="text-xl mb-2 text-gray-400">Belum ada kenangan</p>
                <a href="{{ route('upload') }}"
                    class="inline-flex items-center gap-2 mt-4 px-6 py-3 rounded-full text-sm text-white font-medium"
                    style="background: rgba(124,58,237,.3); border: 1px solid rgba(124,58,237,.4);">
                    Upload Sekarang
                </a>
            </div>
        @else
            <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
                @foreach($photos as $photo)
                    @if($photo->files->isNotEmpty())

                        {{-- Cek event rank untuk badge --}}
                        @php
                            $eventRank = null;
                            $activeParticipation = $photo->eventParticipations
                                ->filter(fn($p) => $p->event && $p->event->status === 'active')
                                ->first();

                            if ($activeParticipation) {
                                $event = $activeParticipation->event;
                                $ranked = $event->photos()->get();
                                $pos = $ranked->search(fn($p) => $p->id === $photo->id);
                                if ($pos !== false && $pos < $event->max_winners) {
                                    $eventRank = $pos + 1;
                                }
                            }
                        @endphp

                        <div class="break-inside-avoid rounded-2xl overflow-hidden transition-all duration-200 cursor-pointer group"
                            style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); backdrop-filter: blur(12px);"
                            onclick="window.location.href='{{ route('photos.show', $photo) }}'">

                            {{-- Foto / Slider --}}
                            <div class="relative overflow-hidden"
                                onclick="event.stopPropagation(); window.location.href='{{ route('photos.show', $photo) }}'">

                                @if($photo->files->count() > 1)
                                    <div class="flex transition-transform duration-300 ease-out" id="st-{{ $photo->id }}"
                                        style="width: {{ $photo->files->count() * 100 }}%">
                                        @foreach($photo->files as $f)
                                            <div style="width: {{ 100 / $photo->files->count() }}%">
                                                <img src="{{ $f->url }}" alt="{{ $photo->caption }}" class="w-full object-cover">
                                            </div>
                                        @endforeach
                                    </div>
                                    <button onclick="event.stopPropagation(); slideCard('{{ $photo->id }}', -1)"
                                        class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full flex items-center justify-center text-white text-sm transition-all z-10"
                                        style="background: rgba(0,0,0,.5); border: 1px solid rgba(255,255,255,.15); backdrop-filter: blur(8px);">
                                        ‹
                                    </button>
                                    <button onclick="event.stopPropagation(); slideCard('{{ $photo->id }}', 1)"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full flex items-center justify-center text-white text-sm transition-all z-10"
                                        style="background: rgba(0,0,0,.5); border: 1px solid rgba(255,255,255,.15); backdrop-filter: blur(8px);">
                                        ›
                                    </button>
                                    <div class="absolute bottom-2.5 left-0 right-0 flex justify-center gap-1.5 z-10">
                                        @foreach($photo->files as $i => $__)
                                            <div id="sd-{{ $photo->id }}-{{ $i }}"
                                                class="rounded-full transition-all duration-200 {{ $i === 0 ? 'w-3 h-1.5 bg-white' : 'w-1.5 h-1.5 bg-white/40' }}">
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <img src="{{ $photo->files->first()->url }}" alt="{{ $photo->caption }}"
                                        class="w-full object-cover group-hover:scale-[1.02] transition-transform duration-500">
                                @endif

                                {{-- Event rank badge --}}
                                @if($eventRank)
                                    <div class="absolute top-2 left-2 z-10">
                                        <div class="flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold"
                                            style="background: linear-gradient(135deg, rgba(234,179,8,.9), rgba(202,138,4,.9)); border: 1px solid rgba(234,179,8,.5); box-shadow: 0 0 12px rgba(234,179,8,.4);">
                                            {{ $eventRank === 1 ? '👑' : ($eventRank === 2 ? '🥈' : '🥉') }}
                                            #{{ $eventRank }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="p-4">
                                <p class="font-semibold text-sm mb-0.5 truncate text-white/90">{{ $photo->caption }}</p>
                                @if($photo->description)
                                    <p class="text-gray-500 text-xs mb-2 line-clamp-2">{{ $photo->description }}</p>
                                @endif

                                <div class="flex items-center gap-2 mb-3">
                                    <img src="{{ $photo->user->avatar_url }}" class="w-5 h-5 rounded-full object-cover flex-shrink-0">
                                    <span class="text-gray-400 text-xs truncate">{{ $photo->user->name }}</span>
                                    <span class="text-gray-600 text-xs ml-auto">{{ $photo->created_at->diffForHumans() }}</span>
                                </div>

                                @if($photo->tags->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 mb-3">
                                        @foreach($photo->tags->take(3) as $tag)
                                            <a href="{{ route('search') }}?q={{ $tag->name }}" onclick="event.stopPropagation()"
                                                class="px-2.5 py-0.5 text-xs rounded-full transition-colors text-gray-500 hover:text-violet-400"
                                                style="background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.08);">
                                                #{{ $tag->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Action bar: HANYA Like, Comment, Download --}}
                                <div class="flex items-center gap-2 pt-3" style="border-top: 1px solid rgba(255,255,255,.08)">

                                    {{-- Like --}}
                                    <button onclick="event.stopPropagation(); doLike({{ $photo->id }}, this)"
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs transition-all {{ $photo->isLikedBy(auth()->id()) ? 'text-red-400' : 'text-gray-400 hover:text-red-400' }}"
                                        style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.08); backdrop-filter: blur(8px);">
                                        <span>{{ $photo->isLikedBy(auth()->id()) ? '♥' : '♡' }}</span>
                                        <span class="lc">{{ $photo->likes->count() }}</span>
                                    </button>

                                    {{-- Comment --}}
                                    <button
                                        onclick="event.stopPropagation(); window.location.href='{{ route('photos.show', $photo) }}#comments'"
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs text-gray-400 hover:text-blue-400 transition-all"
                                        style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.08); backdrop-filter: blur(8px);">
                                        <span>◯</span>
                                        <span>{{ $photo->comments->count() }}</span>
                                    </button>

                                    {{-- Download --}}
                                    <a href="{{ route('photos.download', $photo) }}" onclick="event.stopPropagation()"
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs text-gray-400 hover:text-green-400 transition-all ml-auto"
                                        style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.08); backdrop-filter: blur(8px);"
                                        title="Download">
                                        ⬇
                                    </a>

                                    {{-- Views --}}
                                    <span class="text-xs text-gray-600 flex items-center gap-1">
                                        👁 {{ number_format($photo->views) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="mt-8">{{ $photos->links() }}</div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const SC = {};

        function slideCard(id, dir) {
            const t = document.getElementById(`st-${id}`);
            const dots = document.querySelectorAll(`[id^="sd-${id}-"]`);
            const n = dots.length;
            SC[id] = ((SC[id] || 0) + dir + n) % n;
            t.style.transform = `translateX(-${SC[id] * (100 / n)}%)`;
            dots.forEach((d, i) => {
                d.className = i === SC[id]
                    ? 'rounded-full bg-white w-3 h-1.5 transition-all duration-200'
                    : 'rounded-full bg-white/40 w-1.5 h-1.5 transition-all duration-200';
            });
        }

        async function doLike(id, btn) {
            const r = await fetch(`/photos/${id}/like`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }
            });
            const d = await r.json();
            btn.querySelector('span').innerHTML = d.liked ? '♥' : '♡';
            btn.querySelector('.lc').textContent = d.total;
            btn.className = btn.className.replace(/text-(gray-400|red-400)/g, '');
            btn.classList.add(d.liked ? 'text-red-400' : 'text-gray-400');
        }
    </script>
@endpush