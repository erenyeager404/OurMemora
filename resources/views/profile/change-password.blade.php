@extends('layouts.app')
@section('title', 'Ganti Password')

@section('content')
    <div class="change-password-page">

        <a href="{{ route('profile') }}"
            class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-sm mb-8 transition-colors">
            ← Kembali ke Profile
        </a>

        <h2 class="text-2xl font-bold mb-8">🔒 Ganti Password</h2>

        <div class="change-password-card">
            <form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="form-label">Password Lama</label>
                    <input type="password" name="current_password" class="form-input" placeholder="Masukkan password lama">
                    @error('current_password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" id="newPw" class="form-input" placeholder="Minimal 6 karakter"
                        oninput="checkStrength(this.value)">
                    {{-- Strength bar --}}
                    <div class="flex gap-1 mt-2">
                        <div id="s1" class="h-1 flex-1 rounded-full bg-gray-700 transition-colors"></div>
                        <div id="s2" class="h-1 flex-1 rounded-full bg-gray-700 transition-colors"></div>
                        <div id="s3" class="h-1 flex-1 rounded-full bg-gray-700 transition-colors"></div>
                    </div>
                    <p id="sLabel" class="text-xs mt-1 text-gray-500"></p>
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="form-input"
                        placeholder="Ulangi password baru">
                </div>

                <button type="submit" class="btn-submit">Simpan Password</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function checkStrength(val) {
            let s = 0;
            if (val.length >= 6) s++;
            if (val.length >= 10) s++;
            if (/[A-Z]/.test(val) && /[0-9]/.test(val)) s++;
            const c = ['', 'bg-red-500', 'bg-yellow-500', 'bg-green-500'];
            const l = ['', 'Lemah', 'Sedang', 'Kuat'];
            [1, 2, 3].forEach(i => {
                const el = document.getElementById('s' + i);
                el.className = `h-1 flex-1 rounded-full transition-colors ${i <= s ? c[s] : 'bg-gray-700'}`;
            });
            const lbl = document.getElementById('sLabel');
            lbl.textContent = val.length > 0 ? l[s] : '';
            lbl.style.color = s === 1 ? '#ef4444' : s === 2 ? '#eab308' : s === 3 ? '#22c55e' : '';
        }
    </script>
@endpush