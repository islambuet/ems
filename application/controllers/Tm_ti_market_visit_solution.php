<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tm_ti_market_visit_solution extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_ti_market_visit_solution');
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
        $this->controller_url='tm_ti_market_visit_solution';
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
            $data['title']="Visit and Solution List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_ti_market_visit_solution/list",$data,true));
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
        if((isset($this->permissions['edit'])&&($this->permissions['edit']==1))||(isset($this->permissions['add'])&&($this->permissions['add']==1)))
        {
            if(($this->input->post('id')))
            {
                $visit_id=$this->input->post('id');
            }
            else
            {
                $visit_id=$id;
            }
            $this->db->from($this->config->item('table_tm_market_visit_ti').' mvt');
            $this->db->select('mvt.*');
            $this->db->select('stmv.host_type,stmv.host_id,stmv.district_id,stmv.territory_id');
            $this->db->select('shift.name shift_name');

            $this->db->join($this->config->item('table_setup_tm_market_visit').' stmv','stmv.id = mvt.setup_id','INNER');
            $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = stmv.shift_id','INNER');
            $this->db->where('mvt.id',$visit_id);
            $data['visit']=$this->db->get()->row_array();
            if(!$data['visit'])
            {
                System_helper::invalid_try("Invalid try at edit",$visit_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['districts']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$data['visit']['territory_id']),0,0,array('ordering'));
            $data['district']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('id ='.$data['visit']['district_id']),1);
            $data['visit']['customer_name']='';
            if($data['visit']['host_type']==$this->config->item('system_host_type_customer'))
            {
                $this->db->from($this->config->item('table_csetup_customers').' cus');
                $this->db->select('cus.id value,CONCAT(cus.customer_code," - ",cus.name) text,cus.status');
                $this->db->where('cus.id',$data['visit']['host_id']);
                $result=$this->db->get()->row_array();
                $data['visit']['customer_name']=$result['text'];
                if($result['status']!=$this->config->item('system_status_active'))
                {
                    $data['visit']['customer_name'].= '('.$result['status'].')';
                }
            }
            elseif($data['visit']['host_type']==$this->config->item('system_host_type_other_customer'))
            {
                $this->db->from($this->config->item('table_csetup_other_customers').' cus');
                $this->db->select('cus.id value,cus.name text,cus.status');
                $this->db->where('cus.id',$data['visit']['host_id']);
                $result=$this->db->get()->row_array();
                $data['visit']['customer_name']=$result['text'];
                if($result['status']!=$this->config->item('system_status_active'))
                {
                    $data['visit']['customer_name'].= '('.$result['status'].')';
                }
            }
            $data['title']='TI Market Visit Solution';
            $user_ids=array();
            $user_ids[$data['visit']['user_created']]=$data['visit']['user_created'];
            $data['previous_solutions']=Query_helper::get_info($this->config->item('table_tm_market_visit_solution_ti'),'*',array('visit_id ='.$visit_id),0,0,array('date_created DESC'));
            foreach($data['previous_solutions'] as $solution)
            {
                $user_ids[$solution['user_created']]=$solution['user_created'];
            }
            $data['users']=System_helper::get_users_info($user_ids);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_ti_market_visit_solution/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$visit_id);
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
                $visit_id=$this->input->post('id');
            }
            else
            {
                $visit_id=$id;
            }
            $this->db->from($this->config->item('table_tm_market_visit_ti').' mvt');
            $this->db->select('mvt.*');
            $this->db->select('stmv.host_type,stmv.host_id,stmv.district_id,stmv.territory_id');
            $this->db->select('shift.name shift_name');

            $this->db->join($this->config->item('table_setup_tm_market_visit').' stmv','stmv.id = mvt.setup_id','INNER');
            $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = stmv.shift_id','INNER');
            $this->db->where('mvt.id',$visit_id);
            $data['visit']=$this->db->get()->row_array();
            if(!$data['visit'])
            {
                System_helper::invalid_try("Invalid try at edit",$visit_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['districts']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$data['visit']['territory_id']),0,0,array('ordering'));
            $data['district']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('id ='.$data['visit']['district_id']),1);
            $data['visit']['customer_name']='';
            if($data['visit']['host_type']==$this->config->item('system_host_type_customer'))
            {
                $this->db->from($this->config->item('table_csetup_customers').' cus');
                $this->db->select('cus.id value,CONCAT(cus.customer_code," - ",cus.name) text,cus.status');
                $this->db->where('cus.id',$data['visit']['host_id']);
                $result=$this->db->get()->row_array();
                $data['visit']['customer_name']=$result['text'];
                if($result['status']!=$this->config->item('system_status_active'))
                {
                    $data['visit']['customer_name'].= '('.$result['status'].')';
                }
            }
            elseif($data['visit']['host_type']==$this->config->item('system_host_type_other_customer'))
            {
                $this->db->from($this->config->item('table_csetup_other_customers').' cus');
                $this->db->select('cus.id value,cus.name text,cus.status');
                $this->db->where('cus.id',$data['visit']['host_id']);
                $result=$this->db->get()->row_array();
                $data['visit']['customer_name']=$result['text'];
                if($result['status']!=$this->config->item('system_status_active'))
                {
                    $data['visit']['customer_name'].= '('.$result['status'].')';
                }
            }
            $user_ids=array();
            $user_ids[$data['visit']['user_created']]=$data['visit']['user_created'];
            $data['previous_solutions']=Query_helper::get_info($this->config->item('table_tm_market_visit_solution_ti'),'*',array('visit_id ='.$visit_id),0,0,array('date_created DESC'));
            foreach($data['previous_solutions'] as $solution)
            {
                $user_ids[$solution['user_created']]=$solution['user_created'];
            }
            $data['users']=System_helper::get_users_info($user_ids);
            $data['title']='TI Market Visit Solution(Details)';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_ti_market_visit_solution/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$visit_id);
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
        $visit_id = $this->input->post("visit_id");
        $solution = $this->input->post("solution");
        $user = User_helper::get_user();
        $time=time();
        $this->db->trans_start();  //DB Transaction Handle START
        if((isset($this->permissions['edit'])&&($this->permissions['edit']==1))||(isset($this->permissions['add'])&&($this->permissions['add']==1)))
        {
            if(strlen($solution)>0)
            {
                $data=array();
                $data['visit_id']=$visit_id;
                $data['solution']=$solution;
                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                Query_helper::add($this->config->item('table_tm_market_visit_solution_ti'),$data);
            }
        }
        //may need for previous solution edit if it has edit permission not done
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
    public function get_items()
    {
        $current_records = $this->input->post('total_records');
        if(!$current_records)
        {
            $current_records=0;
        }
        $pagesize = $this->input->post('pagesize');
        if(!$pagesize)
        {
            $pagesize=40;
        }
        else
        {
            $pagesize=$pagesize*2;
        }
        $this->db->from($this->config->item('table_tm_market_visit_ti').' mvt');
        $this->db->select('mvt.*');
        $this->db->select('stmv.host_type,stmv.host_id');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->select('shift.name shift_name');
        $this->db->select('dd.name special_district_name');

        $this->db->select('cus.name customer_name,cus.status cus_status');
        $this->db->select('ocus.name ocustomer_name,ocus.status ocus_status');

        $this->db->select('count(mvst.id) total_solution',false);

        $this->db->join($this->config->item('table_setup_tm_market_visit').' stmv','stmv.id = mvt.setup_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = stmv.district_id','INNER');

        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = stmv.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = stmv.shift_id','INNER');

        $this->db->join($this->config->item('table_setup_location_districts').' dd','dd.id = mvt.special_district_id','LEFT');
        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = stmv.host_id','LEFT');
        $this->db->join($this->config->item('table_csetup_other_customers').' ocus','cus.id = stmv.host_id','LEFT');
        $this->db->join($this->config->item('table_tm_market_visit_solution_ti').' mvst','mvt.id = mvst.visit_id','LEFT');
        if($this->locations['division_id']>0)
        {
            $this->db->where('division.id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('zone.id',$this->locations['zone_id']);
                if($this->locations['territory_id']>0)
                {
                    $this->db->where('t.id',$this->locations['territory_id']);
                }
            }
        }
        $this->db->group_by('mvt.id');
        $this->db->order_by('mvt.id DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['day']=date('l',$item['date']);
            $item['date']=System_helper::display_date($item['date']);
            if($item['host_type']==$this->config->item('system_host_type_customer'))
            {
                if($item['cus_status']!=$this->config->item('system_status_active'))
                {
                    $item['customer_name'].= '('.$item['cus_status'].')';
                }
            }
            elseif($item['host_type']==$this->config->item('system_host_type_other_customer'))
            {

                $item['customer_name']=$item['ocustomer_name'];
                if($item['ocus_status']!=$this->config->item('system_status_active'))
                {
                    $item['customer_name'].= '('.$item['ocus_status'].')';
                }
            }
            elseif($item['host_type']==$this->config->item('system_host_type_special'))
            {
                $item['customer_name']=$item['title'];
                $item['district_name']=$item['special_district_name'];
            }
        }
        //$items=$this->db->get()->result_array();
        $this->jsonReturn($items);

    }

}
