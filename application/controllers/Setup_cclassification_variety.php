<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_cclassification_variety extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_cclassification_variety');
        $this->controller_url='setup_cclassification_variety';
        //$this->load->model("sys_module_task_model");
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=="add")
        {
            $this->system_add();
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
            $data['title']="Varieties";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_cclassification_variety/list",$data,true));
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

    private function system_add()
    {
        if(isset($this->permissions['add'])&&($this->permissions['add']==1))
        {

            $data['title']="Create New Variety";
            $data["variety"] = Array(
                'id' => 0,
                'crop_id'=>0,
                'crop_type_id'=>0,
                'whose'=>'ARM',
                'competitor_id'=>'',
                'stock_id'=>'',
                'hybrid'=>'',
                'name' => '',
                'description' => '',
                'ordering' => 999,
                'principal_id' => 0,
                'name_import' => '',
                'status' => $this->config->item('system_status_active')
            );
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crop_types']=array();
            $data['competitors']=Query_helper::get_info($this->config->item('table_basic_setup_competitor'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['principals']=Query_helper::get_info($this->config->item('table_basic_setup_principal'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_cclassification_variety/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
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
            $this->db->select('type.crop_id crop_id');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->where('v.id',$variety_id);

            $data['variety']=$this->db->get()->row_array();
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crop_types']=Query_helper::get_info($this->config->item('table_setup_classification_crop_types'),array('id value','name text'),array('crop_id ='.$data['variety']['crop_id']));
            $data['competitors']=Query_helper::get_info($this->config->item('table_basic_setup_competitor'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['principals']=Query_helper::get_info($this->config->item('table_basic_setup_principal'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $data['title']="Edit Variety (".$data['variety']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_cclassification_variety/add_edit",$data,true));
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
            $this->db->select('type.crop_id crop_id');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->where('v.id',$variety_id);

            $data['variety']=$this->db->get()->row_array();
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crop_types']=Query_helper::get_info($this->config->item('table_setup_classification_crop_types'),array('id value','name text'),array('crop_id ='.$data['variety']['crop_id']));
            $data['competitors']=Query_helper::get_info($this->config->item('table_basic_setup_competitor'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['principals']=Query_helper::get_info($this->config->item('table_basic_setup_principal'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $data['title']="Details of (".$data['variety']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_cclassification_variety/details",$data,true));
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
        if($id>0)
        {
            if(!(isset($this->permissions['edit'])&&($this->permissions['edit']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
                die();
            }
        }
        else
        {
            if(!(isset($this->permissions['add'])&&($this->permissions['add']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
                die();

            }
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->jsonReturn($ajax);
        }
        else
        {
            $data=$this->input->post('variety');
            if($data['whose']!='Competitor')
            {
                $data['competitor_id']='';
            }

            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = time();

                Query_helper::update($this->config->item('table_setup_classification_varieties'),$data,array("id = ".$id));

            }
            else
            {

                $data['user_created'] = $user->user_id;
                $data['date_created'] = time();
                Query_helper::add($this->config->item('table_setup_classification_varieties'),$data);
            }
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_add();
                }
                else
                {
                    $this->system_list();
                }
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
        $this->load->library('form_validation');
        $this->form_validation->set_rules('variety[name]',$this->lang->line('LABEL_NAME'),'required');
        $this->form_validation->set_rules('variety[crop_type_id]',$this->lang->line('LABEL_CROP_TYPE'),'required');
        $variety=$this->input->post('variety');

        if($variety['whose']=='Competitor')
        {
            $this->form_validation->set_rules('variety[competitor_id]',$this->lang->line('LABEL_COMPETITOR_NAME'),'required');
        }
        //same name restriction withdraw
        /*$exists=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),array('name'),array('name ="'.$variety['name'].'"','id !='.$this->input->post('id'),'status ="'.$this->config->item('system_status_active').'"'),1);
        if($exists)
        {
            $this->message="Same Variety Name already Exists";
            return false;
        }*/
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    public function get_items()
    {
        $user = User_helper::get_user();

        $this->db->from($this->config->item('table_setup_classification_varieties').' v');
        $this->db->select('v.id,v.name,v.status,v.ordering,v.whose,v.stock_id');
        $this->db->select('crop.name crop_name');
        $this->db->select('type.name crop_type_name');
        $this->db->select('competitor.name competitor_name');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->join($this->config->item('table_basic_setup_competitor').' competitor','competitor.id = v.competitor_id','LEFT');
        //$this->db->order_by('v.ordering','ASC');
        $this->db->where('v.status !=',$this->config->item('system_status_delete'));


        $items=$this->db->get()->result_array();



        $this->jsonReturn($items);

    }

}
