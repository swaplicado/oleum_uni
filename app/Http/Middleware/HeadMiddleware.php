<?php

namespace App\Http\Middleware;

use Closure;

class HeadMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $student = $request->route('student');

        if ($student == null) {
            return $next($request);
        }

        if (\Auth::user()->user_type_id <= 2) {
            $lStudentsByDept = \DB::table('adm_departments AS d')
                                    ->join('adm_jobs AS j', 'd.id_department', '=', 'j.department_id')
                                    ->join('users AS u', 'j.id_job', '=', 'u.job_id')
                                    ->select('u.id')
                                    ->where('d.is_deleted', false)
                                    ->where('j.is_deleted', false)
                                    ->where('u.is_deleted', false)
                                    ->where('u.id', $student)
                                    ->where('d.head_user_n_id', \Auth::id());
    
            $lStudentsByBranch = \DB::table('adm_branches AS b')
                                    ->join('users AS u', 'b.id_branch', '=', 'u.branch_id')
                                    ->select('u.id')
                                    ->where('b.is_deleted', false)
                                    ->where('u.is_deleted', false)
                                    ->where('u.id', $student)
                                    ->where('b.head_user_id', \Auth::id());
    
            $lStudentsByCompany = \DB::table('adm_companies AS c')
                                    ->join('adm_branches AS b', 'c.id_company', '=', 'b.company_id')
                                    ->join('users AS u', 'b.id_branch', '=', 'u.branch_id')
                                    ->select('u.id')
                                    ->where('b.is_deleted', false)
                                    ->where('u.is_deleted', false)
                                    ->where('u.id', $student)
                                    ->where('c.head_user_id', \Auth::id());
    
            $lStudentsByOrg = \DB::table('adm_organizations AS o')
                                    ->join('adm_companies AS c', 'o.id_organization', '=', 'c.organization_id')
                                    ->join('adm_branches AS b', 'c.id_company', '=', 'b.company_id')
                                    ->join('users AS u', 'b.id_branch', '=', 'u.branch_id')
                                    ->select('u.id')
                                    ->where('b.is_deleted', false)
                                    ->where('u.is_deleted', false)
                                    ->where('u.id', $student)
                                    ->where('o.head_user_id', \Auth::id());
    
    
            $lStudentsAux = $lStudentsByDept->union($lStudentsByBranch)
                                        ->union($lStudentsByCompany)
                                        ->union($lStudentsByOrg)
                                        ->distinct()
                                        ->pluck('u.id');

            if (count($lStudentsAux) > 0) {
                return $next($request);
            }
        }
        else {
            return $next($request);
        }

        return redirect()->route('unauthorized');
    }
}
