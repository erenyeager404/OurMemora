@extends('layouts.admin')

@section('content')
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('admin.events.index') }}"
            class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-sm mb-8 transition-all px-4 py-2 rounded-full"
            style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);">
            ← Kembali
        </a>

        <h2 class="text-2xl font-bold mb-8">◎ Buat Event Baru</h2>

        <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data"
            class="rounded-2xl p-6 space-y-5"
            style="background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.1);">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Judul Event *</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        placeholder="Contoh: 20 Foto Sunset Terbaik Mei 2026"
                        class="w-full px-4 py-3 rounded-2xl text-sm text-white placeholder-gray-600 focus:outline-none"
                        style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);">
                    @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="col-span-2">
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Poster Event</label>
                    <input type="file" name="poster" accept="image/*"
                        class="w-full px-4 py-3 rounded-2xl text-sm text-gray-300"
                        style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);">
                </div>

                <div class="col-span-2">
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Jelaskan event ini..."
                        class="w-full px-4 py-3 rounded-2xl text-sm text-white placeholder-gray-600 focus:outline-none resize-none"
                        style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Tanggal Mulai *</label>
                    <input type="datetime-local" name="start_date" value="{{ old('start_date') }}"
                        class="w-full px-4 py-3 rounded-2xl text-sm text-white focus:outline-none"
                        style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);">
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Tanggal Berakhir *</label>
                    <input type="datetime-local" name="end_date" value="{{ old('end_date') }}"
                        class="w-full px-4 py-3 rounded-2xl text-sm text-white focus:outline-none"
                        style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);">
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Jumlah Pemenang</label>
                    <input type="number" name="max_winners" value="{{ old('max_winners', 3) }}" min="1" max="100"
                        class="w-full px-4 py-3 rounded-2xl text-sm text-white focus:outline-none"
                        style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);">
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Auto Tag <span
                            class="text-gray-600">(tanpa #)</span></label>
                    <input type="text" name="auto_tag" value="{{ old('auto_tag') }}" placeholder="SunsetMay2026"
                        class="w-full px-4 py-3 rounded-2xl text-sm text-white placeholder-gray-600 focus:outline-none"
                        style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);">
                    <p class="text-gray-600 text-xs mt-1">Otomatis ditambahkan ke foto peserta</p>
                </div>

                <div class="col-span-2">
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Aturan Event</label>
                    <textarea name="rules" rows="3" placeholder="Syarat dan ketentuan event..."
                        class="w-full px-4 py-3 rounded-2xl text-sm text-white placeholder-gray-600 focus:outline-none resize-none"
                        style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);">{{ old('rules') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Status</label>
                    <select name="status" class="w-full px-4 py-3 rounded-2xl text-sm text-white focus:outline-none"
                        style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12);">
                        <option value="draft">Draft</option>
                        <option value="active">Active</option>
                        <option value="ended">Ended</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="w-full py-3 rounded-full text-sm font-medium text-white transition-all"
                style="background: linear-gradient(135deg, #DC2626, #B91C1C); border: 1px solid rgba(220,38,38,.5);">
                Buat Event
            </button>
        </form>
    </div>
@endsection