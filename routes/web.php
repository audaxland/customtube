<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/search', function () {
    return view('welcome');
});

Route::get('/user', function () {
    return view('welcome');
});

/**
 * Handles a query to the youtube api using the google api library
 * @route GET /search-client
 */
Route::get('/search', [SearchController::class, 'searchYoutubeApi']);


/**
 * Handles reading the authenticated/or not status for the current user
 * @route GET /status
 */
Route::get('/status', [SearchController::class,'authStatus']);

/**
 * Handles the redirection from google immediatly after authentication
 * @route GET /oauth
 */
Route::get('/oauth', [SearchController::class,'authenticateOauth']);

/**
 * Handles logging the user out
 * @route DELETE /oauth
 */
Route::delete('/oauth', [SearchController::class,'closeAuthentication']);

/**
 * Returns the list of channels that the logged in user is subscribed to
 * @route GET /subscriptions
 */
Route::get('/subscriptions', [SearchController::class,'getUsersSubscriptions']);
