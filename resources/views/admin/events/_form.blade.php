@php $e = $event; @endphp

<div class="grid grid-cols-2 gap-4">

    <div class="col-span-2">
        <label class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Judul Event
            *</label>
        <input type="text" name="title" value="{{ old('title', $e?->title) }}"
            placeholder="Contoh: 20 Foto Sunset Terbaik Mei 2026"
            class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none"
            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
        @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="col-span-2">
        <label class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">
            Poster <span class="normal-case font-normal text-gray-600">(opsional)</span>
        </label>
        @if($e?->poster_path)
            <div class="mb-2">
                <img src="{{ $e->poster_url }}" class="h-24 rounded-xl object-cover">
                <p class="text-xs text-gray-600 mt-1">Upload baru untuk mengganti</p>
            </div>
        @endif
        <input type="file" name="poster" accept="image/*" class="w-full px-4 py-2.5 rounded-xl text-sm text-gray-300"
            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
    </div>

    <div class="col-span-2">
        <label class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Deskripsi</label>
        <textarea name="description" rows="3"
            class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none resize-none"
            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">{{ old('description', $e?->description) }}</textarea>
    </div>

    <div class="col-span-2">
        <label class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">
            Hadiah <span class="normal-case font-normal text-gray-600">(opsional)</span>
        </label>
        <textarea name="prize_description" rows="2"
            placeholder="Contoh: Sertifikat + featured di homepage selama 1 minggu"
            class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none resize-none"
            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">{{ old('prize_description', $e?->prize_description) }}</textarea>
    </div>

    <div>
        <label class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Tanggal Mulai
            *</label>
        <input type="datetime-local" name="start_date"
            value="{{ old('start_date', $e?->start_date?->format('Y-m-d\TH:i')) }}"
            class="w-full px-4 py-3 rounded-xl text-sm text-white focus:outline-none"
            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
        @error('start_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Tanggal Berakhir
            *</label>
        <input type="datetime-local" name="end_date" value="{{ old('end_date', $e?->end_date?->format('Y-m-d\TH:i')) }}"
            class="w-full px-4 py-3 rounded-xl text-sm text-white focus:outline-none"
            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
        @error('end_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Jumlah
            Pemenang</label>
        <input type="number" name="max_winners" value="{{ old('max_winners', $e?->max_winners ?? 3) }}" min="1"
            max="100" class="w-full px-4 py-3 rounded-xl text-sm text-white focus:outline-none"
            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
    </div>

    <div>
        <label class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">
            Auto Tag <span class="normal-case font-normal text-gray-600">(tanpa #)</span>
        </label>
        <input type="text" name="auto_tag" value="{{ old('auto_tag', $e?->auto_tag) }}" placeholder="SunsetMay2026"
            class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none"
            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
        <p class="text-[11px] text-gray-600 mt-1">Otomatis ditambahkan ke foto peserta</p>
    </div>

    <div class="col-span-2">
        <label class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Aturan Event</label>
        <textarea name="rules" rows="4" placeholder="Tulis syarat dan ketentuan event..."
            class="w-full px-4 py-3 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none resize-none"
            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">{{ old('rules', $e?->rules) }}</textarea>
    </div>

    <div>
        <label class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Status</label>
        <select name="status" class="w-full px-4 py-3 rounded-xl text-sm text-white focus:outline-none"
            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
            @foreach(['draft' => 'Draft (tersembunyi)', 'active' => 'Active (buka pendaftaran)', 'voting' => 'Voting (tutup submit)', 'ended' => 'Ended (selesai)'] as $val => $lbl)
                <option value="{{ $val }}" {{ old('status', $e?->status ?? 'draft') === $val ? 'selected' : '' }}>
                    {{ $lbl }}
                </option>
            @endforeach
        </select>
    </div>
</div>