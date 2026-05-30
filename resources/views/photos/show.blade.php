@php
    $files = $photo->files;
    $total = $files->count();
    $firstUrl = $files->first()?->url ?? '';
@endphp

@extends(auth()->check() ? 'layouts.app' : 'layouts.guest')
@section('title', $photo->caption)

@section('content')

    {{-- Dynamic background dari foto --}}
    <div class="fixed inset-0 z-0 transition-all duration-700" id="photoBg">
        <img id="bgImg" src="{{ $firstUrl }}" alt="" class="w-full h-full object-cover"
            style="filter:blur(40px);transform:scale(1.1);opacity:.2">
        <div class="absolute inset-0"
            style="background:linear-gradient(to bottom,rgba(9,11,20,.75) 0%,rgba(9,11,20,.5) 40%,rgba(9,11,20,.88) 100%)">
        </div>
    </div>

    <div class="relative z-10 max-w-5xl mx-auto">

        <a href="{{ url()->previous() }}"
            class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white mb-6 px-4 py-2 rounded-xl transition-all"
            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

            {{-- Foto + Slider --}}
            <div>
                <div class="rounded-2xl overflow-hidden relative" style="border:1px solid rgba(255,255,255,.12)">
                    <div class="flex transition-transform duration-400 ease-out" id="ds" style="width:{{ $total * 100 }}%">
                        @foreach($files as $f)
                            <div style="width:{{ 100 / $total }}%">
                                <img src="{{ $f->url }}" alt="{{ $photo->caption }}" class="w-full object-cover"
                                    style="max-height:520px">
                            </div>
                        @endforeach
                    </div>

                    @if($total > 1)
                        <button onclick="dSlide(-1)"
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full flex items-center justify-center text-white transition-all z-10"
                            style="background:rgba(0,0,0,.55);border:1px solid rgba(255,255,255,.15);backdrop-filter:blur(8px)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button onclick="dSlide(1)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full flex items-center justify-center text-white transition-all z-10"
                            style="background:rgba(0,0,0,.55);border:1px solid rgba(255,255,255,.15);backdrop-filter:blur(8px)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-2 z-10">
                            @for($i = 0; $i < $total; $i++)
                                <div id="dd-{{ $i }}"
                                    class="rounded-full transition-all duration-300 {{ $i === 0 ? 'w-4 h-2 bg-white' : 'w-2 h-2 bg-white/40' }}">
                                </div>
                            @endfor
                        </div>
                    @endif
                </div>

                {{-- Thumbnails --}}
                @if($total > 1)
                    <div class="flex gap-2 mt-3 overflow-x-auto pb-2">
                        @foreach($files as $i => $f)
                            <button onclick="dGoTo({{ $i }})" id="dt-{{ $i }}"
                                class="flex-shrink-0 w-14 h-14 rounded-xl overflow-hidden border-2 transition-all {{ $i === 0 ? 'border-violet-500' : 'border-transparent opacity-55' }}">
                                <img src="{{ $f->thumb_url }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Info Panel --}}
            <div class="flex flex-col rounded-2xl p-6"
                style="background:rgba(9,11,20,.72);border:1px solid rgba(255,255,255,.1);backdrop-filter:blur(20px)">

                {{-- Uploader --}}
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <img src="{{ $photo->user->avatar_url }}" alt="{{ $photo->user->name }}"
                            class="w-11 h-11 rounded-full object-cover ring-2 ring-violet-500/30">
                        <div>
                            <p class="font-semibold text-[14px]">{{ $photo->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $photo->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @auth
                        @if(auth()->id() !== $photo->user_id)
                                <button onclick="doFollow({{ $photo->user_id }}, this)"
                                    class="px-4 py-1.5 text-xs rounded-full font-medium transition-all {{ auth()->user()->isFollowing($photo->user_id) ? 'text-gray-300' : 'text-white' }}"
                                    style="{{ auth()->user()->isFollowing($photo->user_id)
                            ? 'background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15)'
                            : 'background:rgba(124,58,237,.4);border:1px solid rgba(124,58,237,.5)' }}">
                                    {{ auth()->user()->isFollowing($photo->user_id) ? '✓ Mengikuti' : '+ Ikuti' }}
                                </button>
                        @endif
                    @endauth
                </div>

                <h1 class="text-2xl font-bold mb-2 leading-tight">{{ $photo->caption }}</h1>
                @if($photo->description)
                    <p class="text-gray-400 text-sm mb-4 leading-relaxed">{{ $photo->description }}</p>
                @endif

                @if($photo->tags->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5 mb-4">
                        @foreach($photo->tags as $tag)
                            <a href="{{ route('search') }}?q={{ $tag->name }}"
                                class="px-3 py-1 text-xs rounded-full text-gray-400 hover:text-violet-400 transition-colors"
                                style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- Stats --}}
                <div class="flex items-center gap-3 py-4 mb-4"
                    style="border-top:1px solid rgba(255,255,255,.08);border-bottom:1px solid rgba(255,255,255,.08)">
                    @auth
                        <button onclick="dLike({{ $photo->id }}, this)"
                            class="flex items-center gap-2 text-sm px-4 py-2 rounded-full transition-all {{ $photo->isLikedBy(auth()->id()) ? 'text-red-400' : 'text-gray-400 hover:text-red-400' }}"
                            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                            <svg class="w-4 h-4" fill="{{ $photo->isLikedBy(auth()->id()) ? 'currentColor' : 'none' }}"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span id="dlc">{{ $photo->likes->count() }}</span>
                        </button>
                    @else
                        <button onclick="openModal('login','like')"
                            class="flex items-center gap-2 text-sm px-4 py-2 rounded-full text-gray-400 hover:text-red-400 transition-all"
                            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            {{ $photo->likes->count() }}
                        </button>
                    @endauth

                    <span class="flex items-center gap-2 text-sm text-gray-400 px-4 py-2 rounded-full"
                        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span id="dcc">{{ $photo->comments->count() }}</span>
                    </span>

                    <span class="flex items-center gap-1.5 text-xs text-gray-600 ml-auto">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{ number_format($photo->views) }}
                    </span>

                    {{-- Owner menu --}}
                    @auth
                        @if(auth()->id() === $photo->user_id)
                            <div class="relative">
                                <button onclick="togglePhotoMenu(this)"
                                    class="flex items-center justify-center w-9 h-9 rounded-full text-gray-400 hover:text-white transition-all"
                                    style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M5 12h.01M12 12h.01M19 12h.01" />
                                    </svg>
                                </button>
                                <div class="hidden absolute right-0 bottom-full mb-2 rounded-xl overflow-hidden shadow-2xl z-50"
                                    style="background:#0f111a;border:1px solid rgba(255,255,255,.1);min-width:160px">
                                    <form method="POST" action="{{ route('photos.destroy', $photo) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus foto ini secara permanen?')"
                                            class="flex items-center gap-2.5 px-4 py-3 text-[12px] text-red-400 hover:bg-red-900/20 transition-colors w-full text-left">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus Foto
                                        </button>
                                    </form>
                                    <button onclick="copyLink('{{ route('photos.show', $photo) }}')"
                                        class="flex items-center gap-2.5 px-4 py-3 text-[12px] text-gray-400 hover:bg-white/5 transition-colors w-full text-left">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        Salin Link
                                    </button>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>

                {{-- Download --}}
                <div class="mb-5">
                    <a href="{{ route('photos.download', $photo) }}"
                        class="flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm text-gray-200 transition-all"
                        style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Foto
                    </a>
                </div>

                {{-- Comments --}}
                <div id="comments" class="overflow-y-auto max-h-64 space-y-3 mb-4 pr-1">
                    @forelse($photo->comments as $c)
                        <div class="flex gap-3">
                            <img src="{{ $c->user->avatar_url }}" class="w-7 h-7 rounded-full flex-shrink-0 object-cover">
                            <div>
                                <span class="text-violet-300 font-medium text-[12px]">{{ $c->user->name }}</span>
                                <span class="text-gray-300 text-[12px] ml-1.5">{{ $c->body }}</span>
                                <p class="text-gray-600 text-[11px] mt-0.5">{{ $c->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-600 text-sm py-8">Belum ada komentar</p>
                    @endforelse
                </div>

                @auth
                    <div class="flex gap-2">
                        <input type="text" id="dci" placeholder="Tulis komentar..."
                            class="flex-1 px-4 py-2.5 text-sm text-white placeholder-gray-600 rounded-xl focus:outline-none transition-all"
                            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)"
                            onkeydown="if(event.key==='Enter') dComment({{ $photo->id }})">
                        <button onclick="dComment({{ $photo->id }})"
                            class="px-5 py-2.5 text-sm text-white rounded-xl transition-all"
                            style="background:rgba(124,58,237,.4);border:1px solid rgba(124,58,237,.5)">
                            Kirim
                        </button>
                    </div>
                @else
                    <button onclick="openModal('login','comment')"
                        class="w-full py-2.5 text-sm text-gray-500 rounded-xl transition-all"
                        style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08)">
                        Login untuk berkomentar
                    </button>
                @endauth
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const _c = document.querySelector('meta[name="csrf-token"]')?.content || '';
        let dIdx = 0;
        const dTot = {{ $total }};

        function updateBg(src) {
            const bg = document.getElementById('bgImg');
            if (!bg) return;
            bg.style.opacity = '0';
            setTimeout(() => { bg.src = src; bg.style.opacity = '.2'; }, 200);
        }

        // Set bg awal
        updateBg('{{ $firstUrl }}');

        function dSlide(dir) { dIdx = (dIdx + dir + dTot) % dTot; dGoTo(dIdx); }
        function dGoTo(i) {
            dIdx = i;
            const s = document.getElementById('ds');
            if (s) s.style.transform = `translateX(-${i * (100 / dTot)}%)`;
            document.querySelectorAll('[id^="dd-"]').forEach((d, j) => {
                d.className = j === i
                    ? 'rounded-full bg-white w-4 h-2 transition-all duration-300'
                    : 'rounded-full bg-white/40 w-2 h-2 transition-all duration-300';
            });
            document.querySelectorAll('[id^="dt-"]').forEach((t, j) => {
                t.classList.toggle('border-violet-500', j === i);
                t.classList.toggle('border-transparent', j !== i);
                t.classList.toggle('opacity-55', j !== i);
            });
            // Update bg sesuai foto aktif
            const imgs = document.querySelectorAll('#ds img');
            if (imgs[i]) updateBg(imgs[i].src);
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'ArrowLeft') dSlide(-1);
            if (e.key === 'ArrowRight') dSlide(1);
        });

        async function dLike(id, btn) {
            const r = await fetch(`/photos/${id}/like`, { method: 'POST', headers: { 'X-CSRF-TOKEN': _c, 'Content-Type': 'application/json' } });
            const d = await r.json();
            const svg = btn.querySelector('svg');
            svg.setAttribute('fill', d.liked ? 'currentColor' : 'none');
            document.getElementById('dlc').textContent = d.total;
            btn.className = btn.className.replace(/text-(gray-400|red-400)/g, '');
            btn.classList.add(d.liked ? 'text-red-400' : 'text-gray-400');
        }

        async function dComment(id) {
            const inp = document.getElementById('dci');
            const body = inp.value.trim(); if (!body) return;
            const r = await fetch(`/photos/${id}/comment`, { method: 'POST', headers: { 'X-CSRF-TOKEN': _c, 'Content-Type': 'application/json' }, body: JSON.stringify({ body }) });
            const d = await r.json();
            const el = document.createElement('div'); el.className = 'flex gap-3';
            el.innerHTML = `<div class="w-7 h-7 rounded-full bg-violet-800 flex-shrink-0 flex items-center justify-center text-xs font-bold">${d.comment.user_name[0]}</div><div><span class="text-violet-300 font-medium text-[12px]">${d.comment.user_name}</span><span class="text-gray-300 text-[12px] ml-1.5">${d.comment.body}</span></div>`;
            const list = document.getElementById('comments');
            list.appendChild(el);
            const cnt = document.getElementById('dcc'); if (cnt) cnt.textContent = d.total;
            inp.value = ''; list.scrollTop = list.scrollHeight;
        }

        async function doFollow(id, btn) {
            const r = await fetch(`/users/${id}/follow`, { method: 'POST', headers: { 'X-CSRF-TOKEN': _c, 'Content-Type': 'application/json' } });
            const d = await r.json();
            btn.textContent = d.is_following ? '✓ Mengikuti' : '+ Ikuti';
            btn.style.background = d.is_following ? 'rgba(255,255,255,.08)' : 'rgba(124,58,237,.4)';
            btn.style.border = d.is_following ? '1px solid rgba(255,255,255,.15)' : '1px solid rgba(124,58,237,.5)';
        }

        function togglePhotoMenu(btn) {
            const m = btn.nextElementSibling;
            m.classList.toggle('hidden');
            const close = e => {
                if (!btn.contains(e.target) && !m.contains(e.target)) {
                    m.classList.add('hidden'); document.removeEventListener('click', close);
                }
            };
            setTimeout(() => document.addEventListener('click', close), 0);
        }

        function copyLink(url) {
            navigator.clipboard.writeText(url).then(() => {
                const t = document.createElement('div');
                t.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 z-50 px-4 py-2.5 rounded-xl text-sm text-white';
                t.style.cssText = 'background:#0f111a;border:1px solid rgba(255,255,255,.12);backdrop-filter:blur(12px)';
                t.textContent = 'Link disalin!';
                document.body.appendChild(t);
                setTimeout(() => t.remove(), 2000);
            });
        }
    </script>
@endpush