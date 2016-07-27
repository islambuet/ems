<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment_receive extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Payment_receive');
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
        $this->controller_url='payment_receive';
        //$this->load->model("sys_module_task_model");
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=="edit")
        {
            $this->system_edit($id);
        }
        elseif($action=="details")
        {
            $this->system_details($id);
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
            $data['title']="Payment Receive List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("payment_receive/list",$data,true));
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
    private function system_edit($id)
    {
        if((isset($this->permissions['edit'])&&($this->permissions['edit']==1))||(isset($this->permissions['add'])&&($this->permissions['add']==1)))
        {
            if(($this->input->post('id')))
            {
                $payment_id=$this->input->post('id');
            }
            else
            {
                $payment_id=$id;
            }

            $this->db->from($this->config->item('table_payment_payment').' payment');
            $this->db->select('payment.*');
            $this->db->select('CONCAT(cus.customer_code," - ",cus.name) customer_name');
            $this->db->select('cus.id customer_id');
            $this->db->select('d.name district_name,d.id district_id');
            $this->db->select('t.name territory_name,t.id territory_id');
            $this->db->select('zone.name zone_name,zone.id zone_id');
            $this->db->select('division.name division_name,division.id division_id');
            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = payment.customer_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->where('payment.id',$payment_id);
            $data['payment']=$this->db->get()->row_array();
            if(!$data['payment'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$payment_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if(!$this->check_my_editable($data['payment']))
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$payment_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['banks']=Query_helper::get_info($this->config->item('table_basic_setup_bank'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['arm_banks']=Query_helper::get_info($this->config->item('table_basic_setup_arm_bank'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $this->load->model("sales_model");
            $balance=$this->sales_model->get_customer_current_credit($data['payment']['customer_id']);
            $data['payment']['credit']=$balance['tp'];
            if($data['payment']['date_payment_receive']>0)
            {
                $data['payment']['credit']=$balance['tp']+$data['payment']['amount'];
            }
            if($data['payment']['date_payment_receive']>0)
            {
                if(!(isset($this->permissions['edit'])&&($this->permissions['edit']==1)))
                {
                    $ajax['status']=false;
                    $ajax['system_message']="Already Received";
                    $this->jsonReturn($ajax);
                }
            }
            else
            {
                $data['payment']['date_payment_receive']=time();
                $data['payment']['amount']=$data['payment']['amount_customer'];
            }
            $user_ids=array();
            $user_ids[$data['payment']['user_created']]=$data['payment']['user_created'];
            $data['users']=System_helper::get_users_info($user_ids);

            $data['title']="Receive Payment";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("payment_receive/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$payment_id);
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
                $payment_id=$this->input->post('id');
            }
            else
            {
                $payment_id=$id;
            }
            $this->db->from($this->config->item('table_payment_payment').' payment');
            $this->db->select('payment.*');
            $this->db->select('CONCAT(cus.customer_code," - ",cus.name) customer_name');
            $this->db->select('cus.id customer_id');
            $this->db->select('d.name district_name,d.id district_id');
            $this->db->select('t.name territory_name,t.id territory_id');
            $this->db->select('zone.name zone_name,zone.id zone_id');
            $this->db->select('division.name division_name,division.id division_id');
            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = payment.customer_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->where('payment.id',$payment_id);
            $data['payment']=$this->db->get()->row_array();
            if(!$data['payment'])
            {
                System_helper::invalid_try($this->config->item('system_view_not_exists'),$payment_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if(!$this->check_my_editable($data['payment']))
            {
                System_helper::invalid_try($this->config->item('system_view_others'),$payment_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['banks']=Query_helper::get_info($this->config->item('table_basic_setup_bank'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['arm_banks']=Query_helper::get_info($this->config->item('table_basic_setup_arm_bank'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['arm_bank_accounts']=array();
            if($data['payment']['arm_bank_id']>0)
            {
                $data['arm_bank_accounts']=Query_helper::get_info($this->config->item('table_basic_setup_arm_bank'),array('id value','name text'),array('bank_id'=>$data['payment']['arm_bank_id'],'status ="'.$this->config->item('system_status_active').'"'));
            }

            $user_ids=array();
            $user_ids[$data['payment']['user_created']]=$data['payment']['user_created'];
            if($data['payment']['user_receive']>0)
            {
                $user_ids[$data['payment']['user_receive']]=$data['payment']['user_receive'];
            }
            $data['users']=System_helper::get_users_info($user_ids);
            $data['title']="Details of Payment";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("payment_receive/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$payment_id);
            $this->jsonReturn($ajax);
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
        if(!((isset($this->permissions['edit'])&&($this->permissions['edit']==1))||(isset($this->permissions['add'])&&($this->permissions['add']==1))))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
            die();
        }
        if(!$this->check_validation())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->jsonReturn($ajax);
        }
        else
        {
            $time=time();
            $data=$this->input->post('payment');
            $data['date_payment_receive']=System_helper::get_time($data['date_payment_receive']);
            $this->db->trans_start();  //DB Transaction Handle START

                $data['user_updated'] = $user->user_id;
                $data['user_receive'] = $user->user_id;
                $data['date_updated'] = $time;
                $data['date_receive'] = $time;
                Query_helper::update($this->config->item('table_payment_payment'),$data,array("id = ".$id));


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
    }
    private function check_my_editable($customer)
    {
        if(($this->locations['division_id']>0)&&($this->locations['division_id']!=$customer['division_id']))
        {
            return false;
        }
        if(($this->locations['zone_id']>0)&&($this->locations['zone_id']!=$customer['zone_id']))
        {
            return false;
        }
        if(($this->locations['territory_id']>0)&&($this->locations['territory_id']!=$customer['territory_id']))
        {
            return false;
        }
        if(($this->locations['district_id']>0)&&($this->locations['district_id']!=$customer['district_id']))
        {
            return false;
        }
        return true;
    }
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('payment[amount]','Receive amount','required|numeric');
        $this->form_validation->set_rules('payment[date_payment_receive]',$this->lang->line('LABEL_DATE_RECEIVE'),'required');
        $this->form_validation->set_rules('payment[arm_bank_id]',$this->lang->line('LABEL_ARM_BANK_NAME'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        $id=$this->input->post('id');
        if($id>0)
        {
            $this->db->from($this->config->item('table_payment_payment').' payment');
            $this->db->select('payment.*');
            $this->db->select('cus.name');
            $this->db->select('d.name district_name,d.id district_id');
            $this->db->select('t.name territory_name,t.id territory_id');
            $this->db->select('zone.name zone_name,zone.id zone_id');
            $this->db->select('division.name division_name,division.id division_id');
            $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = payment.customer_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->where('payment.id',$id);

            $payment=$this->db->get()->row_array();


            if(!$payment)
            {
                System_helper::invalid_try($this->config->item('system_save'),$id,'Hack trying to edit an id that does not exits');
                $this->message="Invalid Try";
                return false;
            }
            if(!$this->check_my_editable($payment))
            {
                System_helper::invalid_try($this->config->item('system_save'),$id,'Hack To edit other customer that does not in my area');
                $this->message="Invalid Try";
                return false;
            }
        }

        return true;
    }
    public function get_items()
    {
        $this->db->from($this->config->item('table_payment_payment').' payment');
        $this->db->select('payment.id,payment.amount,payment.amount_customer,payment.payment_way,payment.date_payment_customer,payment.date_payment_receive,payment.cheque_no');
        $this->db->select('CONCAT(cus.customer_code," - ",cus.name) customer_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->join($this->config->item('table_csetup_customers').' cus','cus.id = payment.customer_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        if($this->locations['division_id']>0)
        {
            $this->db->where('division.id',$this->locations['division_id']);
            if($this->locations['zone_id']>0)
            {
                $this->db->where('zone.id',$this->locations['zone_id']);
                if($this->locations['territory_id']>0)
                {
                    $this->db->where('t.id',$this->locations['territory_id']);
                    if($this->locations['district_id']>0)
                    {
                        $this->db->where('d.id',$this->locations['district_id']);
                    }
                }
            }
        }
        $this->db->where('payment.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('payment.id','DESC');
        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_payment_customer']=System_helper::display_date($item['date_payment_customer']);
            if($item['date_payment_receive']>0)
            {
                $item['date_payment_receive']=System_helper::display_date($item['date_payment_receive']);
                $item['status_receive']='Received';
            }
            else
            {
                $item['date_payment_receive']='';
                $item['status_receive']='Pending';
            }
            $item['amount_customer']=number_format($item['amount_customer'],2);
            if($item['amount']>0)
            {
                $item['amount']=number_format($item['amount'],2);
            }
            $item['payment_id']=str_pad($item['id'],6,'0',STR_PAD_LEFT);
        }
        $this->jsonReturn($items);
    }

}
