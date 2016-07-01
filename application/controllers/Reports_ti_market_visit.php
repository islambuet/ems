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
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
                if($this->locations['zone_id']>0)
                {
                    $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$this->locations['zone_id']));
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
                $item['location'].=$districts[$result['district_id']]['text'];
                $details['district_name']=$districts[$result['district_id']]['text'];
                $item['customer_name']=$customers[$result['host_id']]['text'];
               // $details['customer_name']=$customers[$result['host_id']]['text'];
            }
            elseif($result['host_type']==$this->config->item('system_host_type_other_customer'))
            {
                $item['location'].=$districts[$result['district_id']]['text'];
                $details['district_name']=$districts[$result['district_id']]['text'];
                $item['customer_name']=$other_customers[$result['host_id']]['text'];
             //   $details['customer_name']=$other_customers[$result['host_id']]['text'];
            }
            elseif($result['host_type']==$this->config->item('system_host_type_special'))
            {

                $item['customer_name']=$result['title'];
             //   $details['customer_name']=$result['title'];
                if($result['special_district_id']>0)
                {
                    $item['location'].=$districts[$result['district_id']]['text'];
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
            $details['user_created']= $users[$result['user_created']]['name'];
            $details['time_created']= System_helper::display_date_time($result['date_created']);;





            $item['details']=$details;
            $items[]=$item;
            /*$item['day']=date('l',$item['date']);
            $item['date']=System_helper::display_date($item['date']);
            if($item['host_type']==$this->config->item('system_host_type_customer'))
            {

                $item['customer_name']=$customers[$item['host_id']]['text'];
                if($customers[$item['host_id']]['status']!=$this->config->item('system_status_active'))
                {
                    $item['customer_name'].= '('.$customers[$item['host_id']]['status'].')';
                }
            }
            elseif($item['host_type']==$this->config->item('system_host_type_other_customer'))
            {

                $item['customer_name']=$other_customers[$item['host_id']]['text'];
                if($other_customers[$item['host_id']]['status']!=$this->config->item('system_status_active'))
                {
                    $item['customer_name'].= '('.$other_customers[$item['host_id']]['status'].')';
                }
            }
            elseif($item['host_type']==$this->config->item('system_host_type_special'))
            {
                $item['customer_name']=$item['title'];
                $item['district_name']=$item['special_district_name'];
            }*/
        }
        $this->jsonReturn($items);

        /*
        /*foreach($results as $result)
        {
            $user_ids[$result['user_created']]=$result['user_created'];
            $visits[$result['id']]['date']=$result['date'];
            $visits[$result['id']]['date_visit']=System_helper::display_date($result['date']).'<br>'.date('l',$result['date']);
            //$visits[$result['id']]['date_created']=System_helper::display_date_time($result['date_created']);
            $visits[$result['id']]['date_created']=$result['date_created'];
            $visits[$result['id']]['user_created']=$result['user_created'];
            $visits[$result['id']]['location']=$result['division_name'].'<br>'.$result['zone_name'].'<br>'.$result['territory_name'].'<br>'.$result['district_name'];
            $visits[$result['id']]['shift_name']=$result['shift_name'];
            $visits[$result['id']]['customer_name']=$result['customer_name'];
            $visits[$result['id']]['activities']=$result['activities'];
            $visits[$result['id']]['picture_url_activities']=base_url().'images/no_image.jpg';
            if($result['picture_url_activities'])
            {
                $visits[$result['id']]['picture_url_activities']=$result['picture_url_activities'];
            }
            $visits[$result['id']]['problem']=$result['activities'];
            $visits[$result['id']]['picture_url_problem']=base_url().'images/no_image.jpg';
            if($result['picture_url_problem'])
            {
                $visits[$result['id']]['picture_url_problem']=$result['picture_url_problem'];
            }
            $visits[$result['id']]['recommendation']=$result['recommendation'];
            if($result['solution'])
            {
                $user_ids[$result['user_solution']]=$result['user_solution'];
                $visits[$result['id']]['solutions'][]=array('solution'=>$result['solution'],'date_solution'=>$result['date_solution'],'user_solution'=>$result['user_solution']);
            }

        }
        $users=System_helper::get_users_info($user_ids);
        $count=0;
        foreach($visits as $visit)
        {
            $count++;
            $visit['sl_no']=$count;
            $visit['activities_picture']='<img src="'.$visit['picture_url_activities'].'" style="max-height: 100px;max-width: 133px;">';
            $visit['problem_picture']='<img src="'.$visit['picture_url_problem'].'" style="max-height: 100px;max-width: 133px;">';

            $html_row='<div class="pop_up" data-item-no="'.($count-1).'" style="height: 110px;width: 143px;cursor:pointer;">';
            if(isset($visit['solutions'])&&(sizeof($visit['solutions'])>0))
            {
                $html_row.=$visit['solutions'][sizeof($visit['solutions'])-1]['solution'];
            }
            else
            {
                $html_row.='No Solution given yet';
            }
            $html_row.='</div>';
            $visit['solution']=$html_row;
            $html_tooltip='';
            $html_tooltip.='<div>';
            $html_tooltip.='<div>'.$this->lang->line('LABEL_DATE').': '.System_helper::display_date($visit['date']).'</div>';
            $html_tooltip.='<div>'.$this->lang->line('LABEL_DAY').': '.date('l',$visit['date']).'</div>';
            $html_tooltip.='<div>'.$this->lang->line('LABEL_SHIFT').': '.$visit['shift_name'].'</div>';
            $html_tooltip.='<div>'.$this->lang->line('LABEL_CUSTOMER_NAME').': '.$visit['customer_name'].'</div>';
            $html_tooltip.='<div>Activities: <b>'.$visit['activities'].'</b></div>';
            $html_tooltip.='<div>Activities Picture :</div>';
            $html_tooltip.='<div><img src="'.$visit['picture_url_activities'].'" style="max-width: 100%;"></div>';
            $html_tooltip.='<div>Problem: <b>'.$visit['problem'].'</b></div>';
            $html_tooltip.='<div>Problem Picture :</div>';
            $html_tooltip.='<div><img src="'.$visit['picture_url_problem'].'" style="max-width: 100%;"></div>';
            $html_tooltip.='<div>Recommendation: <b>'.$visit['recommendation'].'</b></div>';
            $html_tooltip.='<div>Recommendation By: '.$users[$visit['user_created']]['name'].'</div>';
            $html_tooltip.='<div>Recommendation Time: '.System_helper::display_date_time($visit['date_created']).'</div>';

            if(isset($visit['solutions'])&&(sizeof($visit['solutions'])>0))
            {
                $html_tooltip.='<div>Solutions: </div>';
                foreach($visit['solutions'] as $solution)
                {
                    $html_tooltip.='<div>'.$users[$solution['user_solution']]['name'].' at '.System_helper::display_date_time($solution['date_solution']).'</div>';
                    $html_tooltip.='<div><b>'.$solution['solution'].'</b></div>';
                }
            }
            else
            {
                $html_tooltip.='<div>Problem: <b>No Solution given yet</b></div>';
            }

            $html_tooltip.='</div>';
            $visit['details']=$html_tooltip;
            $items[]=$visit;
        }*/

    }
}
