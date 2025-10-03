@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">{{ $event->title }}</h2>

    <div class="mb-2">
        <span class="font-semibold">Date:</span> {{ $event->date->format('Y-m-d') }}
    </div>
    @if($event->location)
        <div class="mb-2">
            <span class="font-semibold">Location:</span> {{ $event->location }}
        </div>
    @endif
    <div class="mb-4">
        <span class="font-semibold">Description:</span> {{ $event->description ?? 'No description' }}
    </div>

    <div class="flex justify-between items-center mt-4">
        <a href="{{ route('events.index') }}" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 focus:bg-red-600 active:bg-red-700 transition">Back to Events</a>
        <a href="{{ route('events.edit', $event) }}" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 focus:bg-red-600 active:bg-red-700 transition">Manage Event</a>
    </div>
</div>
@endsection
