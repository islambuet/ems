<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reports_stock extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Reports_stock');
        $this->controller_url='reports_stock';
        //$this->load->model("sys_module_task_model");
        //$this->load->model("sales_model");
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
            $data['title']="Stock Report Search";
            $ajax['status']=true;
            $data['warehouses']=Query_helper::get_info($this->config->item('table_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array());
            $fiscal_years=Query_helper::get_info($this->config->item('table_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }
            $data['date_start']='';
            $data['date_end']=System_helper::display_date(time());
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("reports_stock/search",$data,true));
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
            if($reports['date_start']>=$reports['date_end'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Starting Date should be less than End date';
                $this->jsonReturn($ajax);
            }
            $reports['date_end']=$reports['date_end']+3600*24-1;
            $keys=',';

            foreach($reports as $elem=>$value)
            {
                $keys.=$elem.":'".$value."',";
            }

            $data['keys']=trim($keys,',');

            $data['title']="Stock Report";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view("reports_stock/list",$data,true));
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
        $starting_items=$this->get_stocks($this->input->post('date_end'));
        $items=array();
        if(sizeof($starting_items)>0)
        {
            $initial_items=$this->get_stocks($this->input->post('date_start'));
            foreach($starting_items as $vid=>$variety)
            {
                foreach($variety as $pack_id=>$pack)
                {
                    $initial=array();
                    $initial['stock_in']=0;
                    $initial['excess']=0;
                    $initial['sales']=0;
                    $initial['sales_return']=0;
                    $initial['sales_bonus']=0;
                    $initial['sales_return_bonus']=0;
                    $initial['short']=0;
                    $initial['rnd']=0;
                    $initial['sample']=0;
                    if(isset($initial_items[$vid][$pack_id]))
                    {
                        $initial=$initial_items[$vid][$pack_id];
                    }
                    $info=array();
                    $info['crop_name']=$pack['crop_name'];
                    $info['crop_type_name']=$pack['crop_type_name'];
                    $info['variety_name']=$pack['variety_name'];
                    $info['pack_size_name']=$pack['pack_size_name'];
                    $info['starting_stock']=$initial['stock_in']+$initial['excess']-$initial['sales']+$initial['sales_return']-$initial['sales_bonus']+$initial['sales_return_bonus']-$initial['short']-$initial['rnd']-$initial['sample'];
                    $info['current']=$pack['stock_in']+$pack['excess']-$pack['sales']+$pack['sales_return']-$pack['sales_bonus']+$pack['sales_return_bonus']-$pack['short']-$pack['rnd']-$pack['sample'];

                    $info['stock_in']=$pack['stock_in']-$initial['stock_in'];
                    $info['excess']=$pack['excess']-$initial['excess'];
                    $info['sales']=$pack['sales']-$initial['sales'];
                    $info['sales_return']=$pack['sales_return']-$initial['sales_return'];
                    $info['sales_bonus']=$pack['sales_bonus']-$initial['sales_bonus'];
                    $info['sales_return_bonus']=$pack['sales_return_bonus']-$initial['sales_return_bonus'];
                    $info['short']=$pack['short']-$initial['short'];
                    $info['rnd']=$pack['rnd']-$initial['rnd'];
                    $info['sample']=$pack['sample']-$initial['sample'];
                    $items[]=$info;
                }
            }
        }

        $this->jsonReturn($items);


    }
    private function get_stocks($time)
    {
        $stocks=array();
        if($time==0)
        {
            return $stocks;
        }
        //stock in
        $this->db->from($this->config->item('table_stockin_varieties').' stv');
        $this->db->select('variety_id,pack_size_id');
        $this->db->select('SUM(quantity) stock_in');

        $this->db->select('pack.name pack_size_name');
        $this->db->select('crop.name crop_name,crop.id crop_id');
        $this->db->select('type.name crop_type_name,type.id type_id');
        $this->db->select('v.name variety_name');

        $this->db->group_by(array('variety_id','pack_size_id'));

        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =stv.variety_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_vpack_size').' pack','pack.id =stv.pack_size_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');

        $this->db->where('stv.status',$this->config->item('system_status_active'));
        $this->db->where('stv.date_stock_in <=',$time);
        $this->db->order_by('crop.ordering');
        $this->db->order_by('type.ordering');
        $this->db->order_by('v.ordering');
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_in']=$result['stock_in'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['excess']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['sales']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['sales_return']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['sales_bonus']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['sales_return_bonus']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['short']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['rnd']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['sample']=0;

            $stocks[$result['variety_id']][$result['pack_size_id']]['pack_size_name']=$result['pack_size_name'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['variety_name']=$result['variety_name'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['crop_type_name']=$result['crop_type_name'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['crop_name']=$result['crop_name'];
        }
        //excess
        $this->db->from($this->config->item('table_stockin_excess_inventory'));
        $this->db->select('variety_id,pack_size_id');
        $this->db->select('SUM(quantity) stock_in');
        $this->db->group_by(array('variety_id','pack_size_id'));
        $this->db->where('status',$this->config->item('system_status_active'));
        $this->db->where('date_stock_in <=',$time);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['excess']=$result['stock_in'];
        }
        //stock out
        $this->db->from($this->config->item('table_stockout'));
        $this->db->select('variety_id,pack_size_id,purpose');
        $this->db->select('SUM(quantity) stockout');
        $this->db->group_by(array('variety_id','pack_size_id','purpose'));
        $this->db->where('status',$this->config->item('system_status_active'));
        $this->db->where('date_stock_out <=',$time);
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            if($result['purpose']==$this->config->item('system_purpose_short'))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]['short']=$result['stockout'];
            }
            elseif($result['purpose']==$this->config->item('system_purpose_rnd'))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]['rnd']=$result['stockout'];
            }
            elseif($result['purpose']==$this->config->item('system_purpose_customer'))
            {
                $stocks[$result['variety_id']][$result['pack_size_id']]['sample']=$result['stockout'];
            }
        }
        //sales
        $this->db->from($this->config->item('table_sales_po_details').' spd');
        $this->db->select('variety_id,pack_size_id');
        $this->db->select('SUM(quantity) sales');
        $this->db->join($this->config->item('table_sales_po').' sp','sp.id =spd.sales_po_id','INNER');
        $this->db->group_by(array('variety_id','pack_size_id'));

        $this->db->where('sp.status_approved',$this->config->item('system_status_po_approval_approved'));
        $this->db->where('spd.revision',1);
        $this->db->where('sp.date_approved <=',$time);
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['sales']=$result['sales'];
        }
        //sales return
        $this->db->from($this->config->item('table_sales_po_details').' spd');
        $this->db->select('variety_id,pack_size_id');
        $this->db->select('SUM(quantity_return) sales_return');
        $this->db->join($this->config->item('table_sales_po').' sp','sp.id =spd.sales_po_id','INNER');
        $this->db->group_by(array('variety_id','pack_size_id'));

        $this->db->where('sp.status_received',$this->config->item('system_status_po_received_received'));
        $this->db->where('spd.revision',1);
        $this->db->where('spd.date_return <=',$time);
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['sales_return']=$result['sales_return'];
        }
        //sales bonus
        $this->db->from($this->config->item('table_sales_po_details').' spd');
        $this->db->select('variety_id,bonus_pack_size_id pack_size_id');
        $this->db->select('SUM(quantity_bonus) sales_bonus');
        $this->db->join($this->config->item('table_sales_po').' sp','sp.id =spd.sales_po_id','INNER');
        $this->db->group_by(array('variety_id','bonus_pack_size_id'));

        $this->db->where('bonus_details_id >',0);
        $this->db->where('sp.status_approved',$this->config->item('system_status_po_approval_approved'));
        $this->db->where('spd.revision',1);
        $this->db->where('sp.date_approved <=',$time);
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['sales_bonus']=$result['sales_bonus'];
        }
        //sales bonus return
        $this->db->from($this->config->item('table_sales_po_details').' spd');
        $this->db->select('variety_id,bonus_pack_size_id pack_size_id');
        $this->db->select('SUM(quantity_bonus_return) sales_return_bonus');
        $this->db->join($this->config->item('table_sales_po').' sp','sp.id =spd.sales_po_id','INNER');
        $this->db->group_by(array('variety_id','bonus_pack_size_id'));

        $this->db->where('bonus_details_id >',0);
        $this->db->where('sp.status_received',$this->config->item('system_status_po_received_received'));
        $this->db->where('spd.revision',1);
        $this->db->where('spd.date_return <=',$time);
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['sales_return_bonus']=$result['sales_return_bonus'];
        }
        return $stocks;

    }

}
