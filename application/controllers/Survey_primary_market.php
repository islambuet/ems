<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Survey_primary_market extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Survey_primary_market');
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
        $this->controller_url='survey_primary_market';
        //$this->load->model("sys_module_task_model");
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="get_survey")
        {
            $this->get_survey();
        }
        /*elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="request_approve")
        {
            $this->system_request_approve($id);
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
            $data['title']="Primary Market Survey List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("survey_primary_market/list",$data,true));
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

    private function system_add()
    {
        if(isset($this->permissions['add'])&&($this->permissions['add']==1))
        {
            $data['title']="Market Survey";
            $data["survey"] = Array(
                'division_id'=>$this->locations['division_id'],
                'zone_id'=>$this->locations['zone_id'],
                'territory_id'=>$this->locations['territory_id'],
                'district_id'=>$this->locations['district_id'],
                'upazilla_id'=>$this->locations['upazilla_id'],
                'year' => date('Y')
            );
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
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("survey_primary_market/search",$data,true));
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
                $po_id=$this->input->post('id');
            }
            else
            {
                $po_id=$id;
            }

            $this->db->from($this->config->item('table_sales_po').' po');
            //$this->db->from($this->config->item('table_csetup_other_customers').' cus');
            $this->db->select('po.*');
            $this->db->select('cus.district_id');
            $this->db->select('d.territory_id');
            $this->db->select('t.zone_id zone_id');
            $this->db->select('zone.division_id division_id');
            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = po.customer_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->where('po.id',$po_id);
            $data['po']=$this->db->get()->row_array();

            if(!$data['po'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$po_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if(!$this->check_my_editable($data['po']))
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$po_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if($data['po']['status_requested']==$this->config->item('system_status_po_request_requested'))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_PO_EDIT_UNABLE");
                $this->jsonReturn($ajax);
            }

            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$data['po']['division_id']));
            $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$data['po']['zone_id']));
            $data['districts']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$data['po']['territory_id']));
            $data['customers']=Query_helper::get_info($this->config->item('table_csetup_customers'),array('id value','name text'),array('district_id ='.$data['po']['district_id'],'status ="'.$this->config->item('system_status_active').'"'));
            $data['warehouses']=Query_helper::get_info($this->config->item('table_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));


            $this->db->from($this->config->item('table_basic_setup_warehouse_crops').' wc');
            $this->db->select('wc.crop_id value,c.name text');
            $this->db->join($this->config->item('table_setup_classification_crops').' c','c.id =wc.crop_id','INNER');
            $this->db->where('wc.warehouse_id',$data['po']['warehouse_id']);
            $this->db->where('wc.revision',1);
            $data['crops']=$this->db->get()->result_array();

            $this->db->from($this->config->item('table_sales_po_details').' spd');
            $this->db->select('spd.*');
            $this->db->select('v.name variety_name');
            $this->db->select('crop_type.name crop_type_name');
            $this->db->select('crop.name crop_name');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =spd.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
            $this->db->where('spd.sales_po_id',$data['po']['id']);
            $this->db->where('spd.revision',1);
            $data['po_varieties']=$this->db->get()->result_array();
            $data['remarks']=$data['po_varieties'][0]['remarks'];
            $data['title']="Edit PO (".str_pad($data['po']['id'],$this->config->item('system_po_no_length'),'0',STR_PAD_LEFT).')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("sales_po/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$po_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }
    private function get_survey()
    {
        $data['year']=$this->input->post('year');
        $data['crop_type_id']=$this->input->post('crop_type_id');
        $data['upazilla_id']=$this->input->post('upazilla_id');
        $data['unions']=Query_helper::get_info($this->config->item('table_setup_location_unions'),array('id value','name text'),array('upazilla_id ='.$data['upazilla_id'],'status ="'.$this->config->item('system_status_active').'"'));
        $data['varieties_arm']=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),'*',array('crop_type_id ='.$data['crop_type_id'],'status ="'.$this->config->item('system_status_active').'"','whose ="ARM"'));
        $data['varieties_competitor']=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),'*',array('crop_type_id ='.$data['crop_type_id'],'status ="'.$this->config->item('system_status_active').'"','whose ="Competitor"'));
        $data['max_customers_number']=2;
        //get info
        //if exits check my editable
        $data['title']="New Survey";//edit Survey
        $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("survey_primary_market/add_edit",$data,true));
        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->jsonReturn($ajax);

    }
    private function system_save()
    {
        echo '<PRE>';
        print_r($this->input->post());
        echo '</PRE>';
        die();
        $id = $this->input->post("id");
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
            $time=time();
            $po=$this->input->post('po');
            $po['date_po']=System_helper::get_time($po['date_po']);
            $po_varieties=$this->input->post('po_varieties');
            /*echo '<PRE>';
            print_r($po);
            print_r($po_varieties);
            echo '</PRE>';
            die();*/
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $po['user_updated'] = $user->user_id;
                $po['date_updated'] = $time;
                Query_helper::update($this->config->item('table_sales_po'),$po,array("id = ".$id));

            }
            else
            {
                $po['user_created'] = $user->user_id;
                $po['date_created'] = $time;
                $po_id=Query_helper::add($this->config->item('table_sales_po'),$po);
                if($po_id===false)
                {
                    $this->db->trans_complete();
                    $ajax['status']=false;
                    $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                    $this->jsonReturn($ajax);
                    die();
                }
                else
                {
                    $id=$po_id;
                }
            }
            $this->db->where('sales_po_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_sales_po_details'));
            $remarks=$this->input->post('remarks');
            foreach($po_varieties as $data)
            {
                $data['sales_po_id']=$id;
                $data['remarks']=$remarks;
                $data['revision']=1;
                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                Query_helper::add($this->config->item('table_sales_po_details'),$data);
            }

            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_add();
                }
                else
                {
                    $this->system_list();
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->jsonReturn($ajax);
            }
        }
    }
    private function check_my_editable($customer)
    {
        if(($this->locations['division_id']>0)&&($this->locations['division_id']!=$customer['division_id']))
        {
            return false;
        }
        if(($this->locations['zone_id']>0)&&($this->locations['zone_id']!=$customer['zone_id']))
        {
            return false;
        }
        if(($this->locations['territory_id']>0)&&($this->locations['territory_id']!=$customer['territory_id']))
        {
            return false;
        }
        if(($this->locations['district_id']>0)&&($this->locations['district_id']!=$customer['district_id']))
        {
            return false;
        }
        return true;
    }

    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('po[customer_id]',$this->lang->line('LABEL_CUSTOMER_NAME'),'required');
        $this->form_validation->set_rules('po[warehouse_id]',$this->lang->line('LABEL_WAREHOUSE_NAME'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        $po_varieties=$this->input->post('po_varieties');
        if(!(sizeof($po_varieties)>0))
        {
            $this->message=$this->lang->line('MSG_MIN_ONE_PO_REQUIRED');
            return false;
        }
        else
        {
            foreach($po_varieties as $po)
            {
                if(!(($po['variety_id']>0)&&($po['pack_size_id']>0)&&($po['quantity']>0)))
                {
                    $this->message=$this->lang->line('MSG_UNFINISHED_PO');
                    return false;
                }
            }
        }
        $id = $this->input->post("id");
        if($id>0)
        {
            $po_info=Query_helper::get_info($this->config->item('table_sales_po'),'*',array('id ='.$id),1);
            if($po_info['status_requested']==$this->config->item('system_status_po_request_requested'))
            {
                $this->message=$this->lang->line('MSG_PO_EDIT_UNABLE');
                return false;
            }
        }
        return true;
    }

    public function get_items()
    {
        /*$this->db->from($this->config->item('table_sales_po_details').' pod');

        $this->db->select('SUM(pod.quantity) quantity_total');
        $this->db->select('SUM(pod.quantity*pod.pack_size) quantity_weight');
        $this->db->select('SUM(pod.quantity*pod.variety_price) price_total');

        $this->db->select('po.*');
        $this->db->select('cus.name,cus.customer_code');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->join($this->config->item('table_sales_po').' po','po.id = pod.sales_po_id','INNER');
        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = po.customer_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        if($this->locations['division_id']>0)
        {
            $this->db->where('division.id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('zone.id',$this->locations['zone_id']);
                if($this->locations['territory_id']>0)
                {
                    $this->db->where('t.id',$this->locations['territory_id']);
                    if($this->locations['district_id']>0)
                    {
                        $this->db->where('d.id',$this->locations['district_id']);
                    }
                }
            }
        }
        $this->db->where('pod.revision',1);
        $this->db->group_by('po.id');
        $this->db->order_by('po.id','DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['po_no']=str_pad($item['id'],$this->config->item('system_po_no_length'),'0',STR_PAD_LEFT);
            $item['quantity_weight']=number_format($item['quantity_weight']/1000,3,'.','');
            $item['price_total']=number_format($item['price_total'],2);
            $item['date_po']=System_helper::display_date($item['date_po']);

        }*/
        $items=array();
        $this->jsonReturn($items);
    }

}
