<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_bsetup_warehouse extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_bsetup_warehouse');
        $this->controller_url='setup_bsetup_warehouse';
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
            $data['title']="Warehouses";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_bsetup_warehouse/list",$data,true));
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

            $data['title']="Create New Warehouse";
            $data["warehouse"] = Array(
                'id' => 0,
                'name' => '',
                'capacity' => '',
                'address' => '',
                'ordering' => 99
            );
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['warehouse_crops']=array();
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_bsetup_warehouse/add_edit",$data,true));
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
                $warehouse_id=$this->input->post('id');
            }
            else
            {
                $warehouse_id=$id;
            }

            $data['warehouse']=Query_helper::get_info($this->config->item('table_basic_setup_warehouse'),'*',array('id ='.$warehouse_id),1);
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['warehouse_crops']=array();
            $warehouse_crops=Query_helper::get_info($this->config->item('table_basic_setup_warehouse_crops'),array('crop_id'),array('revision = 1','warehouse_id ='.$warehouse_id));
            foreach($warehouse_crops as$wc)
            {
                $data['warehouse_crops'][]=$wc['crop_id'];
            }

            $data['title']="Edit Warehouse (".$data['warehouse']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_bsetup_warehouse/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$warehouse_id);
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
            $time=time();
            $data=$this->input->post('warehouse');
            $this->db->trans_start();  //DB Transaction Handle START
            if($id==0)
            {
                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                $warehouse_id=Query_helper::add($this->config->item('table_basic_setup_warehouse'),$data);
                if($warehouse_id===false)
                {
                    $this->db->trans_complete();
                    $ajax['status']=false;
                    $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                    $this->jsonReturn($ajax);
                    die();
                }
                else
                {
                    $id=$warehouse_id;
                }
            }
            else
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = $time;
                Query_helper::update($this->config->item('table_basic_setup_warehouse'),$data,array("id = ".$id));
            }
            $crops=$this->input->post('crops');

            $this->db->where('warehouse_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_basic_setup_warehouse_crops'));
            if(is_array($crops))
            {
                foreach($crops as $crop)
                {
                    $crop_data=array();
                    $crop_data['warehouse_id']=$id;
                    $crop_data['crop_id']=$crop;
                    $crop_data['user_created'] = $user->user_id;
                    $crop_data['date_created'] = $time;
                    Query_helper::add($this->config->item('table_basic_setup_warehouse_crops'),$crop_data);
                }
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
        $this->form_validation->set_rules('warehouse[name]',$this->lang->line('LABEL_NAME'),'required');
        $this->form_validation->set_rules('warehouse[capacity]',$this->lang->line('LABEL_CAPACITY_KG'),'required|numeric');

        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    public function get_items()
    {
        $items=Query_helper::get_info($this->config->item('table_basic_setup_warehouse'),array('id','name','capacity','address','ordering'),array('status !="'.$this->config->item('system_status_delete').'"'));
        $this->jsonReturn($items);

    }

}
