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
        <button type="submit"
                class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700 focus:bg-red-700 active:bg-red-800 transition">
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
                class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700 focus:bg-red-700 active:bg-red-800 transition">
            Add Participant
        </button>
    </div>
</form>

<!-- مجموعة إدارة الملفات والأزرار -->
<div class="mb-6 space-y-2">

    <!-- رفع ملف Excel -->
    <form action="{{ route('participants.uploadExcel', $event->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
        @csrf
        <input type="file" name="file" required class="border p-2 rounded">
        <button type="submit"
                class="bg-red-600 text-white font-semibold px-4 py-2 rounded shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 transition">
            Upload Excel File
        </button>
    </form>

        <!-- إضافة المشاركين من الملف المرفوع -->
        @if(session()->has('uploaded_participants') && count(session('uploaded_participants')) > 0)
            <form action="{{ route('participants.addUploaded', $event->id) }}" method="POST" class="inline-block">
                @csrf
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 transition">
                    Add Participants from Uploaded File
                </button>
            </form>
        @endif






    <!-- تحميل جميع المشاركين كملف Excel -->
    <a href="{{ route('participants.export', $event->id) }}"
       class="bg-red-600 text-white font-semibold px-4 py-2 rounded shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 transition inline-block">
        Download Participants Excel
    </a>

    <!-- تصدير PDF -->
    <a href="{{ route('events.pdf', $event->id) }}" target="_blank"
       class="bg-red-600 text-white font-semibold px-4 py-2 rounded shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 transition inline-block">
        Export PDF Participants
    </a>

    <!-- حذف جميع المشاركين -->
    <form action="{{ route('participants.destroyAll', $event->id) }}" method="POST" class="inline-block">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 transition">
            Remove All Participants
        </button>
    </form>

</div>






<!-- قائمة المشاركين مع QR Code بجانب زر Remove -->
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
            {{-- زر إزالة المشارك --}}
            <form action="{{ route('participants.destroy', [$event->id, $participant->id]) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bg-red-600 text-white px-2 py-1 rounded shadow hover:bg-red-700 focus:bg-red-700 active:bg-red-800 transition">
                    Remove
                </button>
            </form>


            {{-- QR Code بجانب زر Remove --}}
            <div>{!! QrCode::size(80)->generate($participant->email) !!}</div>
        </div>
    </li>
@endforeach
</ul>
</div>
@endsection
