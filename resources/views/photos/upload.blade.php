@extends('layouts.app')
@section('title', 'Upload')

@section('content')
    <div class="max-w-xl mx-auto">
        <h2 class="text-2xl font-bold mb-8">📷 Upload Kenangan</h2>

        <form method="POST" action="{{ route('upload') }}" enctype="multipart/form-data"
            class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-6 space-y-5">
            @csrf

            {{-- Drop zone --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Foto (maks. 10) *</label>
                <div id="dz"
                    class="w-full min-h-52 border-2 border-dashed border-white/15 rounded-2xl transition-all duration-200 cursor-pointer hover:border-violet-500/60 hover:bg-white/4"
                    onclick="document.getElementById('pi').click()"
                    ondragover="event.preventDefault(); this.classList.add('border-violet-500','bg-violet-900/10')"
                    ondragleave="this.classList.remove('border-violet-500','bg-violet-900/10')" ondrop="onDrop(event)">

                    <div id="dp"
                        class="flex flex-col items-center justify-center h-full py-10 text-gray-500 pointer-events-none">
                        <span class="text-5xl mb-3">📷</span>
                        <p class="text-sm font-medium">Klik atau seret foto ke sini</p>
                        <p class="text-xs mt-1 text-gray-600">JPG, PNG, WebP — maks 5MB per foto</p>
                    </div>

                    <div id="pg" class="hidden grid grid-cols-3 sm:grid-cols-4 gap-3 p-3"></div>
                </div>
                <input type="file" name="photos[]" id="pi" accept="image/*" multiple class="hidden"
                    onchange="handleFiles(this.files)">
                @error('photos') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                @error('photos.*') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Caption --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Caption *</label>
                <input type="text" name="caption" value="{{ old('caption') }}" placeholder="Tulis caption singkat..."
                    class="w-full px-4 py-3 bg-white/8 border border-white/15 rounded-xl text-sm text-white placeholder-gray-500 focus:outline-none focus:border-violet-500 transition-colors">
                @error('caption') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">
                    Deskripsi <span class="text-gray-600">(opsional)</span>
                </label>
                <textarea name="description" rows="3" placeholder="Ceritakan momen ini..."
                    class="w-full px-4 py-3 bg-white/8 border border-white/15 rounded-xl text-sm text-white placeholder-gray-500 focus:outline-none focus:border-violet-500 transition-colors resize-none">{{ old('description') }}</textarea>
            </div>

            {{-- Tags --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">
                    Tags <span class="text-gray-600">(pisah dengan koma)</span>
                </label>
                <input type="text" name="tags" value="{{ old('tags') }}" placeholder="alam, pantai, sunset"
                    class="w-full px-4 py-3 bg-white/8 border border-white/15 rounded-xl text-sm text-white placeholder-gray-500 focus:outline-none focus:border-violet-500 transition-colors">
            </div>

            {{-- Visibilitas --}}
            <div>
                <label class="block text-xs text-gray-400 mb-3 font-medium">Visibilitas</label>
                <div class="flex gap-3">
                    <label id="vPub"
                        class="flex items-start gap-3 cursor-pointer p-3 flex-1 rounded-xl border transition-colors border-violet-500/50 bg-violet-900/20"
                        onclick="setVis('public')">
                        <input type="radio" name="status" value="public" checked class="hidden">
                        <div>
                            <p class="text-sm font-medium">🌍 Public</p>
                            <p class="text-xs text-gray-500">Semua orang bisa lihat</p>
                        </div>
                    </label>
                    <label id="vPrv"
                        class="flex items-start gap-3 cursor-pointer p-3 flex-1 rounded-xl border transition-colors border-white/10 hover:border-white/20"
                        onclick="setVis('private')">
                        <input type="radio" name="status" value="private" class="hidden">
                        <div>
                            <p class="text-sm font-medium">🔒 Private</p>
                            <p class="text-xs text-gray-500">Hanya kamu yang bisa lihat</p>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit"
                class="w-full py-3 bg-violet-600 hover:bg-violet-700 rounded-xl text-sm font-medium text-white transition-colors">
                ⬆ Upload Foto
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let FILES = [];

        function handleFiles(incoming) {
            Array.from(incoming).forEach(f => { if (FILES.length < 10) FILES.push(f); });
            render();
        }

        function onDrop(e) {
            e.preventDefault();
            const dz = document.getElementById('dz');
            dz.classList.remove('border-violet-500', 'bg-violet-900/10');
            handleFiles(e.dataTransfer.files);
        }

        function render() {
            const pg = document.getElementById('pg');
            const dp = document.getElementById('dp');

            if (!FILES.length) {
                pg.classList.add('hidden');
                dp.classList.remove('hidden');
                sync(); return;
            }

            dp.classList.add('hidden');
            pg.classList.remove('hidden');
            pg.innerHTML = '';

            FILES.forEach((file, i) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const div = document.createElement('div');
                    div.className = 'relative rounded-xl overflow-hidden aspect-square';
                    div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                    ${i === 0 ? '<div class="absolute bottom-0 left-0 right-0 bg-violet-600/90 text-white text-xs text-center py-1">Utama</div>' : ''}
                    <button type="button" onclick="removeFile(${i})"
                            class="absolute top-1.5 right-1.5 w-5 h-5 bg-black/70 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs transition-colors">
                        ✕
                    </button>`;
                    pg.insertBefore(div, pg.querySelector('.add-btn'));
                };
                reader.readAsDataURL(file);
            });

            if (FILES.length < 10) {
                const a = document.createElement('div');
                a.className = 'add-btn aspect-square rounded-xl border-2 border-dashed border-white/15 hover:border-violet-500/60 flex items-center justify-center text-2xl text-gray-600 hover:text-violet-400 transition-colors cursor-pointer';
                a.innerHTML = '＋';
                a.onclick = () => document.getElementById('pi').click();
                pg.appendChild(a);
            }

            sync();
        }

        function removeFile(i) { FILES.splice(i, 1); render(); }

        function sync() {
            const dt = new DataTransfer();
            FILES.forEach(f => dt.items.add(f));
            document.getElementById('pi').files = dt.files;
        }

        function setVis(v) {
            document.querySelector(`input[value="${v}"]`).checked = true;
            const pub = document.getElementById('vPub');
            const prv = document.getElementById('vPrv');
            if (v === 'public') {
                pub.className = pub.className.replace('border-white/10 hover:border-white/20', 'border-violet-500/50 bg-violet-900/20');
                prv.className = prv.className.replace('border-violet-500/50 bg-violet-900/20', 'border-white/10 hover:border-white/20');
            } else {
                prv.className = prv.className.replace('border-white/10 hover:border-white/20', 'border-violet-500/50 bg-violet-900/20');
                pub.className = pub.className.replace('border-violet-500/50 bg-violet-900/20', 'border-white/10 hover:border-white/20');
            }
        }
    </script>
@endpush