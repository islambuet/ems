<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_ti_daily_attendance extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_ti_daily_attendance');
        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->jsonReturn($ajax);
        }
        $this->controller_url='reports_ti_daily_attendance';
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
            $data['title']="TI Daily Attendance Report";
            $ajax['status']=true;
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
//            print_r($all_user);
//            exit;
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
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id']));

                }
            }

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
            if(!($reports['user_id']>0))
            {
                $ajax['status']=false;
                $ajax['system_message']='Please select a employee';
                $this->jsonReturn($ajax);
            }
            if(!($reports['date_start']))
            {
                $ajax['status']=false;
                $ajax['system_message']='Please Select a Starting Date';
                $this->jsonReturn($ajax);
            }
            if(!($reports['date_end']))
            {
                $ajax['status']=false;
                $ajax['system_message']='Please Select a Ending Date';
                $this->jsonReturn($ajax);
            }

            $reports['date_end']=System_helper::get_time($reports['date_end']);
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['date_end']>0)
            {
                $reports['date_end']=$reports['date_end']+3600*24-1;
            }

            $data['options']=$reports;

            $db_login=$this->load->database('armalik_login',TRUE);
            $db_login->from($this->config->item('table_setup_user').' user');
            $db_login->select('user.employee_id');
            $db_login->select('user_info.name');
            $db_login->select('designation.name designation_name');
            $db_login->join($this->config->item('table_setup_user_info').' user_info','user.id = user_info.user_id','INNER');
            $db_login->join($this->config->item('table_setup_designation').' designation','designation.id = user_info.designation','LEFT');
            $db_login->where('user_info.revision',1);
            $db_login->where('user.status!=',$this->config->item('system_status_inactive'));
            $db_login->where('user.id',$reports['user_id']);

            $results=$db_login->get()->row_array();
            $results['date_start']=System_helper::display_date($reports['date_start']);
            $results['date_end']=System_helper::display_date($reports['date_end']);
            $data['employee_info']=$results;
            $ajax['status']=true;
            $data['title']="Daily Attendance Report";
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url);
            $this->jsonReturn($ajax);
        }else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }

    private function system_get_items()
    {
        $user = User_helper::get_user();
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');
        $user_id=$this->input->post('user_id');
        $db_login=$this->load->database('armalik_login',TRUE);
        $db_login->from($this->config->item('table_setup_user').' user');
        $db_login->select('user.id,user.employee_id,user.user_name,user.status');
        $db_login->select('user_info.name,user_info.ordering,user_info.user_id');
        $db_login->select('designation.name designation_name');
        $db_login->join($this->config->item('table_setup_user_info').' user_info','user.id = user_info.user_id','INNER');
        $db_login->join($this->config->item('table_setup_designation').' designation','designation.id = user_info.designation','LEFT');
        $db_login->where('user_info.revision',1);
        $db_login->where('user.id',$user_id);
        $db_login->order_by('user_info.ordering','ASC');
        $user_name=$db_login->get()->row_array();
        $this->db->from($this->config->item('table_tm_daily_activities_ti').' daily_activities');
        $this->db->select('daily_activities.*');
        $this->db->select('aa.user_id');
        $this->db->select('t.name territory_name');
        $this->db->join($this->config->item('table_system_assigned_area').' aa','aa.user_id = daily_activities.user_started','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = aa.territory_id','LEFT');
        if($date_end>0)
        {
            $this->db->where('daily_activities.date_started <=',$date_end);
        }
        if($date_start>0)
        {
            $this->db->where('daily_activities.date_started >=',$date_start);
        }
        $this->db->where('daily_activities.status!=',$this->config->item('system_status_delete'));
        $this->db->where('daily_activities.user_started',$user_id);
        $this->db->order_by('daily_activities.id DESC');
        $this->db->group_by('daily_activities.id');
        $daily_activities=$this->db->get()->result_array();
        $daily_activities_list=array();
        foreach($daily_activities as &$activity)
        {
            $date_string=System_helper::display_date($activity['date_started']);
            $daily_activities_list[$date_string]['date_started']=$date_string;
            $daily_activities_list[$date_string]['id']=$activity['id'];
            $daily_activities_list[$date_string]['status']=$activity['status'];
            $daily_activities_list[$date_string]['date_started']=$activity['date_started'];
            $daily_activities_list[$date_string]['user_started']=$activity['user_started'];
            $daily_activities_list[$date_string]['date_updated_start']=$activity['date_updated_start'];
            $daily_activities_list[$date_string]['user_updated_start']=$activity['user_updated_start'];
            $daily_activities_list[$date_string]['date_reported']=$activity['date_reported'];
            $daily_activities_list[$date_string]['user_reported']=$activity['user_reported'];
            $daily_activities_list[$date_string]['date_updated_report']=$activity['date_updated_report'];
            $daily_activities_list[$date_string]['user_updated_report']=$activity['user_updated_report'];
            $daily_activities_list[$date_string]['zsc_comment']=$activity['zsc_comment'];
            $daily_activities_list[$date_string]['attendance']=$activity['attendance'];
            $daily_activities_list[$date_string]['date_attendance']=$activity['date_attendance'];
            $daily_activities_list[$date_string]['user_attendance']=$activity['user_attendance'];
            $daily_activities_list[$date_string]['date_updated_attendance']=$activity['date_updated_attendance'];
        }
        $date_diff = $date_end - $date_start;
        $day=ceil($date_diff / (60 * 60 * 24));
        $date_time=$date_start;
        $items=array();
        for($i=1;$i<=$day;$i++)
        {
            $date_string=System_helper::display_date($date_time);
            if(isset($daily_activities_list[$date_string]))
            {
                $item['id']=$daily_activities_list[$date_string]['id'];
                $item['date']=$date_string;
                $item['date_started']=System_helper::display_date_time($daily_activities_list[$date_string]['date_started']);
                $item['date_reported']=System_helper::display_date_time($daily_activities_list[$date_string]['date_reported']);
                $item['attendance']=$daily_activities_list[$date_string]['attendance'];
                $item['attendance_taken_time']=System_helper::display_date_time($daily_activities_list[$date_string]['date_attendance']);
                $items[]=$item;

            }else
            {

                $item['id']=$date_time;
                $item['date']=$date_string;
                $item['date_started']='-';
                $item['date_reported']='-';
                $item['attendance']='-';
                $item['attendance_taken_time']='-';
                //$id_number+=1;
                $items[]=$item;
            }
            $date_time=$date_time+86400;
        }
        $this->jsonReturn($items);
    }

    private function system_details($id)
    {
        if(isset($this->permissions['edit'])&&($this->permissions['edit']==1))
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
            $data['item']=Query_helper::get_info($this->config->item('table_tm_daily_activities_ti'),'*',array('id ='.$item_id),1);
            $data['item_details']=Query_helper::get_info($this->config->item('table_tm_daily_activities_ti_details'),'*',array('activities_id ='.$item_id,'status!="'.$this->config->item('system_status_delete').'"'));

            $data['title']="Daily Task Reporting";
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
