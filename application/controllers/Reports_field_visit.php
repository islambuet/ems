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
        elseif($action=="get_items")
        {
            $this->system_get_items();
        }
        /*elseif($action=="details")
        {
            $this->system_details($id);
        }*/
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
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id']));
                }
            }

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
            $data['title']="Field Visit Report";
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));
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

        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');


        $this->db->from($this->config->item('table_tm_farmers').' tmf');
        $this->db->select('tmf.*');
        $this->db->select('upazilla.name upazilla_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');

        $this->db->select('season.name season_name');
        $this->db->select('count(distinct vp.day_no) num_visit_done',true);
        //$this->db->select('count(distinct vfp.picture_id) num_fruit_picture',false);
        //$this->db->select('count(distinct case when vdp.status="Active" then vdp.id end) num_disease_picture',false);
        $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');


        $this->db->join($this->config->item('table_setup_tm_seasons').' season','season.id =tmf.season_id','INNER');
        $this->db->join($this->config->item('table_tm_visits_picture').' vp','tmf.id =vp.setup_id','LEFT');
        //$this->db->join($this->config->item('table_tm_visits_fruit_picture').' vfp','tmf.id =vfp.setup_id','LEFT');
        //$this->db->join($this->config->item('table_tm_visits_disease_picture').' vdp','tmf.id =vdp.setup_id','LEFT');
        if($division_id>0)
        {
            $this->db->where('division.id',$division_id);
            if($zone_id>0)
            {
                $this->db->where('zone.id',$zone_id);
                if($territory_id>0)
                {
                    $this->db->where('t.id',$territory_id);
                }
            }
        }
        $this->db->where('tmf.status',$this->config->item('system_status_active'));
        $this->db->order_by('tmf.id','DESC');
        $this->db->group_by('tmf.id');
        $items=$this->db->get()->result_array();
        //echo $this->db->last_query();
        $time=time();
        foreach($items as &$item)
        {
            $item['date_sowing']=System_helper::display_date($item['date_sowing']);

            /*$day=floor(($time-$item['date_sowing'])/(24*3600));
            $item['day']=$day;

            if(($day%$item['interval'])==0)
            {
                $item['color_background']='#E8C5D0';
            }
            elseif(($day%$item['interval'])==1)
            {
                $item['color_background']='#BAD1DB';
            }
            elseif(($item['interval']-($day%$item['interval']))==1)
            {
                $item['color_background']='#DAF2DB';
            }
            else
            {
                $item['color_background']='';
            }*/



        }

        $this->jsonReturn($items);
    }


}
