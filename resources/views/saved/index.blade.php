@extends('layouts.app')
@section('title', 'Saved')

@section('content')
    <div>
        <h2 class="text-2xl font-bold mb-8">◈ Foto Tersimpan</h2>

        @if($photos->isEmpty())
            <div class="text-center py-24 text-gray-500">
                <p class="text-7xl mb-4">◇</p>
                <p class="text-xl mb-2">Belum ada foto tersimpan</p>
                <p class="text-sm text-gray-600">Klik ◇ di dashboard untuk menyimpan foto</p>
            </div>
        @else
            <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
                @foreach($photos as $photo)
                    @if($photo && $photo->files->isNotEmpty())
                        <div class="break-inside-avoid bg-white/10 backdrop-blur-md rounded-2xl overflow-hidden border border-white/15 cursor-pointer hover:bg-white/15 transition-colors"
                            onclick="openDetail({{ $photo->id }}, '{{ $photo->files->first()->url }}')">
                            <img src="{{ $photo->files->first()->url }}" alt="{{ $photo->caption }}" class="w-full object-cover">
                            <div class="p-4">
                                <p class="font-semibold text-sm mb-1 truncate">{{ $photo->caption }}</p>
                                <div class="flex items-center gap-2">
                                    <img src="{{ $photo->user->avatar_url }}" class="w-4 h-4 rounded-full object-cover">
                                    <p class="text-gray-400 text-xs">{{ $photo->user->name }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
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