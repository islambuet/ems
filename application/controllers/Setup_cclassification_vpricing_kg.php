<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_cclassification_vpricing_kg extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_cclassification_vpricing_kg');
        $this->controller_url='setup_cclassification_vpricing_kg';

    }

    public function index($action="search",$id1=0,$id2=0,$id3=0)
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items")
        {
            $this->get_items($id1);
        }
        elseif($action=="edit")
        {
            $this->system_edit($id1,$id2);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_search();
        }
    }
    private function system_search()
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {

            $fy_info=System_helper::get_fiscal_years();
            $data['years']=$fy_info['years'];
            $data['price']=array();
            $data['price']['year0_id']=$fy_info['budget_year']['value']-1;
            $data['title']="Variety Pricing (Kg) Search";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_cclassification_vpricing_kg/search",$data,true));
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
            $data['year0_id']=$this->input->post('year0_id');
            $keys=',';
            $keys.="year0_id:'".$data['year0_id']."',";
            $data['keys']=trim($keys,',');
            $data['title']="Varieties Price (Kg)";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("setup_cclassification_vpricing_kg/list",$data,true));
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
    private function get_items($year0_id)
    {
        $this->db->from($this->config->item('table_setup_classification_varieties').' v');
        $this->db->select('v.id,vpk.price_net');
        $this->db->select('v.name variety_name');
        $this->db->select('crop.name crop_name');
        $this->db->select('type.name type_name');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_variety_price_kg').' vpk','v.id = vpk.variety_id and vpk.year0_id ='.$year0_id,'LEFT');
        $this->db->order_by('crop.ordering ASC');
        $this->db->order_by('type.ordering ASC');
        $this->db->order_by('v.ordering ASC');

        $items=$this->db->get()->result_array();
        $this->jsonReturn($items);
    }
    private function system_edit($year0_id,$variety_id)
    {
        if(isset($this->permissions['edit'])&&($this->permissions['edit']==1))
        {
            if(($this->input->post('id')))
            {
                $variety_id=$this->input->post('id');
            }

            $this->db->from($this->config->item('table_setup_classification_varieties').' v');
            $this->db->select('v.id variety_id,vpk.price_net');
            $this->db->select('v.name variety_name');
            $this->db->select('crop.name crop_name');
            $this->db->select('type.name type_name');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_variety_price_kg').' vpk','v.id = vpk.variety_id and vpk.year0_id ='.$year0_id,'LEFT');
            $this->db->where('v.id',$variety_id);
            $data['price']=$this->db->get()->row_array();
            if(!$data['price'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try';
                $this->jsonReturn($ajax);
                die();
            }
            $year=Query_helper::get_info($this->config->item('table_basic_setup_fiscal_year'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"',' id ='.$year0_id),1);
            if(!$year)
            {
                $ajax['status']=false;
                $ajax['system_message']='Invalid Try';
                $this->jsonReturn($ajax);
                die();
            }
            $data['price']['year0_id']=$year0_id;
            $data['price']['year_name']=$year['text'];

            $data['title']='Edit Pricing';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_cclassification_vpricing_kg/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$year0_id.'/'.$variety_id);
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

        $year0_id=$this->input->post('year0_id');
        $variety_id=$this->input->post('variety_id');
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
            $info=Query_helper::get_info($this->config->item('table_setup_classification_variety_price_kg'),'*',array('year0_id ='.$year0_id,'variety_id ='.$variety_id),1);
            $this->db->trans_start();  //DB Transaction Handle START
            $data=$this->input->post('price');
            if($info)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = $time;
                Query_helper::update($this->config->item('table_setup_classification_variety_price_kg'),$data,array("id = ".$info['id']));
            }
            else
            {
                $data['year0_id'] = $year0_id;
                $data['variety_id'] = $variety_id;
                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                Query_helper::add($this->config->item('table_setup_classification_variety_price_kg'),$data);
            }

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $this->system_search();
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
        $this->form_validation->set_rules('price[price_net]',$this->lang->line('LABEL_PRICE_NET'),'required|numeric');

        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }



}
