<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_po_delivery extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_po_delivery');
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
        $this->controller_url='reports_po_delivery';
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
            $data['title']="PO Delivery Report Search";
            $ajax['status']=true;
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
                            $data['customers']=Query_helper::get_info($this->config->item('table_csetup_customers'),array('id value','name text'),array('district_id ='.$this->locations['district_id'],'status ="'.$this->config->item('system_status_active').'"'));
                        }
                    }
                }
            }

            $fiscal_years=Query_helper::get_info($this->config->item('table_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("reports_po_delivery/search",$data,true));
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
            else
            {
                $reports['date_end']=time();
            }
            if($reports['date_start']>$reports['date_end'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Start Date Must be less than End Date';
                $this->jsonReturn($ajax);
            }

            $keys=',';

            foreach($reports as $elem=>$value)
            {
                $keys.=$elem.":'".$value."',";
            }

            $data['keys']=trim($keys,',');


            $ajax['status']=true;
            $data['title']="PO Delivery Report";
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_po_delivery/list",$data,true));

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
        $district_id=$this->input->post('district_id');
        $customer_id=$this->input->post('customer_id');
        $customer_type=$this->input->post('customer_type');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        $this->db->from($this->config->item('table_sales_po_details').' pod');

        $this->db->select('SUM(pod.quantity) quantity_total');
        $this->db->select('SUM(pod.quantity*pod.pack_size) quantity_weight');

        $this->db->select('po.*');
        $this->db->select('CONCAT(cus.customer_code," - ",cus.name) customer_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');

        $this->db->select('courier.name courier_name');

        $this->db->select('delivery.date_delivery date_delivery,delivery.date_booking,delivery.track_no,delivery.remarks,delivery.invoice_no');

        $this->db->join($this->config->item('table_sales_po').' po','po.id = pod.sales_po_id','INNER');
        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = po.customer_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_sales_po_delivery').' delivery','delivery.sales_po_id = po.id','INNER');
        $this->db->join($this->config->item('table_basic_setup_couriers').' courier','courier.id = delivery.courier_id','LEFT');

        if($division_id>0)
        {
            $this->db->where('division.id',$division_id);
            if($zone_id>0)
            {
                $this->db->where('zone.id',$zone_id);
                if($territory_id>0)
                {
                    $this->db->where('t.id',$territory_id);
                    if($district_id>0)
                    {
                        $this->db->where('d.id',$district_id);
                        if($customer_id>0)
                        {
                            $this->db->where('cus.id',$customer_id);
                        }
                    }
                }
            }
        }
        if($customer_type)
        {
            $this->db->where('cus.type',$customer_type);
        }
        $this->db->where('delivery.revision',1);
        if($date_end>0)
        {
            $this->db->where('delivery.date_delivery <=',$date_end);
        }
        if($date_start>0)
        {
            $this->db->where('delivery.date_delivery >=',$date_start);
        }

        $this->db->where('pod.revision',1);
        $this->db->where('po.status_delivered',$this->config->item('system_status_po_delivery_delivered'));
        $this->db->group_by('po.id');
        $this->db->order_by('division.ordering','ASC');
        $this->db->order_by('zone.ordering','ASC');
        $this->db->order_by('t.ordering','ASC');
        $this->db->order_by('d.ordering','ASC');
        $this->db->order_by('cus.ordering','ASC');
        $this->db->order_by('po.id','DESC');
        $results=$this->db->get()->result_array();
        
        $division_name='';
        $zone_name='';
        $territory_name='';
        $district_name='';
        $customer_name='';

        foreach($results as $result)
        {
            $item=array();
            $item['id']=$result['id'];
            $item['division_name']=$result['division_name'];
            $item['zone_name']=$result['zone_name'];
            $item['territory_name']=$result['territory_name'];
            $item['district_name']=$result['district_name'];
            $item['customer_name']=$result['customer_name'];
            if($division_name!=$result['division_name'])
            {
                $division_name=$result['division_name'];
                $zone_name=$result['zone_name'];
                $territory_name=$result['territory_name'];
                $district_name=$result['district_name'];
                $customer_name=$result['customer_name'];
            }
            else
            {
                $item['division_name']='';
                if($zone_name!=$result['zone_name'])
                {
                    $zone_name=$result['zone_name'];
                    $territory_name=$result['territory_name'];
                    $district_name=$result['district_name'];
                    $customer_name=$result['customer_name'];
                }
                else
                {
                    $item['zone_name']='';
                    if($territory_name!=$result['territory_name'])
                    {
                        $territory_name=$result['territory_name'];
                        $district_name=$result['district_name'];
                        $customer_name=$result['customer_name'];
                    }
                    else
                    {
                        $item['territory_name']='';
                        if($district_name!=$result['district_name'])
                        {
                            $district_name=$result['district_name'];
                            $customer_name=$result['customer_name'];
                        }
                        else
                        {
                            $item['district_name']='';
                            if($customer_name!=$result['customer_name'])
                            {
                                $customer_name=$result['customer_name'];
                            }
                            else
                            {
                                $item['customer_name']='';
                            }
                        }
                    }
                }
            }
            $item['po_no']=str_pad($result['id'],$this->config->item('system_po_no_length'),'0',STR_PAD_LEFT);
            $item['date_po']=System_helper::display_date($result['date_po']);
            $item['date_approved']=System_helper::display_date($result['date_approved']);
            $item['date_delivery']=System_helper::display_date($result['date_delivery']);
            $item['quantity_total']=$result['quantity_total'];
            $item['quantity_weight']=number_format($result['quantity_weight']/1000,3,'.','');
            $item['courier_name']=$result['courier_name'];
            $item['date_booking']=System_helper::display_date($result['date_booking']);
            $item['track_no']=$result['track_no'];
            $item['invoice_no']=$result['invoice_no'];
            $item['remarks']=$result['remarks'];
            $items[]=$item;
        }
        $this->jsonReturn($items);
    }
}
