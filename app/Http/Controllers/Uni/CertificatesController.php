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

        return $mpdf->Output($date->format('Y_m_d_H_i_s')."_".strtolower($sDocText)."_uvaeth.pdf", "I");
    }

}
