<?php

namespace App\Common\Services;

use App\Models\AssignedAreaModel;
use App\Models\RestrictedAreaModel;


class ValidateRestrictedAreaService
{
    public function __construct(AssignedAreaModel $assigned_area,RestrictedAreaModel $restricted_area)
    {
        $this->AssignedAreaModel   = $assigned_area;
        $this->RestrictedAreaModel = $restricted_area;

    }
    


    public function check_validate_restricted_area($arr_validate_restricted_area_data)
    {
        $pickup_lat    = isset($arr_validate_restricted_area_data['pickup_lat']) ? $arr_validate_restricted_area_data['pickup_lat'] :'';
        $pickup_lng    = isset($arr_validate_restricted_area_data['pickup_lng']) ? $arr_validate_restricted_area_data['pickup_lng'] :'';

        $drop_lat    = isset($arr_validate_restricted_area_data['drop_lat']) ? $arr_validate_restricted_area_data['drop_lat'] :'';
        $drop_lng    = isset($arr_validate_restricted_area_data['drop_lng']) ? $arr_validate_restricted_area_data['drop_lng'] :'';

        $restricted_area_cnt = 0;
        
        if(($pickup_lat!=''&& $pickup_lng!='') && ($drop_lat!=''&& $drop_lng!='')) 
        {
            $arr_pick_up_lat_lng = "$pickup_lat $pickup_lat";
            $arr_drop_lat_lng    = "$drop_lat $drop_lng";

            $arr_all_points = [];

            array_push($arr_all_points, $arr_pick_up_lat_lng);
            array_push($arr_all_points, $arr_drop_lat_lng);

            $arr_restricted_area = $this->get_all_restricted_area();
            
            if(isset($arr_restricted_area) && sizeof($arr_restricted_area)>0)
            {
                foreach ($arr_restricted_area as $zone)
                { 
                    $id                     = isset($zone['id']) ? $zone['id'] :'';
                    $co_ordinates           = isset($zone['co-ordinates']) ? trim($zone['co-ordinates']) :'';
                    
                    $polygon                = array();

                    if($co_ordinates)
                    {
                        $polygon_ary = $this->convert_goolge_co_ordinates_to_array($co_ordinates);

                        if(isset($polygon_ary) && sizeof($polygon_ary )>0)
                        {
                            foreach ($polygon_ary as $key => $value) 
                            {
                                if(trim($value) !=="" && trim($value) !==" ")
                                {
                                  $polygon[$key] = $value;
                                }
                            }                    
                        }

                        if(isset($polygon) && sizeof($polygon) > 0)
                        {  
                            // The last point's coordinates must be the same as the first one's, to "close the loop"
                            if(isset($arr_all_points) && sizeof($arr_all_points)>0)
                            {
                                foreach($arr_all_points as $key => $point)
                                {
                                    $position = $this->pointInPolygon($point, $polygon);

                                    if($position == 'inside' || $position == 'boundary')
                                    {
                                        $restricted_area_cnt ++;
                                    }
                                } 
                            }
                        }
                           
                    }
                } 
            }
        }

        $arr_response = [];
        if($restricted_area_cnt>0)
        { 
            $arr_response['status'] = 'error';
            $arr_response['msg'] = 'Sorry, area is out of service zone.';               
        }
        else
        {
            $arr_response['status'] = 'success';
            $arr_response['msg']    = 'Area is in of service zone.';
        }
        return $arr_response;
    }

    private function get_all_restricted_area()
    {
        $arr_restricted_area = [];

        $obj_restricted_area = $this->AssignedAreaModel
                                            ->select('id','co-ordinates')
                                            ->where('is_active','1')
                                            ->get();
        
        if($obj_restricted_area)
        {
            $arr_restricted_area = $obj_restricted_area->toArray();
        }

        return $arr_restricted_area;
    }
    private function convert_goolge_co_ordinates_to_array($co_ordinates)
    {
        $str = str_replace('new google.maps.LatLng (', '"', $co_ordinates); 
        $str = str_replace('),', '"', $str);
        $str = str_replace(')', '"', $str);
        $str = str_replace('"', '_', $str);
        $str = str_replace(',', ' ', $str);

        return $polygon_ary = explode("_", $str);
    }
    private function pointInPolygon($point, $polygon, $pointOnVertex = true) 
    {
        $this->pointOnVertex = $pointOnVertex;
 
        // Transform string coordinates into arrays with x and y values
        $point = $this->pointStringToCoordinates($point);
        $vertices = array(); 
        foreach ($polygon as $vertex) {
          //  echo "<br/>vartex=".$vertex;
            $vertices[] = $this->pointStringToCoordinates($vertex); 
        }
 
        // Check if the point sits exactly on a vertex
        if ($this->pointOnVertex == true and $this->pointOnVertex($point, $vertices) == true) {
           // return "<br/>vartex=";
        }
 
        // Check if the point is inside the polygon or on the boundary
        $intersections = 0; 
        $vertices_count = count($vertices);
 
        for ($i=1; $i < $vertices_count; $i++) 
        {
            $vertex1 = $vertices[$i-1]; 
            $vertex2 = $vertices[$i];
            if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])) { // Check if point is on an horizontal polygon boundary
                return "boundary";
            }
            if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) { 
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x']; 
                if ($xinters == $point['x']) { // Check if point is on the polygon boundary (other than horizontal)
                    return "boundary";
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                    $intersections++; 
                }
            } 
        } 
        // If the number of edges we passed through is odd, then it's in the polygon. 
        if ($intersections % 2 != 0) {
            return "inside";
        } else {
            return "outside";
        }
    }
    private function pointStringToCoordinates($pointString) 
    { 
        $coordinates = explode(" ", $pointString); 
        return array("x" => $coordinates[0], "y" => $coordinates[1]);
    }

    private function pointOnVertex($point, $vertices) 
    {
        foreach($vertices as $vertex) {
            if ($point == $vertex) {
                return true;
            }
        }
    }
}
?>