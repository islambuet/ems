<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 3/19/17
 * Time: 10:03 AM
 */

class Tm_fd_bud_report_approve extends Root_Controller {

    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_fd_bud_report_approve');
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
        $this->permissions['report_approve']=1;
        if($this->locations['territory_id']>0)
        {
            $this->permissions['report_approve']=0;
        }
        $this->controller_url='tm_fd_bud_report_approve';
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list();
        }
        if($action=="get_items")
        {
            $this->system_get_items();
        }
        if($action=="edit")
        {
            $this->system_edit($id);
        }
        if($action=="save")
        {
            $this->system_save();
        }
        elseif($action=="details")
        {
            $this->system_details($id);
        }
        elseif($action=="approve")
        {
            $this->system_approve($id);
        }
        elseif($action=="save_approval")
        {
            $this->system_save_approval();
        }
        else
        {
            $this->system_list();
        }
    }

    private function system_list()
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            $data['title']="Field Day Reporting List for Approval";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/list",$data,true));
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

    private function system_get_items()
    {
        $this->db->from($this->config->item('table_tm_fd_bud_info_details').' fdb_details');
        $this->db->select('fdb_details.*');
        $this->db->select('fdb.*');
        $this->db->select('v.name variety_name');
        $this->db->select('v1.name com_variety_name');
        $this->db->select('crop.name crop_name');
        $this->db->select('type.name crop_type_name');
        $this->db->select('u.name upazilla_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');

        $this->db->join($this->config->item('table_tm_fd_bud_budget').' fdb','fdb.id = fdb_details.budget_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id = fdb_details.variety_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
        $this->db->join($this->config->item('table_setup_location_upazillas').' u','u.id = fdb_details.upazilla_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = u.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_varieties').' v1','v1.id = fdb_details.competitor_variety_id','LEFT');

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
                        if($this->locations['upazilla_id']>0)
                        {
                            $this->db->where('u.id',$this->locations['upazilla_id']);
                        }
                    }
                }
            }
        }
        $this->db->where('fdb_details.revision',1);
        $this->db->where('fdb.status_reporting',$this->config->item('LABEL_FDR_FORWARDED'));
        $this->db->group_by('fdb.id');
        $this->db->order_by('fdb.id','DESC');
        $items=$this->db->get()->result_array();

        foreach($items as &$item)
        {
            $item['date']=System_helper::display_date($item['date']);
            $item['expected_date']=System_helper::display_date($item['expected_date']);
            $item['total_budget']=number_format($item['total_budget'],2);
        }
        $this->jsonReturn($items);
    }

    private function check_my_editable($security)
    {
        if(($this->locations['division_id']>0)&&($this->locations['division_id']!=$security['division_id']))
        {
            return false;
        }
        if(($this->locations['zone_id']>0)&&($this->locations['zone_id']!=$security['zone_id']))
        {
            return false;
        }
        if(($this->locations['territory_id']>0)&&($this->locations['territory_id']!=$security['territory_id']))
        {
            return false;
        }
        if(($this->locations['district_id']>0)&&($this->locations['district_id']!=$security['district_id']))
        {
            return false;
        }
        if(($this->locations['upazilla_id']>0)&&($this->locations['upazilla_id']!=$security['upazilla_id']))
        {
            return false;
        }
        return true;
    }

    private function system_edit($id)
    {
        if(isset($this->permissions['edit'])&&($this->permissions['edit']==1))
        {
            if($this->input->post('id'))
            {
                $budgeted_id=$this->input->post('id');
            }
            else
            {
                $budgeted_id=$id;
            }

            $result=Query_helper::get_info($this->config->item('table_tm_fd_bud_reporting'),'*',array('budget_id ='.$budgeted_id));
            if(!$result)
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_HAVE_TO_COMPLETE");
                $this->jsonReturn($ajax);
            }

            $this->db->from($this->config->item('table_tm_fd_bud_info_details').' fdb_details');
            $this->db->select('fdb_details.*');
            $this->db->select('fdb.*');
            $this->db->select('v.name variety_name');
            $this->db->select('v1.name com_variety_name');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->select('type.name crop_type_name,type.id crop_type_id');
            $this->db->select('u.name upazilla_name');
            $this->db->select('d.name district_name,d.id district_id');
            $this->db->select('t.name territory_name,t.id territory_id');
            $this->db->select('zone.name zone_name,zone.id zone_id');
            $this->db->select('division.name division_name,division.id division_id');

            $this->db->join($this->config->item('table_tm_fd_bud_budget').' fdb','fdb.id = fdb_details.budget_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id = fdb_details.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->join($this->config->item('table_setup_location_upazillas').' u','u.id = fdb_details.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = u.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v1','v1.id = fdb_details.competitor_variety_id','LEFT');
            $this->db->where('fdb_details.budget_id',$budgeted_id);
            $this->db->where('fdb_details.revision',1);
            $data['item_info']=$this->db->get()->row_array();
            if(!$data['item_info'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$budgeted_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if(!$this->check_my_editable($data['item_info']))
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$budgeted_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if($data['item_info']['status_report_approved']==$this->config->item('system_status_po_request_approved')||$data['item_info']['status_report_approved']==$this->config->item('system_status_po_request_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']='The Field Day Report '.$data['item_info']['status_report_approved'].' Already';
                $this->jsonReturn($ajax);
            }
            if($data['item_info']['status_requested']==$this->config->item('system_status_po_request_pending'))
            {
                System_helper::invalid_try('Trying to edit FDR when FDB not requested for approval',$budgeted_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if($data['item_info']['status_reporting']!=$this->config->item('LABEL_FDR_FORWARDED'))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if($data['item_info']['status_requested']==$this->config->item('system_status_po_request_rejected'))
            {
                System_helper::invalid_try('Trying to edit FDR when FDB request rejected',$budgeted_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if($data['item_info']['status_approved']==$this->config->item('system_status_po_request_pending'))
            {
                System_helper::invalid_try('Trying to edit FDR when FDB approval status pending',$budgeted_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if($data['item_info']['status_approved']==$this->config->item('system_status_po_request_rejected'))
            {
                System_helper::invalid_try('Trying to edit FDR when FDB approval rejected',$budgeted_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

            $results=Query_helper::get_info($this->config->item('table_setup_fd_bud_expense_items'),array('id value','name text','status'),array(),0,0,array('ordering ASC'));
            foreach($results as $result)
            {
                $data['expense_items'][$result['value']]=$result;
            }
            $data['expense_budget']=array();
            $results=Query_helper::get_info($this->config->item('table_tm_fd_bud_details_expense'),'*',array('budget_id ='.$budgeted_id,'revision=1'));
            foreach($results as $result)
            {
                $data['expense_budget'][$result['item_id']]=$result;
            }

            $results=Query_helper::get_info($this->config->item('table_setup_fsetup_leading_farmer'),array('id value','name text','phone_no','status'),array('upazilla_id ='.$data['item_info']['upazilla_id']),0,0,array('ordering ASC'));
            $data['leading_farmers']=array();
            foreach($results as $result)
            {
                $data['leading_farmers'][$result['value']]=$result;
            }
            $data['participants']=array();
            $data['total']='';
            $results=Query_helper::get_info($this->config->item('table_tm_fd_bud_details_participant'),'*',array('budget_id ='.$budgeted_id,'revision=1'));
            foreach($results as $result)
            {
                $data['participants'][$result['farmer_id']]=$result;
            }
            $data['farmers']=array();

            $data['file_details']=array();
            $data['video_file_details']['file_name']='';
            $data['video_file_details']['file_type']='';

            $result=Query_helper::get_info($this->config->item('table_tm_fd_bud_reporting'),'*',array('budget_id ='.$budgeted_id));

            foreach($result as $res)
            {
                $data['item']['date']=$res['date'];
                $data['item']['date_of_fd']=$res['date_of_fd'];
                $data['item']['recommendation']=$res['recommendation'];
            }
            $results=Query_helper::get_info($this->config->item('table_tm_fd_rep_details_expense'),'*',array('budget_id ='.$budgeted_id,'revision=1'));
            foreach($results as $res)
            {
                $data['expense_report'][$res['item_id']]=$res;
            }

            $result=Query_helper::get_info($this->config->item('table_tm_fd_rep_details_info'),'*',array('budget_id ='.$budgeted_id,'revision=1'));
            foreach($result as $res)
            {
                $data['new_item']=$res;
            }
            $data['item']['id']=$data['new_item']['id'];
            $data['item']['budget_id']=$data['new_item']['budget_id'];

            $results=Query_helper::get_info($this->config->item('table_tm_fd_rep_details_participant'),'*',array('budget_id ='.$budgeted_id,'revision=1'));
            foreach($results as $res)
            {
                $data['farmers'][$res['farmer_id']]=$res;
            }

            $results=Query_helper::get_info($this->config->item('table_tm_fd_rep_details_picture'),'*',array('budget_id ='.$budgeted_id,'revision=1'));
            foreach($results as $result)
            {
                if(substr($result['file_type'],0,5)=='image')
                {
                    $data['file_details'][]=$result;
                }
                elseif(substr($result['file_type'],0,5)=='video')
                {
                    $data['video_file_details']=$result;
                }
            }

            $expense_files=Query_helper::get_info($this->config->item('table_tm_fd_rep_expense_picture'),'*',array('budget_id='.$budgeted_id,'revision=1'));
            $data['expense_files']=array();
            foreach($expense_files as $expense)
            {
                $data['expense_files'][$expense['item_id']][]=$expense;
            }

            $data['title']='Editing Report On Field Day';
            $ajax['system_page_url']=site_url($this->controller_url."/index/edit/".$budgeted_id);
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("LABEL_NOT_EDITABLE");
            $this->jsonReturn($ajax);
        }
    }

    private function system_save()
    {
        if(isset($_FILES['video'])){
            if($_FILES['video']['size']>10000000 && $_FILES['video']['type']!='video/mp4')
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("Please Upload a Short Video File (Below 10MB)");
                $this->jsonReturn($ajax);
            }}
        $id = $this->input->post("id");
        $budget_id = $this->input->post("item[budget_id]");
        $user = User_helper::get_user();
        if($id>0 && $budget_id>0)
        {
            $this->db->from($this->config->item('table_tm_fd_bud_info_details').' fdb_details');
            $this->db->select('fdb_details.upazilla_id');
            $this->db->select('u.district_id');
            $this->db->select('d.territory_id');
            $this->db->select('t.zone_id zone_id');
            $this->db->select('zone.division_id division_id');

            $this->db->join($this->config->item('table_setup_location_upazillas').' u','u.id = fdb_details.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = u.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->where('fdb_details.budget_id',$budget_id);
            //$this->db->where('fdb_details.id',$id);
            $this->db->where('fdb_details.revision',1);
            $data['item_info']=$this->db->get()->row_array();
            //print_r($data['item_info']);exit;
            if(!$this->check_my_editable($data['item_info']))
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
        }
        elseif($id==0 && $budget_id>0)
        {
            $result=Query_helper::get_info($this->config->item('table_tm_fd_bud_reporting'),'*',array('budget_id ='.$budget_id));
            if($result)
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
        }
        else
        {
            System_helper::invalid_try($this->config->item('system_edit_others'),$id);
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
        if($id>0)
        {
            if(!(isset($this->permissions['edit'])&&($this->permissions['edit']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
        }
        else
        {
            if(!(isset($this->permissions['add'])&&($this->permissions['add']==1)))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
        }

        $result=Query_helper::get_info($this->config->item('table_tm_fd_bud_budget'),'*',array('id ='.$budget_id));
        if($result[0]['status_reporting']!=$this->config->item('LABEL_FDR_FORWARDED'))
        {
            $ajax['status']=false;
            $ajax['system_message']='Report Not Forwarded Yet.';
            $this->jsonReturn($ajax);
        }
        if($result[0]['status_report_approved']!=$this->config->item('system_status_po_approval_pending'))
        {
            $this->message='FDR Already '.$result[0]['status_report_approved'].'. You Cannot Edit It.';
            $this->system_list();
        }

        $participants=$this->input->post('farmers');
        $expense_report=$this->input->post('expense_report');
        if(!$this->check_validation($participants,$expense_report))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->jsonReturn($ajax);
        }
        else
        {
            $time=time();
            $field_report=$this->input->post('item');
            $field_report['date']=System_helper::get_time($field_report['date']);
            $field_report['date_of_fd']=System_helper::get_time($field_report['date_of_fd']);
            $field_report_details=$this->input->post('new_item');
            $total_expense=0;
            foreach($expense_report as &$exp_report)
            {
                if($exp_report=='')
                {
                    $exp_report=0;
                }
                $total_expense+=$exp_report;
            }
            $field_report_details['total_expense'] = $total_expense;
            $total_participant=0;
            $field_report_details['participant_through_customer']=floor($field_report_details['participant_through_customer']);
            $field_report_details['participant_through_others']=floor($field_report_details['participant_through_others']);
            foreach($participants as &$no_of_participant)
            {
                if($no_of_participant=='')
                {
                    $no_of_participant=0;
                }
                if($no_of_participant>0)
                {
                    $no_of_participant=floor($no_of_participant);
                    $total_participant+=$no_of_participant;
                }
            }
            $field_report_details['total_participant']=$total_participant+$field_report_details['participant_through_customer']+$field_report_details['participant_through_others']+$field_report_details['guest'];
            $budget_id=$field_report['budget_id'];
            $this->db->trans_begin();  //DB Transaction Handle START
            if($id>0)
            {
                $field_report['user_updated'] = $user->user_id;
                $field_report['date_updated'] = $time;
                Query_helper::update($this->config->item('table_tm_fd_bud_reporting'),$field_report,array("budget_id = ".$budget_id));
            }
            else
            {
                $field_report['user_created'] = $user->user_id;
                $field_report['date_created'] = $time;
                $report_id=Query_helper::add($this->config->item('table_tm_fd_bud_reporting'),$field_report);
                if($report_id===false)
                {
                    $this->db->trans_rollback();
                    $ajax['status']=false;
                    $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                    $this->jsonReturn($ajax);
                }
            }
            //reporting details start
            $this->db->where('budget_id',$budget_id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_tm_fd_rep_details_info'));
            $field_report_details['budget_id']=$budget_id;
            $field_report_details['revision']=1;
            $field_report_details['user_created'] = $user->user_id;
            $field_report_details['date_created'] = $time;
            Query_helper::add($this->config->item('table_tm_fd_rep_details_info'),$field_report_details);
            //reporting details end

            //expense details start
            $this->db->where('budget_id',$budget_id);
            $this->db->set('revision','revision+1', FALSE);
            $this->db->update($this->config->item('table_tm_fd_rep_details_expense'));
            foreach($expense_report as $item_id=>$amount)
            {
                $data=array();
                $data['budget_id']=$budget_id;
                $data['item_id']=$item_id;
                $data['amount']=$amount;
                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                $data['revision']=1;
                Query_helper::add($this->config->item('table_tm_fd_rep_details_expense'),$data);
            }
            //expense details end

            //participant though leading farmer details start
            $this->db->where('budget_id',$budget_id);
            $this->db->set('revision','revision+1', FALSE);
            $this->db->update($this->config->item('table_tm_fd_rep_details_participant'));
            foreach($participants as $farmer_id=>$number)
            {
                $data=array();
                $data['budget_id']=$budget_id;
                $data['farmer_id']=$farmer_id;
                $data['number']=$number;
                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                $data['revision']=1;
                Query_helper::add($this->config->item('table_tm_fd_rep_details_participant'),$data);
            }
            //participant though leading farmer details end

            //file details start
            $this->db->where('budget_id',$budget_id);
            $this->db->set('revision', 'revision+1', FALSE);
            $this->db->update($this->config->item('table_tm_fd_rep_details_picture'));
            $file_folder='images/field_day_reporting/'.$budget_id;
            $dir=(FCPATH).$file_folder;
            if(!is_dir($dir))
            {
                mkdir($dir, 0777);
            }
            $types='gif|jpg|png|jpeg|wmv|mp4|mov|ftv|mkv|3gp|avi';
            $uploaded_files = System_helper::upload_file($file_folder,$types);
            foreach($uploaded_files as $file)
            {
                if(!$file['status'])
                {
                    $this->db->trans_rollback();
                    $ajax['status']=false;
                    $ajax['system_message']=$file['message'];
                    $this->jsonReturn($ajax);
                }
            }

            //-----file expense

            if($this->input->post('h_expense_files'))
            {
                $h_expense_files=$this->input->post('h_expense_files');
            }
            if($this->input->post('demo'))
            {
                $total_index=$this->input->post('demo');
            }
            else
            {
                $total_index=array();
            }
            //print_r($total_index);


            $this->db->where('budget_id',$budget_id);
            $this->db->set('revision','revision+1', FALSE);
            $this->db->update($this->config->item('table_tm_fd_rep_expense_picture'));
            $results=Query_helper::get_info($this->config->item('table_setup_fd_bud_expense_items'),array('id value'),array(),0,0,array('ordering ASC'));
            foreach($results as $result)
            {
                $items[$result['value']]=$result['value'];
            }
            foreach($items as $item_id=>$demo)
            {
                if(isset($total_index[$item_id]))
                {

                }
                else
                {
                    $total_index[$item_id]=array();
                }
                foreach($total_index[$item_id] as $i=>$val)
                {
                    if(isset($uploaded_files['expense_file_'.$item_id.'_'.$i]['info']['file_name']) && $uploaded_files['expense_file_'.$item_id.'_'.$i]['info']['is_image']==1)
                    {
                        $data=array();
                        $data['budget_id']=$budget_id;
                        $data['item_id']=$item_id;
                        $data['file_location']=$file_folder.'/'.$uploaded_files['expense_file_'.$item_id.'_'.$i]['info']['file_name'];
                        $data['file_name']=$uploaded_files['expense_file_'.$item_id.'_'.$i]['info']['file_name'];
                        $data['user_created'] = $user->user_id;
                        $data['date_created'] = $time;
                        $data['revision']=1;
                        Query_helper::add($this->config->item('table_tm_fd_rep_expense_picture'),$data);
                    }
                    elseif(isset($uploaded_files['expense_file_'.$item_id.'_'.$i]['info']['file_name']) && $uploaded_files['expense_file_'.$item_id.'_'.$i]['info']['is_image']!=1)
                    {
                        $this->db->trans_rollback();
                        $dlt=(FCPATH).$file_folder.'/'.$uploaded_files['expense_file_'.$item_id.'_'.$i]['info']['file_name'];
                        unlink($dlt);
                        $ajax['status']=false;
                        $ajax['system_message']=$this->lang->line("Please Upload a Image File for Field Day Expense");
                        $this->jsonReturn($ajax);
                    }
                    elseif(!isset($uploaded_files['expense_file_'.$item_id.'_'.$i]['info']['file_name']) && isset($h_expense_files[$item_id][$i]) && $h_expense_files[$item_id][$i]!='')
                    {
                        $data=array();
                        $data['budget_id']=$budget_id;
                        $data['item_id']=$item_id;
                        $data['file_location']=$file_folder.'/'.$h_expense_files[$item_id][$i];
                        $data['file_name']=$h_expense_files[$item_id][$i];
                        $data['user_created'] = $user->user_id;
                        $data['date_created'] = $time;
                        $data['revision']=1;
                        Query_helper::add($this->config->item('table_tm_fd_rep_expense_picture'),$data);
                    }
                    elseif(!isset($uploaded_files['expense_file_'.$item_id.'_'.$i]['info']['file_name']) && !isset($h_expense_files[$item_id][$i]))
                    {
                        $data=array();
                    }
                }
            }

            //-----file expense



            $files=array();
            $remarks=array();
            if($this->input->post('files')){$files=$this->input->post('files');}
            if($this->input->post('remarks')){$remarks=$this->input->post('remarks');}
            foreach($remarks as $index=>$remark)
            {
                if((isset($uploaded_files['file_'.$index])))
                {
                    $type=substr($uploaded_files['file_'.$index]['info']['file_type'],0,5);
                }
                else
                {
                    $type=substr($files['file_type_'.$index],0,5);
                }
                if($type=='image')
                {
                    $data=array();
                    $data['budget_id']=$budget_id;
                    if(isset($uploaded_files['file_'.$index]))
                    {
                        $data['file_location']=$file_folder.'/'.$uploaded_files['file_'.$index]['info']['file_name'];
                        $data['file_name']=$uploaded_files['file_'.$index]['info']['file_name'];
                        $data['file_type']=$uploaded_files['file_'.$index]['info']['file_type'];
                    }
                    else
                    {
                        $data['file_location']=$file_folder.'/'.$files['file_'.$index];
                        $data['file_name']=$files['file_'.$index];
                        $data['file_type']=$files['file_type_'.$index];
                    }
                    $data['file_remarks']=$remark;
                    $data['user_created'] = $user->user_id;
                    $data['date_created'] = $time;
                    $data['revision']=1;
                    Query_helper::add($this->config->item('table_tm_fd_rep_details_picture'),$data);
                }
                else
                {
                    $this->db->trans_rollback();
                    $ajax['status']=false;
                    $ajax['system_message']=$this->lang->line("Please Upload a Image File");
                    $this->jsonReturn($ajax);
                }
            }
            $data=array();
            if($this->input->post('video_file'))
            {
                $video_file=$this->input->post('video_file');
            }
            if(isset($uploaded_files['video']))
            {
                $type=substr($uploaded_files['video']['info']['file_type'],0,5);
            }
            elseif(isset($video_file['file_type']))
            {
                $type=substr($video_file['file_type'],0,5);
            }
            else
            {
                $type='';
            }
            if($type=='video')
            {
                if(isset($uploaded_files['video']))
                {
                    $data['file_location']=$file_folder.'/'.$uploaded_files['video']['info']['file_name'];
                    $data['file_name']=$uploaded_files['video']['info']['file_name'];
                    $data['file_type']=$uploaded_files['video']['info']['file_type'];
                }
                else
                {
                    $data['file_location']=$file_folder.'/'.$video_file['file_name'];
                    $data['file_name']=$video_file['file_name'];
                    $data['file_type']=$video_file['file_type'];
                }
                $data['budget_id']=$budget_id;
                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                $data['revision']=1;
                Query_helper::add($this->config->item('table_tm_fd_rep_details_picture'),$data);
            }
            else
            {
                $this->db->trans_rollback();
                if($type)
                {
                    $dlt=(FCPATH).$file_folder.'/'.$uploaded_files['video']['info']['file_name'];
                    unlink($dlt);
                }
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("Please Upload a Short Video File");
                $this->jsonReturn($ajax);
            }

            if ($this->db->trans_status() === TRUE)
            {
                $this->db->trans_commit();
                $this->message=$this->lang->line("MSG_SAVED_SUCCESS");
                $this->system_list();
            }
            else
            {
                $this->db->trans_rollback();
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                $this->jsonReturn($ajax);
            }
        }
    }

    private function system_details($id)
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            if(($this->input->post('id')))
            {
                $budget_id=$this->input->post('id');
            }
            else
            {
                $budget_id=$id;
            }

            $this->db->from($this->config->item('table_tm_fd_bud_info_details').' fdb_details');
            $this->db->select('fdb_details.*');
            $this->db->select('fdb.*');
            $this->db->select('v.name variety_name');
            $this->db->select('v1.name com_variety_name');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->select('type.name crop_type_name,type.id crop_type_id');
            $this->db->select('u.name upazilla_name');
            $this->db->select('d.name district_name,d.id district_id');
            $this->db->select('t.name territory_name,t.id territory_id');
            $this->db->select('zone.name zone_name,zone.id zone_id');
            $this->db->select('division.name division_name,division.id division_id');

            $this->db->join($this->config->item('table_tm_fd_bud_budget').' fdb','fdb.id = fdb_details.budget_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id = fdb_details.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->join($this->config->item('table_setup_location_upazillas').' u','u.id = fdb_details.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = u.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v1','v1.id = fdb_details.competitor_variety_id','LEFT');
            $this->db->where('fdb_details.budget_id',$budget_id);
            $this->db->where('fdb_details.revision',1);
            $data['item_info']=$this->db->get()->row_array();

            if($data['item_info']['status_reporting']=='Pending')
            {
                $ajax['status']=false;
                $ajax['system_message']='Report Not Forwarded Yet';
                $this->jsonReturn($ajax);
            }
            if(!$data['item_info'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$budget_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if(!$this->check_my_editable($data['item_info']))
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$budget_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

            $data['report_item']=Query_helper::get_info($this->config->item('table_tm_fd_bud_reporting'),'*',array('budget_id ='.$budget_id));
            $user_ids=array();
            if($data['item_info']['user_report_approved']>0)
            {
                $user_ids[$data['item_info']['user_report_approved']]=$data['item_info']['user_report_approved'];
            }
            $info_details=Query_helper::get_info($this->config->item('table_tm_fd_rep_details_info'),'*',array('budget_id='.$budget_id),0,0,array('id DESC','revision ASC'));
            $data['info_details']=array();;
            foreach($info_details as $info)
            {
                $data['info_details'][$info['revision']][]=$info;
                $user_ids[$info['user_created']]=$info['user_created'];
            }

            //get user info from login site
            $data['users_info']=System_helper::get_users_info($user_ids);

            $results=Query_helper::get_info($this->config->item('table_setup_fd_bud_expense_items'),array('id value','name text'),array(),0,0,array('ordering ASC'));
            $data['expense_items']=array();
            foreach($results as $result)
            {
                $data['expense_items'][$result['value']]=$result;
            }
            $data['expense_budget']=array();
            $results=Query_helper::get_info($this->config->item('table_tm_fd_bud_details_expense'),'*',array('budget_id ='.$budget_id,'revision=1'));
            foreach($results as $result)
            {
                $data['expense_budget'][$result['item_id']]=$result;
            }
            $expense_details=Query_helper::get_info($this->config->item('table_tm_fd_rep_details_expense'),'*',array('budget_id='.$budget_id),0,0,array('id ASC','revision ASC'));
            $data['expense_details']=array();
            foreach($expense_details as $expense)
            {
                $data['expense_details'][$expense['revision']][$expense['item_id']]=$expense;
            }

            $results=Query_helper::get_info($this->config->item('table_setup_fsetup_leading_farmer'),array('id value','name text','phone_no','status'),array('upazilla_id ='.$data['item_info']['upazilla_id']),0,0,array('ordering ASC'));
            $data['leading_farmers']=array();
            foreach($results as $result)
            {
                $data['leading_farmers'][$result['value']]=$result;
            }
            $data['participants']=array();
            $results=Query_helper::get_info($this->config->item('table_tm_fd_bud_details_participant'),'*',array('budget_id ='.$budget_id,'revision=1'));
            foreach($results as $res)
            {
                $data['participants'][$res['farmer_id']]=$res;
            }
            $participant_details=Query_helper::get_info($this->config->item('table_tm_fd_rep_details_participant'),'*',array('budget_id='.$budget_id),0,0,array('id ASC','revision ASC'));
            $data['participant_details']=array();
            foreach($participant_details as $participant)
            {
                $data['participant_details'][$participant['revision']][$participant['farmer_id']]=$participant;
            }

            $data['file_details']=array();
            $data['video_file_details']=array();

            $results=Query_helper::get_info($this->config->item('table_tm_fd_rep_details_picture'),'*',array('budget_id ='.$budget_id,'revision=1'));
            foreach($results as $result)
            {
                if(substr($result['file_type'],0,5)=='image')
                {
                    $data['file_details'][]=$result;
                }
                elseif(substr($result['file_type'],0,5)=='video')
                {
                    $data['video_file_details']=$result;
                }
            }

            $expense_files=Query_helper::get_info($this->config->item('table_tm_fd_rep_expense_picture'),'*',array('budget_id='.$budget_id),0,0,array('id ASC','revision ASC'));
            $data['expense_files']=array();
            foreach($expense_files as $expense)
            {
                $data['expense_files'][$expense['revision']][$expense['item_id']][]=$expense;
            }

            $data['title']='Completed Field Day Report';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$budget_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }

    private function check_validation($participants,$expense_report)
    {
        $expenses=Query_helper::get_info($this->config->item('table_setup_fd_bud_expense_items'),array('id value','name text','status'),array(),0,0,array('ordering ASC'));
        $farmers=Query_helper::get_info($this->config->item('table_setup_fsetup_leading_farmer'),array('id value','CONCAT(name," (",phone_no,")") text','status'),array(),0,0,array('ordering ASC'));
        $fmr=array();

        foreach($farmers as $farmer)
        {
            $fmr[$farmer['value']]=$farmer['text'];
        }

        $expense=array();
        foreach($expenses as $exp)
        {
            $expense[$exp['value']]=$exp['text'];
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('item[date]',$this->lang->line('LABEL_REPORTING_DATE'),'required');
        $this->form_validation->set_rules('item[date_of_fd]',$this->lang->line('LABEL_FIELD_DAY_DATE'),'required');
        $this->form_validation->set_rules('new_item[next_sales_target]',$this->lang->line('LABEL_NEXT_SALES_TARGET'),'required|numeric');
        $this->form_validation->set_rules('new_item[guest]',$this->lang->line('LABEL_GUEST'),'required|numeric');
        $this->form_validation->set_rules('new_item[participant_comment]',$this->lang->line('LABEL_PARTICIPANT_COMMENT'),'required');
        $this->form_validation->set_rules('new_item[participant_through_customer]',$this->lang->line('LABEL_PARTICIPANT_THROUGH_CUSTOMER'),'required');
        $this->form_validation->set_rules('new_item[participant_through_others]',$this->lang->line('LABEL_PARTICIPANT_THROUGH_OTHERS'),'required');
        $this->form_validation->set_rules('item[recommendation]',$this->lang->line('LABEL_RECOMMENDATION'),'required');
        if($expense_report)
        {
            foreach($expense_report as $index=>$exp)
            {
                if(!$exp)
                {
                    $this->form_validation->set_rules('expense_report['.$index.']',$expense[$index],'required');
                }
            }
        }
        if($participants)
        {
            foreach($participants as $index=>$id)
            {
                if(!$id)
                {
                    $this->form_validation->set_rules('farmers['.$index.']',$fmr[$index],'required');
                }
            }
        }
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }

    private function system_approve($id)
    {
        if(isset($this->permissions['edit'])&&($this->permissions['edit']==1))
        {
            if(($this->input->post('id')))
            {
                $budget_id=$this->input->post('id');
            }
            else
            {
                $budget_id=$id;
            }

            $this->db->from($this->config->item('table_tm_fd_bud_info_details').' fdb_details');
            $this->db->select('fdb_details.*');
            $this->db->select('fdb.*');
            $this->db->select('v.name variety_name');
            $this->db->select('v1.name com_variety_name');
            $this->db->select('crop.name crop_name,crop.id crop_id');
            $this->db->select('type.name crop_type_name,type.id crop_type_id');
            $this->db->select('u.name upazilla_name');
            $this->db->select('d.name district_name,d.id district_id');
            $this->db->select('t.name territory_name,t.id territory_id');
            $this->db->select('zone.name zone_name,zone.id zone_id');
            $this->db->select('division.name division_name,division.id division_id');

            $this->db->join($this->config->item('table_tm_fd_bud_budget').' fdb','fdb.id = fdb_details.budget_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id = fdb_details.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' type','type.id = v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id = type.crop_id','INNER');
            $this->db->join($this->config->item('table_setup_location_upazillas').' u','u.id = fdb_details.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = u.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v1','v1.id = fdb_details.competitor_variety_id','LEFT');
            $this->db->where('fdb_details.budget_id',$budget_id);
            $this->db->where('fdb_details.revision',1);
            $data['item_info']=$this->db->get()->row_array();

            if(!$data['item_info'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$budget_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if(!$this->check_my_editable($data['item_info']))
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$budget_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if($data['item_info']['status_reporting']==$this->config->item('system_status_po_request_pending'))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            if($data['item_info']['status_report_approved']==$this->config->item('system_status_po_request_approved'))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_FDR_APPROVED_UNABLE");
                $this->jsonReturn($ajax);
            }
            if($data['item_info']['status_report_approved']==$this->config->item('system_status_po_request_rejected'))
            {
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("MSG_FDR_REJECTED_UNABLE");
                $this->jsonReturn($ajax);
            }

            $data['report_item']=Query_helper::get_info($this->config->item('table_tm_fd_bud_reporting'),'*',array('budget_id ='.$budget_id));
            $user_ids=array();
            if($data['item_info']['user_report_approved']>0)
            {
                $user_ids[$data['item_info']['user_report_approved']]=$data['item_info']['user_report_approved'];
            }
            $info_details=Query_helper::get_info($this->config->item('table_tm_fd_rep_details_info'),'*',array('budget_id='.$budget_id),0,0,array('id DESC','revision ASC'));
            $data['info_details']=array();;
            foreach($info_details as $info)
            {
                $data['info_details'][$info['revision']][]=$info;
                $user_ids[$info['user_created']]=$info['user_created'];
            }

            //get user info from login site
            $data['users_info']=System_helper::get_users_info($user_ids);

            $results=Query_helper::get_info($this->config->item('table_setup_fd_bud_expense_items'),array('id value','name text'),array(),0,0,array('ordering ASC'));
            $data['expense_items']=array();
            foreach($results as $result)
            {
                $data['expense_items'][$result['value']]=$result;
            }
            $data['expense_budget']=array();
            $results=Query_helper::get_info($this->config->item('table_tm_fd_bud_details_expense'),'*',array('budget_id ='.$budget_id,'revision=1'));
            foreach($results as $result)
            {
                $data['expense_budget'][$result['item_id']]=$result;
            }
            $expense_details=Query_helper::get_info($this->config->item('table_tm_fd_rep_details_expense'),'*',array('budget_id='.$budget_id),0,0,array('id ASC','revision ASC'));
            $data['expense_details']=array();
            foreach($expense_details as $expense)
            {
                $data['expense_details'][$expense['revision']][$expense['item_id']]=$expense;
            }

            $results=Query_helper::get_info($this->config->item('table_setup_fsetup_leading_farmer'),array('id value','name text','phone_no','status'),array('upazilla_id ='.$data['item_info']['upazilla_id']),0,0,array('ordering ASC'));
            $data['leading_farmers']=array();
            foreach($results as $result)
            {
                $data['leading_farmers'][$result['value']]=$result;
            }
            $data['participants']=array();
            $results=Query_helper::get_info($this->config->item('table_tm_fd_bud_details_participant'),'*',array('budget_id ='.$budget_id,'revision=1'));
            foreach($results as $res)
            {
                $data['participants'][$res['farmer_id']]=$res;
            }
            $participant_details=Query_helper::get_info($this->config->item('table_tm_fd_rep_details_participant'),'*',array('budget_id='.$budget_id),0,0,array('id ASC','revision ASC'));
            $data['participant_details']=array();
            foreach($participant_details as $participant)
            {
                $data['participant_details'][$participant['revision']][$participant['farmer_id']]=$participant;
            }

            $data['file_details']=array();
            $data['video_file_details']=array();

            $results=Query_helper::get_info($this->config->item('table_tm_fd_rep_details_picture'),'*',array('budget_id ='.$budget_id,'revision=1'));
            foreach($results as $result)
            {
                if(substr($result['file_type'],0,5)=='image')
                {
                    $data['file_details'][]=$result;
                }
                elseif(substr($result['file_type'],0,5)=='video')
                {
                    $data['video_file_details']=$result;
                }
            }

            $expense_files=Query_helper::get_info($this->config->item('table_tm_fd_rep_expense_picture'),'*',array('budget_id='.$budget_id),0,0,array('id ASC','revision ASC'));
            $data['expense_files']=array();
            foreach($expense_files as $expense)
            {
                $data['expense_files'][$expense['revision']][$expense['item_id']][]=$expense;
            }

            $data['title']='Approve or Reject This Field Day Report';
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/approve",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/approve/'.$budget_id);
            $this->jsonReturn($ajax);
        }
        else
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
    }

    private function system_save_approval()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if($id>0)
        {
            $data=array();
            $result=Query_helper::get_info($this->config->item('table_tm_fd_bud_budget'),'*',array('id ='.$id));
            foreach($result as $res)
            {
                $data=$res;
            }
            if($data['status_reporting']!=$this->config->item('LABEL_FDR_FORWARDED'))
            {
                $ajax['status']=false;
                $ajax['system_message']='Report Not Forwarded Yet';
                $this->jsonReturn($ajax);
            }
            if($data['status_report_approved']==$this->config->item('system_status_po_approval_approved') || $data['status_report_approved']==$this->config->item('system_status_po_request_rejected'))
            {
                $this->message='Already '.$data['status_report_approved'];
                $ajax['status']=false;
                $ajax['system_message']=$this->message;
                $this->jsonReturn($ajax);
            }

            $this->db->from($this->config->item('table_tm_fd_bud_info_details').' fdb_details');
            $this->db->select('fdb_details.upazilla_id');
            $this->db->select('u.district_id');
            $this->db->select('d.territory_id');
            $this->db->select('t.zone_id zone_id');
            $this->db->select('zone.division_id division_id');

            $this->db->join($this->config->item('table_setup_location_upazillas').' u','u.id = fdb_details.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = u.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->where('fdb_details.budget_id',$id);
            //$this->db->where('fdb_details.id',$id);
            $this->db->where('fdb_details.revision',1);
            $data['item_info']=$this->db->get()->row_array();
            if(!$this->check_my_editable($data['item_info']))
            {
                System_helper::invalid_try($this->config->item('system_edit_others').' (budget_id)',$id); //budget_id will be shown in history hack table
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

        }
        if($id<=0)
        {
            $ajax['status']=false;
            $ajax['system_message']='Invalid Try';
            $this->jsonReturn($ajax);
        }
        if(!(isset($this->permissions['edit'])&&($this->permissions['edit']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
        if(!$this->check_validation_approval())
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->message;
            $this->jsonReturn($ajax);
        }
        if($this->permissions['report_approve']!=1)
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }
        else
        {
            $time=time();
            $data=$this->input->post('approve');
            $data['user_report_approved'] = $user->user_id;
            $data['date_report_approved'] = $time;
            $this->db->trans_start();  //DB Transaction Handle START

            Query_helper::update($this->config->item('table_tm_fd_bud_budget'),$data,array("id = ".$id));

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

    private function check_validation_approval()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('approve[remarks_report_approved]',$this->lang->line('LABEL_RECOMMENDATION'),'required');
        $this->form_validation->set_rules('approve[status_report_approved]',$this->lang->line('ACTION_APPROVE_REJECT'),'required');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        return true;
    }

} 