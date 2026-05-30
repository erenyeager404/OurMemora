@extends(auth()->user()->is_admin ? 'layouts.admin' : 'layouts.app')
@section('title', 'Profile')

@section('content')
    <div>
        {{-- Header --}}
        <div class="rounded-2xl p-6 mb-6" style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09)">
            <div class="flex items-start gap-5">
                <div class="relative flex-shrink-0">
                    <img src="{{ $user->avatar_url }}" id="avImg" alt=""
                        class="w-20 h-20 rounded-full object-cover ring-2 ring-violet-500/30">
                    <label for="avInput" title="Ganti foto profil"
                        class="absolute bottom-0 right-0 w-7 h-7 rounded-full flex items-center justify-center cursor-pointer transition-all"
                        style="background:rgba(124,58,237,.7);border:1px solid rgba(124,58,237,.9)">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        <input type="file" id="avInput" class="hidden" accept="image/*" onchange="uploadAv(this)">
                    </label>
                </div>

                <div class="flex-1 min-w-0">
                    <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                    <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                    <p class="text-gray-600 text-xs mt-0.5">Bergabung {{ $user->created_at->format('d M Y') }}</p>
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
                    class="flex-shrink-0 flex items-center gap-2 px-4 py-2 rounded-xl text-sm text-gray-300 hover:text-white transition-all"
                    style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    Ganti Password
                </a>
            </div>
        </div>

        {{-- Header bar foto + sort via dropdown ⋮ --}}
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-gray-300">
                Foto Kamu
                <span class="text-gray-600 font-normal text-sm ml-1">({{ $photos->count() }})</span>
            </h3>

            {{-- Sort dropdown — titik tiga --}}
            <div class="relative">
                <button onclick="toggleSort(this)"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs text-gray-400 hover:text-white transition-all"
                    style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                            d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                    </svg>
                    @php
                        $sortLabels = ['newest' => 'Terbaru', 'oldest' => 'Terlama', 'public' => 'Public', 'private' => 'Private'];
                    @endphp
                    {{ $sortLabels[request('sort', 'newest')] }}
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div id="sortMenu"
                    class="hidden absolute right-0 top-full mt-1.5 rounded-xl overflow-hidden shadow-2xl z-50"
                    style="background:#0f111a;border:1px solid rgba(255,255,255,.1);min-width:140px">
                    @foreach(['newest' => 'Terbaru', 'oldest' => 'Terlama', 'public' => 'Public saja', 'private' => 'Private saja'] as $val => $lbl)
                        <a href="{{ route('profile') }}?sort={{ $val }}"
                            class="flex items-center justify-between px-4 py-2.5 text-xs transition-colors {{ request('sort', 'newest') === $val ? 'text-violet-400' : 'text-gray-300 hover:bg-white/5' }}">
                            {{ $lbl }}
                            @if(request('sort', 'newest') === $val)
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Grid foto — klik → detail page. Tidak ada tombol delete di sini --}}
        @if($photos->isEmpty())
            <div class="text-center py-20 text-gray-600">
                <svg class="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-lg text-gray-500 mb-4">Belum ada foto</p>
                <a href="{{ route('upload') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm text-white transition-all"
                    style="background:rgba(124,58,237,.4);border:1px solid rgba(124,58,237,.5)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Upload Sekarang
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach($photos as $photo)
                    @if($photo->files->isNotEmpty())
                        <a href="{{ route('photos.show', $photo) }}"
                            class="relative rounded-xl overflow-hidden aspect-square cursor-pointer group block">
                            <img src="{{ $photo->files->first()->thumb_url }}" alt="{{ $photo->caption }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                            <div class="absolute top-2 left-2">
                                @if($photo->status === 'private')
                                    <span class="flex items-center px-2 py-0.5 rounded-full text-[10px]"
                                        style="background:rgba(9,11,20,.75);border:1px solid rgba(255,255,255,.15)">
                                        <svg class="w-2.5 h-2.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Private
                                    </span>
                                @endif
                            </div>

                            <div class="absolute inset-0 flex items-end p-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200"
                                style="background:linear-gradient(to top,rgba(9,11,20,.8) 0%,transparent 60%)">
                                <div class="w-full">
                                    <p class="text-xs text-white font-medium truncate">{{ $photo->caption }}</p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[11px] text-gray-300 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                            {{ $photo->likes->count() }}
                                        </span>
                                        <span class="text-[11px] text-gray-300 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                            {{ $photo->comments->count() }}
                                        </span>
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

        function toggleSort(btn) {
            const m = document.getElementById('sortMenu');
            m.classList.toggle('hidden');
            const close = e => {
                if (!btn.contains(e.target) && !m.contains(e.target)) {
                    m.classList.add('hidden');
                    document.removeEventListener('click', close);
                }
            };
            setTimeout(() => document.addEventListener('click', close), 0);
        }

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