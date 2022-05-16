<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Sys\Sequence;
use App\Uni\KnowledgeArea;
use App\Uni\Module;
use App\Uni\Course;
use App\Uni\EduContent;
use App\Uni\ElementContent;

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
        $this->updateRoute = "courses.update";
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
                            ])
                    ->where('mo.is_deleted', 0)
                    ->where('co.is_deleted', 0);

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
        $title = "Crear curso para ".$oModule->module;

        $seq = Sequence::selectRaw('CONCAT(code, " - ", sequence) AS seq, id_sequence')
                        ->get();

        $lContents = EduContent::where('is_deleted', false)
                                ->whereIn('file_type', ['image', 'video'])
                                ->get();

        foreach ($lContents as $content) {
            $content->f_type = $content->file_type == 'image' ? 'Imagen' : 'Video';
        }

        return view('mgr.courses.create')->with('title', $title)
                                        ->with('storeRoute', $this->storeRoute)
                                        ->with('moduleId', $moduleId)
                                        ->with('lContents', $lContents)
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
            $oCourse->has_points = isset($request->has_points);
            $oCourse->university_points = $request->university_points;
            $oCourse->description = $request->description;
            $oCourse->objectives = $request->objectives;
            $oCourse->has_document = isset($request->has_document);
            $oCourse->is_deleted = 0;
            $oCourse->module_id = $request->module_id;
            $oCourse->elem_status_id = config('csys.elem_status.NEW');
            $oCourse->sequence_id = $request->sequence;
            $oCourse->created_by_id = \Auth::id();
            $oCourse->updated_by_id = \Auth::id();

            \DB::beginTransaction();

            $oCourse->save();

            if($request->course_cover != 0){
                $elem = new ElementContent();

                $elem->order = 1;
                $elem->content_id = $request->course_cover;
                $elem->element_type_id = config('csys.elem_type.COURSE');
                $elem->course_n_id = $oCourse->id_course;
                $elem->created_by_id = \Auth::id();
                $elem->updated_by_id = \Auth::id();

                $elem->save();
            }

            \DB::commit();
        }
        catch (\Throwable $th) {
            \DB::rollBack();
            return back()->withError($th->getMessage())->withInput();
        }

        return redirect()->route('courses.index', $oCourse->module_id);
    }

    public function edit($id)
    {
        $oCourse = Course::find($id);

        $title = "Editar curso ".$oCourse->course;

        $seq = Sequence::selectRaw('CONCAT(code, " - ", sequence) AS seq, id_sequence')
                        ->get();

        $lContents = EduContent::where('is_deleted', false)
                                ->whereIn('file_type', ['image', 'video'])
                                ->get();

        foreach ($lContents as $content) {
            $content->f_type = $content->file_type == 'image' ? 'Imagen' : 'Video';
        }

        $oCovert = ElementContent::where('course_n_id', $oCourse->id_course)
                                    ->where('element_type_id', config('csys.elem_type.COURSE'))
                                    ->orderBy('created_at', 'DESC')
                                    ->first();

        if ($oCovert != null) {
            $oCover = EduContent::find($oCovert->content_id);
        }
        else {
            $oCover = null;
        }

        return view('mgr.courses.edit')->with('title', $title)
                                        ->with('updateRoute', $this->updateRoute)
                                        ->with('oCourse', $oCourse)
                                        ->with('lContents', $lContents)
                                        ->with('oCover', $oCover)
                                        ->with('sequences', $seq);
    }

    public function update(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            
            $oCourse = Course::find($id);

            $oCourse->course = $request->course;
            $oCourse->course_key = $request->course_key;
            $oCourse->completion_days = $request->completion_days;
            $oCourse->university_points = $request->university_points;
            $oCourse->description = $request->description;
            $oCourse->objectives = $request->objectives;
            $oCourse->has_document = isset($request->has_document);
            $oCourse->elem_status_id = config('csys.elem_status.NEW');
            $oCourse->sequence_id = $request->sequence;
            $oCourse->created_by_id = \Auth::id();
            $oCourse->updated_by_id = \Auth::id();

            $oCourse->save();

            ElementContent::where('element_type_id', config('csys.elem_type.COURSE'))
                            ->where('course_n_id', $oCourse->id_course)
                            ->delete();

            if($request->course_cover != 0){
                $elem = new ElementContent();
    
                $elem->order = 1;
                $elem->content_id = $request->course_cover;
                $elem->element_type_id = config('csys.elem_type.COURSE');
                $elem->course_n_id = $oCourse->id_course;
                $elem->created_by_id = \Auth::id();
                $elem->updated_by_id = \Auth::id();
    
                $elem->save();
            }                            

            \DB::commit();
        }
        catch (\Throwable $th) {
            \DB::rollBack();
            
            return back()->withError($th->getMessage())->withInput();
        }

        return redirect()->route('courses.index', $oCourse->module_id);
    }

    public function updateStatus(Request $request){
        try {
            $oCourse = Course::find($request->row_id);
            $oCourse->elem_status_id = (Integer)$request->estatus;
            $oCourse->updated_by_id = \Auth::id();
            $oCourse->update();
        }
        catch (\Throwable $th) {
            return back()->withError($th->getMessage())->withInput();
        }
        
        return redirect()->route('courses.index', $oCourse->module_id)->with('success', 'El registro se actualizó correctamente.');
    }
}
