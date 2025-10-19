@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto mt-10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Participants for "{{ $event->title }}"</h2>
        <a href="{{ route('events.index') }}"
           class="bg-gray-600 text-white px-4 py-2 rounded shadow hover:bg-gray-700 transition">
           Back to Events
        </a>
    </div>

    @if($event->participants->count())
        <!-- بلوك أبيض لقائمة المشاركين -->
        <div class="bg-white border rounded shadow p-4">
            <ul class="divide-y">
                @foreach($event->participants as $participant)
                    <li class="flex justify-between items-center p-2" data-id="{{ $participant->id }}">
                        <div class="participant-info">
                            <strong>{{ $participant->name }}</strong> ({{ $participant->email }})
                            @if($participant->phone)
                                - {{ $participant->phone }}
                            @else
                                - No phone
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- زر تعديل أزرق -->
                            <button type="button"
                                    class="bg-blue-600 text-white px-2 py-1 rounded shadow hover:bg-blue-700"
                                    onclick="openEditForm({{ $participant->id }}, '{{ $participant->name }}', '{{ $participant->email }}', '{{ $participant->phone }}')">
                                Edit
                            </button>

                            <!-- زر حذف أحمر -->
                            <form action="{{ route('participants.destroy', [$event->id, $participant->id]) }}" method="POST" data-ajax="true">
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
    @else
        <p class="text-gray-600 mt-4">No participants registered for this event yet.</p>
    @endif
</div>

<!-- Modal تعديل المشارك -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded shadow-lg w-96">
        <h3 class="text-lg font-bold mb-4">Edit Participant</h3>
        <form id="editForm">
            @csrf
            <input type="hidden" name="participant_id" id="participant_id">

            <div class="mb-2">
                <label class="block text-sm font-medium">Name</label>
                <input type="text" name="name" id="edit_name" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-2">
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" id="edit_email" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-2">
                <label class="block text-sm font-medium">Phone</label>
                <input type="text" name="phone" id="edit_phone" class="w-full border rounded p-2">
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="closeEditForm()" class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- jQuery AJAX Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // حذف المشاركين
    $('form[data-ajax="true"]').submit(function(e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function() {
                form.closest('li').remove();
            }
        });
    });

    // تعديل المشاركين
    $('#editForm').submit(function(e) {
        e.preventDefault();
        var participantId = $('#participant_id').val();
        var formData = $(this).serialize();

        $.ajax({
            url: '/events/{{ $event->id }}/participants/' + participantId,
            type: 'POST',
            data: formData,
            success: function(response) {
                var li = $('li[data-id="' + participantId + '"]');
                li.find('.participant-info').html('<strong>' + response.name + '</strong> (' + response.email + ') - ' + (response.phone || 'No phone'));
                closeEditForm();
            }
        });
    });
});

// فتح وإغلاق الـ Modal
function openEditForm(id, name, email, phone) {
    $('#participant_id').val(id);
    $('#edit_name').val(name);
    $('#edit_email').val(email);
    $('#edit_phone').val(phone || '');
    $('#editModal').removeClass('hidden').addClass('flex');
}

function closeEditForm() {
    $('#editModal').addClass('hidden').removeClass('flex');
}
</script>
@endsection
