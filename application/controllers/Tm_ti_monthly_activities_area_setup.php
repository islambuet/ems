<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tm_ti_monthly_activities_area_setup extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_ti_monthly_activities_area_setup');
        $this->locations=User_helper::get_locations();
        if(!($this->locations))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line('MSG_LOCATION_NOT_ASSIGNED_OR_INVALID');
            $this->jsonReturn($ajax);
        }
        $this->controller_url='tm_ti_monthly_activities_area_setup';
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
            $data['title']="Area Setup For Monthly Activities";
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
        $this->db->from($this->config->item('table_system_assigned_area').' aa');
        $this->db->select('aa.user_id id');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->select('COUNT(ast.employee_info_id) as number_of_area');
        $this->db->join('arm_demo_login.'.$this->config->item('table_setup_user').' su','su.id = aa.user_id AND su.status="'.$this->config->item('system_status_active').'"','INNER');
        $this->db->join($this->config->item('table_tm_monthly_activities_area_setup_ti').' ast','ast.employee_info_id = aa.user_id AND ast.status="'.$this->config->item('system_status_active').'"','LEFT');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = aa.territory_id','LEFT');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = aa.zone_id','LEFT');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = aa.division_id','LEFT');
        $this->db->where('aa.revision',1);
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
        $this->db->order_by('aa.user_id');
        $this->db->group_by('aa.user_id');
        $results=$this->db->get()->result_array();
        $user_ids=array();
        $user_ids[0]=0;
        foreach($results as $result)
        {
            $user_ids[$result['id']]=$result['id'];
        }
        $user_details=System_helper::get_users_info($user_ids);
        $items=array();
        foreach($results as $result)
        {
            if(isset($user_details[$result['id']]))
            {
                $item=array();
                $item['id']=$result['id'];
                $item['name']=$user_details[$result['id']]['name'];
                if($result['division_name'])
                {
                    $item['division_name']=$result['division_name'];
                }
                else
                {
                    $item['division_name']='ALL';
                }
                if($result['zone_name'])
                {
                    $item['zone_name']=$result['zone_name'];
                }
                else
                {
                    $item['zone_name']='ALL';
                }
                if($result['territory_name'])
                {
                    $item['territory_name']=$result['territory_name'];
                }
                else
                {
                    $item['territory_name']='ALL';
                }
                if($result['number_of_area'])
                {
                    $item['number_of_area']=$result['number_of_area'];
                }
                else
                {
                    $item['number_of_area']='0';
                }
                $items[]=$item;
            }

        }
        $this->jsonReturn($items);
    }
    private function system_edit($id)
    {
        if(isset($this->permissions['add']) && ($this->permissions['add']==1) || isset($this->permissions['edit']) && ($this->permissions['edit']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }
            $data['item']['id']=$item_id;



            $user_location=Query_helper::get_info($this->config->item('table_system_assigned_area'),'*',array('user_id ='.$item_id,'revision=1'),1);
            if(!$this->check_my_editable($user_location))
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$item_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

            $this->db->from($this->config->item('table_system_assigned_area').' aa');
            $this->db->select('division.name as division_name');
            $this->db->select('zone.name as zone_name');
            $this->db->select('territory.name as territory_name');
            $this->db->select('u.name');
            $this->db->select('GROUP_CONCAT(district.name) as district_name,GROUP_CONCAT(district.id) as district_id');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = aa.division_id','LEFT');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = aa.zone_id','LEFT');
            $this->db->join($this->config->item('table_setup_location_territories').' territory','territory.id = aa.territory_id','LEFT');
            if($user_location['territory_id']>0)
            {
                $this->db->join($this->config->item('table_setup_location_districts').' district','district.territory_id = '.$user_location['territory_id'],'INNER');
                $this->db->where('district.status',$this->config->item('system_status_active'));
            }
            else
            {
                if($user_location['zone_id']>0)
                {
                    $this->db->join($this->config->item('table_setup_location_territories').' t','t.zone_id = '.$user_location['zone_id'],'INNER');
                    $this->db->join($this->config->item('table_setup_location_districts').' district','district.territory_id = t.id','INNER');
                    $this->db->where('t.status',$this->config->item('system_status_active'));
                    $this->db->where('district.status',$this->config->item('system_status_active'));
                }
                else
                {
                    if($user_location['division_id']>0)
                    {
                        $this->db->join($this->config->item('table_setup_location_zones').' z','z.division_id = '.$user_location['division_id'],'INNER');
                        $this->db->join($this->config->item('table_setup_location_territories').' t','t.zone_id = z.id','INNER');
                        $this->db->join($this->config->item('table_setup_location_districts').' district','district.territory_id = t.id','INNER');
                        $this->db->where('z.status',$this->config->item('system_status_active'));
                        $this->db->where('t.status',$this->config->item('system_status_active'));
                        $this->db->where('district.status',$this->config->item('system_status_active'));
                    }
                    else
                    {
                        $this->db->join($this->config->item('table_setup_location_districts').' district','district.status="'.$this->config->item('system_status_active').'"','INNER');
                        $this->db->where('district.status',$this->config->item('system_status_active'));
                    }
                }
            }
            $this->db->join('arm_demo_login.'.$this->config->item('table_setup_user_info').' u','u.user_id = '.$item_id.' AND u.revision = 1','INNER');
            $this->db->where('aa.user_id',$item_id);
            $this->db->where('aa.revision',1);
            $result=$this->db->get()->row_array();

            $district_ids=explode(',',$result['district_id']);
            $district_names=explode(',',$result['district_name']);
            $data['districts']=array_combine($district_ids,$district_names);

            if(!$result['division_name'])
            {
                $data['division_name']='ALL';
            }
            else
            {
                $data['division_name']=$result['division_name'];
            }
            if(!$result['zone_name'])
            {
                $data['zone_name']='ALL';
            }
            else
            {
                $data['zone_name']=$result['zone_name'];
            }
            if(!$result['territory_name'])
            {
                $data['territory_name']='ALL';
            }
            else
            {
                $data['territory_name']=$result['territory_name'];
            }
            $data['name']=$result['name'];

            $this->db->from($this->config->item('table_tm_monthly_activities_area_setup_ti').' ast');
            $this->db->select('ast.*');
            $this->db->select('u.name upazilla_name');
            $this->db->select('d.name district_name');
            $this->db->select('t.id territory_id');
            $this->db->join($this->config->item('table_setup_location_upazillas').' u','u.id = ast.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = u.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->where('ast.status!=',$this->config->item('system_status_delete'));
            $this->db->where('t.id',$user_location['territory_id']);
            $results=$this->db->get()->result_array();
            if($results)
            {
                if(isset($this->permissions['edit']) && ($this->permissions['edit']==1))
                {
                    foreach($results as $result)
                    {
                        $data['old_item'][$result['id']]=$result;
                    }
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']='You are not permitted to edit this';
                    $this->jsonReturn($ajax);
                }
            }
            else
            {
                if(isset($this->permissions['add']) && ($this->permissions['add']==1))
                {
                    $data['old_item']=array();
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']='You have no permission to edit this';
                    $this->jsonReturn($ajax);
                }
            }

            $data['title']="Edit Area (".$data['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/edit',$data,true));
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

    private function system_details($id)
    {
        if(isset($this->permissions['view']) && ($this->permissions['view']==1))
        {
            if(($this->input->post('id')))
            {
                $item_id=$this->input->post('id');
            }
            else
            {
                $item_id=$id;
            }
            $data['item']['id']=$item_id;

            $this->db->from($this->config->item('table_tm_monthly_activities_area_setup_ti').' ast');
            $this->db->select('ast.*');
            $this->db->select('u.name upazilla_name');
            $this->db->select('d.name district_name');
            $this->db->join($this->config->item('table_setup_location_upazillas').' u','u.id = ast.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = u.district_id','INNER');
            $this->db->where('ast.employee_info_id',$data['item']['id']);
            $this->db->where('ast.status=',$this->config->item('system_status_active'));
            $results=$this->db->get()->result_array();

            if($results)
            {
                if(isset($this->permissions['edit']) && ($this->permissions['edit']==1))
                {
                    foreach($results as $result)
                    {
                        $data['old_item'][$result['id']]=$result;
                    }
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']='You are not permitted to edit this';
                    $this->jsonReturn($ajax);
                }
            }
            else
            {
                if(isset($this->permissions['add']) && ($this->permissions['add']==1))
                {
                    $data['old_item']=array();
                }
                else
                {
                    $ajax['status']=false;
                    $ajax['system_message']='You have no permission to edit this';
                    $this->jsonReturn($ajax);
                }
            }

            $user_location=Query_helper::get_info($this->config->item('table_system_assigned_area'),'*',array('user_id ='.$item_id,'revision=1'),1);

            if(!$this->check_my_editable($user_location))
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$item_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

            $this->db->from($this->config->item('table_system_assigned_area').' aa');
            $this->db->select('division.name as division_name');
            $this->db->select('zone.name as zone_name');
            $this->db->select('territory.name as territory_name');
            $this->db->select('u.name');
            $this->db->select('GROUP_CONCAT(district.name) as district_name,GROUP_CONCAT(district.id) as district_id');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = aa.division_id','LEFT');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = aa.zone_id','LEFT');
            $this->db->join($this->config->item('table_setup_location_territories').' territory','territory.id = aa.territory_id','LEFT');
            if($user_location['territory_id']>0)
            {
                $this->db->join($this->config->item('table_setup_location_districts').' district','district.territory_id = '.$user_location['territory_id'],'INNER');
                $this->db->where('district.status',$this->config->item('system_status_active'));
            }
            else
            {
                if($user_location['zone_id']>0)
                {
                    $this->db->join($this->config->item('table_setup_location_territories').' t','t.zone_id = '.$user_location['zone_id'],'INNER');
                    $this->db->join($this->config->item('table_setup_location_districts').' district','district.territory_id = t.id','INNER');
                    $this->db->where('t.status',$this->config->item('system_status_active'));
                    $this->db->where('district.status',$this->config->item('system_status_active'));
                }
                else
                {
                    if($user_location['division_id']>0)
                    {
                        $this->db->join($this->config->item('table_setup_location_zones').' z','z.division_id = '.$user_location['division_id'],'INNER');
                        $this->db->join($this->config->item('table_setup_location_territories').' t','t.zone_id = z.id','INNER');
                        $this->db->join($this->config->item('table_setup_location_districts').' district','district.territory_id = t.id','INNER');
                        $this->db->where('z.status',$this->config->item('system_status_active'));
                        $this->db->where('t.status',$this->config->item('system_status_active'));
                        $this->db->where('district.status',$this->config->item('system_status_active'));
                    }
                    else
                    {
                        $this->db->join($this->config->item('table_setup_location_districts').' district','district.status="'.$this->config->item('system_status_active').'"','INNER');
                        $this->db->where('district.status',$this->config->item('system_status_active'));
                    }
                }
            }
            $this->db->join('arm_demo_login.'.$this->config->item('table_setup_user_info').' u','u.user_id = '.$item_id.' AND u.revision = 1','INNER');
            $this->db->where('aa.user_id',$item_id);
            $this->db->where('aa.revision',1);
            $result=$this->db->get()->row_array();

            $district_ids=explode(',',$result['district_id']);
            $district_names=explode(',',$result['district_name']);
            $data['districts']=array_combine($district_ids,$district_names);

            if(!$result['division_name'])
            {
                $data['division_name']='ALL';
            }
            else
            {
                $data['division_name']=$result['division_name'];
            }
            if(!$result['zone_name'])
            {
                $data['zone_name']='ALL';
            }
            else
            {
                $data['zone_name']=$result['zone_name'];
            }
            if(!$result['territory_name'])
            {
                $data['territory_name']='ALL';
            }
            else
            {
                $data['territory_name']=$result['territory_name'];
            }
            $data['name']=$result['name'];

            $data['title']="Edit Area (".$data['name'].')';
            $ajax['status']=true;
            $ajax['system_content'][]=array('id'=>'#system_content','html'=>$this->load->view($this->controller_url.'/details',$data,true));
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

    private function system_save()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->jsonReturn($ajax);
        }
        $results=Query_helper::get_info($this->config->item('table_tm_monthly_activities_area_setup_ti'),'*',array('employee_info_id ='.$id,'status !="'.$this->config->item('system_status_delete').'"'));
        if($results)
        {
            foreach($results as $result)
            {
                $old_items[$result['id']]=$result;
            }
            $this->db->trans_start();  //DB Transaction Handle START
            if($this->input->post('old_items'))
            {
                $new_items=$this->input->post('old_items');
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = $time;
                foreach($new_items as $index=>$new_item)
                {
                    if($new_item['status']!=$old_items[$index]['status'])
                    {
                        $data['status']=$new_item['status'];
                        Query_helper::update($this->config->item('table_tm_monthly_activities_area_setup_ti'),$data,array('id='.$index));
                    }
                }
            }
            if($this->input->post('items'))
            {
                $data=array();
                $data['employee_info_id']=$id;
                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                $items=$this->input->post('items');
                foreach($items as $item)
                {
                    $data['upazilla_id']=$item['upazilla_id'];
                    $data['area_name']=$item['area_name'];
                    $data['area_address']=$item['area_address'];
                    if($item['arm_variety'])
                    {
                        $data['arm_variety']=$item['arm_variety'];
                    }
                    else
                    {
                        $data['arm_variety']=NULL;
                    }
                    if($item['competitor_variety'])
                    {
                        $data['competitor_variety']=$item['competitor_variety'];
                    }
                    else
                    {
                        $data['competitor_variety']=NULL;
                    }
                    Query_helper::add($this->config->item('table_tm_monthly_activities_area_setup_ti'),$data);
                }
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
        else
        {
            $items=$this->input->post('items');
            $this->db->trans_start();  //DB Transaction Handle START
            $data['employee_info_id']=$id;
            $data['user_created'] = $user->user_id;
            $data['date_created'] = $time;
            foreach($items as $item)
            {
                $data['upazilla_id']=$item['upazilla_id'];
                $data['area_name']=$item['area_name'];
                if($item['area_address'])
                {
                    $data['area_address']=$item['area_address'];
                }
                else
                {
                    $data['area_address']=NULL;
                }
                if($item['arm_variety'])
                {
                    $data['arm_variety']=$item['arm_variety'];
                }
                else
                {
                    $data['arm_variety']=NULL;
                }
                if($item['competitor_variety'])
                {
                    $data['competitor_variety']=$item['competitor_variety'];
                }
                else
                {
                    $data['competitor_variety']=NULL;
                }
                Query_helper::add($this->config->item('table_tm_monthly_activities_area_setup_ti'),$data);
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

    private function check_validation()
    {
        $items=$this->input->post('items');
        $old_items=$this->input->post('old_items');
        $id = $this->input->post("id");
        $this->load->library('form_validation');
        if($id==0)
        {
            $this->form_validation->set_rules('employee_info_id',$this->lang->line('LABEL_EMPLOYEE_NAME'),'required');
        }
        if($old_items)
        {
            foreach($old_items as $index=>$old_item)
            {
                $this->form_validation->set_rules('old_items['.$index.'][status]',$this->lang->line('STATUS'),'required');
            }
        }
        if($items)
        {
            foreach($items as $index=>$item)
            {
                $this->form_validation->set_rules('items['.$index.'][upazilla_id]',$this->lang->line('LABEL_UPAZILLA_NAME'),'required');
                $this->form_validation->set_rules('items['.$index.'][area_name]',$this->lang->line('LABEL_AREA_NAME'),'required');
                $this->form_validation->set_rules('items['.$index.'][area_address]',$this->lang->line('LABEL_AREA_ADDRESS'),'required');
            }
        }
        else
        {
            if(!$old_items)
            {
                $this->message='Please add an area';
                return false;
            }
        }
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    private function check_my_editable($security)
    {
        if(($this->locations['division_id']>0)&&($this->locations['division_id']!=$security['division_id']))
        {
            return false;
        }
        if(($this->locations['zone_id']>0)&&($this->locations['zone_id']!=$security['zone_id']))
        {
            return false;
        }
        if(($this->locations['territory_id']>0)&&($this->locations['territory_id']!=$security['territory_id']))
        {
            return false;
        }
        if(($this->locations['district_id']>0)&&($this->locations['district_id']!=$security['district_id']))
        {
            return false;
        }
        return true;
    }
}