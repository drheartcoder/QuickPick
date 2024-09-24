<?php 
namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\StateModel; 
use App\Models\CityModel; 

 
class LocationController extends Controller
{

  /*
    | Constructor : creates instances of model class 
    |               & handles the admin authantication
    | auther : MOHAN SONAR 
    | Date : 04-05-2016
    | @return \Illuminate\Http\Response
    */
    public function __construct()
    {
        
    }


      /*
    | get_states : function to generate States belongs 
    |              to specific country
    | auther : MOHAN SONAR 
    | Date : 02/02/2016
    | @param :  int $country_id
    | @return \Illuminate\Http\Response
    */

    public function get_states(Request $request)
    {
        $country_id = base64_decode($request->input('country_id'));
        $arr_state = array();
        $arr_response = array();

        $obj_states = StateModel::select('id','state_name')
                                       ->where('country_id',$country_id)
                                       ->get();

        if($obj_states != FALSE)
        {
            $arr_state =  $obj_states->toArray();
        }
        if(sizeof($arr_state)>0)
        {
            $arr_response['status']    = "success";
            $arr_response['arr_state'] = $arr_state;
        }
        else
        {
            $arr_response['status']    = "error";
            $arr_response['arr_state'] = array();
        }
        return response()->json($arr_response);
    }

    public function get_cities(Request $request)
    {
        $state_id = base64_decode($request->input('state_id'));
        $arr_cities  = $arr_response = array();
        
        $obj_cities = CityModel::select('id','city_name')
                                       ->where('state_id',$state_id)
                                       ->get();

        if($obj_cities != FALSE)
        {
            $arr_cities =  $obj_cities->toArray();
        }
        if(sizeof($arr_cities)>0)
        {
            $arr_response['status']    = "success";
            $arr_response['arr_cities'] = $arr_cities;
        }
        else
        {
            $arr_response['status']    = "error";
            $arr_response['arr_cities'] = array();
        }
        return response()->json($arr_response);
    }
}