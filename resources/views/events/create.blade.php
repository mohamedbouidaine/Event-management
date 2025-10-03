@extends('layouts.app')

@section('content')
    <!-- المحتوى هنا -->

    <div class="max-w-2xl mx-auto p-6 bg-white shadow rounded mt-6">
        <h1 class="text-2xl font-bold mb-4">Add New Event</h1>

        <!-- عرض رسائل الخطأ -->
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>- {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

<form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-4">
        <label class="block font-semibold mb-1">Event Title</label>
        <input type="text" name="title" placeholder="Event Title"
               class="w-full border rounded p-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-1">Description</label>
        <textarea name="description" placeholder="Description"
                  class="w-full border rounded p-2" required></textarea>
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-1">Date</label>
        <input type="date" name="date" class="w-full border rounded p-2" required>
    </div>

    <div class="mb-4">
        <label class="block font-semibold mb-1">Location</label>
        <input type="text" name="location" placeholder="Event Location"
               class="w-full border rounded p-2">
    </div>

    <!-- 🔹 هنا نضيف رفع الصورة -->
    <div class="mb-4">
        <label class="block font-semibold mb-1">Event Image</label>
        <input type="file" name="image" class="w-full border rounded p-2">
    </div>

    <button type="submit"
        style="background:red; color:white; font-size:20px; padding:10px; width:200px;">
        Add Event
    </button>
</form>


        </form>
    </div>
@endsection
