@extends(auth()->check() ? 'layouts.app' : 'layouts.guest')
@section('title', 'Events')

@section('content')
    <div>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold">◎ Events & Challenge</h2>
                <p class="text-gray-500 text-sm mt-1">Ikuti event, upload foto terbaik, dan menangkan!</p>
            </div>
        </div>

        {{-- Active Events --}}
        @if($activeEvents->isNotEmpty())
            <h3 class="text-lg font-semibold mb-4 text-white">🔥 Event Aktif</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
                @foreach($activeEvents as $event)
                    <a href="{{ route('events.show', $event) }}" class="group rounded-2xl overflow-hidden transition-all block"
                        style="background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1); backdrop-filter: blur(12px);">

                        {{-- Poster --}}
                        @if($event->poster_path)
                            <div class="h-40 overflow-hidden">
                                <img src="{{ $event->poster_url }}" alt="{{ $event->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                        @else
                            <div class="h-40 flex items-center justify-center text-5xl"
                                style="background: linear-gradient(135deg, rgba(124,58,237,.3), rgba(59,130,246,.2));">
                                🏆
                            </div>
                        @endif

                        <div class="p-4">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <h4 class="font-semibold text-sm leading-snug">{{ $event->title }}</h4>
                                <span class="flex-shrink-0 px-2 py-0.5 text-xs rounded-full text-green-400"
                                    style="background: rgba(16,185,129,.15); border: 1px solid rgba(16,185,129,.3);">
                                    Aktif
                                </span>
                            </div>

                            @if($event->description)
                                <p class="text-gray-500 text-xs mb-3 line-clamp-2">{{ $event->description }}</p>
                            @endif

                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span>👥 {{ $event->participations_count }} peserta</span>
                                <span class="text-violet-400 font-medium">
                                    {{ $event->daysRemaining() > 0 ? $event->daysRemaining() . ' hari tersisa' : 'Berakhir hari ini!' }}
                                </span>
                            </div>

                            @if($event->auto_tag)
                                <div class="mt-3">
                                    <span class="px-2.5 py-1 text-xs rounded-full text-violet-300"
                                        style="background: rgba(124,58,237,.15); border: 1px solid rgba(124,58,237,.3);">
                                        #{{ $event->auto_tag }}
                                    </span>
                                </div>
                            @endif

                            @auth
                                <a href="{{ route('upload') }}"
                                    class="block mt-4 w-full py-2.5 rounded-full text-xs text-center font-medium text-white transition-all"
                                    style="background: rgba(124,58,237,.4); border: 1px solid rgba(124,58,237,.5);"
                                    onclick="event.stopPropagation()">
                                    + Ikuti Event
                                </a>
                            @endauth
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 text-gray-600 mb-10">
                <p class="text-5xl mb-3">◎</p>
                <p class="text-lg">Belum ada event aktif saat ini</p>
                <p class="text-sm mt-1">Pantau terus untuk event berikutnya!</p>
            </div>
        @endif

        {{-- Ended Events --}}
        @if($endedEvents->isNotEmpty())
            <h3 class="text-lg font-semibold mb-4 text-gray-400">📁 Event Selesai</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($endedEvents as $event)
                    <a href="{{ route('events.show', $event) }}"
                        class="group rounded-2xl overflow-hidden transition-all block opacity-70 hover:opacity-100"
                        style="background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.08);">
                        @if($event->poster_path)
                            <div class="h-32 overflow-hidden">
                                <img src="{{ $event->poster_url }}" alt="{{ $event->title }}"
                                    class="w-full h-full object-cover grayscale">
                            </div>
                        @else
                            <div class="h-32 flex items-center justify-center text-4xl text-gray-600"
                                style="background: rgba(255,255,255,.03);">🏁</div>
                        @endif
                        <div class="p-4">
                            <h4 class="font-semibold text-sm text-gray-300">{{ $event->title }}</h4>
                            <div class="flex items-center justify-between mt-2 text-xs text-gray-600">
                                <span>👥 {{ $event->participations_count }} peserta</span>
                                <span>{{ $event->end_date->format('d M Y') }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection