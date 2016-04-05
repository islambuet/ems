<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Survey_product_arm extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Survey_product_arm');
        $this->controller_url='survey_product_arm';
        //$this->load->model("sys_module_task_model");
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_list($id);
        }
    }

    private function system_list()
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            $data['title']="ARM Varieties Settings";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("survey_product_arm/list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }

    }
    private function system_edit($id)
    {
        if(isset($this->permissions['edit'])&&($this->permissions['edit']==1))
        {
            if(($this->input->post('id')))
            {
                $variety_id=$this->input->post('id');
            }
            else
            {
                $variety_id=$id;
            }

            $this->db->from($this->config->item('table_setup_classification_varieties').' v');
            $this->db->select('v.*');
            $this->db->select('type.name type_name');
            $this->db->select('crop.name crop_name');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('v.id',$variety_id);
            $this->db->where('v.whose','ARM');
            $data['variety']=$this->db->get()->row_array();
            if(!$data['variety'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$variety_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

            $info=Query_helper::get_info($this->config->item('table_survey_product'),'*',array('variety_id ='.$variety_id),1);
            if($info)
            {
                $data['survey']=$info;

            }
            else
            {
                $data['survey']['characteristics']='';
                $data['survey']['remarks']='';
                $data['survey']['picture']='';
                $data['survey']['date_start']=time();
                $data['survey']['date_end']=time();
            }
            $data['date_start']=time();
            $data['date_end']=time();

            $data['title']="Settings for (".$data['variety']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("survey_product_arm/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$variety_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }
    private function system_details($id)
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            if(($this->input->post('id')))
            {
                $variety_id=$this->input->post('id');
            }
            else
            {
                $variety_id=$id;
            }

            $this->db->from($this->config->item('table_setup_classification_varieties').' v');
            $this->db->select('v.*');
            $this->db->select('type.name type_name');
            $this->db->select('crop.name crop_name');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->where('v.id',$variety_id);
            $this->db->where('v.whose','ARM');
            $data['variety']=$this->db->get()->row_array();
            if(!$data['variety'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$variety_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

            $info=Query_helper::get_info($this->config->item('table_survey_product'),'*',array('variety_id ='.$variety_id),1);
            if($info)
            {
                $data['survey']=$info;

            }
            else
            {
                $data['survey']['characteristics']='';
                $data['survey']['remarks']='';
                $data['survey']['picture']='';
                $data['survey']['date_start']=time();
                $data['survey']['date_end']=time();
            }
            $data['date_start']=time();
            $data['date_end']=time();

            $data['title']="Settings Detail of (".$data['variety']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("survey_product_arm/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$variety_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }

    private function system_save()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if(!(isset($this->permissions['edit'])&&($this->permissions['edit']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
            die();
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->jsonReturn($ajax);
        }
        else
        {
            $variety=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),'*',array('id ='.$id,'whose ="ARM"'),1);
            if(!$variety)
            {
                System_helper::invalid_try('Trying to save invalid variety',$id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $info=Query_helper::get_info($this->config->item('table_survey_product'),'*',array('variety_id ='.$id),1);
            $data=$this->input->post('survey');
            $data['date_start']=System_helper::get_time($this->input->post('date_start').'-1970');
            $data['date_end']=System_helper::get_time($this->input->post('date_end').'-1970');
            if($data['date_end']<$data['date_start'])
            {
                $data['date_end']=System_helper::get_time($this->input->post('date_end').'-1971');
            }
            if($data['date_end']!=0)
            {
                $data['date_end']+=24*3600-1;
            }

            $file_folder='images/survey_product/'.$id;
            $dir=(FCPATH).$file_folder;
            if(!is_dir($dir))
            {
                mkdir($dir, 0777);
            }
            $uploaded_files = System_helper::upload_file($file_folder);
            if(array_key_exists('image',$uploaded_files))
            {
                if($uploaded_files['image']['status'])
                {
                    $data['picture']=$file_folder.'/'.$uploaded_files['image']['info']['file_name'];
                    $data['picture_file_name']=$uploaded_files['image']['info']['file_name'];
                }
                else
                {

                    $ajax['status']=false;
                    $ajax['system_message']=$uploaded_files['image']['message'];
                    $this->jsonReturn($ajax);
                    die();
                }
            }
            $this->db->trans_start();  //DB Transaction Handle START
            if($info)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = time();

                Query_helper::update($this->config->item('table_survey_product'),$data,array("id = ".$info['id']));

            }
            else
            {

                $data['variety_id'] = $id;
                $data['user_created'] = $user->user_id;
                $data['date_created'] = time();
                Query_helper::add($this->config->item('table_survey_product'),$data);
            }
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                $this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->jsonReturn($ajax);
            }
        }
    }
    private function check_validation()
    {
        return true;
    }
    public function get_items()
    {
        $this->db->from($this->config->item('table_setup_classification_varieties').' v');
        $this->db->select('sp.characteristics,sp.picture,sp.picture_file_name');
        $this->db->select('v.id,v.name');
        $this->db->select('crop.name crop_name');
        $this->db->select('type.name crop_type_name');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->join($this->config->item('table_survey_product').' sp','sp.variety_id = v.id','LEFT');
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('type.ordering','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->where('v.status !=',$this->config->item('system_status_delete'));
        $this->db->where('v.whose','ARM');


        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            if(strlen($item['characteristics'])>0)
            {
                $item['characteristics']="Done";
            }
            else
            {
                $item['characteristics']="Not Done";
            }
            if(strlen($item['picture_file_name'])>0)
            {
                $item['picture']="Done";
            }
            else
            {
                $item['picture']="Not Done";
            }
        }



        $this->jsonReturn($items);

    }

}
