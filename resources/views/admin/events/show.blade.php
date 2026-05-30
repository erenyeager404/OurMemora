@extends('layouts.admin')
@section('content')
    <div>
        <div class="flex items-center gap-4 mb-7">
            <a href="{{ route('admin.events.index') }}"
                class="flex items-center gap-2 text-sm text-gray-400 hover:text-white px-4 py-2 rounded-xl transition-all"
                style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
            <h2 class="text-2xl font-bold flex-1 truncate">{{ $event->title }}</h2>
            <a href="{{ route('admin.events.edit', $event) }}"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm text-gray-300 hover:text-white transition-all"
                style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Edit
            </a>
            <form method="POST" action="{{ route('admin.events.status', $event) }}">
                @csrf @method('PATCH')
                <select name="status" onchange="this.form.submit()"
                    class="text-sm text-white px-4 py-2 rounded-xl focus:outline-none transition-all"
                    style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12)">
                    <option value="draft" {{ $event->status === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ $event->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="voting" {{ $event->status === 'voting' ? 'selected' : '' }}>Voting</option>
                    <option value="ended" {{ $event->status === 'ended' ? 'selected' : '' }}>Ended</option>
                </select>
            </form>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-4 mb-7">
            <div class="p-4 rounded-2xl" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08)">
                <p class="text-[11px] text-gray-500 uppercase tracking-wide mb-1">Peserta</p>
                <p class="text-3xl font-bold text-violet-400">{{ $totalParticipants }}</p>
            </div>
            <div class="p-4 rounded-2xl" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08)">
                <p class="text-[11px] text-gray-500 uppercase tracking-wide mb-1">Foto Masuk</p>
                <p class="text-3xl font-bold text-blue-400">{{ $leaderboard->count() }}</p>
            </div>
            <div class="p-4 rounded-2xl" style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08)">
                <p class="text-[11px] text-gray-500 uppercase tracking-wide mb-1">Target Pemenang</p>
                <p class="text-3xl font-bold text-yellow-400">{{ $event->max_winners }}</p>
            </div>
        </div>

        {{-- Leaderboard --}}
        <h3 class="font-semibold mb-4 text-gray-300">Leaderboard ({{ $leaderboard->count() }} foto)</h3>
        <div class="space-y-2">
            @forelse($leaderboard as $i => $photo)
                @if($photo->files->isNotEmpty())
                    <div class="flex items-center gap-4 p-4 rounded-2xl transition-all"
                        style="background:{{ $i === 0 ? 'rgba(234,179,8,.07)' : ($i === 1 ? 'rgba(156,163,175,.04)' : 'rgba(255,255,255,.02)') }};border:1px solid {{ $i === 0 ? 'rgba(234,179,8,.2)' : 'rgba(255,255,255,.06)' }}">

                        <div class="w-8 text-center flex-shrink-0">
                            @if($i === 0) <span class="text-lg">👑</span>
                            @elseif($i === 1) <span class="text-lg">🥈</span>
                            @elseif($i === 2) <span class="text-lg">🥉</span>
                            @else <span class="text-sm text-gray-500 font-medium">#{{ $i + 1 }}</span>
                            @endif
                        </div>

                        <img src="{{ $photo->files->first()->thumb_url }}" class="w-14 h-14 rounded-xl object-cover flex-shrink-0">

                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm truncate">{{ $photo->caption }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <img src="{{ $photo->user->avatar_url }}" class="w-4 h-4 rounded-full">
                                <span class="text-gray-500 text-xs">{{ $photo->user->name }}</span>
                            </div>
                        </div>

                        <span class="text-red-400 font-bold text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            {{ $photo->likes_count }}
                        </span>

                        <a href="{{ route('photos.show', $photo) }}"
                            class="flex items-center px-3 py-1.5 rounded-lg text-xs text-gray-400 hover:text-white transition-all"
                            style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1)">
                            Lihat
                        </a>
                    </div>
                @endif
            @empty
                <p class="text-center text-gray-600 py-10">Belum ada foto</p>
            @endforelse
        </div>
    </div>
@endsection