<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_bsetup_payment_ways extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_bsetup_payment_ways');
        $this->controller_url='setup_bsetup_payment_ways';
        //$this->load->model("sys_module_task_model");
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }

        elseif($action=="get_items")
        {
            $this->get_items();
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
            $data['title']="Payment Ways";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
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
    public function get_items()
    {
        $items=Query_helper::get_info($this->config->item('table_basic_setup_payment_ways'),'*',array('status !="'.$this->config->item('system_status_delete').'"'));
        $this->jsonReturn($items);

    }
    private function system_add()
    {
        if(isset($this->permissions['add'])&&($this->permissions['add']==1))
        {

            $data['title']="Create New Payment Way";
            $data["crop"] = Array(
                'id' => 0,
                'name' => '',
                'description' => '',
                'ordering' => 99
            );
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
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
                $crop_id=$this->input->post('id');
            }
            else
            {
                $crop_id=$id;
            }

            $data['crop']=Query_helper::get_info($this->config->item('table_basic_setup_payment_ways'),'*',array('id ='.$crop_id),1);
            $data['title']="Edit Payment way (".$data['crop']['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$crop_id);
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
            $data=$this->input->post('crop');
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = time();

                Query_helper::update($this->config->item('table_basic_setup_payment_ways'),$data,array("id = ".$id));

            }
            else
            {

                $data['user_created'] = $user->user_id;
                $data['date_created'] = time();
                Query_helper::add($this->config->item('table_basic_setup_payment_ways'),$data);
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
        $this->form_validation->set_rules('crop[name]',$this->lang->line('LABEL_NAME'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }


}
