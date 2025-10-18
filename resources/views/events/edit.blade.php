@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto mt-10 p-6 bg-white rounded shadow">

    <!-- تعديل بيانات الحدث -->
    <h2 class="text-2xl font-bold mb-4">Edit Event: {{ $event->title }}</h2>
    <form action="{{ route('events.update', $event) }}" method="POST" class="mb-6">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="block font-semibold mb-1">Event Title</label>
            <input type="text" name="title" value="{{ $event->title }}" class="w-full border p-2 rounded" required>
        </div>
        <div class="mb-3">
            <label class="block font-semibold mb-1">Date</label>
            <input type="date" name="date" value="{{ $event->date->format('Y-m-d') }}" class="w-full border p-2 rounded" required>
        </div>
        <div class="mb-3">
            <label class="block font-semibold mb-1">Location</label>
            <input type="text" name="location" value="{{ $event->location ?? '' }}" class="w-full border p-2 rounded">
        </div>
        <div class="mb-3">
            <label class="block font-semibold mb-1">Description</label>
            <textarea name="description" class="w-full border p-2 rounded">{{ $event->description }}</textarea>
        </div>
        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700 transition">
            Update Event
        </button>
    </form>

<hr class="my-6">

<!-- إدارة المشاركين -->
<h3 class="text-xl font-bold mb-3">Participants</h3>

@if ($errors->any())
    <div class="text-red-600 mb-4">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="text-green-600 mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- نموذج إضافة مشارك جديد يدويًا -->
<form action="{{ route('participants.store', $event->id) }}" method="POST" class="mb-4">
    @csrf
    <div class="flex flex-col md:flex-row gap-2">
        <input type="text" name="name" placeholder="Participant Name"
            class="border p-2 rounded w-full md:w-1/3"
            value="{{ old('name') }}" required>

        <input type="email" name="email" placeholder="Email"
            class="border p-2 rounded w-full md:w-1/3"
            value="{{ old('email') }}" required>

        <input type="text" name="phone" placeholder="Phone Number"
            class="border p-2 rounded w-full md:w-1/3"
            value="{{ old('phone') }}">

        <button type="submit"
                class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700 transition">
            Add Participant
        </button>
    </div>
</form>

<!-- رفع ملف Excel -->
<form action="{{ route('participants.uploadExcel', $event->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 mb-4">
    @csrf
    <input type="file" name="file" required class="border p-2 rounded">
    <button type="submit" class="bg-red-600 text-white font-semibold px-4 py-2 rounded shadow hover:bg-red-700 transition">
        Upload Excel File
    </button>
</form>

<!-- Modal لاختيار الأعمدة -->
@if(session()->has('first_row') && count(session('first_row')) > 0)
    <button id="showMappingModal" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition mb-2">
        Select Columns
    </button>

    <div id="mappingModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-2xl w-96 shadow-lg">
            <h2 class="text-xl font-semibold mb-4 text-center text-black">Select Columns from First Row</h2>

            @php $firstRow = session('first_row', []); @endphp

            <form method="POST" action="{{ route('participants.mapColumns', $event->id) }}">
                @csrf
                <label>Name:</label>
                <select name="name_value" class="w-full mb-2 p-2 border">
                    @foreach($firstRow as $index => $cell)
                        <option value="{{ $index }}">{{ $cell }}</option>
                    @endforeach
                </select>

                <label>Email:</label>
                <select name="email_value" class="w-full mb-2 p-2 border">
                    @foreach($firstRow as $index => $cell)
                        <option value="{{ $index }}">{{ $cell }}</option>
                    @endforeach
                </select>

                <label>Phone:</label>
                <select name="phone_value" class="w-full mb-4 p-2 border">
                    @foreach($firstRow as $index => $cell)
                        <option value="{{ $index }}">{{ $cell }}</option>
                    @endforeach
                </select>

                <button type="submit" class="w-full bg-gray-500 text-white py-2 rounded mb-2">
                    Save Column Mapping
                </button>
            </form>

            @if(session()->has('column_mapping') && count(session('uploaded_rows', [])) > 0)
                <form action="{{ route('participants.addMappedParticipants', $event->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">
                        Add Participants Based on Mapping
                    </button>
                </form>
            @endif

            <button type="button" id="closeMappingModal" class="w-full mt-2 bg-gray-500 text-white py-2 rounded">
                Cancel
            </button>
        </div>
    </div>

    <script>
        const showBtn = document.getElementById('showMappingModal');
        const closeBtn = document.getElementById('closeMappingModal');
        const modal = document.getElementById('mappingModal');

        showBtn.addEventListener('click', () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        });

        closeBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
    </script>
@endif

<!-- تنزيل Excel أو PDF -->
<div class="flex gap-2 mt-4 mb-4">
    <a href="{{ route('participants.export', $event->id) }}" class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700">
        Download Participants Excel
    </a>

    <a href="{{ route('events.pdf', $event->id) }}" target="_blank" class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700">
        Export PDF Participants
    </a>

    <form action="{{ route('participants.destroyAll', $event->id) }}" method="POST" class="inline-block">
        @csrf
        @method('DELETE')
        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700" onclick="return confirm('Are you sure you want to remove all participants?');">
            Remove All Participants
        </button>
    </form>
</div>

<!-- قائمة المشاركين -->
<ul class="mt-4 border rounded divide-y">
    @foreach($event->participants as $participant)
        <li class="flex justify-between items-center p-2">
            <div>
                <strong>{{ $participant->name }}</strong> ({{ $participant->email }})
                @if($participant->phone)
                    - {{ $participant->phone }}
                @else
                    - No phone
                @endif
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('participants.destroy', [$event->id, $participant->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-2 py-1 rounded shadow hover:bg-red-700">
                        Remove
                    </button>
                </form>
            </div>
        </li>
    @endforeach
</ul>

</div>
@endsection
