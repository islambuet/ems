<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stockin_excess extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Stockin_excess');
        $this->controller_url='stockin_excess';
        //$this->load->model("sys_module_task_model");
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=="add")
        {
            $this->system_add();
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="delete")
        {
            $this->system_delete();
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_list($id);
        }
    }

    private function system_list()
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            $data['title']="Excess Inventory list";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("stockin_excess/list",$data,true));
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

    private function system_add()
    {
        if(isset($this->permissions['add'])&&($this->permissions['add']==1))
        {

            $data['title']="New Excess Inventory";
            $data["stock_in"] = Array(
                'id' => 0,
                'fiscal_year_id' => '',
                'warehouse_id' => '',
                'variety_id' => '',
                'pack_size_id' => '',
                'quantity' => '0',
                'date_mfg' => '',
                'date_exp' => '',
                'date_stock_in' => time()
            );
            $data['fiscal_years']=Query_helper::get_info($this->config->item('table_basic_setup_fiscal_year'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['warehouses']=Query_helper::get_info($this->config->item('table_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("stockin_excess/add_edit",$data,true));
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
    private function system_edit($id)
    {
        if(isset($this->permissions['edit'])&&($this->permissions['edit']==1))
        {
            if(($this->input->post('id')))
            {
                $stock_id=$this->input->post('id');
            }
            else
            {
                $stock_id=$id;
            }
            $this->db->from($this->config->item('table_stockin_excess_inventory').' stei');
            $this->db->select('stei.*');
            $this->db->select('v.crop_type_id crop_type_id');
            $this->db->select('type.crop_id crop_id');

            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id = stei.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->where('stei.id',$stock_id);
            $this->db->where('stei.status',$this->config->item('system_status_active'));

            $data['stock_in']=$this->db->get()->row_array();
            if(!$data['stock_in'])
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
                die();
            }
            $data['title']="Edit Excess Inventory";
            $data['fiscal_years']=Query_helper::get_info($this->config->item('table_basic_setup_fiscal_year'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['warehouses']=Query_helper::get_info($this->config->item('table_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array());
            $data['crop_types']=Query_helper::get_info($this->config->item('table_setup_classification_crop_types'),array('id value','name text'),array('crop_id ='.$data['stock_in']['crop_id']));
            $data['varieties']=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),array('id value','name text'),array('crop_type_id ='.$data['stock_in']['crop_type_id']));
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_setup_classification_vpack_size'),array('id value','name text'),array());

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("stockin_excess/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$stock_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }
    private function system_details($id)
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            if(($this->input->post('id')))
            {
                $stock_id=$this->input->post('id');
            }
            else
            {
                $stock_id=$id;
            }
            $this->db->from($this->config->item('table_stockin_excess_inventory').' stei');
            $this->db->select('stei.*');
            $this->db->select('v.crop_type_id crop_type_id');
            $this->db->select('type.crop_id crop_id');

            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id = stei.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->where('stei.id',$stock_id);
            $this->db->where('stei.status',$this->config->item('system_status_active'));

            $data['stock_in']=$this->db->get()->row_array();
            if(!$data['stock_in'])
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
                die();
            }
            $data['title']="Detail of Excess Inventory";
            $data['fiscal_years']=Query_helper::get_info($this->config->item('table_basic_setup_fiscal_year'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['warehouses']=Query_helper::get_info($this->config->item('table_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array());
            $data['crop_types']=Query_helper::get_info($this->config->item('table_setup_classification_crop_types'),array('id value','name text'),array('crop_id ='.$data['stock_in']['crop_id']));
            $data['varieties']=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),array('id value','name text'),array('crop_type_id ='.$data['stock_in']['crop_type_id']));
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_setup_classification_vpack_size'),array('id value','name text'),array());

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("stockin_excess/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$stock_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }
    private function system_delete()
    {
        if(isset($this->permissions['delete'])&&($this->permissions['delete']==1))
        {
            $ids = $this->input->post("ids");
            $user = User_helper::get_user();
            $this->db->trans_start();  //DB Transaction Handle START
            $time=time();
            foreach($ids as $id)
            {
                Query_helper::update($this->config->item('table_stockin_excess_inventory'),array('status'=>$this->config->item('system_status_delete'),'user_updated'=>$user->user_id,'date_updated'=>$time),array("id = ".$id));
            }
            $this->db->trans_complete();   //DB Transaction Handle END

            if ($this->db->trans_status() === TRUE)
            {
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                $this->system_list();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->jsonReturn($ajax);
            }
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }

    }

    private function system_save()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if($id>0)
        {
            if(!(isset($this->permissions['edit'])&&($this->permissions['edit']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
                die();
            }
        }
        else
        {
            if(!(isset($this->permissions['add'])&&($this->permissions['add']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
                die();

            }
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->jsonReturn($ajax);
        }
        else
        {
            $data=$this->input->post('stock_in');
            $data['date_stock_in']=System_helper::get_time($data['date_stock_in']);
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = time();

                Query_helper::update($this->config->item('table_stockin_excess_inventory'),$data,array("id = ".$id));

            }
            else
            {

                $data['user_created'] = $user->user_id;
                $data['date_created'] = time();
                Query_helper::add($this->config->item('table_stockin_excess_inventory'),$data);
            }
            $this->db->trans_complete();   //DB Transaction Handle END
            if ($this->db->trans_status() === TRUE)
            {
                $save_and_new=$this->input->post('system_save_new_status');
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                if($save_and_new==1)
                {
                    $this->system_add();
                }
                else
                {
                    $this->system_list();
                }
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->jsonReturn($ajax);
            }
        }
    }
    private function check_validation()
    {
        $id=$this->input->post('id');
        $data=$this->input->post('stock_in');
        $this->load->library('form_validation');
        if($id==0)
        {
            $this->form_validation->set_rules('stock_in[fiscal_year_id]',$this->lang->line('LABEL_FISCAL_YEAR'),'required');
            $this->form_validation->set_rules('stock_in[warehouse_id]',$this->lang->line('LABEL_WAREHOUSE_NAME'),'required');
            $this->form_validation->set_rules('stock_in[variety_id]',$this->lang->line('LABEL_VARIETY_NAME'),'required');
            $this->form_validation->set_rules('stock_in[pack_size_id]',$this->lang->line('LABEL_PACK_NAME'),'required');
        }
        else
        {
            $info=Query_helper::get_info($this->config->item('table_stockin_varieties'),'*',array('id ='.$id),1);
            $data['fiscal_year_id']=$info['fiscal_year_id'];
        }
        $this->form_validation->set_rules('stock_in[quantity]',$this->lang->line('LABEL_QUANTITY_PIECES'),'required|numeric');
        $this->form_validation->set_rules('stock_in[date_stock_in]',$this->lang->line('LABEL_DATE_STOCK_IN'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        $fiscal_info=Query_helper::get_info($this->config->item('table_basic_setup_fiscal_year'),'*',array('status ="'.$this->config->item('system_status_active').'"','id ='.$data['fiscal_year_id']),1);
        $stock_in_date=System_helper::get_time($data['date_stock_in']);
        if($stock_in_date<$fiscal_info['date_start']||$stock_in_date>$fiscal_info['date_end'])
        {
            $this->message='Stock In date must be between fiscal year';
            return false;
        }
        return true;
    }
    public function get_items()
    {
        $this->db->from($this->config->item('table_stockin_excess_inventory').' stei');
        $this->db->select('stei.id,stei.quantity');
        $this->db->select('v.name variety_name');
        $this->db->select('crop.name crop_name');
        $this->db->select('type.name crop_type_name');
        $this->db->select('pack.name pack_size_name');
        $this->db->select('warehouse.name warehouse_name');
        $this->db->select('fy.name fiscal_year_name');

        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id = stei.variety_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_vpack_size').' pack','pack.id = stei.pack_size_id','INNER');
        $this->db->join($this->config->item('table_basic_setup_warehouse').' warehouse','warehouse.id = stei.warehouse_id','INNER');
        $this->db->join($this->config->item('table_basic_setup_fiscal_year').' fy','fy.id = stei.fiscal_year_id','INNER');
        $this->db->where('stei.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('stei.id','DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['quantity_weight']=number_format($item['quantity']*$item['pack_size_name']/1000,3, '.', '');
        }
        $this->jsonReturn($items);

    }

}
