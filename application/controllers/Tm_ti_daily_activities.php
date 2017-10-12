<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tm_ti_daily_activities extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_ti_daily_activities');
        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->jsonReturn($ajax);
        }
        $this->controller_url='tm_ti_daily_activities';
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=='get_items')
        {
            $this->system_get_items();
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="reporting")
        {
            $this->system_reporting($id);
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="save_reporting")
        {
            $this->system_save_reporting();
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
            $data['title']="Daily Activities List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
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
        $user=User_helper::get_user();
        $this->db->from($this->config->item('table_tm_daily_activities_ti').' daily_activities');
        $this->db->select('daily_activities.*');
        $this->db->join($this->config->item('table_system_assigned_area').' aa','aa.user_id = daily_activities.user_started','INNER');
        if($user->user_group!=1 && $user->user_group!=2)
        {
            $this->db->where('user_started',$user->user_id);

        }
        $this->db->where('status!=',$this->config->item('system_status_delete'));
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
    private function system_add()
    {
        $user=User_helper::get_user();
        $user_id=$user->user_id;
        $time=time();
        $today_date=System_helper::display_date($time);
        if($user->user_group!=1 && $user->user_group!=2)
        {
            $items=Query_helper::get_info($this->config->item('table_tm_daily_activities_ti'),'*',array('user_started ='.$user_id));
            $old_items=array();
            foreach($items as $item)
            {
                $old_items[]=System_helper::display_date($item['date_started']);
            }
            if (in_array($today_date, $old_items))
            {
                $ajax['status']=false;
                $ajax['system_message']='You have already added todays task. Please edit previous task.';
                $this->jsonReturn($ajax);
            }
        }
        if(isset($this->permissions['add'])&&($this->permissions['add']==1))
        {
            $data['title']="Daily Task";
            $data["item"] = Array(
                'id' => 0,
                'date_started' =>time(),
                'remarks_started' => '',
            );
            $data['item_details']=array();
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
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
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }
            $data['item']=Query_helper::get_info($this->config->item('table_tm_daily_activities_ti'),'*',array('id ='.$item_id),1);
            if($data['item']['attendance']!==null)
            {
                if(!(isset($this->permissions['delete'])&&($this->permissions['delete']==1)))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Attendance Taken. You can not edit it';
                    $this->jsonReturn($ajax);
                }
            }
            $data['item_details']=Query_helper::get_info($this->config->item('table_tm_daily_activities_ti_details'),'id,remarks_started',array('activities_id ='.$item_id,'status!="'.$this->config->item('system_status_delete').'"'));
            $data['title']="Edit Daily Task";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$item_id);
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
        $remarks_started_check=true;
        $remarks_started_new=$this->input->post('remarks_started_new');
        if(isset($remarks_started_new))
        {
            foreach($remarks_started_new as $key=>$remarks)
            {
                if($remarks==null)
                {
                    $remarks_started_check=false;
                }
            }
            if(!$remarks_started_check)
            {
                $ajax['status']=false;
                $ajax['system_message']='You can not add empty task. Please delete empty row first.';
                $this->jsonReturn($ajax);
                die();
            }
        }
        $time=time();
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if($id>0)
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
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->jsonReturn($ajax);
        }
        else
        {
            $this->db->trans_start();  //DB Transaction Handle START
            $remarks_started_new=$this->input->post('remarks_started_new');
            if(!is_array($remarks_started_new))
            {
                $remarks_started_new=array();
            }

            if($id>0)
            {
                $remarks_started_old=$this->input->post('remarks_started_old');
                if(!is_array($remarks_started_old))
                {
                    $remarks_started_old=array();
                }

                $activities_details=Query_helper::get_info($this->config->item('table_tm_daily_activities_ti_details'),'id,remarks_started',array('activities_id ='.$id,'status!="'.$this->config->item('system_status_delete').'"'));

                $data_edit=array();
                $data_edit['date_updated_start_task']=$time;
                $data_edit['user_updated_start_task']=$user->user_id;

                $data_delete=array();
                $data_delete['status']=$this->config->item('system_status_delete');
                $data_delete['date_updated_start_task']=$time;
                $data_delete['user_updated_start_task']=$user->user_id;

                foreach($activities_details as $details)
                {
                    if(isset($remarks_started_old[$details['id']]))
                    {
                        if($details['remarks_started']!=$remarks_started_old[$details['id']])
                        {
                            $data_edit['remarks_started']=$remarks_started_old[$details['id']];
                            Query_helper::update($this->config->item('table_tm_daily_activities_ti_details'),$data_edit,array('id='.$details['id']));
                        }
                    }
                    else
                    {
                        Query_helper::update($this->config->item('table_tm_daily_activities_ti_details'),$data_delete,array('id='.$details['id']));
                    }
                }
                $data=array();
                $data['date_updated_start']=$time;
                $data['user_updated_start']=$user->user_id;
                Query_helper::update($this->config->item('table_tm_daily_activities_ti'),$data,array('id='.$id));
            }
            else
            {
                $data=array();
                $data['date_started']=$time;
                $data['user_started']=$user->user_id;
                $id=Query_helper::add($this->config->item('table_tm_daily_activities_ti'),$data);
            }

            $data=array();
            $data['activities_id']=$id;
            $data['date_started_task']=$time;
            $data['user_started_task']=$user->user_id;

            foreach($remarks_started_new as $remarks)
            {
                $data['remarks_started']=$remarks;
                Query_helper::add($this->config->item('table_tm_daily_activities_ti_details'),$data);
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
    }
    private function system_reporting($id)
    {
        $user=User_helper::get_user();
        if(isset($this->permissions['edit'])&&($this->permissions['edit']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }

            $data['item']=Query_helper::get_info($this->config->item('table_tm_daily_activities_ti'),'*',array('id ='.$item_id),1);
            if($data['item']['attendance']!==null)
            {
                if(!(isset($this->permissions['delete'])&&($this->permissions['delete']==1)))
                {
                    $ajax['status']=false;
                    $ajax['system_message']='Attendance Taken. You can not edit it';
                    $this->jsonReturn($ajax);
                }
            }
            $data['item_details']=Query_helper::get_info($this->config->item('table_tm_daily_activities_ti_details'),'*',array('activities_id ='.$item_id,'status!="'.$this->config->item('system_status_delete').'"'));

            $data['title']="Daily Task Reporting";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/reporting_add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/reporting/'.$item_id);
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
        if(isset($this->permissions['edit'])&&($this->permissions['edit']==1))
        {
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
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$item_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }

    private function system_save_reporting()
    {
        $time=time();
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if($id>0)
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
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
            die();
        }
        if(!$this->check_validation_reporting())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->jsonReturn($ajax);
        }
        else
        {
            $allowed_types='jpg|png|pdf|doc|docx';
            $folder=FCPATH.'images/daily_activities/'.$id;
            if(!is_dir($folder))
            {
                mkdir($folder,0777);
            }
            $uploaded_files=System_helper::upload_file('images/daily_activities/'.$id,$allowed_types);
            foreach($uploaded_files as $file)
            {
                if(!$file['status'])
                {
                    $ajax['status']=false;
                    $ajax['system_message']=$file['message'];
                    $this->jsonReturn($ajax);
                }
            }

            $item_details=Query_helper::get_info($this->config->item('table_tm_daily_activities_ti_details'),'*',array('activities_id ='.$id,'status!="'.$this->config->item('system_status_delete').'"'));
            $old_item_details=array();
            foreach($item_details as $details)
            {
                $old_item_details[$details['id']]=$details['date_reported_task'];
            }
            $this->db->trans_start();  //DB Transaction Handle START
            $items=$this->input->post('items');
            foreach($items as $details_id=>$remarks_reported)
            {
                $data=array();
                $data['remarks_reported']=$remarks_reported;
                if(isset($uploaded_files['file_'.$details_id]))
                {
                    $data['file_name']=$uploaded_files['file_'.$details_id]['info']['file_name'];
                    $data['file_type']=$uploaded_files['file_'.$details_id]['info']['file_type'];
                    $data['file_location']='images/daily_activities/'.$id.'/'.$data['file_name'];
                }
                if(isset($old_item_details[$details_id]))
                {
                    $data['date_updated_report_task']=$time;
                    $data['user_updated_report_task']=$user->user_id;
                }else
                {
                    if(($data['remarks_reported']!=null) || (isset($data['file_name']) && $data['file_name']!=null))
                    {
                        $data['date_reported_task']=$time;
                        $data['user_reported_task']=$user->user_id;
                    }
                }
                Query_helper::update($this->config->item('table_tm_daily_activities_ti_details'),$data,array('id='.$details_id));
            }
            $data_activities_ti['date_reported']=$time;
            $data_activities_ti['user_reported']=$user->user_id;
            $old_item=Query_helper::get_info($this->config->item('table_tm_daily_activities_ti'),'*',array('id ='.$id),1);
            if(isset($old_item['date_reported']))
            {
                $data_activities_ti['date_updated_report']=$time;
                $data_activities_ti['user_updated_report']=$user->user_id;
            }
            Query_helper::update($this->config->item('table_tm_daily_activities_ti'),$data_activities_ti,array('id='.$id));
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
    private function check_validation()
    {
        return true;
    }
    private function check_validation_reporting()
    {
        return true;
    }
}
