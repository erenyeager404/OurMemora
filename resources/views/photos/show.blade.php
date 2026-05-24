@extends('layouts.app')
@section('content')
    <div class="max-w-5xl mx-auto">

        {{-- Back button --}}
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center gap-2 text-gray-400 hover:text-white
                  text-sm mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- FOTO --}}
            <div>
                @php
                    $albumPhotos = $photo->album_id
                        ? \App\Models\Photo::where('album_id', $photo->album_id)
                            ->orderBy('id')->get()
                        : collect([$photo]);
                @endphp

                <div class="rounded-2xl overflow-hidden border border-gray-800 relative">
                    <div class="slide-wrapper flex transition-transform duration-300"
                         id="detail-slider"
                         style="width: {{ $albumPhotos->count() * 100 }}%">
                        @foreach($albumPhotos as $ap)
                            <div style="width: {{ 100 / $albumPhotos->count() }}%">
                                <img src="{{ Storage::url($ap->file_path) }}"
                                     alt="{{ $ap->caption }}"
                                     class="w-full object-cover">
                            </div>
                        @endforeach
                    </div>

                    @if($albumPhotos->count() > 1)
                        <button onclick="detailSlide(-1)"
                                class="absolute left-3 top-1/2 -translate-y-1/2
                                       bg-black/60 hover:bg-black/80 text-white
                                       rounded-full p-2 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button onclick="detailSlide(1)"
                                class="absolute right-3 top-1/2 -translate-y-1/2
                                       bg-black/60 hover:bg-black/80 text-white
                                       rounded-full p-2 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5">
                            @foreach($albumPhotos as $i => $__)
                                <div class="detail-dot w-2 h-2 rounded-full
                                            {{ $i === 0 ? 'bg-white' : 'bg-white/40' }}
                                            transition-colors"></div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Thumbnail strip --}}
                @if($albumPhotos->count() > 1)
                    <div class="flex gap-2 mt-3 overflow-x-auto pb-2">
                        @foreach($albumPhotos as $i => $ap)
                            <button onclick="detailGoTo({{ $i }})"
                                    class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden
                                           border-2 transition-colors
                                           {{ $i === 0 ? 'border-violet-500' : 'border-transparent' }}"
                                    id="thumb-{{ $i }}">
                                <img src="{{ Storage::url($ap->file_path) }}"
                                     class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- INFO & COMMENTS --}}
            <div class="flex flex-col">

                {{-- User info --}}
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-violet-600 rounded-full
                                    flex items-center justify-center font-bold">
                            {{ strtoupper(substr($photo->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-sm">{{ $photo->user->name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $photo->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    {{-- Follow button --}}
                    @if(auth()->id() !== $photo->user_id)
                                    <button onclick="toggleFollow({{ $photo->user_id }}, this)"
                                            class="px-4 py-1.5 text-xs rounded-full font-medium transition-colors
                                                   {{ auth()->user()->isFollowing($photo->user_id)
                        ? 'bg-gray-700 text-gray-300 hover:bg-red-900/30 hover:text-red-400'
                        : 'bg-violet-600 hover:bg-violet-700 text-white' }}">
                                        {{ auth()->user()->isFollowing($photo->user_id) ? 'Mengikuti' : 'Ikuti' }}
                                    </button>
                    @endif
                </div>

                {{-- Caption & deskripsi --}}
                <h1 class="text-xl font-bold mb-2">{{ $photo->caption }}</h1>
                @if($photo->description)
                    <p class="text-gray-400 text-sm mb-4 leading-relaxed">
                        {{ $photo->description }}
                    </p>
                @endif

                {{-- Tags --}}
                @if($photo->tags->isNotEmpty())
                    <div class="flex flex-wrap gap-1 mb-4">
                        @foreach($photo->tags as $tag)
                            <a href="{{ route('search') }}?q={{ $tag->name }}"
                               class="px-2 py-0.5 bg-gray-800 text-gray-400 text-xs
                                      rounded-full hover:text-violet-400 transition-colors">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- Stats --}}
                <div class="flex items-center gap-5 py-4 border-y border-gray-800 mb-4">
                    <button onclick="toggleLikeDetail({{ $photo->id }}, this)"
                            class="flex items-center gap-2 transition-colors
                                   {{ $photo->isLikedBy(auth()->id()) ? 'text-red-400' : 'text-gray-400 hover:text-red-400' }}">
                        <svg class="w-5 h-5" fill="{{ $photo->isLikedBy(auth()->id()) ? 'currentColor' : 'none' }}"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682
                                     a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318
                                     a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span id="detail-like-count">{{ $photo->likes->count() }}</span>
                    </button>

                    <span class="flex items-center gap-2 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8
                                     a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72
                                     C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span id="detail-comment-count">{{ $photo->comments->count() }}</span>
                    </span>

                    <span class="flex items-center gap-2 text-gray-500 text-sm ml-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943
                                     9.542 7-1.274 4.057-5.064 7-9.542 7
                                     -4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        {{ number_format($photo->views) }} views
                    </span>
                </div>

                {{-- Comments --}}
                <div class="flex-1 overflow-y-auto max-h-64 space-y-3 mb-4"
                     id="detail-comments">
                    @forelse($photo->comments as $comment)
                        <div class="flex gap-2 text-sm">
                            <div class="w-7 h-7 bg-violet-800 rounded-full flex-shrink-0
                                        flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <span class="text-violet-400 font-medium text-xs">
                                    {{ $comment->user->name }}
                                </span>
                                <span class="text-gray-300 text-xs ml-1">{{ $comment->body }}</span>
                                <p class="text-gray-600 text-xs mt-0.5">
                                    {{ $comment->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-600 text-xs text-center py-4">
                            Belum ada komentar
                        </p>
                    @endforelse
                </div>

                {{-- Input komentar --}}
                <div class="flex gap-2">
                    <input type="text" id="detail-comment-input"
                           placeholder="Tulis komentar..."
                           class="flex-1 px-4 py-2.5 bg-gray-800 border border-gray-700
                                  rounded-xl text-sm text-white placeholder-gray-500
                                  focus:outline-none focus:border-violet-500 transition-colors">
                    <button onclick="submitDetailComment({{ $photo->id }})"
                            class="px-4 py-2.5 bg-violet-600 hover:bg-violet-700
                                   rounded-xl text-sm transition-colors font-medium">
                        Kirim
                    </button>
                </div>

            </div>
        </div>
    </div>

    <script>
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    const totalSlides = {{ $albumPhotos->count() }};
    let detailIndex = 0;

    function detailSlide(dir) {
        detailIndex = (detailIndex + dir + totalSlides) % totalSlides;
        detailGoTo(detailIndex);
    }

    function detailGoTo(index) {
        detailIndex = index;
        const wrapper = document.getElementById('detail-slider');
        wrapper.style.transform = `translateX(-${index * (100 / totalSlides)}%)`;

        document.querySelectorAll('.detail-dot').forEach((dot, i) => {
            dot.classList.toggle('bg-white', i === index);
            dot.classList.toggle('bg-white/40', i !== index);
        });

        document.querySelectorAll('[id^="thumb-"]').forEach((thumb, i) => {
            thumb.classList.toggle('border-violet-500', i === index);
            thumb.classList.toggle('border-transparent', i !== index);
        });
    }

    async function toggleLikeDetail(photoId, btn) {
        const res = await fetch(`/photos/${photoId}/like`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }
        });
        const data = await res.json();
        const svg = btn.querySelector('svg');
        document.getElementById('detail-like-count').textContent = data.total;
        if (data.liked) {
            btn.classList.replace('text-gray-400', 'text-red-400');
            svg.setAttribute('fill', 'currentColor');
        } else {
            btn.classList.replace('text-red-400', 'text-gray-400');
            svg.setAttribute('fill', 'none');
        }
    }

    async function submitDetailComment(photoId) {
        const input = document.getElementById('detail-comment-input');
        const body = input.value.trim();
        if (!body) return;

        const res = await fetch(`/photos/${photoId}/comment`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
            body: JSON.stringify({ body })
        });
        const data = await res.json();

        const list = document.getElementById('detail-comments');
        const div = document.createElement('div');
        div.className = 'flex gap-2 text-sm';
        div.innerHTML = `
            <div class="w-7 h-7 bg-violet-800 rounded-full flex-shrink-0
                        flex items-center justify-center text-xs font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div>
                <span class="text-violet-400 font-medium text-xs">${data.comment.user_name}</span>
                <span class="text-gray-300 text-xs ml-1">${data.comment.body}</span>
                <p class="text-gray-600 text-xs mt-0.5">${data.comment.created_at}</p>
            </div>
        `;
        list.appendChild(div);

        const countEl = document.getElementById('detail-comment-count');
        if (countEl) countEl.textContent = parseInt(countEl.textContent) + 1;

        input.value = '';
        list.scrollTop = list.scrollHeight;
    }

    async function toggleFollow(userId, btn) {
        const res = await fetch(`/users/${userId}/follow`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }
        });
        const data = await res.json();
        if (data.is_following) {
            btn.textContent = 'Mengikuti';
            btn.className = btn.className
                .replace('bg-violet-600 hover:bg-violet-700 text-white',
                         'bg-gray-700 text-gray-300 hover:bg-red-900/30 hover:text-red-400');
        } else {
            btn.textContent = 'Ikuti';
            btn.className = btn.className
                .replace('bg-gray-700 text-gray-300 hover:bg-red-900/30 hover:text-red-400',
                         'bg-violet-600 hover:bg-violet-700 text-white');
        }
    }

    // Reverb real-time untuk halaman detail
    window.Echo.channel('photo.{{ $photo->id }}')
        .listen('.photo.liked', (data) => {
            document.getElementById('detail-like-count').textContent = data.total_likes;
        })
        .listen('.photo.commented', (data) => {
            const list = document.getElementById('detail-comments');
            const div = document.createElement('div');
            div.className = 'flex gap-2 text-sm';
            div.innerHTML = `
                <div class="w-7 h-7 bg-violet-800 rounded-full flex-shrink-0
                            flex items-center justify-center text-xs font-bold">
                    ${data.comment.user_name.charAt(0).toUpperCase()}
                </div>
                <div>
                    <span class="text-violet-400 font-medium text-xs">${data.comment.user_name}</span>
                    <span class="text-gray-300 text-xs ml-1">${data.comment.body}</span>
                    <p class="text-gray-600 text-xs mt-0.5">${data.comment.created_at}</p>
                </div>
            `;
            list.appendChild(div);
            list.scrollTop = list.scrollHeight;
            const countEl = document.getElementById('detail-comment-count');
            if (countEl) countEl.textContent = parseInt(countEl.textContent) + 1;
        });
    </script>
@endsection