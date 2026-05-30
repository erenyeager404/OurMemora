@extends('layouts.admin')

@section('content')
    <div>
        <div class="flex items-center justify-between mb-7">
            <div>
                <h2 class="text-2xl font-bold">Dashboard Admin</h2>
                <p class="text-gray-500 text-sm mt-0.5">Kelola semua foto di platform</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.events.create') }}"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-white transition-all"
                    style="background:rgba(220,38,38,.4);border:1px solid rgba(220,38,38,.5)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Event
                </a>
                <span class="px-3 py-1.5 rounded-xl text-xs font-medium text-red-400"
                    style="background:rgba(220,38,38,.12);border:1px solid rgba(220,38,38,.25)">
                    Administrator
                </span>
            </div>
        </div>

        @if($photos->isEmpty())
            <div class="text-center py-24 text-gray-600">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-gray-500 text-lg">Belum ada foto publik</p>
            </div>
        @else
            <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
                @foreach($photos as $photo)
                    @if($photo->files->isNotEmpty())
                        <div class="break-inside-avoid rounded-2xl overflow-hidden"
                            style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09)">

                            {{-- Foto --}}
                            <a href="{{ route('photos.show', $photo) }}" class="block overflow-hidden">
                                <img src="{{ $photo->files->first()->thumb_url }}" alt="{{ $photo->caption }}"
                                    class="w-full object-cover hover:scale-[1.02] transition-transform duration-300">
                            </a>

                            <div class="p-4">
                                <p class="font-semibold text-[13px] mb-1 truncate">{{ $photo->caption }}</p>

                                {{-- Uploader --}}
                                <div class="flex items-center gap-2 mb-3">
                                    <img src="{{ $photo->user->avatar_url }}" class="w-5 h-5 rounded-full object-cover flex-shrink-0">
                                    <span class="text-gray-500 text-xs truncate">{{ $photo->user->name }}</span>
                                    <span class="text-gray-600 text-xs ml-auto">
                                        {{ $photo->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                {{-- Stats + Hapus --}}
                                <div class="flex items-center gap-3 pt-3" style="border-top:1px solid rgba(255,255,255,.07)">

                                    {{-- Like --}}
                                    <span class="flex items-center gap-1 text-xs text-gray-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        {{ $photo->likes->count() }}
                                    </span>

                                    {{-- Comment --}}
                                    <span class="flex items-center gap-1 text-xs text-gray-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        {{ $photo->comments->count() }}
                                    </span>

                                    {{-- Views --}}
                                    <span class="flex items-center gap-1 text-xs text-gray-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{ number_format($photo->views) }}
                                    </span>

                                    {{-- Hapus --}}
                                    <form method="POST" action="{{ route('admin.photos.destroy', $photo) }}" class="ml-auto">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Hapus foto ini dari platform?')"
                                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs text-red-400 transition-all hover:text-red-300"
                                            style="background:rgba(220,38,38,.12);border:1px solid rgba(220,38,38,.25)">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-8">{{ $photos->links() }}</div>
        @endif
    </div>
@endsection