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


Route::get('/404', function () {
    return view('layouts.404');
})->name('notfound');

Route::get('/401', function () {
    return view('layouts.401');
})->name('unauthorized');

Route::middleware(['auth', 'menu'])->group(function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/profile', 'ProfilesController@myProfile')->name('profile');
    Route::get('/users', 'UsersController@index')->name('users')->middleware('manager');
    Route::put('/users/resetpass', 'UsersController@resetPassword')->name('users.reset.pass')->middleware('manager');
    Route::put('/users/updemail', 'UsersController@updateEmail')->name('users.update.mail')->middleware('manager');
    Route::put('/users/updusername', 'UsersController@updateUsername')->name('users.update.username')->middleware('manager');
    Route::post('/users/upduserarea', 'UsersController@updateUserArea')->name('users.update.userarea')->middleware('manager');

    /**
     * password
     */
    Route::get('/changepass', 'ProfilesController@changePassword')->name('change.pass');
    Route::post('/changepass', 'ProfilesController@updatePassword')->name('update.pass');

    /**
     * Avatar
     */
    Route::get('/changeavatar', 'ProfilesController@changeAvatar')->name('change.avatar');
    Route::post('/changeavatar', 'ProfilesController@updateAvatar')->name('update.avatar');

    // Controllers Within The "App\Http\Controllers\Adm" Namespace
    Route::prefix('adm')->namespace('Adm')->middleware('manager')->group( function () {
        /**
         * Rutas de CRUD de Sucursales
         */
        Route::resource('branches','BranchesController');

        /**
         * Rutas de CRUD de Empresas
         */
        Route::resource('companies','CompaniesController');

        /**
         * Rutas de CRUD areas funcionales
         */
        Route::get('areasAdm', 'AreasController@index')->name('areasAdm.index');
        Route::get('areasAdm/create', 'AreasController@create')->name('areasAdm.create');
        Route::post('areasAdm/store', 'AreasController@store')->name('areasAdm.store');
        Route::get('areasAdm/edit/{area_id}', 'AreasController@edit')->name('areasAdm.edit');
        Route::post('areasAdm/update/{area_id}', 'AreasController@update')->name('areasAdm.update');
        Route::delete('areasAdm/delete/{area_id}', 'AreasController@destroy')->name('areasAdm.delete');

        Route::get('areasAdm/departments', 'DepartmentsController@indexDepartments')->name('areasAdm.departments');
        Route::post('areasAdm/departments/update', 'DepartmentsController@updateDeptoArea')->name('areasAdm.departments.update');
    });
    
    // Controllers Within The "App\Http\Controllers\Mgr" Namespace
    Route::prefix('mgr')->namespace('Mgr')->middleware('manager')->group( function () {
        /**
         * Rutas sincronizar con mongodb
         */
        Route::get('/SyncMongodb', 'SyncMongodb@syncronizer')->name('syncMongodb');
        
        /**
         * Rutas de CRUD de Áreas de Competencia
         */
        Route::resource('kareas','KnowledgeAreasController');
        Route::post('/kares/status', 'KnowledgeAreasController@updateStatus')->name('kareas.status');
        Route::delete('/kares/delete/{id}', 'KnowledgeAreasController@delete')->name('kareas.delete');
        Route::post('kareas/getModule','KnowledgeAreasController@getModules')->name('kareas.getModule');
        Route::post('kareas/getCourse','KnowledgeAreasController@getCourses')->name('kareas.getCourse');
        Route::post('kareas/getTopic','KnowledgeAreasController@getTopics')->name('kareas.getTopic');
        Route::post('kareas/getSubtopic','KnowledgeAreasController@getSubtopics')->name('kareas.getSubtopic');
        /**
         * Rutas de CRUD de Módulos
         */
        Route::get('/modules/{ka?}', 'ModulesController@index')->name('modules.index');
        Route::get('/modules/{ka}/create', 'ModulesController@create')->name('modules.create');
        Route::post('/modules', 'ModulesController@store')->name('modules.store');
        Route::get('/modules/edit/{id}', 'ModulesController@edit')->name('modules.edit');
        Route::put('/modules/update/{id}', 'ModulesController@update')->name('modules.update');
        Route::post('/modules/status', 'ModulesController@updateStatus')->name('modules.status');
        Route::delete('/modules/delete/{id}', 'ModulesController@delete')->name('modules.delete');
    
        /**
         * Rutas de CRUD de Cursos
         */
        Route::get('/courses/{mod?}', 'CoursesController@index')->name('courses.index');
        Route::get('/courses/{mod}/create', 'CoursesController@create')->name('courses.create');
        Route::post('/courses', 'CoursesController@store')->name('courses.store');
        Route::get('/courses/edit/{id}', 'CoursesController@edit')->name('courses.edit');
        Route::put('/courses/{id}', 'CoursesController@update')->name('courses.update');
        Route::post('/courses/status', 'CoursesController@updateStatus')->name('courses.status');
        Route::delete('/courses/delete/{id}', 'CoursesController@delete')->name('courses.delete');
    
        /**
         * Rutas de CRUD de Temas (Topics)
         */
        Route::get('/topics/{course?}', 'TopicsController@index')->name('topics.index');
        Route::get('/topics/create', 'TopicsController@create')->name('topics.create');
        Route::post('/topics', 'TopicsController@store')->name('topics.store');
        Route::delete('/topics/delete/{id}', 'TopicsController@delete')->name('topics.delete');
        Route::post('/topics/edit/{id}', 'TopicsController@edit')->name('topics.edit');
    
        /**
         * Rutas de CRUD de Subtemas (SubTopics)
         */
        Route::post('/subtopics', 'SubTopicsController@store')->name('subtopics.store');
        Route::delete('/subtopics/{id}', 'SubTopicsController@delete')->name('subtopics.delete');
        Route::post('/subtopics/edit/{id}', 'SubTopicsController@edit')->name('subtopics.edit');
        // Contenidos vs subtemas
        Route::get('/topics/{course?}/subtopics/{id?}/contents', 'ElementsContentsController@subtopics')->name('subtopics.contents.index');
        Route::post('/topics/{course?}/subtopics/contents', 'ElementsContentsController@store')->name('subtopics.contents.store');
        Route::delete('/subtopics/contents/{id}', 'ElementsContentsController@destroyContent')->name('subtopics.contents.destroy');
        // Preguntas de subtemas
        Route::get('/topics/{course?}/subtopics/{id?}/questions', 'QuestionsController@index')->name('subtopics.questions.index');
        Route::post('/question', 'QuestionsController@store')->name('questions.store');
        Route::get('/question', 'QuestionsController@getQuestion')->name('questions.getquestion');
        Route::put('/question', 'QuestionsController@update')->name('questions.update');
        Route::put('/questiondel', 'QuestionsController@delete')->name('questions.delete');
        Route::put('/deleteanswer', 'QuestionsController@deleteAnswer')->name('questions.delanswer');

        /**
         * Rutas de copiado de elementos (subtema, temas, modulos, cuadrantes)
         */
        Route::post('/copyElement', 'copyElementsController@copyElement')->name('copyElement');
    
        /**
         * Rutas de Gestión de contenidos
         */
        Route::get('/contents', 'ContentsController@index')->name('contents.index');
        Route::get('/contents/create', 'ContentsController@create')->name('contents.create');
        Route::post('/contents', 'ContentsController@store')->name('contents.store');
        Route::get('/contents/preview', 'ContentsController@getPreview')->name('contents.preview');
        Route::delete('/contents/{id}', 'ContentsController@destroyContent')->name('contents.destroy');
    
        /**
         * Rutas de asignaciones de áreas de competencia
         */
        Route::get('/assignments', 'AssignmentsController@index')->name('assignments.index');
        Route::get('/getstudents', 'AssignmentsController@getStudents')->name('assignments.getstudents');
        Route::get('/assignments/create', 'AssignmentsController@create')->name('assignments.create');
        Route::post('/assignments', 'AssignmentsController@store')->name('assignments.store');
        Route::delete('/assignments/delete/{id}', 'AssignmentsController@delete')->name('assignments.delete');
        Route::put('/assignments/updateassignment', 'AssignmentsController@updateAssignment')->name('assignments.updateassignment');
        Route::post('/assignments/getDurationDays', 'AssignmentsController@getDurationDays')->name('assignments.getDurationDays');
        
        Route::get('/assignments/scheduled', 'ScheduledAssignmentsController@index')->name('assignments.scheduled.index');
        Route::get('/assignments/scheduled/create', 'ScheduledAssignmentsController@create')->name('assignments.scheduled.create');
        Route::post('/assignments/scheduled', 'ScheduledAssignmentsController@store')->name('assignments.scheduled.store');
        Route::get('/assignments/manualschedule', 'ScheduledAssignmentsController@processAssignmentSchedule')->name('assignments.manual.schedule');

        /**Rutas de asignaciones módulos */
        Route::get('/assignments/{id}/modules', 'AssignmentsController@indexAssignmentModules')->name('assignments.modules');
        Route::put('/assignments/update/modules/{id}', 'AssignmentsController@updateAssignmentModule')->name('assignments.modules.update');
        Route::get('/assignments/{id}/courses/{idModule}', 'AssignmentsController@indexAssignmentCourses')->name('assignments.courses');
        Route::put('/assignments/update/courses/{id}', 'AssignmentsController@updateAssignmentCourse')->name('assignments.courses.update');

        /**Rutas de asignaciones control */
        Route::get('assignments/group', 'groupAssignmentController@index')->name('assignmentsGroup.index');
        Route::get('assignments/group/getModules', 'groupAssignmentController@getModules')->name('assignmentsGroup.getModules');
        Route::post('assignments/group/update', 'groupAssignmentController@updateAssign')->name('assignmentsGroup.update');
        Route::post('assignments/group/delete', 'groupAssignmentController@deleteAssign')->name('assignmentsGroup.delete');

        /**
         * Premios administración
         */
        Route::get('/gifts', 'GiftsController@gifts')->name('gifts.index');
        Route::get('/gifts/create', 'GiftsController@createGift')->name('gifts.create');
        Route::post('/gifts', 'GiftsController@storeGift')->name('gifts.store');
        // stock
        Route::get('/giftstk/create/{class}/{gift}', 'GiftsStockController@create')->name('giftstk.create');
        Route::post('/giftstk', 'GiftsStockController@store')->name('giftstk.store');

        /**
         * Control de puntos
         */
        Route::get('/points', 'PointsController@index')->name('points.index');
        Route::get('/points/detail/{id?}', 'PointsController@getDetail')->name('points.detail');
        Route::post('/points', 'PointsController@store')->name('points.store');

        /**
         * Prerrequisitos
         */
        Route::get('/getpredata', 'PrerequisitesController@getPreData')->name('get.pre.data');
        Route::post('/predata', 'PrerequisitesController@storePreRequisite')->name('store.pre.data');
        Route::put('/predatadelete', 'PrerequisitesController@deleteRow')->name('delete.pre.row');

        /**
         * Carrusel
         */
        Route::get('/carousel', 'CarouselController@index')->name('carousel.index');
        Route::get('/carousel/create', 'CarouselController@create')->name('carousel.create');
        Route::post('/carousel', 'CarouselController@store')->name('carousel.store');
        Route::get('/carousel/edit/{id}', 'CarouselController@edit')->name('carousel.edit');
        Route::put('/carousel/update', 'CarouselController@update')->name('carousel.update');
        Route::delete('/carousel/{id}', 'CarouselController@delete')->name('carousel.delete');
    });
    
    // Controllers Within The "App\Http\Controllers\Uni" Namespace
    Route::prefix('uni')->namespace('Uni')->group( function () {
        /**
         * Universidad Alumno
         */
        Route::get('/areas', 'UniversityController@indexAreas')->name('areas.index');
        Route::get('/modules/{assignment}/{area?}', 'UniversityController@indexModules')->name('uni.modules.index');
        Route::get('/courses/{assignment}/{module}', 'UniversityController@indexCourses')->name('uni.courses.index');
        Route::get('/course/{course?}/{assignment?}', 'UniversityController@viewCourse')->name('uni.courses.course');
        Route::get('/course/play/{subtopic}/{grouper}/{assignment}', 'UniversityController@playSubtopic')->name('uni.courses.course.play');

        /**
         * Toma de curso
         */
        Route::post('/takecourse', 'TakesController@takeCourse')->name('take.course');
        Route::post('/takecontent', 'TakesController@takeSubtopicContent')->name('take.content');

        /**
         * Reviews de curso
         */
        Route::post('/reviews/store', 'ReviewsController@storeCourseReviews')->name('reviews.store');
    
        /**
         * Exámenes
         */
        Route::get('/exam/{subtopic}/{taken}/{grouper}', 'ExamsController@exam')->name('exam.evaluate');
        Route::post('/exam/recordanswer', 'ExamsController@recordAnswer')->name('exam.record.answer');
        Route::post('/exam/recordexam', 'ExamsController@recordExam')->name('exam.record.exam');
        
        /**
         * Kardex
         */
        Route::get('/kardex/{student?}', 'KardexController@index')->name('kardex.index')->middleware('head');
        Route::get('/kardex/index/head', 'KardexController@indexHead')->name('kardex.head');
        Route::get('/kardex/modules/{area}/{assignment}/{student?}', 'KardexController@kardexModules')->name('kardex.modules');
        Route::get('/kardex/courses/{module}/{assignment}/{student?}', 'KardexController@kardexCourses')->name('kardex.courses');
        // Route::get('/kardexreport', 'KardexController@generateReport')->name('kardex.report');
        Route::get('/Reports', 'KardexController@Reports')->name('Reports');
        Route::get('/kardexreport', 'KardexController@indexReport')->name('kardex.indexReport');
        Route::post('/kardexGenReport', 'KardexController@generateMReporte')->name('kardex.generateReport');

        /**
         * Certificados
         */
        Route::get('/certificate/{elem_type}/{id}/{assignment}', 'CertificatesController@generateAreaCertificate')->name('certificate');

        /**
         * Premios
         */
        Route::get('/shop', 'ShopController@index')->name('shop');
        Route::post('/shop', 'ShopController@exchange')->name('shop.exchange');

        /**
         * Notificaciones
         */
        Route::post('/notify_question', 'NotificationsController@sendQuestion')->name('notify.question');
    });
    
    // Controllers Within The "App\Http\Controllers\Sys" Namespace
    Route::prefix('sys')->namespace('Sys')->group( function () {
        /**
         * Sincronización
         */
        Route::get('/tosynchronize', 'SyncController@toSynchronize')->name('to.synchronize');
    });

});