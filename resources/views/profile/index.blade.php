@extends('layouts.app')
@section('title', 'Profile')

@section('content')
    <div>

        {{-- Profile Header --}}
        <div class="card-info mb-6">
            <div class="flex items-start gap-6">
                <div class="profile-avatar-wrap">
                    <img src="{{ $user->avatar_url }}" class="profile-avatar" alt="{{ $user->name }}" id="avatarPreview">
                    <label for="avatarInput" class="profile-avatar-edit" title="Ganti foto profil">
                        ✎
                        <input type="file" id="avatarInput" class="hidden" accept="image/*" onchange="uploadAvatar(this)">
                    </label>
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                    <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                    <p class="text-gray-500 text-xs mt-1">
                        Bergabung {{ $user->created_at->format('d M Y') }}
                    </p>
                    <div class="profile-stats mt-4">
                        <div class="profile-stat">
                            <span class="profile-stat-num">{{ $photos->count() }}</span>
                            <span class="profile-stat-label">Foto</span>
                        </div>
                        <div class="profile-stat">
                            <span class="profile-stat-num">{{ $user->following()->count() }}</span>
                            <span class="profile-stat-label">Following</span>
                        </div>
                        <div class="profile-stat">
                            <span class="profile-stat-num">{{ $user->followers()->count() }}</span>
                            <span class="profile-stat-label">Followers</span>
                        </div>
                    </div>
                </div>
                {{-- Tombol ganti password --}}
                <a href="{{ route('profile.password.page') }}" class="btn-ghost text-sm self-start">
                    🔒 Ganti Password
                </a>
            </div>
        </div>

        {{-- Sort bar --}}
        <div class="sort-bar">
            <span class="text-sm text-gray-400 mr-2">Urutkan:</span>
            <a href="{{ route('profile') }}?sort=newest"
                class="sort-btn {{ request('sort', 'newest') === 'newest' ? 'active' : '' }}">
                Terbaru
            </a>
            <a href="{{ route('profile') }}?sort=oldest"
                class="sort-btn {{ request('sort') === 'oldest' ? 'active' : '' }}">
                Terlama
            </a>
            <a href="{{ route('profile') }}?sort=public"
                class="sort-btn {{ request('sort') === 'public' ? 'active' : '' }}">
                🌍 Public
            </a>
            <a href="{{ route('profile') }}?sort=private"
                class="sort-btn {{ request('sort') === 'private' ? 'active' : '' }}">
                🔒 Private
            </a>
        </div>

        {{-- Photo grid --}}
        <h3 class="font-semibold mb-4 text-gray-300">
            📷 Foto Kamu ({{ $photos->count() }})
        </h3>

        @if($photos->isEmpty())
            <div class="text-center py-20 text-gray-600">
                <p class="text-5xl mb-4">📷</p>
                <p class="text-lg">Belum ada foto.</p>
                <a href="{{ route('upload') }}" class="btn-primary mt-4 inline-flex">Upload sekarang</a>
            </div>
        @else
            <div class="profile-photo-grid">
                @foreach($photos as $photo)
                    @if($photo->files->isNotEmpty())
                        <div class="profile-photo-item group"
                            onclick="openPhotoDetail({{ $photo->id }}, '{{ $photo->files->first()->url }}')">
                            <img src="{{ $photo->files->first()->url }}" alt="{{ $photo->caption }}" class="profile-photo-img">

                            {{-- Status badge --}}
                            <div class="profile-photo-status">
                                @if($photo->status === 'private')
                                    <span class="badge-gray text-xs">🔒</span>
                                @else
                                    <span class="badge-violet text-xs">🌍</span>
                                @endif
                            </div>

                            {{-- Overlay --}}
                            <div class="profile-photo-overlay">
                                <p class="text-xs font-medium truncate flex-1 text-white">
                                    {{ $photo->caption }}
                                </p>
                                <form method="POST" action="{{ route('photos.destroy', $photo) }}" onclick="event.stopPropagation()">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Hapus foto ini?')"
                                        class="w-7 h-7 bg-red-600 hover:bg-red-700 rounded-lg
                                                                               flex items-center justify-center text-xs transition-colors">
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

    {{-- Photo Detail Modal (sama seperti dashboard) --}}
    <div id="photoDetailModal" class="modal-backdrop" onclick="handleModalBg(event)">
        <div class="photo-detail-bg" id="modalBg">
            <img id="modalBgImg" src="" alt="">
            <div class="photo-detail-overlay"></div>
        </div>
        <div class="relative z-10 w-full max-w-5xl mx-4 my-8">
            <div id="modalContent" class="card overflow-hidden"></div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        // Reuse fungsi modal dari dashboard
        async function openPhotoDetail(photoId, bgSrc) {
            const modal = document.getElementById('photoDetailModal');
            const bgImg = document.getElementById('modalBgImg');
            const content = document.getElementById('modalContent');
            bgImg.src = bgSrc;
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
            content.innerHTML = '<div class="flex items-center justify-center py-20 text-gray-400"><span class="text-4xl">◌</span></div>';
            const res = await fetch(`/photo/${photoId}?partial=1`);
            content.innerHTML = await res.text();
        }
        function closePhotoDetail() {
            document.getElementById('photoDetailModal').classList.remove('open');
            document.body.style.overflow = '';
        }
        function handleModalBg(e) {
            if (e.target === document.getElementById('photoDetailModal')) closePhotoDetail();
        }
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closePhotoDetail(); });

        // Upload avatar
        async function uploadAvatar(input) {
            const file = input.files[0]; if (!file) return;
            const fd = new FormData();
            fd.append('avatar', file);
            fd.append('_token', csrf);
            const res = await fetch('{{ route("profile.avatar") }}', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.url) document.getElementById('avatarPreview').src = data.url;
        }
    </script>
@endpush