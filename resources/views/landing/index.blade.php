@extends('layouts.guest')
@section('title', 'OurMemora — Abadikan Setiap Momen')

@section('content')

    {{-- Hero --}}
    <section class="hero-section">
        <div class="hero-blob"></div>
        <div class="relative z-10">
            <div class="hero-badge">
                <span class="w-2 h-2 bg-violet-400 rounded-full animate-pulse"></span>
                Platform kenangan modern
            </div>
            <h1 class="hero-title">
                Setiap foto<br>
                menyimpan <span class="text-violet-400">cerita</span>
            </h1>
            <p class="hero-subtitle">
                OurMemora hadir untuk mengabadikan momen berharga,<br>
                berbagi karya, dan terhubung dengan orang-orang terdekat.
            </p>
            @guest
                <div class="hero-cta-group">
                    <button onclick="openAuthModal('register')" class="btn-primary px-8 py-3 text-base">
                        Mulai Mengabadikan
                    </button>
                    <button onclick="openAuthModal('login')" class="btn-ghost px-8 py-3 text-base">
                        Sudah punya akun
                    </button>
                </div>
            @endguest
        </div>
    </section>

    {{-- Gallery --}}
    <section class="max-w-7xl mx-auto px-6 pb-24">
        @guest
            <div class="guest-info-bar">
                <span>&#8681;</span>
                Download gratis tanpa login &nbsp;&middot;&nbsp; Like, Simpan &amp; Komentar perlu akun
            </div>
        @endguest

        <div class="gallery-grid">
            @forelse($photos as $photo)
                @if($photo->files->isNotEmpty())
                    <div class="card-photo-landing group">
                        <a href="{{ route('photos.show', $photo) }}" class="block overflow-hidden">
                            <img src="{{ $photo->files->first()->url }}" alt="{{ $photo->caption }}"
                                class="w-full object-cover transition-transform duration-500 group-hover:scale-105">
                        </a>
                        <div class="photo-overlay">
                            <div>
                                <p class="font-semibold text-sm">{{ $photo->caption }}</p>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <img src="{{ $photo->user->avatar_url }}" alt="{{ $photo->user->name }}"
                                        class="w-5 h-5 rounded-full">
                                    <p class="text-gray-300 text-xs">{{ $photo->user->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 mt-3">
                                @auth
                                    <button onclick="toggleLike({{ $photo->id }}, this)"
                                        class="action-btn-like {{ $photo->isLikedBy(auth()->id()) ? 'liked' : '' }}">
                                        <span>{{ $photo->isLikedBy(auth()->id()) ? '♥' : '♡' }}</span>
                                        <span class="like-count">{{ $photo->likes->count() }}</span>
                                    </button>
                                @else
                                    <button onclick="openAuthModal('login','like')" class="action-btn-like">
                                        <span>♡</span>
                                        <span>{{ $photo->likes->count() }}</span>
                                    </button>
                                @endauth
                                <a href="{{ route('photos.download', $photo) }}"
                                    class="ml-auto text-sm text-gray-300 hover:text-green-400 transition-colors" title="Download">
                                    &#8681;
                                </a>
                            </div>
                        </div>

                        {{-- Info bawah --}}
                        <div class="p-3">
                            <p class="font-medium text-sm truncate mb-0.5">{{ $photo->caption }}</p>
                            <div class="flex items-center gap-2">
                                <img src="{{ $photo->user->avatar_url }}" alt="{{ $photo->user->name }}"
                                    class="w-4 h-4 rounded-full">
                                <p class="text-gray-500 text-xs">{{ $photo->user->name }}</p>
                            </div>

                            {{-- Actions --}}
                            <div class="action-bar">
                                @auth
                                    <button onclick="toggleLike({{ $photo->id }}, this)"
                                        class="action-btn-like {{ $photo->isLikedBy(auth()->id()) ? 'liked' : '' }}">
                                        <span>{{ $photo->isLikedBy(auth()->id()) ? '♥' : '♡' }}</span>
                                        <span class="like-count">{{ $photo->likes->count() }}</span>
                                    </button>
                                    <button onclick="toggleSave({{ $photo->id }}, this)"
                                        class="action-btn-save {{ $photo->isSavedBy(auth()->id()) ? 'saved' : '' }}">
                                        <span>{{ $photo->isSavedBy(auth()->id()) ? '◈' : '◇' }}</span>
                                    </button>
                                    <button onclick="toggleComment({{ $photo->id }})" class="action-btn-comment">
                                        <span>◯</span>
                                        <span class="comment-count">{{ $photo->comments->count() }}</span>
                                    </button>
                                @else
                                    <button onclick="openAuthModal('login','like')" class="action-btn-like">
                                        <span>♡</span> {{ $photo->likes->count() }}
                                    </button>
                                    <button onclick="openAuthModal('login','save')" class="action-btn-save">
                                        <span>◇</span>
                                    </button>
                                    <button onclick="openAuthModal('login','comment')" class="action-btn-comment">
                                        <span>◯</span> {{ $photo->comments->count() }}
                                    </button>
                                @endauth
                                <a href="{{ route('photos.download', $photo) }}" class="ml-auto action-btn hover:text-green-400"
                                    title="Download">
                                    &#8681;
                                </a>
                            </div>

                            @auth
                                <div id="comment-section-{{ $photo->id }}" class="comment-box hidden">
                                    <div id="comments-{{ $photo->id }}" class="space-y-1.5 max-h-28 overflow-y-auto mb-2">
                                        @foreach($photo->comments->take(3) as $c)
                                            <div class="comment-item">
                                                <span class="comment-name">{{ $c->user->name }}</span>
                                                <span class="text-gray-400 ml-1">{{ $c->body }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="comment-input-wrap">
                                        <input type="text" id="comment-input-{{ $photo->id }}" placeholder="Tulis komentar..."
                                            class="comment-input">
                                        <button onclick="submitComment({{ $photo->id }})"
                                            class="btn-primary px-3 py-1.5 text-xs">Kirim</button>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>
                @endif
            @empty
                <div class="col-span-4 text-center py-24 text-gray-600">
                    <p class="text-6xl mb-4">📷</p>
                    <p class="text-xl mb-2">Belum ada kenangan di sini</p>
                    <p class="text-sm">Jadilah yang pertama mengabadikan momen!</p>
                </div>
            @endforelse
        </div>
        <div class="mt-10">{{ $photos->links() }}</div>
    </section>

@endsection

@push('scripts')
    <script>
        async function toggleLike(id, btn) {
            const res = await fetch(`/photos/${id}/like`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' } });
            const data = await res.json();
            btn.querySelectorAll('.like-count, span:first-child').forEach((el, i) => {
                if (i === 0) el.innerHTML = data.liked ? '♥' : '♡';
                else el.textContent = data.total;
            });
            btn.classList.toggle('liked', data.liked);
        }
        async function toggleSave(id, btn) {
            const res = await fetch(`/photos/${id}/save`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' } });
            const data = await res.json();
            btn.querySelector('span').innerHTML = data.saved ? '◈' : '◇';
            btn.classList.toggle('saved', data.saved);
        }
        function toggleComment(id) {
            document.getElementById(`comment-section-${id}`).classList.toggle('hidden');
        }
        async function submitComment(id) {
            const input = document.getElementById(`comment-input-${id}`);
            const body = input.value.trim(); if (!body) return;
            const res = await fetch(`/photos/${id}/comment`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }, body: JSON.stringify({ body }) });
            const data = await res.json();
            const list = document.getElementById(`comments-${id}`);
            const div = document.createElement('div'); div.className = 'comment-item';
            div.innerHTML = `<span class="comment-name">${data.comment.user_name}</span><span class="text-gray-400 ml-1">${data.comment.body}</span>`;
            list.appendChild(div); input.value = '';
        }
    </script>
@endpush