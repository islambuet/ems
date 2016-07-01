<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_arm_competitor_variety extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_arm_competitor_variety');
        $this->controller_url='reports_arm_competitor_variety';
        //$this->load->model("sys_module_task_model");
        //$this->load->model("sales_model");
    }

    public function index($action="search",$id=0)
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="list_variety")
        {
            $this->system_list_variety();
        }
        elseif($action=="list")
        {
            $this->system_list();
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
            $data['title']="Arm & Competitor Variety Report Search";
            $ajax['status']=true;
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array());
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("reports_arm_competitor_variety/search",$data,true));
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
    private function system_list_variety()
    {
        $filters=$this->input->post('report');

        //ARM
        $this->db->from($this->config->item('table_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
        if($filters['crop_id']>0)
        {
            $this->db->where('crop.id',$filters['crop_id']);
            if($filters['crop_type_id']>0)
            {
                $this->db->where('crop_type.id',$filters['crop_type_id']);
            }
        }

        $this->db->where('v.whose','ARM');
        $this->db->where('v.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('v.ordering','ASC');
        $data['arm_varieties']=$this->db->get()->result_array();
        //competitor
        $this->db->from($this->config->item('table_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
        if($filters['crop_id']>0)
        {
            $this->db->where('crop.id',$filters['crop_id']);
            if($filters['crop_type_id']>0)
            {
                $this->db->where('crop_type.id',$filters['crop_type_id']);
            }
        }

        $this->db->where('v.whose','Competitor');
        $this->db->where('v.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('v.ordering','ASC');
        $data['competitor_varieties']=$this->db->get()->result_array();
        $data['report']=$filters;
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#variety_list_container","html"=>$this->load->view("reports_arm_competitor_variety/list_variety",$data,true));

        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->jsonReturn($ajax);
    }
    private function system_list()
    {

        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            $variety_ids=$this->input->post('variety_ids');

            if(!((sizeof($variety_ids)>0)))
            {
                $ajax['status']=false;
                $ajax['system_message']="Please Select at lease One Variety";
                $this->jsonReturn($ajax);
            }
            $keys=',';

            $keys.="variety_ids:'".json_encode($variety_ids)."',";
            $data['keys']=trim($keys,',');
            $data['title']="Arm & Competitor Variety Report ";

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_arm_competitor_variety/list",$data,true));

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
        $items=array();
        $variety_ids=json_decode($this->input->post('variety_ids'),true);
        $this->db->from($this->config->item('table_setup_classification_varieties').' v');
        $this->db->select('sp.*');
        $this->db->select('v.id,v.name variety_name');
        $this->db->select('crop.name crop_name');
        $this->db->select('type.name crop_type_name');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->join($this->config->item('table_survey_product').' sp','sp.variety_id = v.id','LEFT');
        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('type.ordering','ASC');
        $this->db->order_by('v.ordering','ASC');
        $this->db->where('v.status !=',$this->config->item('system_status_delete'));
        $this->db->where_in('v.id',$variety_ids);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $item=array();
            $item['crop_info']=$result['crop_name'].'<br>'.$result['crop_type_name'].'<br>'.$result['variety_name'];
            $item['characteristics']=nl2br($result['characteristics']);
            $item['cultivation_period']='';
            if($result['date_start']!=0)
            {
                $item['cultivation_period']=date('d-F',$result['date_start']).' to '.date('d-F',$result['date_end']);
            }
            $image=base_url().'images/no_image.jpg';
            if(strlen($result['picture_url'])>0)
            {
                $image=$result['picture_url'];
            }
            $item['picture']='<img style="max-width: 100%;max-height: 100%" src="'.$image.'">';
            $item['remarks']=$result['remarks'];
            $item['details']['crop_name']=$result['crop_name'];
            $item['details']['crop_type_name']=$result['crop_type_name'];
            $item['details']['variety_name']=$result['variety_name'];
            $item['details']['characteristics']=nl2br($result['characteristics']);
            $item['details']['cultivation_period']=$item['cultivation_period'];
            $item['details']['picture']=$image;
            $item['details']['remarks']=$result['remarks'];
            $items[]=$item;
        }
        $this->jsonReturn($items);
    }
}
