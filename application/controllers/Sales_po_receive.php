<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sales_po_receive extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Sales_po_receive');
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
        $this->controller_url='sales_po_receive';
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
            $data['title']="Received List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("sales_po_receive/list",$data,true));
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
            if($data['po']['status_delivered']!=$this->config->item('system_status_po_delivery_delivered'))
            {
                System_helper::invalid_try('Trying to edit not delivered po',$po_id);
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
            $data['receive_info']=array();

            if($data['po']['status_received']==$this->config->item('system_status_po_received_received'))
            {
                $data['date_receive']=$data['po']['date_receive'];
                $receive_data=Query_helper::get_info($this->config->item('table_sales_po_receives'),'*',array('sales_po_id ='.$po_id,'revision =1'));
                $data['remarks']=$receive_data[0]['remarks'];
                foreach($receive_data as $rv)
                {
                    $data['receive_info'][$rv['sales_po_detail_id']]=$rv;
                }


                //$data['receive_info']=Query_helper::get_info($this->config->item('table_sales_po_delivery'),'*',array('sales_po_id ='.$po_id,'revision =1'),1);
            }
            else
            {
                $time=time();
                $data['date_receive']=$time;
                $data['remarks']='';
                foreach($data['po_varieties'] as $v)
                {
                    $data['receive_info'][$v['id']]=array('quantity_receive'=>$v['quantity'],'quantity_bonus_receive'=>$v['quantity_bonus']);

                }
            }


            $data['title']="Receive PO (".str_pad($data['po']['id'],$this->config->item('system_po_no_length'),'0',STR_PAD_LEFT).')';


            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("sales_po_receive/add_edit",$data,true));
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
            if($data['po']['status_delivered']!=$this->config->item('system_status_po_delivery_delivered'))
            {
                System_helper::invalid_try('Trying to view not delivered po',$po_id);
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


            $data['receive_info']=array();

            if($data['po']['status_received']==$this->config->item('system_status_po_received_received'))
            {
                $receive_info=Query_helper::get_info($this->config->item('table_sales_po_receives'),'*',array('sales_po_id ='.$po_id),0,0,array('revision ASC'));
                $data['receive_details']=array();
                foreach($receive_info as $info)
                {
                    $data['receive_details'][$info['revision']][$info['sales_po_detail_id']]=$info;
                    $user_ids[$info['user_created']]=$info['user_created'];
                }

                //$data['receive_info']=Query_helper::get_info($this->config->item('table_sales_po_delivery'),'*',array('sales_po_id ='.$po_id,'revision =1'),1);
            }
            $data['users']=System_helper::get_users_info($user_ids);


            $data['title']="Receive PO (".str_pad($data['po']['id'],$this->config->item('system_po_no_length'),'0',STR_PAD_LEFT).')';


            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("sales_po_receive/details",$data,true));
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


            $po_info=Query_helper::get_info($this->config->item('table_sales_po'),'*',array('id ='.$id),1);

            if(!$po_info)
            {
                System_helper::invalid_try('Trying to save receive info on non existing id',$id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if($po_info['status_delivered']!=$this->config->item('system_status_po_delivery_delivered'))
            {
                System_helper::invalid_try('Trying to save receive on not delivered po',$id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $date_receive=System_helper::get_time($this->input->post('date_receive'));
            $remarks=$this->input->post('remarks');
            $receive_info=$this->input->post('receive');
            $po_varieties=Query_helper::get_info($this->config->item('table_sales_po_details'),'*',array('sales_po_id ='.$id,'revision =1'));
            $receive_data=array();
            foreach($po_varieties as $pv)
            {
                $info=array();
                $info['sales_po_id']=$pv['sales_po_id'];
                $info['sales_po_detail_id']=$pv['id'];
                $info['quantity_receive']=$receive_info[$pv['id']]['quantity_receive'];



                if(($info['quantity_receive']<0)||((strval(intval($info['quantity_receive'])))!=$info['quantity_receive']))
                {
                    $ajax['status']=false;
                    $ajax['system_message']="Invalid Receive Quantity";
                    $this->jsonReturn($ajax);
                    die();

                }
                /*if($info['quantity_receive']>$pv['quantity'])
                {
                    $ajax['status']=false;
                    $ajax['system_message']="Receive Quantity is greater than Delivered Quantity";
                    $this->jsonReturn($ajax);
                    die();
                }*/
                $info['quantity_bonus_receive']=$receive_info[$pv['id']]['quantity_bonus_receive'];
                if(($info['quantity_bonus_receive']<0)||(strval(intval($info['quantity_bonus_receive']))!=$info['quantity_bonus_receive']))
                {
                    $ajax['status']=false;
                    $ajax['system_message']="Invalid Receive Bonus Quantity";
                    $this->jsonReturn($ajax);
                    die();

                }
                /*if($info['quantity_bonus_receive']>$pv['quantity_bonus'])
                {
                    $ajax['status']=false;
                    $ajax['system_message']="Receive Bonus Quantity is greater than Delivered Bonus Quantity";
                    $this->jsonReturn($ajax);
                    die();
                }*/
                $info['date_receive']=$date_receive;
                $info['remarks']=$remarks;
                $info['revision']=1;
                $info['user_created'] = $user->user_id;
                $info['date_created'] = $time;
                $receive_data[]=$info;

            }
            $this->db->trans_start();  //DB Transaction Handle START
            $data=array();
            $data['date_receive']=$date_receive;
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = $time;

            if($po_info['status_received']==$this->config->item('system_status_po_received_received'))
            {

            }
            else
            {
                $data['status_received']=$this->config->item('system_status_po_received_received');
                $data['date_received']=$time;
                $data['user_received'] = $user->user_id;
            }
            Query_helper::update($this->config->item('table_sales_po'),$data,array("id = ".$id));

            $this->db->where('sales_po_id',$id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_sales_po_receives'));
            foreach($receive_data as $data)
            {
                Query_helper::add($this->config->item('table_sales_po_receives'),$data);
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
        $this->form_validation->set_rules('date_receive',$this->lang->line('LABEL_DATE_RECEIVED'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
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
        $this->db->where('po.status_delivered',$this->config->item('system_status_po_delivery_delivered'));
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
