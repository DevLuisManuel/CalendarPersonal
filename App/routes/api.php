<?php

use App\Http\Controllers\Api\{ScheduleController, VerifyController};
use Illuminate\Support\Facades\Route;

Route::controller(ScheduleController::class)->prefix('Schedule')->group(function () {
    Route::get('/', 'listAppointments')->name('Schedule.listAppointments'); // List Calendar
    Route::post('Appointment', 'createAppointment')->name('Schedule.Appointment'); //Create
    Route::get('Appointment/{id}', 'readAppointment')->name('Schedule.readAppointment'); // Read
    Route::put('Appointment/{id}', 'updateAppointment')->name('Schedule.updateAppointment'); // Update
    Route::delete('Appointment/{id}', 'deleteAppointment')->name('Schedule.deleteAppointment'); // Delete
});

Route::controller(VerifyController::class)->prefix('Verify')->group(function () {
    Route::post('/User', 'verifyUser')->name('verify-user');
});
