<?php

use App\Http\Controllers\Api\V1\FormFieldController;
use App\Http\Controllers\Api\V1\FormSubmissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/form-fields', [FormFieldController::class, 'index'])->name('form-fields.index');
    Route::post('/form-submissions', [FormSubmissionController::class, 'store'])->name('form-submissions.store');
});
