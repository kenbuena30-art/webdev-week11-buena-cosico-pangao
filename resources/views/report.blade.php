<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Events Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; max-width: 900px; }
        h1 { margin-bottom: 5px; }
        h2 { margin-top: 30px; border-bottom: 2px solid #333; padding-bottom: 4px; }
        table { border-collapse: collapse; width: 100%; margin-top: 8px; }
        th, td { border: 1px solid #aaa; padding: 6px 10px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h1>Campus Events Report</h1>
    <p>Chosen venue: <strong>{{ $venue->name }}</strong> |
       Chosen event: <strong>{{ $event->title }}</strong> |
       Chosen participant: <strong>{{ $participant->name }}</strong></p>

    <h2>1. Every venue with the number of events it hosts</h2>
    <table>
        <tr><th>Venue</th><th>Events hosted</th></tr>
        @foreach ($venues as $v)
            <tr><td>{{ $v->name }}</td><td>{{ $v->events_count }}</td></tr>
        @endforeach
    </table>

    <h2>2. All events at {{ $venue->name }}</h2>
    <table>
        <tr><th>Event</th><th>Starts at</th></tr>
        @foreach ($venueEvents as $e)
            <tr><td>{{ $e->title }}</td><td>{{ $e->starts_at->format('M d, Y h:i A') }}</td></tr>
        @endforeach
    </table>

    <h2>3. Participants of "{{ $event->title }}" and their registration status</h2>
    <table>
        <tr><th>Participant</th><th>Status</th></tr>
        @foreach ($eventParticipants as $p)
            <tr><td>{{ $p->name }}</td><td>{{ $p->pivot->status }}</td></tr>
        @endforeach
    </table>

    <h2>4. Events joined by {{ $participant->name }}</h2>
    <table>
        <tr><th>Event</th><th>Status</th></tr>
        @foreach ($participantEvents as $e)
            <tr><td>{{ $e->title }}</td><td>{{ $e->pivot->status }}</td></tr>
        @endforeach
    </table>

    <h2>5. Events with at least one waitlisted participant</h2>
    <table>
        <tr><th>Event</th></tr>
        @foreach ($waitlistedEvents as $e)
            <tr><td>{{ $e->title }}</td></tr>
        @endforeach
    </table>

    <h2>6. Notes</h2>
    <p><strong>Notes of event "{{ $event->title }}":</strong></p>
    <table>
        <tr><th>Note</th></tr>
        @forelse ($eventNotes as $n)
            <tr><td>{{ $n->body }}</td></tr>
        @empty
            <tr><td>No notes</td></tr>
        @endforelse
    </table>
    <p><strong>Notes of venue "{{ $venue->name }}":</strong></p>
    <table>
        <tr><th>Note</th></tr>
        @forelse ($venueNotes as $n)
            <tr><td>{{ $n->body }}</td></tr>
        @empty
            <tr><td>No notes</td></tr>
        @endforelse
    </table>

    <h2>7. What a chosen note belongs to</h2>
    <p>Note: "{{ $note->body }}" belongs to the
       <strong>{{ class_basename($note->notable_type) }}</strong>
       "{{ $noteOwner->name ?? $noteOwner->title }}".</p>
</body>
</html>