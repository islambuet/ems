<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common_controller extends Root_Controller
{
    private  $message;
    public function __construct()
    {
        parent::__construct();
        $this->message="";

    }

    //location setup
    public function get_dropdown_zones_by_divisionid()
    {
        $division_id = $this->input->post('division_id');
        $html_container_id='#zone_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$division_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }
    public function get_dropdown_territories_by_zoneid()
    {
        $zone_id = $this->input->post('zone_id');
        $html_container_id='#territory_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$zone_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }
    public function get_dropdown_districts_by_territoryid()
    {
        $territory_id = $this->input->post('territory_id');
        $html_container_id='#district_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$territory_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }
    public function get_dropdown_upazillas_by_districtid()
    {
        $district_id = $this->input->post('district_id');
        $html_container_id='#upazilla_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_setup_location_upazillas'),array('id value','name text'),array('district_id ='.$district_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }
    public function get_dropdown_unions_by_upazillaid()
    {
        $upazilla_id = $this->input->post('upazilla_id');
        $html_container_id='#union_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_setup_location_unions'),array('id value','name text'),array('upazilla_id ='.$upazilla_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }
    public function get_dropdown_customers_by_districtid()
    {
        $district_id = $this->input->post('district_id');
        $html_container_id='#customer_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        //$this->db->from($this->config->item('table_csetup_customers'));
        //$this->db->select('id value');
        //$this->db->select('CONCAT(customer_code,"-",name) text',false);
        //$data['items']=$this->db->get()->result_array();
        $data['items']=Query_helper::get_info($this->config->item('table_csetup_customers'),array('id value','CONCAT(customer_code," - ",name) text'),array('district_id ='.$district_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }
    public function get_dropdown_armbankaccounts_by_armbankid()
    {
        $arm_bank_id = $this->input->post('arm_bank_id');
        $html_container_id='#arm_bank_account_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_basic_setup_arm_bank_accounts'),array('id value','account_no text'),array('bank_id ='.$arm_bank_id,'status ="'.$this->config->item('system_status_active').'"'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }

    //crop classification

    public function get_dropdown_croptypes_by_cropid()
    {
        $crop_id = $this->input->post('crop_id');
        $html_container_id='#crop_type_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_setup_classification_crop_types'),array('id value','name text'),array('crop_id ='.$crop_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }
    public function get_dropdown_varieties_by_croptypeid()
    {
        $crop_type_id = $this->input->post('crop_type_id');
        $html_container_id='#variety_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),array('id value','name text'),array('crop_type_id ='.$crop_type_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }
    public function get_dropdown_armvarieties_by_croptypeid()
    {
        $crop_type_id = $this->input->post('crop_type_id');
        $html_container_id='#variety_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),array('id value','name text'),array('crop_type_id ='.$crop_type_id,'status ="'.$this->config->item('system_status_active').'"','whose ="ARM"'),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }
    //stock in
    public function get_dropdown_crops_by_warehouseid()
    {
        $html_container_id='#crop_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $warehouse_id = $this->input->post('warehouse_id');
        $this->db->from($this->config->item('table_basic_setup_warehouse_crops').' wc');
        $this->db->select('wc.crop_id value,c.name text');
        $this->db->join($this->config->item('table_setup_classification_crops').' c','c.id =wc.crop_id','INNER');
        $this->db->where('wc.warehouse_id',$warehouse_id);
        $this->db->where('wc.revision',1);
        $this->db->order_by('c.ordering ASC');
        $data['items']=$this->db->get()->result_array();
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }
    public function get_dropdown_allcrops()
    {
        $html_container_id='#crop_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array(),0,0,array('ordering ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }
    public function get_dropdown_allpack_sizes()
    {
        $html_container_id='#pack_size_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $data['items']=Query_helper::get_info($this->config->item('table_setup_classification_vpack_size'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('name ASC'));
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }
    public function get_dropdown_packsizes_by_variety_warehouse()
    {
        $html_container_id='#pack_size_id';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }

        $variety_id = $this->input->post('variety_id');
        $warehouse_id = $this->input->post('warehouse_id');

        $this->db->from($this->config->item('table_stockin_varieties').' stv');
        //$this->db->from($this->config->item('table_basic_setup_warehouse_crops').' wc');
        $this->db->select('stv.pack_size_id value,pack.name text');
        $this->db->join($this->config->item('table_setup_classification_vpack_size').' pack','pack.id =stv.pack_size_id','INNER');
        if($warehouse_id>0)
        {
            $this->db->where('stv.warehouse_id',$warehouse_id);
        }

        $this->db->where('stv.variety_id',$variety_id);
        $this->db->where('stv.status',$this->config->item('system_status_active'));
        $this->db->group_by('stv.pack_size_id');

        $data['items']=$this->db->get()->result_array();
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$this->load->view("dropdown_with_select",$data,true));

        $this->jsonReturn($ajax);
    }
    public function get_price_by_variety_pack_size_id()
    {
        $html_container_id='#pack_price';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }
        $variety_id = $this->input->post('variety_id');
        $pack_size_id = $this->input->post('pack_size_id');
        $info=Query_helper::get_info($this->config->item('table_setup_classification_variety_price'),array('price'),array('variety_id ='.$variety_id,'pack_size_id ='.$pack_size_id,'revision =1'),1);
        $price=$this->lang->line('LABEL_NOT_SET');
        if($info)
        {
            $price=$info['price'];
        }
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$price);

        $this->jsonReturn($ajax);
    }
    public function get_dropdown_curent_stock_by_variety_pack_size_id()
    {
        $html_container_id='#stock_current';
        if($this->input->post('html_container_id'))
        {
            $html_container_id=$this->input->post('html_container_id');
        }

        $variety_id = $this->input->post('variety_id');
        $pack_size_id = $this->input->post('pack_size_id');
        $this->load->model("sales_model");
        $stock_info=$this->sales_model->get_stocks(array(array('variety_id'=>$variety_id,'pack_size_id'=>$pack_size_id)));
        $current_stock='';
        if(isset($stock_info[$variety_id][$pack_size_id]))
        {
            $current_stock=$stock_info[$variety_id][$pack_size_id]['current_stock'];
        }

        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id,"html"=>$current_stock);

        $this->jsonReturn($ajax);
    }
    public function get_credit_by_customer_id()
    {
        $html_container_id_tp='#credit_tp';
        $html_container_id_net='#credit_net';
        if($this->input->post('html_container_id_tp'))
        {
            $html_container_id_tp=$this->input->post('html_container_id_tp');
        }
        if($this->input->post('html_container_id_net'))
        {
            $html_container_id_net=$this->input->post('html_container_id_net');
        }
        $customer_id = $this->input->post('customer_id');
        $this->load->model("sales_model");
        $current_credit=$this->sales_model->get_customer_current_credit($customer_id);
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>$html_container_id_tp,"html"=>number_format($current_credit['tp'],2));
        $ajax['system_content'][]=array("id"=>$html_container_id_net,"html"=>number_format($current_credit['net'],2));
        $this->jsonReturn($ajax);
    }


}
