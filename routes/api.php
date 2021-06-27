<?php

use App\Http\Controllers\UsersController;
use App\Http\Controllers\App\CountriesController;
use App\Http\Controllers\App\SkillsController;
use App\Http\Controllers\Experts\ExpertsController;
use App\Http\Controllers\Clients\ClientsController;
use App\Http\Controllers\Pages\PagesController;
use App\Http\Controllers\Pages\HomePageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::group([
    'middleware' => 'api'
], function ($router) {

    Route::group([
        'prefix' => 'auth'
    ], function ($router) {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/user-profile', [AuthController::class, 'userProfile']);
    });


    //Users
    Route::post('/users/get_data', [UsersController::class, 'get_data']);
    Route::post('/users/add', [UsersController::class, 'add']);
    Route::post('/users/update', [UsersController::class, 'update']);
    Route::post('/users/delete', [UsersController::class, 'delete']);
    Route::post('/users/get', [UsersController::class, 'get']);



     //Skill
    Route::post('/skills/get_data', [SkillsController::class, 'get_data']);
    Route::post('/skills/add', [SkillsController::class, 'add']);
    Route::post('/skills/update', [SkillsController::class, 'update']);
    Route::post('/skills/delete', [SkillsController::class, 'delete']);
    Route::post('/skills/get', [SkillsController::class, 'get']);
    Route::post('/skills/get_all', [SkillsController::class, 'get_all']);


    //Countries
    Route::post('/countries/get_data', [CountriesController::class, 'get_data']);
    Route::post('/countries/add', [CountriesController::class, 'add']);
    Route::post('/countries/update', [CountriesController::class, 'update']);
    Route::post('/countries/delete', [CountriesController::class, 'delete']);
    Route::post('/countries/get', [CountriesController::class, 'get']);


    //Experts
    Route::post('/experts/get_data', [ExpertsController::class, 'get_data']);
    Route::post('/experts/add', [ExpertsController::class, 'add']);
    Route::post('/experts/update', [ExpertsController::class, 'update']);
    Route::post('/experts/delete', [ExpertsController::class, 'delete']);
    Route::post('/experts/get', [ExpertsController::class, 'get']);
    Route::post('/experts/fileupload', [ExpertsController::class, 'uploadfile_to_s3']);
    Route::post('/experts/updateEducation', [ExpertsController::class, 'updateEducation']);
    Route::post('/experts/add_expert_education', [ExpertsController::class, 'addExpertEducation']);
    Route::post('/experts/delete_expert_education', [ExpertsController::class, 'deleteExpertEducation']);
    Route::post('/experts/add_expert_tool', [ExpertsController::class, 'addExpertTool']);
    Route::post('/experts/delete_expert_skill', [ExpertsController::class, 'deleteExpertSkill']);
    Route::post('/experts/delete_expert_tool', [ExpertsController::class, 'deleteExpertTool']);
    Route::post('/experts/add_expert_skills', [ExpertsController::class, 'addExpertSkills']);
    Route::post('/experts/change_expert_password', [ExpertsController::class, 'changeExpertPassword']);




    //Pages
    Route::post('/pages/get_fields', [PagesController::class, 'get_fields']);
    Route::post('/pages/home/update_banner', [HomePageController::class, 'update_banner']);
    Route::post('/pages/home/delete_banner', [HomePageController::class, 'delete_banner']);
    Route::post('/pages/home/add_marketplace', [HomePageController::class, 'add_marketplace']);


    //Clients
    Route::post('/clients/register_new_client', [ClientsController::class, 'register_new_client']);


});