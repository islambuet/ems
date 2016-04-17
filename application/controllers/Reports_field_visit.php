<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_field_visit extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_field_visit');
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
        $this->controller_url='reports_field_visit';
    }

    public function index($action="search",$id=0)
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="list_variety")
        {
            $this->system_list_variety();
        }
        elseif($action=="list")
        {
            $this->system_list();
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
            $data['title']="Search";
            $ajax['status']=true;
            $data['years']=Query_helper::get_info($this->config->item('table_tm_farmers'),array('Distinct(year)'),array());

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
            $data['seasons']=Query_helper::get_info($this->config->item('table_setup_tm_seasons'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("reports_field_visit/search",$data,true));
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
    private function system_list_variety()
    {
        $filters=$this->input->post('report');
        //ARM

        $this->db->from($this->config->item('table_tm_farmers').' tmf');
        $this->db->select('v.name variety_name');
        $this->db->select('tmf.variety_id');
        $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tmf.variety_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');

        $this->db->join($this->config->item('table_setup_tm_seasons').' season','season.id =tmf.season_id','INNER');
        if($filters['division_id']>0)
        {
            $this->db->where('division.id',$filters['division_id']);
            if($filters['zone_id']>0)
            {
                $this->db->where('zone.id',$filters['zone_id']);
                if($filters['territory_id']>0)
                {
                    $this->db->where('t.id',$filters['territory_id']);
                    if($filters['district_id']>0)
                    {
                        $this->db->where('d.id',$filters['district_id']);
                        if($filters['upazilla_id']>0)
                        {
                            $this->db->where('upazilla.id',$filters['upazilla_id']);
                        }
                    }
                }
            }
        }
        if($filters['crop_id']>0)
        {
            $this->db->where('crop.id',$filters['crop_id']);
            if($filters['crop_type_id']>0)
            {
                $this->db->where('crop_type.id',$filters['crop_type_id']);
            }
        }
        if($filters['season_id']>0)
        {
            $this->db->where('tmf.season_id',$filters['season_id']);

        }
        if($filters['year']>0)
        {
            $this->db->where('tmf.year',$filters['year']);

        }
        $this->db->where('v.whose','ARM');
        $this->db->where('tmf.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('v.ordering','DESC');
        $this->db->group_by('tmf.variety_id');

        $data['arm_varieties']=$this->db->get()->result_array();
        //competitor
        $this->db->from($this->config->item('table_tm_farmers').' tmf');
        $this->db->select('tmf.variety_id');
        $this->db->select('v.name variety_name');
        $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tmf.variety_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');

        $this->db->join($this->config->item('table_setup_tm_seasons').' season','season.id =tmf.season_id','INNER');
        if($filters['division_id']>0)
        {
            $this->db->where('division.id',$filters['division_id']);
            if($filters['zone_id']>0)
            {
                $this->db->where('zone.id',$filters['zone_id']);
                if($filters['territory_id']>0)
                {
                    $this->db->where('t.id',$filters['territory_id']);
                    if($filters['district_id']>0)
                    {
                        $this->db->where('d.id',$filters['district_id']);
                        if($filters['upazilla_id']>0)
                        {
                            $this->db->where('upazilla.id',$filters['upazilla_id']);
                        }
                    }
                }
            }
        }
        if($filters['crop_id']>0)
        {
            $this->db->where('crop.id',$filters['crop_id']);
            if($filters['crop_type_id']>0)
            {
                $this->db->where('crop_type.id',$filters['crop_type_id']);
            }
        }
        if($filters['season_id']>0)
        {
            $this->db->where('tmf.season_id',$filters['season_id']);

        }
        if($filters['year']>0)
        {
            $this->db->where('tmf.year',$filters['year']);

        }
        $this->db->where('v.whose','Competitor');
        $this->db->where('tmf.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('v.ordering','DESC');
        $this->db->group_by('tmf.variety_id');
        $data['competitor_varieties']=$this->db->get()->result_array();
        $data['report']=$filters;
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#variety_list_container","html"=>$this->load->view("reports_field_visit/list_variety",$data,true));

        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->jsonReturn($ajax);
    }
    private function system_list()
    {

        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            $variety_ids=$this->input->post('variety_ids');

            if(!((sizeof($variety_ids)>0)))
            {
                $ajax['status']=false;
                $ajax['system_message']="Please Select at lease One Variety";
                $this->jsonReturn($ajax);
            }
            $filters=$this->input->post('report');
            $keys=',';
            foreach($filters as $elem=>$value)
            {
                $keys.=$elem.":'".$value."',";
            }
            $keys.="variety_ids:'".json_encode($variety_ids)."',";
            $data['keys']=trim($keys,',');

            $data['fruits_picture_headers']=Query_helper::get_info($this->config->item('table_setup_tm_fruit_picture'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
            $data['max_visits']=1;
            $data['max_diseases']=1;

            $this->db->from($this->config->item('table_tm_farmers').' tmf');
            $this->db->select('count(distinct case when vp.remarks IS NOT NULL or vp.picture_url IS NOT NULL then vp.id end) num_visit_done',true);
            $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tmf.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');

            $this->db->join($this->config->item('table_setup_tm_seasons').' season','season.id =tmf.season_id','INNER');
            $this->db->join($this->config->item('table_tm_visits_picture').' vp','tmf.id =vp.setup_id','LEFT');
            if($filters['division_id']>0)
            {
                $this->db->where('division.id',$filters['division_id']);
                if($filters['zone_id']>0)
                {
                    $this->db->where('zone.id',$filters['zone_id']);
                    if($filters['territory_id']>0)
                    {
                        $this->db->where('t.id',$filters['territory_id']);
                        if($filters['district_id']>0)
                        {
                            $this->db->where('d.id',$filters['district_id']);
                            if($filters['upazilla_id']>0)
                            {
                                $this->db->where('upazilla.id',$filters['upazilla_id']);
                            }
                        }
                    }
                }
            }
            if($filters['season_id']>0)
            {
                $this->db->where('tmf.season_id',$filters['season_id']);

            }
            if($filters['year']>0)
            {
                $this->db->where('tmf.year',$filters['year']);

            }
            $this->db->where('tmf.status !=',$this->config->item('system_status_delete'));
            $this->db->where_in('tmf.variety_id',$variety_ids);
            $this->db->group_by('tmf.id');
            $this->db->order_by('num_visit_done DESC');
            $result=$this->db->get()->row_array();
            if($result)
            {
                if($result['num_visit_done']>0)
                {
                    $data['max_visits']=$result['num_visit_done'];
                }

            }
            $this->db->from($this->config->item('table_tm_farmers').' tmf');
            $this->db->select('count(distinct case when vdp.status="Active" then vdp.id end) num_disease_picture',false);
            $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tmf.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');

            $this->db->join($this->config->item('table_setup_tm_seasons').' season','season.id =tmf.season_id','INNER');
            $this->db->join($this->config->item('table_tm_visits_disease_picture').' vdp','tmf.id =vdp.setup_id','LEFT');
            if($filters['division_id']>0)
            {
                $this->db->where('division.id',$filters['division_id']);
                if($filters['zone_id']>0)
                {
                    $this->db->where('zone.id',$filters['zone_id']);
                    if($filters['territory_id']>0)
                    {
                        $this->db->where('t.id',$filters['territory_id']);
                        if($filters['district_id']>0)
                        {
                            $this->db->where('d.id',$filters['district_id']);
                            if($filters['upazilla_id']>0)
                            {
                                $this->db->where('upazilla.id',$filters['upazilla_id']);
                            }
                        }
                    }
                }
            }
            if($filters['season_id']>0)
            {
                $this->db->where('tmf.season_id',$filters['season_id']);

            }
            if($filters['year']>0)
            {
                $this->db->where('tmf.year',$filters['year']);

            }
            $this->db->where('tmf.status !=',$this->config->item('system_status_delete'));
            $this->db->where_in('tmf.variety_id',$variety_ids);
            $this->db->group_by('tmf.id');
            $this->db->order_by('num_disease_picture DESC');
            $result=$this->db->get()->row_array();
            if($result)
            {
                if($result['num_disease_picture']>0)
                {
                    $data['max_diseases']=$result['num_disease_picture'];
                }

            }
            $data['title']="Field Visit Report";

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_field_visit/list",$data,true));

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
        $items=array();
        $user_ids=array();

        $year=$this->input->post('year');
        $season_id=$this->input->post('season_id');

        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $upazilla_id=$this->input->post('upazilla_id');

        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_ids=json_decode($this->input->post('variety_ids'),true);




        $this->db->from($this->config->item('table_tm_farmers').' tmf');
        $this->db->select('tmf.*');
        $this->db->select('upazilla.name upazilla_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->select('crop.name crop_name');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->select('v.name variety_name');

        $this->db->select('season.name season_name');
        $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tmf.variety_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');

        $this->db->join($this->config->item('table_setup_tm_seasons').' season','season.id =tmf.season_id','INNER');
        if($division_id>0)
        {
            $this->db->where('division.id',$division_id);
            if($zone_id>0)
            {
                $this->db->where('zone.id',$zone_id);
                if($territory_id>0)
                {
                    $this->db->where('t.id',$territory_id);
                    if($district_id>0)
                    {
                        $this->db->where('d.id',$district_id);
                        if($upazilla_id>0)
                        {
                            $this->db->where('upazilla.id',$upazilla_id);
                        }
                    }
                }
            }
        }
        if($season_id>0)
        {
            $this->db->where('tmf.season_id',$season_id);

        }
        if($year>0)
        {
            $this->db->where('tmf.year',$year);

        }
        $this->db->where('tmf.status !=',$this->config->item('system_status_delete'));
        $this->db->where_in('tmf.variety_id',$variety_ids);
        $this->db->order_by('v.whose','ASC');
        $this->db->order_by('v.ordering','DESC');

        $results=$this->db->get()->result_array();
        $setup_ids=array();
        foreach($results as $result)
        {
            $item['id']=$result['id'];
            $setup_ids[]=$result['id'];
            $item['year_season']=$result['year'].'<br>'.$result['season_name'];
            $item['crop_info']=$result['crop_name'].'<br>'.$result['crop_type_name'].'<br>'.$result['variety_name'];
            $item['location']=$result['division_name'].'<br>'.$result['zone_name'].'<br>'.$result['territory_name'].'<br>'.$result['district_name'].'<br>'.$result['upazilla_name'];
            $item['date_sowing']=$result['date_sowing'];
            $item['interval']=$result['interval'];
            $items[]=$item;
        }
        $this->db->from($this->config->item('table_tm_visits_picture').' vp');
        $this->db->select('vp.*');
        $this->db->where_in('vp.setup_id',$setup_ids);
        $this->db->order_by('vp.day_no','ASC');
        $results=$this->db->get()->result_array();
        $visit_infos=array();
        foreach($results as $result)
        {
            $visit_infos[$result['setup_id']][$result['day_no']]=$result;
            $user_ids[$result['user_created']]=$result['user_created'];
            if($result['user_feedback'])
            {
                $user_ids[$result['user_feedback']]=$result['user_feedback'];
            }
        }

        $this->db->from($this->config->item('table_tm_visits_fruit_picture').' vfp');
        $this->db->select('vfp.*');
        $this->db->where_in('vfp.setup_id',$setup_ids);
        $this->db->order_by('vfp.picture_id','ASC');
        $results=$this->db->get()->result_array();
        $fruit_infos=array();
        foreach($results as $result)
        {
            $fruit_infos[$result['setup_id']][$result['picture_id']]=$result;
            $user_ids[$result['user_created']]=$result['user_created'];
            if($result['user_feedback'])
            {
                $user_ids[$result['user_feedback']]=$result['user_feedback'];
            }
        }
        $this->db->from($this->config->item('table_tm_visits_disease_picture').' vdp');
        $this->db->select('vdp.*');
        $this->db->where_in('vdp.setup_id',$setup_ids);
        $results=$this->db->get()->result_array();
        $disease_infos=array();
        foreach($results as $result)
        {
            $disease_infos[$result['setup_id']][]=$result;
            $user_ids[$result['user_created']]=$result['user_created'];
            if($result['user_feedback'])
            {
                $user_ids[$result['user_feedback']]=$result['user_feedback'];
            }
        }
        $users=System_helper::get_users_info($user_ids);
        foreach($items as $i=>&$item)
        {
            if(isset($visit_infos[$item['id']]))
            {
                foreach($visit_infos[$item['id']] as $visit)
                {
                    $image=base_url().'images/no_image.jpg';
                    if(strlen($visit['picture_url'])>0)
                    {
                        $image=$visit['picture_url'];
                    }
                    $html_row='<div class="pop_up" data-item-no="'.$i.'" data-key="visit_pictures_'.$visit['day_no'].'" style="height: 125px;width: 133px;cursor:pointer;">';
                    $html_row.='<div style="height:100px;"><img src="'.$image.'" style="max-height: 100px;max-width: 133px;"></div>';
                    $html_row.='<div style="height: 25px;text-align: center; ">'.System_helper::display_date($item['date_sowing']+24*3600*$visit['day_no']*$item['interval']).'</div>';
                    $html_row.='</div>';
                    $item['visit_pictures_'.$visit['day_no']]=$html_row;
                    $html_tooltip='';
                    $html_tooltip.='<div>';
                    $html_tooltip.='<div><img src="'.$image.'" style="max-width: 100%;"></div>';
                    $html_tooltip.='<div style="text-align:center;margin-bottom:5px;">Date: '.System_helper::display_date($item['date_sowing']+24*3600*$visit['day_no']*$item['interval']).'</div>';
                    $html_tooltip.='<div style="width:50%;float:left;">';
                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').': <div  style="font-size: 15px;font-weight:bold;">'.$visit['remarks'].'</div></div>';
                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').' '.$this->lang->line('LABEL_ENTRY_TIME').': <div>'.System_helper::display_date_time($visit['date_created']).'</div></div>';
                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').' By: <div>'.$users[$visit['user_created']]['name'].'</div></div>';
                    $html_tooltip.='</div>';
                    $html_tooltip.='<div style="width:50%;float: right;">';
                    $feedback=$this->lang->line('LABEL_FEEDBACK_NOT_GIVEN');
                    $feedback_date='';
                    if(strlen($visit['feedback'])>0)
                    {
                        $feedback=$visit['feedback'];
                        $feedback_date=System_helper::display_date_time($visit['date_feedback']);
                    }
                    $html_tooltip.='<div>'.$this->lang->line('LABEL_FEEDBACK').': <div  style="font-size: 15px;font-weight:bold;">'.$feedback.'</div></div>';
                    if($feedback_date)
                    {
                        $html_tooltip.='<div>'.$this->lang->line('LABEL_FEEDBACK').' '.$this->lang->line('LABEL_ENTRY_TIME').': <div>'.$feedback_date.'</div></div>';
                        $html_tooltip.='<div>'.$this->lang->line('LABEL_FEEDBACK').' By: <div>'.$users[$visit['user_feedback']]['name'].'</div></div>';
                    }

                    $html_tooltip.='</div>';
                    $html_tooltip.='</div>';
                    $item['details']['visit_pictures_'.$visit['day_no']]=$html_tooltip;
                }
            }
            if(isset($fruit_infos[$item['id']]))
            {
                foreach($fruit_infos[$item['id']] as $visit)
                {
                    $image=base_url().'images/no_image.jpg';
                    if(strlen($visit['picture_url'])>0)
                    {
                        $image=$visit['picture_url'];
                    }
                    $html_row='<div class="pop_up" data-item-no="'.$i.'" data-key="fruit_pictures_'.$visit['picture_id'].'" style="height: 125px;width: 133px;cursor:pointer;">';
                    $html_row.='<div style="height:100px;"><img src="'.$image.'" style="max-height: 100px;max-width: 133px;"></div>';
                    $html_row.='<div style="height: 25px;text-align: center; ">'.System_helper::display_date($visit['date_created']).'</div>';
                    $html_row.='</div>';
                    $item['fruit_pictures_'.$visit['picture_id']]=$html_row;
                    $html_tooltip='';
                    $html_tooltip.='<div>';
                    $html_tooltip.='<div><img src="'.$image.'" style="max-width: 100%;"></div>';
                    $html_tooltip.='<div style="text-align:center;margin-bottom:5px;">Date: '.System_helper::display_date($visit['date_created']).'</div>';
                    $html_tooltip.='<div style="width:50%;float:left;">';
                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').': <div  style="font-size: 15px;font-weight:bold;">'.$visit['remarks'].'</div></div>';
                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').' '.$this->lang->line('LABEL_ENTRY_TIME').': <div>'.System_helper::display_date_time($visit['date_created']).'</div></div>';
                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').' By: <div>'.$users[$visit['user_created']]['name'].'</div></div>';
                    $html_tooltip.='</div>';
                    $html_tooltip.='<div style="width:50%;float: right;">';
                    $feedback=$this->lang->line('LABEL_FEEDBACK_NOT_GIVEN');
                    $feedback_date='';
                    if(strlen($visit['feedback'])>0)
                    {
                        $feedback=$visit['feedback'];
                        $feedback_date=System_helper::display_date_time($visit['date_feedback']);
                    }
                    $html_tooltip.='<div>'.$this->lang->line('LABEL_FEEDBACK').': <div  style="font-size: 15px;font-weight:bold;">'.$feedback.'</div></div>';
                    if($feedback_date)
                    {
                        $html_tooltip.='<div>'.$this->lang->line('LABEL_FEEDBACK').' '.$this->lang->line('LABEL_ENTRY_TIME').': <div>'.$feedback_date.'</div></div>';
                        $html_tooltip.='<div>'.$this->lang->line('LABEL_FEEDBACK').' By: <div>'.$users[$visit['user_feedback']]['name'].'</div></div>';
                    }

                    $html_tooltip.='</div>';
                    $html_tooltip.='</div>';
                    $item['details']['fruit_pictures_'.$visit['picture_id']]=$html_tooltip;
                }
            }
            if(isset($disease_infos[$item['id']]))
            {
                foreach($disease_infos[$item['id']] as $index=>$visit)
                {
                    $image=base_url().'images/no_image.jpg';
                    if(strlen($visit['picture_url'])>0)
                    {
                        $image=$visit['picture_url'];
                    }
                    $html_row='<div class="pop_up" data-item-no="'.$i.'" data-key="disease_pictures_'.$index.'" style="height: 125px;width: 133px;cursor:pointer;">';
                    $html_row.='<div style="height:100px;"><img src="'.$image.'" style="max-height: 100px;max-width: 133px;"></div>';
                    $html_row.='<div style="height: 25px;text-align: center; ">'.System_helper::display_date($visit['date_created']).'</div>';
                    $html_row.='</div>';
                    $item['disease_pictures_'.$index]=$html_row;
                    $html_tooltip='';
                    $html_tooltip.='<div>';
                    $html_tooltip.='<div><img src="'.$image.'" style="max-width: 100%;"></div>';
                    $html_tooltip.='<div style="text-align:center;margin-bottom:5px;">Date: '.System_helper::display_date($visit['date_created']).'</div>';
                    $html_tooltip.='<div style="width:50%;float:left;">';
                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').': <div  style="font-size: 15px;font-weight:bold;">'.$visit['remarks'].'</div></div>';
                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').' '.$this->lang->line('LABEL_ENTRY_TIME').': <div>'.System_helper::display_date_time($visit['date_created']).'</div></div>';
                    $html_tooltip.='<div>'.$this->lang->line('LABEL_REMARKS').' By: <div>'.$users[$visit['user_created']]['name'].'</div></div>';
                    $html_tooltip.='</div>';
                    $html_tooltip.='<div style="width:50%;float: right;">';
                    $feedback=$this->lang->line('LABEL_FEEDBACK_NOT_GIVEN');
                    $feedback_date='';
                    if(strlen($visit['feedback'])>0)
                    {
                        $feedback=$visit['feedback'];
                        $feedback_date=System_helper::display_date_time($visit['date_feedback']);
                    }
                    $html_tooltip.='<div>'.$this->lang->line('LABEL_FEEDBACK').': <div  style="font-size: 15px;font-weight:bold;">'.$feedback.'</div></div>';
                    if($feedback_date)
                    {
                        $html_tooltip.='<div>'.$this->lang->line('LABEL_FEEDBACK').' '.$this->lang->line('LABEL_ENTRY_TIME').': <div>'.$feedback_date.'</div></div>';
                        $html_tooltip.='<div>'.$this->lang->line('LABEL_FEEDBACK').' By: <div>'.$users[$visit['user_feedback']]['name'].'</div></div>';
                    }

                    $html_tooltip.='</div>';
                    $html_tooltip.='</div>';
                    $item['details']['disease_pictures_'.$index]=$html_tooltip;
                }
            }

        }
        $this->jsonReturn($items);
    }


}
