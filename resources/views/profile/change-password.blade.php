@extends('layouts.app')
@section('title', 'Ganti Password')
@section('content')
    <div class="max-w-md mx-auto">
        <a href="{{ route('profile') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white mb-8 px-4 py-2 rounded-xl transition-all"
            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Profile
        </a>
        <h2 class="text-2xl font-bold mb-7">Ganti Password</h2>
        <div class="rounded-2xl p-7" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.09)">
            <form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Password
                        Lama</label>
                    <input type="password" name="current_password" placeholder="Masukkan password lama"
                        class="w-full px-4 py-2.5 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none"
                        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                    @error('current_password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Password
                        Baru</label>
                    <input type="password" name="password" id="nPw" placeholder="Minimal 6 karakter"
                        oninput="chk(this.value)"
                        class="w-full px-4 py-2.5 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none"
                        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                    <div class="flex gap-1 mt-2">
                        <div id="s1" class="h-1 flex-1 rounded-full bg-white/10 transition-colors"></div>
                        <div id="s2" class="h-1 flex-1 rounded-full bg-white/10 transition-colors"></div>
                        <div id="s3" class="h-1 flex-1 rounded-full bg-white/10 transition-colors"></div>
                    </div>
                    <p id="sL" class="text-[11px] mt-1"></p>
                    @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-[11px] text-gray-500 mb-1.5 font-semibold uppercase tracking-wide">Konfirmasi
                        Password</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                        class="w-full px-4 py-2.5 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none"
                        style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                </div>
                <button type="submit" class="w-full py-3 rounded-xl text-sm font-medium text-white transition-all mt-2"
                    style="background:rgba(124,58,237,.4);border:1px solid rgba(124,58,237,.5)">
                    Simpan Password
                </button>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        function chk(v) {
            let s = 0;
            if (v.length >= 6) s++; if (v.length >= 10) s++; if (/[A-Z]/.test(v) && /[0-9]/.test(v)) s++;
            const c = ['', 'bg-red-500', 'bg-yellow-500', 'bg-green-500'];
            const l = ['', 'Lemah', 'Sedang', 'Kuat'];
            [1, 2, 3].forEach(i => {
                const el = document.getElementById('s' + i);
                el.className = `h-1 flex-1 rounded-full transition-colors ${i <= s ? c[s] : 'bg-white/10'}`;
            });
            const lb = document.getElementById('sL');
            lb.textContent = v ? l[s] : ''; lb.style.color = s === 1 ? '#ef4444' : s === 2 ? '#eab308' : '#22c55e';
        }
    </script>
@endpush