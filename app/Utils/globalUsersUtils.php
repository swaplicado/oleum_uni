<?php namespace App\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Request;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use Exception;
use App\User;

class globalUsersUtils {

    public static function loginToPGH(){
        $config = \App\Utils\Configuration::getConfigurations();
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];

        $url = $config->pghApiRoute;

        $client = new Client([
            'base_uri' => $url,
            'timeout' => 30.0,
            'headers' => $headers,
            'verify' => false
        ]);

        $pghUser = $config->pghUser;
        $body = '{
                "username": "'. $pghUser->username .'",
                "password": "'. $pghUser->password .'"
        }';

        $request = new \GuzzleHttp\Psr7\Request('POST', 'login', $headers, $body);
        $response = $client->sendAsync($request)->wait();
        $jsonString = $response->getBody()->getContents();
        $data = json_decode($jsonString);

        return $data;
    }

    public static function globalUpdateUser($token_type, $access_token, $oUser){
        try {
            $config = \App\Utils\Configuration::getConfigurations();
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => $token_type.' '.$access_token
            ];
    
            $url = $config->pghApiRoute;
            $client = new Client([
                'base_uri' => $url,
                'timeout' => 30.0,
                'headers' => $headers
            ]);
    
            $body = json_encode(['user' => $oUser, 'fromSystem' => $config->univIdSystem]);
    
            $request = new \GuzzleHttp\Psr7\Request('POST', 'syncUser', $headers, $body);
            $response = $client->sendAsync($request)->wait();
            $jsonString = $response->getBody()->getContents();
            $data = json_decode($jsonString);
    
            return $data;
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_encode(['status' => 'error', 'message' => $th->getMessage(), 'data' => null]);
        }
    }

    public static function globalUpdatePassword($token_type, $access_token, $oUser){
        try {
            $config = \App\Utils\Configuration::getConfigurations();
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => $token_type.' '.$access_token
            ];
    
            $url = $config->pghApiRoute;
            $client = new Client([
                'base_uri' => $url,
                'timeout' => 30.0,
                'headers' => $headers
            ]);
    
            $body = json_encode(['user' => $oUser, 'fromSystem' => $config->univIdSystem]);
    
            $request = new \GuzzleHttp\Psr7\Request('POST', 'updateGlobal', $headers, $body);
            $response = $client->sendAsync($request)->wait();
            $jsonString = $response->getBody()->getContents();
            $data = json_decode($jsonString);
    
            return $data;
        } catch (\Throwable $th) {
            \Log::error($th);
            return json_decode(json_encode(['status' => 'error', 'message' => $th->getMessage(), 'data' => null]));
        }
    }
}