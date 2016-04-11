<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_popular_variety extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_popular_variety');
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
        $this->controller_url='reports_popular_variety';
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

            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("reports_popular_variety/search",$data,true));
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
            $keys=',';

            foreach($reports as $elem=>$value)
            {
                $keys.=$elem.":'".$value."',";
            }

            $data['keys']=trim($keys,',');
            $data['title']="Popular Variety Report";

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_popular_variety/list",$data,true));

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
        $year=$this->input->post('year');

        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $upazilla_id=$this->input->post('upazilla_id');

        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');

        $this->db->from($this->config->item('table_tm_popular_variety').' tmpv');
        $this->db->select('tmpv.*');
        $this->db->select('tmpvd.date_remarks,tmpvd.picture_url,tmpvd.remarks');

        $this->db->select('upazilla.name upazilla_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');

        $this->db->select('crop.name crop_name');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->select('v.name variety_name');

        $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmpv.upazilla_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_tm_popular_variety_details').' tmpvd','tmpv.id =tmpvd.setup_id','INNER');

        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =tmpv.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tmpv.variety_id','LEFT');
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
        if($crop_id>0)
        {
            $this->db->where('crop.id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
                if($variety_id>0)
                {
                    $this->db->where('tmpv.variety_id',$variety_id);
                }
            }
        }


        $this->db->where('tmpv.status',$this->config->item('system_status_active'));
        $this->db->where('tmpvd.status',$this->config->item('system_status_active'));
        $results=$this->db->get()->result_array();
        $pvs=array();

        foreach($results as $result)
        {
            $crop_info=$result['crop_name'].'<br>'.$result['crop_type_name'].'<br>';
            if($result['variety_id']>0)
            {
                $crop_info.=$result['variety_name'];
            }
            else
            {
                $crop_info.=$result['other_variety_name'];
            }
            $pvs[$result['id']]['crop_info']=$crop_info;
            $pvs[$result['id']]['location']=$result['division_name'].'<br>'.$result['zone_name'].'<br>'.$result['territory_name'].'<br>'.$result['district_name'].'<br>'.$result['upazilla_name'];
            $image=base_url().'images/no_image.jpg';
            if(strlen($result['picture_url'])>0)
            {
                $image=$result['picture_url'];
            }
            $pvs[$result['id']]['infos'][]=array('image'=>$image,'remarks'=>$result['remarks'],'date_remarks'=>System_helper::display_date($result['date_remarks']),'date_created'=>System_helper::display_date_time($result['date_created']));
        }
        foreach($pvs as $pv)
        {
            $item=array();
            $item['crop_info']=$pv['crop_info'];
            $item['location']=$pv['location'];
            $html_row='';
            $details=array();

            foreach($pv['infos'] as $i=>$info)
            {
                $html_row.='<div class="popular_popup" data-item-no="'.sizeof($items).'" data-info-no="'.$i.'" style="height: 125px;width: 133px;margin-right:10px;  float: left;cursor:pointer;">';
                $html_row.='<div style="height:100px;"><img src="'.$info['image'].'" style="max-height: 100px;max-width: 133px;"></div>';
                $html_row.='<div style="height: 25px;text-align: center; ">'.$info['date_remarks'].'</div>';
                $html_row.='</div>';
                $html_tooltip='';
                $html_tooltip.='<div>';
                $html_tooltip.='<div><img src="'.$info['image'].'" style="width: 100%;"></div>';
                $html_tooltip.='<div>Date: '.$info['date_remarks'].'</div>';
                $html_tooltip.='<div>Date Created: '.$info['date_created'].'</div>';
                $html_tooltip.='<div>Remarks: '.$info['remarks'].'</div>';
                $html_tooltip.='</div>';
                $details[]=$html_tooltip;

            }
            $item['images']=$html_row;
            $item['details']=$details;
            $items[]=$item;

        }
        $this->jsonReturn($items);
    }

}
