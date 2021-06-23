<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Sys\Sequence;
use App\Uni\KnowledgeArea;
use App\Uni\Module;
use App\Uni\Course;

class CoursesController extends Controller
{
    protected $newRoute;
    protected $storeRoute;

    /**
     * Create a new controller instance.
     *
     * @param  UserRepository  $users
     * @return void
     */
    public function __construct()
    {
        $this->newRoute = "courses.create";
        $this->storeRoute = "courses.store";
    }

    /**
     * Show the application index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, $moduleId)
    {
        $title = 'Cursos';

        $lCourses = \DB::table('uni_courses AS co')
                    ->join('uni_modules AS mo', 'co.module_id', '=', 'mo.id_module')
                    ->join('sys_element_status AS es', 'co.elem_status_id', '=', 'es.id_element_status')
                    ->join('sys_sequences AS seq', 'co.sequence_id', '=', 'seq.id_sequence')                    
                    ->select(['co.id_course',
                            'co.course',
                            'co.course_key',
                            'co.description',
                            'co.completion_days',
                            'co.university_points',
                            'co.module_id',
                            'co.is_deleted',
                            'es.code AS status_code',
                            'seq.code AS seq_code',
                            'mo.module'
                            ]);

        if (isset($moduleId) && $moduleId > 0) {
            $lCourses = $lCourses->where('module_id', $moduleId);
        }

        $lCourses = $lCourses->get();

        return view('mgr.courses.index')->with('title', $title)
                                        ->with('newRoute', $this->newRoute)
                                        ->with('moduleId', $moduleId)
                                        ->with('lCourses', $lCourses);
    }

    public function create(Request $request, $moduleId)
    {
        $oModule = Module::find($moduleId);
        $title = "Crear cursos para ".$oModule->module;

        $seq = Sequence::selectRaw('CONCAT(code, " - ", sequence) AS seq, id_sequence')
                        ->get();

        return view('mgr.courses.create')->with('title', $title)
                                        ->with('storeRoute', $this->storeRoute)
                                        ->with('moduleId', $moduleId)
                                        ->with('sequences', $seq);
    }

    public function store(Request $request)
    {
        try {
            $oCourse = new Course();

            $oCourse->course = $request->course;
            $oCourse->course_key = $request->course_key;
            $oCourse->hash_id = hash('ripemd160', $oCourse->course);
            $oCourse->completion_days = $request->completion_days;
            $oCourse->university_points = $request->university_points;
            $oCourse->description = $request->description;
            $oCourse->objectives = $request->objectives;
            $oCourse->is_deleted = 0;
            $oCourse->module_id = $request->module_id;
            $oCourse->elem_status_id = config('csys.elem_status.NEW');
            $oCourse->sequence_id = $request->sequence;
            $oCourse->created_by_id = \Auth::id();
            $oCourse->updated_by_id = \Auth::id();

            $oCourse->save();
        }
        catch (\Throwable $th) {
            return back()->withError($th->getMessage())->withInput();
        }

        return redirect()->route('courses.index', $oCourse->module_id);
    }
}
