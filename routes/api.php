<?php

use App\Http\Controllers\Accounts\TransactionsController;
use App\Http\Controllers\Notifications\NotificationsController;
use App\Http\Controllers\Settings\RolesController;
use App\Http\Controllers\Settings\SystemSettingsController;
use App\Http\Controllers\Tasks\TasksController;
use App\Http\Controllers\Test\TestAttemptsController;
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
    Route::post('/experts/status_change', [ExpertsController::class, 'status_change']);

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
    Route::post('/clients/get_member_list_data', [ClientsController::class, 'get_member_list_data']);


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
    Route::post('/test/templates/get_data', [TestTemplatesController::class, 'get_data']);
    Route::post('/test/templates/add', [TestTemplatesController::class, 'add']);
    Route::post('/test/templates/update', [TestTemplatesController::class, 'update']);
    Route::post('/test/templates/delete', [TestTemplatesController::class, 'delete']);
    Route::post('/test/templates/get', [TestTemplatesController::class, 'get']);

    //Test Attempts
    Route::post('/test/attempts/get_data', [TestAttemptsController::class, 'get_data']);
    Route::post('/test/attempts/status_change', [TestAttemptsController::class, 'status_change']);
    Route::post('/test/attempts/get', [TestAttemptsController::class, 'get']);
    Route::post('/test/attempts/delete', [TestAttemptsController::class, 'delete']);


    //Accounts
    Route::post('/accounts/transactions/ledger', [TransactionsController::class, 'ledger']);
    Route::post('/accounts/transactions/get_wallet_data', [TransactionsController::class, 'get_wallet_data']);



    //System Settings
    Route::post('/settings/system_settings/get', [SystemSettingsController::class, 'get']);
    Route::post('/settings/system_settings/update', [SystemSettingsController::class, 'update']);


    //Roles
    Route::post('/settings/roles/get_data', [RolesController::class, 'get_data']);
    Route::post('/settings/roles/add', [RolesController::class, 'add']);
    Route::post('/settings/roles/update', [RolesController::class, 'update']);
    Route::post('/settings/roles/delete', [RolesController::class, 'delete']);
    Route::post('/settings/roles/get', [RolesController::class, 'get']);



    Route::group([
        'prefix' => 'web'
    ],
        function ($router) {
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
            Route::post('/clients/get_proposal_by_id', [TasksController::class, 'api_get_proposal_by_id']);
            Route::post('/clients/assign_task', [TasksController::class, 'api_assign_task']);


            //Account
            Route::post('/clients/add_payment', [TransactionsController::class, 'api_add_payment']);
            Route::post('/transaction/get_transaction_history', [TransactionsController::class, 'api_get_transaction_history']);
            Route::post('/transaction/get_wallet_summary', [TransactionsController::class, 'api_get_wallet_summary']);


            //Experts
            Route::post('/experts/register_as_a_reference_code', [ExpertsController::class, 'api_register_as_a_reference_code']);
            Route::post('/experts/register_without_reference_code', [ExpertsController::class, 'api_register_without_reference_code']);


            Route::post('/experts/skill_add', [ExpertsController::class, 'api_skill_add']);
            Route::post('/experts/tool_add', [ExpertsController::class, 'api_tool_add']);
            Route::post('/experts/tool_delete', [ExpertsController::class, 'api_tool_delete']);
            Route::post('/experts/get_my_skills', [ExpertsController::class, 'api_get_my_skills']);
            Route::post('/experts/get_my_tools', [ExpertsController::class, 'api_get_my_tools']);
            Route::post('/experts/send_proposal_task', [TasksController::class, 'api_send_proposal_task']);
            Route::post('/experts/change_profile', [ExpertsController::class, 'api_change_profile']);
            Route::post('/experts/get_expert_tasks', [TasksController::class, 'api_get_expert_tasks']);
            Route::post('/experts/get_expert_new_tasks', [TasksController::class, 'api_get_expert_new_tasks']);
            Route::post('/experts/get_proposal_by_id', [TasksController::class, 'api_get_proposal_by_id_expert']);

            Route::post('/experts/get_expert_tasks_send_proposals', [TasksController::class, 'api_get_expert_tasks_send_proposals']);

            //Task
            Route::post('/tasks/get_task_by_id', [TasksController::class, 'api_get_task_by_id']);


            //Test
            Route::post('/test/get_test_detail', [TestTemplatesController::class, 'api_get_test_detail']);
            Route::post('/test/start_test', [TestAttemptsController::class, 'api_start_test']);
            Route::post('/test/end_test', [TestAttemptsController::class, 'api_end_test']);

            Route::post('/notifications/get_notifications', [NotificationsController::class, 'api_get_notifications']);
            Route::post('/notifications/set_notify_notification', [NotificationsController::class, 'api_set_notify_notification']);
            Route::post('/notifications/set_view_notification', [NotificationsController::class, 'api_set_view_notification']);


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