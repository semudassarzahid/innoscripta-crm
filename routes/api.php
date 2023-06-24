<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::apiResource('leads', 'App\Http\Controllers\LeadController');
    Route::post('/leads/upload',['App\Http\Controllers\LeadController','uploadAndCreateLeads']);
    Route::put('leads/{leadId}/status', 'App\Http\Controllers\LeadController@updateStatus');
    Route::apiResource('custom-fields', 'App\Http\Controllers\CustomFieldController');
    Route::apiResource('custom-field-types', 'App\Http\Controllers\CustomFieldTypeController');
    Route::resource('statuses', 'App\Http\Controllers\StatusController');
    Route::post('/statuses/reorderPositions', ['App\Http\Controllers\StatusController','reorderPositions']);
    Route::resource('workflow-rules', 'App\Http\Controllers\WorkflowRuleController');
    Route::resource('emails', 'App\Http\Controllers\EmailController');
    Route::resource('activities', 'App\Http\Controllers\ActivityController');
    Route::get('activities/by-lead/{id}', ['App\Http\Controllers\ActivityController','byLead']);
    Route::resource('reminders', 'App\Http\Controllers\ReminderController');
    Route::resource('users', 'App\Http\Controllers\UserController');

});
