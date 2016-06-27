<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tm_zi_market_visit extends Root_Controller
{
    private $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_zi_market_visit');
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
        $this->controller_url='tm_zi_market_visit';
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="search_list")
        {
            $this->system_search_list();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        /*elseif($action=="details")
        {
            $this->system_details($id);
        }*/
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_list();
        }
    }
    private function system_list()
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            $data['title']="Visit List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_zi_market_visit/list",$data,true));
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
        $this->db->from($this->config->item('table_tm_market_visit_zi').' mvzi');
        $this->db->select('mvzi.setup_details_id id,mvzi.title');
        $this->db->select('mvszid.date,mvszid.host_type');
        $this->db->select('CONCAT(cus.customer_code," - ",cus.name) customer_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->select('shift.name shift_name');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = mvzi.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = mvzi.territory_id','INNER');

        $this->db->join($this->config->item('table_setup_tm_market_visit_zi_details').' mvszid','mvszid.id = mvzi.setup_details_id','INNER');
        $this->db->join($this->config->item('table_setup_tm_market_visit_zi').' mvszi','mvszi.id = mvszid.setup_id','INNER');

        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = mvszi.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = mvszid.shift_id','INNER');
        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvszid.host_id and mvszid.host_type ="'.$this->config->item('system_host_type_customer').'"','LEFT');
        if($this->locations['division_id']>0)
        {
            $this->db->where('division.id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('zone.id',$this->locations['zone_id']);
            }
        }
        $this->db->order_by('mvzi.id DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['day']=date('l',$item['date']);
            $item['date']=System_helper::display_date($item['date']);
            if($item['host_type']==$this->config->item('system_host_type_special'))
            {
                $item['customer_name']=$item['title'];
            }
        }
        $this->jsonReturn($items);
        //$this->jsonReturn(array());

    }
    private function system_search()
    {
        if(isset($this->permissions['add'])&&($this->permissions['add']==1))
        {
            $data['title']="ZI Market Visit";
            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();

            if($this->locations['division_id']>0)
            {
                $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$this->locations['division_id']));
            }

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_zi_market_visit/search",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }

    }
    private function system_search_list()
    {
        $user = User_helper::get_user();
        $date=System_helper::get_time($this->input->post('date'));
        $zone_id=$this->input->post('zone_id');

        if(!$zone_id)
        {
            $ajax['status']=false;
            $ajax['system_message']='Please Select a Zone';
            $this->jsonReturn($ajax);
        }
        if(!$date)
        {
            $ajax['status']=false;
            $ajax['system_message']='Please Select a valid date';
            $this->jsonReturn($ajax);
        }
        if(($user->user_group!=1)&&($user->user_group!=2)&&($date>time()))
        {

            $ajax['status']=false;
            $ajax['system_message']='You cannot select future date';
            $this->jsonReturn($ajax);
        }
        $this->db->from($this->config->item('table_setup_tm_market_visit_zi_details').' mvszid');
        $this->db->select('mvszid.*');
        //$this->db->select('mvsz.*');
        $this->db->select('CONCAT(cus.customer_code," - ",cus.name) customer_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('shift.name shift_name');

        $this->db->join($this->config->item('table_setup_tm_market_visit_zi').' mvszi','mvszi.id = mvszid.setup_id','INNER');
        $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = mvszid.shift_id','INNER');
        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvszid.host_id and mvszid.host_type ="'.$this->config->item('system_host_type_customer').'"','LEFT');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','LEFT');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','LEFT');

        $this->db->where('mvszid.date',$date);
        $this->db->where('mvszi.status_approve',$this->config->item('system_status_approved'));
        $this->db->where('mvszi.zone_id',$zone_id);
        $data['schedules']=$this->db->get()->result_array();
        $data['visit_done']=array();
        if(sizeof($data['schedules'])>0)
        {
            $this->db->from($this->config->item('table_tm_market_visit_zi').' mvzi');
            $this->db->select('mvzi.*');
            $this->db->join($this->config->item('table_setup_tm_market_visit_zi_details').' mvszid','mvszid.id = mvzi.setup_details_id','INNER');
            $this->db->where('mvszid.setup_id',$data['schedules'][0]['setup_id']);
            $results=$this->db->get()->result_array();
            foreach($results as $result)
            {
                $data['visit_done'][]=$result['setup_details_id'];
            }
        }
        $data['title']='Schedule for '.$this->input->post('date').'('.date('l',$date).')';
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("tm_zi_market_visit/search_list",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->jsonReturn($ajax);


    }
    private function system_edit($id)
    {
        if(($this->input->post('id')))
        {
            $setup_details_id=$this->input->post('id');
        }
        else
        {
            $setup_details_id=$id;
        }
        $this->db->from($this->config->item('table_setup_tm_market_visit_zi_details').' mvzid');

        $this->db->select('mvzid.*');
        $this->db->select('shift.name shift_name');
        $this->db->select('cus.id customer_id');
        $this->db->select('CONCAT(cus.customer_code," - ",cus.name) customer_name');
        $this->db->select('d.id district_id,d.name district_name');
        $this->db->select('t.id territory_id,t.name territory_name');

        $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = mvzid.shift_id','INNER');
        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvzid.host_id and mvzid.host_type ="'.$this->config->item('system_host_type_customer').'"','LEFT');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','LEFT');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','LEFT');
        $this->db->where('mvzid.id',$setup_details_id);
        $data['visit']=$this->db->get()->row_array();
        if(!$data['visit'])
        {
            System_helper::invalid_try("Try to use non-existing",$setup_details_id);
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
        $this->db->from($this->config->item('table_setup_tm_market_visit_zi').' mvzi');
        $this->db->select('zone.id zone_id,zone.name zone_name');
        $this->db->select('division.id division_id,division.name division_name');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = mvzi.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        $this->db->where('mvzi.status_approve',$this->config->item('system_status_approved'));
        $this->db->where('mvzi.id',$data['visit']['setup_id']);
        $result=$this->db->get()->row_array();
        if(!$result)
        {
            System_helper::invalid_try("Try to use Non approval or not existing setup",$setup_details_id);
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
        $data['visit']['division_name']=$result['division_name'];
        $data['visit']['division_id']=$result['division_id'];
        $data['visit']['zone_id']=$result['zone_id'];
        $data['visit']['zone_name']=$result['zone_name'];
        $result=Query_helper::get_info($this->config->item('table_tm_market_visit_zi'),'*',array('setup_details_id ='.$setup_details_id),1);
        if($result)
        {
            if(!(isset($this->permissions['edit'])&&($this->permissions['edit']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
                die();
            }
            $data['title']="Edit Market Visit";
            if($data['visit']['host_type']=$this->config->item('system_status_config_custom'))
            {
                $data['visit']['territory_id']=$result['territory_id'];
                $data['visit']['district_id']=$result['district_id'];
            }
            $data['visit']['title']=$result['title'];
            $data['visit']['activities']=$result['activities'];
            $data['visit']['picture_url_activities']=$result['picture_url_activities'];
            $data['visit']['problem']=$result['problem'];
            $data['visit']['picture_url_problem']=$result['picture_url_problem'];
            $data['visit']['recommendation']=$result['recommendation'];
        }
        else
        {
            if(!(isset($this->permissions['add'])&&($this->permissions['add']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
                die();

            }
            $data['title']="New Market Visit";
            $data['visit']['title']='';
            $data['visit']['activities']='';
            $data['visit']['picture_url_activities']='';
            $data['visit']['problem']='';
            $data['visit']['picture_url_problem']='';
            $data['visit']['recommendation']='';
        }

        $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$data['visit']['zone_id']));
        $data['districts']=array();
        if($data['visit']['territory_id']>0)
        {
            $data['districts']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$data['visit']['territory_id']));
        }

        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_zi_market_visit/add_edit",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$setup_details_id);
        $this->jsonReturn($ajax);


    }

    private function system_details($id)
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            if(($this->input->post('id')))
            {
                $visit_id=$this->input->post('id');
            }
            else
            {
                $visit_id=$id;
            }
            $this->db->from($this->config->item('table_tm_market_visit_zi').' mvz');
            $this->db->select('mvz.*');
            $this->db->select('CONCAT(cus.customer_code," - ",cus.name) customer_name');
            $this->db->select('d.name district_name');
            $this->db->select('shift.name shift_name');

            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvz.customer_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
            $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = mvz.shift_id','INNER');
            $this->db->where('mvz.id',$visit_id);
            $data['visit']=$this->db->get()->row_array();
            if(!$data['visit'])
            {
                System_helper::invalid_try("Invalid try at edit",$visit_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['title']='Visit Details';
            $user_ids=array();
            $user_ids[$data['visit']['user_created']]=$data['visit']['user_created'];
            $data['previous_solutions']=Query_helper::get_info($this->config->item('table_tm_market_visit_solution_zi'),'*',array('visit_id ='.$visit_id),0,0,array('date_created DESC'));
            foreach($data['previous_solutions'] as $solution)
            {
                $user_ids[$solution['user_created']]=$solution['user_created'];
            }
            $data['users']=System_helper::get_users_info($user_ids);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_zi_market_visit/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$visit_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }


    private function system_save()
    {
        $setup_details_id=$this->input->post('id');

        $info=Query_helper::get_info($this->config->item('table_tm_market_visit_zi'),'*',array('setup_details_id ='.$setup_details_id),1);
        //edit
        if($info)
        {
            $id = $info['id'];
        }
        else
        {
            $id=0;
        }
        $this->db->from($this->config->item('table_setup_tm_market_visit_zi').' mvzi');
        $this->db->select('mvzi.*');
        $this->db->join($this->config->item('table_setup_tm_market_visit_zi_details').' mvzid','mvzi.id = mvzid.setup_id','INNER');
        $this->db->where('mvzi.status_approve',$this->config->item('system_status_approved'));
        $this->db->where('mvzid.id',$setup_details_id);
        $setup_info=$this->db->get()->row_array();
        if(!$setup_info)
        {
            System_helper::invalid_try("Invalid try to save",$setup_details_id);
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
        $user = User_helper::get_user();
        $time=time();
        if($id>0)
        {
            if(!(isset($this->permissions['edit'])&&($this->permissions['edit']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
                die();
            }
        }
        else
        {
            if(!(isset($this->permissions['add'])&&($this->permissions['add']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
                die();

            }
        }

        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->jsonReturn($ajax);
        }
        else
        {
            $visit = $this->input->post("visit");
            $visit['setup_details_id']=$setup_details_id;

            $file_folder='images/zi_market_visit/'.$setup_info['zone_id'];
            $dir=(FCPATH).$file_folder;
            if(!is_dir($dir))
            {
                mkdir($dir, 0777);
            }
            $uploaded_files = System_helper::upload_file($file_folder);
            if(array_key_exists('image_activities',$uploaded_files))
            {
                if($uploaded_files['image_activities']['status'])
                {
                    $visit['picture_url_activities']=base_url().$file_folder.'/'.$uploaded_files['image_activities']['info']['file_name'];
                    $visit['picture_file_full_activities']=$file_folder.'/'.$uploaded_files['image_activities']['info']['file_name'];
                    $visit['picture_file_name_activities']=$uploaded_files['image_activities']['info']['file_name'];
                }
                else
                {

                    $ajax['status']=false;
                    $ajax['system_message']=$uploaded_files['image_activities']['message'];
                    $this->jsonReturn($ajax);
                    die();
                }
            }
            if(array_key_exists('image_problem',$uploaded_files))
            {
                if($uploaded_files['image_problem']['status'])
                {
                    $visit['picture_url_problem']=base_url().$file_folder.'/'.$uploaded_files['image_problem']['info']['file_name'];
                    $visit['picture_file_full_problem']=$file_folder.'/'.$uploaded_files['image_problem']['info']['file_name'];
                    $visit['picture_file_name_problem']=$uploaded_files['image_problem']['info']['file_name'];
                }
                else
                {

                    $ajax['status']=false;
                    $ajax['system_message']=$uploaded_files['image_problem']['message'];
                    $this->jsonReturn($ajax);
                    die();
                }
            }
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $visit['user_updated'] = $user->user_id;
                $visit['date_updated'] = $time;
                Query_helper::update($this->config->item('table_tm_market_visit_zi'),$visit,array("id = ".$id));

            }
            else
            {

                $visit['user_created'] = $user->user_id;
                $visit['date_created'] = $time;
                Query_helper::add($this->config->item('table_tm_market_visit_zi'),$visit);
            }
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                $this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->jsonReturn($ajax);
            }
        }
    }
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('visit[recommendation]','Recommendation','required');
        $this->form_validation->set_rules('visit[territory_id]',$this->lang->line('LABEL_TERRITORY_NAME'),'required|numeric');
        $this->form_validation->set_rules('visit[district_id]',$this->lang->line('LABEL_DISTRICT_NAME'),'required|numeric');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
}
