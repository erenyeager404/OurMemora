@extends('layouts.guest')
@section('title', 'Verifikasi Email')
@section('content')
    <div class="min-h-screen flex items-center justify-center px-6 pt-16">
        <div class="w-full max-w-sm text-center">
            <div class="w-20 h-20 mx-auto mb-6 rounded-full flex items-center justify-center"
                style="background:rgba(124,58,237,.15);border:1px solid rgba(124,58,237,.3)">
                <svg class="w-10 h-10 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold mb-2">Cek Email Kamu</h1>
            <p class="text-gray-500 text-sm mb-1">Link verifikasi dikirim ke</p>
            <p class="text-violet-400 font-semibold mb-6">{{ auth()->user()->email }}</p>
            <p class="text-gray-600 text-xs mb-8 leading-relaxed">
                Buka email dan klik tombol verifikasi.<br>
                Tidak ada? Cek folder <span class="text-gray-400">Spam</span>.
            </p>
            @foreach(['success', 'warning', 'message'] as $k)
                @if(session($k))
                    <div class="mb-3 p-3 rounded-xl text-sm {{ $k === 'success' ? 'text-green-300' : 'text-yellow-300' }}"
                        style="{{ $k === 'success' ? 'background:rgba(6,78,59,.3);border:1px solid rgba(16,185,129,.2)' : 'background:rgba(92,54,0,.3);border:1px solid rgba(234,179,8,.2)' }}">
                        {{ session($k) }}
                    </div>
                @endif
            @endforeach
            <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
                @csrf
                <button type="submit" class="w-full py-3 rounded-xl text-sm font-medium text-white transition-all"
                    style="background:rgba(124,58,237,.4);border:1px solid rgba(124,58,237,.5)">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-600 hover:text-gray-400 transition-colors">
                    ← Keluar dan ganti akun
                </button>
            </form>
        </div>
    </div>
@endsection