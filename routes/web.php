<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CalendarController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });





Route::get('/', [CalendarController::class, 'showUploadForm'])->name('home');
Route::post('/generateICS', [CalendarController::class, 'addToGoogleCalendar']);
// Route::get('/download-ics', [CalendarController::class, 'downloadIcs']);

// Route::get('/generate-ics', [YourController::class, 'generateICS']);
Route::post('/add-to-google-calendar', [CalendarController::class, 'addToGoogleCalendar'])->name('addToGoogleCalendar');
// Route::get('/confirmation-page', [CalendarController::class, 'confirmationPage'])->name('confirmationPage');





Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
