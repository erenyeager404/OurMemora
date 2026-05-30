@extends('layouts.app')
@section('title', 'Saved')
@section('content')
    <div>
        <h2 class="text-2xl font-bold mb-7">Foto Tersimpan</h2>
        @if($photos->isEmpty())
            <div class="text-center py-24 text-gray-600">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
                <p class="text-xl text-gray-500 mb-2">Belum ada foto tersimpan</p>
                <p class="text-sm">Klik ikon simpan di dashboard untuk menyimpan foto</p>
            </div>
        @else
            <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
                @foreach($photos as $photo)
                    @if($photo && $photo->files->isNotEmpty())
                        <a href="{{ route('photos.show', $photo) }}"
                            class="break-inside-avoid rounded-2xl overflow-hidden cursor-pointer group block transition-all"
                            style="background:rgba(255,255,255,.055);border:1px solid rgba(255,255,255,.09)">
                            <div class="overflow-hidden">
                                <img src="{{ $photo->files->first()->thumb_url }}" alt="{{ $photo->caption }}"
                                    class="w-full object-cover group-hover:scale-[1.03] transition-transform duration-400">
                            </div>
                            <div class="p-4">
                                <p class="font-semibold text-[13px] mb-1 truncate">{{ $photo->caption }}</p>
                                <div class="flex items-center gap-2">
                                    <img src="{{ $photo->user->avatar_url }}" class="w-4 h-4 rounded-full object-cover">
                                    <span class="text-gray-500 text-xs">{{ $photo->user->name }}</span>
                                </div>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
@endsection