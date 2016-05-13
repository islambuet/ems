<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tm_di_market_visit_solution extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_di_market_visit_solution');
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
        $this->controller_url='tm_di_market_visit_solution';
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
            $data['title']="DI Market Visit and Solution List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_di_market_visit_solution/list",$data,true));
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
            $this->db->from($this->config->item('table_tm_market_visit_di').' mvdi');

            $this->db->select('mvdi.*');
            $this->db->select('CONCAT(cus.customer_code," - ",cus.name) cus_name');
            $this->db->select('d.name district_name');
            $this->db->select('t.name territory_name');
            $this->db->select('zone.name zone_name');
            $this->db->select('division.name division_name');

            $this->db->select('count(mvsdi.id) total_solution',false);

            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = mvdi.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = mvdi.division_id','INNER');
            $this->db->join($this->config->item('table_tm_market_visit_solution_di').' mvsdi','mvdi.id = mvsdi.visit_id','LEFT');
            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvdi.customer_id','LEFT');
            $this->db->where('mvdi.id',$visit_id);
            $data['visit']=$this->db->get()->row_array();
            if(!$data['visit'])
            {
                System_helper::invalid_try("Invalid try at edit",$visit_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['title']='DI Market Visit Solution';
            $user_ids=array();
            $user_ids[$data['visit']['user_created']]=$data['visit']['user_created'];
            $data['previous_solutions']=Query_helper::get_info($this->config->item('table_tm_market_visit_solution_di'),'*',array('visit_id ='.$visit_id),0,0,array('date_created DESC'));
            foreach($data['previous_solutions'] as $solution)
            {
                $user_ids[$solution['user_created']]=$solution['user_created'];
            }
            $data['users']=System_helper::get_users_info($user_ids);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_di_market_visit_solution/add_edit",$data,true));
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
            $this->db->from($this->config->item('table_tm_market_visit_di').' mvdi');

            $this->db->select('mvdi.*');
            $this->db->select('CONCAT(cus.customer_code," - ",cus.name) cus_name');
            $this->db->select('d.name district_name');
            $this->db->select('t.name territory_name');
            $this->db->select('zone.name zone_name');
            $this->db->select('division.name division_name');

            $this->db->select('count(mvsdi.id) total_solution',false);

            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = mvdi.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = mvdi.division_id','INNER');
            $this->db->join($this->config->item('table_tm_market_visit_solution_di').' mvsdi','mvdi.id = mvsdi.visit_id','LEFT');
            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvdi.customer_id','LEFT');
            $this->db->where('mvdi.id',$visit_id);
            $data['visit']=$this->db->get()->row_array();
            if(!$data['visit'])
            {
                System_helper::invalid_try("Invalid try at edit",$visit_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['title']='DI Market Visit Solution Details';
            $user_ids=array();
            $user_ids[$data['visit']['user_created']]=$data['visit']['user_created'];
            $data['previous_solutions']=Query_helper::get_info($this->config->item('table_tm_market_visit_solution_di'),'*',array('visit_id ='.$visit_id),0,0,array('date_created DESC'));
            foreach($data['previous_solutions'] as $solution)
            {
                $user_ids[$solution['user_created']]=$solution['user_created'];
            }
            $data['users']=System_helper::get_users_info($user_ids);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_di_market_visit_solution/details",$data,true));
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
                Query_helper::add($this->config->item('table_tm_market_visit_solution_di'),$data);
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
        $this->db->from($this->config->item('table_tm_market_visit_di').' mvdi');

        $this->db->select('mvdi.*');
        $this->db->select('CONCAT(cus.customer_code," - ",cus.name) cus_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');

        $this->db->select('count(mvsdi.id) total_solution',false);

        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = mvdi.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = mvdi.division_id','INNER');
        $this->db->join($this->config->item('table_tm_market_visit_solution_di').' mvsdi','mvdi.id = mvsdi.visit_id','LEFT');
        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvdi.customer_id','LEFT');
        if($this->locations['division_id']>0)
        {
            $this->db->where('division.id',$this->locations['division_id']);
        }
        $this->db->order_by('mvdi.id DESC');
        $this->db->group_by('mvdi.id');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date']=System_helper::display_date($item['date']);
            if($item['customer_id']>0)
            {
                $item['customer_name']=$item['cus_name'];
            }
        }
        $this->jsonReturn($items);

    }

}
