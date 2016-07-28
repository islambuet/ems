<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_market_survey extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_market_survey');
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
        $this->controller_url='reports_market_survey';
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
            $data['title']="Market Survey Report Search";
            $ajax['status']=true;
            $data['years']=Query_helper::get_info($this->config->item('table_survey_primary'),array('Distinct(year)'),array());

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

            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("reports_market_survey/search",$data,true));
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

            if(!($reports['year']>0))
            {
                $ajax['status']=false;
                $ajax['system_message']="Please Select a year";
                $this->jsonReturn($ajax);
            }
            $keys=',';

            foreach($reports as $elem=>$value)
            {
                $keys.=$elem.":'".$value."',";
            }

            $data['keys']=trim($keys,',');

            $data['max_customers_number']=$this->config->item('system_msurvey_customers_num');
            $data['customers']=array();
            $customers=Query_helper::get_info($this->config->item('table_survey_primary_customers'),'*',array('year ='.$reports['year'],'upazilla_id ='.$reports['upazilla_id']));
            foreach($customers as $customer)
            {
                $data['customers'][$customer['customer_no']]=$customer;
            }
            $data['title']="Market Survey Report";

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_market_survey/list",$data,true));

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
        //$this->db->join($this->config->item('table_survey_primary').' sp','sp.crop_type_id = v.crop_type_id','INNER');
        //$this->db->where('sp.upazilla_id',$upazilla_id);
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

        $this->db->from($this->config->item('table_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name,v.whose');
        $this->db->select('crop_type.name crop_type_name,crop_type.id crop_type_id');
        $this->db->select('crop.name crop_name,crop.id crop_id');

        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');


        if($crop_id>0)
        {
            $this->db->where('crop.id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
                if($variety_id>0)
                {
                    $this->db->where('v.id',$variety_id);
                }
            }
        }
        $this->db->where('(whose ="ARM" OR whose="Competitor")');

        $this->db->order_by('crop.ordering','ASC');
        $this->db->order_by('crop_type.ordering','ASC');
        $this->db->order_by('v.ordering','ASC');
        $results=$this->db->get()->result_array();
        if(!$results)
        {
            $this->jsonReturn($items);
        }
        $varieties=array();
        foreach($results as $result)
        {
            $varieties[$result['crop_id']][$result['crop_type_id']]['crop_name']=$result['crop_name'];
            $varieties[$result['crop_id']][$result['crop_type_id']]['crop_type_name']=$result['crop_type_name'];
            $varieties[$result['crop_id']][$result['crop_type_id']][$result['whose']][]=$result;
        }

        $quantity_survey=array();
        $this->db->from($this->config->item('table_survey_primary_quantity_survey').' spqs');
        $this->db->select('spqs.variety_id');
        $this->db->select('SUM(spqs.weight_final) weight_final');

        $this->db->select('sp.crop_type_id');
        $this->db->join($this->config->item('table_survey_primary').' sp','sp.id =spqs.survey_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =sp.crop_type_id','INNER');

        $this->db->join($this->config->item('table_setup_location_upazillas').' upz','upz.id = sp.upazilla_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upz.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        //$this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        if($crop_id>0)
        {
            $this->db->where('crop_type.crop_id',$crop_id);
            if($crop_type_id>0)
            {
                $this->db->where('crop_type.id',$crop_type_id);
            }
        }

        if($division_id>0)
        {
            $this->db->where('zone.division_id',$division_id);
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
                            $this->db->where('upz.id',$upazilla_id);
                        }
                    }
                }
            }
        }

        $this->db->group_by('sp.crop_type_id');
        $this->db->group_by('spqs.variety_id');
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $quantity_survey[$result['crop_type_id']][$result['variety_id']]=$result;

        }


        foreach($varieties as $crops)
        {
            foreach($crops as $type_id=>$types)
            {
                $arm_size=0;
                $competitor_size=0;
                if(isset($types['ARM']))
                {
                    $arm_size=sizeof($types['ARM']);
                }
                if(isset($types['Competitor']))
                {
                    $competitor_size=sizeof($types['Competitor']);
                }
                $arm_total=0;
                $competitor_total=0;
                $other_total=0;
                for($i=0;$i<max($arm_size,$competitor_size,1);$i++)
                {
                    $item=array();
                    $item['crop_name']='';
                    $item['crop_type_name']='';
                    $item['arm_variety_name']='';
                    $item['arm_weight']='';
                    $item['competitor_variety_name']='';
                    $item['competitor_weight']='';
                    $item['op_weight']='';
                    if($i==0)
                    {
                        $item['crop_name']=$types['crop_name'];
                        $item['crop_type_name']=$types['crop_type_name'];
                        if(isset($quantity_survey[$type_id][0])&&($quantity_survey[$type_id][0]['weight_final']>0))
                        {
                            $other_total=$quantity_survey[$type_id][0]['weight_final'];
                            $item['op_weight']=$other_total;
                        }
                    }
                    if(isset($types['ARM'][$i]))
                    {
                        $item['arm_variety_name']=$types['ARM'][$i]['variety_name'];
                        if(isset($quantity_survey[$type_id][$types['ARM'][$i]['variety_id']])&&($quantity_survey[$type_id][$types['ARM'][$i]['variety_id']]['weight_final']>0))
                        {
                            $item['arm_weight']=$quantity_survey[$type_id][$types['ARM'][$i]['variety_id']]['weight_final'];
                            $arm_total+=$item['arm_weight'];
                        }

                    }
                    if(isset($types['Competitor'][$i]))
                    {
                        $item['competitor_variety_name']=$types['Competitor'][$i]['variety_name'];
                        if(isset($quantity_survey[$type_id][$types['Competitor'][$i]['variety_id']])&&($quantity_survey[$type_id][$types['Competitor'][$i]['variety_id']]['weight_final']>0))
                        {
                            $item['competitor_weight']=$quantity_survey[$type_id][$types['Competitor'][$i]['variety_id']]['weight_final'];
                            $competitor_total+=$item['competitor_weight'];
                        }
                    }
                    $items[]=$item;
                }
                //total row
                $item=array();
                $item['crop_name']='';
                $item['crop_type_name']='Total';
                $item['arm_variety_name']='';
                $item['arm_weight']=$arm_total;
                $item['competitor_variety_name']='';
                $item['competitor_weight']=$competitor_total;
                $item['op_weight']=$other_total;
                $items[]=$item;
                //percentage row
                $item=array();
                $item['crop_name']='';
                $item['crop_type_name']='Percentage';
                $item['arm_variety_name']='';
                $item['arm_weight']='N/A';
                $item['competitor_variety_name']='';
                $item['competitor_weight']='N/A';
                $item['op_weight']='N/A';
                $total_market_size=$arm_total+$competitor_total+$other_total;
                if($total_market_size>0)
                {
                    $item['arm_weight']=round($arm_total/$total_market_size*100,2);
                    $item['competitor_weight']=round($competitor_total/$total_market_size*100,2);
                    $item['op_weight']=round($other_total/$total_market_size*100,2);
                }
                $items[]=$item;
            }
        }

        $this->jsonReturn($items);
    }

}
