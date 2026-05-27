@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div>
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold">Semua Kenangan</h2>
            <a href="{{ route('upload') }}"
                class="flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 rounded-xl text-sm font-medium text-white transition-colors">
                ＋ Upload
            </a>
        </div>

        @if($photos->isEmpty())
            <div class="text-center py-24 text-gray-500">
                <p class="text-7xl mb-4">📷</p>
                <p class="text-xl mb-2">Belum ada kenangan</p>
                <a href="{{ route('upload') }}"
                    class="inline-flex items-center gap-2 mt-4 px-6 py-2.5 bg-violet-600 hover:bg-violet-700 rounded-xl text-sm text-white transition-colors">
                    Upload Sekarang
                </a>
            </div>
        @else
            <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
                @foreach($photos as $photo)
                    @if($photo->files->isNotEmpty())
                        <div
                            class="break-inside-avoid bg-white/10 backdrop-blur-md rounded-2xl overflow-hidden border border-white/15 hover:bg-white/15 transition-colors">

                            {{-- Foto / Slider --}}
                            <div class="relative overflow-hidden cursor-pointer"
                                onclick="openDetail({{ $photo->id }}, '{{ $photo->files->first()->url }}')">

                                @if($photo->files->count() > 1)
                                    <div class="flex transition-transform duration-300 ease-out" id="st-{{ $photo->id }}"
                                        style="width: {{ $photo->files->count() * 100 }}%">
                                        @foreach($photo->files as $f)
                                            <div style="width: {{ 100 / $photo->files->count() }}%">
                                                <img src="{{ $f->url }}" alt="{{ $photo->caption }}" class="w-full object-cover">
                                            </div>
                                        @endforeach
                                    </div>

                                    <button onclick="event.stopPropagation(); slide('{{ $photo->id }}', -1)"
                                        class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/60 hover:bg-black/80 text-white rounded-full flex items-center justify-center text-sm transition-colors z-10">
                                        ‹
                                    </button>
                                    <button onclick="event.stopPropagation(); slide('{{ $photo->id }}', 1)"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-black/60 hover:bg-black/80 text-white rounded-full flex items-center justify-center text-sm transition-colors z-10">
                                        ›
                                    </button>

                                    <div class="absolute bottom-2.5 left-0 right-0 flex justify-center gap-1.5 z-10">
                                        @foreach($photo->files as $i => $__)
                                            <div id="sd-{{ $photo->id }}-{{ $i }}"
                                                class="rounded-full transition-all duration-200 {{ $i === 0 ? 'w-3 h-1.5 bg-white' : 'w-1.5 h-1.5 bg-white/50' }}">
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <img src="{{ $photo->files->first()->url }}" alt="{{ $photo->caption }}" class="w-full object-cover">
                                @endif
                            </div>

                            <div class="p-4">
                                <p class="font-semibold text-sm mb-0.5 truncate">{{ $photo->caption }}</p>
                                @if($photo->description)
                                    <p class="text-gray-400/70 text-xs mb-2 line-clamp-2">{{ $photo->description }}</p>
                                @endif

                                <div class="flex items-center gap-2 mb-3">
                                    <img src="{{ $photo->user->avatar_url }}" class="w-5 h-5 rounded-full object-cover flex-shrink-0">
                                    <span class="text-gray-400/80 text-xs truncate">{{ $photo->user->name }}</span>
                                    <span class="text-gray-600 text-xs ml-auto">{{ $photo->created_at->diffForHumans() }}</span>
                                </div>

                                @if($photo->tags->isNotEmpty())
                                    <div class="flex flex-wrap gap-1 mb-3">
                                        @foreach($photo->tags->take(3) as $tag)
                                            <a href="{{ route('search') }}?q={{ $tag->name }}"
                                                class="px-2.5 py-0.5 bg-gray-800/80 text-gray-500 text-xs rounded-full hover:text-violet-400 transition-colors">
                                                #{{ $tag->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Actions --}}
                                <div class="flex items-center gap-3 pt-3 border-t border-white/10">
                                    <button onclick="doLike({{ $photo->id }}, this)"
                                        class="flex items-center gap-1.5 text-xs transition-colors {{ $photo->isLikedBy(auth()->id()) ? 'text-red-400' : 'text-gray-400 hover:text-red-400' }}">
                                        <span>{{ $photo->isLikedBy(auth()->id()) ? '♥' : '♡' }}</span>
                                        <span class="lc">{{ $photo->likes->count() }}</span>
                                    </button>

                                    <button onclick="doSave({{ $photo->id }}, this)"
                                        class="flex items-center gap-1 text-xs transition-colors {{ $photo->isSavedBy(auth()->id()) ? 'text-violet-400' : 'text-gray-400 hover:text-violet-400' }}">
                                        <span>{{ $photo->isSavedBy(auth()->id()) ? '◈' : '◇' }}</span>
                                    </button>

                                    <button onclick="doCmt({{ $photo->id }})"
                                        class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-blue-400 transition-colors">
                                        <span>◯</span>
                                        <span class="cc">{{ $photo->comments->count() }}</span>
                                    </button>

                                    <span class="flex items-center gap-1 text-xs text-gray-600 ml-auto">
                                        👁 {{ number_format($photo->views) }}
                                    </span>

                                    {{-- More --}}
                                    <div class="relative">
                                        <button onclick="toggleDD(this)"
                                            class="text-gray-400 hover:text-white transition-colors px-1 text-base">
                                            ⋯
                                        </button>
                                        <div
                                            class="hidden absolute right-0 bottom-full mb-2 w-40 bg-gray-800 rounded-xl shadow-2xl border border-gray-700/50 z-50 overflow-hidden">
                                            <a href="{{ route('photos.download', $photo) }}"
                                                class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-gray-300 hover:bg-gray-700/80 transition-colors">
                                                ⬇ Download
                                            </a>
                                            <button onclick="copyLink('{{ route('photos.show', $photo) }}')"
                                                class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-gray-300 hover:bg-gray-700/80 transition-colors w-full text-left">
                                                🔗 Salin Link
                                            </button>
                                            @if($photo->user_id === auth()->id())
                                                <form method="POST" action="{{ route('photos.destroy', $photo) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Hapus foto ini?')"
                                                        class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-red-400 hover:bg-red-900/20 transition-colors w-full text-left">
                                                        🗑 Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Comment box --}}
                                <div id="cs-{{ $photo->id }}" class="hidden mt-3">
                                    <div id="cl-{{ $photo->id }}" class="space-y-1.5 max-h-28 overflow-y-auto mb-2">
                                        @foreach($photo->comments->take(5) as $c)
                                            <div class="text-xs">
                                                <span class="text-violet-300 font-medium">{{ $c->user->name }}</span>
                                                <span class="text-gray-300 ml-1">{{ $c->body }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex gap-2">
                                        <input type="text" id="ci-{{ $photo->id }}" placeholder="Tulis komentar..."
                                            class="flex-1 px-3 py-2 bg-white/8 border border-white/15 rounded-lg text-xs text-white placeholder-gray-500 focus:outline-none focus:border-violet-500 transition-colors">
                                        <button onclick="sendCmt({{ $photo->id }})"
                                            class="px-3 py-2 bg-violet-600 hover:bg-violet-700 rounded-lg text-xs text-white transition-colors">
                                            Kirim
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="mt-8">{{ $photos->links() }}</div>
        @endif
    </div>

    {{-- Photo Detail Modal --}}
    <div id="detailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm"
        onclick="if(event.target===this) closeDetail()">

        {{-- Background foto --}}
        <div class="fixed inset-0 z-0 transition-all duration-500">
            <img id="dBgImg" src="" alt="" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/75 backdrop-blur-xl"></div>
        </div>

        <div class="relative z-10 w-full max-w-5xl mx-4 my-6 max-h-[90vh] overflow-y-auto">
            <div id="dContent" class="bg-gray-900/95 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-center py-24 text-gray-400 text-3xl">◌</div>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div id="toast"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] bg-gray-800 border border-gray-700 text-white text-sm px-5 py-3 rounded-xl shadow-xl hidden">
        🔗 Link disalin!
    </div>

@endsection

@push('scripts')
    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const SS = {};

        // Slider
        function slide(id, dir) {
            const t = document.getElementById(`st-${id}`);
            const dots = document.querySelectorAll(`[id^="sd-${id}-"]`);
            const n = dots.length;
            SS[id] = ((SS[id] || 0) + dir + n) % n;
            t.style.transform = `translateX(-${SS[id] * (100 / n)}%)`;
            dots.forEach((d, i) => {
                if (i === SS[id]) { d.className = 'rounded-full bg-white w-3 h-1.5 transition-all duration-200'; }
                else { d.className = 'rounded-full bg-white/50 w-1.5 h-1.5 transition-all duration-200'; }
            });
        }

        // Detail modal
        async function openDetail(id, bg) {
            const m = document.getElementById('detailModal');
            document.getElementById('dBgImg').src = bg;
            document.getElementById('dContent').innerHTML = '<div class="flex items-center justify-center py-24 text-gray-400 text-3xl">◌</div>';
            m.classList.remove('hidden');
            m.classList.add('flex');
            document.body.style.overflow = 'hidden';
            const res = await fetch(`/photo/${id}?partial=1`);
            document.getElementById('dContent').innerHTML = await res.text();
        }

        function closeDetail() {
            const m = document.getElementById('detailModal');
            m.classList.add('hidden');
            m.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDetail(); });

        // Like
        async function doLike(id, btn) {
            const r = await fetch(`/photos/${id}/like`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' } });
            const d = await r.json();
            btn.querySelector('span').innerHTML = d.liked ? '♥' : '♡';
            btn.querySelector('.lc').textContent = d.total;
            btn.className = btn.className.replace(/text-(gray-400|red-400)/g, '');
            btn.classList.add(d.liked ? 'text-red-400' : 'text-gray-400');
        }

        // Save
        async function doSave(id, btn) {
            const r = await fetch(`/photos/${id}/save`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' } });
            const d = await r.json();
            btn.querySelector('span').innerHTML = d.saved ? '◈' : '◇';
            btn.className = btn.className.replace(/text-(gray-400|violet-400)/g, '');
            btn.classList.add(d.saved ? 'text-violet-400' : 'text-gray-400');
        }

        // Comment
        function doCmt(id) { document.getElementById(`cs-${id}`).classList.toggle('hidden'); }

        async function sendCmt(id) {
            const inp = document.getElementById(`ci-${id}`);
            const body = inp.value.trim(); if (!body) return;
            const r = await fetch(`/photos/${id}/comment`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }, body: JSON.stringify({ body }) });
            const d = await r.json();
            const el = document.createElement('div'); el.className = 'text-xs';
            el.innerHTML = `<span class="text-violet-300 font-medium">${d.comment.user_name}</span><span class="text-gray-300 ml-1">${d.comment.body}</span>`;
            document.getElementById(`cl-${id}`).appendChild(el);
            const cc = document.querySelector(`button[onclick="doCmt(${id})"] .cc`);
            if (cc) cc.textContent = d.total;
            inp.value = '';
        }

        // Dropdown
        function toggleDD(btn) {
            const m = btn.nextElementSibling;
            document.querySelectorAll('[class*="absolute right-0 bottom-full"]').forEach(x => { if (x !== m) x.classList.add('hidden'); });
            m.classList.toggle('hidden');
            const close = e => { if (!btn.contains(e.target) && !m.contains(e.target)) { m.classList.add('hidden'); document.removeEventListener('click', close); } };
            setTimeout(() => document.addEventListener('click', close), 0);
        }

        // Copy link
        function copyLink(url) {
            navigator.clipboard.writeText(url).then(() => {
                const t = document.getElementById('toast');
                t.classList.remove('hidden');
                setTimeout(() => t.classList.add('hidden'), 2000);
            });
        }
    </script>
@endpush