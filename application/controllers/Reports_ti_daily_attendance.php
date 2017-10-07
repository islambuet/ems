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
            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['customers']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id']));
                }
            }
            $data['date_start']='';
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

            $reports['date_end']=System_helper::get_time($reports['date_end']);
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['date_end']>0)
            {
                $reports['date_end']=$reports['date_end']+3600*24-1;
            }

            $data['options']=$reports;
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
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $attendance=$this->input->post('attendance');
        $db_login=$this->load->database('armalik_login',TRUE);
        $db_login->from($this->config->item('table_setup_user').' user');
        $db_login->select('user.id,user.employee_id,user.user_name,user.status');
        $db_login->select('user_info.name,user_info.ordering');
        $db_login->join($this->config->item('table_setup_user_info').' user_info','user.id = user_info.user_id','INNER');
        $db_login->join($this->config->item('table_setup_designation').' designation','designation.id = user_info.designation','LEFT');
        $db_login->where('user_info.revision',1);
        $db_login->order_by('user_info.ordering','ASC');
        if($user->user_group!=1)
        {
            $db_login->where('user_info.user_group !=',1);
        }
        $results=$db_login->get()->result_array();
        $users_info=array();
        foreach($results as $result)
        {
            $users_info[$result['id']]['employee_id']=$result['employee_id'];
            $users_info[$result['id']]['name']=$result['name'];
        }

        $this->db->from($this->config->item('table_tm_daily_activities_ti').' daily_activities');
        $this->db->select('daily_activities.*');
        $this->db->select('aa.user_id');
        $this->db->select('t.name territory_name');
        $this->db->join($this->config->item('table_system_assigned_area').' aa','aa.user_id = daily_activities.user_started','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = aa.territory_id','LEFT');

        if($attendance)
        {
            $this->db->where('daily_activities.attendance',$attendance);
        }
        if($date_end>0)
        {
            $this->db->where('daily_activities.date_started <=',$date_end);
        }
        if($date_start>0)
        {
            $this->db->where('daily_activities.date_started >=',$date_start);
        }
        if($division_id>0)
        {
            $this->db->where('aa.division_id',$division_id);
            if($zone_id>0)
            {
                $this->db->where('aa.zone_id',$zone_id);
                if($territory_id>0)
                {
                    $this->db->where('aa.territory_id',$territory_id);
                }
            }
        }

        $this->db->where('daily_activities.status!=',$this->config->item('system_status_delete'));
        $this->db->order_by('daily_activities.id DESC');
        $this->db->group_by('daily_activities.id');
        $items=$this->db->get()->result_array();

        $this->db->from($this->config->item('table_tm_daily_activities_ti').' daily_activities');
        $this->db->select('activities_details.activities_id,activities_details.remarks_started,activities_details.remarks_reported');
        $this->db->join($this->config->item('table_tm_daily_activities_ti_details').' activities_details','activities_details.activities_id=daily_activities.id','INNER');
        $this->db->where('daily_activities.status!=',$this->config->item('system_status_delete'));
        $this->db->where('activities_details.status!=',$this->config->item('system_status_delete'));
        $results=$this->db->get()->result_array();
        $tasks_list=array();
        $work_done_list=array();
        foreach($results as $result)
        {
            if(isset($tasks_list[$result['activities_id']]))
            {
                $tasks_list[$result['activities_id']]['number']++;
                $tasks_list[$result['activities_id']]['text'].=', '.$tasks_list[$result['activities_id']]['number'].'. '.$result['remarks_started'];
            }
            else
            {
                $tasks_list[$result['activities_id']]['number']=1;
                $tasks_list[$result['activities_id']]['text']='1. '.$result['remarks_started'];
            }
            if($result['remarks_reported']!=null)
            {
                if(isset($work_done_list[$result['activities_id']]))
                {
                    $work_done_list[$result['activities_id']]['number']++;
                    $work_done_list[$result['activities_id']]['text'].=', '.$work_done_list[$result['activities_id']]['number'].'. '.$result['remarks_reported'];
                }else
                {
                    $work_done_list[$result['activities_id']]['number']=1;
                    $work_done_list[$result['activities_id']]['text']='1. '.$result['remarks_reported'];
                }
            }
        }

        foreach($items as &$item)
        {
            if(isset($users_info[$item['user_id']]))
            {
                $item['employee_id']=$users_info[$item['user_id']]['employee_id'];
                $item['employee_name']=$users_info[$item['user_id']]['name'];

            }
            if(isset($tasks_list[$item['id']]))
            {
                $item['remarks_started']=$tasks_list[$item['id']]['text'];
            }
            else
            {
                $item['remarks_started']='-';
            }

            if(isset($work_done_list[$item['id']]))
            {
                $item['remarks_reported']=$work_done_list[$item['id']]['text'];
            }
            else
            {
                $item['remarks_reported']='-';
            }


            if($item['date_started']==null)
            {
                $item['date_started']='-';
            }else
            {
                $item['date_started']=System_helper::display_date_time($item['date_started']);
            }

            if($item['date_reported']==null)
            {
                $item['date_reported']='-';
            }else
            {
                $item['date_reported']=System_helper::display_date_time($item['date_reported']);
            }
            if($item['attendance']==null)
            {
                $item['attendance']='-';
            }elseif($item['attendance']=='present')
            {
                $item['attendance']='Present';
            }elseif($item['attendance']=='halfday')
            {
                $item['attendance']='Half Day';
            }elseif($item['attendance']=='absent')
            {
                $item['attendance']='Absent';
            }
        }
        $this->jsonReturn($items);

    }
}
