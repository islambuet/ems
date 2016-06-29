<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tm_zi_market_visit_solution extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_zi_market_visit_solution');
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
        $this->controller_url='tm_zi_market_visit_solution';
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
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_zi_market_visit_solution/list",$data,true));
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
        $this->db->from($this->config->item('table_tm_market_visit_zi').' mvzi');
        $this->db->select('mvzi.setup_details_id id,mvzi.title');
        $this->db->select('mvszid.date,mvszid.host_type');
        $this->db->select('CONCAT(cus.customer_code," - ",cus.name) customer_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->select('shift.name shift_name');
        $this->db->select('count(mvsolzi.id) total_solution',false);
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = mvzi.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = mvzi.territory_id','INNER');

        $this->db->join($this->config->item('table_setup_tm_market_visit_zi_details').' mvszid','mvszid.id = mvzi.setup_details_id','INNER');
        $this->db->join($this->config->item('table_setup_tm_market_visit_zi').' mvszi','mvszi.id = mvszid.setup_id','INNER');

        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = mvszi.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = mvszid.shift_id','INNER');
        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvszid.host_id and mvszid.host_type ="'.$this->config->item('system_host_type_customer').'"','LEFT');
        $this->db->join($this->config->item('table_tm_market_visit_solution_zi').' mvsolzi','mvzi.setup_details_id = mvsolzi.setup_details_id','LEFT');
        if($this->locations['division_id']>0)
        {
            $this->db->where('division.id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('zone.id',$this->locations['zone_id']);
            }
        }
        $this->db->group_by('mvzi.setup_details_id');
        $this->db->order_by('mvzi.id DESC');

        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['day']=date('l',$item['date']);
            $item['date']=System_helper::display_date($item['date']);
            if($item['host_type']==$this->config->item('system_host_type_special'))
            {
                $item['customer_name']=$item['title'];
            }
        }
        $this->jsonReturn($items);
        //$this->jsonReturn(array());

    }
    private function system_edit($id)
    {
        if((isset($this->permissions['edit'])&&($this->permissions['edit']==1))||(isset($this->permissions['add'])&&($this->permissions['add']==1)))
        {
            if(($this->input->post('id')))
            {
                $setup_details_id=$this->input->post('id');
            }
            else
            {
                $setup_details_id=$id;
            }
            $this->db->from($this->config->item('table_tm_market_visit_zi').' mvzi');
            $this->db->select('mvzi.*');
            $this->db->select('d.name district_name');
            $this->db->select('t.name territory_name');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = mvzi.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = mvzi.territory_id','INNER');
            $this->db->where('mvzi.setup_details_id',$setup_details_id);
            $data['visit']=$this->db->get()->row_array();
            if(!$data['visit'])
            {
                System_helper::invalid_try("Invalid try at edit",$setup_details_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $this->db->from($this->config->item('table_setup_tm_market_visit_zi_details').' mvzid');
            $this->db->select('mvzid.date date,mvzid.host_type,mvzid.setup_id');
            $this->db->select('shift.name shift_name');
            $this->db->select('cus.id customer_id');
            $this->db->select('CONCAT(cus.customer_code," - ",cus.name) customer_name');

            $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = mvzid.shift_id','INNER');
            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvzid.host_id and mvzid.host_type ="'.$this->config->item('system_host_type_customer').'"','LEFT');
            $this->db->where('mvzid.id',$setup_details_id);
            $result=$this->db->get()->row_array();
            if(!$result)
            {
                System_helper::invalid_try("Try to use non-existing",$setup_details_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $setup_id=$result['setup_id'];
            $data['visit']['date']=$result['date'];
            $data['visit']['shift_name']=$result['shift_name'];
            $data['visit']['customer_name']=$result['customer_name'];
            $data['visit']['host_type']=$result['host_type'];

            $this->db->from($this->config->item('table_setup_tm_market_visit_zi').' mvzi');
            $this->db->select('zone.id zone_id,zone.name zone_name');
            $this->db->select('division.id division_id,division.name division_name');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = mvzi.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->where('mvzi.status_approve',$this->config->item('system_status_approved'));
            $this->db->where('mvzi.id',$setup_id);
            $result=$this->db->get()->row_array();
            if(!$result)
            {
                System_helper::invalid_try("Try to use Non approval or not existing setup",$setup_details_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['visit']['division_name']=$result['division_name'];
            $data['visit']['division_id']=$result['division_id'];
            $data['visit']['zone_id']=$result['zone_id'];
            $data['visit']['zone_name']=$result['zone_name'];

            $data['title']='Visit Solution';
            $user_ids=array();
            $user_ids[$data['visit']['user_created']]=$data['visit']['user_created'];
            $data['previous_solutions']=Query_helper::get_info($this->config->item('table_tm_market_visit_solution_zi'),'*',array('setup_details_id ='.$setup_details_id),0,0,array('date_created DESC'));
            foreach($data['previous_solutions'] as $solution)
            {
                $user_ids[$solution['user_created']]=$solution['user_created'];
            }
            $data['users']=System_helper::get_users_info($user_ids);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_zi_market_visit_solution/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$setup_details_id);
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
                $setup_details_id=$this->input->post('id');
            }
            else
            {
                $setup_details_id=$id;
            }
            $this->db->from($this->config->item('table_tm_market_visit_zi').' mvzi');
            $this->db->select('mvzi.*');
            $this->db->select('d.name district_name');
            $this->db->select('t.name territory_name');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = mvzi.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = mvzi.territory_id','INNER');
            $this->db->where('mvzi.setup_details_id',$setup_details_id);
            $data['visit']=$this->db->get()->row_array();
            if(!$data['visit'])
            {
                System_helper::invalid_try("Invalid try at edit",$setup_details_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $this->db->from($this->config->item('table_setup_tm_market_visit_zi_details').' mvzid');
            $this->db->select('mvzid.date date,mvzid.host_type,mvzid.setup_id');
            $this->db->select('shift.name shift_name');
            $this->db->select('cus.id customer_id');
            $this->db->select('CONCAT(cus.customer_code," - ",cus.name) customer_name');

            $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = mvzid.shift_id','INNER');
            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvzid.host_id and mvzid.host_type ="'.$this->config->item('system_host_type_customer').'"','LEFT');
            $this->db->where('mvzid.id',$setup_details_id);
            $result=$this->db->get()->row_array();
            if(!$result)
            {
                System_helper::invalid_try("Try to use non-existing",$setup_details_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $setup_id=$result['setup_id'];
            $data['visit']['date']=$result['date'];
            $data['visit']['shift_name']=$result['shift_name'];
            $data['visit']['customer_name']=$result['customer_name'];
            $data['visit']['host_type']=$result['host_type'];

            $this->db->from($this->config->item('table_setup_tm_market_visit_zi').' mvzi');
            $this->db->select('zone.id zone_id,zone.name zone_name');
            $this->db->select('division.id division_id,division.name division_name');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = mvzi.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->where('mvzi.status_approve',$this->config->item('system_status_approved'));
            $this->db->where('mvzi.id',$setup_id);
            $result=$this->db->get()->row_array();
            if(!$result)
            {
                System_helper::invalid_try("Try to use Non approval or not existing setup",$setup_details_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['visit']['division_name']=$result['division_name'];
            $data['visit']['division_id']=$result['division_id'];
            $data['visit']['zone_id']=$result['zone_id'];
            $data['visit']['zone_name']=$result['zone_name'];

            $data['title']='Visit Details';
            $user_ids=array();
            $user_ids[$data['visit']['user_created']]=$data['visit']['user_created'];
            $data['previous_solutions']=Query_helper::get_info($this->config->item('table_tm_market_visit_solution_zi'),'*',array('setup_details_id ='.$setup_details_id),0,0,array('date_created DESC'));
            foreach($data['previous_solutions'] as $solution)
            {
                $user_ids[$solution['user_created']]=$solution['user_created'];
            }
            $data['users']=System_helper::get_users_info($user_ids);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_zi_market_visit_solution/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$setup_details_id);
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
        $setup_details_id = $this->input->post("id");
        $solution = $this->input->post("solution");
        if(!(strlen($solution)>0))
        {
            $ajax['status']=false;
            $ajax['system_message']="solution cannot be empty";
            $this->jsonReturn($ajax);
        }
        $user = User_helper::get_user();
        $time=time();
        $this->db->trans_start();  //DB Transaction Handle START
        if((isset($this->permissions['edit'])&&($this->permissions['edit']==1))||(isset($this->permissions['add'])&&($this->permissions['add']==1)))
        {

            $data=array();
            $data['setup_details_id']=$setup_details_id;
            $data['status_read_zi']=$this->config->item('system_status_no');
            $data['solution']=$solution;
            $data['user_created'] = $user->user_id;
            $data['date_created'] = $time;
            Query_helper::add($this->config->item('table_tm_market_visit_solution_zi'),$data);

            $data=array();
            $data['status_read_di']=$this->config->item('system_status_yes');
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = $time;
            Query_helper::update($this->config->item('table_tm_market_visit_zi'),$data,array("setup_details_id = ".$setup_details_id));

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


}
