<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales_po_delivery extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Sales_po_delivery');
        $this->controller_url='sales_po_delivery';
        $this->load->model("sales_model");
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list();
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
            $this->system_list();
        }
    }

    private function system_list()
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            $data['title']="Delivery List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("sales_po_delivery/list",$data,true));
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

            $this->db->select('po.*');
            $this->db->select('cus.district_id,d.name district_name,cus.name customer_name');
            $this->db->select('d.territory_id,t.name territory_name');
            $this->db->select('t.zone_id zone_id,zone.name zone_name');
            $this->db->select('zone.division_id division_id,division.name division_name');
            $this->db->select('warehouse.name warehouse_name');

            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = po.customer_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->join($this->config->item('table_basic_setup_warehouse').' warehouse','warehouse.id = po.warehouse_id','INNER');
            $this->db->where('po.id',$po_id);
            $data['po']=$this->db->get()->row_array();

            if(!$data['po'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$po_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if($data['po']['status_approved']!=$this->config->item('system_status_po_approval_approved'))
            {
                System_helper::invalid_try('Trying to edit no approval po',$po_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

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


            $data['customer_varieties_quantity']=array();
            foreach($data['po_varieties'] as $variety)
            {
                if(!isset($data['customer_varieties_quantity'][$variety['variety_id']][$variety['pack_size_id']]))
                {
                    $info=array();
                    $info['crop_name']=$variety['crop_name'];
                    $info['crop_type_name']=$variety['crop_type_name'];
                    $info['variety_name']=$variety['variety_name'];
                    $info['variety_id']=$variety['variety_id'];
                    $info['pack_size']=$variety['pack_size'];
                    $info['pack_size_id']=$variety['pack_size_id'];
                    $info['quantity']=$variety['quantity'];
                    $data['customer_varieties_quantity'][$variety['variety_id']][$variety['pack_size_id']]=$info;
                }
                else
                {
                    $data['customer_varieties_quantity'][$variety['variety_id']][$variety['pack_size_id']]['quantity']+=$variety['quantity'];
                }
                if($variety['bonus_details_id']>0)
                {
                    if(!isset($data['customer_varieties_quantity'][$variety['variety_id']][$variety['bonus_pack_size_id']]))
                    {
                        $info=array();
                        $info['crop_name']=$variety['crop_name'];
                        $info['crop_type_name']=$variety['crop_type_name'];
                        $info['variety_name']=$variety['variety_name'];
                        $info['variety_id']=$variety['variety_id'];
                        $info['pack_size']=$variety['bonus_pack_size'];
                        $info['pack_size_id']=$variety['bonus_pack_size_id'];
                        $info['quantity']=$variety['quantity_bonus'];
                        $data['customer_varieties_quantity'][$variety['variety_id']][$variety['bonus_pack_size_id']]=$info;
                    }
                    else
                    {
                        $data['customer_varieties_quantity'][$variety['variety_id']][$variety['bonus_pack_size_id']]['quantity']+=$variety['quantity_bonus'];
                    }
                }

            }


            $data['title']="Delivery PO (".str_pad($data['po']['id'],$this->config->item('system_po_no_length'),'0',STR_PAD_LEFT).')';
            if($data['po']['status_delivered']==$this->config->item('system_status_po_delivery_delivered'))
            {
                $data['delivery_info']=Query_helper::get_info($this->config->item('table_sales_po_delivery'),'*',array('sales_po_id ='.$po_id,'revision =1'),1);
            }
            else
            {
                $time=time();
                $data['delivery_info']=Array(
                    'date_delivery' => $time,
                    'date_invoice' => $time,
                    'invoice_no' => '',
                    'courier_id' => '',
                    'track_no' => '',
                    'date_booking' => $time,
                    'remarks' => ''
                );
            }
            $data['couriers']=Query_helper::get_info($this->config->item('table_basic_setup_couriers'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("sales_po_delivery/add_edit",$data,true));
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
    private function system_details($id)
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
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

            $this->db->select('po.*');
            $this->db->select('cus.district_id,d.name district_name,cus.name customer_name');
            $this->db->select('d.territory_id,t.name territory_name');
            $this->db->select('t.zone_id zone_id,zone.name zone_name');
            $this->db->select('zone.division_id division_id,division.name division_name');
            $this->db->select('warehouse.name warehouse_name');

            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = po.customer_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->join($this->config->item('table_basic_setup_warehouse').' warehouse','warehouse.id = po.warehouse_id','INNER');
            $this->db->where('po.id',$po_id);
            $data['po']=$this->db->get()->row_array();

            if(!$data['po'])
            {
                System_helper::invalid_try($this->config->item('system_view_not_exists'),$po_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if($data['po']['status_approved']!=$this->config->item('system_status_po_approval_approved'))
            {
                System_helper::invalid_try('Trying to view no approval po',$po_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $user_ids=array();
            $user_ids[$data['po']['user_created']]=$data['po']['user_created'];
            if($data['po']['user_requested']>0)
            {
                $user_ids[$data['po']['user_requested']]=$data['po']['user_requested'];
            }
            if($data['po']['user_approved']>0)
            {
                $user_ids[$data['po']['user_approved']]=$data['po']['user_approved'];
            }
            if($data['po']['user_delivered']>0)
            {
                $user_ids[$data['po']['user_delivered']]=$data['po']['user_delivered'];
            }
            if($data['po']['user_received']>0)
            {
                $user_ids[$data['po']['user_received']]=$data['po']['user_received'];
            }


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


            $data['customer_varieties_quantity']=array();
            foreach($data['po_varieties'] as $variety)
            {
                if(!isset($data['customer_varieties_quantity'][$variety['variety_id']][$variety['pack_size_id']]))
                {
                    $info=array();
                    $info['crop_name']=$variety['crop_name'];
                    $info['crop_type_name']=$variety['crop_type_name'];
                    $info['variety_name']=$variety['variety_name'];
                    $info['variety_id']=$variety['variety_id'];
                    $info['pack_size']=$variety['pack_size'];
                    $info['pack_size_id']=$variety['pack_size_id'];
                    $info['quantity']=$variety['quantity'];
                    $data['customer_varieties_quantity'][$variety['variety_id']][$variety['pack_size_id']]=$info;
                }
                else
                {
                    $data['customer_varieties_quantity'][$variety['variety_id']][$variety['pack_size_id']]['quantity']+=$variety['quantity'];
                }
                if($variety['bonus_details_id']>0)
                {
                    if(!isset($data['customer_varieties_quantity'][$variety['variety_id']][$variety['bonus_pack_size_id']]))
                    {
                        $info=array();
                        $info['crop_name']=$variety['crop_name'];
                        $info['crop_type_name']=$variety['crop_type_name'];
                        $info['variety_name']=$variety['variety_name'];
                        $info['variety_id']=$variety['variety_id'];
                        $info['pack_size']=$variety['bonus_pack_size'];
                        $info['pack_size_id']=$variety['bonus_pack_size_id'];
                        $info['quantity']=$variety['quantity_bonus'];
                        $data['customer_varieties_quantity'][$variety['variety_id']][$variety['bonus_pack_size_id']]=$info;
                    }
                    else
                    {
                        $data['customer_varieties_quantity'][$variety['variety_id']][$variety['bonus_pack_size_id']]['quantity']+=$variety['quantity_bonus'];
                    }
                }

            }


            $data['title']="Delivery PO (".str_pad($data['po']['id'],$this->config->item('system_po_no_length'),'0',STR_PAD_LEFT).')';
            if($data['po']['status_delivered']==$this->config->item('system_status_po_delivery_delivered'))
            {
                $delivery_info=Query_helper::get_info($this->config->item('table_sales_po_delivery'),'*',array('sales_po_id ='.$po_id),0,0,array('revision ASC'));

                $data['delivery_details']=array();
                foreach($delivery_info as $info)
                {
                    $data['delivery_details'][$info['revision']]=$info;
                    $user_ids[$info['user_created']]=$info['user_created'];
                }

            }

            $data['users']=System_helper::get_users_info($user_ids);
            $data['couriers']=Query_helper::get_info($this->config->item('table_basic_setup_couriers'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering'));

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("sales_po_delivery/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$po_id);
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
        $user = User_helper::get_user();
        if(!(isset($this->permissions['edit'])&&($this->permissions['edit']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
            die();
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

            $delivery_info=$this->input->post('delivery');
            $po_info=Query_helper::get_info($this->config->item('table_sales_po'),'*',array('id ='.$id),1);

            if(!$po_info)
            {
                System_helper::invalid_try('Trying to save delivery info on non existing id',$id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if($po_info['status_approved']!=$this->config->item('system_status_po_approval_approved'))
            {
                System_helper::invalid_try('Trying to save delivery in on not approved po',$id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

//            echo '<PRE>';
//            print_r($delivery_info);
//            echo '</PRE>';
//            return;
            $this->db->trans_start();  //DB Transaction Handle START
            $data=array();
            $data['date_delivery']=System_helper::get_time($delivery_info['date_delivery']);
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = $time;

            if($po_info['status_delivered']==$this->config->item('system_status_po_delivery_delivered'))
            {

            }
            else
            {
                $data['status_delivered']=$this->config->item('system_status_po_delivery_delivered');
                $data['date_delivered']=$time;
                $data['user_delivered'] = $user->user_id;
            }
            Query_helper::update($this->config->item('table_sales_po'),$data,array("id = ".$id));

            $this->db->where('sales_po_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_sales_po_delivery'));

            $delivery_info['date_delivery']=System_helper::get_time($delivery_info['date_delivery']);
            $delivery_info['date_invoice']=System_helper::get_time($delivery_info['date_invoice']);
            $delivery_info['date_booking']=System_helper::get_time($delivery_info['date_booking']);
            $delivery_info['sales_po_id']=$id;
            $delivery_info['revision']=1;
            $delivery_info['user_created'] = $user->user_id;
            $delivery_info['date_created'] = $time;
            Query_helper::add($this->config->item('table_sales_po_delivery'),$delivery_info);

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
        $this->form_validation->set_rules('delivery[date_delivery]',$this->lang->line('LABEL_DATE_DELIVERY'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }
    public function get_items()
    {
        $this->db->from($this->config->item('table_sales_po_details').' pod');

        $this->db->select('SUM(pod.quantity) quantity_total');
        $this->db->select('SUM(pod.quantity*pod.pack_size) quantity_weight');
        //$this->db->select('SUM(pod.quantity*pod.variety_price) price_total');



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

        $this->db->where('pod.revision',1);
        $this->db->where('po.status_approved',$this->config->item('system_status_po_approval_approved'));
        $this->db->group_by('po.id');
        $this->db->order_by('po.id','DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['po_no']=str_pad($item['id'],$this->config->item('system_po_no_length'),'0',STR_PAD_LEFT);
            $item['quantity_weight']=number_format($item['quantity_weight']/1000,3,'.','');
            //$item['price_total']=number_format($item['price_total'],2);
            $item['date_po']=System_helper::display_date($item['date_po']);

        }
        $this->jsonReturn($items);
    }

}
