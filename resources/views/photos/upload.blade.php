@extends('layouts.app')
@section('title', 'Upload')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Step indicator --}}
    <div class="flex items-center gap-3 mb-8">
        <div id="step-ind-1" class="flex items-center gap-2">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold"
                 style="background: rgba(124,58,237,.6); border: 1px solid rgba(124,58,237,.8);">1</div>
            <span class="text-sm font-medium text-white">Pilih Foto</span>
        </div>
        <div class="flex-1 h-px bg-white/10"></div>
        <div id="step-ind-2" class="flex items-center gap-2 opacity-40">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-white/10 border border-white/20">2</div>
            <span class="text-sm font-medium">Edit Foto</span>
        </div>
        <div class="flex-1 h-px bg-white/10"></div>
        <div id="step-ind-3" class="flex items-center gap-2 opacity-40">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold bg-white/10 border border-white/20">3</div>
            <span class="text-sm font-medium">Detail</span>
        </div>
    </div>

    {{-- ───────────────────────────────
         STEP 1: Pilih Foto
    ─────────────────────────────────── --}}
    <div id="step1">
        <h2 class="text-2xl font-bold mb-6">📷 Pilih Foto</h2>

        <div id="dz"
             class="w-full min-h-64 rounded-2xl transition-all duration-200 cursor-pointer flex flex-col items-center justify-center"
             style="border: 2px dashed rgba(255,255,255,.15); background: rgba(255,255,255,.03);"
             onclick="document.getElementById('pi').click()"
             ondragover="event.preventDefault(); this.style.borderColor='rgba(124,58,237,.6)'; this.style.background='rgba(124,58,237,.05)'"
             ondragleave="this.style.borderColor='rgba(255,255,255,.15)'; this.style.background='rgba(255,255,255,.03)'"
             ondrop="onDrop(event)">

            <span class="text-5xl mb-4">📷</span>
            <p class="text-sm font-medium text-gray-300">Klik atau seret foto ke sini</p>
            <p class="text-xs mt-1 text-gray-600">JPG, PNG, WebP — maks 10MB per foto, maks 10 foto</p>
        </div>
        <input type="file" id="pi" accept="image/*" multiple class="hidden" onchange="onFilesSelected(this.files)">

        {{-- Preview grid --}}
        <div id="pg" class="hidden grid grid-cols-3 sm:grid-cols-4 gap-3 mt-4"></div>

        <div id="step1Next" class="hidden mt-6">
            <button onclick="goToStep(2)"
                    class="w-full py-3 rounded-full text-sm font-medium text-white transition-all"
                    style="background: linear-gradient(135deg, #7C3AED, #6D28D9); border: 1px solid rgba(124,58,237,.5);">
                Lanjut ke Editor →
            </button>
        </div>
    </div>

    {{-- ───────────────────────────────
         STEP 2: Editor
    ─────────────────────────────────── --}}
    <div id="step2" class="hidden">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold">✏ Edit Foto</h2>
            <div class="flex items-center gap-2 text-sm text-gray-400">
                Foto <span id="editIndex">1</span> / <span id="editTotal">1</span>
                <button onclick="prevEditPhoto()" class="px-3 py-1.5 rounded-full text-xs"
                        style="background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.1);">‹</button>
                <button onclick="nextEditPhoto()" class="px-3 py-1.5 rounded-full text-xs"
                        style="background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.1);">›</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Preview canvas --}}
            <div class="flex flex-col">
                <div class="rounded-2xl overflow-hidden flex items-center justify-center relative"
                     style="background: rgba(0,0,0,.4); border: 1px solid rgba(255,255,255,.1); min-height: 300px;">
                    <canvas id="editorCanvas" class="max-w-full max-h-80 object-contain"></canvas>
                </div>
                {{-- Foto navigator thumbnails --}}
                <div id="editThumbs" class="flex gap-2 mt-3 overflow-x-auto pb-2"></div>
            </div>

            {{-- Controls --}}
            <div class="space-y-5 rounded-2xl p-5"
                 style="background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.1);">

                {{-- Crop --}}
                <div>
                    <p class="text-xs text-gray-400 mb-2 font-medium">✂ Crop</p>
                    <div class="flex gap-2 flex-wrap">
                        @foreach(['Original' => 'orig', '1:1' => '1x1', '4:5' => '4x5', '16:9' => '16x9'] as $label => $val)
                            <button onclick="setCrop('{{ $val }}')"
                                    class="crop-btn px-3 py-1.5 rounded-full text-xs transition-all"
                                    data-val="{{ $val }}"
                                    style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Rotate --}}
                <div>
                    <p class="text-xs text-gray-400 mb-2 font-medium">↺ Rotate</p>
                    <div class="flex gap-2">
                        @foreach(['90°' => 90, '180°' => 180, '270°' => 270] as $label => $deg)
                            <button onclick="doRotate({{ $deg }})"
                                    class="px-3 py-1.5 rounded-full text-xs transition-all hover:text-white"
                                    style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);">
                                {{ $label }}
                            </button>
                        @endforeach
                        <button onclick="doRotate(0, true)"
                                class="px-3 py-1.5 rounded-full text-xs transition-all hover:text-red-400"
                                style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);">
                            Reset
                        </button>
                    </div>
                </div>

                {{-- Filters --}}
                <div>
                    <p class="text-xs text-gray-400 mb-2 font-medium">🎨 Filter</p>
                    <div class="grid grid-cols-4 gap-2">
                        @foreach([
                            'Original'   => '',
                            'Vintage'    => 'sepia(.5) contrast(1.1) brightness(.9)',
                            'Warm'       => 'sepia(.2) saturate(1.4) brightness(1.05)',
                            'Cool'       => 'hue-rotate(180deg) saturate(.8)',
                            'B&W'        => 'grayscale(1)',
                            'Cinematic'  => 'contrast(1.3) saturate(.7) brightness(.85)',
                            'Dreamy'     => 'blur(.5px) brightness(1.1) saturate(1.3)',
                            'Fade'       => 'opacity(.85) contrast(.9) brightness(1.1)',
                        ] as $name => $val)
                            <button onclick="setFilter('{{ $val }}', this)"
                                    class="filter-btn flex flex-col items-center gap-1 p-2 rounded-xl text-xs transition-all"
                                    data-filter="{{ $val }}"
                                    style="background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08);">
                                <span>{{ $name }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Adjustments --}}
                <div>
                    <p class="text-xs text-gray-400 mb-3 font-medium">⚙ Adjustment</p>
                    <div class="space-y-3">
                        @foreach([
                            ['id'=>'brightness','label'=>'Brightness','min'=>0,'max'=>200,'default'=>100],
                            ['id'=>'contrast','label'=>'Contrast','min'=>0,'max'=>200,'default'=>100],
                            ['id'=>'saturation','label'=>'Saturation','min'=>0,'max'=>200,'default'=>100],
                            ['id'=>'exposure','label'=>'Exposure','min'=>50,'max'=>150,'default'=>100],
                        ] as $adj)
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-xs text-gray-500">{{ $adj['label'] }}</span>
                                    <span id="{{ $adj['id'] }}-val" class="text-xs text-gray-400">{{ $adj['default'] }}</span>
                                </div>
                                <input type="range" id="{{ $adj['id'] }}-slider"
                                       min="{{ $adj['min'] }}" max="{{ $adj['max'] }}" value="{{ $adj['default'] }}"
                                       oninput="setAdj('{{ $adj['id'] }}', this.value)"
                                       class="w-full h-1.5 rounded-full appearance-none cursor-pointer"
                                       style="accent-color: #7C3AED; background: rgba(255,255,255,.1);">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Apply button --}}
                <button onclick="applyEdits()"
                        class="w-full py-2.5 rounded-full text-sm font-medium text-white transition-all"
                        style="background: rgba(124,58,237,.4); border: 1px solid rgba(124,58,237,.5);">
                    ✓ Terapkan Perubahan
                </button>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <button onclick="goToStep(1)"
                    class="flex-1 py-3 rounded-full text-sm text-gray-400 transition-all"
                    style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);">
                ← Kembali
            </button>
            <button onclick="goToStep(3)"
                    class="flex-1 py-3 rounded-full text-sm font-medium text-white transition-all"
                    style="background: linear-gradient(135deg, #7C3AED, #6D28D9); border: 1px solid rgba(124,58,237,.5);">
                Lanjut ke Detail →
            </button>
        </div>
    </div>

    {{-- ───────────────────────────────
         STEP 3: Detail
    ─────────────────────────────────── --}}
    <div id="step3" class="hidden">
        <h2 class="text-2xl font-bold mb-6">📝 Detail Foto</h2>

        <form id="uploadForm" method="POST" action="{{ route('upload') }}" enctype="multipart/form-data"
              class="rounded-2xl p-6 space-y-5"
              style="background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.1);">
            @csrf

            {{-- Hidden file input yang akan diisi oleh JS --}}
            <div id="hiddenFilesContainer"></div>

            {{-- Caption --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Caption *</label>
                <input type="text" name="caption" value="{{ old('caption') }}" placeholder="Tulis caption..."
                       required
                       class="w-full px-4 py-3 rounded-2xl text-sm text-white placeholder-gray-600 focus:outline-none transition-all"
                       style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);">
                @error('caption') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Deskripsi <span class="text-gray-600">(opsional)</span></label>
                <textarea name="description" rows="3" placeholder="Ceritakan momen ini..."
                          class="w-full px-4 py-3 rounded-2xl text-sm text-white placeholder-gray-600 focus:outline-none transition-all resize-none"
                          style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);">{{ old('description') }}</textarea>
            </div>

            {{-- Tags --}}
            <div>
                <label class="block text-xs text-gray-400 mb-1.5 font-medium">Tags <span class="text-gray-600">(pisah koma)</span></label>
                <input type="text" name="tags" value="{{ old('tags') }}" placeholder="alam, pantai, sunset"
                       class="w-full px-4 py-3 rounded-2xl text-sm text-white placeholder-gray-600 focus:outline-none transition-all"
                       style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);">
            </div>

            {{-- Event (jika ada event aktif) --}}
            @if($activeEvents->isNotEmpty())
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Ikuti Event <span class="text-gray-600">(opsional)</span></label>
                    <select name="event_id"
                            class="w-full px-4 py-3 rounded-2xl text-sm text-white focus:outline-none transition-all"
                            style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);">
                        <option value="">— Tidak ikut event —</option>
                        @foreach($activeEvents as $event)
                            <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }} ({{ $event->daysRemaining() }} hari tersisa)
                            </option>
                        @endforeach
                    </select>
                    <p class="text-gray-600 text-xs mt-1">
                        Tag event akan otomatis ditambahkan ke fotomu
                    </p>
                </div>
            @endif

            {{-- Visibilitas --}}
            <div>
                <label class="block text-xs text-gray-400 mb-3 font-medium">Visibilitas</label>
                <div class="flex gap-3">
                    <label id="vPub" class="flex items-start gap-3 cursor-pointer p-4 flex-1 rounded-2xl border transition-all"
                           style="border-color: rgba(124,58,237,.5); background: rgba(124,58,237,.1);"
                           onclick="setVis('public')">
                        <input type="radio" name="status" value="public" checked class="hidden">
                        <div>
                            <p class="text-sm font-medium">🌍 Public</p>
                            <p class="text-xs text-gray-500 mt-0.5">Semua orang bisa lihat</p>
                        </div>
                    </label>
                    <label id="vPrv" class="flex items-start gap-3 cursor-pointer p-4 flex-1 rounded-2xl border transition-all"
                           style="border-color: rgba(255,255,255,.1); background: rgba(255,255,255,.03);"
                           onclick="setVis('private')">
                        <input type="radio" name="status" value="private" class="hidden">
                        <div>
                            <p class="text-sm font-medium">🔒 Private</p>
                            <p class="text-xs text-gray-500 mt-0.5">Hanya kamu</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="goToStep(2)"
                        class="flex-1 py-3 rounded-full text-sm text-gray-400 transition-all"
                        style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);">
                    ← Kembali
                </button>
                <button type="submit" id="submitBtn"
                        class="flex-1 py-3 rounded-full text-sm font-medium text-white transition-all"
                        style="background: linear-gradient(135deg, #7C3AED, #6D28D9); border: 1px solid rgba(124,58,237,.5);">
                    ⬆ Upload Foto
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ══════════════════════════════════════════════════════════════
//  STATE
// ══════════════════════════════════════════════════════════════
let origFiles   = [];   // File[] asli dari input
let editedFiles = [];   // File[] setelah diedit
let currentEditIdx = 0;

// State editor per foto
let editorStates = [];  // [{rotation, filterStr, brightness, contrast, saturation, exposure, cropAspect}]

const canvas = document.getElementById('editorCanvas');
const ctx    = canvas.getContext('2d');

// ══════════════════════════════════════════════════════════════
//  STEP NAVIGATION
// ══════════════════════════════════════════════════════════════
function goToStep(n) {
    [1, 2, 3].forEach(i => {
        document.getElementById(`step${i}`).classList.toggle('hidden', i !== n);
        const ind = document.getElementById(`step-ind-${i}`);
        if (ind) ind.classList.toggle('opacity-40', i > n);
    });
    if (n === 2) renderEditor();
    if (n === 3) prepareFilesForUpload();
}

// ══════════════════════════════════════════════════════════════
//  STEP 1 — File selection
// ══════════════════════════════════════════════════════════════
function onFilesSelected(files) {
    Array.from(files).forEach(f => {
        if (origFiles.length < 10) {
            origFiles.push(f);
            editedFiles.push(null); // null = belum diedit, pakai orig
            editorStates.push(defaultState());
        }
    });
    renderPreviews();
    document.getElementById('step1Next').classList.toggle('hidden', origFiles.length === 0);
}

function onDrop(e) {
    e.preventDefault();
    const dz = document.getElementById('dz');
    dz.style.borderColor = 'rgba(255,255,255,.15)';
    dz.style.background  = 'rgba(255,255,255,.03)';
    onFilesSelected(e.dataTransfer.files);
}

function renderPreviews() {
    const pg = document.getElementById('pg');
    pg.innerHTML = '';
    if (!origFiles.length) { pg.classList.add('hidden'); return; }
    pg.classList.remove('hidden');

    origFiles.forEach((f, i) => {
        const url = URL.createObjectURL(f);
        const div = document.createElement('div');
        div.className = 'relative rounded-xl overflow-hidden aspect-square cursor-pointer';
        div.onclick = () => { currentEditIdx = i; goToStep(2); };
        div.innerHTML = `
            <img src="${url}" class="w-full h-full object-cover">
            ${i === 0 ? '<div class="absolute bottom-0 left-0 right-0 text-center text-xs py-1 text-white font-medium" style="background:rgba(124,58,237,.8)">Utama</div>' : ''}
            <button type="button" onclick="event.stopPropagation(); removeFile(${i})"
                    class="absolute top-1.5 right-1.5 w-5 h-5 rounded-full flex items-center justify-center text-white text-xs transition-all"
                    style="background: rgba(0,0,0,.6)">✕</button>`;
        pg.appendChild(div);
    });

    if (origFiles.length < 10) {
        const add = document.createElement('div');
        add.className = 'aspect-square rounded-xl flex items-center justify-center text-3xl cursor-pointer transition-all text-gray-600 hover:text-violet-400';
        add.style.cssText = 'border: 2px dashed rgba(255,255,255,.15);';
        add.innerHTML = '＋';
        add.onclick = () => document.getElementById('pi').click();
        pg.appendChild(add);
    }
}

function removeFile(i) {
    origFiles.splice(i, 1);
    editedFiles.splice(i, 1);
    editorStates.splice(i, 1);
    renderPreviews();
    document.getElementById('step1Next').classList.toggle('hidden', origFiles.length === 0);
}

// ══════════════════════════════════════════════════════════════
//  STEP 2 — Editor
// ══════════════════════════════════════════════════════════════
function defaultState() {
    return { rotation: 0, filterStr: '', brightness: 100, contrast: 100, saturation: 100, exposure: 100, cropAspect: 'orig' };
}

function getState() { return editorStates[currentEditIdx]; }

function renderEditor() {
    document.getElementById('editIndex').textContent = currentEditIdx + 1;
    document.getElementById('editTotal').textContent = origFiles.length;

    // Render thumbnails
    const tc = document.getElementById('editThumbs');
    tc.innerHTML = '';
    origFiles.forEach((f, i) => {
        const url = URL.createObjectURL(f);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = `flex-shrink-0 w-12 h-12 rounded-lg overflow-hidden border-2 transition-all ${i === currentEditIdx ? 'border-violet-500' : 'border-transparent opacity-50'}`;
        btn.innerHTML = `<img src="${url}" class="w-full h-full object-cover">`;
        btn.onclick = () => { currentEditIdx = i; renderEditor(); };
        tc.appendChild(btn);
    });

    // Load image to canvas
    const s = getState();
    const file = origFiles[currentEditIdx];
    const img = new Image();
    img.onload = () => {
        let w = img.naturalWidth;
        let h = img.naturalHeight;

        // Apply rotation dimensions swap
        const rot = s.rotation % 360;
        if (rot === 90 || rot === 270) { [w, h] = [h, w]; }

        // Apply crop aspect
        const {cw, ch} = getCropDimensions(w, h, s.cropAspect);
        canvas.width  = cw;
        canvas.height = ch;

        ctx.save();
        ctx.translate(cw / 2, ch / 2);
        ctx.rotate((s.rotation * Math.PI) / 180);
        ctx.filter = buildFilter(s);
        ctx.drawImage(img, -img.naturalWidth / 2, -img.naturalHeight / 2);
        ctx.restore();

        // Update UI controls
        document.getElementById('brightness-slider').value = s.brightness;
        document.getElementById('contrast-slider').value   = s.contrast;
        document.getElementById('saturation-slider').value = s.saturation;
        document.getElementById('exposure-slider').value   = s.exposure;
        document.getElementById('brightness-val').textContent = s.brightness;
        document.getElementById('contrast-val').textContent   = s.contrast;
        document.getElementById('saturation-val').textContent = s.saturation;
        document.getElementById('exposure-val').textContent   = s.exposure;

        document.querySelectorAll('.crop-btn').forEach(b => {
            b.style.borderColor = b.dataset.val === s.cropAspect ? 'rgba(124,58,237,.8)' : 'rgba(255,255,255,.1)';
            b.style.background  = b.dataset.val === s.cropAspect ? 'rgba(124,58,237,.2)' : 'rgba(255,255,255,.06)';
        });
        document.querySelectorAll('.filter-btn').forEach(b => {
            const match = b.dataset.filter === s.filterStr;
            b.style.borderColor = match ? 'rgba(124,58,237,.8)' : 'rgba(255,255,255,.08)';
            b.style.background  = match ? 'rgba(124,58,237,.15)' : 'rgba(255,255,255,.04)';
        });
    };
    img.src = URL.createObjectURL(file);
}

function buildFilter(s) {
    let f = `brightness(${s.brightness}%) contrast(${s.contrast}%) saturate(${s.saturation}%)`;
    if (s.exposure !== 100) f += ` brightness(${s.exposure}%)`;
    if (s.filterStr) f += ' ' + s.filterStr;
    return f;
}

function getCropDimensions(w, h, aspect) {
    if (aspect === 'orig') return { cw: w, ch: h };
    const [ar, ab] = { '1x1': [1,1], '4x5': [4,5], '16x9': [16,9] }[aspect];
    const ratio = ar / ab;
    if (w / h > ratio) { return { cw: Math.floor(h * ratio), ch: h }; }
    else               { return { cw: w, ch: Math.floor(w / ratio) }; }
}

function setCrop(val) {
    getState().cropAspect = val;
    renderEditor();
}

function doRotate(deg, reset = false) {
    if (reset) getState().rotation = 0;
    else getState().rotation = (getState().rotation + deg) % 360;
    renderEditor();
}

function setFilter(val, btn) {
    getState().filterStr = val;
    renderEditor();
}

function setAdj(type, val) {
    getState()[type] = parseInt(val);
    document.getElementById(`${type}-val`).textContent = val;
    renderEditor();
}

function prevEditPhoto() {
    if (currentEditIdx > 0) { currentEditIdx--; renderEditor(); }
}
function nextEditPhoto() {
    if (currentEditIdx < origFiles.length - 1) { currentEditIdx++; renderEditor(); }
}

async function applyEdits() {
    // Export current canvas state as File
    return new Promise(resolve => {
        canvas.toBlob(blob => {
            const file = new File([blob], origFiles[currentEditIdx].name, { type: 'image/jpeg' });
            editedFiles[currentEditIdx] = file;
            const btn = document.querySelector('#step2 button[onclick="applyEdits()"]');
            btn.textContent = '✓ Diterapkan!';
            btn.style.background = 'rgba(16,185,129,.3)';
            btn.style.borderColor = 'rgba(16,185,129,.5)';
            setTimeout(() => {
                btn.textContent = '✓ Terapkan Perubahan';
                btn.style.background = 'rgba(124,58,237,.4)';
                btn.style.borderColor = 'rgba(124,58,237,.5)';
            }, 1500);
            resolve();
        }, 'image/jpeg', 0.92);
    });
}

// ══════════════════════════════════════════════════════════════
//  STEP 3 — Prepare files for form submit
// ══════════════════════════════════════════════════════════════
async function prepareFilesForUpload() {
    // Untuk setiap foto yang belum di-apply, apply dulu dengan state default
    for (let i = 0; i < origFiles.length; i++) {
        if (!editedFiles[i]) {
            // Gunakan file asli tanpa edit
            editedFiles[i] = origFiles[i];
        }
    }

    // Buat DataTransfer dengan edited files
    const dt = new DataTransfer();
    editedFiles.forEach(f => { if (f) dt.items.add(f); });

    // Inject ke form sebagai hidden input dengan file input
    const container = document.getElementById('hiddenFilesContainer');
    container.innerHTML = '';
    const inp = document.createElement('input');
    inp.type = 'file';
    inp.name = 'photos[]';
    inp.multiple = true;
    inp.style.display = 'none';
    inp.files = dt.files;
    container.appendChild(inp);
}

function setVis(v) {
    document.querySelector(`input[value="${v}"]`).checked = true;
    const pub = document.getElementById('vPub');
    const prv = document.getElementById('vPrv');
    if (v === 'public') {
        pub.style.borderColor = 'rgba(124,58,237,.5)'; pub.style.background = 'rgba(124,58,237,.1)';
        prv.style.borderColor = 'rgba(255,255,255,.1)'; prv.style.background = 'rgba(255,255,255,.03)';
    } else {
        prv.style.borderColor = 'rgba(124,58,237,.5)'; prv.style.background = 'rgba(124,58,237,.1)';
        pub.style.borderColor = 'rgba(255,255,255,.1)'; pub.style.background = 'rgba(255,255,255,.03)';
    }
}

// Form submit — pastikan files sudah siap
document.getElementById('uploadForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    await prepareFilesForUpload();
    e.target.submit();
});
</script>
@endpush