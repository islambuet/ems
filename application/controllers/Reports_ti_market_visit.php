<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_ti_market_visit extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_ti_market_visit');
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
        $this->controller_url='reports_ti_market_visit';
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
            $data['title']="TI Market Visit Report Search";
            $ajax['status']=true;
            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['customers']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id']));
                    if($this->locations['territory_id']>0)
                    {
                        $data['districts']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$this->locations['territory_id']),0,0,array('ordering ASC'));
                        if($this->locations['district_id']>0)
                        {
                            $data['customers']=Query_helper::get_info($this->config->item('table_csetup_customers'),array('id value','name text'),array('district_id ='.$this->locations['district_id'],'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
                        }
                    }
                }
            }
            $fiscal_years=Query_helper::get_info($this->config->item('table_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }

            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("reports_ti_market_visit/search",$data,true));
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

            $keys=',';

            foreach($reports as $elem=>$value)
            {
                $keys.=$elem.":'".$value."',";
            }

            $data['keys']=trim($keys,',');
            if(isset($reports['activities_picture']))
            {
                $data['activities_picture']=true;
            }
            else
            {
                $data['activities_picture']=false;
            }
            if(isset($reports['problem_picture']))
            {
                $data['problem_picture']=true;
            }
            else
            {
                $data['problem_picture']=false;
            }


            $ajax['status']=true;
            $data['title']="TI Market Visit Report";
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_ti_market_visit/list",$data,true));
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
        $district_id=$this->input->post('district_id');
        $customer_id=$this->input->post('customer_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        $results=Query_helper::get_info($this->config->item('table_csetup_customers'),array('id value','CONCAT(customer_code," - ",name) text','status'),array('status !="'.$this->config->item('system_status_delete').'"'));
        $customers=array();
        foreach($results as $result)
        {
            $customers[$result['value']]=$result;
        }
        $results=Query_helper::get_info($this->config->item('table_csetup_other_customers'),array('id value','name text','status'),array('status !="'.$this->config->item('system_status_delete').'"'));
        $other_customers=array();
        foreach($results as $result)
        {
            $other_customers[$result['value']]=$result;
        }
        $results=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('status !="'.$this->config->item('system_status_delete').'"'));
        $districts=array();
        foreach($results as $result)
        {
            $districts[$result['value']]=$result;
        }

        $users=System_helper::get_users_info(array());
        //solutions
        $this->db->from($this->config->item('table_tm_market_visit_solution_ti').' mvsti');
        $this->db->select('mvsti.solution,mvsti.date_created,mvsti.user_created,mvsti.visit_id');

        $this->db->join($this->config->item('table_tm_market_visit_ti').' mvti','mvti.id = mvsti.visit_id','INNER');
        $this->db->join($this->config->item('table_setup_tm_market_visit').' stmvti','stmvti.id = mvti.setup_id','INNER');

        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = stmvti.territory_id','INNER');
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
        if($date_start>0)
        {
            $this->db->where('mvti.date >=',$date_start);

        }
        if($date_end>0)
        {
            $this->db->where('mvti.date <=',$date_end);

        }
        $this->db->order_by('mvsti.id DESC');
        $results=$this->db->get()->result_array();
        $solutions=array();
        foreach($results as $result)
        {
            $solutions[$result['visit_id']][]=array('solution'=>$result['solution'],'created_time'=>System_helper::display_date_time($result['date_created']),'created_user'=>$users[$result['user_created']]['name']);
        }

        $this->db->from($this->config->item('table_tm_market_visit_ti').' mvt');
        $this->db->select('mvt.*');
        $this->db->select('stmv.host_type,stmv.host_id,stmv.district_id');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->select('shift.name shift_name');
        $this->db->join($this->config->item('table_setup_tm_market_visit').' stmv','stmv.id = mvt.setup_id','INNER');

        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = stmv.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = stmv.shift_id','INNER');


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
        if($date_start>0)
        {
            $this->db->where('mvt.date >=',$date_start);

        }
        if($date_end>0)
        {
            $this->db->where('mvt.date <=',$date_end);

        }
        $this->db->order_by('mvt.date DESC');
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $item=array();
            $details=array();
            $item['date_visit']=System_helper::display_date($result['date']).'<br>'.date('l',$result['date']);
            $details['date']=System_helper::display_date($result['date']);
            $details['day']=date('l',$result['date']);

            $item['location']=$result['division_name'].'<br>'.$result['zone_name'].'<br>'.$result['territory_name'].'<br>';
            $details['division_name']=$result['division_name'];
            $details['zone_name']=$result['zone_name'];
            $details['territory_name']=$result['territory_name'];

            if($result['host_type']==$this->config->item('system_host_type_customer'))
            {
                if(($customer_id>0)&&($result['host_id']!=$customer_id))
                {
                    continue;
                }
                elseif(($district_id>0)&&($result['district_id']!=$district_id))
                {
                    continue;
                }
                $item['location'].=$districts[$result['district_id']]['text'];
                $details['district_name']=$districts[$result['district_id']]['text'];
                $item['customer_name']=$customers[$result['host_id']]['text'];
               // $details['customer_name']=$customers[$result['host_id']]['text'];
            }
            elseif($result['host_type']==$this->config->item('system_host_type_other_customer'))
            {
                if(($customer_id>0)&&($result['host_id']!=$customer_id))
                {
                    continue;
                }
                elseif(($district_id>0)&&($result['district_id']!=$district_id))
                {
                    continue;
                }
                $item['location'].=$districts[$result['district_id']]['text'];
                $details['district_name']=$districts[$result['district_id']]['text'];
                $item['customer_name']=$other_customers[$result['host_id']]['text'];
             //   $details['customer_name']=$other_customers[$result['host_id']]['text'];
            }
            elseif($result['host_type']==$this->config->item('system_host_type_special'))
            {
                if($customer_id>0)
                {
                    continue;
                }
                elseif(($district_id>0)&&($result['special_district_id']!=$district_id))
                {
                    continue;
                }

                $item['customer_name']=$result['title'];
             //   $details['customer_name']=$result['title'];
                if($result['special_district_id']>0)
                {
                    $item['location'].=$districts[$result['special_district_id']]['text'];
                    $details['district_name']=$districts[$result['special_district_id']]['text'];
                }
                else
                {
                    $details['district_name']='';
                }
            }
            $item['shift_name']=$result['shift_name'];
            $item['activities']=$result['activities'];
            $image=base_url().'images/no_image.jpg';
            if(strlen($result['picture_url_activities'])>0)
            {
                $image=$result['picture_url_activities'];
            }
            $item['activities_picture']='<img style="max-width: 100%;max-height: 100%" src="'.$image.'">';
            $details['activities_picture']=$image;

            $item['problem']=$result['problem'];
            $image=base_url().'images/no_image.jpg';
            if(strlen($result['picture_url_problem'])>0)
            {
                $image=$result['picture_url_problem'];
            }
            $item['problem_picture']='<img style="max-width: 100%;max-height: 100%" src="'.$image.'">';
            $details['problem_picture']=$image;
            $item['recommendation']=$result['recommendation'];
            if(isset($solutions[$result['id']]))
            {
                $item['solution']=$solutions[$result['id']][0]['solution'];
                $details['solutions']=$solutions[$result['id']];
            }
            else
            {
                $item['solution']='';
                $details['solutions']=array();
            }
            $details['user_created']= $users[$result['user_created']]['name'];
            $details['time_created']= System_helper::display_date_time($result['date_created']);
            $item['details']=$details;
            $items[]=$item;

        }
        $this->jsonReturn($items);

    }
}
