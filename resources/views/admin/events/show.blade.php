@extends('layouts.admin')

@section('content')
    <div>
        <a href="{{ route('admin.events.index') }}"
            class="inline-flex items-center gap-2 text-gray-400 hover:text-white text-sm mb-6 transition-all px-4 py-2 rounded-full"
            style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);">
            ← Semua Events
        </a>

        <div class="flex items-start gap-6 mb-8">
            @if($event->poster_path)
                <img src="{{ $event->poster_url }}" class="w-32 h-32 rounded-2xl object-cover flex-shrink-0">
            @endif
            <div class="flex-1">
                <h2 class="text-2xl font-bold mb-2">{{ $event->title }}</h2>
                <p class="text-gray-400 text-sm mb-3">{{ $event->description }}</p>
                <div class="flex items-center gap-4 text-sm text-gray-500">
                    <span>👥 {{ $totalParticipants }} peserta</span>
                    <span>🏆 Top {{ $event->max_winners }}</span>
                    <span>{{ $event->start_date->format('d M Y') }} — {{ $event->end_date->format('d M Y') }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.events.status', $event) }}">
                @csrf @method('PATCH')
                <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2 rounded-full text-sm text-white focus:outline-none"
                    style="background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.15);">
                    <option value="draft" {{ $event->status === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ $event->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="ended" {{ $event->status === 'ended' ? 'selected' : '' }}>Ended</option>
                </select>
            </form>
        </div>

        {{-- Leaderboard --}}
        <h3 class="text-lg font-semibold mb-4">🏆 Leaderboard ({{ $leaderboard->count() }} foto)</h3>

        <div class="space-y-2">
            @forelse($leaderboard as $i => $photo)
                @if($photo->files->isNotEmpty())
                    <div class="flex items-center gap-4 p-4 rounded-2xl"
                        style="background: {{ $i === 0 ? 'rgba(234,179,8,.08)' : ($i === 1 ? 'rgba(156,163,175,.05)' : 'rgba(255,255,255,.03)') }}; border: 1px solid {{ $i === 0 ? 'rgba(234,179,8,.2)' : 'rgba(255,255,255,.08)' }};">
                        <span
                            class="text-lg font-bold w-8 text-center {{ $i === 0 ? 'text-yellow-400' : ($i === 1 ? 'text-gray-400' : 'text-gray-600') }}">
                            {{ $i === 0 ? '👑' : ($i === 1 ? '🥈' : ($i === 2 ? '🥉' : '#' . ($i + 1))) }}
                        </span>
                        <img src="{{ $photo->files->first()->url }}" class="w-14 h-14 rounded-xl object-cover flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm truncate">{{ $photo->caption }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <img src="{{ $photo->user->avatar_url }}" class="w-4 h-4 rounded-full">
                                <span class="text-gray-500 text-xs">{{ $photo->user->name }}</span>
                            </div>
                        </div>
                        <span class="text-red-400 font-bold text-sm">♥ {{ $photo->likes_count }}</span>
                        <a href="{{ route('photos.show', $photo) }}"
                            class="px-3 py-1.5 rounded-full text-xs text-gray-400 hover:text-white transition-all"
                            style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);">
                            Lihat
                        </a>
                    </div>
                @endif
            @empty
                <p class="text-center text-gray-600 py-10">Belum ada foto di event ini</p>
            @endforelse
        </div>
    </div>
@endsection