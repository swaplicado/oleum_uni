<?php

namespace App\Http\Controllers\Mgr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Database\QueryException;

use App\Sys\Sequence;
use App\Uni\KnowledgeArea;
use App\Uni\Module;
use App\Uni\Course;
use App\Uni\EduContent;
use App\Uni\ElementContent;

use App\Utils\assignmentsUtils;

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

        $lCuadrantes = \DB::table('uni_knowledge_areas AS ka')
                        ->join('sys_element_status AS es', 'ka.elem_status_id', '=', 'es.id_element_status')
                        ->join('sys_sequences AS seq', 'ka.sequence_id', '=', 'seq.id_sequence')
                        ->select(['ka.id_knowledge_area',
                                'ka.knowledge_area_title',
                                'ka.knowledge_area',
                                'ka.description',
                                'ka.is_deleted',
                                'es.code AS status_code',
                                'seq.code AS seq_code',
                                ])
                        ->where('ka.is_deleted', 0)
                                ->get();

        return view('mgr.courses.index')->with('title', $title)
                                        ->with('newRoute', $this->newRoute)
                                        ->with('moduleId', $moduleId)
                                        ->with('lCuadrantes', $lCuadrantes)
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

        $lCourses = \DB::table('uni_courses')
                        ->where([['is_deleted', 0],['module_id', $moduleId]])
                        ->get();

        return view('mgr.courses.create')->with('title', $title)
                                        ->with('storeRoute', $this->storeRoute)
                                        ->with('moduleId', $moduleId)
                                        ->with('lContents', $lContents)
                                        ->with('sequences', $seq)
                                        ->with('lCourses', $lCourses)
                                        ->with('moduleDays', $oModule->completion_days);
    }

    public function store(Request $request)
    {
        try {
            $oCourse = new Course();

            $oCourse->course = $request->course;
            $oCourse->course_key = $request->course_key;
            $oCourse->hash_id = hash('ripemd160', $oCourse->course);
            $oCourse->completion_days = $request->completion_days;
            $oCourse->pre_course_id = $request->pre_course;
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
            $session = \DB::connection('mongodb')->getMongoClient()->startSession();
            $session->startTransaction();

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

            if(!assignmentsUtils::validateTotalDaysInModule($oCourse->module_id)){
                throw new \Exception("El número total de días del curso '".$oCourse->course."' es superior al número de días del módulo");
            }

            if(assignmentsUtils::getAssignmentByModule($request->module_id) > 0){
                $ka_id = assignmentsUtils::getCuadranteIdFromModule($request->module_id);
                assignmentsUtils::setModuleAssignments($ka_id);
            }

            \DB::commit();
            $session->commitTransaction();
        }
        catch (\Throwable $th) {
            \DB::rollBack();
            $session->abortTransaction();
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

        $lCourses = \DB::table('uni_courses')
                        ->where([['is_deleted', 0],['module_id', $oCourse->module_id], ['id_course', '!=', $id]])
                        ->get();

        return view('mgr.courses.edit')->with('title', $title)
                                        ->with('updateRoute', $this->updateRoute)
                                        ->with('oCourse', $oCourse)
                                        ->with('lContents', $lContents)
                                        ->with('oCover', $oCover)
                                        ->with('sequences', $seq)
                                        ->with('lCourses', $lCourses);
    }

    public function update(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            
            $oCourse = Course::find($id);

            $oCourse->course = $request->course;
            $oCourse->course_key = $request->course_key;
            $oCourse->completion_days = $request->completion_days;
            $oCourse->pre_course_id = $request->pre_course;
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

            if(!assignmentsUtils::validateTotalDaysInModule($oCourse->module_id)){
                throw new \Exception("El número total de días del curso '".$oCourse->course."' es superior al número de días del módulo");
            }

            if(assignmentsUtils::getAssignmentByModule($oCourse->module_id) > 0){
                $ka_id = assignmentsUtils::getCuadranteIdFromModule($oCourse->module_id);
                assignmentsUtils::setModuleAssignments($ka_id);
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

    public function delete($id){
        $success = true;

        try {
            DB::transaction(function () use ($id) {
                $build = DB::table('uni_courses as co')
                            ->leftJoin('uni_topics as top', 'top.course_id', '=', 'co.id_course')
                            ->leftJoin('uni_subtopics as sub', 'sub.topic_id', '=', 'top.id_topic')
                            ->leftJoin('uni_questions as q', 'q.subtopic_id', '=', 'sub.id_subtopic')
                            ->where('co.id_course',$id);

                $isIncurse = DB::table('uni_courses as co')
                            ->leftJoin('uni_modules as mo', 'mo.id_module', '=', 'co.module_id')
                            ->leftJoin('uni_knowledge_areas as ka', 'ka.id_knowledge_area', '=', 'mo.knowledge_area_id')
                            ->leftJoin('uni_assignments as ag', 'ag.knowledge_area_id', '=', 'ka.id_knowledge_area')
                            ->select('ag.id_assignment','ag.is_over')
                            ->where('co.id_course',$id)->where('ag.is_over',0)
                            ->get();

                if($isIncurse->isEmpty()){
                    $result = $build->select('co.id_course','sub.id_subtopic')->get();

                    DB::table('uni_contents_vs_elements')
                        ->where(function($query) use ($result) {
                            $query->whereIn('subtopic_n_id',$result->pluck('id_subtopic')->toArray());
                        })->orWhere(function($query) use ($result) {
                                $query->whereIn('course_n_id',$result->pluck('id_course')->toArray());
                        })
                        ->delete();
                        
                    $build->update(['co.is_deleted' => 1, 'top.is_deleted' => 1, 'sub.is_deleted' => 1, 'q.is_deleted' => 1]);
                }else{
                    throw new \Exception('area en curso');
                }
                
            });
        } catch (QueryException $e) {
            $success = false;
            $msg = "Error al eliminar el registro";
            $icon = "error";
        } catch (\Exception $e) {
            $success = false;
            $msg = "El curso está siendo cursado";
            $icon = "error";
        }

        if ($success) {
            $msg = "Se eliminó el registro con éxito";
            $icon = "success";
        }

        return redirect()->back()->with(['message' => $msg, 'icon' => $icon]);
    }
}
