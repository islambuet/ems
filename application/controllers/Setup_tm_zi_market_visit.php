<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_tm_zi_market_visit extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_tm_zi_market_visit');
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
        $this->controller_url='setup_tm_zi_market_visit';
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
        elseif($action=="get_schedule")
        {
            $this->get_schedule($id);
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
            $data['title']="Zonal In-Charge Market Visit Setup List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_tm_zi_market_visit/list",$data,true));
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
            $data['title']="Zonal In-Charge Market Visit Setup";
            $data["setup"] = Array(
                'division_id'=>$this->locations['division_id'],
                'zone_id'=>$this->locations['zone_id']
            );
            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
            }
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_tm_zi_market_visit/add_edit",$data,true));
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
                $zone_id=$this->input->post('id');
            }
            else
            {
                $zone_id=$id;
            }
            $this->db->from($this->config->item('table_setup_tm_market_visit_zi').' stmv');
            $this->db->select('stmv.zone_id zone_id');
            $this->db->select('zone.division_id division_id');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = stmv.zone_id','INNER');
            $this->db->group_by('zone.id');
            $this->db->where('stmv.zone_id',$zone_id);
            $data['setup']=$this->db->get()->row_array();

            if(!$data['setup'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$zone_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$data['setup']['division_id']));


            $data['title']="Zonal In-Charge Market Visit Setup";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_tm_zi_market_visit/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$zone_id);
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
                $zone_id=$this->input->post('id');
            }
            else
            {
                $zone_id=$id;
            }
            $this->db->from($this->config->item('table_setup_tm_market_visit_zi').' stmv');
            $this->db->select('zone.id zone_id,zone.name zone_name');
            $this->db->select('division.id division_id,division.name division_name');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = stmv.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->group_by('zone.id');
            $this->db->where('stmv.zone_id',$zone_id);
            $data['setup']=$this->db->get()->row_array();

            if(!$data['setup'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$zone_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['title']="Details of Zonal In-Charge Market Visit Setup";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_tm_zi_market_visit/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$zone_id);
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
        $zone_id = $this->input->post("zone_id");
        $time=time();
        $user = User_helper::get_user();
        $setup_exists=Query_helper::get_info($this->config->item('table_setup_tm_market_visit_zi'),'*',array('zone_id ='.$zone_id,'revision =1'),1);
        if($setup_exists)
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
        $customers=$this->input->post('customers');
        if(sizeof($customers)>0)
        {

            $this->db->trans_start();  //DB Transaction Handle START
            $this->db->where('zone_id',$zone_id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_setup_tm_market_visit_zi'));
            foreach($customers as $day_no=>$days)
            {
                foreach($days as $shift_id=>$day)
                {
                    foreach($day as $customer_id)
                    {
                        $data=array();
                        $data['zone_id']=$zone_id;
                        $data['day_no']=$day_no;
                        $data['shift_id']=$shift_id;
                        $data['shift_id']=$shift_id;
                        $data['customer_id']=$customer_id;
                        $data['revision']=1;
                        $data['user_created'] = $user->user_id;
                        $data['date_created'] =$time;
                        Query_helper::add($this->config->item('table_setup_tm_market_visit_zi'),$data);

                    }
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
        else
        {
            $ajax['status']=false;
            $ajax['system_message']="No customer was selected";
            $this->jsonReturn($ajax);
        }
    }
    private function get_schedule($id)
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            if(($this->input->post('zone_id')))
            {
                $zone_id=$this->input->post('zone_id');
            }
            else
            {
                $zone_id=$id;
            }
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("setup_tm_zi_market_visit/schedule",array('zone_id'=>$zone_id),true));
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
    public function get_districts()
    {
        $territory_id=$this->input->post('territory_id');
        $data['shift_id']=$this->input->post('shift_id');
        $data['day_no']=$this->input->post('day_no');
        $data['items']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$territory_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering DESC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>'#district_container_'.$data['day_no'].'_'.$data['shift_id'],"html"=>$this->load->view("setup_tm_zi_market_visit/districts",$data,true));
        $this->jsonReturn($ajax);
    }
    public function get_customers()
    {
        $district_id=$this->input->post('district_id');
        $data['shift_id']=$this->input->post('shift_id');
        $data['day_no']=$this->input->post('day_no');
        $data['items']=Query_helper::get_info($this->config->item('table_csetup_customers'),array('id value','CONCAT(customer_code," - ",name) text'),array('district_id ='.$district_id,'status ="'.$this->config->item('system_status_active').'"'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>'#customers_container_'.$data['day_no'].'_'.$data['shift_id'],"html"=>$this->load->view("setup_tm_ti_market_visit/customer_selection",$data,true));
        $this->jsonReturn($ajax);
    }
    public function get_items()
    {

        $this->db->from($this->config->item('table_setup_tm_market_visit_zi').' stmv');
        $this->db->select('zone.id id');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = stmv.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        if($this->locations['division_id']>0)
        {
            $this->db->where('division.id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('zone.id',$this->locations['zone_id']);
            }
        }
        $this->db->where('stmv.revision',1);
        $this->db->group_by('zone.id');
        $this->db->order_by('stmv.id','DESC');

        $items=$this->db->get()->result_array();

        $this->jsonReturn($items);
    }


}
