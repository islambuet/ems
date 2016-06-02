<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setup_tm_ti_market_visit extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Setup_tm_ti_market_visit');
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
        $this->controller_url='setup_tm_ti_market_visit';
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
            $data['title']="TI Market Visit Setup List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_tm_ti_market_visit/list",$data,true));
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
            $data['title']="TI Market Visit Setup (new)";
            $data["setup"] = Array(
                'division_id'=>$this->locations['division_id'],
                'zone_id'=>$this->locations['zone_id'],
                'territory_id'=>$this->locations['territory_id']
            );
            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id']));
                }
            }
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_tm_ti_market_visit/add_edit",$data,true));
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
                $territory_id=$this->input->post('id');
            }
            else
            {
                $territory_id=$id;
            }
            $this->db->from($this->config->item('table_setup_tm_market_visit').' stmv');
            $this->db->select('stmv.territory_id territory_id');
            $this->db->select('zone.id zone_id');
            $this->db->select('zone.division_id division_id');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = stmv.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->group_by('t.id');
            $this->db->where('stmv.territory_id',$territory_id);
            $data['setup']=$this->db->get()->row_array();

            if(!$data['setup'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$territory_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$data['setup']['division_id']));
            $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$data['setup']['zone_id']));

            $data['title']="TI Market Visit Setup (edit)";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_tm_ti_market_visit/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$territory_id);
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
                $territory_id=$this->input->post('id');
            }
            else
            {
                $territory_id=$id;
            }
            $this->db->from($this->config->item('table_setup_tm_market_visit').' stmv');
            $this->db->select('stmv.territory_id territory_id');
            $this->db->select('t.name territory_name');
            $this->db->select('zone.id zone_id,zone.name zone_name');
            $this->db->select('division.id division_id,division.name division_name');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = stmv.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->group_by('t.id');
            $this->db->where('stmv.territory_id',$territory_id);
            $data['setup']=$this->db->get()->row_array();

            if(!$data['setup'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$territory_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['title']="TI Market Visit Setup Details";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_tm_ti_market_visit/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$territory_id);
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

        $territory_id = $this->input->post("territory_id");
        $time=time();
        $user = User_helper::get_user();
        $setup_exists=Query_helper::get_info($this->config->item('table_setup_tm_market_visit'),'*',array('territory_id ='.$territory_id,'revision =1'),1);
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
        $data=$this->input->post('data');
        $this->db->trans_start();  //DB Transaction Handle START
        $this->db->where('territory_id',$territory_id);
        $this->db->set('revision', 'revision+1', FALSE);
        $this->db->update($this->config->item('table_setup_tm_market_visit'));
        foreach($data as $day_no=>$days)
        {
            foreach($days as $shift_id=>$day)
            {
                if($day['district_id']>0)
                {
                    if(isset($day['customer']))
                    {
                        foreach($day['customer'] as $host_id)
                        {
                            $data=array();
                            $data['territory_id']=$territory_id;
                            $data['day_no']=$day_no;
                            $data['shift_id']=$shift_id;
                            $data['district_id']=$day['district_id'];
                            $data['host_type']=$this->config->item('system_host_type_customer');
                            $data['host_id']=$host_id;
                            $data['revision']=1;
                            $data['user_created'] = $user->user_id;
                            $data['date_created'] =$time;
                            Query_helper::add($this->config->item('table_setup_tm_market_visit'),$data);
                        }
                    }
                    if(isset($day['other_customer']))
                    {
                        foreach($day['other_customer'] as $host_id)
                        {
                            $data=array();
                            $data['territory_id']=$territory_id;
                            $data['day_no']=$day_no;
                            $data['shift_id']=$shift_id;
                            $data['district_id']=$day['district_id'];
                            $data['host_type']=$this->config->item('system_host_type_other_customer');
                            $data['host_id']=$host_id;
                            $data['revision']=1;
                            $data['user_created'] = $user->user_id;
                            $data['date_created'] =$time;
                            Query_helper::add($this->config->item('table_setup_tm_market_visit'),$data);
                        }
                    }
                    if(isset($day['special'])&& $day['special']>0)
                    {
                        for($i=0;$i<$day['special'];$i++)
                        {
                            $data=array();
                            $data['territory_id']=$territory_id;
                            $data['day_no']=$day_no;
                            $data['shift_id']=$shift_id;
                            $data['district_id']=$day['district_id'];
                            $data['host_type']=$this->config->item('system_host_type_special');
                            $data['host_id']=($i+1);
                            $data['revision']=1;
                            $data['user_created'] = $user->user_id;
                            $data['date_created'] =$time;
                            Query_helper::add($this->config->item('table_setup_tm_market_visit'),$data);
                        }
                    }

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
    private function get_schedule($id)
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            if(($this->input->post('territory_id')))
            {
                $territory_id=$this->input->post('territory_id');
            }
            else
            {
                $territory_id=$id;
            }
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("setup_tm_ti_market_visit/schedule",array('territory_id'=>$territory_id),true));
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
    public function get_customers()
    {
        $district_id=$this->input->post('district_id');
        $data['shift_id']=$this->input->post('shift_id');
        $data['day_no']=$this->input->post('day_no');
        $data['items']=Query_helper::get_info($this->config->item('table_csetup_customers'),array('id value','CONCAT(customer_code," - ",name) text','status'),array('district_id ='.$district_id,'status !="'.$this->config->item('system_status_delete').'"'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>'#customers_container_'.$data['day_no'].'_'.$data['shift_id'],"html"=>$this->load->view("setup_tm_ti_market_visit/customer_selection",$data,true));
        $data['items']=Query_helper::get_info($this->config->item('table_csetup_other_customers'),array('id value','name text','status'),array('district_id ='.$district_id,'status !="'.$this->config->item('system_status_delete').'"'));
        $ajax['system_content'][]=array("id"=>'#other_customers_container_'.$data['day_no'].'_'.$data['shift_id'],"html"=>$this->load->view("setup_tm_ti_market_visit/other_customer_selection",$data,true));
        $ajax['system_content'][]=array("id"=>'#special_container_'.$data['day_no'].'_'.$data['shift_id'],"html"=>$this->load->view("setup_tm_ti_market_visit/special_input",$data,true));
        $this->jsonReturn($ajax);
    }
    public function get_items()
    {

        $this->db->from($this->config->item('table_setup_tm_market_visit').' stmv');
        $this->db->select('t.id id');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = stmv.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
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
        $this->db->where('stmv.revision',1);
        $this->db->group_by('t.id');
        $this->db->order_by('stmv.id','DESC');

        $items=$this->db->get()->result_array();

        $this->jsonReturn($items);
    }


}
