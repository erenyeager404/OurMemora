@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div>
        <h2 class="text-2xl font-bold mb-8">Semua Foto</h2>

        @if($photos->isEmpty())
            <div class="text-center py-20 text-gray-500">
                <p class="text-xl mb-2">Belum ada foto</p>
                <p class="text-sm">Jadilah yang pertama upload!</p>
            </div>
        @else
            <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
                @foreach($photos as $photo)
                    <div class="break-inside-avoid bg-gray-900 rounded-2xl border border-gray-800">
                        <!-- Gambar -->
                        <a href="{{ route('photos.show', $photo) }}" class="overflow-hidden rounded-t-2xl block">
                            <img src="{{ Storage::url($photo->file_path) }}" alt="{{ $photo->caption }}"
                                class="w-full object-cover">
                        </a>
                        <div class="p-4">
                            <p class="font-semibold text-sm mb-1">{{ $photo->caption }}</p>
                            @if($photo->description)
                                <p class="text-gray-400 text-xs mb-2 line-clamp-2">{{ $photo->description }}</p>
                            @endif
                            <p class="text-gray-500 text-xs mb-3">
                                oleh <span class="text-violet-400">{{ $photo->user->name }}</span>
                            </p>

                            <!-- Baris tombol utama dan dropdown -->
                            <div class="flex items-center gap-3 pt-3 border-t border-gray-800">
                                <!-- Tombol Like -->
                                <button onclick="toggleLike({{ $photo->id }}, this)"
                                    data-liked="{{ $photo->isLikedBy(auth()->id()) ? 'true' : 'false' }}"
                                    class="flex items-center gap-1.5 text-xs transition-colors {{ $photo->isLikedBy(auth()->id()) ? 'text-red-400' : 'text-gray-400 hover:text-red-400' }}">
                                    <svg class="w-4 h-4" fill="{{ $photo->isLikedBy(auth()->id()) ? 'currentColor' : 'none' }}"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span class="like-count">{{ $photo->likes->count() }}</span>
                                </button>

                                <!-- Tombol Save -->
                                <button onclick="toggleSave({{ $photo->id }}, this)"
                                    data-saved="{{ $photo->isSavedBy(auth()->id()) ? 'true' : 'false' }}"
                                    class="flex items-center gap-1.5 text-xs transition-colors {{ $photo->isSavedBy(auth()->id()) ? 'text-violet-400' : 'text-gray-400 hover:text-violet-400' }}">
                                    <svg class="w-4 h-4" fill="{{ $photo->isSavedBy(auth()->id()) ? 'currentColor' : 'none' }}"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                    </svg>
                                    Save
                                </button>

                                <!-- Tombol Comment -->
                                <button onclick="toggleComment({{ $photo->id }})"
                                    class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-blue-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <span>{{ $photo->comments->count() }}</span>
                                </button>

                                <!-- View Count (dikanankan) -->
                                <span class="flex items-center gap-1 text-xs text-gray-500 ml-auto">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ number_format($photo->views) }}
                                </span>

                                <!-- Dropdown tiga titik (ke atas) -->
                                <div class="relative">
                                    <button onclick="toggleDropdown(this)"
                                        class="flex items-center text-gray-400 hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>
                                    <div
                                        class="dropdown-menu hidden absolute right-0 bottom-full mb-2 w-36 bg-gray-800 rounded-lg shadow-lg border border-gray-700 z-50">
                                        <!-- Download -->
                                        <a href="{{ route('photos.download', $photo) }}"
                                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-300 hover:bg-gray-700 hover:text-green-400 rounded-t-lg transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Download
                                        </a>
                                        <!-- Share (modal) -->
                                        <button onclick="showShareModal('{{ route('photos.show', $photo) }}')"
                                            class="flex items-center gap-2 px-4 py-2 text-xs text-gray-300 hover:bg-gray-700 hover:text-violet-400 rounded-b-lg transition-colors w-full">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                            </svg>
                                            Share
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom komentar (toggle) -->
                            <div id="comment-section-{{ $photo->id }}" class="hidden mt-3">
                                <div id="comments-{{ $photo->id }}" class="space-y-2 mb-3 max-h-32 overflow-y-auto">
                                    @foreach($photo->comments as $comment)
                                        <div class="text-xs">
                                            <span class="text-violet-400 font-medium">{{ $comment->user->name }}</span>
                                            <span class="text-gray-300 ml-1">{{ $comment->body }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" id="comment-input-{{ $photo->id }}" placeholder="Tulis komentar..."
                                        class="flex-1 px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-xs text-white placeholder-gray-500 focus:outline-none focus:border-violet-500">
                                    <button onclick="submitComment({{ $photo->id }})"
                                        class="px-3 py-2 bg-violet-600 hover:bg-violet-700 rounded-lg text-xs transition-colors">
                                        Kirim
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal Share Link -->
    <div id="shareModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 hidden transition-all">
        <div class="bg-gray-900 rounded-2xl max-w-md w-full mx-4 border border-gray-700 shadow-xl">
            <div class="flex justify-between items-center p-4 border-b border-gray-800">
                <h3 class="text-lg font-semibold text-white">Bagikan Foto</h3>
                <button onclick="closeShareModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <p class="text-sm text-gray-300 mb-2">Salin link foto ini:</p>
                <div class="flex gap-2">
                    <input type="text" id="shareLinkInput" readonly
                        class="flex-1 px-3 py-2 bg-gray-800 border border-gray-700 rounded-lg text-sm text-white focus:outline-none focus:border-violet-500">
                    <button onclick="copyShareLink()"
                        class="px-4 py-2 bg-violet-600 hover:bg-violet-700 rounded-lg text-sm transition-colors">
                        Salin
                    </button>
                </div>
                <p id="copyFeedback" class="text-xs text-green-400 mt-2 hidden">Link disalin ke clipboard!</p>
            </div>
        </div>
    </div>

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').content;

        // Like
        async function toggleLike(photoId, btn) {
            const res = await fetch(`/photos/${photoId}/like`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json'
                }
            });
            const data = await res.json();
            const svg = btn.querySelector('svg');
            const count = btn.querySelector('.like-count');
            if (data.liked) {
                btn.classList.replace('text-gray-400', 'text-red-400');
                svg.setAttribute('fill', 'currentColor');
            } else {
                btn.classList.replace('text-red-400', 'text-gray-400');
                svg.setAttribute('fill', 'none');
            }
            count.textContent = data.total;
        }

        // Save
        async function toggleSave(photoId, btn) {
            const res = await fetch(`/photos/${photoId}/save`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }
            });
            const data = await res.json();
            const svg = btn.querySelector('svg');
            if (data.saved) {
                btn.classList.replace('text-gray-400', 'text-violet-400');
                svg.setAttribute('fill', 'currentColor');
            } else {
                btn.classList.replace('text-violet-400', 'text-gray-400');
                svg.setAttribute('fill', 'none');
            }
        }

        // Toggle comment section
        function toggleComment(photoId) {
            const section = document.getElementById(`comment-section-${photoId}`);
            section.classList.toggle('hidden');
        }

        // Submit comment
        async function submitComment(photoId) {
            const input = document.getElementById(`comment-input-${photoId}`);
            const body = input.value.trim();
            if (!body) return;
            const res = await fetch(`/photos/${photoId}/comment`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ body })
            });
            const data = await res.json();
            const list = document.getElementById(`comments-${photoId}`);
            const div = document.createElement('div');
            div.className = 'text-xs';
            div.innerHTML = `<span class="text-violet-400 font-medium">${data.comment.user_name}</span>
                                             <span class="text-gray-300 ml-1">${data.comment.body}</span>`;
            list.appendChild(div);
            input.value = '';
        }

        // Dropdown tiga titik
        function toggleDropdown(btn) {
            const menu = btn.nextElementSibling;
            // Tutup dropdown lain yang terbuka
            document.querySelectorAll('.dropdown-menu').forEach(m => {
                if (m !== menu) m.classList.add('hidden');
            });
            menu.classList.toggle('hidden');
            // Tutup jika klik di luar
            const closeHandler = function (e) {
                if (!btn.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.add('hidden');
                    document.removeEventListener('click', closeHandler);
                }
            };
            setTimeout(() => document.addEventListener('click', closeHandler), 0);
        }

        // Share modal
        const shareModal = document.getElementById('shareModal');
        const shareLinkInput = document.getElementById('shareLinkInput');

        function showShareModal(url) {
            shareLinkInput.value = url;
            shareModal.classList.remove('hidden');
            // Tutup semua dropdown yang terbuka
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.add('hidden'));
        }

        function closeShareModal() {
            shareModal.classList.add('hidden');
            const feedback = document.getElementById('copyFeedback');
            if (feedback) feedback.classList.add('hidden');
        }

        function copyShareLink() {
            if (!shareLinkInput.value) return;
            navigator.clipboard.writeText(shareLinkInput.value).then(() => {
                const feedback = document.getElementById('copyFeedback');
                feedback.classList.remove('hidden');
                setTimeout(() => feedback.classList.add('hidden'), 2000);
            }).catch(() => {
                alert('Gagal menyalin link, silakan salin manual.');
            });
        }

        // Tutup modal jika klik backdrop
        shareModal.addEventListener('click', function (e) {
            if (e.target === shareModal) closeShareModal();
        });
    </script>
@endsection