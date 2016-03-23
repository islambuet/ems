<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_sales extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_sales');
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
        $this->controller_url='reports_sales';
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
            $data['title']="Search Sales";
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
            $data['warehouses']=Query_helper::get_info($this->config->item('table_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array());
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('name ASC'));
            $fiscal_years=Query_helper::get_info($this->config->item('table_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }
            $data['date_start']='';
            $data['date_end']=System_helper::display_date(time());

            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("reports_sales/search",$data,true));
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
            if($reports['report_name']=='po')
            {
                $data['title']="Approved Purchase Orders";
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_sales/list_po",$data,true));
            }
            elseif($reports['report_name']=='sales')
            {
                $data['title']="Sales Report";
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_sales/list_sales",$data,true));
            }
            else
            {
                $data['title']="Approved Purchase Orders";
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_sales/list_po",$data,true));
            }


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
    public function get_items_po()
    {
        $items=array();
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $customer_id=$this->input->post('customer_id');

        $report_type=$this->input->post('report_type');//means kg/pkt

        $warehouse_id=$this->input->post('warehouse_id');
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        $po_no=$this->input->post('po_no');
        if($po_no)
        {
            $po_no=intval($po_no);
            if(!($po_no>0))
            {
                $this->jsonReturn($items);
            }
        }
        $this->db->from($this->config->item('table_sales_po_details').' pod');

        $this->db->select('pod.*');



        $this->db->select('po.id po_no,po.date_po');
        $this->db->select('CONCAT(cus.customer_code," - ",cus.name) name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->select('v.name variety_name');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_sales_po').' po','po.id = pod.sales_po_id','INNER');
        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = po.customer_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =pod.variety_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');

        $this->db->where('pod.revision',1);
        $this->db->where('po.status_approved',$this->config->item('system_status_po_approval_approved'));
        if($po_no>0)
        {
            $this->db->where('po.id',$po_no);
        }
        else
        {
            if($date_end>0)
            {
                $this->db->where('po.date_approved <=',$date_end);
            }
            if($date_start>0)
            {
                $this->db->where('po.date_approved >=',$date_start);
            }
            if($warehouse_id>0)
            {
                $this->db->where('po.warehouse_id',$warehouse_id);
            }
            if($crop_id>0)
            {
                $this->db->where('crop.id',$crop_id);
            }
            if($crop_type_id>0)
            {
                $this->db->where('type.id',$crop_type_id);
            }
            if($variety_id>0)
            {
                $this->db->where('pod.variety_id',$variety_id);
            }
            if($pack_size_id>0)
            {
                $this->db->where('pod.pack_size_id',$pack_size_id);
            }
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
        }
        $this->db->order_by('po.id','ASC');
        $results=$this->db->get()->result_array();
        $current_po_no='';
        $total_price=0;
        $total_quantity=0;
        $total_bonus_quantity=0;
        $grand_total_price=0;
        $grand_total_quantity=0;
        $grand_total_bonus_quantity=0;
        if(!$results)
        {
            $this->jsonReturn($items);
        }

        foreach($results as $count=>$result)
        {
            $info=array();
            if($count>0)
            {
                if($current_po_no!=$result['po_no'])
                {
                    $current_po_no=$result['po_no'];
                    $total_info=array();
                    $total_info['po_no']='';
                    $total_info['name']='';
                    $total_info['date_po']='';
                    $total_info['division_name']='';
                    $total_info['zone_name']='';
                    $total_info['territory_name']='';
                    $total_info['district_name']='';
                    $total_info['crop_name']='';
                    $total_info['crop_type_name']='';
                    $total_info['variety_name']='Total';
                    $total_info['pack_size']='';
                    $total_info['quantity']=$total_quantity;
                    $total_info['bonus_pack_size']='';
                    $total_info['quantity_bonus']=$total_bonus_quantity;
                    $total_info['variety_price']='';
                    $total_info['price_total']=$total_price;
                    $items[]=$this->get_po_row($report_type,$total_info);


                    $total_price=0;
                    $total_quantity=0;
                    $total_bonus_quantity=0;
                    $info['po_no']=$result['po_no'];
                    $info['name']=$result['name'];
                    $info['date_po']=$result['date_po'];
                    $info['division_name']=$result['division_name'];
                    $info['zone_name']=$result['zone_name'];
                    $info['territory_name']=$result['territory_name'];
                    $info['district_name']=$result['district_name'];
                }
                else
                {
                    $info['po_no']='';
                    $info['name']='';
                    $info['date_po']='';
                    $info['division_name']='';
                    $info['zone_name']='';
                    $info['territory_name']='';
                    $info['district_name']='';
                }


            }
            else
            {
                $current_po_no=$result['po_no'];
                $info['po_no']=$result['po_no'];
                $info['name']=$result['name'];
                $info['date_po']=$result['date_po'];
                $info['division_name']=$result['division_name'];
                $info['zone_name']=$result['zone_name'];
                $info['territory_name']=$result['territory_name'];
                $info['district_name']=$result['district_name'];

            }
            $info['crop_name']=$result['crop_name'];
            $info['crop_type_name']=$result['crop_type_name'];
            $info['variety_name']=$result['variety_name'];
            $info['pack_size']=$result['pack_size'];
            $info['quantity']=$result['quantity'];

            $info['bonus_pack_size']=$result['bonus_pack_size'];
            $info['quantity_bonus']=$result['quantity_bonus'];


            $quantity=$result['quantity'];
            $quantity_bonus=$result['quantity_bonus'];
            if($report_type=='weight')
            {
                $quantity=$result['quantity']*$info['pack_size'];
                $quantity_bonus=$result['quantity_bonus']*$info['bonus_pack_size'];
                $info['quantity']=$quantity;
                $info['quantity_bonus']=$quantity_bonus;
            }
            $total_quantity+=$quantity;
            $grand_total_quantity+=$quantity;
            $total_bonus_quantity+=$quantity_bonus;
            $grand_total_bonus_quantity+=$quantity_bonus;




            $info['variety_price']=$result['variety_price'];
            $price=$result['quantity']*$result['variety_price'];
            $info['price_total']=$price;
            $total_price+=$price;
            $grand_total_price+=$price;
            $items[]=$this->get_po_row($report_type,$info);
            //$items[]=$this->get_po_row($report_type,$result['po_no'],$result['name'],$result['date_po'],$result['division_name'],$result['zone_name'],$result['territory_name'],$result['district_name'],$result['crop_name'],$result['crop_type_name'],$result['variety_name'],$result['pack_size'],$result['quantity'],$result['bonus_pack_size'],$result['quantity_bonus'],$result['variety_price'],'123');

        }

        $total_info=array();
        $total_info['po_no']='';
        $total_info['name']='';
        $total_info['date_po']='';
        $total_info['division_name']='';
        $total_info['zone_name']='';
        $total_info['territory_name']='';
        $total_info['district_name']='';
        $total_info['crop_name']='';
        $total_info['crop_type_name']='';
        $total_info['variety_name']='Total';
        $total_info['pack_size']='';
        $total_info['quantity']=$total_quantity;
        $total_info['bonus_pack_size']='';
        $total_info['quantity_bonus']=$total_bonus_quantity;
        $total_info['variety_price']='';
        $total_info['price_total']=$total_price;
        $items[]=$this->get_po_row($report_type,$total_info);

        $total_info=array();
        $total_info['po_no']='';
        $total_info['name']='';
        $total_info['date_po']='';
        $total_info['division_name']='';
        $total_info['zone_name']='';
        $total_info['territory_name']='';
        $total_info['district_name']='';
        $total_info['crop_name']='';
        $total_info['crop_type_name']='Grand Total';
        $total_info['variety_name']='';
        $total_info['pack_size']='';
        $total_info['quantity']=$grand_total_quantity;
        $total_info['bonus_pack_size']='';
        $total_info['quantity_bonus']=$grand_total_bonus_quantity;
        $total_info['variety_price']='';
        $total_info['price_total']=$grand_total_price;
        $items[]=$this->get_po_row($report_type,$total_info);
        $this->jsonReturn($items);
    }
    //private function get_po_row($report_type,$po_no,$name,$date_po,$division_name,$zone_name,$territory_name,$district_name,$crop_name,$crop_type_name,$variety_name,$pack_size,$quantity,$bonus_pack_size,$quantity_bonus,$variety_price,$price_total)
    private function get_po_row($report_type,$info)
    {
        $row=array();
        $row['po_no']=$info['po_no'];
        if($info['po_no'])
        {
            $row['po_no']=str_pad($info['po_no'],$this->config->item('system_po_no_length'),'0',STR_PAD_LEFT);
        }

        $row['name']=$info['name'];
        $row['date_po']='';
        if($info['date_po'])
        {
            $row['date_po']=System_helper::display_date($info['date_po']);
        }

        $row['division_name']=$info['division_name'];
        $row['zone_name']=$info['zone_name'];
        $row['territory_name']=$info['territory_name'];
        $row['district_name']=$info['district_name'];
        $row['crop_name']=$info['crop_name'];
        $row['crop_type_name']=$info['crop_type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['pack_size']=$info['pack_size'];
        $row['quantity']=$info['quantity'];
        $row['bonus_pack_size']=$info['bonus_pack_size'];
        $row['quantity_bonus']=$info['quantity_bonus'];
        $row['variety_price']='';
        if($info['variety_price'])
        {
            $row['variety_price']=number_format($info['variety_price'],2);
        }

        $row['price_total']=number_format($info['price_total'],2);

        if($report_type=='weight')
        {
            $row['quantity']=number_format($info['quantity']/1000,3,'.','');
            $row['quantity_bonus']=number_format($info['quantity_bonus']/1000,3,'.','');
            if($info['variety_price'])
            {
                $row['variety_price']=number_format($info['variety_price']*1000/$info['pack_size'],2);
            }

        }
        return $row;
    }
    public function get_items_sales()
    {
        $items=array();
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $customer_id=$this->input->post('customer_id');

        $report_type=$this->input->post('report_type');//means kg/pkt

        $warehouse_id=$this->input->post('warehouse_id');
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $pack_size_id=$this->input->post('pack_size_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        $po_no=$this->input->post('po_no');
        if($po_no)
        {
            $po_no=intval($po_no);
            if(!($po_no>0))
            {
                $this->jsonReturn($items);
            }
        }
        $this->db->from($this->config->item('table_sales_po_details').' pod');

        $this->db->select('pod.*');



        $this->db->select('po.id po_no,po.date_po');
        $this->db->select('CONCAT(cus.customer_code," - ",cus.name) name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->select('v.name variety_name');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->select('crop.name crop_name');

        $this->db->join($this->config->item('table_sales_po').' po','po.id = pod.sales_po_id','INNER');
        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = po.customer_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =pod.variety_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');

        $this->db->where('pod.revision',1);
        $this->db->where('po.status_approved',$this->config->item('system_status_po_approval_approved'));
        if($po_no>0)
        {
            $this->db->where('po.id',$po_no);
        }
        else
        {
            if($date_end>0)
            {
                $this->db->where('po.date_approved <=',$date_end);
            }
            if($date_start>0)
            {
                $this->db->where('po.date_approved >=',$date_start);
            }
            if($warehouse_id>0)
            {
                $this->db->where('po.warehouse_id',$warehouse_id);
            }
            if($crop_id>0)
            {
                $this->db->where('crop.id',$crop_id);
            }
            if($crop_type_id>0)
            {
                $this->db->where('type.id',$crop_type_id);
            }
            if($variety_id>0)
            {
                $this->db->where('pod.variety_id',$variety_id);
            }
            if($pack_size_id>0)
            {
                $this->db->where('pod.pack_size_id',$pack_size_id);
            }
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
        }
        $this->db->order_by('po.id','ASC');
        $results=$this->db->get()->result_array();
        $current_po_no='';
        $total_price=0;
        $total_quantity=0;
        $total_bonus_quantity=0;
        $total_quantity_return=0;
        $total_quantity_bonus_return=0;
        $total_quantity_actual=0;
        $total_price_actual=0;

        $grand_total_price=0;
        $grand_total_quantity=0;
        $grand_total_bonus_quantity=0;

        $grand_quantity_return=0;
        $grand_quantity_bonus_return=0;
        $grand_quantity_actual=0;
        $grand_price_actual=0;
        if(!$results)
        {
            $this->jsonReturn($items);
        }

        foreach($results as $count=>$result)
        {
            $info=array();
            if($count>0)
            {
                if($current_po_no!=$result['po_no'])
                {
                    $current_po_no=$result['po_no'];
                    $total_info=array();
                    $total_info['po_no']='';
                    $total_info['name']='';
                    $total_info['date_po']='';
                    $total_info['division_name']='';
                    $total_info['zone_name']='';
                    $total_info['territory_name']='';
                    $total_info['district_name']='';
                    $total_info['crop_name']='';
                    $total_info['crop_type_name']='';
                    $total_info['variety_name']='Total';
                    $total_info['pack_size']='';
                    $total_info['quantity']=$total_quantity;
                    $total_info['bonus_pack_size']='';
                    $total_info['quantity_bonus']=$total_bonus_quantity;
                    $total_info['quantity_return']=$total_quantity_return;
                    $total_info['quantity_bonus_return']=$total_quantity_bonus_return;
                    $total_info['quantity_actual']=$total_quantity_actual;

                    $total_info['variety_price']='';
                    $total_info['price_total_actual']=$total_price_actual;
                    $total_info['price_total']=$total_price;
                    $items[]=$this->get_sales_row($report_type,$total_info);


                    $total_price=0;
                    $total_price_actual=0;
                    $total_quantity=0;
                    $total_bonus_quantity=0;
                    $total_quantity_return=0;
                    $total_quantity_bonus_return=0;
                    $total_quantity_actual=0;
                    $info['po_no']=$result['po_no'];
                    $info['name']=$result['name'];
                    $info['date_po']=$result['date_po'];
                    $info['division_name']=$result['division_name'];
                    $info['zone_name']=$result['zone_name'];
                    $info['territory_name']=$result['territory_name'];
                    $info['district_name']=$result['district_name'];
                }
                else
                {
                    $info['po_no']='';
                    $info['name']='';
                    $info['date_po']='';
                    $info['division_name']='';
                    $info['zone_name']='';
                    $info['territory_name']='';
                    $info['district_name']='';
                }


            }
            else
            {
                $current_po_no=$result['po_no'];
                $info['po_no']=$result['po_no'];
                $info['name']=$result['name'];
                $info['date_po']=$result['date_po'];
                $info['division_name']=$result['division_name'];
                $info['zone_name']=$result['zone_name'];
                $info['territory_name']=$result['territory_name'];
                $info['district_name']=$result['district_name'];

            }
            $info['crop_name']=$result['crop_name'];
            $info['crop_type_name']=$result['crop_type_name'];
            $info['variety_name']=$result['variety_name'];
            $info['pack_size']=$result['pack_size'];
            $info['quantity']=$result['quantity'];

            $info['bonus_pack_size']=$result['bonus_pack_size'];
            $info['quantity_bonus']=$result['quantity_bonus'];

            /*{ name: 'quantity_return', type: 'string' },
            { name: 'quantity_bonus_return', type: 'string' },
            { name: 'quantity_actual', type: 'string' },*/



            $quantity=$result['quantity'];
            $quantity_bonus=$result['quantity_bonus'];
            $quantity_return=0;
            $quantity_bonus_return=0;
            $quantity_actual=$result['quantity'];
            $info['price_total_actual']=$result['quantity']*$result['variety_price'];
            if($this->get_return_validity($date_start,$date_end,$result['date_return']))
            {
                $quantity_return=$result['quantity_return'];
                $quantity_bonus_return=$result['quantity_bonus_return'];
                $quantity_actual=$result['quantity']-$result['quantity_return'];
                $info['price_total_actual']=($result['quantity']-$result['quantity_return'])*$result['variety_price'];
            }

            if($report_type=='weight')
            {
                $quantity=$result['quantity']*$info['pack_size'];
                $quantity_bonus=$result['quantity_bonus']*$info['bonus_pack_size'];
                $info['quantity']=$quantity;
                $info['quantity_bonus']=$quantity_bonus;
                $quantity_return=$quantity_return*$info['pack_size'];
                $quantity_bonus_return=$quantity_bonus_return*$info['bonus_pack_size'];
                $quantity_actual=$quantity_actual*$info['pack_size'];
            }
            $info['quantity_return']=$quantity_return;
            $total_quantity_return+=$quantity_return;
            $grand_quantity_return+=$quantity_return;

            $info['quantity_bonus_return']=$quantity_bonus_return;
            $total_quantity_bonus_return+=$quantity_bonus_return;
            $grand_quantity_bonus_return+=$quantity_bonus_return;

            $info['quantity_actual']=$quantity_actual;
            $total_quantity_actual+=$quantity_actual;
            $grand_quantity_actual+=$quantity_actual;

            $total_quantity+=$quantity;
            $grand_total_quantity+=$quantity;
            $total_bonus_quantity+=$quantity_bonus;
            $grand_total_bonus_quantity+=$quantity_bonus;




            $info['variety_price']=$result['variety_price'];
            $price=$result['quantity']*$result['variety_price'];
            $info['price_total']=$price;
            $total_price+=$price;
            $grand_total_price+=$price;
            $total_price_actual+=$info['price_total_actual'];
            $grand_price_actual+=$info['price_total_actual'];
            $items[]=$this->get_sales_row($report_type,$info);
            //$items[]=$this->get_po_row($report_type,$result['po_no'],$result['name'],$result['date_po'],$result['division_name'],$result['zone_name'],$result['territory_name'],$result['district_name'],$result['crop_name'],$result['crop_type_name'],$result['variety_name'],$result['pack_size'],$result['quantity'],$result['bonus_pack_size'],$result['quantity_bonus'],$result['variety_price'],'123');

        }

        $total_info=array();
        $total_info['po_no']='';
        $total_info['name']='';
        $total_info['date_po']='';
        $total_info['division_name']='';
        $total_info['zone_name']='';
        $total_info['territory_name']='';
        $total_info['district_name']='';
        $total_info['crop_name']='';
        $total_info['crop_type_name']='';
        $total_info['variety_name']='Total';
        $total_info['pack_size']='';
        $total_info['quantity']=$total_quantity;
        $total_info['bonus_pack_size']='';
        $total_info['quantity_bonus']=$total_bonus_quantity;
        $total_info['quantity_return']=$quantity_return;
        $total_info['quantity_bonus_return']=$quantity_bonus_return;
        $total_info['quantity_actual']=$quantity_actual;
        $total_info['variety_price']='';
        $total_info['price_total_actual']=$total_price_actual;
        $total_info['price_total']=$total_price;
        $items[]=$this->get_sales_row($report_type,$total_info);

        $total_info=array();
        $total_info['po_no']='';
        $total_info['name']='';
        $total_info['date_po']='';
        $total_info['division_name']='';
        $total_info['zone_name']='';
        $total_info['territory_name']='';
        $total_info['district_name']='';
        $total_info['crop_name']='';
        $total_info['crop_type_name']='Grand Total';
        $total_info['variety_name']='';
        $total_info['pack_size']='';
        $total_info['quantity']=$grand_total_quantity;
        $total_info['bonus_pack_size']='';
        $total_info['quantity_bonus']=$grand_total_bonus_quantity;
        $total_info['quantity_return']=$grand_quantity_return;
        $total_info['quantity_bonus_return']=$grand_quantity_bonus_return;
        $total_info['quantity_actual']=$grand_quantity_actual;
        $total_info['variety_price']='';
        $total_info['price_total']=$grand_total_price;
        $total_info['price_total_actual']=$grand_price_actual;
        $items[]=$this->get_sales_row($report_type,$total_info);
        $this->jsonReturn($items);
    }
    private function get_return_validity($time_start,$time_end,$time)
    {
        if($time>0)
        {
            if(($time_start>0)&&($time_end>0))
            {
                if(($time>=$time_start)&&($time<=$time_end))
                {
                    return true;
                }
                else
                {
                    return false;
                }

            }
            elseif(($time_end>0))
            {
                if(($time<=$time_end))
                {
                    return true;
                }
                else
                {
                    return false;
                }

            }
            elseif(($time_start>0))
            {
                if(($time>=$time_start))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return true;
            }

        }
        else
        {
            return false;
        }

    }
    private function get_sales_row($report_type,$info)
    {
        $row=array();
        $row['po_no']=$info['po_no'];
        if($info['po_no'])
        {
            $row['po_no']=str_pad($info['po_no'],$this->config->item('system_po_no_length'),'0',STR_PAD_LEFT);
        }

        $row['name']=$info['name'];
        $row['date_po']='';
        if($info['date_po'])
        {
            $row['date_po']=System_helper::display_date($info['date_po']);
        }

        $row['division_name']=$info['division_name'];
        $row['zone_name']=$info['zone_name'];
        $row['territory_name']=$info['territory_name'];
        $row['district_name']=$info['district_name'];
        $row['crop_name']=$info['crop_name'];
        $row['crop_type_name']=$info['crop_type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['pack_size']=$info['pack_size'];
        $row['quantity']=$info['quantity'];
        $row['bonus_pack_size']=$info['bonus_pack_size'];
        $row['quantity_bonus']=$info['quantity_bonus'];

        $row['quantity_return']=$info['quantity_return'];
        $row['quantity_bonus_return']=$info['quantity_bonus_return'];
        $row['quantity_actual']=$info['quantity_actual'];
        $row['variety_price']='';
        if($info['variety_price'])
        {
            $row['variety_price']=number_format($info['variety_price'],2);
        }

        $row['price_total']=number_format($info['price_total'],2);
        $row['price_total_actual']=number_format($info['price_total_actual'],2);

        if($report_type=='weight')
        {
            $row['quantity']=number_format($info['quantity']/1000,3,'.','');
            $row['quantity_bonus']=number_format($info['quantity_bonus']/1000,3,'.','');
            $row['quantity_return']=number_format($info['quantity_return']/1000,3,'.','');
            $row['quantity_bonus_return']=number_format($info['quantity_bonus_return']/1000,3,'.','');
            $row['quantity_actual']=number_format($info['quantity_actual']/1000,3,'.','');
            if($info['variety_price'])
            {
                $row['variety_price']=number_format($info['variety_price']*1000/$info['pack_size'],2);
            }

        }
        return $row;
    }


}
