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

<!-- AJAX Script - ضع هذا قبل نهاية @endsection -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#addParticipant').click(function(e) {
        e.preventDefault();

        var name = $('#name').val();
        var email = $('#email').val();
        var event_id = '{{ $event->id }}'; // معرف الحدث

        $.ajax({
            url: '{{ route("participants.store") }}', // تأكد من الـ route الصحيح
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                event_id: event_id,
                name: name,
                email: email
            },
            success: function(response) {
                // تحديث قائمة المشاركين إذا كانت موجودة
                if($('#participantsList').length) {
                    $('#participantsList').append('<li>' + response.name + ' - ' + response.email + '</li>');
                }
                // مسح الحقول بعد الإضافة
                $('#name').val('');
                $('#email').val('');
            },
            error: function(xhr) {
                alert('حدث خطأ: ' + xhr.responseText);
            }
        });
    });
});
</script>

@endsection
