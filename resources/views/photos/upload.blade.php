@extends('layouts.app')
@section('title', 'Upload')

@section('content')
    <div class="upload-wrap">
        <h2 class="text-2xl font-bold mb-8">📷 Upload Kenangan</h2>

        <form method="POST" action="{{ route('upload') }}" enctype="multipart/form-data" class="upload-card"
            id="uploadForm">
            @csrf

            {{-- Drop zone --}}
            <div>
                <label class="form-label">Foto (maks. 10) *</label>
                <div class="dropzone" id="dropzone" onclick="document.getElementById('photoInput').click()"
                    ondragover="event.preventDefault(); this.classList.add('drag-over')"
                    ondragleave="this.classList.remove('drag-over')" ondrop="handleDrop(event)">

                    {{-- Placeholder --}}
                    <div class="dropzone-placeholder" id="dropPlaceholder">
                        <span class="dropzone-placeholder-icon">📷</span>
                        <p class="text-sm font-medium">Klik atau seret foto ke sini</p>
                        <p class="text-xs mt-1">JPG, PNG, WebP — maks 5MB per foto</p>
                    </div>

                    {{-- Preview grid --}}
                    <div class="preview-grid hidden" id="previewGrid"></div>
                </div>
                <input type="file" name="photos[]" id="photoInput" accept="image/*" multiple class="hidden"
                    onchange="handleFiles(this.files)">
                @error('photos') <p class="form-error">{{ $message }}</p> @enderror
                @error('photos.*') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Caption --}}
            <div>
                <label class="form-label">Caption *</label>
                <input type="text" name="caption" value="{{ old('caption') }}" placeholder="Tulis caption singkat..."
                    class="form-input-glass">
                @error('caption') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="form-label">Deskripsi <span class="text-gray-600">(opsional)</span></label>
                <textarea name="description" rows="3" placeholder="Ceritakan momen di balik foto ini..."
                    class="form-textarea">{{ old('description') }}</textarea>
            </div>

            {{-- Tags --}}
            <div>
                <label class="form-label">Tags <span class="text-gray-600">(pisah dengan koma)</span></label>
                <input type="text" name="tags" value="{{ old('tags') }}" placeholder="alam, pantai, sunset"
                    class="form-input-glass">
            </div>

            {{-- Visibilitas --}}
            <div>
                <label class="form-label">Visibilitas</label>
                <div class="flex gap-3">
                    <label class="visibility-option selected flex-1" id="opt-public" onclick="selectVisibility('public')">
                        <input type="radio" name="status" value="public" checked class="hidden">
                        <div>
                            <p class="text-sm font-medium">🌍 Public</p>
                            <p class="text-xs text-gray-500">Semua orang bisa melihat</p>
                        </div>
                    </label>
                    <label class="visibility-option flex-1" id="opt-private" onclick="selectVisibility('private')">
                        <input type="radio" name="status" value="private" class="hidden">
                        <div>
                            <p class="text-sm font-medium">🔒 Private</p>
                            <p class="text-xs text-gray-500">Hanya kamu yang bisa lihat</p>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-submit">⬆ Upload Foto</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        let selectedFiles = [];

        function handleFiles(newFiles) {
            const arr = Array.from(newFiles);
            // Tambah ke existing, bukan reset — batas 10
            arr.forEach(f => {
                if (selectedFiles.length < 10) selectedFiles.push(f);
            });
            renderPreviews();
        }

        function handleDrop(e) {
            e.preventDefault();
            document.getElementById('dropzone').classList.remove('drag-over');
            handleFiles(e.dataTransfer.files);
        }

        function renderPreviews() {
            const grid = document.getElementById('previewGrid');
            const ph = document.getElementById('dropPlaceholder');

            if (selectedFiles.length === 0) {
                grid.classList.add('hidden');
                ph.classList.remove('hidden');
                // reset file input juga
                rebuildFileInput();
                return;
            }

            ph.classList.add('hidden');
            grid.classList.remove('hidden');
            grid.innerHTML = '';

            selectedFiles.forEach((file, i) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const div = document.createElement('div');
                    div.className = 'preview-item';
                    div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                    ${i === 0 ? '<div class="preview-main-badge">Utama</div>' : ''}
                    <button type="button" onclick="removeFile(${i})" class="preview-remove">✕</button>
                `;
                    grid.appendChild(div);
                };
                reader.readAsDataURL(file);
            });

            // Tombol tambah lagi (jika < 10)
            if (selectedFiles.length < 10) {
                const addBtn = document.createElement('div');
                addBtn.className = 'preview-add-btn';
                addBtn.innerHTML = '+';
                addBtn.onclick = () => document.getElementById('photoInput').click();
                grid.appendChild(addBtn);
            }

            rebuildFileInput();
        }

        function removeFile(idx) {
            selectedFiles.splice(idx, 1);
            renderPreviews();
        }

        function rebuildFileInput() {
            // Buat DataTransfer baru dengan file yang tersisa
            const dt = new DataTransfer();
            selectedFiles.forEach(f => dt.items.add(f));
            document.getElementById('photoInput').files = dt.files;
        }

        function selectVisibility(val) {
            document.querySelector(`input[value="${val}"]`).checked = true;
            document.getElementById('opt-public').classList.toggle('selected', val === 'public');
            document.getElementById('opt-private').classList.toggle('selected', val === 'private');
        }
    </script>
@endpush