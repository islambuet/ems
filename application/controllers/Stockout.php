<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stockout extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Stockout');
        $this->controller_url='stockout';
        //$this->load->model("sys_module_task_model");
        $this->load->model("sales_model");
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
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("stockout/list",$data,true));
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

            $data['title']="New Short Inventory";
            $data["stock_out"] = Array(
                'id' => 0,
                'warehouse_id' => '',
                'variety_id' => '',
                'pack_size_id' => '',
                'purpose'=>'',
                'quantity' => '0',
                'remarks' => '',
                'date_stock_out' => time()
            );
            $data['stock_current']='';
            $data['warehouses']=Query_helper::get_info($this->config->item('table_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("stockout/add_edit",$data,true));
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
            $this->db->from($this->config->item('table_stockout').' stei');
            $this->db->select('stei.*');
            $this->db->select('v.crop_type_id crop_type_id');
            $this->db->select('type.crop_id crop_id');

            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id = stei.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->where('stei.id',$stock_id);
            $this->db->where('stei.status',$this->config->item('system_status_active'));

            $data['stock_out']=$this->db->get()->row_array();
            if(!$data['stock_out'])
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
                die();
            }
            $data['title']="Edit Short Inventory";
            $data['warehouses']=Query_helper::get_info($this->config->item('table_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array());
            $data['crop_types']=Query_helper::get_info($this->config->item('table_setup_classification_crop_types'),array('id value','name text'),array('crop_id ='.$data['stock_out']['crop_id']));
            $data['varieties']=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),array('id value','name text'),array('crop_type_id ='.$data['stock_out']['crop_type_id']));
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_setup_classification_vpack_size'),array('id value','name text'),array());

            $stock_info=$this->sales_model->get_stocks(array(array('variety_id'=>$data['stock_out']['variety_id'],'pack_size_id'=>$data['stock_out']['pack_size_id'])));
            $data['stock_current']=$stock_info[$data['stock_out']['variety_id']][$data['stock_out']['pack_size_id']]['current_stock'];

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("stockout/add_edit",$data,true));
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
            $this->db->from($this->config->item('table_stockout').' stei');
            $this->db->select('stei.*');
            $this->db->select('v.crop_type_id crop_type_id');
            $this->db->select('type.crop_id crop_id');

            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id = stei.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->where('stei.id',$stock_id);
            $this->db->where('stei.status',$this->config->item('system_status_active'));

            $data['stock_out']=$this->db->get()->row_array();
            if(!$data['stock_out'])
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
                die();
            }
            $data['title']="Detail of Short Inventory";
            $data['warehouses']=Query_helper::get_info($this->config->item('table_basic_setup_warehouse'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array());
            $data['crop_types']=Query_helper::get_info($this->config->item('table_setup_classification_crop_types'),array('id value','name text'),array('crop_id ='.$data['stock_out']['crop_id']));
            $data['varieties']=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),array('id value','name text'),array('crop_type_id ='.$data['stock_out']['crop_type_id']));
            $data['pack_sizes']=Query_helper::get_info($this->config->item('table_setup_classification_vpack_size'),array('id value','name text'),array());

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("stockout/details",$data,true));
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
                Query_helper::update($this->config->item('table_stockout'),array('status'=>$this->config->item('system_status_delete'),'user_updated'=>$user->user_id,'date_updated'=>$time),array("id = ".$id));
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
            $data=$this->input->post('stock_out');
            $data['date_stock_out']=System_helper::get_time($data['date_stock_out']);
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = time();

                Query_helper::update($this->config->item('table_stockout'),$data,array("id = ".$id));

            }
            else
            {

                $data['user_created'] = $user->user_id;
                $data['date_created'] = time();
                Query_helper::add($this->config->item('table_stockout'),$data);
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
        $data=$this->input->post('stock_out');
        $this->load->library('form_validation');
        if($id==0)
        {
            $this->form_validation->set_rules('stock_out[warehouse_id]',$this->lang->line('LABEL_WAREHOUSE_NAME'),'required');
            $this->form_validation->set_rules('stock_out[variety_id]',$this->lang->line('LABEL_VARIETY_NAME'),'required');
            $this->form_validation->set_rules('stock_out[pack_size_id]',$this->lang->line('LABEL_PACK_NAME'),'required');
            $this->form_validation->set_rules('stock_out[purpose]',$this->lang->line('LABEL_STOCK_OUT_PURPOSE'),'required');
        }
        $this->form_validation->set_rules('stock_out[quantity]',$this->lang->line('LABEL_QUANTITY_PIECES'),'required|numeric');
        $this->form_validation->set_rules('stock_out[date_stock_out]',$this->lang->line('LABEL_DATE_STOCK_OUT'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        $actual_quantity=$data['quantity'];
        if($id>0)
        {
            $info=Query_helper::get_info($this->config->item('table_stockout'),'*',array('id ='.$id,'status ="'.$this->config->item('system_status_active').'"'),1);
            $data['variety_id']=$info['variety_id'];
            $data['pack_size_id']=$info['pack_size_id'];
            $actual_quantity-=$info['quantity'];
        }

        $stock_info=$this->sales_model->get_stocks(array(array('variety_id'=>$data['variety_id'],'pack_size_id'=>$data['pack_size_id'])));

        if($stock_info[$data['variety_id']][$data['pack_size_id']]['current_stock']<$actual_quantity)
        {
            $this->message="Excess Stock Out";
            return false;
        }


        return true;
    }
    public function get_items()
    {
        $this->db->from($this->config->item('table_stockout').' sout');
        $this->db->select('sout.*');
        $this->db->select('v.name variety_name');
        $this->db->select('crop.name crop_name');
        $this->db->select('type.name crop_type_name');
        $this->db->select('pack.name pack_size_name');
        $this->db->select('warehouse.name warehouse_name');
        $this->db->select('fy.name fiscal_year_name');

        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id = sout.variety_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_vpack_size').' pack','pack.id = sout.pack_size_id','INNER');
        $this->db->join($this->config->item('table_basic_setup_warehouse').' warehouse','warehouse.id = sout.warehouse_id','INNER');
        //$this->db->join($this->config->item('table_basic_setup_fiscal_year').' fy','fy.id = stei.fiscal_year_id','INNER');
        $this->db->join($this->config->item('table_basic_setup_fiscal_year').' fy','fy.date_start <= sout.date_stock_out and fy.date_end >= sout.date_stock_out','LEFT');
        $this->db->where('sout.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('sout.id','DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['quantity_weight']=number_format($item['quantity']*$item['pack_size_name']/1000,3, '.', '');
            $item['date_stock_out']=System_helper::display_date($item['date_stock_out']);
            $item['purpose']=$this->lang->line('LABEL_STOCK_OUT_PURPOSE_'.strtoupper($item['purpose']));
        }
        $this->jsonReturn($items);

    }

}
