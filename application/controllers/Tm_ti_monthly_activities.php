<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tm_ti_monthly_activities extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_ti_monthly_activities');
        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->jsonReturn($ajax);
        }
        $this->controller_url='tm_ti_monthly_activities';
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=='get_items')
        {
            $this->system_get_items();
        }
        elseif($action=="reporting")
        {
            $this->system_reporting($id);
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
            $data['title']="TI Monthly Activities";
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
        $user_location=User_helper::get_locations();
        $this->db->from($this->config->item('table_tm_monthly_activities_area_setup_ti').' ast');
        $this->db->select('ast.*');
        $this->db->select('u.name upazilla_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.id territory_id');
        $this->db->join($this->config->item('table_setup_location_upazillas').' u','u.id = ast.upazilla_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = u.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        if($user->user_group!=1 && $user->user_group!=2)
        {
            $this->db->where('t.id',$user_location['territory_id']);
        }
        $this->db->join('arm_demo_login.'.$this->config->item('table_setup_user_info').' ui','ui.user_id = ast.employee_info_id AND ui.revision=1','LEFT');
        $this->db->where('ast.status=',$this->config->item('system_status_active'));
        $this->db->order_by('ast.id ASC');
        $items=$this->db->get()->result_array();
        $this->jsonReturn($items);
    }

    private function system_save()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();

        $time = time();
        $fiscal_years=Query_helper::get_info($this->config->item('table_basic_setup_fiscal_year'),array('id value','name text','date_start','date_end'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('id ASC'));

        foreach($fiscal_years as $years)
        {
            if($time>=$years['date_start'] && $time<=$years['date_end'])
            {
                $fiscal_year_id=$years['value'];
            }
        }

        $month_id = date('n');
        $area_id = $this->input->post("area_id");
        if($id>0)
        {
            if(!(isset($this->permissions['edit']) && ($this->permissions['edit']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
                die();
            }
        }
        else
        {
            if(!(isset($this->permissions['add']) && ($this->permissions['add']==1)))
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
            $data=$this->input->post('item');
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $data['area_id']=$area_id;
                $data['month_id']=$month_id;
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = time();
                Query_helper::update($this->config->item('table_tm_monthly_activities_ti'),$data,array("area_id = ".$area_id,"month_id = ".$month_id,"fiscal_year_id = ".$fiscal_year_id));
            }
            else
            {
                $data['area_id']=$area_id;
                $data['month_id']=$month_id;
                $data['fiscal_year_id']=$fiscal_year_id;
                $data['user_created'] = $user->user_id;
                $data['date_created'] = time();
                Query_helper::add($this->config->item('table_tm_monthly_activities_ti'),$data);
            }
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

    private function system_reporting($id)
    {
        $user=User_helper::get_user();
        $user_id=$user->user_id;
        $time=time();
        if(isset($this->permissions['add'])&&($this->permissions['add']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }
            $month_id=date('n');
            $data['month_name'] = date('F', mktime(0, 0, 0, $month_id, 1));
            $time = time();
            $fiscal_years=Query_helper::get_info($this->config->item('table_basic_setup_fiscal_year'),array('id value','name text','date_start','date_end'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('id ASC'));

            foreach($fiscal_years as $years)
            {
                if($time>=$years['date_start'] && $time<=$years['date_end'])
                {
                    $fiscal_year_id=$years['value'];
                }
            }


            $result=Query_helper::get_info($this->config->item('table_tm_monthly_activities_ti'),'*',array('area_id ='.$item_id,'month_id ='.$month_id,'fiscal_year_id ='.$fiscal_year_id),1);
            $area_set_up_info=Query_helper::get_info($this->config->item('table_tm_monthly_activities_area_setup_ti'),'*',array('id ='.$item_id),1);
            if($user->user_group!=1 && $user->user_group!=2)
            {
                if($result)
                {
                    if(!(isset($this->permissions['edit'])&&($this->permissions['edit']==1)))
                    {
                        $ajax['status']=false;
                        $ajax['system_message']='Reporting done of this area.'.$this->lang->line("YOU_DONT_HAVE_ACCESS");
                        $this->jsonReturn($ajax);
                    }
                }
            }
            if($result)
            {
                $data['item']=$result;
            }
            else
            {
                $data["item"] = Array(
                    'id' => 0,
                    'area_id' => $item_id,
                    'achievement' => '',
                    'work_done' => '',
                    'next_month_crop_variety' => '',
                    'amount_self_target' => '',
                    'reason_self_target' => '',
                    'value_marking' => '',
                    'reason_marking' => ''
                );
            }
            $data['title']="Monthly Activities Reporting (".$area_set_up_info['area_name'].')';
            $ajax['system_page_url']=site_url($this->controller_url."/index/reporting/".$item_id);
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
    private function check_validation()
    {
        $data=$this->input->post('item');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[achievement]',$this->lang->line('LABEL_PREVIOUS_MONTH_ACHIEVEMENT'),'required');
        $this->form_validation->set_rules('item[work_done]',$this->lang->line('LABEL_SUCCESSFULLY_WORK_DONE'),'required');
        $this->form_validation->set_rules('item[next_month_crop_variety]',$this->lang->line('LABEL_NEXT_MONTHS_CROP_VARIETY'),'required');
        $this->form_validation->set_rules('item[amount_self_target]',$this->lang->line('LABEL_SELF_TARGET').' (Amount)','required');
        $this->form_validation->set_rules('item[reason_self_target]',$this->lang->line('LABEL_SELF_TARGET').' (Reason)','required');
        $this->form_validation->set_rules('item[value_marking]',$this->lang->line('LABEL_SELF_MARKING').' (Out of 10)','required');
        $this->form_validation->set_rules('item[reason_marking]',$this->lang->line('LABEL_SELF_MARKING').' (Reason)','required');

        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
}
