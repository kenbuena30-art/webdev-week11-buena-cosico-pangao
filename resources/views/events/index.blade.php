<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Events</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f2f2f2; }
        .count { font-weight: bold; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h1>Events</h1>
    <p class="count">Queries run: {{ $queryCount }}</p>

    <table>
        <tr>
            <th>Title</th>
            <th>Venue</th>
            <th>Participants</th>
        </tr>
        @foreach ($events as $event)
            <tr>
                <td>{{ $event->title }}</td>
                <td>{{ $event->venue->name }}</td>
                <td>{{ $event->participants_count }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>