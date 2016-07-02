<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sys_reset_approved_po extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Sys_reset_approved_po');
        $this->controller_url='sys_reset_approved_po';
    }

    public function index($action="search",$id=0)
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="save")
        {
            $this->system_save();
        }
        else
        {
            $this->system_search();
        }
    }
    private function system_search()
    {
        if(isset($this->permissions['edit'])&&($this->permissions['edit']==1))
        {

            $data['title']="Reset Approved PO";
            $ajax['system_page_url']=site_url($this->controller_url."/index/search");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("sys_reset_approved_po/search",$data,true));
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
    private function system_save()
    {
        if(isset($this->permissions['edit'])&&($this->permissions['edit']==1))
        {
            $user=User_helper::get_user();
            $time=time();
            $po_no=intval(trim($this->input->post('po_no')));
            $po_info=Query_helper::get_info($this->config->item('table_sales_po'),'*',array('id ='.$po_no),1);
            if(!$po_info)
            {
                $ajax['status']=false;
                $ajax['system_content'][]=array("id"=>"#po_reset_message_container","html"=>'<div class="alert alert-danger">PO not Found</div>');
                $this->jsonReturn($ajax);
            }
            else
            {
                if($po_info['status_approved']==$this->config->item('system_status_po_approval_pending'))
                {
                    $ajax['status']=false;
                    $ajax['system_content'][]=array("id"=>"#po_reset_message_container","html"=>'<div class="alert alert-success">PO Did not Approved yet</div>');
                    $this->jsonReturn($ajax);
                }
                elseif($po_info['status_delivered']==$this->config->item('system_status_po_delivery_delivered'))
                {
                    $ajax['status']=false;
                    $ajax['system_content'][]=array("id"=>"#po_reset_message_container","html"=>'<div class="alert alert-danger">PO Already Delivered</div>');
                    $this->jsonReturn($ajax);
                }
                else
                {
                    $this->db->trans_start();  //DB Transaction Handle START
                    $data=array();
                    $data['status_approved']=$this->config->item('system_status_po_approval_pending');
                    $data['remarks_approved']='';
                    $data['user_approved']=null;
                    $data['date_approved']=null;
                    $data['user_updated'] = $user->user_id;
                    $data['date_updated'] = $time;
                    Query_helper::update($this->config->item('table_sales_po'),$data,array("id = ".$po_no));
                    $data=array();
                    $data['po_id']=$po_no;
                    $data['status_field']='status_approved';
                    $data['new_status']=$this->config->item('system_status_po_approval_pending');
                    $data['previous_status']=$po_info['status_approved'];
                    $data['previous_info']=json_encode($po_info);
                    $data['user_created'] = $user->user_id;
                    $data['date_created'] = $time;
                    Query_helper::add($this->config->item('table_system_po_status_change'),$data);

                    $this->db->trans_complete();   //DB Transaction Handle END

                    if ($this->db->trans_status() === TRUE)
                    {
                        $ajax['status']=true;
                        $ajax['system_content'][]=array("id"=>"#po_reset_message_container","html"=>'<div class="alert alert-success">Po Status Changed To Pending</div>');
                        $this->jsonReturn($ajax);
                    }
                    else
                    {
                        $ajax['status']=false;
                        $ajax['system_content'][]=array("id"=>"#po_reset_message_container","html"=>'<div class="alert alert-danger">'.$this->lang->line("MSG_SAVED_FAIL").'</div>');
                        $this->jsonReturn($ajax);
                    }

                }
                $ajax['status']=false;
                $ajax['system_content'][]=array("id"=>"#po_reset_message_container","html"=>'ON Process');
                $this->jsonReturn($ajax);
            }
            /*if($po_info['status_requested']==$this->config->item('system_status_po_request_requested'))
            {
                $this->message=$this->lang->line('MSG_PO_EDIT_UNABLE');
                return false;
            }*/

            /*$this->db->trans_start();  //DB Transaction Handle START
            $data['user_created'] = $user->user_id;
            $data['date_created'] = time();
            Query_helper::add($this->config->item('table_system_site_offline'),$data);
            $this->db->trans_complete();   //DB Transaction Handle END

            if ($this->db->trans_status() === TRUE)
            {
                $this->dashboard_page();
            }
            else
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->jsonReturn($ajax);
            }*/
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }


    }
}
