<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_zi_market_visit extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_zi_market_visit');
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
        $this->controller_url='reports_zi_market_visit';
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
            $data['title']="Search ZI Visit";
            $ajax['status']=true;
            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
            }
            $fiscal_years=Query_helper::get_info($this->config->item('table_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }

            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("reports_zi_market_visit/search",$data,true));
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
            $data['title']="ZI Market Visit Report";
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_zi_market_visit/list",$data,true));
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
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        $this->db->from($this->config->item('table_tm_market_visit_zi').' mvz');

        $this->db->select('mvz.*');
        $this->db->select('CONCAT(cus.customer_code," - ",cus.name) customer_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->select('shift.name shift_name');
        $this->db->select('mvsz.solution solution,mvsz.date_created date_solution,mvsz.user_created user_solution');

        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvz.customer_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');

        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = mvz.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = mvz.shift_id','INNER');
        $this->db->join($this->config->item('table_tm_market_visit_solution_zi').' mvsz','mvz.id = mvsz.visit_id','LEFT');
        if($division_id>0)
        {
            $this->db->where('division.id',$division_id);
            if($zone_id>0)
            {
                $this->db->where('zone.id',$zone_id);
            }
        }
        if($date_start>0)
        {
            $this->db->where('mvz.date >=',$date_start);

        }
        if($date_end>0)
        {
            $this->db->where('mvz.date <=',$date_end);

        }
        $this->db->order_by('mvz.date DESC');
        $results=$this->db->get()->result_array();
        $visits=array();
        $user_ids=array();
        foreach($results as $result)
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
        }
        $this->jsonReturn($items);
    }
}
