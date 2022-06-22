<?php

use App\Http\Controllers\BotManController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::match(array('GET', 'POST'), '/botman', [BotManController::class, 'handle']);
Route::post('/', function (Request $request) {
    error_log("received a call");
    $data = $request->getContent();

    error_log( $data );

//    error_log($data['callerNumber']);

    $text = "Welcome to Ushahid platform";
    // Compose the response
    $response  = '<?xml version="1.0" encoding="UTF-8"?>';
    $response .= '<Response>';
    $response .= '<Say>'.$text.'</Say>';
    $response .= '</Response>';

    // Print the response onto the page so that our gateway can read it
    header('Content-type: text/plain');
//    echo $response;
    return $response;
});

