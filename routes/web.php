<?php

use App\Http\Controllers\Settings\RolesController;
use Illuminate\Support\Facades\Artisan;
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

Route::get('/home', function () {

        Artisan::call('config:cache');
//    Artisan::call('view:clear');
//    Artisan::call('route:clear');
//    Artisan::call('route:cache');
//    Artisan::call('config:clear');
//    Artisan::call('config:cache');
//    Artisan::call('cache:clear');
//    Artisan::call('optimize');
    print_r(env('APP_URL'));die;;
    return view('welcome');
});


Route::get('/roles/get_data', [RolesController::class, 'get_data']);
