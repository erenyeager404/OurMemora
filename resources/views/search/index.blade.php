@extends('layouts.app')
@section('title', 'Pencarian')

@section('content')
    <div>
        <form method="GET" action="{{ route('search') }}" class="mb-8">
            <div class="relative">
                <input type="text" name="q" value="{{ $query }}" placeholder="Cari foto, tag, atau user..." autofocus
                    class="w-full px-5 py-4 pr-14 bg-white/8 border border-white/15 rounded-2xl text-white text-base placeholder-gray-500 focus:outline-none focus:border-violet-500 transition-colors">
                <button type="submit"
                    class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-violet-600 hover:bg-violet-700 rounded-xl flex items-center justify-center text-base transition-colors">
                    ⌕
                </button>
            </div>
        </form>

        @if($query)
            @if($tags->isNotEmpty())
                <div class="flex flex-wrap gap-2 mb-6">
                    @foreach($tags as $tag)
                        <a href="{{ route('search') }}?q={{ $tag->name }}"
                            class="px-3 py-1 bg-violet-900/40 border border-violet-800/50 text-violet-300 text-xs rounded-full hover:bg-violet-800/40 transition-colors">
                            #{{ $tag->name }} ({{ $tag->photos_count }})
                        </a>
                    @endforeach
                </div>
            @endif

            <p class="text-gray-400 text-sm mb-6">
                {{ $photos->count() }} hasil untuk
                <span class="text-white font-medium">"{{ $query }}"</span>
            </p>

            @if($photos->isEmpty())
                <div class="text-center py-20 text-gray-600">
                    <p class="text-7xl mb-4">⌕</p>
                    <p class="text-xl">Tidak ada foto ditemukan</p>
                    <p class="text-sm mt-2">Coba kata kunci lain</p>
                </div>
            @else
                <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
                    @foreach($photos as $photo)
                        @if($photo->files->isNotEmpty())
                            <div class="break-inside-avoid bg-white/10 backdrop-blur-md rounded-2xl overflow-hidden border border-white/15 cursor-pointer hover:bg-white/15 transition-colors"
                                onclick="openDetail({{ $photo->id }}, '{{ $photo->files->first()->url }}')">
                                <img src="{{ $photo->files->first()->url }}" alt="{{ $photo->caption }}" class="w-full object-cover">
                                <div class="p-4">
                                    <p class="font-semibold text-sm mb-1 truncate">{{ $photo->caption }}</p>
                                    <div class="flex items-center gap-2 mb-2">
                                        <img src="{{ $photo->user->avatar_url }}" class="w-4 h-4 rounded-full object-cover">
                                        <p class="text-gray-500 text-xs">{{ $photo->user->name }}</p>
                                    </div>
                                    @if($photo->tags->isNotEmpty())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($photo->tags as $tag)
                                                <span class="px-2 py-0.5 bg-gray-800/80 text-gray-500 text-xs rounded-full">
                                                    #{{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        @else
            <div class="text-center py-10">
                <p class="text-gray-500 text-sm mb-8">Cari berdasarkan caption, tag, atau nama user</p>
                @if($popularTags->isNotEmpty())
                    <p class="text-gray-400 text-sm mb-4">Tag Populer</p>
                    <div class="flex flex-wrap gap-2 justify-center">
                        @foreach($popularTags as $tag)
                            <a href="{{ route('search') }}?q={{ $tag->name }}"
                                class="px-4 py-2 bg-white/8 border border-white/15 text-gray-300 text-sm rounded-full hover:border-violet-500/50 hover:text-violet-400 transition-colors">
                                #{{ $tag->name }}
                                <span class="text-gray-600 text-xs ml-1">{{ $tag->photos_count }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>

    {{-- Detail Modal --}}
    <div id="detailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm"
        onclick="if(event.target===this) closeDetail()">
        <div class="fixed inset-0 z-0"><img id="dBgImg" src="" alt="" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/75 backdrop-blur-xl"></div>
        </div>
        <div class="relative z-10 w-full max-w-5xl mx-4 my-6 max-h-[90vh] overflow-y-auto">
            <div id="dContent" class="bg-gray-900/95 backdrop-blur-xl border border-white/10 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-center py-24 text-gray-400 text-3xl">◌</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        async function openDetail(id, bg) {
            const m = document.getElementById('detailModal');
            document.getElementById('dBgImg').src = bg;
            document.getElementById('dContent').innerHTML = '<div class="flex items-center justify-center py-24 text-gray-400 text-3xl">◌</div>';
            m.classList.remove('hidden'); m.classList.add('flex');
            document.body.style.overflow = 'hidden';
            document.getElementById('dContent').innerHTML = await (await fetch(`/photo/${id}?partial=1`)).text();
        }
        function closeDetail() {
            document.getElementById('detailModal').classList.add('hidden');
            document.getElementById('detailModal').classList.remove('flex');
            document.body.style.overflow = '';
        }
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDetail(); });
    </script>
@endpush