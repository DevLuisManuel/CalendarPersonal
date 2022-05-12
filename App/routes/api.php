<?php

use App\Http\Controllers\Api\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::controller(ScheduleController::class)->prefix('Schedule')->group(function () {
    Route::get('/', 'listAppointments')->name('Schedule.listAppointments'); // List Calendar
    Route::post('Appointment', 'appointment')->name('Schedule.Appointment'); //Create
    Route::get('/Appointment/{id}', 'readAppointment')->name('Schedule.readAppointment'); // Read
    Route::put('/Appointment/{id}', 'updateAppointment')->name('Schedule.updateAppointment'); // Update
    Route::delete('/Appointment/{id}', 'deleteAppointment')->name('Schedule.deleteAppointment'); // Delete
});
