@extends('layouts.guest')
@section('title', 'OurMemora — Abadikan Setiap Momen')

@section('content')

    {{-- Hero --}}
    <section class="pt-28 pb-16 px-6 text-center relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none"
            style="background: radial-gradient(ellipse 60% 50% at 50% 0%, rgba(124,58,237,0.18) 0%, transparent 70%)">
        </div>
        <div class="relative z-10">
            <div
                class="inline-flex items-center gap-2 px-4 py-2 mb-6 bg-violet-950/60 border border-violet-800/40 rounded-full text-xs text-violet-300">
                <span class="w-2 h-2 bg-violet-400 rounded-full animate-pulse"></span>
                Abadikan kenangan, bagikan cerita
            </div>
            <h1 class="text-5xl md:text-6xl font-bold mb-5 leading-[1.15] tracking-tight">
                Setiap foto<br>menyimpan <span class="text-violet-400">cerita</span>
            </h1>
            <p class="text-gray-400 text-lg mb-10 max-w-xl mx-auto leading-relaxed">
                OurMemora hadir untuk mengabadikan momen berharga,<br>
                menemukan karya indah, dan terhubung dengan orang-orang terdekat.
            </p>
            @guest
                <div class="flex items-center justify-center gap-4 flex-wrap">
                    <button onclick="openModal('register')"
                        class="px-8 py-3 text-base font-medium text-white bg-violet-600 hover:bg-violet-700 rounded-xl transition-colors">
                        Mulai Mengabadikan
                    </button>
                    <button onclick="openModal('login')"
                        class="px-8 py-3 text-base text-gray-300 hover:text-white border border-white/10 hover:border-white/20 rounded-xl transition-colors">
                        Sudah punya akun
                    </button>
                </div>
            @endguest
        </div>
    </section>

    {{-- Gallery --}}
    <section class="max-w-7xl mx-auto px-6 pb-24">
        <div class="columns-2 md:columns-3 lg:columns-4 gap-4 space-y-4">
            @forelse($photos as $photo)
                @if($photo->files->isNotEmpty())
                    <div class="break-inside-avoid rounded-2xl overflow-hidden group bg-gray-900 border border-gray-800/50">

                        <a href="{{ route('photos.show', $photo) }}" class="block overflow-hidden">
                            <img src="{{ $photo->files->first()->url }}" alt="{{ $photo->caption }}"
                                class="w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        </a>

                        <div class="p-3">
                            <p class="font-medium text-sm truncate mb-1">{{ $photo->caption }}</p>
                            <div class="flex items-center gap-2 mb-2">
                                <img src="{{ $photo->user->avatar_url }}" class="w-4 h-4 rounded-full object-cover">
                                <p class="text-gray-500 text-xs">{{ $photo->user->name }}</p>
                            </div>

                            @if($photo->tags->isNotEmpty())
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach($photo->tags->take(2) as $tag)
                                        <span class="px-2 py-0.5 bg-gray-800/80 text-gray-500 text-xs rounded-full">
                                            #{{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex items-center gap-3 pt-2.5 border-t border-gray-800/80">
                                @auth

                                    <!-- LIKE -->
                                    <button onclick="doLike({{ $photo->id }}, this)"
                                        class="flex items-center gap-1.5 text-xs transition-colors {{ $photo->isLikedBy(auth()->id()) ? 'text-red-400' : 'text-gray-500 hover:text-red-400' }}">

                                        @if($photo->isLikedBy(auth()->id()))
                                            <!-- Filled Heart -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5
                                                                2 5.42 4.42 3 7.5 3
                                                                c1.74 0 3.41.81 4.5 2.09
                                                                C13.09 3.81 14.76 3 16.5 3
                                                                19.58 3 22 5.42 22 8.5
                                                                c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                            </svg>
                                        @else
                                            <!-- Outline Heart -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 stroke-current fill-none"
                                                viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12.1 21.35l-1.1-1.02C5.14 15.24 2 12.39 2 8.85
                                                                    2 5.92 4.42 3.5 7.35 3.5
                                                                    c1.74 0 3.41.81 4.5 2.09
                                                                    1.09-1.28 2.76-2.09 4.5-2.09
                                                                    2.93 0 5.35 2.42 5.35 5.35
                                                                    0 3.54-3.14 6.39-8.9 11.48l-1.1 1.02z" />
                                            </svg>
                                        @endif

                                        <span class="lc">{{ $photo->likes->count() }}</span>
                                    </button>

                                    <!-- SAVE -->
                                    <button onclick="doSave({{ $photo->id }}, this)"
                                        class="flex items-center gap-1 text-xs transition-colors {{ $photo->isSavedBy(auth()->id()) ? 'text-violet-400' : 'text-gray-500 hover:text-violet-400' }}">

                                        @if($photo->isSavedBy(auth()->id()))
                                            <!-- Filled Bookmark -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                                <path d="M6 2c-1.1 0-2 .9-2 2v18l8-4 8 4V4
                                                                c0-1.1-.9-2-2-2H6z" />
                                            </svg>
                                        @else
                                            <!-- Outline Bookmark -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 stroke-current fill-none"
                                                viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 3h12a1 1 0 011 1v17l-7-4-7 4V4a1 1 0 011-1z" />
                                            </svg>
                                        @endif
                                    </button>

                                    <!-- COMMENT -->
                                    <button onclick="doCmt({{ $photo->id }})"
                                        class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-blue-400 transition-colors">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 stroke-current fill-none"
                                            viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5
                                                            M21 12c0 4.418-4.03 8-9 8
                                                            a9.863 9.863 0 01-4-.8L3 20l1.3-3.9
                                                            A7.93 7.93 0 013 12
                                                            c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>

                                        <span class="cc">{{ $photo->comments->count() }}</span>
                                    </button>

                                @else

                                    <!-- GUEST LIKE -->
                                    <button onclick="openModal('login','like')"
                                        class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-red-400 transition-colors">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 stroke-current fill-none"
                                            viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12.1 21.35l-1.1-1.02C5.14 15.24 2 12.39 2 8.85
                                                            2 5.92 4.42 3.5 7.35 3.5
                                                            c1.74 0 3.41.81 4.5 2.09
                                                            1.09-1.28 2.76-2.09 4.5-2.09
                                                            2.93 0 5.35 2.42 5.35 5.35
                                                            0 3.54-3.14 6.39-8.9 11.48l-1.1 1.02z" />
                                        </svg>

                                        <span>{{ $photo->likes->count() }}</span>
                                    </button>

                                    <!-- GUEST SAVE -->
                                    <button onclick="openModal('login','save')"
                                        class="flex items-center gap-1 text-xs text-gray-500 hover:text-violet-400 transition-colors">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 stroke-current fill-none"
                                            viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 3h12a1 1 0 011 1v17l-7-4-7 4V4a1 1 0 011-1z" />
                                        </svg>
                                    </button>

                                    <!-- GUEST COMMENT -->
                                    <button onclick="openModal('login','comment')"
                                        class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-blue-400 transition-colors">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 stroke-current fill-none"
                                            viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h5
                                                            M21 12c0 4.418-4.03 8-9 8
                                                            a9.863 9.863 0 01-4-.8L3 20l1.3-3.9
                                                            A7.93 7.93 0 013 12
                                                            c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>

                                        <span>{{ $photo->comments->count() }}</span>
                                    </button>

                                @endauth

                                <!-- DOWNLOAD -->
                                <a href="{{ route('photos.download', $photo) }}"
                                    class="ml-auto text-gray-500 hover:text-green-400 transition-colors" title="Download">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 stroke-current fill-none"
                                        viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l4-4m-4 4l-4-4
                                                    M5 21h14" />
                                    </svg>

                                </a>
                            </div>

                            @auth
                                <div id="cs-{{ $photo->id }}" class="hidden mt-3">
                                    <div id="cl-{{ $photo->id }}" class="space-y-1.5 mb-2 max-h-28 overflow-y-auto">
                                        @foreach($photo->comments->take(3) as $c)
                                            <div class="text-xs">
                                                <span class="text-violet-300 font-medium">{{ $c->user->name }}</span>
                                                <span class="text-gray-400 ml-1">{{ $c->body }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex gap-2">
                                        <input type="text" id="ci-{{ $photo->id }}" placeholder="Tulis komentar..."
                                            class="flex-1 px-3 py-1.5 bg-gray-800 border border-gray-700 rounded-lg text-xs text-white placeholder-gray-600 focus:outline-none focus:border-violet-500">
                                        <button onclick="sendCmt({{ $photo->id }})"
                                            class="px-3 py-1.5 bg-violet-600 hover:bg-violet-700 rounded-lg text-xs text-white transition-colors">
                                            Kirim
                                        </button>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>
                @endif
            @empty
                <div class="col-span-4 text-center py-24 text-gray-600">
                    <p class="text-7xl mb-4">📷</p>
                    <p class="text-xl">Belum ada kenangan di sini</p>
                </div>
            @endforelse
        </div>
        <div class="mt-10">{{ $photos->links() }}</div>
    </section>

@endsection

@push('scripts')
    <script>
        async function doLike(id, btn) {
            const r = await fetch(`/photos/${id}/like`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' } });
            const d = await r.json();
            btn.querySelector('span').innerHTML = d.liked ? '♥' : '♡';
            btn.querySelector('.lc').textContent = d.total;
            btn.className = btn.className.replace(/text-(gray-500|red-400)/g, '');
            btn.classList.add(d.liked ? 'text-red-400' : 'text-gray-500');
        }
        async function doSave(id, btn) {
            const r = await fetch(`/photos/${id}/save`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' } });
            const d = await r.json();
            btn.querySelector('span').innerHTML = d.saved ? '◈' : '◇';
        }
        function doCmt(id) { document.getElementById(`cs-${id}`).classList.toggle('hidden'); }
        async function sendCmt(id) {
            const inp = document.getElementById(`ci-${id}`);
            const body = inp.value.trim(); if (!body) return;
            const r = await fetch(`/photos/${id}/comment`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }, body: JSON.stringify({ body }) });
            const d = await r.json();
            const el = document.createElement('div'); el.className = 'text-xs';
            el.innerHTML = `<span class="text-violet-300 font-medium">${d.comment.user_name}</span><span class="text-gray-400 ml-1">${d.comment.body}</span>`;
            document.getElementById(`cl-${id}`).appendChild(el);
            inp.value = '';
        }
    </script>
@endpush