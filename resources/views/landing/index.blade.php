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
                                    <button onclick="doLike({{ $photo->id }}, this)"
                                        class="flex items-center gap-1.5 text-xs transition-colors {{ $photo->isLikedBy(auth()->id()) ? 'text-red-400' : 'text-gray-500 hover:text-red-400' }}">
                                        <span>{{ $photo->isLikedBy(auth()->id()) ? '♥' : '♡' }}</span>
                                        <span class="lc">{{ $photo->likes->count() }}</span>
                                    </button>
                                    <button onclick="doSave({{ $photo->id }}, this)"
                                        class="flex items-center gap-1 text-xs transition-colors {{ $photo->isSavedBy(auth()->id()) ? 'text-violet-400' : 'text-gray-500 hover:text-violet-400' }}">
                                        <span>{{ $photo->isSavedBy(auth()->id()) ? '◈' : '◇' }}</span>
                                    </button>
                                    <button onclick="doCmt({{ $photo->id }})"
                                        class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-blue-400 transition-colors">
                                        <span>◯</span>
                                        <span class="cc">{{ $photo->comments->count() }}</span>
                                    </button>
                                @else
                                    <button onclick="openModal('login','like')"
                                        class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-red-400 transition-colors">
                                        <span>♡</span> {{ $photo->likes->count() }}
                                    </button>
                                    <button onclick="openModal('login','save')"
                                        class="flex items-center gap-1 text-xs text-gray-500 hover:text-violet-400 transition-colors">
                                        <span>◇</span>
                                    </button>
                                    <button onclick="openModal('login','comment')"
                                        class="flex items-center gap-1.5 text-xs text-gray-500 hover:text-blue-400 transition-colors">
                                        <span>◯</span> {{ $photo->comments->count() }}
                                    </button>
                                @endauth

                                <a href="{{ route('photos.download', $photo) }}"
                                    class="ml-auto text-gray-500 hover:text-green-400 transition-colors text-sm" title="Download">
                                    ⬇
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