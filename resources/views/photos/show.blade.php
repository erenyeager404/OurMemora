@php
    $isPartial = request()->boolean('partial');
    $albumPhotos = $photo->album_id
        ? \App\Models\Photo::where('album_id', $photo->album_id)->with('files')->orderBy('id')->get()
        : collect([$photo]);
    $total = $albumPhotos->count();
@endphp

@if(!$isPartial)
    @extends(auth()->check() ? 'layouts.app' : 'layouts.guest')
    @section('title', $photo->caption)
    @section('content')
@endif

<div class="{{ $isPartial ? 'p-1' : 'max-w-5xl mx-auto pt-4' }}">

    @if($isPartial)
        <div class="flex justify-end p-4 pb-2">
            <button onclick="closeDetail()"
                    class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                ✕
            </button>
        </div>
    @else
        <a href="{{ url()->previous() }}"
           class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-sm mb-6 transition-colors">
            ← Kembali
        </a>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start p-4 pt-0">

        {{-- Foto --}}
        <div>
            <div class="rounded-2xl overflow-hidden border border-white/10 relative">
                <div class="flex transition-transform duration-300" id="ds"
                     style="width: {{ $total * 100 }}%">
                    @foreach($albumPhotos as $ap)
                        @foreach($ap->files as $f)
                            <div style="width: {{ 100 / $total }}%">
                                <img src="{{ $f->url }}" alt="{{ $ap->caption }}"
                                     class="w-full object-cover"
                                     @if($isPartial) onload="updateBg(this.src)" @endif>
                            </div>
                        @endforeach
                    @endforeach
                </div>

                @if($total > 1)
                    <button onclick="dSlide(-1)"
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-9 h-9 bg-black/60 hover:bg-black/80 text-white rounded-full flex items-center justify-center transition-colors">
                        ‹
                    </button>
                    <button onclick="dSlide(1)"
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-9 h-9 bg-black/60 hover:bg-black/80 text-white rounded-full flex items-center justify-center transition-colors">
                        ›
                    </button>
                    <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5">
                        @for($i = 0; $i < $total; $i++)
                            <div id="dd-{{ $i }}"
                                 class="rounded-full transition-all duration-200 {{ $i === 0 ? 'w-3 h-1.5 bg-white' : 'w-1.5 h-1.5 bg-white/50' }}">
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
                                class="flex-shrink-0 w-14 h-14 rounded-lg overflow-hidden border-2 transition-all {{ $i === 0 ? 'border-violet-500' : 'border-transparent' }}">
                            <img src="{{ $ap->files->first()->url }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="flex flex-col">

            {{-- Uploader --}}
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <img src="{{ $photo->user->avatar_url }}" alt="{{ $photo->user->name }}"
                         class="w-10 h-10 rounded-full object-cover ring-2 ring-violet-500/30">
                    <div>
                        <p class="font-semibold text-sm">{{ $photo->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $photo->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @auth
                    @if(auth()->id() !== $photo->user_id)
                        <button onclick="doFollow({{ $photo->user_id }}, this)"
                                class="{{ auth()->user()->isFollowing($photo->user_id) ? 'px-4 py-1.5 text-xs rounded-full font-medium border border-white/10 text-gray-300 hover:text-white transition-colors' : 'px-4 py-1.5 text-xs rounded-full font-medium bg-violet-600 hover:bg-violet-700 text-white transition-colors' }}">
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
                <div class="flex flex-wrap gap-1 mb-4">
                    @foreach($photo->tags as $tag)
                        <a href="{{ route('search') }}?q={{ $tag->name }}"
                           class="px-2.5 py-0.5 bg-gray-800/80 text-gray-400 text-xs rounded-full hover:text-violet-400 transition-colors">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Stats --}}
            <div class="flex items-center gap-6 py-4 border-y border-white/10 mb-4">
                @auth
                    <button onclick="dLike({{ $photo->id }}, this)"
                            class="flex items-center gap-2 text-sm transition-colors {{ $photo->isLikedBy(auth()->id()) ? 'text-red-400' : 'text-gray-400 hover:text-red-400' }}">
                        <span>{{ $photo->isLikedBy(auth()->id()) ? '♥' : '♡' }}</span>
                        <span id="dlc">{{ $photo->likes->count() }}</span>
                    </button>
                @else
                    <button onclick="openModal('login','like')" class="flex items-center gap-2 text-sm text-gray-400 hover:text-red-400 transition-colors">
                        ♡ {{ $photo->likes->count() }}
                    </button>
                @endauth

                <span class="flex items-center gap-2 text-sm text-gray-400">
                    ◯ <span id="dcc">{{ $photo->comments->count() }}</span>
                </span>

                <span class="flex items-center gap-2 text-sm text-gray-500 ml-auto">
                    👁 {{ number_format($photo->views) }}
                </span>
            </div>

            {{-- Action buttons --}}
            <div class="flex gap-2 mb-5">
                <a href="{{ route('photos.download', $photo) }}"
                   class="flex-1 flex items-center justify-center gap-2 py-2.5 text-sm bg-white/8 hover:bg-white/15 border border-white/12 rounded-xl text-gray-300 transition-colors">
                    ⬇ Download
                </a>
                <button onclick="dCopy('{{ route('photos.show', $photo) }}')"
                        id="dCopyBtn"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 text-sm bg-white/8 hover:bg-white/15 border border-white/12 rounded-xl text-gray-300 transition-colors">
                    🔗 Salin Link
                </button>
                <a href="https://wa.me/?text={{ urlencode($photo->caption . ' ' . route('photos.show', $photo)) }}"
                   target="_blank"
                   class="flex items-center justify-center gap-2 px-4 py-2.5 text-sm bg-green-800 hover:bg-green-700 rounded-xl text-white transition-colors">
                    📱
                </a>
            </div>

            {{-- Comments --}}
            <div class="overflow-y-auto max-h-64 space-y-3 mb-4 pr-1" id="dcl">
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
                <div class="flex gap-2 mt-auto">
                    <input type="text" id="dci" placeholder="Tulis komentar..."
                           class="flex-1 px-4 py-2.5 bg-gray-800/80 border border-gray-700 rounded-xl text-sm text-white placeholder-gray-500 focus:outline-none focus:border-violet-500 transition-colors">
                    <button onclick="dComment({{ $photo->id }})"
                            class="px-4 py-2.5 bg-violet-600 hover:bg-violet-700 rounded-xl text-sm text-white transition-colors">
                        Kirim
                    </button>
                </div>
            @else
                <button onclick="openModal('login','comment')"
                        class="w-full py-2.5 border border-white/10 hover:border-white/20 rounded-xl text-sm text-gray-400 hover:text-white transition-colors mt-2">
                    ◯ Login untuk berkomentar
                </button>
            @endauth
        </div>
    </div>
</div>

<script>
const _c = document.querySelector('meta[name="csrf-token"]')?.content || '';
let dIdx = 0, dTot = {{ $total }};

function dSlide(dir) { dIdx = (dIdx + dir + dTot) % dTot; dGoTo(dIdx); }

function dGoTo(i) {
    dIdx = i;
    const s = document.getElementById('ds');
    if (s) s.style.transform = `translateX(-${i * (100 / dTot)}%)`;
    document.querySelectorAll('[id^="dd-"]').forEach((d, j) => {
        d.className = j === i
            ? 'rounded-full bg-white w-3 h-1.5 transition-all duration-200'
            : 'rounded-full bg-white/50 w-1.5 h-1.5 transition-all duration-200';
    });
    document.querySelectorAll('[id^="dt-"]').forEach((t, j) => {
        t.classList.toggle('border-violet-500', j === i);
        t.classList.toggle('border-transparent', j !== i);
    });
    const imgs = document.querySelectorAll('#ds img');
    if (imgs[i]) updateBg(imgs[i].src);
}

function updateBg(src) {
    const bg = document.getElementById('dBgImg');
    if (bg) bg.src = src;
}

async function dLike(id, btn) {
    const r = await fetch(`/photos/${id}/like`, { method:'POST', headers:{'X-CSRF-TOKEN':_c,'Content-Type':'application/json'} });
    const d = await r.json();
    btn.querySelector('span').innerHTML = d.liked ? '♥' : '♡';
    document.getElementById('dlc').textContent = d.total;
    btn.className = btn.className.replace(/text-(gray-400|red-400)/g, '');
    btn.classList.add(d.liked ? 'text-red-400' : 'text-gray-400');
}

async function dComment(id) {
    const inp = document.getElementById('dci');
    const body = inp.value.trim(); if (!body) return;
    const r = await fetch(`/photos/${id}/comment`, { method:'POST', headers:{'X-CSRF-TOKEN':_c,'Content-Type':'application/json'}, body:JSON.stringify({body}) });
    const d = await r.json();
    const el = document.createElement('div'); el.className = 'flex gap-3';
    el.innerHTML = `<div class="w-7 h-7 rounded-full bg-violet-800 flex-shrink-0 flex items-center justify-center text-xs">${d.comment.user_name[0]}</div><div><span class="text-violet-300 font-medium text-xs">${d.comment.user_name}</span><span class="text-gray-300 text-xs ml-1">${d.comment.body}</span></div>`;
    const list = document.getElementById('dcl');
    list.appendChild(el);
    const cnt = document.getElementById('dcc'); if (cnt) cnt.textContent = d.total;
    inp.value = ''; list.scrollTop = list.scrollHeight;
}

async function doFollow(id, btn) {
    const r = await fetch(`/users/${id}/follow`, { method:'POST', headers:{'X-CSRF-TOKEN':_c,'Content-Type':'application/json'} });
    const d = await r.json();
    btn.textContent = d.is_following ? '✓ Mengikuti' : '+ Ikuti';
    if (d.is_following) {
        btn.className = 'px-4 py-1.5 text-xs rounded-full font-medium border border-white/10 text-gray-300 hover:text-white transition-colors';
    } else {
        btn.className = 'px-4 py-1.5 text-xs rounded-full font-medium bg-violet-600 hover:bg-violet-700 text-white transition-colors';
    }
}

function dCopy(url) {
    navigator.clipboard.writeText(url).then(() => {
        const btn = document.getElementById('dCopyBtn');
        btn.textContent = '✓ Disalin!';
        setTimeout(() => btn.innerHTML = '🔗 Salin Link', 2000);
    });
}
</script>

@if(!$isPartial)
    @endsection
@endif