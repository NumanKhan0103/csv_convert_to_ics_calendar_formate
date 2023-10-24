<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use League\Csv\Reader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Components\Calendar;

class CalendarController extends Controller
{
    public function showUploadForm()
    {
        return view('upload_form');
    }

    // public function upload(Request $request)
    // {

    //     $request->validate([
    //         'csvFile' => 'required|mimes:csv,txt',
    //     ]);

    //     $csv = Reader::createFromPath($request->file('csvFile')->getPathname(), 'r');

    //     $records = $csv->getRecords();

    //     // Initialize the iCalendar data
    //     $icalData = "BEGIN:VCALENDAR\n";

    //     $icalData .= "VERSION:2.0\n";

    //     foreach ($records as $record) {

    //                 $date = $record[0];
    //                 $prayerTimings = array_slice($record, 2); // Exclude date and subheadings

    //                 // Create individual events for each prayer
    //                 $prayers = ['Fajr', 'Sunrise', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];

    //                 foreach ($prayers as $index => $prayer) {
    //                     // Convert extracted data to iCalendar format
    //                     $icalData .= "BEGIN:VEVENT\n";
    //                     $icalData .= "SUMMARY:$prayer Prayer\n";
    //                     $icalData .= "DTSTART:" . str_replace('-', '', $date) . "T" . str_replace('.', '', $prayerTimings[$index]) . "00Z\n";
    //                     $icalData .= "DTEND:" . str_replace('-', '', $date) . "T" . str_replace('.', '', $prayerTimings[$index + 1]) . "00Z\n";

    //                     // ... add more event properties as needed
    //                     $icalData .= "END:VEVENT\n";

    //                 }

    //     }

    //     $icalData .= "END:VCALENDAR";

    //     // Save the iCalendar data to a file
    //     $icalPath = storage_path('app/public/calendar.ics');
    //     file_put_contents($icalPath, $icalData);

    //     return redirect('/download-ics');
    // }

//     public function upload(Request $request)
// {
//     $request->validate([
//         'csvFile' => 'required|mimes:csv,txt',
//     ]);

//     $csv = Reader::createFromPath($request->file('csvFile')->getPathname(), 'r');
//     $records = $csv->getRecords();

//     // Initialize the iCalendar data
//     $icalData = "BEGIN:VCALENDAR\n";
//     $icalData .= "VERSION:2.0\n";

//     foreach ($records as $record) {
//         // Extract data from the CSV record
//         $date = $record[0];
//         $prayerTimings = array_slice($record, 2); // Exclude date and subheadings

//         // Create individual events for each prayer
//         $prayers = ['Fajr', 'Sunrise', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];

//         foreach ($prayers as $index => $prayer) {
            // Convert extracted data to iCalendar format

            // dd($icalData = Calendar::create('Laracon online')
            //     ->event(Event::create('Creating calender feeds')
            //         ->startsAt(new DateTime('6 March 2019 15:00'))
            //         ->endsAt(new DateTime('6 March 2019 16:00'))
            //     )
            //     ->get());

            // $icalData .= "BEGIN:VEVENT\n";
            // $icalData .= "SUMMARY:$prayer Prayer\n";
            // $icalData .= "DTSTART:" . gmdate('Ymd\THis\Z', strtotime("$date $prayerTimings[$index]")) . "\n";
            // $icalData .= "DTEND:" . gmdate('Ymd\THis\Z', strtotime("$date $prayerTimings[$index + 1]")) . "\n";
            // $icalData .= "END:VEVENT\n";
    //     }
    // }

    // $icalData .= "END:VCALENDAR";


    // Save the iCalendar data to a file
    // $icalPath = storage_path('app/public/calendar.ics');
    // file_put_contents($icalPath, $icalData);

    // $calendar = Calendar::create('Laracon Online');

// return response($calendar->get(), 200, [
//    'Content-Type' => 'text/calendar; charset=utf-8',
//    'Content-Disposition' => 'attachment; filename="my-awesome-calendar.ics"',
// ]);

    // return redirect('/download-ical');
// }






    public function upload(Request $request)
    {
        $request->validate([
            'csvFile' => 'required|mimes:csv,txt',
        ]);

        $csv = Reader::createFromPath($request->file('csvFile')->getPathname(), 'r');
        $records = $csv->getRecords();

        // Initialize the iCalendar data
        $calendar = new Calendar();

        $total_record  = 0;

        foreach ($records as $record) {
            $month = $record[0];
            $day = $record[1];
            $year = Carbon::now()->year; // Assuming the current year; adjust as needed
            $prayerTimings = array_slice($record, 2);

            // Create individual events for each prayer
            $prayers = ['Fajr', 'Sunrise', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];

            foreach ($prayers as $index => $prayer) {

                // echo "<pre>";
                // print_r($prayerTimings[$index]);
                // echo "</pre>";
                // echo "<pre>";
                // print_r($prayerTimings[$index + 1]);
                // echo "</pre>";
                // echo "<br>------ start code 8.3 convert to time --------<br>";



                // ($integerValue)
                //  echo   ++$total_record; echo " :: ";

                //  $num = 109;
 $start_time = sprintf("%.2f",$prayerTimings[$index]);
 $end_time = sprintf("%.2f",$prayerTimings[$index + 1]);



                // for start time ( 8.26 give number in csv convert to real time )
                list($start_time_hours, $start_time_minutes) = explode('.', $start_time);
                $start_time_total_minutes = ($start_time_hours * 60) + $start_time_minutes;
                $start_time_time_format = gmdate('H:i', $start_time_total_minutes * 60);

                // for end  time ( 6.26 give number in csv convert to real time )
                list($end_time_hours, $end_time_minutes) = explode('.', $end_time);
                $end_time_total_minutes = ($end_time_hours * 60) + $end_time_minutes;
                $end_time_time_format = gmdate('H:i', $end_time_total_minutes * 60);




                $startTime = DateTime::createFromFormat('H:i', $start_time_time_format);
                $endTime   = DateTime::createFromFormat('H:i', $end_time_time_format);


                // Check if DateTime objects were created successfully
                if ($startTime !== false && $endTime !== false) {

                //  echo "<br>";
                // echo   ++$total_record;
                // print_r($startTime);
                // print_r($endTime);
                // echo "<br>";



                    $startDateTime = Carbon::create($year, $month, $day, $startTime->format('H'), $startTime->format('i'));
                    $endDateTime = Carbon::create($year, $month, $day, $endTime->format('H'), $endTime->format('i'));

                    $event = Event::create("$prayer")
                        ->startsAt($startDateTime)
                        ->endsAt($endDateTime);

                    $calendar->event($event);
                } else {
                    // Log::error("Failed to create DateTime objects for $prayer Prayer at index $index");
                    // continue;
                }
            }

            // dd('end inner loop');
        }



        $icalPath = storage_path('app/public/calendar.ics');
        file_put_contents($icalPath, $calendar->get());



        $encodedTitle = urlencode("Event Title");
    $encodedLocation = urlencode("Event Location");
    $encodedDetails = urlencode("Event Description");
    $encodedDates = urlencode("2023-01-01T12:00:00/2023-01-01T14:00:00");

    // Generate the Google Calendar Event URL
    $googleCalendarUrl = "https://www.google.com/calendar/render?action=TEMPLATE&text=$encodedTitle&dates=$encodedDates&details=$encodedDetails&location=$encodedLocation";

    return redirect($googleCalendarUrl);


        // return redirect('/download-ics');
    }






    public function downloadIcs()
    {
       $ics_path =   public_path('storage/calendar.ics');

        if (File::exists(public_path('storage/calendar.ics')))
        {
            return response()->download($ics_path, 'calendar.ics');
        }

    }

}
