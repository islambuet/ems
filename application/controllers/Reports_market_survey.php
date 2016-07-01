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
            $varieties[$result['crop_id']][$result['crop_type_id']][$result['whose']][$result['variety_id']]=$result;
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
                $arm_total=0;
                $items[]=$this->get_variety_row(0,array('crop_name'=>'','crop_type_name'=>'','variety_name'=>'ARM Variety'),0);
                if(isset($types['ARM']))
                {
                    $count=0;
                    foreach($types['ARM'] as $variety)
                    {
                        $weight=0;
                        if(isset($quantity_survey[$type_id][$variety['variety_id']])&&($quantity_survey[$type_id][$variety['variety_id']]['weight_final']>0))
                        {
                            $weight=$quantity_survey[$type_id][$variety['variety_id']]['weight_final'];
                            $arm_total+=$weight;
                        }
                        $items[]=$this->get_variety_row($count,$variety,$weight);
                        $count++;
                    }

                }
                else
                {
                    $items[]=array('variety_name'=>'Not Found');
                }
                $items[]=$this->get_variety_row(0,array('crop_name'=>'','crop_type_name'=>'Total','variety_name'=>''),$arm_total);
                $competitor_total=0;
                $items[]=$this->get_variety_row(0,array('crop_name'=>'','crop_type_name'=>'','variety_name'=>'Competitor Variety'),0);
                if(isset($types['Competitor']))
                {
                    $count=0;
                    foreach($types['Competitor'] as $variety)
                    {
                        $weight=0;
                        if(isset($quantity_survey[$type_id][$variety['variety_id']])&&($quantity_survey[$type_id][$variety['variety_id']]['weight_final']>0))
                        {
                            $weight=$quantity_survey[$type_id][$variety['variety_id']]['weight_final'];
                            $competitor_total+=$weight;
                        }
                        $items[]=$this->get_variety_row($count,$variety,$weight);
                        $count++;
                    }

                }
                else
                {
                    $items[]=array('variety_name'=>'Not Found');
                }
                $items[]=$this->get_variety_row(0,array('crop_name'=>'','crop_type_name'=>'Total','variety_name'=>''),$competitor_total);

                $other_total=0;

                if(isset($quantity_survey[$type_id][0])&&($quantity_survey[$type_id][0]['weight_final']>0))
                {
                    $other_total=$quantity_survey[$type_id][0]['weight_final'];
                }
                $items[]=$this->get_variety_row(0,array('crop_name'=>'','crop_type_name'=>'','variety_name'=>'Others variety'),$other_total);

                $total_market_size=$arm_total+$competitor_total+$other_total;
                $items[]=$this->get_variety_row(0,array('crop_name'=>'','crop_type_name'=>'Total Market Size','variety_name'=>''),$total_market_size);
                if($total_market_size>0)
                {
                    $items[]=$this->get_variety_row(0,array('crop_name'=>'','crop_type_name'=>'','variety_name'=>'ARM % '),round($arm_total/$total_market_size*100,2));
                    $items[]=$this->get_variety_row(0,array('crop_name'=>'','crop_type_name'=>'','variety_name'=>'OP % '),round($other_total/$total_market_size*100,2));
                }
                else
                {
                    $items[]=array('crop_name'=>'','crop_type_name'=>'','variety_name'=>'ARM % ','weight_final'=>'N/A');
                    $items[]=array('crop_name'=>'','crop_type_name'=>'','variety_name'=>'OP % ','weight_final'=>'N/A');
                }
            }
        }

        $this->jsonReturn($items);
    }
    private function get_variety_row($first,$variety,$quantity_survey)
    {
        $row=array();
        if($first==0)
        {
            $row['crop_name']=$variety['crop_name'];
            $row['crop_type_name']=$variety['crop_type_name'];
        }
        else
        {
            $row['crop_name']='';
            $row['crop_type_name']='';
        }
        $row['variety_name']=$variety['variety_name'];
        if($quantity_survey==0)
        {
            $row['weight_final']='';
        }
        else
        {
            $row['weight_final']=$quantity_survey;
        }

        /*if(isset($quantity_survey[$variety['survey_id']][$variety['variety_id']]))
        {
            $row['weight_assumed']=$quantity_survey[$variety['survey_id']][$variety['variety_id']]['weight_assumed'];
            $row['weight_final']=$quantity_survey[$variety['survey_id']][$variety['variety_id']]['weight_final'];
        }*/

        return $row;
    }
    private function get_arm_header_row()
    {
/*{ name: 'id', type: 'int' },
{ name: 'crop_name', type: 'string' },
{ name: 'crop_type_name', type: 'string' },
{ name: 'variety_name', type: 'string' },
<?php
                    for($i=1;$i<=$max_customers_number;$i++)
                    {?>{ name: '<?php echo 'weight_sales_'.$i;?>', type: 'string' },
                        { name: '<?php echo 'weight_market_'.$i;?>', type: 'string' },
                    <?php
                    }
                ?>

{ name: 'weight_assumed', type: 'string' },
{ name: 'weight_final', type: 'string' },
{ name: 'unions', type: 'string' }*/
        $row=array();
        $row['crop_name']='ARM Variety';
        return $row;
    }

}
