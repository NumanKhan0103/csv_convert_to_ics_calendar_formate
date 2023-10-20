<?php

namespace App\Http\Controllers;

use League\Csv\Reader;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function showUploadForm()
    {
        return view('upload_form');
    }

    public function upload(Request $request)
    {

        $request->validate([
            'csvFile' => 'required|mimes:csv,txt',
        ]);


        $csv = Reader::createFromPath($request->file('csvFile')->getPathname(), 'r');



        $records = $csv->getRecords();

        // Initialize the iCalendar data
        $icalData = "BEGIN:VCALENDAR\n";

        $icalData .= "VERSION:2.0\n";

        foreach ($records as $record) {


                    // Extract data from the CSV record
                    // $date = $record[0];
                    // $fajr = $record[2];
                    // $sunrise = $record[3];
                    // $dhuhr = $record[4];
                    // $asr = $record[5];
                    // $maghrib = $record[6];
                    // $isha = $record[7];

                    // // Create individual events for each prayer
                    // $prayers = ['Fajr', 'Sunrise', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];

                    // foreach ($prayers as $prayer) {

                    //     // Convert extracted data to iCalendar format
                    //     $icalData .= "BEGIN:VEVENT\n";
                    //     $icalData .= "SUMMARY:$prayer Prayer\n";
                    //     $icalData .= "DTSTART:$date $record[$prayerIndex]\n";

                    //     // $prayerIndex =   $prayerIndex+1;

                    //     $icalData .= "DTEND:$date $record[$prayerIndex]\n";
                    //     // ... add more event properties as needed
                    //     $icalData .= "END:VEVENT\n";

                    //     dd($icalData);
                    // }

                    $date = $record[0];
                    $prayerTimings = array_slice($record, 2); // Exclude date and subheadings

                    // Create individual events for each prayer
                    $prayers = ['Fajr', 'Sunrise', 'Dhuhr', 'Asr', 'Maghrib', 'Isha'];

                    foreach ($prayers as $index => $prayer) {
                        // Convert extracted data to iCalendar format
                        $icalData .= "BEGIN:VEVENT\n";
                        $icalData .= "SUMMARY:$prayer Prayer\n";
                        $icalData .= "DTSTART:$date $prayerTimings[$index]\n";
                        // $icalData .= "DTEND:$date $prayerTimings[$index + 1]\n";
                        $icalData .= "DTEND:" . $date . " " . $prayerTimings[$index + 1] . "\n";

                        // ... add more event properties as needed
                        $icalData .= "END:VEVENT\n";
                        dd($icalData);

                    }



        }

        $icalData .= "END:VCALENDAR";

        // Save the iCalendar data to a file
        $icalPath = storage_path('app/public/calendar.ics');
        file_put_contents($icalPath, $icalData);

        return redirect('/download-ical');
    }

    public function downloadIcal()
    {
        // Provide the path to the generated iCalendar file for download
        // $ics_path = public_path('path/to/generated.ics');

        // return response()->download($ics_path, 'calendar.ics');
    }

}
