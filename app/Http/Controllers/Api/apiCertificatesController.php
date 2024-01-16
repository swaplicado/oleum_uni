<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Carbon\Carbon;

use App\User;
use App\Uni\Assignment;
use App\Uni\TakingControl;
use App\Uni\KnowledgeArea;
use App\Uni\Module;
use App\Uni\Course;

use iio\libmergepdf\Merger;

class apiCertificatesController extends Controller
{
    public static function getCuadrantsByUser($user_id, $start_date, $end_date){
        $lCuadrantsAssigments = \DB::table('uni_assignments as a')
                                    ->join('uni_knowledge_areas as k', 'k.id_knowledge_area', '=', 'a.knowledge_area_id')
                                    ->where('a.student_id', $user_id)
                                    ->where('a.is_deleted', 0)
                                    ->where('k.is_deleted', 0)
                                    ->select(
                                        \DB::raw('MAX(a.id_assignment) as id_assignment'),
                                        'k.id_knowledge_area as id_cuadrant',
                                        'k.knowledge_area_title as cuadrant_title',
                                        'k.knowledge_area as cuadrant_name',
                                        'a.student_id',
                                        \DB::raw('MAX(dt_assignment) as ultima_asignacion'),
                                        'dt_end',
                                        'is_closed'
                                        )
                                    ->selectRaw('1 as asignado')
                                    ->groupBy(['id_cuadrant', 'student_id'])
                                    ->get();

        $lCuadrantsNoAssigments = \DB::table('uni_knowledge_areas as k')
                                    ->where('is_deleted', 0)
                                    ->whereNotIn('id_knowledge_area', $lCuadrantsAssigments->pluck('id_cuadrant'))
                                    ->select(
                                            'id_knowledge_area as id_cuadrant',
                                            'knowledge_area_title as cuadrant_title',
                                            'knowledge_area as cuadrant_name',
                                        )
                                    ->selectRaw('"" as student_id')
                                    ->selectRaw('"" as ultima_asignacion')
                                    ->selectRaw('0 as asignado')
                                    ->selectRaw('"" as control')
                                    ->selectRaw('0 as withCertificate')
                                    ->get();

        foreach($lCuadrantsAssigments as $cuadrant){
            $cuadrant->control = \DB::table('uni_taken_controls as c')
                                    ->join('sys_take_status as s', 's.id_status', '=', 'status_id')
                                    ->where('c.assignment_id', $cuadrant->id_assignment)
                                    ->where('c.knowledge_area_n_id', $cuadrant->id_cuadrant)
                                    ->where('c.is_deleted', 0)
                                    ->select(
                                        \DB::raw('MAX(c.id_taken_control) as id_control'),
                                        \DB::raw('MAX(c.dtt_take) as dtt_take'),
                                        'c.dtt_end',
                                        'c.min_grade',
                                        'c.grade',
                                        's.id_status',
                                        's.status',
                                        \DB::raw('
                                            CASE 
                                                WHEN dtt_end != "null" THEN 
                                                    (CASE WHEN grade >= min_grade THEN 1 ELSE 0 END) 
                                                ELSE 0 
                                            END AS aprobe')
                                    )
                                    ->latest()->first();

            if(!is_null($cuadrant->control->dtt_take)){
                $cuadrant->cuadrantStatus = $cuadrant->control->id_status == 7 ? 
                                                ( $cuadrant->control->aprobe == 1 ? 'Aprobado' : 'No aprobado' ) :
                                                    $cuadrant->control->status;

                $cuadrant->withCertificate = $cuadrant->control->id_status == 7 ? 
                                                ( $cuadrant->control->aprobe == 1 ? 1 : 0 ) :
                                                    0;
            }else{
                $cuadrant->cuadrantStatus = 'No cursado';
                $cuadrant->withCertificate = 0;
            }

            $cuadrant->modules = \DB::table('uni_modules')
                                ->where('is_deleted', 0)
                                ->where('knowledge_area_id', $cuadrant->id_cuadrant)
                                ->select(
                                    'id_module',
                                    'module',
                                )
                                ->get();

            foreach($cuadrant->modules as $module){
                $module->control = \DB::table('uni_taken_controls as c')
                                    ->join('sys_take_status as s', 's.id_status', '=', 'status_id')
                                    ->where('c.assignment_id', $cuadrant->id_assignment)
                                    ->where('c.module_n_id', $module->id_module)
                                    ->where('c.is_deleted', 0)
                                    ->select(
                                        \DB::raw('MAX(c.id_taken_control) as id_control'),
                                        \DB::raw('MAX(c.dtt_take) as dtt_take'),
                                        'c.dtt_end',
                                        'c.min_grade',
                                        'c.grade',
                                        's.id_status',
                                        's.status',
                                        \DB::raw('
                                            CASE 
                                                WHEN dtt_end != "null" THEN 
                                                    (CASE WHEN grade >= min_grade THEN 1 ELSE 0 END) 
                                                ELSE 0 
                                            END AS aprobe')
                                    )
                                    ->latest()->first();

                if(!is_null($module->control->dtt_take)){
                    $module->moduleStatus = $module->control->id_status == 7 ? 
                                                    ( $module->control->aprobe == 1 ? 'Aprobado' : 'No aprobado' ) :
                                                    $module->control->status;
    
                    $module->withCertificate = $module->control->id_status == 7 ? 
                                                    ( $module->control->aprobe == 1 ? 1 : 0 ) :
                                                        0;
                }else{
                    $module->moduleStatus = 'No cursado';
                    $module->withCertificate = 0;
                }

                $module->courses = \DB::table('uni_courses')
                                    ->where('is_deleted', 0)
                                    ->where('module_id', $module->id_module)
                                    ->select(
                                        'id_course',
                                        'course',
                                    )
                                    ->get();

                foreach($module->courses as $course){
                    $course->control = \DB::table('uni_taken_controls as c')
                                            ->join('sys_take_status as s', 's.id_status', '=', 'status_id')
                                            ->where('c.assignment_id', $cuadrant->id_assignment)
                                            ->where('c.module_n_id', $module->id_module)
                                            ->where('c.is_deleted', 0)
                                            ->select(
                                                \DB::raw('MAX(c.id_taken_control) as id_control'),
                                                \DB::raw('MAX(c.dtt_take) as dtt_take'),
                                                'c.dtt_end',
                                                'c.min_grade',
                                                'c.grade',
                                                's.id_status',
                                                's.status',
                                                \DB::raw('
                                                    CASE 
                                                        WHEN dtt_end != "null" THEN 
                                                            (CASE WHEN grade >= min_grade THEN 1 ELSE 0 END) 
                                                        ELSE 0 
                                                    END AS aprobe')
                                            )
                                            ->latest()->first();

                    if(!is_null($course->control->dtt_take)){
                        $course->courseStatus = $course->control->id_status == 7 ? 
                                                        ( $course->control->aprobe == 1 ? 'Aprobado' : 'No aprobado' ) :
                                                        $course->control->status;
        
                        $course->withCertificate = $course->control->id_status == 7 ? 
                                                        ( $course->control->aprobe == 1 ? 1 : 0 ) :
                                                            0;
                    }else{
                        $course->courseStatus = 'No cursado';
                        $course->withCertificate = 0;
                    }
                }
            }
        }

        $lCuadrants = $lCuadrantsAssigments->merge($lCuadrantsNoAssigments);

        return $lCuadrants;
    }

    public function getCuadrants(Request $request){
        $arrayEmployees = json_decode($request->lEmployees);

        $lEmployees = \DB::table('users')
                        ->whereIn('external_id', $arrayEmployees)
                        ->where('is_deleted', 0)
                        ->where('is_active', 1)
                        ->select('id', 'full_name')
                        ->get();

        foreach($lEmployees as $employee){
            $employee->cuadrants = self::getCuadrantsByUser($employee->id, $request->start_date, $request->end_date);
        }


        return response()->json([
            'status' => 'success',
            'message' => "Se obtuvieron los cuadrantes de los empleados correctamente",
            'data' => $lEmployees
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function getCertificates(Request $request){
        $lAssigments = collect($request->lAssigments);
        $mpdfArray = array();
        $employeesPdfs = array();

        $lEmployees = $lAssigments->unique('id_employee_univ');

        foreach($lEmployees as $employee){
            $lAssignEmp = $lAssigments->where('id_employee_univ', $employee['id_employee_univ']);
            $mpdfArray = [];
            foreach($lAssignEmp as $assignment){
                switch ($assignment['type']) {
                    case 1:
                        $mpdf = self::generateCertificate($assignment['type'], $assignment['id_cuadrant'], $assignment['id_assignment']);
                        break;
                    case 2:
                        $mpdf = self::generateCertificate($assignment['type'], $assignment['id_module'], $assignment['id_assignment']);
                        break;
                    case 3:
                        $mpdf = self::generateCertificate($assignment['type'], $assignment['id_course'], $assignment['id_assignment']);
                        break;
                    
                    default:
                        break;
                }
                
                $mpdfArray[] = $mpdf;
                // $mpdfArray[] = base64_encode($mpdf->Output('', 'S'));
            }
            // Crear un nuevo objeto mergePDF
            $merger = new Merger();
    
            // Agregar los objetos mPDF al PDF combinado
            foreach ($mpdfArray as $mpdf) {
                $merger->addRaw($mpdf->Output('', 'S'));
            }
            $combinedPdf = $merger->merge();
            $employeesPdfs[] = json_encode(['employee' => $employee['employee'], 'pdf' => base64_encode($combinedPdf)]);
        }


        return response()->json([
            'status' => 'success',
            'message' => "Se obtuvieron los certificados de los empleados correctamente",
            'data' => $employeesPdfs
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }


    public static function generateCertificate($elementType, $id, $assignment)
    {
        $oAssignment = Assignment::find($assignment);

        $student = $oAssignment->student_id;
        $sTypeText = "";
        $sDocText = "";
        $art = "";
        $title = "";
        $oObj = null;

        $lTake = TakingControl::where('status_id', '=', config('csys.take_status.COM'))
                                ->where('element_type_id', $elementType);
        
        $stylesheet = file_get_contents('myapp/css/certificates/certificate.css');

        switch ($elementType) {
            case config('csys.elem_type.AREA'):
                $lTake = $lTake->where('knowledge_area_n_id', $id);
                $art = "el";
                $sDocText = "CERTIFICADO";
                $sTypeText = "CUADRANTE";
                $oObj = KnowledgeArea::find($id);
                $title = $oObj->knowledge_area;
                $stylesheetBg = file_get_contents('myapp/css/certificates/certificate-bg.css');
                break;
            
            case config('csys.elem_type.MODULE'):
                $lTake = $lTake->where('module_n_id', $id);
                $art = "el";
                $sDocText = "RECONOCIMIENTO";
                $sTypeText = "MÓDULO";
                $oObj = Module::find($id);
                $title = $oObj->module;
                $stylesheetBg = file_get_contents('myapp/css/certificates/reconocimiento-bg.css');
                break;
            
            case config('csys.elem_type.COURSE'):
                $lTake = $lTake->where('course_n_id', $id);
                $art = "la";
                $sDocText = "CONSTANCIA";
                $sTypeText = "CURSO";
                $oObj = Course::find($id);
                $title = $oObj->course;
                $stylesheetBg = file_get_contents('myapp/css/certificates/constancia-bg.css');
                break;
            
            default:
                # code...
                break;
        }

        $oTake = $lTake->where('assignment_id', $assignment)
                        ->where('is_deleted', false)
                        ->orderBy('id_taken_control', 'DESC')
                        ->first();

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter', 'orientation' => 'L']);

        $mpdf->SetTitle(ucwords(strtolower($sDocText." ".$title)));
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($stylesheetBg, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML('<div style="height: 130px"></div>');
        $mpdf->WriteHTML('<h4 style="font-size: 20px;" class="alg-center text-blueblack">Otorga '.$art.' presente:</h4>');
        // $mpdf->WriteHTML('<div style="height: 10px"></div>');
        $mpdf->WriteHTML('<h1 class="alg-center big-with-back">'.$sDocText.'</h1>');
        $mpdf->WriteHTML('<div style="height: 5px"></div>');
        $mpdf->WriteHTML('<h4 style="font-size: 20px; margin-bottom: 10px;" class="alg-center text-blueblack">a</h4>');

        // $mpdf->WriteHTML('<div align="center"><img src="img/tron2.png" alt="AETH"><div>');

        $mpdf->WriteHTML('<div style="height: 5px"></div>');
        $oStudent = User::find($student);
        $body = '<h2 style="font-size: 35px;" class="alg-center text-green">'.str_replace(",", "", $oStudent->full_name).'</h2>';
        $mpdf->WriteHTML($body);
        $mpdf->WriteHTML('<div style="height: 1px"></div>');
        $mpdf->WriteHTML('<h4 style="font-size: 20px;" class="alg-center text-blueblack">Por haber completado el '.$sTypeText.'</h4>');
        $titleDiv = '<div style="position: absolute; left: 0; right: 0; top: 500; bottom: 70;">
                        <h3 style="font-size: 40px; line-height: 30px;" class="alg-center text-blueblack">'.$title.'</h3>
                    </div>';

        $mpdf->WriteHTML($titleDiv);

        $oDateIni = Carbon::parse($oAssignment->dt_assignment);
        $oDateIni->locale();
        $oDateFin = Carbon::parse($oAssignment->dt_end);
        // $mpdf->WriteHTML('<div style="height: 2px"></div>');
        $place = '<div style="position: absolute; left: 10; right: 0; top: 595; bottom: 70;">
                    <h5 style="font-size: 20px;" class="alg-center text-blueblack">
                        Morelia Michoacán, '.$oDateIni->monthName.' '.$oDateIni->format('Y').
                    '</h5>
                </div>';

        $mpdf->WriteHTML($place);

        $date = Carbon::now();

        $key = $oStudent->full_name.'_'.$oTake->grouper.'_'.$sTypeText.'_'.$title.'_'.$oAssignment->id_assignment.'_'.$oTake->id_taken_control;

        QrCode::size(50)->generate($key, 'img/j.png');

        $qr = '<div style="position: absolute; left: 50; right: 0; top: 600; bottom: 70;">
                    <img src="img/j.png" 
                        style="width: 90%; height: 90%; margin: 0;" />
                </div>';

        $mpdf->WriteHTML($qr);

        return $mpdf;
    }
}
