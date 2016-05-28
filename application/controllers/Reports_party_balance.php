<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_party_balance extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_party_balance');
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
        $this->controller_url='reports_party_balance';
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
            $data['title']="Party Balance Search";
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
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("reports_party_balance/search",$data,true));
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
            $data['title']="Party Balance Report";
            if($reports['customer_id']>0)
            {
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_party_balance/customer_statement",$data,true));
            }
            else
            {
                if($reports['district_id']>0)
                {
                    $data['areas']='Customers';
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

                $data['arm_banks']=Query_helper::get_info($this->config->item('table_basic_setup_arm_bank'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
                $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_party_balance/list",$data,true));
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
    public function get_items()
    {
        $items=array();
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');
        if($district_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_csetup_customers'),array('id value','name text'),array('district_id ='.$district_id));
            $location_type='customer_id';
        }
        elseif($territory_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$territory_id));
            $location_type='district_id';
        }
        elseif($zone_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$zone_id));
            $location_type='territory_id';
        }
        elseif($division_id>0)
        {
            $areas=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$division_id));
            $location_type='zone_id';
        }
        else
        {
            $areas=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $location_type='division_id';
        }
        $arm_banks=Query_helper::get_info($this->config->item('table_basic_setup_arm_bank'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
        //0-adjust-payment+purchase-sales return
        $area_initial=array();
        //setting 0
        foreach($areas as $area)
        {
            $area_initial[$area['value']]['areas']=$area['text'];
            $area_initial[$area['value']]['opening_balance_tp']=0;
            $area_initial[$area['value']]['opening_balance_net']=0;
            $area_initial[$area['value']]['sales_tp']=0;
            $area_initial[$area['value']]['sales_net']=0;
            foreach($arm_banks as $arm_bank)
            {
                $area_initial[$area['value']]['payment_'.$arm_bank['value']]=0;
            }
            $area_initial[$area['value']]['total_payment']=0;
            $area_initial[$area['value']]['adjust_tp']=0;
            $area_initial[$area['value']]['adjust_net']=0;
        }

        //find adjustment
        //opening balance
        if($date_start>0)
        {
            $this->db->from($this->config->item('table_csetup_balance_adjust').' ba');
            $this->db->select('SUM(ba.amount_tp) amount_tp');
            $this->db->select('SUM(ba.amount_net) amount_net');
            $this->db->select('ba.customer_id customer_id');
            $this->db->select('ba.date_adjust date_adjust');
            $this->db->select('d.id district_id');
            $this->db->select('t.id territory_id');
            $this->db->select('zone.id zone_id');
            $this->db->select('zone.division_id division_id');
            $this->db->where('ba.status',$this->config->item('system_status_active'));
            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = ba.customer_id','INNER');
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
                        }
                    }
                }
            }
            $this->db->where('ba.date_adjust <=',$date_start);
            $group_array[]=$location_type;
            $this->db->group_by($group_array);
            $results=$this->db->get()->result_array();
            if($results)
            {
                foreach($results as $result)
                {

                    $area_initial[$result[$location_type]]['opening_balance_tp']-=$result['amount_tp'];
                    $area_initial[$result[$location_type]]['opening_balance_net']-=$result['amount_net'];
                }
            }
        }
        //other adjustment
        $this->db->from($this->config->item('table_csetup_balance_adjust').' ba');
        $this->db->select('SUM(ba.amount_tp) amount_tp');
        $this->db->select('SUM(ba.amount_net) amount_net');
        $this->db->select('ba.customer_id customer_id');
        $this->db->select('ba.date_adjust date_adjust');
        $this->db->select('d.id district_id');
        $this->db->select('t.id territory_id');
        $this->db->select('zone.id zone_id');
        $this->db->select('zone.division_id division_id');
        $this->db->where('ba.status',$this->config->item('system_status_active'));
        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = ba.customer_id','INNER');
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
                    }
                }
            }
        }
        $this->db->where('ba.date_adjust >',$date_start);
        $this->db->where('ba.date_adjust <=',$date_end);
        $group_array[]=$location_type;
        $this->db->group_by($group_array);
        $results=$this->db->get()->result_array();
        if($results)
        {
            foreach($results as $result)
            {

                $area_initial[$result[$location_type]]['adjust_tp']+=$result['amount_tp'];
                $area_initial[$result[$location_type]]['adjust_net']+=$result['amount_net'];
            }
        }
        //sales in opening balance
        if($date_start>0)
        {
            $this->db->from($this->config->item('table_sales_po_details').' pod');
            $this->db->select('SUM(quantity*variety_price) total_sales_tp');
            $this->db->select('SUM(quantity*variety_price_net) total_sales_net');

            $this->db->select('cus.id customer_id,cus.name customer_name');
            $this->db->select('d.id district_id');
            $this->db->select('t.id territory_id');
            $this->db->select('zone.id zone_id');
            $this->db->select('zone.division_id division_id');

            $this->db->join($this->config->item('table_sales_po').' po','po.id = pod.sales_po_id','INNER');
            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = po.customer_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->where('pod.revision',1);
            $this->db->where('po.status_approved',$this->config->item('system_status_po_approval_approved'));
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
                        }
                    }
                }
            }

            $this->db->where('po.date_approved <=',$date_start);

            $group_array[]=$location_type;
            $this->db->group_by($group_array);
            $results=$this->db->get()->result_array();
            foreach($results as $result)
            {
                $area_initial[$result[$location_type]]['opening_balance_tp']+=$result['total_sales_tp'];
                $area_initial[$result[$location_type]]['opening_balance_net']+=$result['total_sales_net'];
            }
        }
        //sales in sales
        $this->db->from($this->config->item('table_sales_po_details').' pod');
        $this->db->select('SUM(quantity*variety_price) total_sales_tp');
        $this->db->select('SUM(quantity*variety_price_net) total_sales_net');

        $this->db->select('cus.id customer_id,cus.name customer_name');
        $this->db->select('d.id district_id');
        $this->db->select('t.id territory_id');
        $this->db->select('zone.id zone_id');
        $this->db->select('zone.division_id division_id');

        $this->db->join($this->config->item('table_sales_po').' po','po.id = pod.sales_po_id','INNER');
        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = po.customer_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->where('pod.revision',1);
        $this->db->where('po.status_approved',$this->config->item('system_status_po_approval_approved'));
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
                    }
                }
            }
        }

        $this->db->where('po.date_approved >',$date_start);
        $this->db->where('po.date_approved <=',$date_end);

        $group_array[]=$location_type;
        $this->db->group_by($group_array);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $area_initial[$result[$location_type]]['sales_tp']+=$result['total_sales_tp'];
            $area_initial[$result[$location_type]]['sales_net']+=$result['total_sales_net'];
        }
        //payment opening balance
        if($date_start>0)
        {
            $this->db->from($this->config->item('table_payment_payment').' p');
            $this->db->select('p.amount,p.date_payment_receive,p.customer_id');
            $this->db->select('d.id district_id');
            $this->db->select('t.id territory_id');
            $this->db->select('zone.id zone_id');
            $this->db->select('zone.division_id division_id');
            $this->db->where('p.status',$this->config->item('system_status_active'));
            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = p.customer_id','INNER');
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
                        }
                    }
                }
            }
            $this->db->where('p.date_payment_receive <=',$date_start);
            $group_array[]=$location_type;
            $this->db->group_by($group_array);
            $results=$this->db->get()->result_array();
            if($results)
            {
                foreach($results as $result)
                {
                    $area_initial[$result[$location_type]]['opening_balance_tp']-=$result['amount'];
                    $area_initial[$result[$location_type]]['opening_balance_net']-=$result['amount'];
                }
            }

        }
        //payment
        $this->db->from($this->config->item('table_payment_payment').' p');
        $this->db->select('p.amount,p.date_payment_receive,p.arm_bank_id,p.customer_id');
        $this->db->select('d.id district_id');
        $this->db->select('t.id territory_id');
        $this->db->select('zone.id zone_id');
        $this->db->select('zone.division_id division_id');
        $this->db->where('p.status',$this->config->item('system_status_active'));
        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = p.customer_id','INNER');
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
                    }
                }
            }
        }

        $this->db->where('p.date_payment_receive >',$date_start);
        $this->db->where('p.date_payment_receive <=',$date_end);

        $results=$this->db->get()->result_array();
        if($results)
        {
            foreach($results as $result)
            {
                $area_initial[$result[$location_type]]['payment_'.$result['arm_bank_id']]+=$result['amount'];
            }
        }
        //sales return in opening balance
        if($date_start>0)
        {

            $this->db->from($this->config->item('table_sales_po_details').' pod');
            $this->db->select('SUM(quantity_return*variety_price) total_sales_tp');
            $this->db->select('SUM(quantity_return*variety_price_net) total_sales_net');

            $this->db->select('cus.id customer_id,cus.name customer_name');
            $this->db->select('d.id district_id');
            $this->db->select('t.id territory_id');
            $this->db->select('zone.id zone_id');
            $this->db->select('zone.division_id division_id');

            $this->db->join($this->config->item('table_sales_po').' po','po.id = pod.sales_po_id','INNER');
            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = po.customer_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->where('pod.revision',1);
            $this->db->where('po.status_approved',$this->config->item('system_status_po_approval_approved'));
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
                        }
                    }
                }
            }
            $this->db->where('pod.date_return >',0);
            $this->db->where('pod.date_return <=',$date_start);
            $group_array[]=$location_type;
            $this->db->group_by($group_array);
            $results=$this->db->get()->result_array();
            foreach($results as $result)
            {
                $area_initial[$result[$location_type]]['opening_balance_tp']-=$result['total_sales_tp'];
                $area_initial[$result[$location_type]]['opening_balance_net']-=$result['total_sales_net'];

            }
        }
        //sales return in sales
        $this->db->from($this->config->item('table_sales_po_details').' pod');
        $this->db->select('SUM(quantity_return*variety_price) total_sales_tp');
        $this->db->select('SUM(quantity_return*variety_price_net) total_sales_net');

        $this->db->select('cus.id customer_id,cus.name customer_name');
        $this->db->select('d.id district_id');
        $this->db->select('t.id territory_id');
        $this->db->select('zone.id zone_id');
        $this->db->select('zone.division_id division_id');

        $this->db->join($this->config->item('table_sales_po').' po','po.id = pod.sales_po_id','INNER');
        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = po.customer_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->where('pod.revision',1);
        $this->db->where('po.status_approved',$this->config->item('system_status_po_approval_approved'));
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
                    }
                }
            }
        }
        $this->db->where('pod.date_return >',0);
        $this->db->where('pod.date_return >',$date_start);
        $this->db->where('pod.date_return <=',$date_end);
        $group_array[]=$location_type;
        $this->db->group_by($group_array);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $area_initial[$result[$location_type]]['sales_tp']-=$result['total_sales_tp'];
            $area_initial[$result[$location_type]]['sales_net']-=$result['total_sales_net'];

        }

        $total_row=array();
        $total_row['areas']='Total';
        $total_row['opening_balance_tp']=0;
        $total_row['opening_balance_net']=0;
        $total_row['sales_tp']=0;
        $total_row['sales_net']=0;
        foreach($arm_banks as $arm_bank)
        {
            $total_row['payment_'.$arm_bank['value']]=0;
        }
        $total_row['total_payment']=0;
        $total_row['adjust_tp']=0;
        $total_row['adjust_net']=0;
        $total_row['balance_tp']=0;
        $total_row['balance_net']=0;
        foreach($area_initial as $area)
        {
            //opening balance sum
            $total_row['opening_balance_tp']+=$area['opening_balance_tp'];
            $total_row['opening_balance_net']+=$area['opening_balance_net'];
            //sales sum
            $total_row['sales_tp']+=$area['sales_tp'];
            $total_row['sales_net']+=$area['sales_net'];

            //bank sum
            foreach($arm_banks as $arm_bank)
            {
                $total_row['payment_'.$arm_bank['value']]+=($area['payment_'.$arm_bank['value']]);
                $area['total_payment']+=($area['payment_'.$arm_bank['value']]);

            }
            //total payment sum
            $total_row['total_payment']+=$area['total_payment'];
            //other adjustment sum
            $total_row['adjust_tp']+=$area['adjust_tp'];
            $total_row['adjust_net']+=$area['adjust_net'];

            //opening balance+sales-total_payment-adjustment
            $area['balance_tp']=$area['opening_balance_tp']+$area['sales_tp']-$area['total_payment']-$area['adjust_tp'];
            $area['balance_net']=$area['opening_balance_net']+$area['sales_net']-$area['total_payment']-$area['adjust_net'];
            $total_row['balance_tp']+=$area['balance_tp'];
            $total_row['balance_net']+=$area['balance_net'];
            //for printing purpose
            $items[]=$this->get_items_printing_row($area,$arm_banks);

        }
        $items[]=$this->get_items_printing_row($total_row,$arm_banks);
        $this->jsonReturn($items);
    }
    private function get_items_printing_row($row,$arm_banks)
    {
        $info=array();
        $info['areas']=$row['areas'];
        if(($row['opening_balance_tp']+$row['sales_tp'])!=0)
        {
            $info['payment_percentage_tp']=number_format($row['total_payment']*100/($row['opening_balance_tp']+$row['sales_tp']),2);
        }
        else
        {
            $info['payment_percentage_tp']='-';
        }
        if(($row['opening_balance_net']+$row['sales_net'])!=0)
        {
            $info['payment_percentage_net']=number_format($row['total_payment']*100/($row['opening_balance_net']+$row['sales_net']),2);
        }
        else
        {
            $info['payment_percentage_tp']='-';
        }
        if($row['opening_balance_tp']!=0)
        {
            $info['opening_balance_tp']=number_format($row['opening_balance_tp'],2);
        }
        else
        {
            $info['opening_balance_tp']='';
        }
        if($row['opening_balance_net']!=0)
        {
            $info['opening_balance_net']=number_format($row['opening_balance_net'],2);
        }
        else
        {
            $info['opening_balance_net']='';
        }
        if($row['sales_tp']!=0)
        {
            $info['sales_tp']=number_format($row['sales_tp'],2);
        }
        else
        {
            $info['sales_tp']='';
        }
        if($row['sales_net']!=0)
        {
            $info['sales_net']=number_format($row['sales_net'],2);
        }
        else
        {
            $info['sales_net']='';
        }
        foreach($arm_banks as $arm_bank)
        {
            if($row['payment_'.$arm_bank['value']]!=0)
            {
                $info['payment_'.$arm_bank['value']]=number_format($row['payment_'.$arm_bank['value']],2);
            }
            else
            {
                $info['payment_'.$arm_bank['value']]='';
            }

        }
        if($row['total_payment']!=0)
        {
            $info['total_payment']=number_format($row['total_payment'],2);
        }
        else
        {
            $info['total_payment']='';
        }
        if($row['balance_tp']!=0)
        {
            $info['balance_tp']=number_format($row['balance_tp'],2);
        }
        else
        {
            $info['balance_tp']='';
        }
        if($row['balance_net']!=0)
        {
            $info['balance_net']=number_format($row['balance_net'],2);
        }
        else
        {
            $info['balance_net']='';
        }

        return $info;
    }
}
