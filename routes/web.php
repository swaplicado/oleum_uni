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
    Route::get('/topics/create', 'TopicsController@create')->name('topics.create');
    Route::post('/topics', 'TopicsController@store')->name('topics.store');

    /**
     * Rutas de CRUD de Subtemas (SubTopics)
     */
    Route::post('/subtopics', 'SubTopicsController@store')->name('subtopics.store');
    // Contenidos vs subtemas
    Route::get('/topics/{course?}/subtopics/{id?}/contents', 'ElementsContentsController@subtopics')->name('subtopics.contents.index');
    Route::post('/topics/{course?}/subtopics/contents', 'ElementsContentsController@store')->name('subtopics.contents.store');

    /**
     * Rutas de Gestión de contenidos
     */
    Route::get('/contents', 'ContentsController@index')->name('contents.index');
    Route::get('/contents/create', 'ContentsController@create')->name('contents.create');
    Route::post('/contents', 'ContentsController@store')->name('contents.store');
    Route::get('/contents/preview', 'ContentsController@getPreview')->name('contents.preview');

});
