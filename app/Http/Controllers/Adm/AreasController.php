<?php

namespace App\Http\Controllers\Adm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Adm\Areas;
use App\Adm\AreasUsers;

class AreasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $areas = \DB::table('adm_areas as a')
                    ->leftJoin('adm_areas_users as au', function($join)
                    {
                        $join->on('au.area_id', '=', 'a.id_area')
                            ->where('au.is_deleted', 0);
                    })
                    ->leftJoin('users as u', 'u.id', '=', 'au.head_user_id')
                    ->where('a.is_deleted', 0)
                    ->select('a.*', 'au.head_user_id', 'au.area_id as user_area', 'u.full_name as user')
                    ->get();

        foreach($areas as $area){
            $oArea = $areas->where('id_area', $area->father_area_id)->first();
            if(!is_null($oArea)){
                $area->father = $oArea->area;
            }else{
                $area->father = 'No aplica';
            }
        }

        return view('adm.areas.index')->with('areas', $areas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $areas = Areas::where('is_deleted', 0)->get();
        $users = \DB::table('users')
                    ->where('is_deleted', 0)
                    ->where('is_active', 1)
                    ->get();

        return view('adm.areas.create')->with('areas', $areas)
                                        ->with('users', $users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            \DB::beginTransaction();
            $area = new Areas();
            $area->area = $request->area;
            $area->father_area_id = $request->father_area;
            $area->created_by_id = \Auth::id();
            $area->updated_by_id = \Auth::id();
            $area->save();

            $areaUser = new AreasUsers();
            $areaUser->area_id = $area->id_area;
            $areaUser->head_user_id = $request->supervisor;
            $areaUser->is_deleted = 0;
            $areaUser->created_by_id = \Auth::id();
            $areaUser->updated_by_id = \Auth::id();
            $areaUser->save();
            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            return back()->with(['message' => 'Error al guardar el registro', 'icon' => 'error']);
        }

        return redirect(route('areasAdm.index'))->with(['message' => 'Registro guardado con éxito', 'icon' => 'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $area = \DB::table('adm_areas as a')
                    ->leftJoin('adm_areas_users as au', function($join)
                    {
                        $join->on('au.area_id', '=', 'a.id_area')
                            ->where('au.is_deleted', 0);
                    })
                    ->leftJoin('users as u', 'u.id', '=', 'au.head_user_id')
                    ->where('a.is_deleted', 0)
                    ->where('a.id_area', $id)
                    ->select('a.*', 'au.head_user_id', 'au.area_id as user_area', 'u.full_name as user')
                    ->first();

        $areas = Areas::where('is_deleted', 0)->get();

        $users = \DB::table('users')
                    ->where('is_deleted', 0)
                    ->where('is_active', 1)
                    ->get();

        return view('adm.areas.edit')->with('oArea', $area)
                                    ->with('areas', $areas)
                                    ->with('users', $users);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            \DB::beginTransaction();
            $area = Areas::find($id);
            $area->area = $request->area;
            $area->father_area_id = $request->father_area;
            $area->updated_by_id = \Auth::id();
            $area->update();

            $areaUser = AreasUsers::where('area_id', $area->id_area)
                                    ->where('is_deleted', 0)
                                    ->first();

            if(!is_null($areaUser)){
                $areaUser->head_user_id = $request->supervisor;
                $areaUser->updated_by_id = \Auth::id();
                $areaUser->update();
            }else{
                $areaUser = new AreasUsers();
                $areaUser->area_id = $area->id_area;
                $areaUser->head_user_id = $request->supervisor;
                $areaUser->is_deleted = 0;
                $areaUser->created_by_id = \Auth::id();
                $areaUser->updated_by_id = \Auth::id();
                $areaUser->save();
            }

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            return back()->with(['message' => 'Error al actualizar el registro', 'icon' => 'error']);
        }

        return redirect(route('areasAdm.index'))->with(['message' => 'Registro actualizadó con éxito', 'icon' => 'success']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $area = Areas::find($id);
            $area->is_deleted = 1;
            $area->updated_by_id = \Auth::id();
            $area->update();

            $areaUser = AreasUsers::where('area_id', $area->id_area)
                                    ->where('is_deleted', 0)
                                    ->first();

            if(!is_null($areaUser)){
                $areaUser->is_deleted = 1;
                $areaUser->updated_by_id = \Auth::id();
                $areaUser->update();
            }

            \DB::commit();
        } catch (\Throwable $th) {
            \DB::rollBack();
            return json_encode(['success' => false, 'message' => 'Error al eliminar el registro', 'icon' => 'error']);
        }

        return json_encode(['success' => true, 'message' => 'Se eliminó el registro con éxito', 'icon' => 'success']);
    }
}
