<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Http\Controllers\UsersController;

class apiGlobalUsersController extends Controller
{
    public const USERGLOBAL_INSERT = 1;
    public const USERGLOBAL_UPDATE = 2;
    /**
     * The function `getUser` retrieves user data based on various parameters such as full name,
     * username, external ID, and employee number.
     * 
     * @param $full_name The full name of the user you want to retrieve.
     * @param $username The username parameter is used to filter the users based on their username. If a
     * username is provided, the query will only return users with that specific username.
     * @param $external_id The external_id parameter is used to filter the users based on their external
     * ID.
     * @param $employee_num The parameter "employee_num" is used to filter the users based on their
     * employee number. If a value is provided for this parameter, the query will only return users
     * whose "num_employee" column matches the provided value.
     * 
     * @return $query a collection of user objects that match the specified criteria.
     */
    public static function getUser($full_name, $username, $external_id, $employee_num){
        $query = User::where('is_active', 1)->where('is_deleted', 0);

        if(!is_null($username)){
            $query = $query->where('username', $username);
        }

        if(!is_null($full_name)){
            $query = $query->where('full_name', $full_name);
        }
        if(!is_null($external_id)){
            $query = $query->where('external_id', $external_id);
        }
        if(!is_null($employee_num)){
            $query = $query->where('num_employee', $employee_num);
        }
        
        $query = $query->select(
            'id',
            'username',
            'full_name',
            'external_id',
            'num_employee'
            )->get();

        return $query;
    }

    /**
     * The function `getUserToGlobalUser` takes in a request object and retrieves a user from the
     * database based on the provided parameters, returning a JSON response with the user data if
     * found, or an error message if not found or multiple users are found.
     * 
     * @param Request request The  parameter contains the user data, 
     *   Each user data has the following properties:
     * - full_name: The full name of the user.
     * - username: The username of the user.
     * - external_id: The external ID of the user.
     * - employee_num: The employee number of the user.
     * 
     * @return \Illuminate\Http\JsonResponse A JSON response with the user data if found, or an error
     * message if not found or multiple users are found.     
     */
    public function getUserToGlobalUser(Request $request){
        try {
            $full_name = $request->full_name;
            $username = $request->username;
            $external_id = $request->external_id;
            $employee_num = $request->employee_num;
            $user = null;

            $query = self::getUser($full_name, $username, $external_id, $employee_num);
            
        } catch (\Throwable $th) {
            \Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => null
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }

        if($query->count() == 1){
            $user = $query->first();
            return response()->json([
                'status' => 'success',
                'message' => "Se encontró el usuario correctamente",
                'data' => $user
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }else if($query->count() == 0){
            return response()->json([
                'status' => 'success',
                'message' => "No se encontró el usuario: " . $username . " " . $full_name . " " . $external_id . " " . $employee_num . " " . " , por favor verifique los datos ingresados. ",
                'data' => null
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }else if($query->count() > 0){
            return response()->json([
                'status' => 'error',
                'message' => 'Multiple users found for ' . $username . ' ' . $full_name . ' ' . $external_id . ' ' . $employee_num ,
                'data' => null
            ], 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * The function getListUsersToGlobalUsers takes a list of users as input, queries the database to
     * find matching users, and returns a response with the status, message, and user data for each
     * user.
     * 
     * @param Request request The  parameter contains the lUsers array, which is an array of user
     * objects. Each user object has the following properties:
     * - full_name: The full name of the user.
     * - username: The username of the user.
     * - external_id: The external ID of the user.
     * - employee_num: The employee number of the user.
     *
     * @return a JSON response with the following structure:
     * - 'status': Indicates the status of the response ('success' or 'error').
     * - 'message': Provides a message related to the status of the response.
     * - 'data': Contains an array of user objects. Each user object has the following properties:
     *   - 'status': Indicates the status of the user search ('success' or '
     */
    public function getListUsersToGlobalUsers(Request $request){
        try {
            $lUsers =  json_decode($request->lUsers);
            $lUsersResponse = [];
            foreach ($lUsers as $user) {
                $query = self::getUser($user->full_name, $user->username, $user->external_id, $user->employee_num);

                if($query->count() == 1){
                    $user = $query->first();
                    $lUsersResponse[] = [
                        'status' => 'success',
                        'message' => "Se encontró el usuario correctamente",
                        'user' => $user
                    ];
                }else if($query->count() == 0){
                    $lUsersResponse[] = [
                        'status' => 'success',
                        'message' => "No se encontró el usuario: " . $user->username . " " . $user->full_name . " " . $user->external_id . " " . $user->employee_num . " " . " , por favor verifique los datos ingresados. ",
                        'user' => null
                    ];
                }else if($query->count() > 0){
                    $lUsersResponse[] = [
                        'status' => 'error',
                        'message' => 'Multiple users found for ' . $user->username . ' ' . $user->full_name . ' ' . $user->external_id . ' ' . $user->employee_num ,
                        'user' => null
                    ];
                }
            }
        } catch (\Throwable $th) {
            \Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => null
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Se encontrarón los usuarios correctamente",
            'data' => $lUsersResponse
            ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * The function syncUsers receives a request containing a list of users, saves them to the
     * database, and returns a response indicating whether the synchronization was successful or not.
     * 
     * @param Request request The  parameter is an instance of the Request class, which
     * represents an HTTP request. It contains information about the request such as the request
     * method, headers, and input data.
     * Request contains an object users to sinc, Each user object has the following properties:
     * id_employee
     * num_employee
     * lastname1
     * lastname2
     * lastname
     * firstname
     * admission_date
     * leave_date
     * dt_tp_pay
     * dt_bir
     * benefit_date
     * email
     * emailEmp
     * telArea
     * telNum
     * ext
     * company_id
     * overtime_policy
     * checker_policy
     * way_pay
     * dept_rh_id
     * siie_job_id
     * is_active
     * is_deleted
     * 
     * @return $ a JSON response. If the synchronization of users is successful, it will return a success
     * response with a message indicating that the users were synchronized correctly. If there is an
     * error during the synchronization process, it will return an error response with a message
     * indicating the error message.
     */
    public function syncUser(Request $request){
        try {
            $user = (object)$request->user;
            $type = $request->type;
            
            $usrCont = new UsersController();
            if($type == self::USERGLOBAL_INSERT){
                $oUser = $usrCont->insertUserFromApi($user);
            }else if($type == self::USERGLOBAL_UPDATE){
                $oUser = $usrCont->updateUserFromApi($user);
            }

        } catch (\Throwable $th) {
            \Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => null
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Se sincronizaron los usuarios correctamente",
            'data' => $oUser
            ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public static function getUserById($userId){
        try {
            $oUser = User::findOrFail($userId);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => null
                ], 500, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Se encontró el usuario correctamente",
            'data' => $oUser
            ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function updateUser(Request $request){
        try {
            $user = (object)$request->user;
            $userUniv = User::findOrFail($user->user_system_id);
            $userUniv->username = $user->username;
            $userUniv->email = $user->email;
            $userUniv->password = $user->pass;
            $userUniv->update();
        } catch (\Throwable $th) {
            \Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => null
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }

        return response()->json([
            'status' => 'success',
            'message' => "Se actualizó el usuario correctamente",
            'data' => null
            ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    // public function syncListUsers(Request $request){
    //     try {
    //         $lUsers = $request->lUsers;
    //         foreach($lUsers as $user){

    //         }
    //     } catch (\Throwable $th) {
    //         \Log::error($th);
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $th->getMessage(),
    //             'data' => null
    //             ], 500, [], JSON_UNESCAPED_UNICODE);
    //     }
    // }
}
