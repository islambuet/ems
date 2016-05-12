<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tm_di_market_visit extends Root_Controller
{
    private $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_di_market_visit');
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
        $this->controller_url='tm_di_market_visit';
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
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_di_market_visit/list",$data,true));
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
        $this->db->from($this->config->item('table_tm_market_visit_di').' mvdi');

        $this->db->select('mvdi.*');
        $this->db->select('CONCAT(cus.customer_code," - ",cus.name) cus_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');



        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = mvdi.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = mvdi.division_id','INNER');

        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvdi.customer_id','LEFT');
        if($this->locations['division_id']>0)
        {
            $this->db->where('division.id',$this->locations['division_id']);
        }
        $this->db->order_by('mvdi.id DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date']=System_helper::display_date($item['date']);
            $item['locations']=$item['division_name'].'<br>'.$item['zone_name'].'<br>'.$item['territory_name'].'<br>'.$item['district_name'].'<br>';
            if($item['customer_id']>0)
            {
                $item['locations'].=$item['cus_name'];
            }
            else
            {
                $item['locations'].=$item['customer_name'];
            }
        }
        $this->jsonReturn($items);

    }
    private function system_add()
    {
        if(isset($this->permissions['add'])&&($this->permissions['add']==1))
        {
            $data['title']="New DI Visit";
            $data["visit"] = Array(
                'id'=>0,
                'date'=>time(),
                'division_id'=>$this->locations['division_id'],
                'zone_id'=>$this->locations['zone_id'],
                'territory_id'=>$this->locations['territory_id'],
                'district_id'=>$this->locations['district_id'],
                'customer_id'=>'',
                'activities'=>'',
                'picture_url_activities'=>'',
                'problem'=>'',
                'picture_url_problem'=>'',
                'recommendation'=>'',
                'customer_name'=>'',
            );
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
                        $data['districts']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$this->locations['territory_id']));
                        if($this->locations['district_id']>0)
                        {
                            $data['customers']=Query_helper::get_info($this->config->item('table_csetup_customers'),array('id value','CONCAT(customer_code," - ",name) text'),array('district_id ='.$this->locations['district_id'],'status ="'.$this->config->item('system_status_active').'"'));
                        }
                    }
                }
            }
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_di_market_visit/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
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
            $this->db->from($this->config->item('table_tm_market_visit_di').' mvdi');
            $this->db->select('mvdi.*');
            $this->db->select('t.id territory_id');
            $this->db->select('t.zone_id zone_id');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = mvdi.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->where('mvdi.id',$visit_id);
            $data['visit']=$this->db->get()->row_array();
            if(!$data['visit'])
            {
                System_helper::invalid_try("Invalid try at edit",$visit_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['title']='Edit Visit';
            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$data['visit']['division_id']));
            $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$data['visit']['zone_id']));
            $data['districts']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$data['visit']['territory_id']));
            $data['customers']=Query_helper::get_info($this->config->item('table_csetup_customers'),array('id value','CONCAT(customer_code," - ",name) text'),array('district_id ='.$data['visit']['district_id'],'status ="'.$this->config->item('system_status_active').'"'));


            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_di_market_visit/add_edit",$data,true));
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
        $visit = $this->input->post("visit");
        $id=$this->input->post("id");
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
            $visit['date']=System_helper::get_time($visit['date']);
            $file_folder='images/di_market_visit/'.$visit['division_id'];
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
                Query_helper::update($this->config->item('table_tm_market_visit_di'),$visit,array("id = ".$id));

            }
            else
            {

                $visit['user_created'] = $user->user_id;
                $visit['date_created'] = time();
                Query_helper::add($this->config->item('table_tm_market_visit_di'),$visit);
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
    private function system_details($id)
    {
        /*$this->db->select('CONCAT(cus.customer_code," - ",cus.name) cus_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');



        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = mvdi.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = mvdi.division_id','INNER');

        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvdi.customer_id','LEFT');*/

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
            $this->db->from($this->config->item('table_tm_market_visit_di').' mvdi');
            $this->db->select('mvdi.*');
            $this->db->select('CONCAT(cus.customer_code," - ",cus.name) cus_name');
            $this->db->select('d.name district_name');
            $this->db->select('t.id territory_id,t.name territory_name');
            $this->db->select('zone.id zone_id,zone.name zone_name');
            $this->db->select('division.id division_id,division.name division_name');

            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = mvdi.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = mvdi.division_id','INNER');

            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = mvdi.customer_id','LEFT');

            $this->db->where('mvdi.id',$visit_id);
            $data['visit']=$this->db->get()->row_array();
            if(!$data['visit'])
            {
                System_helper::invalid_try("Invalid try at edit",$visit_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['title']='Details of Visit';

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_di_market_visit/details",$data,true));
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
    private function check_validation()
    {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('visit[date]',$this->lang->line('LABEL_DATE'),'required');
        $this->form_validation->set_rules('visit[division_id]',$this->lang->line('LABEL_DIVISION_NAME'),'required|numeric');
        $this->form_validation->set_rules('visit[district_id]',$this->lang->line('LABEL_DISTRICT_NAME'),'required|numeric');
        $this->form_validation->set_rules('visit[recommendation]','Recommendation','required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        if(!(($this->input->post('visit[customer_id]')>0)||(strlen($this->input->post('visit[customer_name]'))>0)))
        {
            $this->message='Select or Type Customer Name';
            return false;
        }
        return true;
    }


}
