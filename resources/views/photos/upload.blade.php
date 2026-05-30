@extends('layouts.app')
@section('title', 'Upload')

@push('head')
    <style>
        /* Step indicator */
        .step-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            transition: all .2s;
            flex-shrink: 0
        }

        .step-dot.done {
            background: rgba(124, 58, 237, .4);
            border: 1px solid rgba(124, 58, 237, .6);
            color: #c4b5fd
        }

        .step-dot.active {
            background: rgba(124, 58, 237, .6);
            border: 1px solid rgba(124, 58, 237, .8);
            color: #fff
        }

        .step-dot.idle {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .1);
            color: #6b7280
        }

        /* Editor controls — lebih manusiawi */
        .ctrl-section {
            margin-bottom: 20px
        }

        .ctrl-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #6b7280;
            margin-bottom: 10px;
            display: block
        }

        .ratio-btn,
        .rotate-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 10px;
            font-size: 12px;
            cursor: pointer;
            transition: all .15s;
            border: 1px solid rgba(255, 255, 255, .1);
            background: rgba(255, 255, 255, .05);
            color: #9ca3af;
            user-select: none
        }

        .ratio-btn:hover,
        .rotate-btn:hover {
            background: rgba(255, 255, 255, .1);
            color: #e5e7eb
        }

        .ratio-btn.active,
        .rotate-btn.active {
            background: rgba(124, 58, 237, .25);
            border-color: rgba(124, 58, 237, .5);
            color: #c4b5fd
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px
        }

        .filter-thumb {
            border-radius: 10px;
            overflow: hidden;
            cursor: pointer;
            position: relative;
            border: 2px solid transparent;
            transition: all .15s
        }

        .filter-thumb:hover {
            border-color: rgba(255, 255, 255, .2)
        }

        .filter-thumb.active {
            border-color: rgba(124, 58, 237, .7)
        }

        .filter-thumb img {
            width: 100%;
            height: 56px;
            object-fit: cover;
            pointer-events: none
        }

        .filter-thumb span {
            display: block;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            padding: 3px 0;
            background: rgba(0, 0, 0, .4)
        }

        .adj-row {
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-bottom: 14px
        }

        .adj-head {
            display: flex;
            justify-content: space-between;
            align-items: center
        }

        .adj-head span {
            font-size: 12px;
            color: #9ca3af
        }

        .adj-head small {
            font-size: 11px;
            color: #6b7280;
            font-variant-numeric: tabular-nums;
            min-width: 28px;
            text-align: right
        }

        input[type=range] {
            width: 100%;
            height: 4px;
            border-radius: 4px;
            appearance: none;
            background: rgba(255, 255, 255, .1);
            accent-color: #7c3aed;
            cursor: pointer
        }

        input[type=range]::-webkit-slider-thumb {
            appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #7c3aed;
            border: 2px solid #a78bfa;
            cursor: pointer;
            box-shadow: 0 0 6px rgba(124, 58, 237, .5)
        }
    </style>
@endpush

@section('content')
    <div class="max-w-2xl mx-auto">

        {{-- Step indicator --}}
        <div class="flex items-center gap-3 mb-8">
            <div id="si1" class="step-dot active">1</div>
            <p class="text-sm font-medium" id="sl1">Pilih Foto</p>
            <div class="flex-1 h-px" style="background:rgba(255,255,255,.08)"></div>
            <div id="si2" class="step-dot idle">2</div>
            <p class="text-sm text-gray-600" id="sl2">Edit Foto</p>
            <div class="flex-1 h-px" style="background:rgba(255,255,255,.08)"></div>
            <div id="si3" class="step-dot idle">3</div>
            <p class="text-sm text-gray-600" id="sl3">Detail</p>
        </div>

        {{-- ══ STEP 1 ══ --}}
        <div id="step1">
            <h2 class="text-xl font-bold mb-5">Pilih Foto</h2>

            <div id="dz"
                class="w-full min-h-56 rounded-2xl flex flex-col items-center justify-center cursor-pointer transition-all"
                style="border:2px dashed rgba(255,255,255,.12);background:rgba(255,255,255,.02)"
                onclick="document.getElementById('pi').click()" ondragover="event.preventDefault();dzActive(true)"
                ondragleave="dzActive(false)" ondrop="onDrop(event)">

                <svg class="w-12 h-12 text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-sm font-medium text-gray-400">Klik atau seret foto ke sini</p>
                <p class="text-xs text-gray-600 mt-1">JPG, PNG, WebP · maks 20MB · maks 10 foto</p>
            </div>
            <input type="file" id="pi" accept="image/*" multiple class="hidden" onchange="onFilesSelected(this.files)">

            <div id="pg" class="hidden grid grid-cols-3 sm:grid-cols-4 gap-3 mt-4"></div>

            <div id="s1Next" class="hidden mt-5">
                <button onclick="goStep(2)" class="w-full py-3 rounded-xl text-sm font-medium text-white transition-all"
                    style="background:rgba(124,58,237,.4);border:1px solid rgba(124,58,237,.5)">
                    Lanjut ke Editor →
                </button>
            </div>
        </div>

        {{-- ══ STEP 2 ══ --}}
        <div id="step2" class="hidden">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-bold">Edit Foto</h2>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500">
                        <span id="eIdx">1</span> / <span id="eTot">1</span>
                    </span>
                    <button onclick="prevPhoto()"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-white transition-all"
                        style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button onclick="nextPhoto()"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:text-white transition-all"
                        style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                {{-- Canvas preview --}}
                <div>
                    <div class="rounded-2xl overflow-hidden flex items-center justify-center"
                        style="background:rgba(0,0,0,.35);border:1px solid rgba(255,255,255,.08);min-height:280px">
                        <canvas id="edCanvas" class="max-w-full max-h-72 object-contain rounded-xl"></canvas>
                    </div>
                    {{-- Thumbnail strip --}}
                    <div id="eThumbs" class="flex gap-2 mt-3 overflow-x-auto pb-1"></div>
                </div>

                {{-- Controls --}}
                <div class="rounded-2xl p-5 space-y-0"
                    style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08)">

                    {{-- Crop --}}
                    <div class="ctrl-section">
                        <span class="ctrl-label">Crop</span>
                        <div class="flex gap-2 flex-wrap">
                            <button onclick="setCrop('orig',this)" class="ratio-btn active"
                                data-crop="orig">Original</button>
                            <button onclick="setCrop('1x1',this)" class="ratio-btn" data-crop="1x1">1 : 1</button>
                            <button onclick="setCrop('4x5',this)" class="ratio-btn" data-crop="4x5">4 : 5</button>
                            <button onclick="setCrop('16x9',this)" class="ratio-btn" data-crop="16x9">16 : 9</button>
                        </div>
                    </div>

                    {{-- Rotate --}}
                    <div class="ctrl-section">
                        <span class="ctrl-label">Putar</span>
                        <div class="flex gap-2">
                            <button onclick="doRotate(90)" class="rotate-btn">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                90°
                            </button>
                            <button onclick="doRotate(180)" class="rotate-btn">180°</button>
                            <button onclick="doRotate(270)" class="rotate-btn">270°</button>
                            <button onclick="resetRotate()" class="rotate-btn"
                                style="color:#ef4444;border-color:rgba(239,68,68,.3)">Reset</button>
                        </div>
                    </div>

                    {{-- Filter --}}
                    <div class="ctrl-section">
                        <span class="ctrl-label">Filter</span>
                        <div class="filter-grid" id="filterGrid"></div>
                    </div>

                    {{-- Adjustment --}}
                    <div class="ctrl-section">
                        <span class="ctrl-label">Penyesuaian</span>
                        <div id="adjControls"></div>
                    </div>

                    {{-- Apply --}}
                    <button onclick="applyEdit()" id="applyBtn"
                        class="w-full py-2.5 rounded-xl text-sm font-medium text-white transition-all mt-2"
                        style="background:rgba(124,58,237,.35);border:1px solid rgba(124,58,237,.45)">
                        Terapkan ke Foto Ini
                    </button>
                </div>
            </div>

            <div class="flex gap-3 mt-5">
                <button onclick="goStep(1)" class="flex-1 py-3 rounded-xl text-sm text-gray-400 transition-all"
                    style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                    ← Kembali
                </button>
                <button onclick="goStep(3)" class="flex-1 py-3 rounded-xl text-sm font-medium text-white transition-all"
                    style="background:rgba(124,58,237,.4);border:1px solid rgba(124,58,237,.5)">
                    Lanjut ke Detail →
                </button>
            </div>
        </div>

        {{-- ══ STEP 3 ══ --}}
        <div id="step3" class="hidden">
            <h2 class="text-xl font-bold mb-5">Detail Foto</h2>

            <form id="uForm" method="POST" action="{{ route('upload') }}" enctype="multipart/form-data"
                class="rounded-2xl p-6 space-y-5"
                style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08)">
                @csrf
                <div id="fileSlot"></div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Caption
                        *</label>
                    <input type="text" name="caption" value="{{ old('caption') }}" placeholder="Tulis caption singkat..."
                        class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none transition-all"
                        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                    @error('caption') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">
                        Deskripsi <span class="normal-case font-normal text-gray-600">(opsional)</span>
                    </label>
                    <textarea name="description" rows="3" placeholder="Ceritakan momen ini..."
                        class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none transition-all resize-none"
                        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">
                        Tags <span class="normal-case font-normal text-gray-600">(pisah koma)</span>
                    </label>
                    <input type="text" name="tags" value="{{ old('tags') }}" placeholder="alam, pantai, sunset"
                        class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none transition-all"
                        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                </div>

                @if($activeEvents->isNotEmpty())
                    <div>
                        <label class="block text-xs text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">
                            Ikuti Event <span class="normal-case font-normal text-gray-600">(opsional)</span>
                        </label>
                        <select name="event_id"
                            class="w-full px-4 py-3 rounded-xl text-sm text-white focus:outline-none transition-all"
                            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                            <option value="">— Tidak ikut event —</option>
                            @foreach($activeEvents as $ev)
                                <option value="{{ $ev->id }}" {{ old('event_id') == $ev->id ? 'selected' : '' }}>
                                    {{ $ev->title }}
                                    ({{ $ev->daysRemaining() > 0 ? $ev->daysRemaining() . ' hari lagi' : 'Berakhir hari ini' }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-gray-600 mt-1">Tag event akan otomatis ditambahkan</p>
                    </div>
                @endif

                <div>
                    <label
                        class="block text-xs text-gray-500 mb-3 font-semibold uppercase tracking-wide">Visibilitas</label>
                    <div class="flex gap-3">
                        <label id="vPub"
                            class="flex items-start gap-3 cursor-pointer p-4 flex-1 rounded-xl border transition-all"
                            style="border-color:rgba(124,58,237,.5);background:rgba(124,58,237,.08)"
                            onclick="setVis('public')">
                            <input type="radio" name="status" value="public" checked class="hidden">
                            <div>
                                <p class="text-sm font-medium flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Public
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">Semua orang bisa lihat</p>
                            </div>
                        </label>
                        <label id="vPrv"
                            class="flex items-start gap-3 cursor-pointer p-4 flex-1 rounded-xl border transition-all"
                            style="border-color:rgba(255,255,255,.1);background:rgba(255,255,255,.03)"
                            onclick="setVis('private')">
                            <input type="radio" name="status" value="private" class="hidden">
                            <div>
                                <p class="text-sm font-medium flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    Private
                                </p>
                                <p class="text-xs text-gray-500 mt-0.5">Hanya kamu</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="goStep(2)"
                        class="flex-1 py-3 rounded-xl text-sm text-gray-400 transition-all"
                        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                        ← Kembali
                    </button>
                    <button type="submit"
                        class="flex-1 py-3 rounded-xl text-sm font-medium text-white flex items-center justify-center gap-2 transition-all"
                        style="background:rgba(124,58,237,.45);border:1px solid rgba(124,58,237,.6)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        Upload Foto
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // ═══════════════════════════════════════════
        // STATE
        // ═══════════════════════════════════════════
        let FILES = [];
        let STATES = [];
        let EDITED = [];
        let curIdx = 0;

        const canvas = document.getElementById('edCanvas');
        const ctx = canvas.getContext('2d');

        const FILTERS = [
            { name: 'Original', css: '' },
            { name: 'Vintage', css: 'sepia(.5) contrast(1.1) brightness(.9)' },
            { name: 'Warm', css: 'sepia(.2) saturate(1.5) brightness(1.05)' },
            { name: 'Cool', css: 'hue-rotate(200deg) saturate(.85)' },
            { name: 'B&W', css: 'grayscale(1)' },
            { name: 'Sinema', css: 'contrast(1.3) saturate(.65) brightness(.88)' },
            { name: 'Dreamy', css: 'brightness(1.1) saturate(1.4) contrast(.9)' },
            { name: 'Fade', css: 'contrast(.88) brightness(1.12) saturate(.8)' },
        ];

        const ADJS = [
            { id: 'br', label: 'Kecerahan', min: 50, max: 150, def: 100 },
            { id: 'cn', label: 'Kontras', min: 50, max: 150, def: 100 },
            { id: 'st', label: 'Saturasi', min: 0, max: 200, def: 100 },
            { id: 'ex', label: 'Eksposur', min: 70, max: 130, def: 100 },
        ];

        function defState() {
            return { rot: 0, crop: 'orig', filter: '', br: 100, cn: 100, st: 100, ex: 100 };
        }
        function S() { return STATES[curIdx]; }

        // ═══════════════════════════════════════════
        // STEP NAVIGATION
        // ═══════════════════════════════════════════
        function goStep(n) {
            [1, 2, 3].forEach(i => {
                document.getElementById(`step${i}`).classList.toggle('hidden', i !== n);
                const dot = document.getElementById(`si${i}`);
                const lbl = document.getElementById(`sl${i}`);
                if (i < n) { dot.className = 'step-dot done'; lbl.className = 'text-sm font-medium text-violet-400'; }
                if (i === n) { dot.className = 'step-dot active'; lbl.className = 'text-sm font-medium text-white'; }
                if (i > n) { dot.className = 'step-dot idle'; lbl.className = 'text-sm text-gray-600'; }
            });
            if (n === 2) initEditor();
            if (n === 3) buildFileSlot();
        }

        // ═══════════════════════════════════════════
        // STEP 1 — File selection
        // ═══════════════════════════════════════════
        function dzActive(on) {
            const dz = document.getElementById('dz');
            dz.style.borderColor = on ? 'rgba(124,58,237,.6)' : 'rgba(255,255,255,.12)';
            dz.style.background = on ? 'rgba(124,58,237,.05)' : 'rgba(255,255,255,.02)';
        }

        function onFilesSelected(incoming) {
            Array.from(incoming).forEach(f => {
                if (FILES.length < 10) {
                    FILES.push(f);
                    STATES.push(defState());
                    EDITED.push(null);
                }
            });
            renderPreviews();
        }

        function onDrop(e) {
            e.preventDefault(); dzActive(false);
            onFilesSelected(e.dataTransfer.files);
        }

        function renderPreviews() {
            const pg = document.getElementById('pg');
            if (!FILES.length) { pg.classList.add('hidden'); document.getElementById('s1Next').classList.add('hidden'); return; }
            pg.classList.remove('hidden');
            document.getElementById('s1Next').classList.remove('hidden');
            pg.innerHTML = '';
            FILES.forEach((f, i) => {
                const url = URL.createObjectURL(f);
                const d = document.createElement('div');
                d.className = 'relative rounded-xl overflow-hidden aspect-square cursor-pointer group';
                d.onclick = () => { curIdx = i; goStep(2); };
                d.innerHTML = `
                <img src="${url}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                ${i === 0 ? `<div class="absolute bottom-0 left-0 right-0 text-center text-[11px] py-1 text-white font-medium" style="background:rgba(124,58,237,.8)">Utama</div>` : ''}
                <button type="button" onclick="event.stopPropagation();removeFile(${i})"
                        class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full flex items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity"
                        style="background:rgba(0,0,0,.65)">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                ${EDITED[i] ? `<div class="absolute top-1.5 left-1.5 w-5 h-5 rounded-full flex items-center justify-center" style="background:rgba(124,58,237,.8)"><svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div>` : ''}
            `;
                pg.appendChild(d);
            });
            if (FILES.length < 10) {
                const a = document.createElement('div');
                a.className = 'aspect-square rounded-xl flex items-center justify-center cursor-pointer transition-all text-gray-600 hover:text-gray-300';
                a.style.cssText = 'border:2px dashed rgba(255,255,255,.1)';
                a.innerHTML = `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>`;
                a.onclick = () => document.getElementById('pi').click();
                pg.appendChild(a);
            }
        }

        function removeFile(i) {
            FILES.splice(i, 1); STATES.splice(i, 1); EDITED.splice(i, 1);
            renderPreviews();
        }

        // ═══════════════════════════════════════════
        // STEP 2 — Editor
        // ═══════════════════════════════════════════
        function initEditor() {
            buildFilterGrid();
            buildAdjControls();
            renderEditorState();
        }

        function buildFilterGrid() {
            const grid = document.getElementById('filterGrid');
            grid.innerHTML = '';
            // Generate previews dari foto pertama
            const url = URL.createObjectURL(FILES[curIdx]);
            FILTERS.forEach((f, i) => {
                const d = document.createElement('div');
                d.className = `filter-thumb${f.css === S().filter ? ' active' : ''}`;
                d.onclick = () => { S().filter = f.css; highlightFilter(i); drawCanvas(); };
                d.innerHTML = `
                <img src="${url}" style="filter:${f.css}">
                <span>${f.name}</span>
            `;
                grid.appendChild(d);
            });
        }

        function highlightFilter(active) {
            document.querySelectorAll('.filter-thumb').forEach((el, i) => {
                el.classList.toggle('active', i === active);
            });
        }

        function buildAdjControls() {
            const c = document.getElementById('adjControls');
            c.innerHTML = '';
            ADJS.forEach(a => {
                const val = S()[a.id];
                c.innerHTML += `
                <div class="adj-row">
                    <div class="adj-head">
                        <span>${a.label}</span>
                        <small id="v-${a.id}">${val}</small>
                    </div>
                    <input type="range" min="${a.min}" max="${a.max}" value="${val}"
                           oninput="setAdj('${a.id}',+this.value)">
                </div>
            `;
            });
        }

        function renderEditorState() {
            document.getElementById('eIdx').textContent = curIdx + 1;
            document.getElementById('eTot').textContent = FILES.length;

            // Thumbnails
            const tc = document.getElementById('eThumbs');
            tc.innerHTML = '';
            FILES.forEach((f, i) => {
                const b = document.createElement('button');
                b.type = 'button';
                b.className = `flex-shrink-0 w-12 h-12 rounded-lg overflow-hidden border-2 transition-all ${i === curIdx ? 'border-violet-500' : 'border-transparent opacity-50'}`;
                b.innerHTML = `<img src="${URL.createObjectURL(f)}" class="w-full h-full object-cover">`;
                b.onclick = () => { curIdx = i; syncControls(); drawCanvas(); renderEditorState(); };
                tc.appendChild(b);
            });

            // Sync crop buttons
            document.querySelectorAll('.ratio-btn').forEach(b => {
                b.classList.toggle('active', b.dataset.crop === S().crop);
            });

            drawCanvas();
        }

        function syncControls() {
            buildFilterGrid();
            buildAdjControls();
        }

        function getCanvasDims(iw, ih, crop) {
            if (crop === 'orig') return [iw, ih];
            const ratios = { '1x1': [1, 1], '4x5': [4, 5], '16x9': [16, 9] };
            const [a, b] = ratios[crop];
            const r = a / b;
            if (iw / ih > r) return [Math.round(ih * r), ih];
            return [iw, Math.round(iw / r)];
        }

        function buildFilter(s) {
            let f = `brightness(${s.br}%) contrast(${s.cn}%) saturate(${s.st}%)`;
            if (s.ex !== 100) f += ` brightness(${s.ex}%)`;
            if (s.filter) f += ' ' + s.filter;
            return f;
        }

        function drawCanvas() {
            const s = S();
            const f = FILES[curIdx];
            const img = new Image();
            img.onload = () => {
                let iw = img.naturalWidth;
                let ih = img.naturalHeight;
                if (s.rot === 90 || s.rot === 270) [iw, ih] = [ih, iw];
                const [cw, ch] = getCanvasDims(iw, ih, s.crop);
                canvas.width = cw; canvas.height = ch;
                ctx.clearRect(0, 0, cw, ch);
                ctx.save();
                ctx.translate(cw / 2, ch / 2);
                ctx.rotate(s.rot * Math.PI / 180);
                ctx.filter = buildFilter(s);
                ctx.drawImage(img, -img.naturalWidth / 2, -img.naturalHeight / 2);
                ctx.restore();
            };
            img.src = URL.createObjectURL(f);
        }

        function setCrop(val, btn) {
            S().crop = val;
            document.querySelectorAll('.ratio-btn').forEach(b => b.classList.toggle('active', b === btn));
            drawCanvas();
        }

        function doRotate(deg) {
            S().rot = (S().rot + deg) % 360;
            drawCanvas();
        }

        function resetRotate() {
            S().rot = 0;
            drawCanvas();
        }

        function setAdj(id, val) {
            S()[id] = val;
            const el = document.getElementById(`v-${id}`);
            if (el) el.textContent = val;
            drawCanvas();
        }

        function prevPhoto() { if (curIdx > 0) { curIdx--; syncControls(); renderEditorState(); } }
        function nextPhoto() { if (curIdx < FILES.length - 1) { curIdx++; syncControls(); renderEditorState(); } }

        async function applyEdit() {
            const btn = document.getElementById('applyBtn');
            btn.disabled = true;
            btn.textContent = 'Memproses...';
            await new Promise(resolve => {
                canvas.toBlob(blob => {
                    EDITED[curIdx] = new File([blob], FILES[curIdx].name, { type: 'image/jpeg' });
                    resolve();
                }, 'image/jpeg', 0.92);
            });
            btn.disabled = false;
            btn.style.background = 'rgba(16,185,129,.3)';
            btn.style.borderColor = 'rgba(16,185,129,.4)';
            btn.innerHTML = `<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> Diterapkan`;
            setTimeout(() => {
                btn.style.background = 'rgba(124,58,237,.35)';
                btn.style.borderColor = 'rgba(124,58,237,.45)';
                btn.textContent = 'Terapkan ke Foto Ini';
            }, 1800);
            renderPreviews();
        }

        // ═══════════════════════════════════════════
        // STEP 3 — Build file input
        // ═══════════════════════════════════════════
        async function buildFileSlot() {
            // Foto yang belum diedit, pakai original
            for (let i = 0; i < FILES.length; i++) {
                if (!EDITED[i]) EDITED[i] = FILES[i];
            }
            const dt = new DataTransfer();
            EDITED.forEach(f => { if (f) dt.items.add(f); });
            const slot = document.getElementById('fileSlot');
            slot.innerHTML = '';
            const inp = document.createElement('input');
            inp.type = 'file'; inp.name = 'photos[]'; inp.multiple = true;
            inp.style.display = 'none'; inp.files = dt.files;
            slot.appendChild(inp);
        }

        document.getElementById('uForm').addEventListener('submit', async e => {
            e.preventDefault();
            await buildFileSlot();
            e.target.submit();
        });

        function setVis(v) {
            document.querySelector(`input[value="${v}"]`).checked = true;
            const pub = document.getElementById('vPub');
            const prv = document.getElementById('vPrv');
            if (v === 'public') {
                pub.style.borderColor = 'rgba(124,58,237,.5)'; pub.style.background = 'rgba(124,58,237,.08)';
                prv.style.borderColor = 'rgba(255,255,255,.1)'; prv.style.background = 'rgba(255,255,255,.03)';
            } else {
                prv.style.borderColor = 'rgba(124,58,237,.5)'; prv.style.background = 'rgba(124,58,237,.08)';
                pub.style.borderColor = 'rgba(255,255,255,.1)'; pub.style.background = 'rgba(255,255,255,.03)';
            }
        }
    </script>
@endpush