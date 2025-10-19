<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // عرض جميع الأحداث
    public function index()
    {
        $events = Event::all();
        return view('events.index', compact('events'));
    }

    // صفحة إنشاء حدث جديد
    public function create()
    {
        return view('events.create');
    }

    // تخزين حدث جديد
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
        ]);

        $event = Event::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Event created successfully!',
                'event' => $event
            ]);
        }

        return redirect()->route('events.index')->with('success', 'Event created successfully!');
    }

    // صفحة تعديل الحدث
    public function edit(Event $event)
    {
        $event->load('participants');
        return view('events.edit', compact('event'));
    }

    // تحديث الحدث
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
        ]);

        $event->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Event updated successfully!',
                'event' => $event
            ]);
        }

        return redirect()->route('events.index')->with('success', 'Event updated successfully!');
    }

    public function participants(Event $event)
{
    // افترض أن لديك علاقة participants في نموذج Event
    $participants = $event->participants;

    // عرض صفحة المشاركين
    return view('events.participants', compact('event', 'participants'));
}


    // حذف الحدث
    public function destroy(Request $request, Event $event)
    {
        $event->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully!'
            ]);
        }

        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
