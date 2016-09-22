<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_trainer_market_visit extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_trainer_market_visit');
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
        $this->controller_url='reports_trainer_market_visit';
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
            $data['title']="Search Trainer Visit";
            $ajax['status']=true;

            $fiscal_years=Query_helper::get_info($this->config->item('table_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("reports_trainer_market_visit/search",$data,true));
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
            $data['title']="Trainer Market Visit Report";
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_trainer_market_visit/list",$data,true));
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
    private function get_visit_setup($date,$setups)
    {
        foreach($setups as $setup)
        {
            if(($setup['date_start']<=$date) && ($setup['date_end']>=$date))
            {
                return $setup;
            }
        }
        return null;
    }

    public function get_items()
    {
        $items=array();
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        $this->db->from($this->config->item('table_setup_tm_market_visit_trainer').' mvsdi');

        $this->db->order_by('mvsdi.date_start DESC');
        $visit_setups=$this->db->get()->result_array();
        $this->db->from($this->config->item('table_tm_market_visit_trainer').' mvdi');

        $this->db->select('mvdi.*');
        $this->db->select('CONCAT(cus.customer_code," - ",cus.name) cus_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');

        $this->db->select('mvsdi.solution solution,mvsdi.date_created date_solution,mvsdi.user_created user_solution');

        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = mvdi.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        $this->db->join($this->config->item('table_tm_market_visit_solution_trainer').' mvsdi','mvdi.id = mvsdi.visit_id','LEFT');
        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvdi.customer_id','LEFT');

        if($date_start>0)
        {
            $this->db->where('mvdi.date >=',$date_start);

        }
        if($date_end>0)
        {
            $this->db->where('mvdi.date <=',$date_end);

        }
        $this->db->order_by('mvdi.date DESC');
        $results=$this->db->get()->result_array();
        $visits=array();
        $user_ids=array();
        foreach($results as $result)
        {
            $user_ids[$result['user_created']]=$result['user_created'];
            $visits[$result['id']]['date']=$result['date'];
            $visits[$result['id']]['date_visit']=System_helper::display_date($result['date']).'<br>'.date('l',$result['date']);

            $visits[$result['id']]['date_created']=$result['date_created'];
            $visits[$result['id']]['user_created']=$result['user_created'];
            $visits[$result['id']]['location']=$result['division_name'].'<br>'.$result['zone_name'].'<br>'.$result['territory_name'].'<br>'.$result['district_name'];
            if($result['customer_id']>0)
            {
                $visits[$result['id']]['customer_name']=$result['cus_name'];
            }
            else
            {
                $visits[$result['id']]['customer_name']=$result['customer_name'];
            }

            $visits[$result['id']]['activities']=$result['activities'];
            $visits[$result['id']]['picture_url_activities']=base_url().'images/no_image.jpg';
            if($result['picture_url_activities'])
            {
                $visits[$result['id']]['picture_url_activities']=$result['picture_url_activities'];
            }
            $visits[$result['id']]['problem']=$result['problem'];
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
        foreach($visits as $visit)
        {

            $visit['activities_picture']='<img src="'.$visit['picture_url_activities'].'" style="max-height: 100px;max-width: 133px;">';
            $visit['problem_picture']='<img src="'.$visit['picture_url_problem'].'" style="max-height: 100px;max-width: 133px;">';

            if(isset($visit['solutions'])&&(sizeof($visit['solutions'])>0))
            {
                $visit['solution']=$visit['solutions'][sizeof($visit['solutions'])-1]['solution'];
            }
            else
            {
                $visit['solution']='No Solution given yet';
            }
            $details=array();
            //$html_tooltip='';
            //$html_tooltip.='<div>';
            $visit_setup_info=$this->get_visit_setup($visit['date'],$visit_setups);
            if($visit_setup_info)
            {
                $details['has_setup']=true;
                $details['title']=$visit_setup_info['title'];
                $details['visit_purpose']=$visit_setup_info['visit_purpose'];
                $details['description']=$visit_setup_info['description'];
                $details['date_start']=System_helper::display_date($visit_setup_info['date_start']);
                $details['date_end']=System_helper::display_date($visit_setup_info['date_end']);
            }
            else
            {
                $details['has_setup']=false;
                $details['title']='';
                $details['visit_purpose']='';
                $details['description']='';
                $details['date_start']='';
                $details['date_end']='';
            }
            $details['date']=System_helper::display_date($visit['date']);
            $details['day']=date('l',$visit['date']);
            $details['customer_name']=$visit['customer_name'];
            $details['activities']=$visit['activities'];
            $details['activities_picture']=$visit['picture_url_activities'];
            $details['problem']=$visit['problem'];
            $details['problem_picture']=$visit['picture_url_problem'];
            $details['recommendation']=$visit['recommendation'];
            $details['user_created']=$users[$visit['user_created']]['name'];
            $details['time_created']=System_helper::display_date_time($visit['date_created']);
            $details['solutions']=array();

            if(isset($visit['solutions'])&&(sizeof($visit['solutions'])>0))
            {

                foreach($visit['solutions'] as $solution)
                {
                    $info=array();
                    $info['created_user']=$users[$solution['user_solution']]['name'];
                    $info['created_time']=System_helper::display_date_time($solution['date_solution']);
                    $info['solution']=$solution['solution'];
                    $details['solutions'][]=$info;                }
            }
            $visit['details']=$details;
            $items[]=$visit;
        }
        $this->jsonReturn($items);
    }
}
