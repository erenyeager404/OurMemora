@extends('layouts.guest')
@section('title', 'OurMemora — Abadikan Setiap Momen')
@section('content')

    <section class="pt-28 pb-14 px-6 text-center relative overflow-hidden">
        <div class="absolute inset-0 pointer-events-none"
            style="background:radial-gradient(ellipse 60% 50% at 50% 0%,rgba(124,58,237,.16) 0%,transparent 70%)"></div>
        <div class="relative z-10">
            <span class="inline-flex items-center gap-2 px-4 py-1.5 mb-6 rounded-full text-xs text-violet-300"
                style="background:rgba(124,58,237,.12);border:1px solid rgba(124,58,237,.25)">
                <span class="w-1.5 h-1.5 bg-violet-400 rounded-full animate-pulse"></span>
                Abadikan kenangan, bagikan cerita
            </span>
            <h1 class="text-5xl md:text-6xl font-bold mb-5 leading-[1.12] tracking-tight">
                Setiap foto<br>menyimpan <span class="text-violet-400">cerita</span>
            </h1>
            <p class="text-gray-400 text-lg mb-10 max-w-lg mx-auto leading-relaxed">
                OurMemora hadir untuk mengabadikan momen berharga, menemukan karya indah, dan terhubung dengan komunitas.
            </p>
            @guest
                <div class="flex items-center justify-center gap-3 flex-wrap">
                    <button onclick="openModal('register')"
                        class="px-8 py-3 text-[15px] font-medium text-white rounded-xl transition-all"
                        style="background:rgba(124,58,237,.5);border:1px solid rgba(124,58,237,.6)">
                        Mulai Mengabadikan
                    </button>
                    <button onclick="openModal('login')"
                        class="px-8 py-3 text-[15px] text-gray-300 hover:text-white rounded-xl transition-all"
                        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                        Sudah punya akun
                    </button>
                </div>
            @endguest
        </div>
    </section>

    <section class="max-w-7xl mx-auto px-6 pb-24">
        @guest
            <p class="text-center text-xs text-gray-600 mb-8">
                Download gratis tanpa login &nbsp;·&nbsp; Like, Simpan &amp; Komentar butuh akun
            </p>
        @endguest

        <div class="columns-2 md:columns-3 lg:columns-4 gap-4 space-y-4">
            @forelse($photos as $photo)
                @if($photo->files->isNotEmpty())
                    <div class="break-inside-avoid rounded-2xl overflow-hidden group cursor-pointer"
                        style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08)"
                        onclick="window.location.href='{{ route('photos.show', $photo) }}'">

                        <div class="overflow-hidden">
                            <img data-src="{{ $photo->files->first()->thumb_url }}"
                                src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 3'%3E%3C/svg%3E"
                                alt="{{ $photo->caption }}"
                                class="lazy w-full object-cover group-hover:scale-[1.03] transition-transform duration-500">
                        </div>

                        <div class="p-3">
                            <p class="font-medium text-sm truncate mb-0.5">{{ $photo->caption }}</p>
                            <div class="flex items-center gap-2 mb-2">
                                <img data-src="{{ $photo->user->avatar_url }}"
                                    src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E"
                                    class="lazy w-4 h-4 rounded-full object-cover">
                                <span class="text-gray-500 text-xs">{{ $photo->user->name }}</span>
                            </div>

                            <div class="flex items-center gap-2 pt-2.5" style="border-top:1px solid rgba(255,255,255,.06)">
                                @auth
                                    <button onclick="event.stopPropagation(); doLike({{ $photo->id }}, this)"
                                        class="flex items-center gap-1 text-xs transition-colors {{ $photo->isLikedBy(auth()->id()) ? 'text-red-400' : 'text-gray-500 hover:text-red-400' }}">
                                        <svg class="w-3.5 h-3.5" fill="{{ $photo->isLikedBy(auth()->id()) ? 'currentColor' : 'none' }}"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        <span class="lc">{{ $photo->likes->count() }}</span>
                                    </button>
                                @else
                                    <button onclick="event.stopPropagation(); openModal('login','like')"
                                        class="flex items-center gap-1 text-xs text-gray-500 hover:text-red-400 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        {{ $photo->likes->count() }}
                                    </button>
                                @endauth

                                <a href="{{ route('photos.download', $photo) }}" onclick="event.stopPropagation()"
                                    class="ml-auto text-gray-500 hover:text-green-400 transition-colors" title="Download">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="col-span-4 text-center py-24 text-gray-600">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-lg">Belum ada kenangan di sini</p>
                </div>
            @endforelse
        </div>
        <div class="mt-10">{{ $photos->links() }}</div>
    </section>
@endsection

@push('scripts')
    <script>
        // Lazy load
        const lazyImgs = document.querySelectorAll('img.lazy');
        const obs = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    const img = e.target;
                    img.src = img.dataset.src;
                    img.addEventListener('load', () => img.classList.add('loaded'), { once: true });
                    obs.unobserve(img);
                }
            });
        }, { rootMargin: '200px' });
        lazyImgs.forEach(img => obs.observe(img));

        async function doLike(id, btn) {
            const r = await fetch(`/photos/${id}/like`, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' } });
            const d = await r.json();
            const svg = btn.querySelector('svg');
            btn.querySelector('.lc').textContent = d.total;
            svg.setAttribute('fill', d.liked ? 'currentColor' : 'none');
            btn.className = btn.className.replace(/text-(gray-500|red-400)/g, '');
            btn.classList.add(d.liked ? 'text-red-400' : 'text-gray-500');
        }
    </script>
@endpush