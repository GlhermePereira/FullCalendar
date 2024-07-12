<?php

use App\Http\Controllers\ScheduleController;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
/*
Route::get('/', function () {
    return view('welcome');
});
*/
Route::get('/', [ScheduleController::class,'index']);
Route::get('/events', [ScheduleController::class,'getEvents']);
Route::delete('/schedule/{id}', [ScheduleController::class,'deletEvent']);
Route::put('/schedule/{id}', [ScheduleController::class,'update']);
Route::post('/schedule/{id}/resize',[ScheduleController::class,'resize']);

Route::get('/events/search',[ScheduleController::class,'search']);

Route::view('add-schedule','schedule.add');

Route::post('create-schedule',[ScheduleController::class,'create']);