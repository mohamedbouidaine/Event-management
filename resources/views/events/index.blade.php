@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto mt-10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">My Events</h2>
        <a href="{{ route('events.create') }}"
           class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700 focus:bg-red-700 active:bg-red-800 transition">
           Add New Event
        </a>
    </div>

    @if($events->count())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($events as $event)
                <div class="border rounded shadow p-4 bg-white hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold mb-2">{{ $event->title }}</h3>
                    <p class="mb-1"><strong>Date:</strong> {{ $event->date->format('Y-m-d') }}</p>
                    @if($event->location)
                        <p class="mb-2"><strong>Location:</strong> {{ $event->location }}</p>
                    @endif
                    <div class="flex justify-between items-center mt-4">
                        <a href="{{ route('events.edit', $event) }}"
                           class="bg-red-600 text-white px-3 py-1 rounded shadow hover:bg-red-700 focus:bg-red-700 active:bg-red-800 transition">
                           Manage
                        </a>

                        <form action="{{ route('events.destroy', $event) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this event?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 text-white px-3 py-1 rounded shadow hover:bg-red-700 focus:bg-red-700 active:bg-red-800 transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-600">No events found. Click "Add New Event" to create one.</p>
    @endif
</div>
@endsection
