<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sys_reset_approved_fdb extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Sys_reset_approved_fdb');
        $this->controller_url='sys_reset_approved_fdb';
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
            $data['title']="Reset Approved FDB";
            $ajax['system_page_url']=site_url($this->controller_url."/index/search");
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("sys_reset_approved_fdb/search",$data,true));
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
            $fdb_no=intval(trim($this->input->post('fdb_no')));
            $fdb_info=Query_helper::get_info($this->config->item('table_tm_fd_bud_budget'),'*',array('id ='.$fdb_no),1);
            $result=Query_helper::get_info($this->config->item('table_tm_fd_bud_reporting'),'*',array('budget_id ='.$fdb_no),1);
            if(!$fdb_info)
            {
                $ajax['status']=false;
                $ajax['system_content'][]=array("id"=>"#fdb_reset_message_container","html"=>'<div class="alert alert-danger">FDB not Found</div>');
                $this->jsonReturn($ajax);
            }
            elseif($result)
            {
                $ajax['status']=false;
                $ajax['system_content'][]=array("id"=>"#fdb_reset_message_container","html"=>'<div class="alert alert-danger">Field Day Reporting Complete. You Can Not Change This FBD Status.</div>');
                $this->jsonReturn($ajax);
            }
            else
            {
                if($fdb_info['status_approved']==$this->config->item('system_status_po_approval_pending'))
                {
                    $ajax['status']=false;
                    $ajax['system_content'][]=array("id"=>"#fdb_reset_message_container","html"=>'<div class="alert alert-success">FDB Did not Approved yet</div>');
                    $this->jsonReturn($ajax);
                }
                else
                {
                    $this->db->trans_start();  //DB Transaction Handle START
                    $data=array();
                    $data['status_approved']=$this->config->item('system_status_po_approval_pending');
                    $data['remarks_approved']=null;
                    $data['user_approved']=null;
                    $data['date_approved']=null;
                    $data['user_updated'] = $user->user_id;
                    $data['date_updated'] = $time;
                    Query_helper::update($this->config->item('table_tm_fd_bud_budget'),$data,array("id = ".$fdb_no));

                    $this->db->trans_complete();   //DB Transaction Handle END

                    if ($this->db->trans_status() === TRUE)
                    {
                        $ajax['status']=true;
                        $ajax['system_content'][]=array("id"=>"#fdb_reset_message_container","html"=>'<div class="alert alert-success">FDB Status Changed To Pending</div>');
                        $this->jsonReturn($ajax);
                    }
                    else
                    {
                        $ajax['status']=false;
                        $ajax['system_content'][]=array("id"=>"#fdb_reset_message_container","html"=>'<div class="alert alert-danger">'.$this->lang->line("MSG_SAVED_FAIL").'</div>');
                        $this->jsonReturn($ajax);
                    }

                }
                $ajax['status']=false;
                $ajax['system_content'][]=array("id"=>"#fdb_reset_message_container","html"=>'ON Process');
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
}