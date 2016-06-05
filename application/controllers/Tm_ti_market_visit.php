<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tm_ti_market_visit extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_ti_market_visit');
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
        $this->controller_url='tm_ti_market_visit';
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=="add")
        {
            $this->system_add();
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
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_list($id);
        }
    }

    private function system_list()
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            $data['title']="Visit List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_ti_market_visit/list",$data,true));
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
    private function system_search()
    {
        if(isset($this->permissions['add'])&&($this->permissions['add']==1))
        {
            $data['title']="TI Market Visit Search Schedules";
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

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_ti_market_visit/search",$data,true));
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
        $time=System_helper::get_time($this->input->post('date'));
        $territory_id=$this->input->post('territory_id');

        if(!$territory_id)
        {
            $ajax['status']=false;
            $ajax['system_message']='Please Select a Territory';
            $this->jsonReturn($ajax);
        }
        if(!$time)
        {
            $ajax['status']=false;
            $ajax['system_message']='Please Select a valid date';
            $this->jsonReturn($ajax);
        }
        if($time>time())
        {
            $ajax['status']=false;
            $ajax['system_message']='You cannot select future date';
            $this->jsonReturn($ajax);
        }
        //find proper schedule version
        $min_revision=0;
        $this->db->from($this->config->item('table_setup_tm_market_visit').' mvst');
        $this->db->select('MIN(mvst.revision) min_revision');
        $this->db->select('mvst.date_updated');
        $this->db->where('date_created<',$time);
        $result=$this->db->get()->row_array();
        if($result && $result['min_revision']>0)
        {
            $min_revision=$result['min_revision'];
            if($min_revision>1)
            {
                if($result['date_updated']<$time)
                {
                    $min_revision=0;
                }
            }
        }
        //proper schedule version found
        $day=date('w',$time);
        $this->db->from($this->config->item('table_setup_tm_market_visit').' mvst');
        $this->db->select('mvst.*');
        $this->db->select('d.name district_name');
        $this->db->select('shift.name shift_name');

        //$this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvst.customer_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = mvst.district_id','INNER');
        $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = mvst.shift_id','INNER');
        $this->db->where('mvst.revision',$min_revision);
        $this->db->where('mvst.territory_id',$territory_id);
        $this->db->where('mvst.day_no',$day);

        $data['schedules']=$this->db->get()->result_array();
        $data['date']=$time;

        $customers=array();
        $this->db->from($this->config->item('table_csetup_customers').' cus');
        $this->db->select('cus.district_id');
        $this->db->select('cus.id value,CONCAT(cus.customer_code," - ",cus.name) text,cus.status');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->where('d.territory_id',$territory_id);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $customers[$result['value']]=$result;
        }
        $data['customers']=$customers;
        $other_customers=array();
        $this->db->from($this->config->item('table_csetup_other_customers').' cus');
        $this->db->select('cus.district_id');
        $this->db->select('cus.id value,cus.name text,cus.status');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->where('d.territory_id',$territory_id);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $other_customers[$result['value']]=$result;
        }
        $data['other_customers']=$other_customers;

        $this->db->from($this->config->item('table_tm_market_visit_ti').' mvt');
        $this->db->select('mvt.*');
        $this->db->join($this->config->item('table_setup_tm_market_visit').' mvst','mvst.id = mvt.setup_id','INNER');
        $this->db->where('mvt.date',$time);
        $this->db->where('mvst.territory_id',$territory_id);
        $results=$this->db->get()->result_array();
        $data['visit_done']=array();
        //$data['visit_done']=$results;
        foreach($results as $result)
        {
            //$data['visit_done'][$result['shift_id']][$result['host_type']][$result['host_id']]=true;
            $data['visit_done'][]=$result['setup_id'];
        }

        $data['title']='Schedule for '.$this->input->post('date').'('.date('l',$time).')';
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("tm_ti_market_visit/search_list",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->jsonReturn($ajax);


    }

    private function system_add()
    {
        if(isset($this->permissions['add'])&&($this->permissions['add']==1))
        {
            $setup_id=$this->input->post('setup_id');
            if($setup_id)
            {
                $this->db->from($this->config->item('table_setup_tm_market_visit').' mvst');
                $this->db->select('mvst.*');

                $this->db->select('d.name district_name');
                $this->db->select('shift.name shift_name');
                $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = mvst.district_id','INNER');
                $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = mvst.shift_id','INNER');
                $this->db->where('mvst.revision',1);
                $this->db->where('mvst.day_no',date('w',$this->input->post('date')));
                $this->db->where('mvst.id',$setup_id);
                $data['visit']=$this->db->get()->row_array();
                if(!$data['visit'])
                {
                    System_helper::invalid_try("Invalid try at system_add",$setup_id);
                    $ajax['status']=false;
                    $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                    $this->jsonReturn($ajax);
                }
                $data['visit']['setup_id']=$data['visit']['id'];
                $data['visit']['id']=0;
                $data['visit']['date']=$this->input->post('date');
                $data['visit']['title']='';
                $data['visit']['special_district_id']='';
                $data['visit']['activities']='';
                $data['visit']['picture_url_activities']='';
                $data['visit']['problem']='';
                $data['visit']['picture_url_problem']='';
                $data['visit']['recommendation']='';
                $data['districts']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$data['visit']['territory_id']),0,0,array('ordering'));
                $data['district']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('id ='.$data['visit']['district_id']),1);
                $data['visit']['customer_name']='';
                if($data['visit']['host_type']==$this->config->item('system_host_type_customer'))
                {
                    $this->db->from($this->config->item('table_csetup_customers').' cus');
                    $this->db->select('cus.id value,CONCAT(cus.customer_code," - ",cus.name) text,cus.status');
                    $this->db->where('cus.id',$data['visit']['host_id']);
                    $result=$this->db->get()->row_array();
                    $data['visit']['customer_name']=$result['text'];
                    if($result['status']!=$this->config->item('system_status_active'))
                    {
                        $data['visit']['customer_name'].= '('.$result['status'].')';
                    }
                }
                elseif($data['visit']['host_type']==$this->config->item('system_host_type_other_customer'))
                {
                    $this->db->from($this->config->item('table_csetup_other_customers').' cus');
                    $this->db->select('cus.id value,cus.name text,cus.status');
                    $this->db->where('cus.id',$data['visit']['host_id']);
                    $result=$this->db->get()->row_array();
                    $data['visit']['customer_name']=$result['text'];
                    if($result['status']!=$this->config->item('system_status_active'))
                    {
                        $data['visit']['customer_name'].= '('.$result['status'].')';
                    }
                }
                /*elseif($data['visit']['host_type']==$this->config->item('system_host_type_special'))
                {
                    $data['visit']['customer_name']='Special '.$data['visit']['host_id'];
                }*/
                $data['title']='TI Market Visit(new)';
                $ajax['status']=true;
                $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_ti_market_visit/add_edit",$data,true));
                if($this->message)
                {
                    $ajax['system_message']=$this->message;
                }
                $this->jsonReturn($ajax);
            }
            else
            {
                $this->system_search();
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }
    /*private function system_details($id)
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
            $this->db->from($this->config->item('table_tm_market_visit_ti').' mvt');
            $this->db->select('mvt.*');
            $this->db->select('CONCAT(cus.customer_code," - ",cus.name) customer_name');
            $this->db->select('d.name district_name');
            $this->db->select('shift.name shift_name');

            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvt.customer_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
            $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = mvt.shift_id','INNER');
            $this->db->where('mvt.id',$visit_id);
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
            $data['previous_solutions']=Query_helper::get_info($this->config->item('table_tm_market_visit_solution_ti'),'*',array('visit_id ='.$visit_id),0,0,array('date_created DESC'));
            foreach($data['previous_solutions'] as $solution)
            {
                $user_ids[$solution['user_created']]=$solution['user_created'];
            }
            $data['users']=System_helper::get_users_info($user_ids);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_ti_market_visit/details",$data,true));
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
    }*/
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
            $this->db->from($this->config->item('table_tm_market_visit_ti').' mvt');
            $this->db->select('mvt.*');
            $this->db->select('stmv.host_type,stmv.host_id,stmv.district_id');
            $this->db->select('shift.name shift_name');

            $this->db->join($this->config->item('table_setup_tm_market_visit').' stmv','stmv.id = mvt.setup_id','INNER');
            $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = stmv.shift_id','INNER');
            $this->db->where('mvt.id',$visit_id);
            $data['visit']=$this->db->get()->row_array();
            if(!$data['visit'])
            {
                System_helper::invalid_try("Invalid try at edit",$visit_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            //$data['districts']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$data['visit']['territory_id']),0,0,array('ordering'));
            $data['district']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('id ='.$data['visit']['district_id']),1);
            $data['visit']['customer_name']='';
            if($data['visit']['host_type']==$this->config->item('system_host_type_customer'))
            {
                $this->db->from($this->config->item('table_csetup_customers').' cus');
                $this->db->select('cus.id value,CONCAT(cus.customer_code," - ",cus.name) text,cus.status');
                $this->db->where('cus.id',$data['visit']['host_id']);
                $result=$this->db->get()->row_array();
                $data['visit']['customer_name']=$result['text'];
                if($result['status']!=$this->config->item('system_status_active'))
                {
                    $data['visit']['customer_name'].= '('.$result['status'].')';
                }
            }
            elseif($data['visit']['host_type']==$this->config->item('system_host_type_other_customer'))
            {
                $this->db->from($this->config->item('table_csetup_other_customers').' cus');
                $this->db->select('cus.id value,cus.name text,cus.status');
                $this->db->where('cus.id',$data['visit']['host_id']);
                $result=$this->db->get()->row_array();
                $data['visit']['customer_name']=$result['text'];
                if($result['status']!=$this->config->item('system_status_active'))
                {
                    $data['visit']['customer_name'].= '('.$result['status'].')';
                }
            }
            /*elseif($data['visit']['host_type']==$this->config->item('system_host_type_special'))
            {
                $data['visit']['customer_name']='Special '.$data['visit']['host_id'];
            }*/
            $user_ids=array();
            $user_ids[$data['visit']['user_created']]=$data['visit']['user_created'];
            $data['previous_solutions']=Query_helper::get_info($this->config->item('table_tm_market_visit_solution_ti'),'*',array('visit_id ='.$visit_id),0,0,array('date_created DESC'));
            foreach($data['previous_solutions'] as $solution)
            {
                $user_ids[$solution['user_created']]=$solution['user_created'];
            }
            $data['users']=System_helper::get_users_info($user_ids);
            
            $data['title']='TI Market Visit(Details)';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_ti_market_visit/details",$data,true));
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
    private function system_edit($id)
    {
        if(isset($this->permissions['edit'])&&($this->permissions['edit']==1))
        {
            if(($this->input->post('id')))
            {
                $visit_id=$this->input->post('id');
            }
            else
            {
                $visit_id=$id;
            }
            $this->db->from($this->config->item('table_tm_market_visit_ti').' mvt');
            $this->db->select('mvt.*');
            $this->db->select('stmv.host_type,stmv.host_id,stmv.district_id,stmv.territory_id');
            $this->db->select('shift.name shift_name');

            $this->db->join($this->config->item('table_setup_tm_market_visit').' stmv','stmv.id = mvt.setup_id','INNER');
            $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = stmv.shift_id','INNER');
            $this->db->where('mvt.id',$visit_id);
            $data['visit']=$this->db->get()->row_array();
            if(!$data['visit'])
            {
                System_helper::invalid_try("Invalid try at edit",$visit_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['districts']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$data['visit']['territory_id']),0,0,array('ordering'));
            $data['district']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('id ='.$data['visit']['district_id']),1);
            $data['visit']['customer_name']='';
            if($data['visit']['host_type']==$this->config->item('system_host_type_customer'))
            {
                $this->db->from($this->config->item('table_csetup_customers').' cus');
                $this->db->select('cus.id value,CONCAT(cus.customer_code," - ",cus.name) text,cus.status');
                $this->db->where('cus.id',$data['visit']['host_id']);
                $result=$this->db->get()->row_array();
                $data['visit']['customer_name']=$result['text'];
                if($result['status']!=$this->config->item('system_status_active'))
                {
                    $data['visit']['customer_name'].= '('.$result['status'].')';
                }
            }
            elseif($data['visit']['host_type']==$this->config->item('system_host_type_other_customer'))
            {
                $this->db->from($this->config->item('table_csetup_other_customers').' cus');
                $this->db->select('cus.id value,cus.name text,cus.status');
                $this->db->where('cus.id',$data['visit']['host_id']);
                $result=$this->db->get()->row_array();
                $data['visit']['customer_name']=$result['text'];
                if($result['status']!=$this->config->item('system_status_active'))
                {
                    $data['visit']['customer_name'].= '('.$result['status'].')';
                }
            }
            /*elseif($data['visit']['host_type']==$this->config->item('system_host_type_special'))
            {
                $data['visit']['customer_name']='Special '.$data['visit']['host_id'];
            }*/
            $data['title']='TI Market Visit(Edit)';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_ti_market_visit/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$visit_id);
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
        $id = $this->input->post("id");
        $visit = $this->input->post("visit");
        $setup_info=$info=Query_helper::get_info($this->config->item('table_setup_tm_market_visit'),'*',array('id ='.$visit['setup_id']),1);
        if(!$setup_info)
        {
            System_helper::invalid_try("Invalid try to save",$id);
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
        $user = User_helper::get_user();
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

            $file_folder='images/ti_market_visit/'.$setup_info['territory_id'];
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
                $visit['date_updated'] = time();
                Query_helper::update($this->config->item('table_tm_market_visit_ti'),$visit,array("id = ".$id));

            }
            else
            {

                $visit['user_created'] = $user->user_id;
                $visit['date_created'] = time();
                Query_helper::add($this->config->item('table_tm_market_visit_ti'),$visit);
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
        $this->form_validation->set_rules('visit[date]','date','required|numeric');
        $this->form_validation->set_rules('visit[setup_id]','setup_id','required|numeric');

        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    public function get_items()
    {
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

        $this->db->from($this->config->item('table_tm_market_visit_ti').' mvt');

        $this->db->select('mvt.*');
        $this->db->select('stmv.host_type,stmv.host_id,stmv.district_id');

        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->select('shift.name shift_name');
        $this->db->select('dd.name special_district_name');

        $this->db->join($this->config->item('table_setup_tm_market_visit').' stmv','stmv.id = mvt.setup_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = stmv.district_id','INNER');

        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = stmv.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_setup_tm_shifts').' shift','shift.id = stmv.shift_id','INNER');

        $this->db->join($this->config->item('table_setup_location_districts').' dd','dd.id = mvt.special_district_id','LEFT');
        if($this->locations['division_id']>0)
        {
            $this->db->where('division.id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('zone.id',$this->locations['zone_id']);
                if($this->locations['territory_id']>0)
                {
                    $this->db->where('t.id',$this->locations['territory_id']);
                }
            }
        }
        $this->db->order_by('mvt.id DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['day']=date('l',$item['date']);
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
            }
        }
        $this->jsonReturn($items);


    }

}
