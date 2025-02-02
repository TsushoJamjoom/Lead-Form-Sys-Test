<!DOCTYPE html>
<html>
<head>
    <title>Ticket Status Changed</title>
</head>
<body>
    <h2>Ticket Status Changed</h2>
    <p>
        Hello {{ $name }},
    </p>
    <p>
        This is to inform you that the status of your ticket (ID: {{ $ticket->id }}) has been changed to: {{ $status }}.
    </p>
    <p>
        <strong>Notes: </strong>{{ $note }}
    </p>
</body>
</html>
