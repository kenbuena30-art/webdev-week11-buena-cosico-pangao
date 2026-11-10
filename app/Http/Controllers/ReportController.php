<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Note;
use App\Models\Participant;
use App\Models\Venue;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // The "chosen" records come from the URL, e.g. /report?venue=2&event=3
        $venue = Venue::findOrFail($request->query('venue', 1));
        $event = Event::findOrFail($request->query('event', 1));
        $participant = Participant::findOrFail($request->query('participant', 1));
        $note = Note::with('notable')->findOrFail($request->query('note', 1));

        // 1. Every venue with the number of events it hosts
        $venues = Venue::withCount('events')->get();

        // 2. All events at one chosen venue
        $venueEvents = $venue->events;

        // 3. All participants of one chosen event, with registration status
        $eventParticipants = $event->participants;

        // 4. All events one chosen participant has joined
        $participantEvents = $participant->events;

        // 5. All events that have at least one waitlisted participant
        $waitlistedEvents = Event::whereHas('participants', function ($query) {
            $query->where('event_participant.status', 'waitlisted');
        })->get();

        // 6. All notes of one chosen event, and of one chosen venue
        $eventNotes = $event->notes;
        $venueNotes = $venue->notes;

        // 7. For one chosen note, what it belongs to (loaded with morphTo)
        $noteOwner = $note->notable;

        return view('report', compact(
            'venue', 'event', 'participant', 'note',
            'venues', 'venueEvents', 'eventParticipants', 'participantEvents',
            'waitlistedEvents', 'eventNotes', 'venueNotes', 'noteOwner'
        ));
    }
}