<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_cclassification_bounsrule extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_cclassification_bounsrule');
        $this->controller_url='setup_cclassification_bounsrule';
        //$this->load->model("sys_module_task_model");
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="add_bonus")
        {
            $this->system_add_bonus($id);
        }
        elseif($action=="edit_bonus")
        {
            $this->system_edit_bonus($id);
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="save_bonus")
        {
            $this->system_save_bonus();
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
            $data['title']="Bonus Rules";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_cclassification_bounsrule/list",$data,true));
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

            $data['title']="Create New Bonus Rule";
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('name ASC'));
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_cclassification_bounsrule/add",$data,true));
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
    private function system_details($id)
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            if(($this->input->post('id')))
            {
                $bonus_id=$this->input->post('id');
            }
            else
            {
                $bonus_id=$id;
            }
            $this->db->from($this->config->item('table_setup_classification_variety_bonus').' vb');
            $this->db->select('vb.id');
            $this->db->select('v.name variety_name');
            $this->db->select('crop.name crop_name');
            $this->db->select('type.name crop_type_name');
            $this->db->select('pack.name pack_size_name');


            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id = vb.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_vpack_size').' pack','pack.id = vb.pack_size_id','INNER');
            $this->db->where('vb.id',$bonus_id);
            $data['bonus']=$this->db->get()->row_array();
            if(!$data['bonus'])
            {
                System_helper::invalid_try($this->config->item('system_view_not_exists'),$bonus_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['title']="Details of Bonus Rules";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_cclassification_bounsrule/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$bonus_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }
    private function system_add_bonus($bonus_id)
    {
        if(isset($this->permissions['add'])&&($this->permissions['add']==1))
        {


            $this->db->from($this->config->item('table_setup_classification_variety_bonus').' vb');
            $this->db->select('vb.id bonus_id');
            $this->db->select('v.name variety_name');
            $this->db->select('crop.name crop_name');
            $this->db->select('type.name crop_type_name');
            $this->db->select('pack.name pack_size_name');


            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id = vb.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_vpack_size').' pack','pack.id = vb.pack_size_id','INNER');
            $this->db->where('vb.id',$bonus_id);
            $data['bonus']=$this->db->get()->row_array();
            if(!$data['bonus'])
            {
                System_helper::invalid_try($this->config->item('system_view_not_exists'),$bonus_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['bonus']['id']=0;
            $data['bonus']['quantity_min']=0;
            $data['bonus']['bonus_pack_size_id']=0;
            $data['bonus']['quantity_bonus']=0;
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('name ASC'));
            $data['title']="Add Bonus Rules";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_cclassification_bounsrule/add_edit_bonus",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/add_bonus/'.$bonus_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }
    private function system_edit_bonus($id)
    {
        if(isset($this->permissions['edit'])&&($this->permissions['edit']==1))
        {
            if(($this->input->post('id')))
            {
                $detail_id=$this->input->post('id');
            }
            else
            {
                $detail_id=$id;
            }


            $this->db->from($this->config->item('table_setup_classification_variety_bonus_details').' vbd');
            $this->db->select('vbd.id,vbd.bonus_id,vbd.quantity_min,vbd.bonus_pack_size_id,vbd.quantity_bonus');
            $this->db->select('v.name variety_name');
            $this->db->select('crop.name crop_name');
            $this->db->select('type.name crop_type_name');
            $this->db->select('pack.name pack_size_name');


            $this->db->join($this->config->item('table_setup_classification_variety_bonus').' vb','vb.id = vbd.bonus_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id = vb.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_vpack_size').' pack','pack.id = vb.pack_size_id','INNER');
            $this->db->where('vbd.id',$detail_id);
            $this->db->where('revision',1);
            $data['bonus']=$this->db->get()->row_array();
            if(!$data['bonus'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$detail_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('name ASC'));
            $data['title']="Edit Bonus Rules";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_cclassification_bounsrule/add_edit_bonus",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit_bonus/'.$detail_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }
    private function system_save_bonus()
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
        if(!$this->check_validation_bonus())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->jsonReturn($ajax);
        }
        else
        {
            $time=time();
            $data=$this->input->post('bonus');
            /*echo '<PRE>';
            print_r($data);
            echo '</PRE>';
            die();*/
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $info=Query_helper::get_info($this->config->item('table_setup_classification_variety_bonus_details'),'*',array('id ='.$id),1);
                if(!$info)
                {
                    System_helper::invalid_try($this->config->item('system_save'),$id);
                    $ajax['status']=false;
                    $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                    $this->jsonReturn($ajax);
                }
                else
                {
                    if($info['quantity_min']!=$data['quantity_min'])
                    {
                        $this->db->where('bonus_id',$info['bonus_id']);
                        $this->db->where('quantity_min',$info['quantity_min']);
                        $this->db->set('revision', 'revision+1', FALSE);
                        $this->db->update($this->config->item('table_setup_classification_variety_bonus_details'));
                    }
                }

            }


            $this->db->where('bonus_id',$data['bonus_id']);
            $this->db->where('quantity_min',$data['quantity_min']);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_setup_classification_variety_bonus_details'));

            $data['user_created'] = $user->user_id;
            $data['date_created'] = $time;
            $data['revision'] = 1;
            Query_helper::add($this->config->item('table_setup_classification_variety_bonus_details'),$data);

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                $_POST['id']=$data['bonus_id'];
                $this->system_details($data['bonus_id']);
                //$this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->jsonReturn($ajax);
            }
        }
    }
    private function check_validation_bonus()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('bonus[bonus_id]',$this->lang->line('LABEL_NAME'),'required|is_natural_no_zero');
        $this->form_validation->set_rules('bonus[quantity_min]',$this->lang->line('LABEL_QUANTITY_MIN'),'required|is_natural');
        $this->form_validation->set_rules('bonus[bonus_pack_size_id]',$this->lang->line('LABEL_BONUS_PACK_NAME'),'required|is_natural_no_zero');
        $this->form_validation->set_rules('bonus[quantity_bonus]',$this->lang->line('LABEL_QUANTITY_BONUS'),'required|is_natural');


        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        $data=$this->input->post('bonus');
        $info=Query_helper::get_info($this->config->item('table_setup_classification_variety_bonus_details'),'*',array('bonus_id ='.$data['bonus_id'],'quantity_min ='.$data['quantity_min'],'revision =1','id !='.$this->input->post('id')));
        if($info)
        {
            $this->message="Bonus Rule for this Quantity already Exists";
            return false;
        }
        return true;
    }
    private function system_save()
    {

        $user = User_helper::get_user();
        if(!(isset($this->permissions['add'])&&($this->permissions['add']==1)))
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
            $time=time();
            $bonus=$this->input->post('bonus');

            $bonus['user_created'] = $user->user_id;
            $bonus['date_created'] = $time;

            $this->db->trans_start();  //DB Transaction Handle START

            $bonus_id=Query_helper::add($this->config->item('table_setup_classification_variety_bonus'),$bonus);
            $details=$this->input->post('details');
            $details['bonus_id']=$bonus_id;
            $details['user_created'] = $user->user_id;
            $details['date_created'] = $time;
            $details['revision'] = 1;
            Query_helper::add($this->config->item('table_setup_classification_variety_bonus_details'),$details);

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
        $data=$this->input->post('bonus');
        $info=Query_helper::get_info($this->config->item('table_setup_classification_variety_bonus'),'*',array('variety_id ='.$data['variety_id'],'pack_size_id ='.$data['pack_size_id']),1);
        if($info)
        {
            $this->message="Bonus Rule already Exists.Please edit the rule.";
            return false;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('bonus[pack_size_id]',$this->lang->line('LABEL_PACK_NAME'),'required');
        $this->form_validation->set_rules('bonus[variety_id]',$this->lang->line('LABEL_VARIETY_NAME'),'required');
        $this->form_validation->set_rules('details[quantity_min]',$this->lang->line('LABEL_QUANTITY_MIN'),'required|is_natural');
        $this->form_validation->set_rules('details[bonus_pack_size_id]',$this->lang->line('LABEL_BONUS_PACK_NAME'),'required|is_natural_no_zero');
        $this->form_validation->set_rules('details[quantity_bonus]',$this->lang->line('LABEL_QUANTITY_BONUS'),'required|is_natural');


        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }

        return true;
    }
    public function get_items()
    {
        $this->db->from($this->config->item('table_setup_classification_variety_bonus').' vb');
        $this->db->select('vb.id');
        $this->db->select('v.name variety_name');
        $this->db->select('crop.name crop_name');
        $this->db->select('type.name crop_type_name');
        $this->db->select('pack.name pack_size_name');

        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id = vb.variety_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_vpack_size').' pack','pack.id = vb.pack_size_id','INNER');

        $this->db->group_by('vb.id');


        $items=$this->db->get()->result_array();
        $this->jsonReturn($items);
    }
    public function get_bonus_details($bonus_id)
    {
        $this->db->from($this->config->item('table_setup_classification_variety_bonus_details').' vbd');
        $this->db->select('vbd.id,vbd.quantity_min,vbd.quantity_bonus');
        $this->db->select('pack.name pack_size_name');
        $this->db->join($this->config->item('table_setup_classification_vpack_size').' pack','pack.id = vbd.bonus_pack_size_id','INNER');
        $this->db->where('vbd.bonus_id',$bonus_id);
        $this->db->where('vbd.revision',1);
        $this->db->order_by('vbd.quantity_min DESC');

        $items=$this->db->get()->result_array();
        $this->jsonReturn($items);
    }

}
