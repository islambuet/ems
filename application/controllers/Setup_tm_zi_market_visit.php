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
            $this->system_list();
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
            $this->get_schedule();
        }
        /*elseif($action=="details")
        {
            $this->system_details($id);
        }*/
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_list();
        }
    }

    private function system_list()
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            $data['title']="ZI Market Visit Setup List";
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
    public function get_items()
    {

        $this->db->from($this->config->item('table_setup_tm_market_visit_zi').' stmv');
        $this->db->select('stmv.*');
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
        $this->db->order_by('stmv.year','DESC');
        $this->db->order_by('stmv.month','DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['month']=date('F',mktime(0, 0, 0,  $item['month'],1, $item['year']));
        }
        $this->jsonReturn($items);
    }

    private function system_add()
    {
        if(isset($this->permissions['add'])&&($this->permissions['add']==1))
        {
            $data['title']="ZI Market Visit Setup";
            $data["setup"] = Array(
                'division_id'=>$this->locations['division_id'],
                'zone_id'=>$this->locations['zone_id'],
                'year'=>date('Y'),
                'month'=>''
            );
            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
            }
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_tm_zi_market_visit/search",$data,true));
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

        if(((isset($this->permissions['add'])&&($this->permissions['add']==1))||(isset($this->permissions['edit'])&&($this->permissions['edit']==1))))
        {
            if(($this->input->post('id')))
            {
                $setup_id=$this->input->post('id');
            }
            else
            {
                $setup_id=$id;
            }
            $this->db->from($this->config->item('table_setup_tm_market_visit_zi').' stmv');
            $this->db->select('stmv.*');
            $this->db->select('zone.division_id division_id');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = stmv.zone_id','INNER');
            $this->db->where('stmv.id',$setup_id);
            $data['setup']=$this->db->get()->row_array();

            if(!$data['setup'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$data['setup']['division_id']));


            $data['title']="ZI Market Visit Setup";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("setup_tm_zi_market_visit/search",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$setup_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }
    private function get_schedule()
    {
        $zone_id=$this->input->post('zone_id');
        $year=$this->input->post('year');
        $month=$this->input->post('month');
        $info=Query_helper::get_info($this->config->item('table_setup_tm_market_visit_zi'),'*',array('zone_id ='.$zone_id,'year ='.$year,'month ='.$month),1);
        if($info)
        {
            $setup_id=$info['id'];
            if($info['status_approve']!=$this->config->item('system_status_pending'))
            {
                if(!(isset($this->permissions['edit'])&&($this->permissions['edit']==1)))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Already '.$info['status_approve'];
                    $this->jsonReturn($ajax);
                    die();
                }

            }
        }

        $data['title']="ZI Visit Schedule";
        $data['zone_id']=$zone_id;
        $data['year']=$year;
        $data['month']=$month;
        $data['shifts']=Query_helper::get_info($this->config->item('table_setup_tm_shifts'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

        $this->db->from($this->config->item('table_csetup_customers').' cus');
        $this->db->select('cus.id customer_id');
        $this->db->select('CONCAT(cus.customer_code," - ",cus.name) customer_name');
        $this->db->select('d.id district_id,d.name district_name');
        $this->db->select('t.id territory_id,t.name territory_name');

        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->where('t.zone_id',$zone_id);
        $this->db->order_by('t.ordering','ASC');
        $this->db->order_by('d.ordering','ASC');
        $this->db->order_by('cus.ordering','ASC');
        $this->db->where('cus.status !=',$this->config->item('system_status_delete'));
        $results=$this->db->get()->result_array();//customers
        $zone_details=array();
        foreach($results as $result)
        {
            $zone_details[$result['territory_id']]['territory_id']=$result['territory_id'];
            $zone_details[$result['territory_id']]['territory_name']=$result['territory_name'];

            $zone_details[$result['territory_id']]['districts'][$result['district_id']]['district_id']=$result['district_id'];
            $zone_details[$result['territory_id']]['districts'][$result['district_id']]['district_name']=$result['district_name'];
            $zone_details[$result['territory_id']]['districts'][$result['district_id']]['customers'][$result['customer_id']]['customer_id']=$result['customer_id'];
            $zone_details[$result['territory_id']]['districts'][$result['district_id']]['customers'][$result['customer_id']]['customer_name']=$result['customer_name'];
        }
        $data['zone_details']=$zone_details;


        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("setup_tm_zi_market_visit/add_edit",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->jsonReturn($ajax);

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
        $zone_id=$this->input->post('zone_id');
        $year=$this->input->post('year');
        $month=$this->input->post('month');
        $setup_id=0;
        $info=Query_helper::get_info($this->config->item('table_setup_tm_market_visit_zi'),'*',array('zone_id ='.$zone_id,'year ='.$year,'month ='.$month),1);
        if($info)
        {
            $setup_id=$info['id'];
            if($info['status_approve']!=$this->config->item('system_status_pending'))
            {
                if(!(isset($this->permissions['edit'])&&($this->permissions['edit']==1)))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Already '.$info['status_approve'];
                    $this->jsonReturn($ajax);
                    die();
                }

            }
        }
        if(!((isset($this->permissions['add'])&&($this->permissions['add']==1))||(isset($this->permissions['edit'])&&($this->permissions['edit']==1))))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
            die();
        }
        $time=time();
        $user = User_helper::get_user();
        $this->db->trans_start();  //DB Transaction Handle START
        if($setup_id==0)
        {
            $data=array();
            $data['zone_id']=$zone_id;
            $data['year']=$year;
            $data['month']=$month;
            $data['status_approve']=$this->config->item('system_status_pending');
            $data['user_created'] = $user->user_id;
            $data['date_created'] =$time;
            $id=Query_helper::add($this->config->item('table_setup_tm_market_visit_zi'),$data);
            if($id===false)
            {
                $this->db->trans_complete();
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->jsonReturn($ajax);
                die();
            }
            else
            {
                $setup_id=$id;
            }
        }
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
        /*$customers=$this->input->post('customers');
        if(sizeof($customers)>0)
        {


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

        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']="No customer was selected";
            $this->jsonReturn($ajax);
        }*/
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



}
