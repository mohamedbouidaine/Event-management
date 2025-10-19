@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white shadow rounded mt-6">
    <h1 class="text-2xl font-bold mb-4">Add New Event</h1>

    <!-- عرض رسائل الأخطاء -->
    <div id="errorMessages" class="mb-4 p-4 bg-red-100 text-red-700 rounded hidden">
        <ul id="errorsList"></ul>
    </div>

    <!-- رسالة النجاح -->
    <div id="successMessage" class="mb-4 p-4 bg-green-100 text-green-700 rounded hidden"></div>

    <form id="addEventForm" action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block font-semibold mb-1">Event Title</label>
            <input type="text" name="title" placeholder="Event Title" class="w-full border rounded p-2" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Description</label>
            <textarea name="description" placeholder="Description" class="w-full border rounded p-2" required></textarea>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Date</label>
            <input type="date" name="date" class="w-full border rounded p-2" required>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Location</label>
            <input type="text" name="location" placeholder="Event Location" class="w-full border rounded p-2">
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Event Image</label>
            <input type="file" name="image" class="w-full border rounded p-2">
        </div>

        <button type="submit" style="background:red; color:white; font-size:20px; padding:10px; width:200px;">
            Add Event
        </button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#addEventForm').submit(function(e) {
        e.preventDefault();

        // إخفاء الرسائل السابقة
        $('#errorMessages').hide();
        $('#successMessage').hide();

        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#successMessage').text(response.message).show();
                $('#addEventForm')[0].reset();
            },
            error: function(xhr) {
                if(xhr.responseJSON && xhr.responseJSON.errors) {
                    $('#errorsList').empty();
                    $.each(xhr.responseJSON.errors, function(field, messages) {
                        $.each(messages, function(i, msg) {
                            $('#errorsList').append('<li>' + msg + '</li>');
                        });
                    });
                    $('#errorMessages').show();
                } else {
                    alert('حدث خطأ أثناء إضافة الحدث');
                }
            }
        });
    });
});
</script>
@endsection
