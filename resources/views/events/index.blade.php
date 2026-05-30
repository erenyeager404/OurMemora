@extends(auth()->check() ? 'layouts.app' : 'layouts.guest')
@section('title','Events')
@section('content')
<div>
    <div class="flex items-center justify-between mb-7">
        <div>
            <h2 class="text-2xl font-bold">Events & Challenge</h2>
            <p class="text-gray-500 text-sm mt-0.5">Ikuti event, upload karya terbaik, dan menangkan!</p>
        </div>
    </div>

    @if($activeEvents->isNotEmpty())
        <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Sedang Berlangsung</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
            @foreach($activeEvents as $event)
                <a href="{{ route('events.show', $event) }}"
                   class="group rounded-2xl overflow-hidden transition-all block"
                   style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.09)">

                    @if($event->poster_path)
                        <div class="h-44 overflow-hidden">
                            <img src="{{ $event->poster_url }}" alt="{{ $event->title }}"
                                 class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-500">
                        </div>
                    @else
                        <div class="h-44 flex items-center justify-center"
                             style="background:linear-gradient(135deg,rgba(124,58,237,.25),rgba(59,130,246,.15))">
                            <svg class="w-16 h-16 text-violet-400 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                    @endif

                    <div class="p-4">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h4 class="font-semibold text-[14px] leading-snug">{{ $event->title }}</h4>
                            @if($event->status === 'voting')
                                <span class="flex-shrink-0 px-2 py-0.5 text-[10px] rounded-full text-yellow-400"
                                      style="background:rgba(234,179,8,.15);border:1px solid rgba(234,179,8,.3)">Voting</span>
                            @else
                                <span class="flex-shrink-0 px-2 py-0.5 text-[10px] rounded-full text-green-400 flex items-center gap-1"
                                      style="background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.25)">
                                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span>
                                    Aktif
                                </span>
                            @endif
                        </div>

                        @if($event->description)
                            <p class="text-gray-500 text-xs mb-3 line-clamp-2">{{ $event->description }}</p>
                        @endif

                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $event->participations_count }} peserta
                            </span>
                            @if($event->status === 'active')
                                <span class="text-violet-400 font-medium">
                                    {{ $event->daysRemaining() > 0 ? $event->daysRemaining().' hari lagi' : 'Hari terakhir!' }}
                                </span>
                            @endif
                        </div>

                        @if($event->auto_tag)
                            <div class="mt-3">
                                <span class="px-2.5 py-1 text-[11px] rounded-full text-violet-300"
                                      style="background:rgba(124,58,237,.12);border:1px solid rgba(124,58,237,.25)">
                                    #{{ $event->auto_tag }}
                                </span>
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-16 text-gray-600 mb-10 rounded-2xl"
             style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06)">
            <svg class="w-14 h-14 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
            </svg>
            <p class="text-gray-500 text-lg">Belum ada event aktif</p>
            <p class="text-sm mt-1">Pantau terus untuk event berikutnya!</p>
        </div>
    @endif

    @if($endedEvents->isNotEmpty())
        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-4">Sudah Selesai</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($endedEvents as $event)
                <a href="{{ route('events.show', $event) }}"
                   class="group flex gap-4 p-4 rounded-2xl transition-all"
                   style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06)">
                    @if($event->poster_path)
                        <img src="{{ $event->poster_url }}" class="w-16 h-16 rounded-xl object-cover flex-shrink-0 grayscale opacity-60 group-hover:opacity-80 transition-opacity">
                    @else
                        <div class="w-16 h-16 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background:rgba(255,255,255,.04)">
                            <svg class="w-7 h-7 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3l14 9-14 9V3z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-sm text-gray-400 truncate">{{ $event->title }}</p>
                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-600">
                            <span>{{ $event->participations_count }} peserta</span>
                            <span>{{ $event->end_date->format('d M Y') }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection