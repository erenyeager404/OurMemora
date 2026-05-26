@if(!request('partial'))
@extends(auth()->check() ? 'layouts.app' : 'layouts.guest')
@section('title', $photo->caption)
@section('content')
@endif

@php
    $albumPhotos = $photo->album_id
        ? \App\Models\Photo::where('album_id', $photo->album_id)->with('files')->orderBy('id')->get()
        : collect([$photo]);
    $total = $albumPhotos->count();
@endphp

<div class="photo-detail-wrap {{ request('partial') ? '' : 'pt-6' }}">

    @if(!request('partial'))
        <a href="{{ url()->previous() }}"
            class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-sm mb-6 transition-colors">
            ← Kembali
        </a>
    @else
        <div class="flex justify-end p-4 pb-0">
            <button onclick="closePhotoDetail()" class="modal-close">✕</button>
        </div>
    @endif

    <div class="photo-detail-grid p-4 pt-0">

        {{-- Foto + Slider --}}
        <div>
            <div class="photo-main-img">
                <div class="flex transition-transform duration-300" id="detailSlider"
                    style="width: {{ $total * 100 }}%">
                    @foreach($albumPhotos as $ap)
                        @foreach($ap->files as $file)
                            <div style="width: {{ 100 / $total }}%">
                                <img src="{{ $file->url }}" alt="{{ $ap->caption }}" class="w-full object-cover"
                                    onload="{{ request('partial') ? 'updateModalBg(this.src)' : '' }}">
                            </div>
                        @endforeach
                    @endforeach
                </div>
                @if($total > 1)
                    <button onclick="dSlide(-1)" class="slider-btn slider-btn-prev">‹</button>
                    <button onclick="dSlide(1)" class="slider-btn slider-btn-next">›</button>
                    <div class="slider-dots">
                        @for($i = 0; $i < $total; $i++)
                            <div class="slider-dot {{ $i === 0 ? 'active' : '' }}" id="ddot-{{ $i }}"></div>
                        @endfor
                    </div>
                @endif
            </div>

            {{-- Thumbnails --}}
            @if($total > 1)
                <div class="thumbnail-strip mt-3">
                    @foreach($albumPhotos as $i => $ap)
                        <button onclick="dGoTo({{ $i }})" id="dthumb-{{ $i }}" class="thumbnail-item {{ $i === 0 ? 'active' : '' }}">
                            <img src="{{ $ap->files->first()->url }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Info panel --}}
        <div class="photo-info-panel">

            {{-- Uploader --}}
            <div class="uploader-row">
                <div class="flex items-center gap-3">
                    <img src="{{ $photo->user->avatar_url }}" class="uploader-avatar">
                    <div>
                        <p class="font-semibold text-sm">{{ $photo->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $photo->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @auth
                    @if(auth()->id() !== $photo->user_id)
                        <button onclick="toggleFollow({{ $photo->user_id }}, this)"
                            class="{{ auth()->user()->isFollowing($photo->user_id) ? 'btn-ghost text-xs' : 'btn-primary text-xs' }}">
                            {{ auth()->user()->isFollowing($photo->user_id) ? '✓ Mengikuti' : '+ Ikuti' }}
                        </button>
                    @endif
                @endauth
            </div>

            <h1 class="photo-caption">{{ $photo->caption }}</h1>
            @if($photo->description)
                <p class="photo-desc">{{ $photo->description }}</p>
            @endif

            {{-- Tags --}}
            @if($photo->tags->isNotEmpty())
                <div class="flex flex-wrap gap-1 mb-4">
                    @foreach($photo->tags as $tag)
                        <a href="{{ route('search') }}?q={{ $tag->name }}" class="tag-chip">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Stats --}}
            <div class="photo-stats-row">
                @auth
                    <button onclick="dToggleLike({{ $photo->id }}, this)"
                        class="stat-item-btn {{ $photo->isLikedBy(auth()->id()) ? 'text-red-400' : 'text-gray-400 hover:text-red-400' }}">
                        <span>{{ $photo->isLikedBy(auth()->id()) ? '♥' : '♡' }}</span>
                        <span id="dLikeCount">{{ $photo->likes->count() }}</span>
                    </button>
                @else
                    <button onclick="openAuthModal('login','like')" class="stat-item-btn text-gray-400 hover:text-red-400">
                        <span>♡</span> {{ $photo->likes->count() }}
                    </button>
                @endauth

                <span class="stat-item-btn text-gray-400">
                    ◯ <span id="dCommentCount">{{ $photo->comments->count() }}</span>
                </span>

                <span class="stat-item-btn text-gray-500 ml-auto">
                    👁 {{ number_format($photo->views) }}
                </span>
            </div>

            {{-- Action buttons --}}
            <div class="photo-actions-row">
                <a href="{{ route('photos.download', $photo) }}" class="photo-action-btn">
                    ⬇ Download
                </a>
                <button onclick="dCopyLink('{{ route('photos.show', $photo) }}')" class="photo-action-btn"
                    id="dCopyBtn">
                    🔗 Salin Link
                </button>
                <a href="https://wa.me/?text={{ urlencode($photo->caption . ' ' . route('photos.show', $photo)) }}"
                    target="_blank" class="photo-action-btn">
                    📱 WA
                </a>
            </div>

            {{-- Komentar --}}
            <div class="comments-list" id="dComments">
                @forelse($photo->comments as $c)
                    <div class="comment-detail-item">
                        <img src="{{ $c->user->avatar_url }}" class="comment-detail-avatar">
                        <div>
                            <span class="text-violet-300 font-medium text-xs">{{ $c->user->name }}</span>
                            <span class="text-gray-300 text-xs ml-1">{{ $c->body }}</span>
                            <p class="text-gray-600 text-xs mt-0.5">{{ $c->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-600 text-xs py-6">Belum ada komentar</p>
                @endforelse
            </div>

            {{-- Input komentar --}}
            @auth
                <div class="comment-detail-input-wrap">
                    <input type="text" id="dCommentInput" placeholder="Tulis komentar..." class="form-input flex-1 py-2">
                    <button onclick="dSubmitComment({{ $photo->id }})" class="btn-primary px-4">
                        Kirim
                    </button>
                </div>
            @else
                <button onclick="openAuthModal('login','comment')" class="btn-ghost w-full mt-2 text-sm">
                    ◯ Login untuk berkomentar
                </button>
            @endauth

        </div>
    </div>
</div>

<script>
    const csrf2 = document.querySelector('meta[name="csrf-token"]')?.content || '';
    let dIdx = 0;
    const dTotal = {{ $total }};

    function dSlide(dir) {
        dIdx = (dIdx + dir + dTotal) % dTotal;
        dGoTo(dIdx);
    }
    function dGoTo(i) {
        dIdx = i;
        const s = document.getElementById('detailSlider');
        if (s) s.style.transform = `translateX(-${i * (100 / dTotal)}%)`;
        document.querySelectorAll('[id^="ddot-"]').forEach((d, j) => d.classList.toggle('active', j === i));
        document.querySelectorAll('[id^="dthumb-"]').forEach((t, j) => t.classList.toggle('active', j === i));

        // Update background foto saat slide
        @if(request('partial'))
            const imgs = document.querySelectorAll('#detailSlider img');
            if (imgs[i]) updateModalBg(imgs[i].src);
        @endif
}

    function updateModalBg(src) {
        const bg = document.getElementById('modalBgImg');
        if (bg) bg.src = src;
    }

    async function dToggleLike(id, btn) {
        const res = await fetch(`/photos/${id}/like`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf2, 'Content-Type': 'application/json' } });
        const data = await res.json();
        btn.querySelector('span').innerHTML = data.liked ? '♥' : '♡';
        document.getElementById('dLikeCount').textContent = data.total;
        btn.classList.toggle('text-red-400', data.liked);
        btn.classList.toggle('text-gray-400', !data.liked);
    }

    async function dSubmitComment(id) {
        const input = document.getElementById('dCommentInput');
        const body = input.value.trim(); if (!body) return;
        const res = await fetch(`/photos/${id}/comment`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf2, 'Content-Type': 'application/json' }, body: JSON.stringify({ body }) });
        const data = await res.json();
        const list = document.getElementById('dComments');
        const div = document.createElement('div'); div.className = 'comment-detail-item';
        div.innerHTML = `<div class="w-7 h-7 rounded-full bg-violet-800 flex-shrink-0 flex items-center justify-center text-xs">${data.comment.user_name.charAt(0)}</div><div><span class="text-violet-300 font-medium text-xs">${data.comment.user_name}</span><span class="text-gray-300 text-xs ml-1">${data.comment.body}</span></div>`;
        list.appendChild(div);
        const cnt = document.getElementById('dCommentCount');
        if (cnt) cnt.textContent = data.total;
        input.value = '';
        list.scrollTop = list.scrollHeight;
    }

    async function toggleFollow(id, btn) {
        const res = await fetch(`/users/${id}/follow`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf2, 'Content-Type': 'application/json' } });
        const data = await res.json();
        if (data.is_following) { btn.textContent = '✓ Mengikuti'; btn.className = 'btn-ghost text-xs'; }
        else { btn.textContent = '+ Ikuti'; btn.className = 'btn-primary text-xs'; }
    }

    function dCopyLink(url) {
        navigator.clipboard.writeText(url).then(() => {
            const btn = document.getElementById('dCopyBtn');
            btn.textContent = '✓ Disalin!';
            setTimeout(() => btn.textContent = '🔗 Salin Link', 2000);
        });
    }
</script>

@if(!request('partial'))
@endsection
@endif