<?php

use App\Http\Controllers\Api\OpenAiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('get-openai', [OpenAiController::class, 'testOpenAi']);
Route::get('test-sqs-client', [OpenAiController::class, 'testSQSClient']);
Route::get('get-sqs-messages', [OpenAiController::class, 'getMessagesFromAWSSqs']);
Route::get('get-users', [OpenAiController::class, 'getAllUsers']);
Route::post('user', [OpenAiController::class, 'storeUser']);
