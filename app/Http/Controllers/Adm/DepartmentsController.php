<?php

namespace App\Http\Controllers\Adm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Adm\Department;
use App\User;

class DepartmentsController extends Controller
{
    public function saveDeptsFromJSON($lSiieDepts)
    {
        $lUnivDepts = Department::pluck('id_department', 'external_id');

        foreach ($lSiieDepts as $jSiieDept) {
            try {
                if (isset($lUnivDepts[$jSiieDept->id_department])) {
                    $idDeptUniv = $lUnivDepts[$jSiieDept->id_department];
                    $this->updDepartment($jSiieDept, $idDeptUniv);
                }
                else {
                    $this->insertDepartment($jSiieDept);
                }
            }
            catch (\Throwable $th) {
            }
        }
    }
    
    private function updDepartment($jSiieDept, $idDeptUniv)
    {
        Department::where('id_department', $idDeptUniv)
                    ->update(
                            [
                                'department' => $jSiieDept->dept_name,
                                'acronym' => $jSiieDept->dept_code,
                                'is_deleted' => $jSiieDept->is_deleted
                            ]
                        );
    }
    
    private function insertDepartment($jSiieDept)
    {
        $oDept = new Department();

        $oDept->department = $jSiieDept->dept_name;
        $oDept->acronym = $jSiieDept->dept_code;
        $oDept->is_deleted = $jSiieDept->is_deleted;
        $oDept->external_id = $jSiieDept->id_department;
        // $oDept->head_user_n_id = $jSiieDept->head_employee_id;
        // $oDept->department_n_id = $jSiieDept->superior_department_id;

        $oDept->save();
    }

    public function setSupDeptAndHeadUser($lSiieDepts)
    {
        $lUnivDepts = Department::pluck('id_department', 'external_id');
        $lUsers = User::pluck('id', 'external_id');

        foreach ($lSiieDepts as $siieDepto) {
            $upds = [];
            if ($siieDepto->superior_department_id > 0) {
                $idSupDepto = $lUnivDepts[$siieDepto->superior_department_id];
                $upds['department_n_id'] = $idSupDepto;
            }
            
            if ($siieDepto->head_employee_id > 0) {
                $idHeadUser = $lUsers[$siieDepto->head_employee_id];
                $upds['head_user_n_id'] = $idHeadUser;
            }

            if (count($upds) == 0) {
                continue;
            }

            $idDepto = $lUnivDepts[$siieDepto->id_department];
    
            Department::where('id_department', $idDepto)
                        ->update($upds);
        }

    }
}
