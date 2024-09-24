<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;

use App\Http\Controllers\Controller;

use App\Models\EmailTemplateModel;
use App\Models\EmailTemplateTranslationModel;
use App\Models\SiteSettingModel;

use App\Common\Services\LanguageService;

use Validator;
use Flash;

class EmailTemplateController extends Controller
{   
	public function __construct(
                                    EmailTemplateModel $email_template, 
                                    LanguageService $langauge,
                                    EmailTemplateTranslationModel $email_template_translation,
                                    SiteSettingModel $site_setting
        )
    {
        $this->EmailTemplateModel            = $email_template;
        $this->BaseModel                     = $this->EmailTemplateModel;
        $this->EmailTemplateTranslationModel = $email_template_translation;
        $this->SiteSettingModel              = $site_setting;
        $this->LanguageService               = $langauge;
        $this->arr_view_data                 = [];
        $this->module_title                  = "Email Template";
        $this->module_icon                  = "fa-envelope";
        $this->module_view_folder            = "admin.email_template";
        $this->module_url_path               = url(config('app.project.admin_panel_slug')."/email_template");
        $this->theme_color                   = theme_color();
    }
    
 
    public function index()
    {   
        $obj_data = $this->BaseModel->get();

        if($obj_data != FALSE)
        {
            $arr_data = $obj_data->toArray();
        }

        $this->arr_view_data['arr_data']        = $arr_data;
        $this->arr_view_data['page_title']      = "Manage ".str_singular($this->module_title);
        $this->arr_view_data['module_title']    = str_plural($this->module_title);
        $this->arr_view_data['module_url_path'] = $this->module_url_path;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;

        
        return view($this->module_view_folder.'.index',$this->arr_view_data);
    }

    
    public function view($enc_id,$act_lng)
    {

        $id   = base64_decode($enc_id);
        $html = ''; 
        $subject = ''; 
        $obj_email_template = $this->BaseModel->where('id',$id)
                                              ->with(['translations'=>function($query) use($act_lng)
                                                        {
                                                            return $query->where('locale',$act_lng);
                                                        }
                                                     ])  
                                              ->first();

        if($obj_email_template)
        {
            $arr_email_template = $obj_email_template->toArray();

            if(isset($arr_email_template) && sizeof($arr_email_template)>0)
            {
                if(isset($arr_email_template['translations']) && sizeof($arr_email_template['translations'])>0)
                {
                    $html = $arr_email_template['translations'][0]['template_html'];
                    $subject = $arr_email_template['translations'][0]['template_subject'];
                }
            }    

            $content  = isset($html)&&sizeof($html)>0?$html:'';
            $subject  = isset($subject)&&sizeof($subject)>0?$subject:'';

            $site_url = '<a href="'.url('/').'">'.config('app.project.name').'</a>.<br/>' ;

            $content  = str_replace("##SITE_URL##",$site_url,$content);  

            return view('email.front_general',compact('content','subject'))->render();
        }
        else
        {
            return redirect()->back();
        }
    }


    public function create()
    {
        $this->arr_view_data['page_title']      = "Create ".str_singular($this->module_title);

        $this->arr_view_data['arr_lang']          = $this->LanguageService->get_all_language();
        $this->arr_view_data['module_title']      = str_plural($this->module_title);
        $this->arr_view_data['module_url_path']   = $this->module_url_path;
        $this->arr_view_data['theme_color']       = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;

        return view($this->module_view_folder.'.create',$this->arr_view_data);
    }


    public function store(Request $request)
    {
        $arr_rules['template_name'] 	    =	"required";  
        $arr_rules['template_subject_en'] 	=	"required";  
        $arr_rules['template_html_en'] 	    =	"required";        
        $arr_rules['variables'] 	   	    =	"required";

        $validator = Validator::make($request->all(),$arr_rules);

        if($validator->fails())
        {
             Flash::error('Please Fill All The Mandatory Fields');
             return redirect()->back()->withErrors($validator)->withInput();
        }
        foreach ($request->input('variables') as  $key => $value) 
        {
        	$arr_varaible[$key] = "##".$value."##";
        }

        $arr_site_settings = [];
        $site_setting = $this->SiteSettingModel->first();
        if($site_setting) 
        {
            $arr_site_settings = $site_setting->toArray();
        }

        $this->site_email_address = isset($arr_site_settings['site_email_address']) && $arr_site_settings['site_email_address'] != "" ? $arr_site_settings['site_email_address'] : 'info@printingstore.com';

        $arr_data = array(
                                'template_variables' 	=>	 implode("~", $arr_varaible),
        						'template_from_mail' 	=>	 $this->site_email_address,
        						'template_from'			=>	 'SUPER-ADMIN',
                                'template_name'         =>   $request->input('template_name')
        				 );

        $entity = $this->BaseModel->create($arr_data);

        if($entity)
        {
            $arr_lang =  $this->LanguageService->get_all_language();      
           /* insert record into translation table */
           if(sizeof($arr_lang) > 0 )
           {
                foreach ($arr_lang as $lang) 
                {            
                    $arr_data = array();
                    
                    $template_subject     = $request->input('template_subject_'.$lang['locale']);
                    $template_html        = $request->input('template_html_'.$lang['locale']);
                    
                    if( (isset($template_subject)  && $template_subject != '') && (isset($template_html) && $template_html != '') )
                    { 
                        $translation = $entity->translateOrNew($lang['locale']);

                        $translation->email_template_id       = $entity->id;
                        $translation->template_subject        = $template_subject;
                        $translation->template_html           = $template_html;
                        $translation->save();
                        Flash::success(str_singular($this->module_title).' Created Successfully');
                    }
                }
           } 
           else
           {
               Flash::error('Problem Occured, While Creating '.str_singular($this->module_title));
           }
        	Flash::success(str_singular($this->module_title).' Created Successfully');
 		}
 		else
 		{
 			Flash::error('Problem Occurred, While Creating '.str_singular($this->module_title));	
 		}

       return redirect()->back();
    }

    

    public function edit($enc_id)
    {
        $id    = base64_decode($enc_id);

        $arr_data = [];
        $arr_lang = $this->LanguageService->get_all_language();
        
        $obj_data = $this->BaseModel->with('translations')->where('id', $id)->first();

        if($obj_data != FALSE)
        {
            $arr_data  = $obj_data->toArray(); 
            $arr_data['translations'] = $this->arrange_locale_wise($arr_data['translations']);
        }

        $arr_variables = isset($arr_data['template_variables'])?
        				 explode("~",$arr_data['template_variables']):array();

        $this->arr_view_data['page_title']                   = "Edit ".str_singular($this->module_title);
        $this->arr_view_data['edit_mode']                    = TRUE;
        $this->arr_view_data['arr_lang']                     = $this->LanguageService->get_all_language();
        $this->arr_view_data['arr_data']                     = $arr_data;
        $this->arr_view_data['module_url_path']              = $this->module_url_path;
        $this->arr_view_data['module_title']                 = $this->module_title;
        $this->arr_view_data['arr_variables']                = $arr_variables;
        $this->arr_view_data['theme_color']     = $this->theme_color;
        $this->arr_view_data['module_icon']     = $this->module_icon;
        
        return view($this->module_view_folder.'.edit',$this->arr_view_data);
    }


    public function update(Request $request, $enc_id)
    {   
		$id = base64_decode($enc_id);

    	$arr_rules['template_name'] 		    =	"required";
    	$arr_rules['template_from']			    =	"required";
    	$arr_rules['template_from_mail']	    =	"required";
    	$arr_rules['template_subject_en']		=	"required";
    	$arr_rules['template_html_en']			=	"required";

    	$arr_data  	=   array(
                                    'template_name'         =>   $request->input('template_name'),
									'template_from'			=>	 $request->input('template_from'),
									'template_from_mail'	=>	 $request->input('template_from_mail'),
    							);



        	$entity = 	$this->BaseModel->where('id',$id)->update($arr_data);

        	if($entity)
        	{
                 $fetched_email_template = $this->BaseModel->where('id',$id)
                                        ->first();

                    if($fetched_email_template)
                    {
                        /*Fetched all related active languages*/
                        $arr_lang =  $this->LanguageService->get_all_language(); 
                        /* insert record into translation table */
                        if(sizeof($arr_lang) > 0 )
                        {
                            foreach ($arr_lang as $lang) 
                            {            
                                
                                $template_subject     = $request->input('template_subject_'.$lang['locale']);
                                $template_html        = $request->input('template_html_'.$lang['locale']);
                               
                                if((isset($template_subject) && $template_subject != '') && 
                                   (isset($template_html) && $template_html != ''))
                                { 
                                    $translation = $fetched_email_template->translateOrNew($lang['locale']);

                                    $translation->email_template_id       = $id;
                                    $translation->template_subject        = $template_subject;
                                    $translation->template_html           = $template_html;
                                    $translation->save();

                                }

                            }

                        }
                        else
                        {
                            Flash::error('Problem Occured, While Updating '.str_singular($this->module_title).' Details');
                        }
                    }
        		Flash::success(str_singular($this->module_title).' Updated Successfully');
        	}
        	else
        	{
        		Flash::error('Problem Occured, While Updating '.str_singular($this->module_title));
        	}

    	return redirect()->back();
    }
    
    public function arrange_locale_wise(array $arr_data)
    {
        if(sizeof($arr_data)>0)
        {
            foreach ($arr_data as $key => $data) 
            {
                $arr_tmp = $data;
                unset($arr_data[$key]);
                $arr_data[$data['locale']] = $data;                    
            }

            return $arr_data;
        }
        else
        {
            return [];
        }
    }
}
