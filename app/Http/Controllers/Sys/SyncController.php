<?php

namespace App\Http\Controllers\Sys;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use App\Http\Controllers\Adm\DepartmentsController;
use App\Http\Controllers\Adm\JobsController;
use App\Http\Controllers\Mgr\ScheduledAssignmentsController;
use App\Http\Controllers\UsersController;

class SyncController extends Controller
{
    public static function toSynchronize($withRedirect = true)
    {
        // \App\Utils\Configuration::setConfiguration('lastSyncDateTime', '2020-04-01 00:00:00');
        $config = \App\Utils\Configuration::getConfigurations();

        $synchronized = SyncController::synchronizeWithERP($config->lastSyncDateTime);

        $newDate = Carbon::now();
        $newDate->subMinutes(10);

        \App\Utils\Configuration::setConfiguration('lastSyncDateTime', $newDate->toDateTimeString());

        if ($withRedirect) {
            if ($synchronized) {
                return redirect()->back()->with('mensaje', 'Sincronizado con sistema externo');
            }
            else {
                return redirect()->back()->with('mensaje', 'No se pudo sincronizar sistema externo');
            }
        }

        return $synchronized;
    }

    public static function synchronizeWithERP($lastSyncDate = "")
    {
        // $jsonString = "";
        // $jsonString = file_get_contents(base_path('response_from_siie.json'));
        $client = new Client([
            'base_uri' => '192.168.1.233:9001',
            'timeout' => 10.0,
        ]);

        try {
            
            $response = $client->request('GET', 'getInfoERP/' . $lastSyncDate);
            $jsonString = $response->getBody()->getContents();
            $data = json_decode($jsonString);

            $deptCont = new DepartmentsController();
            $deptCont->saveDeptsFromJSON($data->departments);
            
            $jobCont = new JobsController();
            $jobCont->saveJobsFromJSON($data->positions);
            
            $usrCont = new UsersController();
            $usrCont->saveUsersFromJSON($data->employees);
            
            $deptCont->setSupDeptAndHeadUser($data->departments);

            $sch = new ScheduledAssignmentsController();
            $sch->processAssignmentSchedule(false);
        }
        catch (\Throwable $th) {
            //throw $th;
            return false;
        }
        
        return true;
    }
}
