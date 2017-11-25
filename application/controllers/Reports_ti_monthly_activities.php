<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_ti_monthly_activities extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_ti_monthly_activities');
        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->jsonReturn($ajax);
        }
        $this->controller_url='reports_ti_monthly_activities';
    }

    public function index($action="search",$id=0)
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="list")
        {
            $this->system_list();
        }elseif($action=="get_items")
        {
            $this->system_get_items();
        }elseif($action=="get_items_all_area")
        {
            $this->system_get_items_all_area();
        }
        elseif($action=="details")
        {
            $this->system_details($id);
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
            $data['title']="TI Monthly Activities Report";
            $ajax['status']=true;
            $user = User_helper::get_user();
            $user_id=$user->user_id;
            $db_login=$this->load->database('armalik_login',TRUE);
            $db_login->from($this->config->item('table_setup_user').' user');
            $db_login->select('user.id,user.employee_id,user.user_name,user.status');
            $db_login->select('user_info.name,user_info.ordering');
            $db_login->select('designation.name designation_name');
            $db_login->join($this->config->item('table_setup_user_info').' user_info','user.id = user_info.user_id AND user_info.revision = 1','INNER');
            $db_login->join($this->config->item('table_setup_designation').' designation','designation.id = user_info.designation','LEFT');
            $db_login->where('user.status',$this->config->item('system_status_active'));
            $db_login->order_by('user_info.ordering','ASC');
            if($user->user_group!=1)
            {
                $db_login->where('user_info.user_group !=',1);
            }
            $results=$db_login->get()->result_array();
            //print_r($results);exit;
            $all_user=array();
            foreach($results as $result)
            {
                $all_user[$result['id']]=$result;
            }
            $this->db->from($this->config->item('table_system_assigned_area').' aa');
            $this->db->select('aa.*');
//            if($user->user_group!=1 && $user->user_group!=2)
//            {
                if($this->locations['division_id']>0)
                {
                    $this->db->where('aa.division_id',$this->locations['division_id']);
                    if($this->locations['zone_id']>0)
                    {
                        $this->db->where('aa.zone_id',$this->locations['zone_id']);
                        if($this->locations['territory_id']>0)
                        {
                            $this->db->where('aa.territory_id',$this->locations['territory_id']);
                        }
                    }
                }
//            }
            $this->db->where('aa.revision',1);
            $user_info=$this->db->get()->result_array();
            $assigned_users_info=array();
            foreach($user_info as &$user)
            {
                if(isset($all_user[$user['user_id']]))
                {
                    $user['value']=$all_user[$user['user_id']]['id'];
                    $user['employee_id']=$all_user[$user['user_id']]['employee_id'];
                    $user['text']=$all_user[$user['user_id']]['employee_id'].'-'.$all_user[$user['user_id']]['name'].' ('.$all_user[$user['user_id']]['designation_name'].')';
                    $user['designation_name']=$all_user[$user['user_id']]['designation_name'];
                    $assigned_users_info[]=$user;
                }
            }
            //division and zone
            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['area_info']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id']));
                    if($this->locations['territory_id']>0)
                    {
                        $data['area_info']=Query_helper::get_info($this->config->item('table_tm_monthly_activities_area_setup_ti'),array('id value','area_name text'),array('employee_info_id ='.$user_id,'status !="'.$this->config->item('system_status_delete').'"'));
                    }
                }
            }
            $data['fiscal_years']=Query_helper::get_info($this->config->item('table_basic_setup_fiscal_year'),array('id value','name text','date_start','date_end'),array('status ="'.$this->config->item('system_status_active').'"'));

            $month=array();
            for($i=1;$i<13;$i++)
            {
                $month[$i]=date('F', mktime(0, 0, 0, $i, 1));
            }
            $data['month']=$month;
            $data['user_info']=$assigned_users_info;
            $data['user_counter']=count($data['user_info']);
            $data['date_start']=System_helper::display_date(time());
            $data['date_end']=System_helper::display_date(time());
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/search",$data,true));
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
            $reports=$this->input->post('report');
            //print_r($reports);exit;
            if(!($reports['user_id']>0))
            {
                $ajax['status']=false;
                $ajax['system_message']='Please select a employee';
                $this->jsonReturn($ajax);
            }
            if(!($reports['year_id']>0))
            {
                $ajax['status']=false;
                $ajax['system_message']='Please select a Fiscal Year';
                $this->jsonReturn($ajax);
            }
            if(!($reports['area_id']) && !($reports['month_id']))
            {
                $ajax['status']=false;
                $ajax['system_message']='Please Select a Area Or a Month';
                $this->jsonReturn($ajax);
            }
            $data['options']=$reports;
            $data['area_name']=Query_helper::get_info($this->config->item('table_tm_monthly_activities_area_setup_ti'),'*',array('id ='.$data['options']['area_id']),1);
            $db_login=$this->load->database('armalik_login',TRUE);
            $db_login->from($this->config->item('table_setup_user').' user');
            $db_login->select('user.employee_id');
            $db_login->select('user_info.name');
            $db_login->select('designation.name designation_name');
            $db_login->join($this->config->item('table_setup_user_info').' user_info','user.id = user_info.user_id','INNER');
            $db_login->join($this->config->item('table_setup_designation').' designation','designation.id = user_info.designation','LEFT');
            $db_login->where('user_info.revision',1);
            $db_login->where('user.status',$this->config->item('system_status_active'));
            $db_login->where('user.id',$reports['user_id']);

            $results=$db_login->get()->row_array();
            $data['employee_info']=$results;
            $ajax['status']=true;
            $data['title']="Monthly Activities Report";

            if(($reports['area_id'] && $reports['month_id']) || $reports['area_id'])
            {
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));
            }
            elseif($reports['month_id'])
            {
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_for_all_area",$data,true));
            }
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

    private function system_get_items()
    {
        $user = User_helper::get_user();
        $user_id=$this->input->post('user_id');
        $area_id=$this->input->post('area_id');
        $year_id=$this->input->post('year_id');
        $results=Query_helper::get_info($this->config->item('table_tm_monthly_activities_ti'),'*',array('area_id ='.$area_id,'fiscal_year_id ='.$year_id));
        $monthly_activities=array();
        foreach($results as $result)
        {
            $monthly_activities[$result['month_id']]=$result;
        }
        $items=array();
        for($i=1;$i<13;$i++)
        {
            if(isset($monthly_activities[$i]))
            {
                $item['id']=$monthly_activities[$i]['id'];
                $item['month_id']=date('F', mktime(0, 0, 0, $i, 1));
                $item['achievement']=nl2br($monthly_activities[$i]['achievement']);
                $item['work_done']=nl2br($monthly_activities[$i]['work_done']);
                $item['next_month_crop_variety']=nl2br($monthly_activities[$i]['next_month_crop_variety']);
                $item['amount_self_target']=nl2br($monthly_activities[$i]['amount_self_target']);
                $item['reason_self_target']=nl2br($monthly_activities[$i]['reason_self_target']);
                $item['value_marking']=nl2br($monthly_activities[$i]['value_marking']);
                $item['reason_marking']=nl2br($monthly_activities[$i]['reason_marking']);
                $items[]=$item;
            }
            else
            {
                $item['id']='id_'.$i;
                $item['month_id']=date('F', mktime(0, 0, 0, $i, 1));
                $item['achievement']='-';
                $item['work_done']='-';
                $item['next_month_crop_variety']='-';
                $item['amount_self_target']='-';
                $item['reason_self_target']='-';
                $item['value_marking']='-';
                $item['reason_marking']='-';
                $items[]=$item;
            }
        }
        $this->jsonReturn($items);
    }

    private function system_get_items_all_area()
    {
        $user = User_helper::get_user();
        $user_id=$this->input->post('user_id');
        $month_id=$this->input->post('month_id');
        $year_id=$this->input->post('year_id');
        $results_area_setup=Query_helper::get_info($this->config->item('table_tm_monthly_activities_area_setup_ti'),'*',array('employee_info_id ='.$user_id,'status !="'.$this->config->item('system_status_delete').'"'));
        $this->db->from($this->config->item('table_tm_monthly_activities_ti').' monthly_activities');
        $this->db->select('monthly_activities.*');
        $this->db->select('as.area_name');
        $this->db->join($this->config->item('table_tm_monthly_activities_area_setup_ti').' as','as.id = monthly_activities.area_id','INNER');
        $this->db->where('as.employee_info_id',$user_id);
        $this->db->where('monthly_activities.month_id',$month_id);
        $this->db->where('monthly_activities.fiscal_year_id',$year_id);
        $results_monthly_activities=$this->db->get()->result_array();
        $monthly_activities=array();
        foreach($results_monthly_activities as $result_activity)
        {
            $monthly_activities[$result_activity['area_id']]=$result_activity;
        }
        $items=array();
        foreach($results_area_setup as $key=>$result)
        {
            if(isset($monthly_activities[$result['id']]))
            {
                $item['id']=$monthly_activities[$result['id']]['id'];
                $item['area_id']=$monthly_activities[$result['id']]['area_name'];
                $item['achievement']=$monthly_activities[$result['id']]['achievement'];
                $item['work_done']=$monthly_activities[$result['id']]['work_done'];
                $item['next_month_crop_variety']=$monthly_activities[$result['id']]['next_month_crop_variety'];
                $item['amount_self_target']=$monthly_activities[$result['id']]['amount_self_target'];
                $item['reason_self_target']=$monthly_activities[$result['id']]['reason_self_target'];
                $item['value_marking']=$monthly_activities[$result['id']]['value_marking'];
                $item['reason_marking']=$monthly_activities[$result['id']]['reason_marking'];
                $items[]=$item;
            }
            else
            {
                $item['id']='id_'.$key;
                $item['area_id']=$result['area_name'];
                $item['achievement']='-';
                $item['work_done']='-';
                $item['next_month_crop_variety']='-';
                $item['amount_self_target']='-';
                $item['reason_self_target']='-';
                $item['value_marking']='-';
                $item['reason_marking']='-';
                $items[]=$item;
            }
        }
        $this->jsonReturn($items);
    }

    private function system_details($id)
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            $html_container_id=$this->input->post('html_container_id');
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }
            $this->db->from($this->config->item('table_tm_monthly_activities_ti').' monthly_activities');
            $this->db->select('monthly_activities.*');
            $this->db->select('as.area_name');
            $this->db->join($this->config->item('table_tm_monthly_activities_area_setup_ti').' as','as.id = monthly_activities.area_id','INNER');
            $this->db->where('monthly_activities.id',$item_id);
            $data['item_details']=$this->db->get()->row_array();
            $data['title']="Monthly Activities (".$data['item_details']['area_name'].")";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view($this->controller_url."/details",$data,true));
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

    public function get_employee_area_list()
    {
        $user_id=$this->input->post('user_id');
        $html_container_id='#area_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_tm_monthly_activities_area_setup_ti'),array('id value','area_name text'),array('employee_info_id ='.$user_id,'status !="'.$this->config->item('system_status_delete').'"'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));
        $this->jsonReturn($ajax);
    }
    public function get_employee_info_list()
    {
        $html_container_id='#employee_info_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }

        $user = User_helper::get_user();
        $db_login=$this->load->database('armalik_login',TRUE);
        $db_login->from($this->config->item('table_setup_user').' user');
        $db_login->select('user.id,user.employee_id,user.user_name,user.status');
        $db_login->select('user_info.name,user_info.ordering');
        $db_login->select('designation.name designation_name');
        $db_login->join($this->config->item('table_setup_user_info').' user_info','user.id = user_info.user_id','INNER');
        $db_login->join($this->config->item('table_setup_designation').' designation','designation.id = user_info.designation','LEFT');
        $db_login->where('user_info.revision',1);
        $db_login->where('user.status!=',$this->config->item('system_status_inactive'));
        $db_login->order_by('user_info.ordering','ASC');
        if($user->user_group!=1)
        {
            $db_login->where('user_info.user_group !=',1);
        }
        $results=$db_login->get()->result_array();
        $all_user=array();
        foreach($results as $result)
        {
            $all_user[$result['id']]=$result;
        }
        $this->db->from($this->config->item('table_system_assigned_area').' aa');
        $this->db->select('aa.*');
        if($user->user_group!=1 && $user->user_group!=2)
        {
            if($this->locations['division_id']>0)
            {
                $this->db->where('aa.division_id',$this->locations['division_id']);
                if($this->locations['zone_id']>0)
                {
                    $this->db->where('aa.zone_id',$this->locations['zone_id']);
                    if($this->locations['territory_id']>0)
                    {
                        $this->db->where('aa.territory_id',$this->locations['territory_id']);
                    }
                }
            }
        }
        $this->db->where('aa.revision',1);
        $user_info=$this->db->get()->result_array();
        $assigned_users_info=array();
        foreach($user_info as &$user)
        {
            if(isset($all_user[$user['user_id']]))
            {
                $user['value']=$all_user[$user['user_id']]['id'];
                $user['employee_id']=$all_user[$user['user_id']]['employee_id'];
                $user['text']=$all_user[$user['user_id']]['employee_id'].'-'.$all_user[$user['user_id']]['name'].' ('.$all_user[$user['user_id']]['designation_name'].')';
                $user['designation_name']=$all_user[$user['user_id']]['designation_name'];
                $assigned_users_info[]=$user;
            }
        }
        $data['items']=$assigned_users_info;
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }
}
