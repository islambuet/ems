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
}
