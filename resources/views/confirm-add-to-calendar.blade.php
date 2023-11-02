<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Add to Google Calendar</title>
</head>
<body>

    <h1>Confirm Add to Google Calendar</h1>

    <p>Do you want to add these events to your Google Calendar?</p>

    <form action="{{ route('addToGoogleCalendar') }}" method="POST">
        @csrf
        <input type="hidden" name="icsData" value="{{ $icsData }}">
        <button type="submit">Yes, add to Google Calendar</button>
    </form>

</body>
</html>
