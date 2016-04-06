<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_primary_market_survey extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_primary_market_survey');
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
        $this->controller_url='reports_primary_market_survey';
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
            $data['title']="Search Survey";
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

            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("reports_primary_market_survey/search",$data,true));
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

            if(!($reports['upazilla_id']>0))
            {
                $ajax['status']=false;
                $ajax['system_message']="Please Select Up-to upazila";
                $this->jsonReturn($ajax);
            }
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
            $data['title']="Primary Market Survey Report";

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_primary_market_survey/list",$data,true));

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

        $this->db->from($this->config->item('table_setup_classification_varieties').' v');
        $this->db->select('sp.id survey_id,sp.crop_type_id,sp.union_ids');
        $this->db->select('v.name variety_name,v.whose,v.id variety_id');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->select('crop.name crop_name,crop.id crop_id');
        $this->db->join($this->config->item('table_survey_primary').' sp','sp.crop_type_id = v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
        $this->db->where('sp.year',$year);
        $this->db->where('sp.upazilla_id',$upazilla_id);
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
        //$this->db->order_by('v.ordering','ASC');
        $results=$this->db->get()->result_array();
        if(!$results)
        {
            $this->jsonReturn($items);
        }
        $union_ids=array();
        $types_union_ids=array();
        $survey_ids=array();
        $varieties=array();
        foreach($results as $result)
        {
            if(strlen($result['union_ids'])>0)
            {
                $ids=json_decode($result['union_ids'],true);
                foreach($ids as $id)
                {
                    $union_ids[$id]=$id;
                    $types_union_ids[$result['crop_type_id']][$id]=$id;
                }
            }
            $survey_ids[$result['survey_id']]=$result['survey_id'];
            $varieties[$result['crop_id']][$result['crop_type_id']][$result['whose']][$result['variety_id']]=$result;
        }
        $unions=array();
        if(sizeof($union_ids)>0)
        {
            $this->db->from($this->config->item('table_setup_location_unions'));
            $this->db->select('id,name');
            $this->db->where_in('id',$union_ids);
            $results=$this->db->get()->result_array();
            foreach($results as $result)
            {
                $unions[$result['id']]=$result['name'];

            }
        }
        $customer_survey=array();

        $this->db->from($this->config->item('table_survey_primary_customer_survey').' spcs');
        $this->db->select('spcs.*');
        $this->db->where_in('survey_id',$survey_ids);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $customer_survey[$result['survey_id']][$result['variety_id']][$result['customer_no']]=$result;

        }
        $quantity_survey=array();
        $this->db->from($this->config->item('table_survey_primary_quantity_survey').' spqs');
        $this->db->select('spqs.*');
        $this->db->where_in('survey_id',$survey_ids);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $quantity_survey[$result['survey_id']][$result['variety_id']]=$result;

        }

//        echo '<PRE>';
//        print_r($quantity_survey);
//        echo '</PRE>';

        foreach($varieties as $crops)
        {
            foreach($crops as $type_id=>$types)
            {
                $union_names=',';
                if(isset($types_union_ids[$type_id]))
                {
                    foreach($types_union_ids[$type_id] as $id)
                    {
                        $union_names.=$unions[$id].',';
                    }
                }
                $survey_id=0;
                $items[]=array('variety_name'=>'ARM Variety','unions'=>trim($union_names,','));
                if(isset($types['ARM']))
                {
                    $count=0;
                    foreach($types['ARM'] as $variety)
                    {
                        $survey_id=$variety['survey_id'];
                        $items[]=$this->get_variety_row($count,$variety,$customer_survey,$quantity_survey);
                        $count++;
                    }

                }
                else
                {
                    $items[]=array('variety_name'=>'Not Found');
                }
                $items[]=array('variety_name'=>'Competitor Variety');
                if(isset($types['Competitor']))
                {
                    $count=0;
                    foreach($types['Competitor'] as $variety)
                    {
                        $survey_id=$variety['survey_id'];
                        $items[]=$this->get_variety_row($count,$variety,$customer_survey,$quantity_survey);
                        $count++;
                    }

                }
                else
                {
                    $items[]=array('variety_name'=>'Not Found');
                }
                $items[]=array('variety_name'=>'Others variety');
                $v=array();
                $v['variety_id']=0;
                $v['crop_name']='';
                $v['crop_type_name']='';
                $v['variety_name']='Others';
                $v['survey_id']=$survey_id;
                $items[]=$this->get_variety_row(0,$v,$customer_survey,$quantity_survey);
            }
        }

        $this->jsonReturn($items);
    }
    private function get_variety_row($first,$variety,$customer_survey,$quantity_survey)
    {
        $row=array();
        if($first==0)
        {
            $row['crop_name']=$variety['crop_name'];
            $row['crop_type_name']=$variety['crop_type_name'];
        }
        $row['variety_name']=$variety['variety_name'];
        for($i=1;$i<=$this->config->item('system_msurvey_customers_num');$i++)
        {
            if(isset($customer_survey[$variety['survey_id']][$variety['variety_id']][$i]))
            {
                $row['weight_sales_'.$i]=$customer_survey[$variety['survey_id']][$variety['variety_id']][$i]['weight_sales'];
                $row['weight_market_'.$i]=$customer_survey[$variety['survey_id']][$variety['variety_id']][$i]['weight_market'];
            }
        }
        if(isset($quantity_survey[$variety['survey_id']][$variety['variety_id']]))
        {
            $row['weight_assumed']=$quantity_survey[$variety['survey_id']][$variety['variety_id']]['weight_assumed'];
            $row['weight_final']=$quantity_survey[$variety['survey_id']][$variety['variety_id']]['weight_final'];
        }

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
