<?php

namespace App\Http\Controllers\Adm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Adm\Company;
use App\Adm\Branch;
use App\User;

class BranchesController extends Controller
{
    
    public function __construct() {
        $this->newRoute = 'branches.create';
        $this->storeRoute = 'branches.store';
    }

    public function index()
    {
        $lBranches = \DB::table('adm_branches AS b')
                        ->join('adm_companies AS c', 'b.company_id', '=', 'c.id_company')
                        ->join('users AS u', 'b.head_user_id', '=', 'u.id')
                        ->select('b.*', 'c.company', 'u.full_name')
                        ->where('b.is_deleted', false)
                        ->where('c.is_deleted', false)
                        ->get();

        return view('adm.branches.index')->with('lBranches', $lBranches)
                                        ->with('newRoute', $this->newRoute)
                                        ->with('title', 'Sucursales de la empresa');
    }

    public function create()
    {
        $companies = Company::where('is_deleted', false)
                                ->select('id_company', 'company')
                                ->orderBy('company', 'ASC')
                                ->orderBy('id_company', 'ASC')
                                ->get();

        $users = User::where('is_deleted', false)
                        ->where('is_active', true)
                        ->select('full_name', 'id')
                        ->orderBy('full_name', 'ASC')
                        ->get();

        return view('adm.branches.create')->with('title', 'Crear sucursal')
                                            ->with('companies', $companies)
                                            ->with('users', $users)
                                            ->with('storeRoute', $this->storeRoute);
    }

    public function store(Request $request)
    {
        $oBranch = new Branch($request->all());
     
        $oBranch->is_deleted = false;
        $oBranch->external_id = 0;

        $oBranch->save();

        return redirect()->route('branches.index')->with("success", "Sucursal creada correctamente");
    }
}
