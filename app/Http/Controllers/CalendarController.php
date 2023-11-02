<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use League\Csv\Reader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Google_Client as GoogleClient; // Update the alias to match the installed library
use Google_Service_Calendar as GoogleCalendarService;
use Google_Service_Calendar_Event as GoogleCalendarEvent;
use App\Http\Controllers\GoogleCalendar;
// use Google\Client as GoogleClient;
// use Spatie\GoogleCalendar\Event as GoogleCalendarEvent;
// use Spatie\GoogleCalendar\GoogleCalendarService;


class CalendarController extends Controller
{
    public function showUploadForm()
    {
        return view('upload_form');
    }


    public function addToGoogleCalendar(Request $request)
{
    $events = [];

    $request->validate([
        'csvFile' => 'required|mimes:csv,txt',
    ]);

    $csv = Reader::createFromPath($request->file('csvFile')->getPathname(), 'r');
    $records = $csv->getRecords();
    $events = [];
    $current_month = date('m');

    // Create an instance of GoogleCalendar
    // $googleCalendar = new App\Http\Controllers\GoogleCalendarController();

    $googleCalendar = new GoogleCalendar();

    foreach ($records as $record) {
        $month = $record[0];
        $day = $record[1];
        $year = Carbon::now()->year;
        $prayerTimings = array_slice($record, 2);

        $prayers = ['Fajr', 'Sunrise', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];

        if ($current_month == $month) {
            foreach ($prayers as $index => $prayer) {
                $start_time = sprintf("%.2f", $prayerTimings[$index]);
                $end_time   = sprintf("%.2f", $prayerTimings[$index + 1]);

                list($start_time_hours, $start_time_minutes) = explode('.', $start_time);
                list($end_time_hours, $end_time_minutes) = explode('.', $end_time);

                $startTime = Carbon::create($year, $month, $day, $start_time_hours, $start_time_minutes);
                $endTime = Carbon::create($year, $month, $day, $end_time_hours, $end_time_minutes);

                if ($startTime !== false && $endTime !== false) {
                    // Create an event and add it to the events array
                    $events[] = $googleCalendar->event
                        ->name($prayer)
                        ->description('Pray Time')
                        ->start($startTime)
                        ->end($endTime);

                } else {
                    Log::error("Failed to create DateTime objects for $prayer Prayer at index $index");
                    continue;
                }
            }
        }
    }

    // Save all events to Google Calendar
    $googleCalendar->events($events);

    // Load the service account credentials from the JSON file
    $serviceAccountPath = public_path('client_secret.json');

    $client = new GoogleClient();
    $client->setAuthConfig($serviceAccountPath);
    $client->addScope(GoogleCalendarService::CALENDAR_EVENTS);

    // Use the service account credentials directly (no need for user authentication)
    $client->setAccessType('offline');

    $calendarService = new GoogleCalendarService($client);

    // Loop through each event and add it to the Google Calendar
    foreach ($events as $event) {
        // Extract event details
        $eventName = $event->name();
        $eventDescription = $event->description();
        $eventStartDateTime = $event->start()->getValue();
        $eventEndDateTime = $event->end()->getValue();

        // Create a new Google Calendar Event
        $googleEvent = new GoogleCalendarEvent([
            'summary' => $eventName,
            'description' => $eventDescription,
            'start' => ['dateTime' => $eventStartDateTime],
            'end' => ['dateTime' => $eventEndDateTime],
        ]);

        // Insert the event into the primary calendar
        $calendarService->events->insert('primary', $googleEvent);
    }


    return redirect()->route('confirmationPage');


    // return response($icsData)
    //     ->header('Content-Type', 'text/calendar; charset=utf-8');
}

    // <!-- public function addToGoogleCalendar(Request $request)
    // {
    //     // Parse the .ics data from the form
    //     $icsData = $request->input('icsData');

    //     // Parse the iCalendar data and add events to the calendar
    //     $vCalendar = \Sabre\VObject\Reader::read($icsData);
    //     foreach ($vCalendar->VEVENT as $vevent) {
    //         // Extract event data
    //         $name = (string)$vevent->SUMMARY;
    //         $startDateTime = new \DateTime((string)$vevent->DTSTART);
    //         $endDateTime = new \DateTime((string)$vevent->DTEND);

    //         // Ensure $name is a string
    //         if (is_array($name)) {
    //             // If $name is an array, take the first element
    //             $name = reset($name);
    //         }

    //         if(is_string($name) && $name != "" && !is_array($name)){

    //         // Add event to your Google Calendar
    //         Event::create([
    //             'name' => (string)$name, // Ensure $name is a string
    //             'startDateTime' => $startDateTime,
    //             'endDateTime' => $endDateTime,
    //         ]);
    //     }
    //     }

    //     // Redirect user to a confirmation page or any other page as needed
    //     return redirect()->route('confirmationPage');
    // } -->






    public function downloadIcs()
    {
       $ics_path =   public_path('storage/calendar.ics');

        if (File::exists(public_path('storage/calendar.ics')))
        {
            return response()->download($ics_path, 'calendar.ics');
        }

    }

}
