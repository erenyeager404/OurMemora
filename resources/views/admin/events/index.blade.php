@extends('layouts.admin')
@section('content')
    <div>
        <div class="flex items-center justify-between mb-7">
            <h2 class="text-2xl font-bold">Manajemen Event</h2>
            <a href="{{ route('admin.events.create') }}"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-white transition-all"
                style="background:rgba(220,38,38,.4);border:1px solid rgba(220,38,38,.5)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Buat Event
            </a>
        </div>

        <div class="space-y-2">
            @forelse($events as $event)
                <div class="flex items-center gap-4 p-4 rounded-2xl transition-all"
                    style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07)">
                    @if($event->poster_path)
                        <img src="{{ $event->poster_url }}" class="w-14 h-14 rounded-xl object-cover flex-shrink-0">
                    @else
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0"
                            style="background:rgba(124,58,237,.2)">
                            <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-[14px]">{{ $event->title }}</p>
                        <p class="text-gray-500 text-xs mt-0.5">
                            {{ $event->start_date->format('d M Y') }} — {{ $event->end_date->format('d M Y') }}
                            · {{ $event->participations_count }} foto
                        </p>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        {{-- Status badge --}}
                        @php
                            $sc = match ($event->status) {
                                'active' => ['rgba(16,185,129,.15)', 'rgba(16,185,129,.3)', 'text-green-400'],
                                'voting' => ['rgba(234,179,8,.12)', 'rgba(234,179,8,.3)', 'text-yellow-400'],
                                'ended' => ['rgba(255,255,255,.05)', 'rgba(255,255,255,.1)', 'text-gray-500'],
                                default => ['rgba(255,255,255,.05)', 'rgba(255,255,255,.1)', 'text-gray-600'],
                            };
                        @endphp
                        <span class="px-2.5 py-1 text-[11px] rounded-full font-medium {{ $sc[2] }}"
                            style="background:{{ $sc[0] }};border:1px solid {{ $sc[1] }}">
                            {{ $event->status_label }}
                        </span>

                        {{-- Quick status change --}}
                        <form method="POST" action="{{ route('admin.events.status', $event) }}">
                            @csrf @method('PATCH')
                            <select name="status" onchange="this.form.submit()"
                                class="text-xs text-gray-400 rounded-lg px-2 py-1.5 focus:outline-none transition-all"
                                style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                                <option value="draft" {{ $event->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="active" {{ $event->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="voting" {{ $event->status === 'voting' ? 'selected' : '' }}>Voting</option>
                                <option value="ended" {{ $event->status === 'ended' ? 'selected' : '' }}>Ended</option>
                            </select>
                        </form>

                        <a href="{{ route('admin.events.show', $event) }}"
                            class="p-2 rounded-lg text-gray-400 hover:text-white transition-all"
                            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>

                        <a href="{{ route('admin.events.edit', $event) }}"
                            class="p-2 rounded-lg text-gray-400 hover:text-white transition-all"
                            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </a>

                        <form method="POST" action="{{ route('admin.events.destroy', $event) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Hapus event ini?')"
                                class="p-2 rounded-lg text-red-400 hover:bg-red-900/20 transition-all"
                                style="border:1px solid rgba(239,68,68,.2)">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 text-gray-600 rounded-2xl"
                    style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06)">
                    <p class="text-gray-500">Belum ada event. Buat yang pertama!</p>
                </div>
            @endforelse
        </div>
        <div class="mt-6">{{ $events->links() }}</div>
    </div>
@endsection