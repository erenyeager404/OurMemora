@extends('layouts.app')
@section('title', 'Profile')

@section('content')
    <div>

        {{-- Profile Header --}}
        <div class="rounded-2xl p-6 mb-6"
            style="background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1); backdrop-filter: blur(12px);">
            <div class="flex items-start gap-6">
                <div class="relative flex-shrink-0">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" id="avImg"
                        class="w-20 h-20 rounded-full object-cover ring-2 ring-violet-500/40">
                    <label for="avInput"
                        class="absolute bottom-0 right-0 w-7 h-7 rounded-full flex items-center justify-center text-xs cursor-pointer transition-all"
                        style="background: rgba(124,58,237,.7); border: 1px solid rgba(124,58,237,.9);"
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
                    class="flex-shrink-0 px-4 py-2 text-sm text-gray-300 hover:text-white rounded-full transition-all"
                    style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);">
                    🔒 Ganti Password
                </a>
            </div>
        </div>

        {{-- Sort bar --}}
        <div class="flex items-center gap-2 mb-6 flex-wrap">
            <span class="text-sm text-gray-500 mr-1">Urutkan:</span>
            @foreach(['newest' => 'Terbaru', 'oldest' => 'Terlama', 'public' => '🌍 Public', 'private' => '🔒 Private'] as $val => $lbl)
                <a href="{{ route('profile') }}?sort={{ $val }}"
                    class="px-3 py-1.5 text-xs rounded-full transition-all {{ request('sort', 'newest') === $val ? 'text-violet-300' : 'text-gray-400 hover:text-white' }}"
                    style="{{ request('sort', 'newest') === $val
                ? 'background: rgba(124,58,237,.2); border: 1px solid rgba(124,58,237,.4);'
                : 'background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1);' }}">
                    {{ $lbl }}
                </a>
            @endforeach
        </div>

        {{-- Photo Grid — TIDAK ada tombol delete di sini --}}
        <h3 class="font-semibold mb-4 text-gray-300">📷 Foto Kamu ({{ $photos->count() }})</h3>

        @if($photos->isEmpty())
            <div class="text-center py-20 text-gray-600">
                <p class="text-6xl mb-4">📷</p>
                <p class="text-lg mb-4">Belum ada foto</p>
                <a href="{{ route('upload') }}"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-sm text-white transition-all"
                    style="background: rgba(124,58,237,.4); border: 1px solid rgba(124,58,237,.5);">
                    Upload Sekarang
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($photos as $photo)
                    @if($photo->files->isNotEmpty())
                        {{-- Klik → redirect ke Photo Detail page --}}
                        <a href="{{ route('photos.show', $photo) }}"
                            class="relative rounded-xl overflow-hidden aspect-square cursor-pointer group block">

                            <img src="{{ $photo->files->first()->url }}" alt="{{ $photo->caption }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                            {{-- Status badge --}}
                            <div class="absolute top-2 left-2">
                                @if($photo->status === 'private')
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full"
                                        style="background: rgba(17,24,39,.8); border: 1px solid rgba(255,255,255,.15);">🔒</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs rounded-full"
                                        style="background: rgba(109,40,217,.5); border: 1px solid rgba(124,58,237,.4);">🌍</span>
                                @endif
                            </div>

                            {{-- Hover overlay: lihat detail --}}
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                style="background: rgba(9,11,20,.5);">
                                <div class="text-center">
                                    <p class="text-white text-xs font-medium px-3 text-center truncate">{{ $photo->caption }}</p>
                                    <div class="flex items-center justify-center gap-3 mt-2 text-xs text-gray-300">
                                        <span>♥ {{ $photo->likes->count() }}</span>
                                        <span>◯ {{ $photo->comments->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

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