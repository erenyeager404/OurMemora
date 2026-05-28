@extends('layouts.admin')

@section('content')

    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-bold">⊞ Dashboard Admin</h2>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.events.create') }}"
                class="flex items-center gap-2 px-4 py-2 rounded-full text-sm transition-all text-white"
                style="background: rgba(220,38,38,.4); border: 1px solid rgba(220,38,38,.5);">
                ＋ Buat Event
            </a>
            <span class="inline-flex items-center px-3 py-1 text-xs rounded-full text-red-400"
                style="background: rgba(220,38,38,.15); border: 1px solid rgba(220,38,38,.3);">
                Administrator
            </span>
        </div>
    </div>

    <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
        @forelse($photos as $photo)
            @if($photo->files->isNotEmpty())
                <div class="break-inside-avoid bg-gray-800/80 rounded-2xl overflow-hidden border border-gray-700/50">
                    <img src="{{ $photo->files->first()->url }}" alt="{{ $photo->caption }}" class="w-full object-cover">
                    <div class="p-4">
                        <p class="font-semibold text-sm mb-1 truncate">{{ $photo->caption }}</p>
                        <div class="flex items-center gap-2 mb-3">
                            <img src="{{ $photo->user->avatar_url }}" class="w-4 h-4 rounded-full object-cover">
                            <p class="text-gray-500 text-xs">{{ $photo->user->name }}</p>
                        </div>
                        <div class="flex items-center gap-3 pt-3 border-t border-gray-700/50">
                            <span class="text-xs text-gray-400">♡ {{ $photo->likes->count() }}</span>
                            <span class="text-xs text-gray-400">◯ {{ $photo->comments->count() }}</span>
                            <span class="text-xs text-gray-500">👁 {{ number_format($photo->views) }}</span>
                            <form method="POST" action="{{ route('admin.photos.destroy', $photo) }}" class="ml-auto">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus foto ini dari platform?')"
                                    class="flex items-center gap-1.5 px-3 py-1.5 bg-red-900/40 hover:bg-red-900/70 text-red-400 text-xs rounded-lg transition-colors">
                                    🗑 Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @empty
            <div class="col-span-4 text-center py-20 text-gray-600">
                <p class="text-5xl mb-4">📷</p>
                <p>Belum ada foto publik</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">{{ $photos->links() }}</div>
    </div>
@endsection