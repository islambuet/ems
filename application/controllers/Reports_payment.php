<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_payment extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_payment');
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
        $this->controller_url='reports_payment';
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
            $data['title']="Payment Report Search";
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
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("reports_payment/search",$data,true));
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
            $data['title']="Payment Report";
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_payment/list",$data,true));

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
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        $this->db->from($this->config->item('table_payment_payment').' payment');
        $this->db->select('payment.id,payment.amount,payment.amount_customer,payment.payment_way,payment.date_payment_customer,payment.date_payment_receive,payment.cheque_no');
        $this->db->select('CONCAT(cus.customer_code," - ",cus.name) customer_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->select('bank.name payment_bank');
        $this->db->select('arm_Bank.name receive_bank');

        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = payment.customer_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_basic_setup_bank').' bank','bank.id = payment.bank_id','LEFT');
        $this->db->join($this->config->item('table_basic_setup_arm_bank').' arm_Bank','arm_bank.id = payment.arm_bank_id','LEFT');

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
        $this->db->where('payment.status !=',$this->config->item('system_status_delete'));
        $this->db->where('payment.date_payment_receive >0');
        if($date_end>0)
        {
            $this->db->where('payment.date_payment_receive <=',$date_end);
        }
        if($date_start>0)
        {
            $this->db->where('payment.date_payment_receive >=',$date_start);
        }

        $this->db->order_by('division.ordering','ASC');
        $this->db->order_by('zone.ordering','ASC');
        $this->db->order_by('t.ordering','ASC');
        $this->db->order_by('d.ordering','ASC');
        $this->db->order_by('cus.ordering','ASC');
        $this->db->order_by('payment.date_payment_receive','ASC');
        $results=$this->db->get()->result_array();
        $total_amount=0;
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
            $item['payment_no']=str_pad($result['id'],$this->config->item('system_po_no_length'),'0',STR_PAD_LEFT);
            $item['payment_date']=System_helper::display_date($result['date_payment_customer']);
            $item['payment_amount']=number_format($result['amount_customer'],2);
            $item['payment_bank']=$result['payment_bank'];
            $item['receive_date']=System_helper::display_date($result['date_payment_receive']);
            $item['receive_amount']=number_format($result['amount'],2);
            $total_amount+=$result['amount'];
            $item['receive_bank']=$result['receive_bank'];
            $items[]=$item;
        }
        $total_row=array();
        $total_row['division_name']='';
        $total_row['zone_name']='';
        $total_row['territory_name']='';
        $total_row['district_name']='';
        $total_row['customer_name']='Total';
        $total_row['payment_no']='';
        $total_row['payment_date']='';
        $total_row['payment_amount']='';
        $total_row['receive_date']='';
        $total_row['receive_amount']=number_format($total_amount,2);
        $items[]=$total_row;

        $this->jsonReturn($items);
    }





}
