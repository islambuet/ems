<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_po_return extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_po_return');
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
        $this->controller_url='reports_po_return';
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
        elseif($action=="details")
        {
            $this->system_details($id);
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
            $data['title']="PO Return Report Search";
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
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/search",$data,true));
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
            $data['title']="PO Return Report";
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list",$data,true));

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
        //get customer/outlet ids

        $this->db->from($this->config->item('table_csetup_customers').' cus');
        $this->db->select('cus.id customer_id,cus.name outlet_name');
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
        $results=$this->db->get()->result_array();
        $outlet_ids=array();
        $outlets=array();
        $outlet_ids[0]=0;
        foreach($results as $result)
        {
            $outlet_ids[$result['customer_id']]=$result['customer_id'];
            $outlets[$result['customer_id']]=$result;
        }
        $this->db->from($this->config->item('table_sales_po_returns').' por');
        $this->db->join($this->config->item('table_sales_po').' po','po.id = por.sales_po_id','INNER');
        $this->db->select('po.id,po.customer_id outlet_id,po.date_po');
        $this->db->select('por.date_return,por.revision,por.date_created,por.user_created');
        $this->db->where_in('po.customer_id',$outlet_ids);
        if($date_end>0)
        {
            $this->db->where('por.date_created <=',$date_end);
        }
        if($date_start>0)
        {
            $this->db->where('por.date_created >=',$date_start);
        }
        $this->db->group_by('po.id');
        $this->db->group_by('por.revision');
        $this->db->order_by('po.id','DESC');
        $this->db->order_by('por.revision','ASC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['po_no']=str_pad($item['id'],$this->config->item('system_po_no_length'),'0',STR_PAD_LEFT);
            $item['date_po']=System_helper::display_date($item['date_po']);
            $item['outlet_name']=$outlets[$item['outlet_id']]['outlet_name'];
            $item['date_return']=System_helper::display_date($item['date_return']);
            $item['time_return']=System_helper::display_date_time($item['date_created']);
        }
        $this->jsonReturn($items);
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

            $data['return_details']=array();
            $return_info=Query_helper::get_info($this->config->item('table_sales_po_returns'),'*',array('sales_po_id ='.$po_id),0,0,array('revision ASC'));
            foreach($return_info as $info)
            {
                $data['return_details'][$info['revision']][$info['sales_po_detail_id']]=$info;
                $user_ids[$info['user_created']]=$info['user_created'];
            }
            $data['users']=System_helper::get_users_info($user_ids);


            $data['title']="Sales Return of PO (".str_pad($data['po']['id'],$this->config->item('system_po_no_length'),'0',STR_PAD_LEFT).')';


            $ajax['status']=true;
            //$ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("sales_po_return/details",$data,true));
            $ajax['system_content'][]=array("id"=>"#popup_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            //$ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$po_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }
}
