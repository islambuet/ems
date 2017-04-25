<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Oreports_sale extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Oreports_sale');
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
        $this->controller_url='oreports_sale';
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
        elseif($action=="get_items_area_sales")
        {
            $this->system_get_items_area_sales();
        }
        elseif($action=="get_items_outlets_sales")
        {
            $this->system_get_items_outlets_sales();
        }
        elseif($action=='get_items_variety_sale')
        {
            $this->system_get_items_variety_sale();
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
            $data['title']="Outlets Sale Report";
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
                            $data['customers']=Query_helper::get_info($this->config->item('table_csetup_customers'),array('id value','name text'),array('district_id ='.$this->locations['district_id'],'status ="'.$this->config->item('system_status_active').'"','type ="Outlet"'));
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

            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/search",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url);

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
    private function system_list()
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            $reports=$this->input->post('report');
            $reports['date_end']=System_helper::get_time($reports['date_end']);
            $reports['date_end']=$reports['date_end']+3600*24-1;
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['date_start']>=$reports['date_end'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Starting Date should be less than End date';
                $this->jsonReturn($ajax);
            }
            $data['options']=$reports;

            $ajax['status']=true;
            if($reports['report_name']=='area_sales')
            {
                if($reports['customer_id']>0)
                {
                    $data['areas']='Outlet';
                }
                elseif($reports['district_id']>0)
                {
                    $data['areas']='Outlets';
                }
                elseif($reports['territory_id']>0)
                {
                    $data['areas']='Districts';
                }
                elseif($reports['zone_id']>0)
                {
                    $data['areas']='Territories';
                }
                elseif($reports['division_id']>0)
                {
                    $data['areas']='Zones';
                }
                else
                {
                    $data['areas']='Divisions';
                }
                $data['title']="Area Wise Sales Report";
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_area_sales",$data,true));
            }
            elseif($reports['report_name']=='outlets_sales')
            {
                $data['title']="Outlet Wise Sales Report";
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_outlets_sales",$data,true));
            }
            elseif($reports['report_name']=='variety_sale')
            {
                $data['title']="Product Sales Report";
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_variety_sale",$data,true));
            }
            else
            {
                $this->message='Invalid Report type';
            }

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
    private function system_get_items_area_sales()
    {
        $items=array();
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $customer_id=$this->input->post('customer_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');
        if($customer_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_csetup_customers'),array('id value','name text'),array('id ='.$customer_id,'status ="'.$this->config->item('system_status_active').'"'));
            $location_type='customer_id';
        }
        elseif($district_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_csetup_customers'),array('id value','name text'),array('district_id ='.$district_id,'status ="'.$this->config->item('system_status_active').'"','type ="Outlet"'));
            $location_type='customer_id';
        }
        elseif($territory_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$territory_id,'status ="'.$this->config->item('system_status_active').'"'));
            $location_type='district_id';
        }
        elseif($zone_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$zone_id,'status ="'.$this->config->item('system_status_active').'"'));
            $location_type='territory_id';
        }
        elseif($division_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$division_id,'status ="'.$this->config->item('system_status_active').'"'));
            $location_type='zone_id';
        }
        else
        {
            $areas=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $location_type='division_id';
        }
        $area_initial=array();
        //setting 0
        foreach($areas as $area)
        {
            $area_initial[$area['value']]['id']=$area['value'];
            $area_initial[$area['value']]['area']=$area['text'];
            $area_initial[$area['value']]['sale_total']=0;
            $area_initial[$area['value']]['payable_total']=0;
            $area_initial[$area['value']]['sale_canceled']=0;
            $area_initial[$area['value']]['payable_canceled']=0;
        }

        //total sales
        $this->db->from($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale').' sale');
        $this->db->select('cus.id customer_id');
        $this->db->select('d.id district_id');
        $this->db->select('t.id territory_id');
        $this->db->select('zone.id zone_id');
        $this->db->select('zone.division_id division_id');

        $this->db->select('SUM(sale.amount_total) sale_total');
        $this->db->select('SUM(sale.amount_payable) payable_total');

        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = sale.customer_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        if($division_id>0)
        {
            $this->db->where('zone.division_id',$division_id);
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
        $this->db->where('sale.date_sale >=',$date_start);
        $this->db->where('sale.date_sale <=',$date_end);
        $this->db->group_by(array($location_type));
        $results=$this->db->get()->result_array();
        if($results)
        {
            foreach($results as $result)
            {

                $area_initial[$result[$location_type]]['sale_total']=$result['sale_total'];
                $area_initial[$result[$location_type]]['payable_total']=$result['payable_total'];
            }
        }

        //total canceled
        $this->db->from($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale').' sale');
        $this->db->select('cus.id customer_id');
        $this->db->select('d.id district_id');
        $this->db->select('t.id territory_id');
        $this->db->select('zone.id zone_id');
        $this->db->select('zone.division_id division_id');

        $this->db->select('SUM(sale.amount_total) sale_canceled');
        $this->db->select('SUM(sale.amount_payable) payable_canceled');

        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = sale.customer_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        if($division_id>0)
        {
            $this->db->where('zone.division_id',$division_id);
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
        $this->db->where('sale.status',$this->config->item('system_status_inactive'));
        $this->db->where('sale.date_canceled >=',$date_start);
        $this->db->where('sale.date_canceled <=',$date_end);
        $this->db->group_by(array($location_type));
        $results=$this->db->get()->result_array();
        if($results)
        {
            foreach($results as $result)
            {

                $area_initial[$result[$location_type]]['sale_canceled']=$result['sale_canceled'];
                $area_initial[$result[$location_type]]['payable_canceled']=$result['payable_canceled'];
            }
        }
        $grand_total=array();
        $grand_total['id']=0;
        $grand_total['area']='Grand Total';
        $grand_total['sale_total']=0;
        $grand_total['payable_total']=0;
        $grand_total['sale_canceled']=0;
        $grand_total['payable_canceled']=0;
        foreach($area_initial as $item)
        {
            $grand_total['sale_total']+=$item['sale_total'];
            $grand_total['payable_total']+=$item['payable_total'];
            $grand_total['sale_canceled']+=$item['sale_canceled'];
            $grand_total['payable_canceled']+=$item['payable_canceled'];
            $items[]=$this->get_area_sales_row($item);
        }
        $items[]=$this->get_area_sales_row($grand_total);
        $this->jsonReturn($items);


    }
    private function get_area_sales_row($info)
    {
        $row=array();
        $row['id']=$info['id'];
        $row['area']=$info['area'];
        if($info['sale_total']!=0)
        {
            $row['sale_total']=number_format($info['sale_total'],2);
        }
        else
        {
            $row['sale_total']='';
        }
        if($info['payable_total']!=0)
        {
            $row['payable_total']=number_format($info['payable_total'],2);
        }
        else
        {
            $row['payable_total']='';
        }
        if(($info['sale_total']-$info['payable_total'])!=0)
        {
            $row['discount_total']=number_format(($info['sale_total']-$info['payable_total']),2);
        }
        else
        {
            $row['discount_total']='';
        }
        if($info['sale_canceled']!=0)
        {
            $row['sale_canceled']=number_format($info['sale_canceled'],2);
        }
        else
        {
            $row['sale_canceled']='';
        }
        if($info['payable_canceled']!=0)
        {
            $row['payable_canceled']=number_format($info['payable_canceled'],2);
        }
        else
        {
            $row['payable_canceled']='';
        }
        if(($info['sale_canceled']-$info['payable_canceled'])!=0)
        {
            $row['discount_canceled']=number_format(($info['sale_canceled']-$info['payable_canceled']),2);
        }
        else
        {
            $row['discount_canceled']='';
        }
        if(($info['sale_total']-$info['sale_canceled'])!=0)
        {
            $row['sale_actual']=number_format(($info['sale_total']-$info['sale_canceled']),2);
        }
        else
        {
            $row['sale_actual']='';
        }
        if(($info['payable_total']-$info['payable_canceled'])!=0)
        {
            $row['payable_actual']=number_format(($info['payable_total']-$info['payable_canceled']),2);
        }
        else
        {
            $row['payable_actual']='';
        }
        if(($info['sale_total']-$info['sale_canceled']-$info['payable_total']+$info['payable_canceled'])!=0)
        {
            $row['discount_actual']=number_format(($info['sale_total']-$info['sale_canceled']-$info['payable_total']+$info['payable_canceled']),2);
        }
        else
        {
            $row['discount_actual']='';
        }
        return $row;

    }
    private function system_get_items_outlets_sales()
    {
        $items=array();
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $customer_id=$this->input->post('customer_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        $this->db->from($this->config->item('table_csetup_customers').' cus');
        $this->db->select('cus.id,cus.name outlet_name');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        if($division_id>0)
        {
            $this->db->where('zone.division_id',$division_id);
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
        $this->db->where('cus.type','Outlet');
        $this->db->order_by('cus.ordering','ASC');
        $this->db->order_by('cus.id','ASC');
        $this->db->where('cus.status',$this->config->item('system_status_active'));
        $results=$this->db->get()->result_array();
        $outlets=array();
        $outlet_ids=array();
        foreach($results as $result)
        {
            $outlets[$result['id']]['id']=$result['id'];
            $outlets[$result['id']]['outlet_name']=$result['outlet_name'];
            $outlets[$result['id']]['sale_total']=0;
            $outlets[$result['id']]['payable_total']=0;
            $outlets[$result['id']]['sale_canceled']=0;
            $outlets[$result['id']]['payable_canceled']=0;
            $outlet_ids[$result['id']]=$result['id'];
        }
        if(sizeof($outlet_ids)>0)
        {
            //total sales
            $this->db->from($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale').' sale');
            $this->db->select('sale.customer_id');
            $this->db->select('SUM(sale.amount_total) sale_total');
            $this->db->select('SUM(sale.amount_payable) payable_total');
            $this->db->where_in('sale.customer_id',$outlet_ids);
            $this->db->where('sale.date_sale >=',$date_start);
            $this->db->where('sale.date_sale <=',$date_end);
            $this->db->group_by('sale.customer_id');
            $results=$this->db->get()->result_array();
            if($results)
            {
                foreach($results as $result)
                {

                    $outlets[$result['customer_id']]['sale_total']=$result['sale_total'];
                    $outlets[$result['customer_id']]['payable_total']=$result['payable_total'];
                }
            }
            //total canceled
            $this->db->from($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale').' sale');
            $this->db->select('sale.customer_id customer_id');
            $this->db->select('SUM(sale.amount_total) sale_canceled');
            $this->db->select('SUM(sale.amount_payable) payable_canceled');
            $this->db->where_in('sale.customer_id',$outlet_ids);
            $this->db->where('sale.status',$this->config->item('system_status_inactive'));
            $this->db->where('sale.date_canceled >=',$date_start);
            $this->db->where('sale.date_canceled <=',$date_end);
            $this->db->group_by('sale.customer_id');
            $results=$this->db->get()->result_array();
            if($results)
            {
                foreach($results as $result)
                {

                    $outlets[$result['customer_id']]['sale_canceled']=$result['sale_canceled'];
                    $outlets[$result['customer_id']]['payable_canceled']=$result['payable_canceled'];
                }
            }

        }
        $grand_total=array();
        $grand_total['id']=0;
        $grand_total['outlet_name']='Grand Total';
        $grand_total['sale_total']=0;
        $grand_total['payable_total']=0;
        $grand_total['sale_canceled']=0;
        $grand_total['payable_canceled']=0;
        foreach($outlets as $item)
        {
            $grand_total['sale_total']+=$item['sale_total'];
            $grand_total['payable_total']+=$item['payable_total'];
            $grand_total['sale_canceled']+=$item['sale_canceled'];
            $grand_total['payable_canceled']+=$item['payable_canceled'];
            $items[]=$this->get_outlet_sales_row($item);
        }
        $items[]=$this->get_outlet_sales_row($grand_total);
        $this->jsonReturn($items);
    }
    private function get_outlet_sales_row($info)
    {
        $row=array();
        $row['id']=$info['id'];
        $row['outlet_name']=$info['outlet_name'];
        if($info['sale_total']!=0)
        {
            $row['sale_total']=number_format($info['sale_total'],2);
        }
        else
        {
            $row['sale_total']='';
        }
        if($info['payable_total']!=0)
        {
            $row['payable_total']=number_format($info['payable_total'],2);
        }
        else
        {
            $row['payable_total']='';
        }
        if(($info['sale_total']-$info['payable_total'])!=0)
        {
            $row['discount_total']=number_format(($info['sale_total']-$info['payable_total']),2);
        }
        else
        {
            $row['discount_total']='';
        }
        if($info['sale_canceled']!=0)
        {
            $row['sale_canceled']=number_format($info['sale_canceled'],2);
        }
        else
        {
            $row['sale_canceled']='';
        }
        if($info['payable_canceled']!=0)
        {
            $row['payable_canceled']=number_format($info['payable_canceled'],2);
        }
        else
        {
            $row['payable_canceled']='';
        }
        if(($info['sale_canceled']-$info['payable_canceled'])!=0)
        {
            $row['discount_canceled']=number_format(($info['sale_canceled']-$info['payable_canceled']),2);
        }
        else
        {
            $row['discount_canceled']='';
        }
        if(($info['sale_total']-$info['sale_canceled'])!=0)
        {
            $row['sale_actual']=number_format(($info['sale_total']-$info['sale_canceled']),2);
        }
        else
        {
            $row['sale_actual']='';
        }
        if(($info['payable_total']-$info['payable_canceled'])!=0)
        {
            $row['payable_actual']=number_format(($info['payable_total']-$info['payable_canceled']),2);
        }
        else
        {
            $row['payable_actual']='';
        }
        if(($info['sale_total']-$info['sale_canceled']-$info['payable_total']+$info['payable_canceled'])!=0)
        {
            $row['discount_actual']=number_format(($info['sale_total']-$info['sale_canceled']-$info['payable_total']+$info['payable_canceled']),2);
        }
        else
        {
            $row['discount_actual']='';
        }
        return $row;

    }
    private function system_get_items_variety_sale()
    {
        $items=array();
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $customer_id=$this->input->post('customer_id');
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        //get customer ids
        $this->db->from($this->config->item('table_csetup_customers').' cus');
        $this->db->select('cus.id,cus.name outlet_name');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        if($division_id>0)
        {
            $this->db->where('zone.division_id',$division_id);
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
        $this->db->where('cus.type','Outlet');
        $this->db->where('cus.status',$this->config->item('system_status_active'));
        $results=$this->db->get()->result_array();
        $outlet_ids=array();
        foreach($results as $result)
        {
            $outlet_ids[$result['id']]=$result['id'];
        }
        if(sizeof($outlet_ids)>0)
        {
            //get variety infos
            $this->db->from($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale_details').' pod');
            $this->db->select('pod.variety_id,pod.pack_size_id,pod.pack_size');
            $this->db->select('v.name variety_name');
            $this->db->select('type.id type_id,type.name type_name');

            $this->db->select('crop.id crop_id,crop.name crop_name');
            $this->db->join($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale').' sale','sale.id =pod.sale_id','INNER');
            $this->db->join($this->config->item('system_db_ems').'.'.$this->config->item('table_setup_classification_varieties').' v','v.id =pod.variety_id','INNER');
            $this->db->join($this->config->item('system_db_ems').'.'.$this->config->item('table_setup_classification_crop_types').' type','type.id =v.crop_type_id','INNER');
            $this->db->join($this->config->item('system_db_ems').'.'.$this->config->item('table_setup_classification_crops').' crop','crop.id =type.crop_id','INNER');
            $this->db->where_in('sale.customer_id',$outlet_ids);
            $where='(sale.date_sale >='.$date_start.' AND sale.date_sale <='.$date_end.')';
            $where.=' OR (sale.date_canceled >='.$date_start.' AND sale.date_canceled <='.$date_end.')';
            $this->db->where('('.$where.')');
            if($crop_id>0)
            {
                $this->db->where('crop.id',$crop_id);
                if($crop_type_id>0)
                {
                    $this->db->where('type.id',$crop_type_id);
                    if($variety_id>0)
                    {
                        $this->db->where('v.id',$variety_id);
                    }
                }
            }
            $this->db->group_by(array('pod.variety_id','pod.pack_size_id'));
            $this->db->order_by('crop.ordering ASC');
            $this->db->order_by('type.ordering ASC');
            $this->db->order_by('v.ordering ASC');
            $results=$this->db->get()->result_array();
            $variety_ids=array();
            $varieties=array();
            foreach($results as $result)
            {
                $varieties[$result['variety_id']][$result['pack_size_id']]=$result;
                $varieties[$result['variety_id']][$result['pack_size_id']]['invoice_quantity']=0;
                $varieties[$result['variety_id']][$result['pack_size_id']]['invoice_quantity_kg']=0;
                $varieties[$result['variety_id']][$result['pack_size_id']]['invoice_amount']=0;
                $varieties[$result['variety_id']][$result['pack_size_id']]['invoice_discount']=0;
                $varieties[$result['variety_id']][$result['pack_size_id']]['invoice_payable']=0;
                $varieties[$result['variety_id']][$result['pack_size_id']]['cancel_quantity']=0;
                $varieties[$result['variety_id']][$result['pack_size_id']]['cancel_quantity_kg']=0;
                $varieties[$result['variety_id']][$result['pack_size_id']]['cancel_amount']=0;
                $varieties[$result['variety_id']][$result['pack_size_id']]['cancel_discount']=0;
                $varieties[$result['variety_id']][$result['pack_size_id']]['cancel_payable']=0;
                $variety_ids[$result['variety_id']]=$result['variety_id'];
            }
            if(sizeof($variety_ids)>0)
            {
                //sale count start to end
                $this->db->from($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale_details').' pod');
                $this->db->select('pod.variety_id,pod.pack_size_id,pod.pack_size');
                $this->db->select('SUM(pod.quantity_sale) invoice_quantity');
                $this->db->select('SUM(pod.quantity_sale * pod.pack_size) invoice_quantity_kg');
                $this->db->select('SUM(pod.quantity_sale * pod.price_unit) invoice_amount');
                $this->db->select('SUM(pod.quantity_sale * pod.price_unit * sale.discount_percentage/100) invoice_discount');
                $this->db->join($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale').' sale','sale.id =pod.sale_id','INNER');

                $this->db->where('pod.revision',1);
                $this->db->where_in('sale.customer_id',$outlet_ids);
                $this->db->where('sale.date_sale <=',$date_end);
                $this->db->where('sale.date_sale >=',$date_start);
                $this->db->where_in('pod.variety_id',$variety_ids);
                $this->db->group_by(array('pod.variety_id','pod.pack_size_id'));
                $results=$this->db->get()->result_array();
                foreach($results as $result)
                {
                    $varieties[$result['variety_id']][$result['pack_size_id']]['invoice_quantity']=$result['invoice_quantity'];
                    $varieties[$result['variety_id']][$result['pack_size_id']]['invoice_quantity_kg']=$result['invoice_quantity_kg'];
                    $varieties[$result['variety_id']][$result['pack_size_id']]['invoice_amount']=$result['invoice_amount'];
                    $varieties[$result['variety_id']][$result['pack_size_id']]['invoice_discount']=$result['invoice_discount'];
                }

                //sale cancel start to end
                $this->db->from($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale_details').' pod');
                $this->db->select('pod.variety_id,pod.pack_size_id,pod.pack_size');
                $this->db->select('SUM(pod.quantity_sale) cancel_quantity');
                $this->db->select('SUM(pod.quantity_sale * pod.pack_size) cancel_quantity_kg');
                $this->db->select('SUM(pod.quantity_sale * pod.price_unit) cancel_amount');
                $this->db->select('SUM(pod.quantity_sale * pod.price_unit * sale.discount_percentage/100) cancel_discount');
                $this->db->join($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale').' sale','sale.id =pod.sale_id','INNER');
                $this->db->where('sale.status',$this->config->item('system_status_inactive'));
                $this->db->where('pod.revision',1);
                $this->db->where_in('sale.customer_id',$outlet_ids);
                $this->db->where('sale.date_canceled <=',$date_end);
                $this->db->where('sale.date_canceled >=',$date_start);
                $this->db->where_in('pod.variety_id',$variety_ids);
                $this->db->group_by(array('pod.variety_id','pod.pack_size_id'));
                $results=$this->db->get()->result_array();
                foreach($results as $result)
                {
                    $varieties[$result['variety_id']][$result['pack_size_id']]['cancel_quantity']=$result['cancel_quantity'];
                    $varieties[$result['variety_id']][$result['pack_size_id']]['cancel_quantity_kg']=$result['cancel_quantity_kg'];
                    $varieties[$result['variety_id']][$result['pack_size_id']]['cancel_amount']=$result['cancel_amount'];
                    $varieties[$result['variety_id']][$result['pack_size_id']]['cancel_discount']=$result['cancel_discount'];
                }
                $type_total=array();
                $crop_total=array();
                $grand_total=array();
                $type_total['crop_name']='';
                $type_total['type_name']='';
                $type_total['variety_name']='Total Type';

                $crop_total['crop_name']='';
                $crop_total['type_name']='Total Crop';
                $crop_total['variety_name']='';

                $grand_total['crop_name']='Grand Total';
                $grand_total['type_name']='';
                $grand_total['variety_name']='';

                $grand_total['pack_size']=$crop_total['pack_size']=$type_total['pack_size']='';
                $grand_total['invoice_quantity']=$crop_total['invoice_quantity']=$type_total['invoice_quantity']=0;
                $grand_total['invoice_quantity_kg']=$crop_total['invoice_quantity_kg']=$type_total['invoice_quantity_kg']=0;
                $grand_total['invoice_amount']=$crop_total['invoice_amount']=$type_total['invoice_amount']=0;
                $grand_total['invoice_discount']=$crop_total['invoice_discount']=$type_total['invoice_discount']=0;
                $grand_total['invoice_payable']=$crop_total['invoice_payable']=$type_total['invoice_payable']=0;
                $grand_total['cancel_quantity']=$crop_total['cancel_quantity']=$type_total['cancel_quantity']=0;
                $grand_total['cancel_quantity_kg']=$crop_total['cancel_quantity_kg']=$type_total['cancel_quantity_kg']=0;
                $grand_total['cancel_amount']=$crop_total['cancel_amount']=$type_total['cancel_amount']=0;
                $grand_total['cancel_discount']=$crop_total['cancel_discount']=$type_total['cancel_discount']=0;
                $grand_total['cancel_payable']=$crop_total['cancel_payable']=$type_total['cancel_payable']=0;
                $prev_crop_name='';
                $prev_type_name='';
                $first_row=true;
                foreach($varieties as $pack)
                {
                    foreach($pack as $info)
                    {
                        if(!$first_row)
                        {
                            if($prev_crop_name!=$info['crop_name'])
                            {
                                $items[]=$this->get_variety_sale_row($type_total);
                                $items[]=$this->get_variety_sale_row($crop_total);
                                $crop_total['invoice_quantity']=$type_total['invoice_quantity']=0;
                                $crop_total['invoice_quantity_kg']=$type_total['invoice_quantity_kg']=0;
                                $crop_total['invoice_amount']=$type_total['invoice_amount']=0;
                                $crop_total['invoice_discount']=$type_total['invoice_discount']=0;
                                $crop_total['invoice_payable']=$type_total['invoice_payable']=0;
                                $crop_total['cancel_quantity']=$type_total['cancel_quantity']=0;
                                $crop_total['cancel_quantity_kg']=$type_total['cancel_quantity_kg']=0;
                                $crop_total['cancel_amount']=$type_total['cancel_amount']=0;
                                $crop_total['cancel_discount']=$type_total['cancel_discount']=0;
                                $crop_total['cancel_payable']=$type_total['cancel_payable']=0;
                                $prev_crop_name=$info['crop_name'];
                                $prev_type_name=$info['type_name'];
                                //sum and reset type total
                                //sum and reset crop total
                            }
                            elseif($prev_type_name!=$info['type_name'])
                            {
                                $items[]=$this->get_variety_sale_row($type_total);
                                $type_total['invoice_quantity']=0;
                                $type_total['invoice_quantity_kg']=0;
                                $type_total['invoice_amount']=0;
                                $type_total['invoice_discount']=0;
                                $type_total['invoice_payable']=0;
                                $type_total['cancel_quantity']=0;
                                $type_total['cancel_quantity_kg']=0;
                                $type_total['cancel_amount']=0;
                                $type_total['cancel_discount']=0;
                                $type_total['cancel_payable']=0;
                                $info['crop_name']='';
                                $prev_type_name=$info['type_name'];
                                //sum and reset type total
                            }
                            else
                            {
                                $info['crop_name']='';
                                $info['type_name']='';
                            }
                        }
                        else
                        {
                            $prev_crop_name=$info['crop_name'];
                            $prev_type_name=$info['type_name'];
                            $first_row=false;
                        }
                        $type_total['invoice_quantity']+=$info['invoice_quantity'];
                        $crop_total['invoice_quantity']+=$info['invoice_quantity'];
                        $grand_total['invoice_quantity']+=$info['invoice_quantity'];
                        $type_total['invoice_quantity_kg']+=$info['invoice_quantity_kg'];
                        $crop_total['invoice_quantity_kg']+=$info['invoice_quantity_kg'];
                        $grand_total['invoice_quantity_kg']+=$info['invoice_quantity_kg'];
                        $type_total['invoice_amount']+=$info['invoice_amount'];
                        $crop_total['invoice_amount']+=$info['invoice_amount'];
                        $grand_total['invoice_amount']+=$info['invoice_amount'];
                        $type_total['invoice_discount']+=$info['invoice_discount'];
                        $crop_total['invoice_discount']+=$info['invoice_discount'];
                        $grand_total['invoice_discount']+=$info['invoice_discount'];

                        $type_total['cancel_quantity']+=$info['cancel_quantity'];
                        $crop_total['cancel_quantity']+=$info['cancel_quantity'];
                        $grand_total['cancel_quantity']+=$info['cancel_quantity'];
                        $type_total['cancel_quantity_kg']+=$info['cancel_quantity_kg'];
                        $crop_total['cancel_quantity_kg']+=$info['cancel_quantity_kg'];
                        $grand_total['cancel_quantity_kg']+=$info['cancel_quantity_kg'];
                        $type_total['cancel_amount']+=$info['cancel_amount'];
                        $crop_total['cancel_amount']+=$info['cancel_amount'];
                        $grand_total['cancel_amount']+=$info['cancel_amount'];
                        $type_total['cancel_discount']+=$info['cancel_discount'];
                        $crop_total['cancel_discount']+=$info['cancel_discount'];
                        $grand_total['cancel_discount']+=$info['cancel_discount'];
                        $items[]=$this->get_variety_sale_row($info);
                    }
                }
                $items[]=$this->get_variety_sale_row($type_total);
                $items[]=$this->get_variety_sale_row($crop_total);
                $items[]=$this->get_variety_sale_row($grand_total);
            }
        }
        $this->jsonReturn($items);
    }
    private function get_variety_sale_row($info)
    {
        $row=array();
        $row['crop_name']=$info['crop_name'];
        $row['type_name']=$info['type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['pack_size']=$info['pack_size'];

        if($info['invoice_quantity']>0)
        {
            $row['invoice_quantity']=$info['invoice_quantity'];
        }
        else
        {
            $row['invoice_quantity']='';
        }
        if($info['invoice_amount']>0)
        {
            $row['invoice_amount']=number_format($info['invoice_amount'],2);
        }
        else
        {
            $row['invoice_amount']='';
        }
        if($info['invoice_discount']>0)
        {
            $row['invoice_discount']=number_format($info['invoice_discount'],2);
        }
        else
        {
            $row['invoice_discount']='';
        }
        if(($info['invoice_amount']-$info['invoice_discount'])!=0)
        {
            $row['invoice_payable']=number_format($info['invoice_amount']-$info['invoice_discount'],2);
        }
        else
        {
            $row['invoice_payable']='';
        }

        if($info['cancel_quantity']>0)
        {
            $row['cancel_quantity']=$info['cancel_quantity'];
        }
        else
        {
            $row['cancel_quantity']='';
        }
        if($info['cancel_amount']>0)
        {
            $row['cancel_amount']=number_format($info['cancel_amount'],2);
        }
        else
        {
            $row['cancel_amount']='';
        }
        if($info['cancel_discount']>0)
        {
            $row['cancel_discount']=number_format($info['cancel_discount'],2);
        }
        else
        {
            $row['cancel_discount']='';
        }
        if(($info['cancel_amount']-$info['cancel_discount'])!=0)
        {
            $row['cancel_payable']=number_format($info['cancel_amount']-$info['cancel_discount'],2);
        }
        else
        {
            $row['cancel_payable']='';
        }
        if($info['invoice_quantity']-$info['cancel_quantity']!=0)
        {
            $row['actual_quantity']=$info['invoice_quantity']-$info['cancel_quantity'];
        }
        else
        {
            $row['actual_quantity']='';
        }
        if(($info['invoice_quantity_kg']-$info['cancel_quantity_kg'])!=0)
        {
            $row['actual_quantity_kg']=number_format((($info['invoice_quantity_kg']-$info['cancel_quantity_kg'])/1000),3,'.','');
        }
        else
        {
            $row['actual_quantity_kg']='';
        }


        if(($info['invoice_amount']-$info['cancel_amount'])!=0)
        {
            $row['actual_amount']=number_format($info['invoice_amount']-$info['cancel_amount'],2);
        }
        else
        {
            $row['actual_amount']='';
        }
        if(($info['invoice_discount']-$info['cancel_discount'])!=0)
        {
            $row['actual_discount']=number_format($info['invoice_discount']-$info['cancel_discount'],2);
        }
        else
        {
            $row['actual_discount']='';
        }
        if((($info['invoice_amount']-$info['cancel_amount'])-($info['invoice_discount']-$info['cancel_discount']))!=0)
        {
            $row['actual_payable']=number_format((($info['invoice_amount']-$info['cancel_amount'])-($info['invoice_discount']-$info['cancel_discount'])),2);
        }
        else
        {
            $row['actual_payable']='';
        }
        return $row;

    }
}
