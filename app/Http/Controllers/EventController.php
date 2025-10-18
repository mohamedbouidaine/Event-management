<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index() {
        $events = Event::all();
        return view('events.index', compact('events'));
    }

    public function create() {
        return view('events.create');
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'date' => 'required|date',
        ]);

        Event::create($request->all());
        return redirect()->route('events.index');
    }

    public function edit(Event $event) {
    // تحميل المشاركين المرتبطين بالحدث
    $event->load('participants');

    return view('events.edit', compact('event'));
}


    public function update(Request $request, Event $event) {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'date' => 'required|date',
        ]);

        $event->update($request->all());
        return redirect()->route('events.index');
    }

    // ← إضافة هذه الدالة
    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
