<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_cclassification_vpricing extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_cclassification_vpricing');
        $this->controller_url='setup_cclassification_vpricing';
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
            $data['title']="Varieties Price";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_cclassification_vpricing/list",$data,true));
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

            $data['title']="Add New pricing";
            $data["price"] = Array(
                'id' => 0,
                'pack_size_id'=>0,
                'whose'=>'ARM',
                'price'=>''
            );
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('name ASC'));
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_cclassification_vpricing/add_edit",$data,true));
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
                $price_id=$this->input->post('id');
            }
            else
            {
                $price_id=$id;
            }

            $this->db->from($this->config->item('table_setup_classification_variety_price').' vp');
            $this->db->select('vp.*');
            $this->db->select('v.crop_type_id crop_type_id');
            $this->db->select('type.crop_id crop_id');

            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id = vp.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->where('vp.revision',1);
            $this->db->where('vp.id',$price_id);
            $data['price']=$this->db->get()->row_array();
            if(!$data['price'])
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
                die();
            }

            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crop_types']=Query_helper::get_info($this->config->item('table_setup_classification_crop_types'),array('id value','name text'),array('crop_id ='.$data['price']['crop_id']));
            $data['varieties']=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),array('id value','name text'),array('crop_type_id ='.$data['price']['crop_type_id']));
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $data['title']='Edit Pricing';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_cclassification_vpricing/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$price_id);
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
            $data=$this->input->post('price');
            $this->db->where('variety_id',$data['variety_id']);
            $this->db->where('pack_size_id',$data['pack_size_id']);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_setup_classification_variety_price'));

            $data['user_created'] = $user->user_id;
            $data['date_created'] = time();
            $data['revision'] = 1;
            Query_helper::add($this->config->item('table_setup_classification_variety_price'),$data);

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
        $this->form_validation->set_rules('price[variety_id]',$this->lang->line('LABEL_VARIETY_NAME'),'required');
        $this->form_validation->set_rules('price[price]',$this->lang->line('LABEL_PRICE'),'required|numeric');
        $this->form_validation->set_rules('price[pack_size_id]',$this->lang->line('LABEL_PACK_NAME'),'required');
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

        $this->db->from($this->config->item('table_setup_classification_variety_price').' vp');
        $this->db->select('vp.id,vp.price');
        $this->db->select('v.name variety_name');
        $this->db->select('crop.name crop_name');
        $this->db->select('type.name crop_type_name');
        $this->db->select('pack.name pack_size_name');

        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id = vp.variety_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_vpack_size').' pack','pack.id = vp.pack_size_id','INNER');
        $this->db->where('vp.revision',1);

        $items=$this->db->get()->result_array();
        $this->jsonReturn($items);

    }

}
