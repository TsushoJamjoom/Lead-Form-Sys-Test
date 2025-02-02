<!DOCTYPE html>
<html>

<head>
    <title>Ticket Status Changed</title>
</head>
@php
    $styleStatus = "background: #ccc;";
    if($statusClass == 'bg-progress'){
        $styleStatus = "background: #f6be11;";
    }elseif($statusClass == 'bg-success'){
        $styleStatus = "background: #199b0d;color: #fff;";
    }
@endphp
<body>
    <h2>Lead Form System: Ticket Status Changed</h2>
    <p>
        Hello {{ $name }},
    </p>
    <p>
        This is an automated email to inform you that the status of your ticket (ID: {{ $ticketId }}) has been
        changed to: <label class="lbl-status" style="padding: 5px;{{$styleStatus}}">{{ $statusText }}</label>
    </p>
    <p>
        <u>Ticket Details:</u>
    </p>
    <div style="margin-left: 20px;">
        <p>
            Customer: {{ $customerName }}
        </p>
        <p>
            Ticket assigned to: {{ $assignedName }}
        </p>
        <p>
            <b>Notes: {{ $note }}</b>
        </p>
    </div>
    <p>
        For more details, please click the button below:
    </p>
    <br />
    <div style="margin:10px;">
        <a href="{{ route('ticket-list') }}" target="_blank"
            style="padding: 15px;border-radius: 10px;background: #0f9ed6;border-color: #2f95b6;color: #FFF;font-size: 20px;text-decoration: none;font-weight: 600;">View
            Ticket Details
        </a>
    </div>
    <br/>
    <br/>
</body>

</html>
