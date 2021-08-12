<?php

namespace App\Http\Controllers\Uni;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use App\User;
use App\Uni\Assignment;
use App\Uni\TakingControl;
use App\Uni\KnowledgeArea;
use App\Uni\Module;
use App\Uni\Course;

class CertificatesController extends Controller
{
    public function generateAreaCertificate($elementType, $id, $assignment)
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
        
        switch ($elementType) {
            case config('csys.elem_type.AREA'):
                $lTake = $lTake->where('knowledge_area_n_id', $id);
                $art = "el";
                $sDocText = "CERTIFICADO";
                $sTypeText = "ÁREA DE COMPETENCIA";
                $oObj = KnowledgeArea::find($id);
                $title = $oObj->knowledge_area;
                break;
            
            case config('csys.elem_type.MODULE'):
                $lTake = $lTake->where('module_n_id', $id);
                $art = "el";
                $sDocText = "RECONOCIMIENTO";
                $sTypeText = "MÓDULO";
                $oObj = Module::find($id);
                $title = $oObj->module;
                break;
            
            case config('csys.elem_type.COURSE'):
                $lTake = $lTake->where('course_n_id', $id);
                $art = "la";
                $sDocText = "CONSTANCIA";
                $sTypeText = "CURSO";
                $oObj = Course::find($id);
                $title = $oObj->course;
                break;
            
            default:
                # code...
                break;
        }

        $oTake = $lTake->where('assignment_id', $assignment)
                        ->where('is_deleted', false)
                        ->orderBy('id_taken_control', 'DESC')
                        ->first();

        $stylesheet = file_get_contents('myapp/css/certificates/certificate.css');

        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'Letter', 'orientation' => 'L']);

        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML('<div style="height: 130px"></div>');
        $mpdf->WriteHTML('<h4 class="alg-center">Otorga '.$art.' presente:</h4>');
        // $mpdf->WriteHTML('<div style="height: 10px"></div>');
        $mpdf->WriteHTML('<h1 style="font-size: 50px" class="alg-center">'.$sDocText.'</h1>');
        $mpdf->WriteHTML('<h4 class="alg-center">a</h4>');

        // $mpdf->WriteHTML('<div align="center"><img src="img/tron2.png" alt="AETH"><div>');

        $oStudent = User::find($student);
        $mpdf->WriteHTML('<div style="height: 15px"></div>');
        $body = '<h2 class="alg-center">'.$oStudent->full_name.'</h2>';
        $mpdf->WriteHTML($body);
        
        $mpdf->WriteHTML('<br>');
        $mpdf->WriteHTML('<h4 class="alg-center">Por haber cursado el</h4>');
        $mpdf->WriteHTML('<h4 class="alg-center">'.$sTypeText.'</h4>');
        $mpdf->WriteHTML('<h3 class="alg-center">'.$title.'</h3>');

        $mpdf->WriteHTML('<div style="height: 20px"></div>');
        $oDateIni = Carbon::parse($oAssignment->dt_assignment);
        $oDateFin = Carbon::parse($oAssignment->dt_end);
        $mpdf->WriteHTML('<h5 class="alg-center">'.$oDateIni->format('d-m-Y').' al '.$oDateFin->format('d-m-Y').'</h5>');

        $date = Carbon::now();

        $key = $oStudent->full_name.'_'.$oTake->grouper.'_'.$sTypeText.'_'.$title.'_'.$oAssignment->id_assignment.'_'.$oTake->id_taken_control;

        QrCode::size(50)->generate($key, 'img/j.png');

        $qr = '<div style="position: absolute; left: 900; right: 0; top: 650; bottom: 0;">
                    <img src="img/j.png" 
                        style="width: 90%; height: 90%; margin: 0;" />
                </div>';

        $mpdf->WriteHTML($qr);

        return $mpdf->Output($date->format('Y_m_d_H_i_s')."_document_univ.pdf", "I");
    }

}
