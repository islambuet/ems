<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tm_farmer_visit_setup extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_farmer_visit_setup');
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
        $this->controller_url='tm_farmer_visit_setup';
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=="get_items")
        {
            $this->get_items();
        }
        elseif($action=="list_variety")
        {
            $this->system_list_variety();
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="delete")
        {
            $this->system_delete();
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="save_unfilled")
        {
            $this->system_save_unfilled();
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
            $data['title']="Farmer and Field Visit Setup List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_farmer_visit_setup/list",$data,true));
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
    private function get_items()
    {
        $current_records = $this->input->post('total_records');
        if(!$current_records)
        {
            $current_records=0;
        }
        $pagesize = $this->input->post('pagesize');
        if(!$pagesize)
        {
            $pagesize=40;
        }
        else
        {
            $pagesize=$pagesize*2;
        }
        //$this->db->from($this->config->item('table_csetup_other_customers').' cus');
        $this->db->from($this->config->item('table_tm_farmers').' tmf');
        $this->db->select('tmf.*');
        $this->db->select('upazilla.name upazilla_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        /*$this->db->select('crop.name crop_name');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->select('v.name variety_name');*/

        $this->db->select('season.name season_name');
        $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        /*$this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tmf.variety_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');*/

        $this->db->join($this->config->item('table_setup_tm_seasons').' season','season.id =tmf.season_id','INNER');
        if($this->locations['division_id']>0)
        {
            $this->db->where('division.id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('zone.id',$this->locations['zone_id']);
                if($this->locations['territory_id']>0)
                {
                    $this->db->where('t.id',$this->locations['territory_id']);
                    if($this->locations['district_id']>0)
                    {
                        $this->db->where('d.id',$this->locations['district_id']);
                        if($this->locations['upazilla_id']>0)
                        {
                            $this->db->where('upazilla.id',$this->locations['upazilla_id']);
                        }
                    }
                }
            }
        }
        $this->db->where('tmf.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('id','DESC');
        $this->db->limit($pagesize,$current_records);
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_sowing']=System_helper::display_date($item['date_sowing']);
        }

        $this->jsonReturn($items);
    }
    private function system_list_variety()
    {
        $crop_type_id=$this->input->post('crop_type_id');

        //ARM
        $this->db->from($this->config->item('table_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name,v.whose');
        $this->db->where('v.crop_type_id',$crop_type_id);
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->order_by('v.whose','ASC');
        $this->db->order_by('v.ordering','ASC');
        $data['varieties']=$this->db->get()->result_array();
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#variety_list_container","html"=>$this->load->view("tm_farmer_visit_setup/list_variety",$data,true));

        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->jsonReturn($ajax);
    }
    private function system_add()
    {
        if(isset($this->permissions['add'])&&($this->permissions['add']==1))
        {
            $data['title']="New Farmer and Field Visit Setup";
            $data["fsetup"] = Array(
                'id'=>0,
                'year' => date('Y'),
                'season_id' => '',
                'division_id'=>$this->locations['division_id'],
                'zone_id'=>$this->locations['zone_id'],
                'territory_id'=>$this->locations['territory_id'],
                'district_id'=>$this->locations['district_id'],
                'upazilla_id'=>$this->locations['upazilla_id'],
                'crop_id'=>'',
                'type_id'=>'',
                'name'=>'',
                'address' => '',
                'contact_no' => '',
                'date_sowing' => time(),
                'date_transplant' => '',
                'num_visits' => 1,
                'interval' => 2

            );
            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['upazillas']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id']));
                    if($this->locations['territory_id']>0)
                    {
                        $data['districts']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$this->locations['territory_id']));
                        if($this->locations['district_id']>0)
                        {
                            $data['upazillas']=Query_helper::get_info($this->config->item('table_setup_location_upazillas'),array('id value','name text'),array('district_id ='.$this->locations['district_id'],'status ="'.$this->config->item('system_status_active').'"'));
                        }
                    }
                }
            }
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['types']=array();
            $data['varieties']=array();
            $data['seasons']=Query_helper::get_info($this->config->item('table_setup_tm_seasons'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_farmer_visit_setup/add_edit",$data,true));
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

        if((isset($this->permissions['edit'])&&($this->permissions['edit']==1))||(isset($this->permissions['add'])&&($this->permissions['add']==1)))
        {
            if(($this->input->post('id')))
            {
                $setup_id=$this->input->post('id');
            }
            else
            {
                $setup_id=$id;
            }
            $data['previous_varieties']=array();//active and inactive
            $results=Query_helper::get_info($this->config->item('table_tm_farmer_varieties'),'*',array('setup_id ='.$setup_id,'status ="'.$this->config->item('system_status_active').'"'));
            if(!$results)
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $variety_id=0;
            foreach($results as $i=>$result)
            {
                if($i==0)
                {
                    $variety_id=$result['variety_id'];
                }
                $data['previous_varieties'][$result['variety_id']]=$result;
            }

            $this->db->from($this->config->item('table_tm_farmers').' tmf');
            $this->db->select('tmf.*');
            $this->db->select('upazilla.name upazilla_name');
            $this->db->select('d.name district_name,d.id district_id');
            $this->db->select('t.name territory_name,t.id territory_id');
            $this->db->select('zone.name zone_name,zone.id zone_id');
            $this->db->select('division.name division_name,division.id division_id');
            $this->db->select('crop_type.id type_id,crop_type.name crop_type_name');
            $this->db->select('crop.id crop_id,crop.name crop_name');
            $this->db->select('v.name variety_name');//invalid
            $this->db->select('season.name season_name');
            $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id ='.$variety_id,'INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
            $this->db->join($this->config->item('table_setup_tm_seasons').' season','season.id =tmf.season_id','INNER');
            $this->db->where('tmf.id',$setup_id);
            $this->db->where('tmf.status','Active');
            $data['fsetup']=$this->db->get()->row_array();

            if(!$data['fsetup'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

            if(!$this->check_my_editable($data['fsetup']))
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }


            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$data['fsetup']['division_id']));
            $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$data['fsetup']['zone_id']));
            $data['districts']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$data['fsetup']['territory_id']));

            $data['upazillas']=Query_helper::get_info($this->config->item('table_setup_location_upazillas'),array('id value','name text'),array('district_id ='.$data['fsetup']['district_id'],'status ="'.$this->config->item('system_status_active').'"'));
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['types']=Query_helper::get_info($this->config->item('table_setup_classification_crop_types'),array('id value','name text'),array('crop_id ='.$data['fsetup']['crop_id'],'status ="'.$this->config->item('system_status_active').'"'));
            $data['varieties']=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),array('id value','name text','whose'),array('crop_type_id ='.$data['fsetup']['type_id'],'status ="'.$this->config->item('system_status_active').'"'),0,0,array('whose ASC','ordering ASC'));

            $data['seasons']=Query_helper::get_info($this->config->item('table_setup_tm_seasons'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $data['title']="Edit Farmer and Field Visit Setup";
            $ajax['status']=true;
            if(isset($this->permissions['edit'])&&($this->permissions['edit']==1))
            {
                $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_farmer_visit_setup/add_edit",$data,true));
            }
            elseif(isset($this->permissions['add'])&&($this->permissions['add']==1))
            {
                $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_farmer_visit_setup/edit_unfilled",$data,true));
            }
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
    private function system_details($id)
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            if(($this->input->post('id')))
            {
                $setup_id=$this->input->post('id');
            }
            else
            {
                $setup_id=$id;
            }
            $data['previous_varieties']=array();//active and inactive
            $results=Query_helper::get_info($this->config->item('table_tm_farmer_varieties'),'*',array('setup_id ='.$setup_id,'status ="'.$this->config->item('system_status_active').'"'));
            if(!$results)
            {
                System_helper::invalid_try('details not exists',$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $variety_id=0;
            foreach($results as $i=>$result)
            {
                if($i==0)
                {
                    $variety_id=$result['variety_id'];
                }
                $data['previous_varieties'][$result['variety_id']]=$result;
            }

            $this->db->from($this->config->item('table_tm_farmers').' tmf');
            $this->db->select('tmf.*');
            $this->db->select('upazilla.name upazilla_name');
            $this->db->select('d.name district_name,d.id district_id');
            $this->db->select('t.name territory_name,t.id territory_id');
            $this->db->select('zone.name zone_name,zone.id zone_id');
            $this->db->select('division.name division_name,division.id division_id');
            $this->db->select('crop.name crop_name');
            $this->db->select('crop_type.id type_id,crop_type.name crop_type_name');
            $this->db->select('v.name variety_name');//invalid
            $this->db->select('season.name season_name');
            $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id ='.$variety_id,'INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
            $this->db->join($this->config->item('table_setup_tm_seasons').' season','season.id =tmf.season_id','INNER');
            $this->db->where('tmf.id',$setup_id);
            $this->db->where('tmf.status','Active');
            $data['fsetup']=$this->db->get()->row_array();
            if(!$data['fsetup'])
            {
                System_helper::invalid_try($this->config->item('system_view_not_exists'),$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if(!$this->check_my_editable($data['fsetup']))
            {
                System_helper::invalid_try($this->config->item('system_view_others'),$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['title']="Detail Farmer and Field Visit Setup";
            $data['varieties']=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),array('id value','name text','whose'),array('crop_type_id ='.$data['fsetup']['type_id'],'status ="'.$this->config->item('system_status_active').'"'),0,0,array('whose ASC','ordering ASC'));
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_farmer_visit_setup/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }

            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$setup_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }
    private function system_delete()
    {
        if(isset($this->permissions['delete'])&&($this->permissions['delete']==1))
        {
            $ids = $this->input->post("ids");
            $user = User_helper::get_user();
            $this->db->trans_start();  //DB Transaction Handle START
            $time=time();
            foreach($ids as $id)
            {
                Query_helper::update($this->config->item('table_tm_farmers'),array('status'=>$this->config->item('system_status_delete'),'user_updated'=>$user->user_id,'date_updated'=>$time),array("id = ".$id));
                Query_helper::update($this->config->item('table_tm_farmer_varieties'),array('status'=>$this->config->item('system_status_delete'),'user_updated'=>$user->user_id,'date_updated'=>$time),array("setup_id = ".$id));
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
            $data=$this->input->post('fsetup');
            $data['date_sowing']=System_helper::get_time($data['date_sowing']);
            $data['date_transplant']=System_helper::get_time($data['date_transplant']);


            $this->db->trans_start();  //DB Transaction Handle START
            if($id==0)
            {
                $data['user_created'] = $user->user_id;
                $data['date_created'] =$time;
                $setup_id=Query_helper::add($this->config->item('table_tm_farmers'),$data);
                if($setup_id===false)
                {
                    $this->db->trans_complete();
                    $ajax['status']=false;
                    $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                    $this->jsonReturn($ajax);
                    die();
                }
                else
                {
                    $id=$setup_id;
                }
            }
            else
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = $time;
                Query_helper::update($this->config->item('table_tm_farmers'),$data,array("id = ".$id));
            }
            $previous_varieties=array();//active and inactive
            $results=Query_helper::get_info($this->config->item('table_tm_farmer_varieties'),'*',array('setup_id ='.$id));
            foreach($results as $result)
            {
                $previous_varieties[$result['variety_id']]=$result;
            }
            $this->db->where('setup_id',$id);
            $this->db->set('status', $this->config->item('system_status_delete'));
            $this->db->set('date_updated', $time);
            $this->db->set('user_updated', $user->user_id);
            $this->db->update($this->config->item('table_tm_farmer_varieties'));
            $variety_ids=$this->input->post('variety_ids');
            foreach($variety_ids as $variety_id)
            {
                $data=array();
                $data['setup_id']=$id;
                $data['variety_id']=$variety_id;
                $data['status']=$this->config->item('system_status_active');
                if(isset($previous_varieties[$variety_id]))
                {

                    $data['user_updated'] = $user->user_id;
                    $data['date_updated'] = $time;
                    Query_helper::update($this->config->item('table_tm_farmer_varieties'),$data,array("id = ".$previous_varieties[$variety_id]['id']));
                }
                else
                {
                    $data['user_created'] = $user->user_id;
                    $data['date_created'] =$time;
                    Query_helper::add($this->config->item('table_tm_farmer_varieties'),$data);
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
    }
    private function system_save_unfilled()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if(!(isset($this->permissions['add'])&&($this->permissions['add']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
            die();

        }

        {
            $data=$this->input->post('fsetup');
            $data['date_transplant']=System_helper::get_time($data['date_transplant']);
            $this->db->trans_start();  //DB Transaction Handle START
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = time();

            Query_helper::update($this->config->item('table_tm_farmers'),$data,array("id = ".$id));

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
    private function check_my_editable($customer)
    {

        if(($this->locations['division_id']>0)&&($this->locations['division_id']!=$customer['division_id']))
        {
            return false;
        }
        if(($this->locations['zone_id']>0)&&($this->locations['zone_id']!=$customer['zone_id']))
        {
            return false;
        }

        if(($this->locations['territory_id']>0)&&($this->locations['territory_id']!=$customer['territory_id']))
        {
            return false;
        }
        if(($this->locations['district_id']>0)&&($this->locations['district_id']!=$customer['district_id']))
        {
            return false;
        }

        if(($this->locations['upazilla_id']>0)&&($this->locations['upazilla_id']!=$customer['upazilla_id']))
        {
            return false;
        }

        return true;
    }
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('fsetup[year]',$this->lang->line('LABEL_YEAR'),'required|numeric');
        $this->form_validation->set_rules('fsetup[season_id]',$this->lang->line('LABEL_SEASON'),'required|numeric');
        $this->form_validation->set_rules('fsetup[upazilla_id]',$this->lang->line('LABEL_UPAZILLA_NAME'),'required|numeric');
        $this->form_validation->set_rules('fsetup[name]',"Farmer's Name",'required');
        $this->form_validation->set_rules('fsetup[date_sowing]',$this->lang->line('LABEL_DATE_SOWING'),'required');
        $this->form_validation->set_rules('fsetup[num_visits]',$this->lang->line('LABEL_NUM_VISITS'),'required|numeric');
        $this->form_validation->set_rules('fsetup[interval]',$this->lang->line('LABEL_INTERVAL'),'required|numeric');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        $variety_ids=$this->input->post('variety_ids');

        if(!((sizeof($variety_ids)>1)))
        {
            $this->message="Minimum 2 variety must be selected";
            return false;
        }

        $id=$this->input->post('id');
        if($id>0)
        {
            $this->db->from($this->config->item('table_tm_farmers').' tmf');
            $this->db->select('upazilla.name upazilla_name,upazilla.id upazilla_id');
            $this->db->select('d.name district_name,d.id district_id');
            $this->db->select('t.name territory_name,t.id territory_id');
            $this->db->select('zone.name zone_name,zone.id zone_id');
            $this->db->select('division.name division_name,division.id division_id');
            $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->where('tmf.id',$id);
            $result=$this->db->get()->row_array();
            if(!$result)
            {
                System_helper::invalid_try($this->config->item('system_save'),$id,'Hack trying to edit an id that does not exits');
                $this->message="Invalid Try";
                return false;
            }
            if(!$this->check_my_editable($result))
            {
                System_helper::invalid_try($this->config->item('system_save'),$id,'Hack To edit other customer that does not in my area');
                $this->message="Invalid Try";
                return false;
            }
        }
        //else
        {
            $fsetup=$this->input->post('fsetup');
            $year=$fsetup['year'];
            $season_id=$fsetup['season_id'];
            $upazilla_id=$fsetup['upazilla_id'];
            $this->db->from($this->config->item('table_tm_farmers').' tmf');
            $this->db->where('year',$year);
            $this->db->where('season_id',$season_id);
            $this->db->where('upazilla_id',$upazilla_id);
            $this->db->where('name',$fsetup['name']);
            $this->db->where('status',$this->config->item('system_status_active'));

            $this->db->where('id !=',$id);
            $result=$this->db->get()->row_array();
            if($result)
            {
                $this->message="This setup already Exits.Please edit that";
                return false;
            }
        }
        return true;
    }


}
