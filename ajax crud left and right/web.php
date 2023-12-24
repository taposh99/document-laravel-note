<?php

use App\Http\Controllers\TeacherController;
use Illuminate\Support\Facades\Route;

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



Route::get('/',[TeacherController:: class,'index'])->name('ajax');
Route::get('/get-all-data',[TeacherController:: class,'getData'])->name('get-all-data');
Route::POST('/store',[TeacherController::class,'store'])->name('store');
Route::get('//teacher/edit/{id}',[TeacherController:: class,'editData'])->name('edit');
Route::POST('/teacher/update/{id}',[TeacherController::class,'updateData']);

