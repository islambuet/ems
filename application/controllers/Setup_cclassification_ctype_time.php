<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_cclassification_ctype_time extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_cclassification_ctype_time');
        $this->locations=User_helper::get_locations();
        if(!is_array($this->locations))
        {
            if($this->locations=='wrong')
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line('MSG_LOCATION_INVALID');
                $this->jsonReturn($ajax);
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED');
                $this->jsonReturn($ajax);
            }

        }
        $this->controller_url='setup_cclassification_ctype_time';
    }

    public function index($action="add",$id=0)
    {
        if($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_add();
        }
    }
    private function system_add()
    {
        if(isset($this->permissions['edit'])&&($this->permissions['edit']==1))
        {
            $data['title']="Assign Crop Type to a Season";
            $ajax['status']=true;
            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id']));
                }
            }
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_cclassification_ctype_time/add_edit",$data,true));
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

    private function system_list()
    {


        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            $crop_type_id=$this->input->post('crop_type_id');
            $territory_id=$this->input->post('territory_id');
            $ajax['status']=true;

            $info=Query_helper::get_info($this->config->item('table_setup_classification_variety_time'),'*',array('crop_type_id ='.$crop_type_id,'territory_id ='.$territory_id,'revision =1'),1);
            if($info)
            {
                $data['title']="Edit Season";
                $data['type_time']=$info;
            }
            else
            {
                $data['title']="Add New Season";
                $data['type_time']['crop_type_id']=$crop_type_id;
                $data['type_time']['territory_id']=$territory_id;
                for($i=1;$i<13;$i++)
                {
                    $data['type_time']['month_'.$i]=0;
                }
            }
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("setup_cclassification_ctype_time/list",$data,true));
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
    private function system_save()
    {
        $user = User_helper::get_user();
        $time=time();
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
            $data=$this->input->post('type_time');
            $this->db->trans_start();  //DB Transaction Handle START
            $this->db->where('crop_type_id',$data['crop_type_id']);
            $this->db->where('territory_id',$data['territory_id']);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_setup_classification_variety_time'));

            $data['user_created'] = $user->user_id;
            $data['date_created'] = $time;
            $data['revision'] = 1;
            Query_helper::add($this->config->item('table_setup_classification_variety_time'),$data);

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                $this->system_add();
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

}
