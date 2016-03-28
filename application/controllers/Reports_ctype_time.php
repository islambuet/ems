<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_ctype_time extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_ctype_time');
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
        $this->controller_url='reports_ctype_time';
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
            $data['title']="Search Varieties";
            $ajax['status']=true;
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
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array());
            $data['ranges']=Query_helper::get_info($this->config->item('table_basic_setup_vcolors'),array('days value','name text'),array(),0,0,array('days'));
            $data['date_report']=System_helper::display_date(time());

            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("reports_ctype_time/search",$data,true));
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
            $reports['date_report']=System_helper::get_time($reports['date_report']);
            $reports['date_report']=$reports['date_report']+3600*24-1;
            $keys=',';

            foreach($reports as $elem=>$value)
            {
                $keys.=$elem.":'".$value."',";
            }

            $data['keys']=trim($keys,',');


            $ajax['status']=true;
            $data['title']="Season wise variety Report";
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_ctype_time/list",$data,true));
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
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $report_range=$this->input->post('report_range');

        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $date_report=$this->input->post('date_report');

        $colors=Query_helper::get_info($this->config->item('table_basic_setup_vcolors'),'*',array(),0,0,array('days'));

        //getting date ranges
        $this->db->from($this->config->item('table_setup_classification_variety_time').' vt');
        $this->db->select('vt.crop_type_id');
        $this->db->select('MIN(vt.date_start) date_start');
        $this->db->select('MAX(vt.date_end) date_end');

        $this->db->where('vt.revision',1);

        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =vt.crop_type_id','INNER');

        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = vt.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        if($division_id>0)
        {
            $this->db->where('zone.division_id',$division_id);
            if($zone_id>0)
            {
                $this->db->where('zone.id',$zone_id);
                if($territory_id>0)
                {
                    $this->db->where('t.id',$territory_id);
                }
            }
        }
        if($crop_id>0)
        {
            $this->db->where('crop_type.crop_id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
            }
        }
        $this->db->group_by('vt.crop_type_id');

        $results=$this->db->get()->result_array();
        $distances=array();
        foreach($results as $result)
        {
            $info=array();
            $info['distance']=$this->get_distance_from_date($result['date_start'],$result['date_end'],$date_report);
            $color=$this->get_colors($info['distance'],$colors);
            $info['color_code']=$color['color_code'];
            $info['days']=$color['days'];
            $distances[$result['crop_type_id']]=$info;
        }


        $this->db->from($this->config->item('table_setup_classification_varieties').' v');

        $this->db->select('v.name variety_name,v.id variety_id');
        $this->db->select('crop_type.name crop_type_name,crop_type.id crop_type_id');
        $this->db->select('crop.name crop_name,crop.id crop_id');

        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');

        $this->db->where('v.whose','ARM');
        $this->db->where('v.status',$this->config->item('system_status_active'));


        if($crop_id>0)
        {
            $this->db->where('crop.id',$crop_id);
        }
        if($crop_type_id>0)
        {
            $this->db->where('crop_type.id',$crop_type_id);
        }
        if($variety_id>0)
        {
            $this->db->where('v.id',$variety_id);
        }
        $this->db->order_by('crop.ordering,crop.id,crop_type.ordering,crop_type.id,v.ordering,v.id');
        $results=$this->db->get()->result_array();
        if(!$results)
        {
            $this->jsonReturn($items);
        }
        $count=0;
        $prev_crop_name='';
        $prev_crop_type_name='';
        foreach($results as $result)
        {
            $info=$this->get_color_and_is_item($report_range,$result,$distances);
            if($info['is_element'])
            {
                if($count>0)
                {
                    if($prev_crop_name!=$result['crop_name'])
                    {
                        $prev_crop_name=$result['crop_name'];
                        $prev_crop_type_name=$result['crop_type_name'];

                    }
                    elseif($prev_crop_type_name!=$result['crop_type_name'])
                    {
                        $prev_crop_type_name=$result['crop_type_name'];
                        $result['crop_name']='';
                    }
                    else
                    {
                        $result['crop_name']='';
                        $result['crop_type_name']='';
                    }

                }
                else
                {
                    $prev_crop_name=$result['crop_name'];
                    $prev_crop_type_name=$result['crop_type_name'];;
                }
                $count++;
                $result['color_code']=$info['color_code'];
                $items[]=$result;
            }
        }



        $this->jsonReturn($items);
    }
    private function get_distance_from_date($date_start,$date_end,$date_now)
    {
        $date_70=strtotime(date('d-M',$date_now).'-1970');
        $date_71=strtotime(date('d-M',$date_now).'-1971');
        if(($date_70>=$date_start)&&($date_70<=$date_end))
        {
            return 0;
        }
        elseif(($date_71>=$date_start)&&($date_71<=$date_end))
        {
            return 0;
        }
        if($date_70>$date_start)
        {
            $date_70=strtotime(date('d-M',$date_now).'-1969');
            $date_71=strtotime(date('d-M',$date_now).'-1970');
        }
        if(($date_start-$date_70)>($date_71-$date_end))
        {
            return ceil(($date_71-$date_end)/(24*3600));
        }
        else
        {
            return (ceil(($date_start-$date_70)/(24*3600)))*-1;
        }


    }
    private function get_color_and_is_item($report_range,$item,$distances)
    {
        $info['is_element']=false;
        $info['color_code']='';
        if($report_range=='')
        {
            $info['is_element']=true;
            if(isset($distances[$item['crop_type_id']]))
            {
                $info['color_code']=$distances[$item['crop_type_id']]['color_code'];
            }

        }
        else
        {
            if(isset($distances[$item['crop_type_id']]))
            {
                if($distances[$item['crop_type_id']]['days']==$report_range)
                {
                    $info['is_element']=true;
                    $info['color_code']=$distances[$item['crop_type_id']]['color_code'];
                }
            }
        }
        return $info;

    }
    private function get_colors($distance,$colors)
    {
        $size=sizeof($colors);
        if($distance>$colors[$size-1]['days'])
        {
            return $colors[0];
        }
        else
        {
            for($i=0;$i<($size-1);$i++)
            {
                if($colors[$i]['days']==$distance)
                {
                    return $colors[$i];
                }
                elseif($distance<0)
                {
                    if(($colors[$i]['days']<=$distance)&&($colors[$i+1]['days']>$distance))
                    {
                        return $colors[$i];
                    }
                }
                elseif($distance>0)
                {
                    if(($colors[$i]['days']<$distance)&&($colors[$i+1]['days']>=$distance))
                    {
                        return $colors[$i+1];
                    }
                }

            }
        }

    }

}
