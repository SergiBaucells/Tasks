<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\MultimediaController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\ChangelogController;
use App\Http\Controllers\ClockController;
use App\Http\Controllers\LoggedUserAvatarController;
use App\Http\Controllers\LoggedUserPhotoController;
use App\Http\Controllers\MobileController;
use App\Http\Controllers\NewslettersController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TasquesController;
use App\Http\Controllers\Web\ChatController;
use App\Http\Controllers\VerifyMobileController;
use App\Task;
use Faker\Factory;

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::post('/tasks', 'TasksController@store');
    Route::delete('/tasks/{id}', 'TasksController@destroy');
    Route::put('/tasks/{id}', 'TasksController@update');
    Route::get('/task_edit/{id}', 'TasksController@edit');
    Route::get('/about', function () {
        return view('about');
    });
    Route::view('/calendari', 'calendari');
    Route::get('/tasks_vue', 'TasksVueController@index');
    Route::get('/tasques', 'TasquesController@index');
    Route::get('/tasques/{id}', '\\' . TasquesController::class . '@show');
    Route::get('/home', 'TasquesController@index');
    // Propies
    Route::post('/taskscompleted/{task}', 'TasksCompletedController@store');
    Route::delete('/taskscompleted/{task}', 'TasksCompletedController@destroy');
    Route::get('/tasks', 'TasksController@index');
    Route::get('/', 'TasksController@index');
    // User tasks
    Route::get('/user/tasks', 'LoggedUserTasksController@index');
    Route::impersonate();
    // Tags
    Route::get('/tags', 'TagsController@index');

    Route::get('/profile', '\\' . ProfileController::class . '@show');


    Route::post('/photo', '\\' . PhotoController::class . '@store');
    Route::post('/avatar', '\\' . AvatarController::class . '@store');


    Route::get('/user/photo', '\\' . LoggedUserPhotoController::class . '@show');
    Route::get('/user/avatar', '\\' . LoggedUserAvatarController::class . '@show');

    //Changelog
    Route::get('/changelog', '\\' . ChangelogController::class . '@index');
//    Route::get('/changelog/module/{module}','Tenant\Web\ChangelogModuleController@index');
//    Route::get('/changelog/user/{user}','Tenant\Web\ChangelogUserController@index');
//    Route::get('/changelog/loggable/{loggable}/{loggableId}','Tenant\Web\ChangelogLoggableController@index');
    Route::get('/notifications', '\\' . NotificationController::class . '@index');

    // Mobile
    Route::get('/mobile', '\\' . MobileController::class . '@index');
    Route::get('/verificar_mobil','\\' . VerifyMobileController::class . '@index');
    Route::post('/verificar_mobil','\\' . VerifyMobileController::class . '@send');

    //Newsletters
    Route::get('/newsletters', '\\' . NewslettersController::class . '@index');

    //Clock
    Route::get('/clock', '\\' . ClockController::class . '@index');

    //Multimedia
    Route::get('/multimedia', '\\' . MultimediaController::class . '@index');

    //Xat
    Route::get('/chat', '\\' . ChatController::class . '@index');
    Route::get('/xat', '\\' . ChatController::class . '@index');

    Route::get('/users', '\\' . UsersController::class . '@index');

    Route::post('/subscriptions', '\\' . PushSubscriptionController::class . '@update');
    Route::post('/subscriptions/Delete', '\\' . PushSubscriptionController::class . '@destroy');


});

Route::get('/game', '\\' . GameController::class . '@index');

Route::post('/login_alt', 'Auth\LoginAltController@login');

Route::post('/register_alt', 'Auth\RegisterAltController@store');

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

// Login Facebook
//Route::get('/auth/facebook', '\\' . LoginController::class . '@redirectToProvider');
//Route::get('/auth/facebook/callback', '\\' . LoginController::class . '@handleProviderCallback');

// Login GitHub
//Route::get('/auth/github', '\\' . LoginController::class . '@redirectToProvider');
//Route::get('/auth/github/callback', '\\' . LoginController::class . '@handleProviderCallback');

Route::get('/auth/{provider}', '\\' . LoginController::class . '@redirectToProvider');
Route::get('/auth/{provider}/callback', '\\' . LoginController::class . '@handleProviderCallback');

Route::get('/prova_cua', function () {
    dump('SHIT!');
    \App\Jobs\SleepJob::dispatch();
});

Route::get('/omplir', function () {
    // 10 000
    for ($i = 1; $i <= 10000; $i++) {
        Task::create([
            'name' => 'Prova'
        ]);
    }
});
