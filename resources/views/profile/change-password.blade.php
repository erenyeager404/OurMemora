@extends('layouts.app')
@section('title', 'Ganti Password')

@section('content')
    <div class="max-w-md mx-auto">
        <a href="{{ route('profile') }}"
            class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-sm mb-8 transition-colors">
            ← Kembali ke Profile
        </a>

        <h2 class="text-2xl font-bold mb-8">🔒 Ganti Password</h2>

        <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-8">
            <form method="POST" action="{{ route('profile.password') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Password Lama</label>
                    <input type="password" name="current_password" placeholder="Masukkan password lama"
                        class="w-full px-4 py-2.5 bg-gray-800/80 border border-gray-700 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors">
                    @error('current_password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Password Baru</label>
                    <input type="password" name="password" id="nPw" placeholder="Min. 6 karakter" oninput="chk(this.value)"
                        class="w-full px-4 py-2.5 bg-gray-800/80 border border-gray-700 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors">
                    <div class="flex gap-1 mt-2">
                        <div id="s1" class="h-1 flex-1 rounded-full bg-gray-700 transition-colors"></div>
                        <div id="s2" class="h-1 flex-1 rounded-full bg-gray-700 transition-colors"></div>
                        <div id="s3" class="h-1 flex-1 rounded-full bg-gray-700 transition-colors"></div>
                    </div>
                    <p id="sLbl" class="text-xs mt-1"></p>
                    @error('password') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs text-gray-400 mb-1.5 font-medium">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi password baru"
                        class="w-full px-4 py-2.5 bg-gray-800/80 border border-gray-700 rounded-xl text-sm text-white placeholder-gray-600 focus:outline-none focus:border-violet-500 transition-colors">
                </div>
                <button type="submit"
                    class="w-full py-3 bg-violet-600 hover:bg-violet-700 rounded-xl text-sm font-medium text-white transition-colors">
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
            if (v.length >= 6) s++;
            if (v.length >= 10) s++;
            if (/[A-Z]/.test(v) && /[0-9]/.test(v)) s++;
            const c = ['', 'bg-red-500', 'bg-yellow-500', 'bg-green-500'];
            const l = ['', 'Lemah', 'Sedang', 'Kuat'];
            [1, 2, 3].forEach(i => {
                const el = document.getElementById('s' + i);
                el.className = `h-1 flex-1 rounded-full transition-colors ${i <= s ? c[s] : 'bg-gray-700'}`;
            });
            const lbl = document.getElementById('sLbl');
            lbl.textContent = v ? l[s] : '';
            lbl.style.color = s === 1 ? '#ef4444' : s === 2 ? '#eab308' : '#22c55e';
        }
    </script>
@endpush