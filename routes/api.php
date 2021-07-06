<?php

use App\Http\Controllers\Tasks\TasksController;
use App\Http\Controllers\Test\TestTemplatesController;
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
    Route::post('/experts/status_change', [ExpertsController::class, 'statusChange']);

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



    //Clients
    Route::post('/clients/get_data', [ClientsController::class, 'get_data']);
    Route::post('/clients/add', [ClientsController::class, 'add']);
    Route::post('/clients/update', [ClientsController::class, 'update']);
    Route::post('/clients/delete', [ClientsController::class, 'delete']);
    Route::post('/clients/get', [ClientsController::class, 'get']);
    Route::post('/clients/fileupload', [ClientsController::class, 'uploadfile_to_s3']);
    Route::post('/clients/change_password', [ClientsController::class, 'changePassword']);


    //Task
    Route::post('/tasks/get_data', [TasksController::class, 'get_data']);
    Route::post('/tasks/add', [TasksController::class, 'add']);
    Route::post('/tasks/update', [TasksController::class, 'update']);
    Route::post('/tasks/delete', [TasksController::class, 'delete']);
    Route::post('/tasks/get', [TasksController::class, 'get']);


    //Pages
    Route::post('/pages/get_fields', [PagesController::class, 'get_fields']);
    Route::post('/pages/home/update_banner', [HomePageController::class, 'update_banner']);
    Route::post('/pages/home/delete_banner', [HomePageController::class, 'delete_banner']);
    Route::post('/pages/home/add_marketplace', [HomePageController::class, 'add_marketplace']);


    //Test
    Route::post('/test_templates/get_data', [TestTemplatesController::class, 'get_data']);
    Route::post('/test_templates/add', [TestTemplatesController::class, 'add']);
    Route::post('/test_templates/update', [TestTemplatesController::class, 'update']);
    Route::post('/test_templates/delete', [TestTemplatesController::class, 'delete']);
    Route::post('/test_templates/get', [TestTemplatesController::class, 'get']);


    Route::group([
        'prefix' => 'web'
    ], function ($router) {
        //Clients
        Route::post('/clients/register_new_client', [ClientsController::class, 'api_register']);
        Route::post('/clients/login', [ClientsController::class, 'api_login']);
        Route::post('/clients/logout', [ClientsController::class, 'api_logout']);
        Route::post('/clients/set_username_or_password', [ClientsController::class, 'api_set_username_or_password']);

        Route::post('/skills', [SkillsController::class, 'api_list']);
        Route::post('/find_total_experts', [ExpertsController::class, 'api_find_total_experts']);
        Route::post('/get_skills_with_experts', [ExpertsController::class, 'api_get_skills_with_experts']);
        Route::post('/get_experts_public_profile', [ExpertsController::class, 'api_get_experts_public_profile']);


        Route::post('/clients/add_task', [TasksController::class, 'api_add_task']);
        Route::post('/clients/get_client_tasks', [TasksController::class, 'api_get_client_tasks']);
        Route::post('/clients/get_client_proposals', [TasksController::class, 'api_get_client_proposals']);


    });

});


Route::get('/clear', function () {
    return "Cleared!";

//    Artisan::call('storage:link');
//    Artisan::call('cache:clear');
//    Artisan::call('config:clear');
//    Artisan::call('config:cache');
//    Artisan::call('view:clear');
    Artisan::call('route:cache');
    Artisan::call('route:clear');

    Artisan::call('optimize');
    return "Cleared!";
});