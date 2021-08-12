<?php

namespace App\Http\Controllers\Adm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Adm\Company;
use App\User;

class CompaniesController extends Controller
{
    public function __construct() {
        $this->newRoute = 'companies.create';
        $this->storeRoute = 'companies.store';
    }

    public function index()
    {
        $lCompanies = \DB::table('adm_companies AS c')
                        ->join('users AS u', 'c.head_user_id', '=', 'u.id')
                        ->select('c.*', 'u.full_name')
                        ->where('c.is_deleted', false)
                        ->get();

        return view('adm.companies.index')->with('lCompanies', $lCompanies)
                                        ->with('newRoute', $this->newRoute)
                                        ->with('title', 'Empresas');
    }

    public function create()
    {
        $users = User::where('is_deleted', false)
                        ->where('is_active', true)
                        ->select('full_name', 'id')
                        ->orderBy('full_name', 'ASC')
                        ->get();

        return view('adm.companies.create')->with('title', 'Crear sucursal')
                                            ->with('users', $users)
                                            ->with('storeRoute', $this->storeRoute);
    }

    public function store(Request $request)
    {
        $oCompany = new Company($request->all());
     
        $oCompany->is_deleted = false;
        $oCompany->external_id = 0;
        $oCompany->organization_id = 1;

        $oCompany->save();

        return redirect()->route('companies.index')->with("success", "Empresa creada correctamente");
    }

}
