<?php

namespace App\Http\Middleware\Api;

use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class UserAuthenticateMiddleware
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
        try{  
            $user = JWTAuth::parseToken()->authenticate();
            if($user){
                return $next($request);
            }
            else{
                $msg = 'Invalid user token';
                return $this->build_response('error',$msg); 
            }
        }
        // catch (TokenExpiredException $e)
        // {
        //     // If the token is expired, then it will be refreshed and added to the headers
        //     try
        //     {
        //         $refreshed = JWTAuth::refresh(JWTAuth::getToken());
        //         $response->header('Authorization', 'Bearer ' . $refreshed);
        //     }
        //     catch (JWTException $e)
        //     {
        //         return ApiHelpers::ApiResponse(103, null);
        //     }
        //     $user = JWTAuth::setToken($refreshed)->toUser();
        // }
        catch (JWTException $e){

            if($e->getstatusCode() == 401){
                $msg = 'Your current session has expired, Please  login again.';
                return $this->build_response('error',$msg);     
            }else{
                $msg = $e->getMessage();
                return $this->build_response('error',$msg);     
            }
            
        }
    }

    public function build_response(
                                    $status = 'success',
                                    $message = "",
                                    $arr_data = [],
                                    $response_format = 'json'
                                )
    {
        if($response_format == 'json')
        {
            $arr_response = [
                'status' => $status,
                'msg' => $message
            ];

            if(sizeof($arr_data)>0)
            {
                $arr_response['data'] = $arr_data;
            }
            
            return response()->json($arr_response,200,[],JSON_UNESCAPED_UNICODE);
        }
    }
}
