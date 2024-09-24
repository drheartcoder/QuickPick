<?php

namespace App\Http\Controllers\Company;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\UserModel;
use App\Common\Services\ReviewService;


class DriverReviewController extends Controller
{
    public function __construct(ReviewService $review_service, UserModel $user)
    {
        $this->ReviewService 		  = $review_service;
        $this->UserModel              = $user;
        $this->arr_view_data          = [];
        $this->module_title           = "Driver Review";
        $this->module_url_slug        = "Driver Review";
		$this->module_view_folder     = "company.driver";
        $this->theme_color            = theme_color();
        $this->module_url_path        = url(config('app.project.company_panel_slug')."/driver");

        $this->review_tag_public_path       = url('/').config('app.project.img_path.review_tag');
        $this->review_tag_base_path         = base_path().config('app.project.img_path.review_tag');
    } 
    public function index($enc_id=FALSE)
    {
        $user_id                                = base64_decode($enc_id);
        $arr_review                             = $this->ReviewService->get_review($user_id);

        $obj_user    = $this->UserModel
                                    ->where('id',$user_id)
                                    ->first();
                
        $arr_user = [];
        if ($obj_user) 
        {
            $arr_user = $obj_user->toArray();
        }


        $this->arr_view_data['page_title']      = "Manage ".str_plural($this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['review_tag_public_path'] = $this->review_tag_public_path;
        $this->arr_view_data['arr_data']        = $arr_review;
        $this->arr_view_data['arr_user']        = $arr_user;
        $this->arr_view_data['user_id']         = $user_id;
        return view($this->module_view_folder.'.review', $this->arr_view_data);    
        
    }
    public function view($enc_id=FALSE)
    {
       
        $review_id                              = base64_decode($enc_id);
        $arr_review                             = $this->ReviewService->get_review_details($review_id);
        
        $this->arr_view_data['page_title']      = "View ".str_plural($this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path.'/review';
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['review_tag_public_path'] = $this->review_tag_public_path;
        $this->arr_view_data['arr_review']      = $arr_review;
        return view($this->module_view_folder.'.view_review', $this->arr_view_data);
        
    }
}
