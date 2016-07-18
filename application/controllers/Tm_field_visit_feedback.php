<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tm_field_visit_feedback extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_field_visit_feedback');
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
        $this->controller_url='tm_field_visit_feedback';
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
            $data['title']="Field Visit Feedback List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_field_visit_feedback/list",$data,true));
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
        //$this->db->from($this->config->item('table_csetup_other_customers').' cus');
        $this->db->from($this->config->item('table_tm_farmers').' tmf');
        $this->db->select('tmf.*');
        $this->db->select('upazilla.name upazilla_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');

        $this->db->select('season.name season_name');
        $this->db->select('count(distinct vp.day_no) num_visit_done',true);
        $this->db->select('count(distinct case when vp.user_feedback>0 then vp.day_no end) num_visit_done_feedback',true);

        $this->db->select('count(distinct vfp.picture_id) num_fruit_picture',false);
        $this->db->select('count(distinct case when vfp.user_feedback>0 then vfp.picture_id end) num_fruit_picture_feedback',false);

        $this->db->select('count(distinct case when vdp.status="Active" then vdp.id end) num_disease_picture',false);
        $this->db->select('count(distinct case when vdp.status="Active" and vdp.user_feedback>0 then vdp.id end) num_disease_picture_feedback',false);

        $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_setup_tm_seasons').' season','season.id =tmf.season_id','INNER');
        $this->db->join($this->config->item('table_tm_visits_picture').' vp','tmf.id =vp.setup_id','LEFT');
        $this->db->join($this->config->item('table_tm_visits_fruit_picture').' vfp','tmf.id =vfp.setup_id','LEFT');
        $this->db->join($this->config->item('table_tm_visits_disease_picture').' vdp','tmf.id =vdp.setup_id','LEFT');
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
        $this->db->order_by('tmf.id','DESC');
        $this->db->group_by('tmf.id');
        $items=$this->db->get()->result_array();

        foreach($items as &$item)
        {
            $item['date_sowing']=System_helper::display_date($item['date_sowing']);
            if (($item['num_visit_done']!=$item['num_visit_done_feedback'])||($item['num_fruit_picture']!=$item['num_fruit_picture_feedback'])||($item['num_disease_picture']!=$item['num_disease_picture_feedback']))
            {
                $item['feedback_require']='Yes';
            }
            else
            {
                $item['feedback_require']='No';
            }

        }

        $this->jsonReturn($items);
    }
    private function system_edit($id)
    {
        if((isset($this->permissions['add'])&&($this->permissions['add']==1))||(isset($this->permissions['edit'])&&($this->permissions['edit']==1)))
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
            $this->db->from($this->config->item('table_tm_farmer_varieties').' tfv');
            $this->db->select('tfv.*');
            $this->db->select('v.name variety_name,v.whose');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tfv.variety_id','INNER');
            $this->db->where('tfv.setup_id',$setup_id);
            $this->db->where('tfv.status',$this->config->item('system_status_active'));
            $this->db->order_by('v.whose ASC');
            $this->db->order_by('v.ordering ASC');
            $results=$this->db->get()->result_array();
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
            $data['visits_picture']=array();
            $results=Query_helper::get_info($this->config->item('table_tm_visits_picture'),'*',array('setup_id ='.$setup_id));
            foreach($results as $result)
            {
                $data['visits_picture'][$result['day_no']][$result['variety_id']]=$result;
            }
            $data['fruits_picture_headers']=Query_helper::get_info($this->config->item('table_setup_tm_fruit_picture'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            $data['fruits_picture']=array();
            $results=Query_helper::get_info($this->config->item('table_tm_visits_fruit_picture'),'*',array('setup_id ='.$setup_id));
            foreach($results as $result)
            {
                $data['fruits_picture'][$result['picture_id']][$result['variety_id']]=$result;
            }
            $data['disease_picture']=Query_helper::get_info($this->config->item('table_tm_visits_disease_picture'),'*',array('setup_id ='.$setup_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('id'));
            $data['users']=System_helper::get_users_info(array());

            $data['title']="Details of Field Visit";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_field_visit_feedback/add_edit",$data,true));
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
            $this->db->from($this->config->item('table_tm_farmer_varieties').' tfv');
            $this->db->select('tfv.*');
            $this->db->select('v.name variety_name,v.whose');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tfv.variety_id','INNER');
            $this->db->where('tfv.setup_id',$setup_id);
            $this->db->where('tfv.status',$this->config->item('system_status_active'));
            $this->db->order_by('v.whose ASC');
            $this->db->order_by('v.ordering ASC');
            $results=$this->db->get()->result_array();
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
            $data['visits_picture']=array();
            $results=Query_helper::get_info($this->config->item('table_tm_visits_picture'),'*',array('setup_id ='.$setup_id));
            foreach($results as $result)
            {
                $data['visits_picture'][$result['day_no']][$result['variety_id']]=$result;
            }
            $data['fruits_picture_headers']=Query_helper::get_info($this->config->item('table_setup_tm_fruit_picture'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            $data['fruits_picture']=array();
            $results=Query_helper::get_info($this->config->item('table_tm_visits_fruit_picture'),'*',array('setup_id ='.$setup_id));
            foreach($results as $result)
            {
                $data['fruits_picture'][$result['picture_id']][$result['variety_id']]=$result;
            }
            $data['disease_picture']=Query_helper::get_info($this->config->item('table_tm_visits_disease_picture'),'*',array('setup_id ='.$setup_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('id'));
            $data['users']=System_helper::get_users_info(array());

            $data['title']="Details of Field Visit";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_field_visit_feedback/details",$data,true));
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
    private function system_save()
    {
        $setup_id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        if(!((isset($this->permissions['edit'])&&($this->permissions['edit']==1))||(isset($this->permissions['add'])&&($this->permissions['add']==1))))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
            die();
        }
        $visit_remarks=$this->input->post('visit_remarks');
        $fruit_remarks=$this->input->post('fruit_remarks');
        $diseases=$this->input->post('disease');
        $this->db->trans_start();

        if(sizeof($visit_remarks)>0)
        {
            foreach($visit_remarks as $day_no=>$variety)
            {
                foreach($variety as $variety_id=>$feedback)
                {
                    if((strlen($feedback))>0)
                    {
                        $data=array();
                        $data['feedback']=$feedback;
                        $data['user_updated'] = $user->user_id;
                        $data['date_updated'] = $time;
                        $data['user_feedback'] = $user->user_id;
                        $data['date_feedback'] = $time;
                        Query_helper::update($this->config->item('table_tm_visits_picture'),$data,array("setup_id = ".$setup_id,'day_no ='.$day_no,'variety_id ='.$variety_id));
                    }
                }

            }
        }
        if(sizeof($fruit_remarks)>0)
        {
            foreach($visit_remarks as $picture_id=>$variety)
            {
                foreach($variety as $variety_id=>$feedback)
                {
                    if((strlen($feedback))>0)
                    {
                        $data=array();
                        $data['feedback']=$feedback;
                        $data['user_updated'] = $user->user_id;
                        $data['date_updated'] = $time;
                        $data['user_feedback'] = $user->user_id;
                        $data['date_feedback'] = $time;
                        Query_helper::update($this->config->item('table_tm_visits_fruit_picture'),$data,array("setup_id = ".$setup_id,'picture_id ='.$picture_id,'variety_id ='.$variety_id));
                    }
                }

            }
        }
        if((sizeof($diseases))>0)
        {
            foreach($diseases as $disease)
            {
                if((strlen($disease['feedback']))>0)
                {
                    $data=array();
                    $data['feedback']=$disease['feedback'];
                    $data['user_updated'] = $user->user_id;
                    $data['date_updated'] = $time;
                    $data['user_feedback'] = $user->user_id;
                    $data['date_feedback'] = $time;
                    Query_helper::update($this->config->item('table_tm_visits_disease_picture'),$data,array("id = ".$disease['id']));
                }
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


}
