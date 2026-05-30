@extends('layouts.app')
@section('title', 'Pencarian')
@section('content')
    <div>
        <form method="GET" action="{{ route('search') }}" class="mb-8">
            <div class="relative">
                <input type="text" name="q" value="{{ $query }}" placeholder="Cari foto, tag, atau user..." autofocus
                    class="w-full px-5 py-4 pr-14 rounded-2xl text-white text-[15px] placeholder-gray-600 focus:outline-none transition-all"
                    style="background:rgba(255,255,255,.055);border:1px solid rgba(255,255,255,.1)">
                <button type="submit"
                    class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-xl flex items-center justify-center text-white transition-all"
                    style="background:rgba(124,58,237,.45);border:1px solid rgba(124,58,237,.5)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>
        </form>

        @if($query)
            @if($tags->isNotEmpty())
                <div class="flex flex-wrap gap-2 mb-5">
                    @foreach($tags as $tag)
                        <a href="{{ route('search') }}?q={{ $tag->name }}"
                            class="px-3 py-1 text-xs rounded-full text-violet-300 transition-colors"
                            style="background:rgba(124,58,237,.15);border:1px solid rgba(124,58,237,.3)">
                            #{{ $tag->name }} · {{ $tag->photos_count }}
                        </a>
                    @endforeach
                </div>
            @endif

            <p class="text-gray-500 text-sm mb-6">
                {{ $photos->count() }} hasil untuk
                <span class="text-white font-medium">"{{ $query }}"</span>
            </p>

            @if($photos->isEmpty())
                <div class="text-center py-20 text-gray-600">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <p class="text-xl text-gray-500">Tidak ada hasil</p>
                    <p class="text-sm mt-1">Coba kata kunci lain</p>
                </div>
            @else
                <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
                    @foreach($photos as $photo)
                        @if($photo->files->isNotEmpty())
                            <a href="{{ route('photos.show', $photo) }}"
                                class="break-inside-avoid rounded-2xl overflow-hidden cursor-pointer group block transition-all"
                                style="background:rgba(255,255,255,.055);border:1px solid rgba(255,255,255,.09)">
                                <div class="overflow-hidden">
                                    <img src="{{ $photo->files->first()->thumb_url }}" alt="{{ $photo->caption }}"
                                        class="w-full object-cover group-hover:scale-[1.03] transition-transform duration-400">
                                </div>
                                <div class="p-4">
                                    <p class="font-semibold text-[13px] mb-1 truncate">{{ $photo->caption }}</p>
                                    <div class="flex items-center gap-2 mb-2">
                                        <img src="{{ $photo->user->avatar_url }}" class="w-4 h-4 rounded-full object-cover">
                                        <span class="text-gray-500 text-xs">{{ $photo->user->name }}</span>
                                    </div>
                                    @if($photo->tags->isNotEmpty())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($photo->tags as $tag)
                                                <span class="px-2 py-0.5 text-[11px] rounded-full text-gray-500"
                                                    style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08)">
                                                    #{{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </a>
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
                                class="px-4 py-2 text-sm text-gray-300 rounded-full hover:text-violet-400 transition-colors"
                                style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                                #{{ $tag->name }}
                                <span class="text-gray-600 text-xs ml-1">{{ $tag->photos_count }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection