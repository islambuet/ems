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
    /*private function get_visit_setup($date,$division_id,$setups)
    {
        foreach($setups as $setup)
        {
            if($division_id==$setup['division_id'])
            {
                if(($setup['date_start']<=$date) && ($setup['date_end']>=$date))
                {
                    return $setup;
                }
            }

        }
        return null;
    }*/

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
        $area_initial=array();

        $sl=0;
        foreach($areas as $area)
        {
            $sl++;
            $area_initial[$area['value']]['sl_no']=$sl;
            $area_initial[$area['value']]['areas']=$area['text'];
            $area_initial[$area['value']]['opening_balance']=0;
            $area_initial[$area['value']]['sales']=0;
            foreach($arm_banks as $arm_bank)
            {
                $area_initial[$area['value']]['payment_'.$arm_bank['value']]=0;
            }
            $area_initial[$area['value']]['total_payment']=0;
        }
        $total_row=array();
        $total_row['sl_no']='';
        $total_row['areas']='Total';
        $total_row['opening_balance']=0;
        $total_row['sales']=0;
        foreach($arm_banks as $arm_bank)
        {
            $total_row['payment_'.$arm_bank['value']]=0;
        }
        $total_row['total_payment']=0;

        //initial payment
        $this->db->from($this->config->item('table_payment_payment').' p');
        $this->db->select('SUM(p.amount) total_initial');
        $this->db->select('p.customer_id customer_id');
        $this->db->select('d.id district_id');
        $this->db->select('t.id territory_id');
        $this->db->select('zone.id zone_id');
        $this->db->select('zone.division_id division_id');
        $this->db->where('p.status',$this->config->item('system_status_active'));
        $this->db->where('p.payment_type',$this->config->item('system_payment_initial'));
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
        $group_array[]=$location_type;
        $this->db->group_by($group_array);
        $results=$this->db->get()->result_array();
        if($results)
        {
            foreach($results as $result)
            {
                $area_initial[$result[$location_type]]['opening_balance']-=$result['total_initial'];
            }
        }
        //payment
        $this->db->from($this->config->item('table_payment_payment').' p');
        $this->db->select('p.amount,p.date_payment,p.arm_bank_id,p.customer_id');
        $this->db->select('d.id district_id');
        $this->db->select('t.id territory_id');
        $this->db->select('zone.id zone_id');
        $this->db->select('zone.division_id division_id');
        $this->db->where('p.status',$this->config->item('system_status_active'));
        $this->db->where('p.payment_type',$this->config->item('system_payment_other'));
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
        if($date_end>0)
        {
            $this->db->where('p.date_payment <=',$date_end);
        }
        $results=$this->db->get()->result_array();
        if($results)
        {
            foreach($results as $result)
            {
                if($date_start>0 && $date_start>=$result['date_payment'])
                {
                    $area_initial[$result[$location_type]]['opening_balance']-=$result['amount'];
                }
                else
                {
                    if(isset($area_initial[$result[$location_type]]['payment_'.$result['arm_bank_id']]))
                    {
                        $area_initial[$result[$location_type]]['payment_'.$result['arm_bank_id']]+=$result['amount'];
                    }

                    $area_initial[$result[$location_type]]['total_payment']+=$result['amount'];
                }

            }
        }
        //sales

        //sales upto end date
        $this->db->from($this->config->item('table_sales_po_details').' pod');
        $this->db->select('SUM(quantity*variety_price) total_sales');

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
        if($date_end>0)
        {
            $this->db->where('po.date_approved <=',$date_end);
        }
        $group_array[]=$location_type;
        $this->db->group_by($group_array);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $area_initial[$result[$location_type]]['sales']+=$result['total_sales'];

        }

        //sales upto start date
        if($date_start>0)
        {
            $this->db->from($this->config->item('table_sales_po_details').' pod');
            $this->db->select('SUM(quantity*variety_price) total_sales');

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
                $area_initial[$result[$location_type]]['sales']-=$result['total_sales'];
                $area_initial[$result[$location_type]]['opening_balance']+=$result['total_sales'];

            }
        }
        //sales return upto end date
        $this->db->from($this->config->item('table_sales_po_details').' pod');
        $this->db->select('SUM(quantity_return*variety_price) total_sales');

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
        if($date_end>0)
        {
            $this->db->where('pod.date_return <=',$date_end);
        }
        $group_array[]=$location_type;
        $this->db->group_by($group_array);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $area_initial[$result[$location_type]]['sales']-=$result['total_sales'];

        }

        //sales return upto start date
        if($date_start>0)
        {
            $this->db->from($this->config->item('table_sales_po_details').' pod');
            $this->db->select('SUM(quantity_return*variety_price) total_sales');

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
                $area_initial[$result[$location_type]]['opening_balance']-=$result['total_sales'];

            }
        }
        //final row
        foreach($area_initial as $area)
        {
            $total_row['opening_balance']+=$area['opening_balance'];
            $total_row['sales']+=$area['sales'];
            $area['balance']=number_format($area['opening_balance']+$area['sales']-$area['total_payment'],2);
            if(($area['opening_balance']+$area['sales'])>0)
            {
                $area['percentage']=number_format($area['total_payment']*100/($area['opening_balance']+$area['sales']),2);
            }
            else
            {
                $area['percentage']='N/A';
            }
            $area['opening_balance']=number_format($area['opening_balance'],2);
            $area['sales']=number_format($area['sales'],2);

            foreach($arm_banks as $arm_bank)
            {
                $total_row['payment_'.$arm_bank['value']]+=($area['payment_'.$arm_bank['value']]);
                $area['payment_'.$arm_bank['value']]=number_format($area['payment_'.$arm_bank['value']],2);
            }
            $total_row['total_payment']+=$area['total_payment'];
            $area['total_payment']=number_format($area['total_payment'],2);
            $items[]=$area;

        }
        $total_row['balance']=number_format($total_row['opening_balance']+$total_row['sales']-$total_row['total_payment'],2);
        if(($total_row['opening_balance']+$total_row['sales'])>0)
        {
            $total_row['percentage']=number_format($total_row['total_payment']*100/($total_row['opening_balance']+$total_row['sales']),2);
        }
        else
        {
            $total_row['percentage']='N/A';
        }
        foreach($arm_banks as $arm_bank)
        {
            $total_row['payment_'.$arm_bank['value']]=number_format($total_row['payment_'.$arm_bank['value']],2);
        }
        $total_row['opening_balance']=number_format($total_row['opening_balance'],2);
        $total_row['sales']=number_format($total_row['sales'],2);

        $total_row['total_payment']=number_format($total_row['total_payment'],2);
        $items[]=$total_row;
        $this->jsonReturn($items);
    }
}
