<?php

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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Controllers Within The "App\Http\Controllers\Adm" Namespace
Route::prefix('mgr')->namespace('Mgr')->group( function () {
    /**
     * Rutas de CRUD de Áreas de Competencia
     */
    Route::resource('kareas','KnowledgeAreasController');

    /**
     * Rutas de CRUD de Módulos
     */
    Route::get('/modules/{ka?}', 'ModulesController@index')->name('modules.index');
    Route::get('/modules/{ka}/create', 'ModulesController@create')->name('modules.create');
    Route::post('/modules', 'ModulesController@store')->name('modules.store');

    /**
     * Rutas de CRUD de Cursos
     */
    Route::get('/courses/{mod?}', 'CoursesController@index')->name('courses.index');
    Route::get('/courses/{mod}/create', 'CoursesController@create')->name('courses.create');
    Route::post('/courses', 'CoursesController@store')->name('courses.store');

    /**
     * Rutas de CRUD de Temas (Topics)
     */
    Route::get('/topics/{course?}', 'TopicsController@index')->name('topics.index');
    Route::get('/topics/{course}/create', 'TopicsController@create')->name('topics.create');
});
