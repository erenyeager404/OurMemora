@php
    $isPartial = false;
    $total = $albumPhotos->count();
    $firstUrl = $photo->files->first()?->url ?? '';
@endphp

@extends(auth()->check() ? 'layouts.app' : 'layouts.guest')
@section('title', $photo->caption)

@section('content')

    {{-- Full page dynamic background dari foto --}}
    <div class="fixed inset-0 z-0" id="photoBg">
        <img id="bgPhotoImg" src="{{ $firstUrl }}" alt="" class="w-full h-full object-cover transition-all duration-700"
            style="filter: blur(40px); transform: scale(1.1); opacity: .22;">
        <div class="absolute inset-0"
            style="background: linear-gradient(to bottom, rgba(9,11,20,.7) 0%, rgba(9,11,20,.5) 50%, rgba(9,11,20,.85) 100%)">
        </div>
    </div>

    <div class="relative z-10 max-w-5xl mx-auto">

        <a href="{{ url()->previous() }}"
            class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-sm mb-6 transition-colors px-4 py-2 rounded-full"
            style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); backdrop-filter: blur(8px);">
            ← Kembali
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

            {{-- Foto + Slider --}}
            <div>
                <div class="rounded-2xl overflow-hidden relative" style="border: 1px solid rgba(255,255,255,.12);">
                    <div class="flex transition-transform duration-400 ease-out" id="ds" style="width: {{ $total * 100 }}%">
                        @foreach($albumPhotos as $ap)
                            @foreach($ap->files as $f)
                                <div style="width: {{ 100 / $total }}%">
                                    <img src="{{ $f->url }}" alt="{{ $ap->caption }}" class="w-full object-cover"
                                        onload="if(this.closest('#ds').parentElement) updatePhotoBg(this.src)">
                                </div>
                            @endforeach
                        @endforeach
                    </div>

                    @if($total > 1)
                        <button onclick="dSlide(-1)"
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full flex items-center justify-center text-white transition-all"
                            style="background: rgba(0,0,0,.5); border: 1px solid rgba(255,255,255,.15); backdrop-filter: blur(10px);">
                            ‹
                        </button>
                        <button onclick="dSlide(1)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full flex items-center justify-center text-white transition-all"
                            style="background: rgba(0,0,0,.5); border: 1px solid rgba(255,255,255,.15); backdrop-filter: blur(10px);">
                            ›
                        </button>
                        <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-2">
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
                        @foreach($albumPhotos as $i => $ap)
                            <button onclick="dGoTo({{ $i }})" id="dt-{{ $i }}"
                                class="flex-shrink-0 w-14 h-14 rounded-xl overflow-hidden border-2 transition-all {{ $i === 0 ? 'border-violet-500' : 'border-transparent opacity-60' }}">
                                <img src="{{ $ap->files->first()->url }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Info Panel --}}
            <div class="flex flex-col"
                style="background: rgba(9,11,20,.7); border: 1px solid rgba(255,255,255,.1); border-radius: 1.5rem; padding: 1.5rem; backdrop-filter: blur(20px);">

                {{-- Uploader --}}
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <img src="{{ $photo->user->avatar_url }}" alt="{{ $photo->user->name }}"
                            class="w-11 h-11 rounded-full object-cover ring-2 ring-violet-500/30">
                        <div>
                            <p class="font-semibold text-sm">{{ $photo->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $photo->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @auth
                        @if(auth()->id() !== $photo->user_id)
                            <button onclick="doFollow({{ $photo->user_id }}, this)"
                                class="px-4 py-1.5 text-xs rounded-full font-medium transition-all {{ auth()->user()->isFollowing($photo->user_id) ? 'text-gray-300' : 'text-white' }}"
                                style="{{ auth()->user()->isFollowing($photo->user_id) ? 'background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.15);' : 'background: rgba(124,58,237,.4); border: 1px solid rgba(124,58,237,.5);' }}">
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
                                class="px-3 py-1 text-xs rounded-full transition-colors text-gray-400 hover:text-violet-400"
                                style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- Stats --}}
                <div class="flex items-center gap-5 py-4 mb-4"
                    style="border-top: 1px solid rgba(255,255,255,.08); border-bottom: 1px solid rgba(255,255,255,.08);">

                    @auth
                        <button onclick="dLike({{ $photo->id }}, this)"
                            class="flex items-center gap-2 text-sm rounded-full px-4 py-2 transition-all {{ $photo->isLikedBy(auth()->id()) ? 'text-red-400' : 'text-gray-400 hover:text-red-400' }}"
                            style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); backdrop-filter: blur(8px);">
                            <span>{{ $photo->isLikedBy(auth()->id()) ? '♥' : '♡' }}</span>
                            <span id="dlc">{{ $photo->likes->count() }}</span>
                        </button>
                    @else
                        <button onclick="openModal('login','like')"
                            class="flex items-center gap-2 text-sm rounded-full px-4 py-2 text-gray-400 hover:text-red-400 transition-all"
                            style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);">
                            ♡ {{ $photo->likes->count() }}
                        </button>
                    @endauth

                    <span class="flex items-center gap-2 text-sm text-gray-400 rounded-full px-4 py-2"
                        style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);">
                        ◯ <span id="dcc">{{ $photo->comments->count() }}</span>
                    </span>

                    <span class="flex items-center gap-2 text-sm text-gray-600 ml-auto">
                        👁 {{ number_format($photo->views) }}
                    </span>
                </div>

                {{-- Action buttons: HANYA Download (Like sudah di stats) --}}
                <div class="flex gap-2 mb-5">
                    <a href="{{ route('photos.download', $photo) }}"
                        class="flex items-center justify-center gap-2 py-2.5 px-6 text-sm text-white rounded-full transition-all flex-1"
                        style="background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.15); backdrop-filter: blur(8px);">
                        ⬇ Download
                    </a>

                    {{-- Delete (hanya jika pemilik) --}}
                    @auth
                        @if(auth()->id() === $photo->user_id)
                            <div class="relative">
                                <button onclick="togglePhotoMenu(this)"
                                    class="flex items-center justify-center w-11 h-11 rounded-full text-gray-400 hover:text-white transition-all"
                                    style="background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.15); backdrop-filter: blur(8px);">
                                    ⋯
                                </button>
                                <div class="hidden absolute right-0 bottom-full mb-2 rounded-2xl overflow-hidden shadow-2xl z-50"
                                    style="background: rgba(9,11,20,.95); border: 1px solid rgba(255,255,255,.1); backdrop-filter: blur(20px); min-width: 160px;">
                                    <a href="{{ route('upload') }}"
                                        class="flex items-center gap-3 px-4 py-3 text-sm text-gray-300 hover:text-white hover:bg-white/5 transition-colors">
                                        ✏ Edit
                                    </a>
                                    <form method="POST" action="{{ route('photos.destroy', $photo) }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus foto ini secara permanen?')"
                                            class="flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-red-900/20 transition-colors w-full text-left">
                                            🗑 Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endauth
                </div>

                {{-- Comments --}}
                <div id="comments" class="overflow-y-auto max-h-64 space-y-3 mb-4 pr-1">
                    @forelse($photo->comments as $c)
                        <div class="flex gap-3">
                            <img src="{{ $c->user->avatar_url }}" class="w-7 h-7 rounded-full flex-shrink-0 object-cover">
                            <div>
                                <span class="text-violet-300 font-medium text-xs">{{ $c->user->name }}</span>
                                <span class="text-gray-300 text-xs ml-1">{{ $c->body }}</span>
                                <p class="text-gray-600 text-xs mt-0.5">{{ $c->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-600 text-xs py-8">Belum ada komentar</p>
                    @endforelse
                </div>

                @auth
                    <div class="flex gap-2">
                        <input type="text" id="dci" placeholder="Tulis komentar..."
                            class="flex-1 px-4 py-2.5 text-sm text-white placeholder-gray-600 rounded-full focus:outline-none transition-all"
                            style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12); backdrop-filter: blur(8px);"
                            onkeydown="if(event.key==='Enter') dComment({{ $photo->id }})">
                        <button onclick="dComment({{ $photo->id }})"
                            class="px-5 py-2.5 text-sm text-white rounded-full transition-all"
                            style="background: rgba(124,58,237,.4); border: 1px solid rgba(124,58,237,.5); backdrop-filter: blur(8px);">
                            Kirim
                        </button>
                    </div>
                @else
                    <button onclick="openModal('login','comment')"
                        class="w-full py-2.5 text-sm text-gray-400 rounded-full transition-all"
                        style="background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.1);">
                        ◯ Login untuk berkomentar
                    </button>
                @endauth
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const _c = document.querySelector('meta[name="csrf-token"]')?.content || '';
        let dIdx = 0, dTot = {{ $total }};

        // Update background foto sesuai slide aktif
        function updatePhotoBg(src) {
            const bg = document.getElementById('bgPhotoImg');
            if (bg) {
                bg.style.opacity = '0';
                setTimeout(() => {
                    bg.src = src;
                    bg.style.opacity = '.22';
                }, 150);
            }
        }

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
                t.classList.toggle('opacity-60', j !== i);
            });

            // Update background
            const imgs = document.querySelectorAll('#ds img');
            if (imgs[i]) updatePhotoBg(imgs[i].src);
        }

        // Keyboard navigation
        document.addEventListener('keydown', e => {
            if (e.key === 'ArrowLeft') dSlide(-1);
            if (e.key === 'ArrowRight') dSlide(1);
        });

        async function dLike(id, btn) {
            const r = await fetch(`/photos/${id}/like`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': _c, 'Content-Type': 'application/json' }
            });
            const d = await r.json();
            btn.querySelector('span').innerHTML = d.liked ? '♥' : '♡';
            document.getElementById('dlc').textContent = d.total;
            btn.className = btn.className.replace(/text-(gray-400|red-400)/g, '');
            btn.classList.add(d.liked ? 'text-red-400' : 'text-gray-400');
        }

        async function dComment(id) {
            const inp = document.getElementById('dci');
            const body = inp.value.trim(); if (!body) return;
            const r = await fetch(`/photos/${id}/comment`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': _c, 'Content-Type': 'application/json' },
                body: JSON.stringify({ body })
            });
            const d = await r.json();
            const el = document.createElement('div');
            el.className = 'flex gap-3';
            el.innerHTML = `
                <div class="w-7 h-7 rounded-full bg-violet-800 flex-shrink-0 flex items-center justify-center text-xs">
                    ${d.comment.user_name[0]}
                </div>
                <div>
                    <span class="text-violet-300 font-medium text-xs">${d.comment.user_name}</span>
                    <span class="text-gray-300 text-xs ml-1">${d.comment.body}</span>
                </div>`;
            const list = document.getElementById('comments');
            list.appendChild(el);
            const cnt = document.getElementById('dcc'); if (cnt) cnt.textContent = d.total;
            inp.value = '';
            list.scrollTop = list.scrollHeight;
        }

        async function doFollow(id, btn) {
            const r = await fetch(`/users/${id}/follow`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': _c, 'Content-Type': 'application/json' }
            });
            const d = await r.json();
            btn.textContent = d.is_following ? '✓ Mengikuti' : '+ Ikuti';
            if (d.is_following) {
                btn.style.background = 'rgba(255,255,255,.08)';
                btn.style.border = '1px solid rgba(255,255,255,.15)';
            } else {
                btn.style.background = 'rgba(124,58,237,.4)';
                btn.style.border = '1px solid rgba(124,58,237,.5)';
            }
        }

        function togglePhotoMenu(btn) {
            const m = btn.nextElementSibling;
            m.classList.toggle('hidden');
            const close = e => {
                if (!btn.contains(e.target) && !m.contains(e.target)) {
                    m.classList.add('hidden');
                    document.removeEventListener('click', close);
                }
            };
            setTimeout(() => document.addEventListener('click', close), 0);
        }
    </script>
@endpush