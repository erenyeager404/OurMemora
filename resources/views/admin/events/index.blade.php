@extends('layouts.admin')

@section('content')
    <div>
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold">◎ Manajemen Event</h2>
            <a href="{{ route('admin.events.create') }}"
                class="flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-medium text-white"
                style="background: rgba(220,38,38,.5); border: 1px solid rgba(220,38,38,.6);">
                ＋ Buat Event
            </a>
        </div>

        <div class="space-y-3">
            @forelse($events as $event)
                <div class="flex items-center gap-4 p-4 rounded-2xl"
                    style="background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.08);">
                    @if($event->poster_path)
                        <img src="{{ $event->poster_url }}" class="w-16 h-16 rounded-xl object-cover flex-shrink-0">
                    @else
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center text-2xl flex-shrink-0"
                            style="background: rgba(124,58,237,.2);">🏆</div>
                    @endif

                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm">{{ $event->title }}</p>
                        <p class="text-gray-500 text-xs mt-0.5">
                            {{ $event->start_date->format('d M Y') }} — {{ $event->end_date->format('d M Y') }}
                            · {{ $event->participations_count }} foto
                        </p>
                        @if($event->auto_tag)
                            <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded-full text-violet-300"
                                style="background: rgba(124,58,237,.15);">#{{ $event->auto_tag }}</span>
                        @endif
                    </div>

                    {{-- Status badge + toggle --}}
                    <div class="flex items-center gap-3">
                        <span
                            class="px-3 py-1 rounded-full text-xs font-medium
                                {{ $event->status === 'active' ? 'text-green-400' : ($event->status === 'ended' ? 'text-gray-500' : 'text-yellow-500') }}"
                            style="{{ $event->status === 'active' ? 'background: rgba(16,185,129,.15); border: 1px solid rgba(16,185,129,.3);' : ($event->status === 'ended' ? 'background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1);' : 'background: rgba(234,179,8,.1); border: 1px solid rgba(234,179,8,.3);') }}">
                            {{ ucfirst($event->status) }}
                        </span>

                        <a href="{{ route('admin.events.show', $event) }}"
                            class="px-3 py-1.5 rounded-full text-xs transition-all text-gray-300 hover:text-white"
                            style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);">
                            Lihat
                        </a>

                        <form method="POST" action="{{ route('admin.events.status', $event) }}" class="inline">
                            @csrf @method('PATCH')
                            <select name="status" onchange="this.form.submit()"
                                class="px-3 py-1.5 rounded-full text-xs text-gray-300 focus:outline-none"
                                style="background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);">
                                <option value="draft" {{ $event->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="active" {{ $event->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="ended" {{ $event->status === 'ended' ? 'selected' : '' }}>Ended</option>
                            </select>
                        </form>

                        <form method="POST" action="{{ route('admin.events.destroy', $event) }}">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Hapus event ini?')"
                                class="px-3 py-1.5 rounded-full text-xs text-red-400 transition-all"
                                style="background: rgba(220,38,38,.1); border: 1px solid rgba(220,38,38,.3);">
                                🗑
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 text-gray-600">
                    <p class="text-5xl mb-3">◎</p>
                    <p>Belum ada event. Buat event pertama!</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $events->links() }}</div>
    </div>
@endsection