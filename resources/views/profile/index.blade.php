@extends('layouts.app')
@section('title', 'Profile')

@section('content')
    <div>

        {{-- Profile Header --}}
        <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-6 mb-6">
            <div class="flex items-start gap-6">

                {{-- Avatar --}}
                <div class="relative flex-shrink-0">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" id="avImg"
                        class="w-20 h-20 rounded-full object-cover ring-2 ring-violet-500/40">
                    <label for="avInput"
                        class="absolute bottom-0 right-0 w-7 h-7 bg-violet-600 hover:bg-violet-700 rounded-full flex items-center justify-center text-xs cursor-pointer transition-colors"
                        title="Ganti foto profil">
                        ✎
                        <input type="file" id="avInput" class="hidden" accept="image/*" onchange="uploadAv(this)">
                    </label>
                </div>

                <div class="flex-1">
                    <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                    <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                    <p class="text-gray-500 text-xs mt-0.5">Bergabung {{ $user->created_at->format('d M Y') }}</p>

                    <div class="flex gap-6 mt-4">
                        <div class="text-center">
                            <span class="text-xl font-bold block">{{ $photos->count() }}</span>
                            <span class="text-gray-500 text-xs">Foto</span>
                        </div>
                        <div class="text-center">
                            <span class="text-xl font-bold block">{{ $user->following()->count() }}</span>
                            <span class="text-gray-500 text-xs">Following</span>
                        </div>
                        <div class="text-center">
                            <span class="text-xl font-bold block">{{ $user->followers()->count() }}</span>
                            <span class="text-gray-500 text-xs">Followers</span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('profile.password.page') }}"
                    class="flex-shrink-0 px-4 py-2 text-sm text-gray-300 hover:text-white border border-white/10 hover:border-white/20 rounded-xl transition-colors">
                    🔒 Ganti Password
                </a>
            </div>
        </div>

        {{-- Sort bar --}}
        <div class="flex items-center gap-2 mb-6 flex-wrap">
            <span class="text-sm text-gray-400 mr-1">Urutkan:</span>
            @foreach(['newest' => 'Terbaru', 'oldest' => 'Terlama', 'public' => '🌍 Public', 'private' => '🔒 Private'] as $val => $lbl)
                <a href="{{ route('profile') }}?sort={{ $val }}" class="px-3 py-1.5 text-xs rounded-lg border transition-colors
                              {{ request('sort', 'newest') === $val
                ? 'bg-violet-600/30 border-violet-600/50 text-violet-300'
                : 'border-white/10 text-gray-400 hover:text-white hover:border-white/20' }}">
                    {{ $lbl }}
                </a>
            @endforeach
        </div>

        {{-- Photo Grid --}}
        <h3 class="font-semibold mb-4 text-gray-300">📷 Foto Kamu ({{ $photos->count() }})</h3>

        @if($photos->isEmpty())
            <div class="text-center py-20 text-gray-600">
                <p class="text-6xl mb-4">📷</p>
                <p class="text-lg mb-4">Belum ada foto</p>
                <a href="{{ route('upload') }}"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-violet-600 hover:bg-violet-700 rounded-xl text-sm text-white transition-colors">
                    Upload Sekarang
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($photos as $photo)
                    @if($photo->files->isNotEmpty())
                        <div class="relative rounded-xl overflow-hidden aspect-square cursor-pointer group"
                            onclick="openDetail({{ $photo->id }}, '{{ $photo->files->first()->url }}')">

                            <img src="{{ $photo->files->first()->url }}" alt="{{ $photo->caption }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                            {{-- Status badge --}}
                            <div class="absolute top-2 left-2">
                                @if($photo->status === 'private')
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 text-xs bg-gray-800/80 text-gray-300 rounded-full border border-gray-700/50">🔒</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 text-xs bg-violet-900/50 text-violet-300 rounded-full border border-violet-800/50">🌍</span>
                                @endif
                            </div>

                            {{-- Hover overlay --}}
                            <div
                                class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-end justify-between p-3">
                                <p class="text-xs text-white truncate flex-1 font-medium">{{ $photo->caption }}</p>
                                <form method="POST" action="{{ route('photos.destroy', $photo) }}" onclick="event.stopPropagation()">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus foto ini?')"
                                        class="w-7 h-7 bg-red-600 hover:bg-red-700 rounded-lg flex items-center justify-center text-xs transition-colors ml-2">
                                        🗑
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- Photo Detail Modal --}}
    <div id="detailModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur-sm"
        onclick="if(event.target===this) closeDetail()">
        <div class="fixed inset-0 z-0 transition-all duration-500">
            <img id="dBgImg" src="" alt="" class="w-full h-full object-cover">
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
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        async function openDetail(id, bg) {
            const m = document.getElementById('detailModal');
            document.getElementById('dBgImg').src = bg;
            document.getElementById('dContent').innerHTML = '<div class="flex items-center justify-center py-24 text-gray-400 text-3xl">◌</div>';
            m.classList.remove('hidden');
            m.classList.add('flex');
            document.body.style.overflow = 'hidden';
            const res = await fetch(`/photo/${id}?partial=1`);
            document.getElementById('dContent').innerHTML = await res.text();
        }

        function closeDetail() {
            const m = document.getElementById('detailModal');
            m.classList.add('hidden');
            m.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDetail(); });

        async function uploadAv(input) {
            const f = input.files[0]; if (!f) return;
            const fd = new FormData();
            fd.append('avatar', f);
            fd.append('_token', csrf);
            const r = await fetch('{{ route("profile.avatar") }}', { method: 'POST', body: fd });
            const d = await r.json();
            if (d.url) document.getElementById('avImg').src = d.url;
        }
    </script>
@endpush