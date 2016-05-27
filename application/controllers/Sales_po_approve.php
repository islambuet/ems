<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales_po_approve extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Sales_po_approve');
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
        $this->controller_url='sales_po_approve';
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
        elseif($action=="approve")
        {
            $this->system_approve($id);
        }
        elseif($action=="save_approve")
        {
            $this->system_save_approve();
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
            $data['title']="Purchase Order For Approval List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("sales_po_approve/list",$data,true));
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
            if(!$this->check_my_editable($data['po']))
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$po_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if($data['po']['status_requested']==$this->config->item('system_status_po_request_pending'))
            {
                System_helper::invalid_try('trying to edit pending po',$po_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if($data['po']['status_approved']==$this->config->item('system_status_po_approval_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_PO_APPROVAL_EDIT_UNABLE_APPROVED");
                $this->jsonReturn($ajax);
            }
            if($data['po']['status_approved']==$this->config->item('system_status_po_approval_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_PO_APPROVAL_EDIT_UNABLE_REJECTED");
                $this->jsonReturn($ajax);
            }
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
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("sales_po_approve/add_edit",$data,true));
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
            if(!$this->check_my_editable($data['po']))
            {
                System_helper::invalid_try($this->config->item('system_view_others'),$po_id);
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
            $this->db->where('spd.sales_po_id',$po_id);
            $this->db->order_by('spd.revision ASC');
            $this->db->order_by('spd.id DESC');

            $po_varieties=$this->db->get()->result_array();
            $data['po_details']=array();
            foreach($po_varieties as $po_variety)
            {
                $data['po_details'][$po_variety['revision']][]=$po_variety;
                $user_ids[$po_variety['user_created']]=$po_variety['user_created'];
            }
            //get user info from login site
            $data['users']=System_helper::get_users_info($user_ids);

            $data['title']="Details of PO (".str_pad($data['po']['id'],$this->config->item('system_po_no_length'),'0',STR_PAD_LEFT).')';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("sales_po_approve/details",$data,true));
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
    private function system_approve($id)
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
            $this->db->select('cus.district_id,d.name district_name,cus.name customer_name,cus.credit_limit');
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
                System_helper::invalid_try("Try to approve on no existing",$po_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if(!$this->check_my_editable($data['po']))
            {
                System_helper::invalid_try('Try to approve others po',$po_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if($data['po']['status_requested']==$this->config->item('system_status_po_request_pending'))
            {
                System_helper::invalid_try('trying to approve pending po',$po_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if($data['po']['status_approved']==$this->config->item('system_status_po_approval_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_PO_APPROVAL_EDIT_UNABLE_APPROVED");
                $this->jsonReturn($ajax);
            }
            if($data['po']['status_approved']==$this->config->item('system_status_po_approval_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_PO_APPROVAL_EDIT_UNABLE_REJECTED");
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

            $variety_pack_size_ids=array();
            $data['customer_varieties_quantity']=array();
            foreach($data['po_varieties'] as $variety)
            {
                $ids=array('variety_id'=>$variety['variety_id'],'pack_size_id'=>$variety['pack_size_id']);
                if(!in_array($ids,$variety_pack_size_ids))
                {
                    $variety_pack_size_ids[]=$ids;
                }
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
                    $ids=array('variety_id'=>$variety['variety_id'],'pack_size_id'=>$variety['bonus_pack_size_id']);
                    if(!in_array($ids,$variety_pack_size_ids))
                    {
                        $variety_pack_size_ids[]=$ids;
                    }
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
            $data['stocks_current']=$this->sales_model->get_stocks($variety_pack_size_ids);

            $data['customer_current_credit']=$this->sales_model->get_customer_current_credit($data['po']['customer_id']);

            $data['remarks']=$data['po_varieties'][0]['remarks'];
            $data['title']="PO No ".str_pad($data['po']['id'],$this->config->item('system_po_no_length'),'0',STR_PAD_LEFT);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("sales_po_approve/approve",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/approve/'.$po_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }
    private function system_save_approve()
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
        if(!$this->check_validation_approve())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->jsonReturn($ajax);
        }
        else
        {
            $time=time();

            $data=$this->input->post('approval');
            $data['user_approved'] = $user->user_id;
            $data['date_approved'] = $time;
            $this->db->trans_start();  //DB Transaction Handle START

            Query_helper::update($this->config->item('table_sales_po'),$data,array("id = ".$id));

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
    private function  check_validation_approve()
    {
        $data=$this->input->post('approval');

        if(!(($data['status_approved']==$this->config->item('system_status_po_approval_approved'))||($data['status_approved']==$this->config->item('system_status_po_approval_rejected'))))
        {
            $this->message="Please Select Approve or Reject";
            return false;
        }
        $po_id = $this->input->post("id");

        $this->db->from($this->config->item('table_sales_po').' po');

        $this->db->select('po.*');
        $this->db->select('cus.district_id,d.name district_name,cus.name customer_name,cus.credit_limit');
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
        $po_info=$this->db->get()->row_array();

        if(!$po_info)
        {
            System_helper::invalid_try("Try to approve or reject on no existing",$po_id);
            $this->message=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            return false;
        }
        if(!$this->check_my_editable($po_info))
        {
            System_helper::invalid_try('Try to approve or reject others po',$po_id);
            $this->message=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            return false;
        }
        if($po_info['status_requested']==$this->config->item('system_status_po_request_pending'))
        {
            System_helper::invalid_try('trying to approve/reject pending po',$po_id);
            $this->message=$this->lang->line('YOU_DONT_HAVE_ACCESS');
            return false;
        }
        if($po_info['status_approved']==$this->config->item('system_status_po_approval_approved'))
        {
            System_helper::invalid_try('trying to approve or reject approved po',$po_id);
            $this->message=$this->lang->line('MSG_PO_APPROVAL_EDIT_UNABLE_APPROVED');
            return false;
        }
        if($po_info['status_approved']==$this->config->item('system_status_po_approval_rejected'))
        {
            System_helper::invalid_try('trying to approve or reject rejected po',$po_id);
            $this->message=$this->lang->line('MSG_PO_APPROVAL_EDIT_UNABLE_REJECTED');
            return false;
        }
        if($data['status_approved']==$this->config->item('system_status_po_approval_rejected'))
        {
            return true;
        }
        $this->db->from($this->config->item('table_sales_po_details').' spd');
        $this->db->select('spd.*');
        $this->db->select('v.name variety_name');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->select('crop.name crop_name');
        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =spd.variety_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
        $this->db->where('spd.sales_po_id',$po_id);
        $this->db->where('spd.revision',1);
        $po_varieties=$this->db->get()->result_array();

        $variety_pack_size_ids=array();
        $customer_varieties_quantity=array();
        foreach($po_varieties as $variety)
        {
            $ids=array('variety_id'=>$variety['variety_id'],'pack_size_id'=>$variety['pack_size_id']);
            if(!in_array($ids,$variety_pack_size_ids))
            {
                $variety_pack_size_ids[]=$ids;
            }
            if(!isset($customer_varieties_quantity[$variety['variety_id']][$variety['pack_size_id']]))
            {
                $info=array();
                $info['crop_name']=$variety['crop_name'];
                $info['crop_type_name']=$variety['crop_type_name'];
                $info['variety_name']=$variety['variety_name'];
                $info['variety_id']=$variety['variety_id'];
                $info['pack_size']=$variety['pack_size'];
                $info['pack_size_id']=$variety['pack_size_id'];
                $info['quantity']=$variety['quantity'];
                $customer_varieties_quantity[$variety['variety_id']][$variety['pack_size_id']]=$info;
            }
            else
            {
                $customer_varieties_quantity[$variety['variety_id']][$variety['pack_size_id']]['quantity']+=$variety['quantity'];
            }
            if($variety['bonus_details_id']>0)
            {
                $ids=array('variety_id'=>$variety['variety_id'],'pack_size_id'=>$variety['bonus_pack_size_id']);
                if(!in_array($ids,$variety_pack_size_ids))
                {
                    $variety_pack_size_ids[]=$ids;
                }
                if(!isset($customer_varieties_quantity[$variety['variety_id']][$variety['bonus_pack_size_id']]))
                {
                    $info=array();
                    $info['crop_name']=$variety['crop_name'];
                    $info['crop_type_name']=$variety['crop_type_name'];
                    $info['variety_name']=$variety['variety_name'];
                    $info['variety_id']=$variety['variety_id'];
                    $info['pack_size']=$variety['bonus_pack_size'];
                    $info['pack_size_id']=$variety['bonus_pack_size_id'];
                    $info['quantity']=$variety['quantity_bonus'];
                    $customer_varieties_quantity[$variety['variety_id']][$variety['bonus_pack_size_id']]=$info;
                }
                else
                {
                    $customer_varieties_quantity[$variety['variety_id']][$variety['bonus_pack_size_id']]['quantity']+=$variety['quantity_bonus'];
                }
            }

        }
        $stocks_current=$this->sales_model->get_stocks($variety_pack_size_ids);
        foreach($customer_varieties_quantity as $variety_id=>$v)
        {
            foreach($v as $pack_size_id=>$variety)
            {
                if($stocks_current[$variety_id][$pack_size_id]['current_stock']<$variety['quantity'])
                {
                    $this->message=$variety['variety_name'].'('.$variety['crop_name'].'-'.$variety['crop_type_name'].') will be Out of stock.';
                    return false;
                }
            }
        }
        return true;
    }


    private function system_save()
    {
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

            $po_varieties=$this->input->post('po_varieties');
            /*echo '<PRE>';
            print_r($po);
            print_r($po_varieties);
            echo '</PRE>';
            die();*/
            $this->db->trans_start();  //DB Transaction Handle START

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
            if($po_info['status_requested']==$this->config->item('system_status_po_request_pending'))
            {
                System_helper::invalid_try('trying to save pending po',$id);
                $this->message=$this->lang->line('YOU_DONT_HAVE_ACCESS');
                return false;
            }
            if($po_info['status_approved']==$this->config->item('system_status_po_approval_approved'))
            {
                $this->message=$this->lang->line('MSG_PO_APPROVAL_EDIT_UNABLE_APPROVED');
                return false;
            }
            if($po_info['status_approved']==$this->config->item('system_status_po_approval_rejected'))
            {
                $this->message=$this->lang->line('MSG_PO_APPROVAL_EDIT_UNABLE_REJECTED');
                return false;
            }
        }
        return true;
    }
    public function get_bonus_and_total()
    {
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        $quantity=$this->input->post('quantity');
        $active_id=$this->input->post('active_id');
        $this->db->from($this->config->item('table_setup_classification_variety_price').' vp');
        $this->db->select('vp.price variety_price,vp.price_net variety_price_net,vp.id variety_price_id');
        $this->db->select('vp_size.name pack_size');
        $this->db->join($this->config->item('table_setup_classification_vpack_size').' vp_size','vp_size.id = vp.pack_size_id','INNER');
        $this->db->where('vp.variety_id',$variety_id);
        $this->db->where('vp.pack_size_id',$pack_size_id);
        $this->db->where('vp.revision',1);
        $price_info=$this->db->get()->row_array();
        if(!$price_info)
        {
            $ajax['status']=false;
            $ajax['system_message']="Invalid Try";
            $this->jsonReturn($ajax);
            die();
        }
        $ajax['status']=true;
        $weight_html='<span>'.number_format($quantity*$price_info['pack_size']/1000,3, '.', '').'</span>';
        $weight_html.='<input type="hidden" name="po_varieties['.$active_id.'][pack_size]" value="'.$price_info['pack_size'].'" />';
        $ajax['system_content'][]=array("id"=>"#total_weight_".$active_id,"html"=>$weight_html);

        $price_html='<span>'.number_format($quantity*$price_info['variety_price'],2).'</span>';
        $price_html.='<input type="hidden" name="po_varieties['.$active_id.'][variety_price]" value="'.$price_info['variety_price'].'" />';
        $price_html.='<input type="hidden" name="po_varieties['.$active_id.'][variety_price_net]" value="'.$price_info['variety_price_net'].'" />';
        $price_html.='<input type="hidden" name="po_varieties['.$active_id.'][variety_price_id]" value="'.$price_info['variety_price_id'].'" />';
        $ajax['system_content'][]=array("id"=>"#total_price_".$active_id,"html"=>$price_html);

        $bonus=System_helper::get_bonus_info($variety_id,$pack_size_id,$quantity);
        $html_quantity_bonus='<span>'.$bonus['quantity_bonus'].'</span>';
        $html_quantity_bonus.='<input type="hidden" name="po_varieties['.$active_id.'][quantity_bonus]" value="'.$bonus['quantity_bonus'].'" />';
        $html_quantity_bonus.='<input type="hidden" name="po_varieties['.$active_id.'][bonus_details_id]" value="'.$bonus['bonus_details_id'].'" />';
        if($bonus['bonus_details_id']>0)
        {
            $html_quantity_bonus.='<input type="hidden" name="po_varieties['.$active_id.'][bonus_pack_size]" value="'.$bonus['bonus_pack_size_name'].'" />';

        }
        else
        {
            $html_quantity_bonus.='<input type="hidden" name="po_varieties['.$active_id.'][bonus_pack_size]" value="0" />';
        }
        $html_quantity_bonus.='<input type="hidden" name="po_varieties['.$active_id.'][bonus_pack_size_id]" value="'.$bonus['bonus_pack_size_id'].'" />';

        $ajax['system_content'][]=array("id"=>"#bonus_quantity_".$active_id,"html"=>$html_quantity_bonus);
        $ajax['system_content'][]=array("id"=>"#bonus_pack_size_name_".$active_id,"html"=>$bonus['bonus_pack_size_name']);
        $ajax['system_content'][]=array("id"=>"#bonus_total_weight_".$active_id,"html"=>'<span>'.number_format($bonus['total_weight'],3,'.','').'</span>');

        //$ajax['system_message']=$this->message;

        $this->jsonReturn($ajax);

    }
    public function get_items()
    {
        $this->db->from($this->config->item('table_sales_po_details').' pod');

        $this->db->select('SUM(pod.quantity) quantity_total');
        $this->db->select('SUM(pod.quantity*pod.pack_size) quantity_weight');
        $this->db->select('SUM(pod.quantity*pod.variety_price) price_total');


        //$this->db->from($this->config->item('table_sales_po').' po');
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
        $this->db->where('po.status_requested',$this->config->item('system_status_po_request_requested'));
        $this->db->group_by('po.id');
        $this->db->order_by('po.id','DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['po_no']=str_pad($item['id'],$this->config->item('system_po_no_length'),'0',STR_PAD_LEFT);
            $item['quantity_weight']=number_format($item['quantity_weight']/1000,3,'.','');
            $item['price_total']=number_format($item['price_total'],2);
            $item['date_po']=System_helper::display_date($item['date_po']);

        }
        $this->jsonReturn($items);
    }

}
