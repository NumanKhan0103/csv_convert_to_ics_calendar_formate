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

        // Process CSV data and convert to iCalendar format
        // Save the iCalendar data to a file

        return redirect('/download-ics-file');
    }

    public function downloadIcal()
    {
        // Provide the path to the generated iCalendar file for download
        $ics_path = public_path('path/to/generated.ics');

        return response()->download($ics_path, 'calendar.ics');
    }

}
