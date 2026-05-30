@extends('layouts.admin')
@section('content')
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('admin.events.show', $event) }}"
            class="inline-flex items-center gap-2 text-sm text-gray-400 hover:text-white mb-7 px-4 py-2 rounded-xl transition-all"
            style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1)">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>
        <h2 class="text-2xl font-bold mb-7">Edit Event</h2>

        <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data"
            class="rounded-2xl p-6 space-y-5"
            style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08)">
            @csrf @method('PUT')
            @include('admin.events._form', ['event' => $event])
            <button type="submit" class="w-full py-3 rounded-xl text-sm font-medium text-white transition-all"
                style="background:rgba(220,38,38,.4);border:1px solid rgba(220,38,38,.5)">
                Simpan Perubahan
            </button>
        </form>
    </div>
@endsection