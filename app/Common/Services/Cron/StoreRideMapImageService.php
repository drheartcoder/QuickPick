<?php
namespace App\Common\Services\Cron;

use App\Models\BookingMasterModel;
use App\Models\BookingMasterCoordinateModel;

class StoreRideMapImageService
{

    public function __construct(
                                BookingMasterModel $booking,
                                BookingMasterCoordinateModel $booking_cordinates
                                )
    {
       
        $this->BookingMasterModel            = $booking;
        $this->BookingMasterCoordinateModel  = $booking_cordinates;
    }
    /************ Booking coordinates details ***************/
    public function store_ride_map_image()
    { 
       
        $arr_booking_details = [];

        $obj_booking_details = $this->BookingMasterModel
                                    ->whereHas('booking_master_coordinate_details',function($q){
                                            $q->where('map_image','');
                                            $q->where('coordinates','!=','');
                                    })
                                    ->whereHas('load_post_request_details',function($q){
                                    })
                                    ->with(['load_post_request_details','booking_master_coordinate_details'])
                                    ->where('booking_status','COMPLETED')
                                    ->orderBy('id','DESC')            
                                    ->get();
                
        if($obj_booking_details)
        {
            $arr_booking_details = $obj_booking_details->toArray();
        }
  
        if(isset($arr_booking_details) && sizeof($arr_booking_details)>0)
        {
            foreach($arr_booking_details as $key => $booking_details)
            {
                if(isset($booking_details['load_post_request_details']) && sizeof($booking_details['load_post_request_details'])>0)
                {    
                    $booking_coordinates_id = isset($booking_details['booking_master_coordinate_details']['id']) ? $booking_details['booking_master_coordinate_details']['id'] : '';
                    
                    $source_lat = $source_lng = $destination_lat = $destination_lng = '';

                    $source_lat      = isset($booking_details['load_post_request_details']['pickup_lat']) ? $booking_details['load_post_request_details']['pickup_lat'] : '';
                    $source_lng      = isset($booking_details['load_post_request_details']['pickup_lng']) ? $booking_details['load_post_request_details']['pickup_lng'] : '';
                        
                    $destination_lat = isset($booking_details['load_post_request_details']['drop_lat']) ? $booking_details['load_post_request_details']['drop_lat'] : '';
                    $destination_lng = isset($booking_details['load_post_request_details']['drop_lng']) ? $booking_details['load_post_request_details']['drop_lng'] : '';

                        $booking_coordinates = isset($booking_details['booking_master_coordinate_details']['coordinates']) ? $booking_details['booking_master_coordinate_details']['coordinates'] : '';
              
                        if($booking_coordinates!='')
                        {
                            $booking_coordinates =  json_decode($booking_coordinates,true);

                            if(isset($booking_coordinates) && sizeof($booking_coordinates)>0)
                            {
                                foreach ($booking_coordinates as $key => $coordinates) 
                                {
                                    $lat = isset($coordinates['lat']) ? $coordinates['lat'] :0;
                                    $lng = isset($coordinates['lng']) ? $coordinates['lng'] :0;
     
                                    if($key == 0){
                                        $source_lat = isset($coordinates['lat']) ? $coordinates['lat'] :0;
                                        $source_lng = isset($coordinates['lng']) ? $coordinates['lng'] :0;
                                        break;
                                    }   
                                }

                            }
                    }
                        
                    $origin      = $source_lat.','.$source_lng;
                    $destination = $destination_lat.','.$destination_lng;

                    $map_image = $this->getStaticGmapURLForDirection($origin, $destination);
                   
                    if($map_image!=''){

                        $this->BookingMasterCoordinateModel->where('id',$booking_coordinates_id)->update(['map_image'=>$map_image]);
                    }
                }
            }          
        }   
        echo "all map images are updated succcessfully";

        return true; 

    }
                                                            
    function getStaticGmapURLForDirection($origin, $destination, $size = "750x450") {
                                
        $icon_path = url('/assets/node_assets/images/pointer.png');
        $markers   = array();
        $markers[] = "markers=icon:" .$icon_path. urlencode("|") . $origin;
        $markers[] = "markers=icon:" .$icon_path. urlencode("|") . $destination;

        $url = "https://maps.googleapis.com/maps/api/directions/json?origin=$origin&destination=$destination";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, false);
        $result = curl_exec($ch);
        curl_close($ch);
        $googleDirection = json_decode($result, true);
        $polyline ='';
        if(isset($googleDirection['routes'][0]['overview_polyline']['points'])){
            $polyline = urlencode($googleDirection['routes'][0]['overview_polyline']['points']);
        }
        $markers = implode($markers, '&');

        return "https://maps.googleapis.com/maps/api/staticmap?size=$size&maptype=roadmap&path=enc:$polyline&$markers";
    }
}