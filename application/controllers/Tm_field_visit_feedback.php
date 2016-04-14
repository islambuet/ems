<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tm_field_visit_feedback extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_field_visit_feedback');
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
        $this->controller_url='tm_field_visit_feedback';
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
            $data['title']="Field Visit Feedback List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_field_visit_feedback/list",$data,true));
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
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            if(($this->input->post('id')))
            {
                $setup_id=$this->input->post('id');
            }
            else
            {
                $setup_id=$id;
            }
            $this->db->from($this->config->item('table_tm_farmers').' tmf');
            $this->db->select('tmf.*');
            $this->db->select('upazilla.name upazilla_name');
            $this->db->select('d.name district_name,d.id district_id');
            $this->db->select('t.name territory_name,t.id territory_id');
            $this->db->select('zone.name zone_name,zone.id zone_id');
            $this->db->select('division.name division_name,division.id division_id');
            $this->db->select('crop.name crop_name');
            $this->db->select('crop_type.name crop_type_name');
            $this->db->select('v.name variety_name');
            $this->db->select('season.name season_name');
            $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tmf.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
            $this->db->join($this->config->item('table_setup_tm_seasons').' season','season.id =tmf.season_id','INNER');
            $this->db->where('tmf.id',$setup_id);
            $this->db->where('tmf.status','Active');
            $data['fsetup']=$this->db->get()->row_array();
            if(!$data['fsetup'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

            if(!$this->check_my_editable($data['fsetup']))
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $user_ids=array();

            $user_ids[$data['fsetup']['user_created']]=$data['fsetup']['user_created'];
            $data['visits_picture']=array();
            $visits=Query_helper::get_info($this->config->item('table_tm_visits_picture'),'*',array('setup_id ='.$setup_id));
            foreach($visits as $visit)
            {
                $data['visits_picture'][$visit['day_no']]=$visit;
                $user_ids[$visit['user_created']]=$visit['user_created'];
                if($visit['user_feedback'])
                {
                    $user_ids[$visit['user_feedback']]=$visit['user_feedback'];
                }
            }
            $data['fruits_picture_headers']=Query_helper::get_info($this->config->item('table_setup_tm_fruit_picture'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            $data['fruits_picture']=array();
            $visits=Query_helper::get_info($this->config->item('table_tm_visits_fruit_picture'),'*',array('setup_id ='.$setup_id));
            foreach($visits as $visit)
            {
                $data['fruits_picture'][$visit['picture_id']]=$visit;
                $user_ids[$visit['user_created']]=$visit['user_created'];
                if($visit['user_feedback'])
                {
                    $user_ids[$visit['user_feedback']]=$visit['user_feedback'];
                }
            }
            $data['disease_picture']=Query_helper::get_info($this->config->item('table_tm_visits_disease_picture'),'*',array('setup_id ='.$setup_id,'status ="'.$this->config->item('system_status_active').'"'));
            foreach($data['disease_picture'] as $visit)
            {
                $user_ids[$visit['user_created']]=$visit['user_created'];
                if($visit['user_feedback'])
                {
                    $user_ids[$visit['user_feedback']]=$visit['user_feedback'];
                }
            }
            $data['users']=System_helper::get_users_info($user_ids);

            $data['title']="Edit of Field Visit Feedback";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_field_visit_feedback/add_edit",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/edit/'.$setup_id);
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
                $setup_id=$this->input->post('id');
            }
            else
            {
                $setup_id=$id;
            }
            $this->db->from($this->config->item('table_tm_farmers').' tmf');
            $this->db->select('tmf.*');
            $this->db->select('upazilla.name upazilla_name');
            $this->db->select('d.name district_name,d.id district_id');
            $this->db->select('t.name territory_name,t.id territory_id');
            $this->db->select('zone.name zone_name,zone.id zone_id');
            $this->db->select('division.name division_name,division.id division_id');
            $this->db->select('crop.name crop_name');
            $this->db->select('crop_type.name crop_type_name');
            $this->db->select('v.name variety_name');
            $this->db->select('season.name season_name');
            $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tmf.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
            $this->db->join($this->config->item('table_setup_tm_seasons').' season','season.id =tmf.season_id','INNER');
            $this->db->where('tmf.id',$setup_id);
            $this->db->where('tmf.status','Active');
            $data['fsetup']=$this->db->get()->row_array();
            if(!$data['fsetup'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

            if(!$this->check_my_editable($data['fsetup']))
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $user_ids=array();

            $user_ids[$data['fsetup']['user_created']]=$data['fsetup']['user_created'];
            $data['visits_picture']=array();
            $visits=Query_helper::get_info($this->config->item('table_tm_visits_picture'),'*',array('setup_id ='.$setup_id));
            foreach($visits as $visit)
            {
                $data['visits_picture'][$visit['day_no']]=$visit;
                $user_ids[$visit['user_created']]=$visit['user_created'];
                if($visit['user_feedback'])
                {
                    $user_ids[$visit['user_feedback']]=$visit['user_feedback'];
                }
            }
            $data['fruits_picture_headers']=Query_helper::get_info($this->config->item('table_setup_tm_fruit_picture'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            $data['fruits_picture']=array();
            $visits=Query_helper::get_info($this->config->item('table_tm_visits_fruit_picture'),'*',array('setup_id ='.$setup_id));
            foreach($visits as $visit)
            {
                $data['fruits_picture'][$visit['picture_id']]=$visit;
                $user_ids[$visit['user_created']]=$visit['user_created'];
                if($visit['user_feedback'])
                {
                    $user_ids[$visit['user_feedback']]=$visit['user_feedback'];
                }
            }
            $data['disease_picture']=Query_helper::get_info($this->config->item('table_tm_visits_disease_picture'),'*',array('setup_id ='.$setup_id,'status ="'.$this->config->item('system_status_active').'"'));
            foreach($data['disease_picture'] as $visit)
            {
                $user_ids[$visit['user_created']]=$visit['user_created'];
                if($visit['user_feedback'])
                {
                    $user_ids[$visit['user_feedback']]=$visit['user_feedback'];
                }
            }
            $data['users']=System_helper::get_users_info($user_ids);

            $data['title']="Details of Field Visit and Feedback";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_field_visit_feedback/details",$data,true));
            if($this->message)
            {
                $ajax['system_message']=$this->message;
            }
            $ajax['system_page_url']=site_url($this->controller_url.'/index/details/'.$setup_id);
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
        $setup_id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        if(!((isset($this->permissions['edit'])&&($this->permissions['edit']==1))||(isset($this->permissions['add'])&&($this->permissions['add']==1))))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
            die();
        }
        $this->db->from($this->config->item('table_tm_farmers').' tmf');
        $this->db->select('tmf.*');
        $this->db->select('upazilla.name upazilla_name');
        $this->db->select('d.name district_name,d.id district_id');
        $this->db->select('t.name territory_name,t.id territory_id');
        $this->db->select('zone.name zone_name,zone.id zone_id');
        $this->db->select('division.name division_name,division.id division_id');
        $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        $this->db->where('tmf.id',$setup_id);
        $this->db->where('tmf.status','Active');
        $fsetup=$this->db->get()->row_array();
        if(!$fsetup)
        {

            System_helper::invalid_try('Save non-existing',$setup_id);
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }

        if(!$this->check_my_editable($fsetup))
        {
            System_helper::invalid_try('save not my area',$setup_id);
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }




        /*$final_details=array();

        $details=$this->input->post('disease');
        if(sizeof($details)>0)
        {
            foreach($details as $i=>$detail)
            {
                $data=array();
                $data['id']=0;
                $data['setup_id']=$setup_id;
                $data['remarks']=$detail['remarks'];
                if(isset($uploaded_files['disease_image_'.$i]))
                {
                    $data['picture_url']=base_url().$file_folder.'/'.$uploaded_files['disease_image_'.$i]['info']['file_name'];
                    $data['picture_file_full']=$file_folder.'/'.$uploaded_files['disease_image_'.$i]['info']['file_name'];
                    $data['picture_file_name']=$uploaded_files['disease_image_'.$i]['info']['file_name'];
                }
                elseif(isset($detail['old_disease_picture']))
                {
                    $data['picture_url']=base_url().$file_folder.'/'.$detail['old_disease_picture'];
                    $data['picture_file_full']=$file_folder.'/'.$detail['old_disease_picture'];
                    $data['picture_file_name']=$detail['old_disease_picture'];
                }
                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                $final_details[]=$data;

            }
        }

        $old_details=Query_helper::get_info($this->config->item('table_tm_visits_disease_picture'),'*',array('setup_id ='.$setup_id,'status ="'.$this->config->item('system_status_active').'"'));

        foreach($old_details as $i=>$detail)
        {
            if(isset($final_details[$i]))
            {
                $final_details[$i]['id']=$detail['id'];
                $final_details[$i]['user_created']=$detail['user_created'];
                $final_details[$i]['date_created']=$detail['date_created'];
                $final_details[$i]['user_updated'] = $user->user_id;
                $final_details[$i]['date_updated'] = $time;
            }
            else
            {
                $detail['status']=$this->config->item('system_status_delete');
                $detail['user_updated'] = $user->user_id;
                $detail['date_updated'] = $time;
                $final_details[]=$detail;
            }
        }*/

        $this->db->trans_start();
        $visits_picture=array();
        $infos=Query_helper::get_info($this->config->item('table_tm_visits_picture'),'*',array('setup_id ='.$setup_id));
        foreach($infos as $info)
        {
            $visits_picture[$info['day_no']]=$info;
        }
        $visit_feedback=$this->input->post('visit_feedback');
        if(sizeof($visit_feedback)>0)
        {
            foreach($visit_feedback as $i=>$feedback)
            {
                $data=array();
                if($feedback)
                {
                    if(isset($visits_picture[$i])&& ($visits_picture[$i]['feedback']!=$feedback))
                    {
                        $data['feedback']=$feedback;
                        $data['user_updated'] = $user->user_id;
                        $data['date_updated'] = $time;
                        $data['user_feedback'] = $user->user_id;
                        $data['date_feedback'] = $time;
                        Query_helper::update($this->config->item('table_tm_visits_picture'),$data,array("id = ".$visits_picture[$i]['id']));
                    }
                }
            }
        }
        $fruits_picture=array();
        $infos=Query_helper::get_info($this->config->item('table_tm_visits_fruit_picture'),'*',array('setup_id ='.$setup_id));
        foreach($infos as $info)
        {
            $fruits_picture[$info['picture_id']]=$info;
        }
        $fruit_feedback=$this->input->post('fruit_feedback');
        if(sizeof($fruit_feedback)>0)
        {
            foreach($fruit_feedback as $i=>$feedback)
            {
                $data=array();
                if($feedback)
                {
                    if(isset($fruits_picture[$i])&& ($fruits_picture[$i]['feedback']!=$feedback))
                    {
                        $data['feedback']=$feedback;
                        $data['user_updated'] = $user->user_id;
                        $data['date_updated'] = $time;
                        $data['user_feedback'] = $user->user_id;
                        $data['date_feedback'] = $time;
                        Query_helper::update($this->config->item('table_tm_visits_fruit_picture'),$data,array("id = ".$fruits_picture[$i]['id']));
                    }
                }
            }
        }
        $disease_picture=array();
        $infos=Query_helper::get_info($this->config->item('table_tm_visits_disease_picture'),'*',array('setup_id ='.$setup_id,'status ="'.$this->config->item('system_status_active').'"'));
        foreach($infos as $info)
        {
            $disease_picture[$info['id']]=$info;
        }
        $disease_feedback=$this->input->post('disease_feedback');
        if(sizeof($disease_feedback)>0)
        {
            foreach($disease_feedback as $i=>$feedback)
            {
                $data=array();
                if($feedback)
                {
                    if(isset($disease_picture[$i])&& ($disease_picture[$i]['feedback']!=$feedback))
                    {
                        $data['feedback']=$feedback;
                        $data['user_updated'] = $user->user_id;
                        $data['date_updated'] = $time;
                        $data['user_feedback'] = $user->user_id;
                        $data['date_feedback'] = $time;
                        Query_helper::update($this->config->item('table_tm_visits_disease_picture'),$data,array("id = ".$disease_picture[$i]['id']));
                    }
                }
            }
        }



        /*foreach($final_details as $detail)
        {
            $detail_id=$detail['id'];
            unset($detail['id']);
            if($detail_id>0)
            {
                Query_helper::update($this->config->item('table_tm_visits_disease_picture'),$detail,array("id = ".$detail_id));
            }
            else
            {
                Query_helper::add($this->config->item('table_tm_visits_disease_picture'),$detail);
            }
        }*/
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
    private function system_save1()
    {


        $setup_id = $this->input->post("id");
        $user = User_helper::get_user();
        $time=time();
        if(!((isset($this->permissions['edit'])&&($this->permissions['edit']==1))||(isset($this->permissions['add'])&&($this->permissions['add']==1))))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
            die();
        }
        $this->db->from($this->config->item('table_tm_farmers').' tmf');
        $this->db->select('tmf.*');
        $this->db->select('upazilla.name upazilla_name');
        $this->db->select('d.name district_name,d.id district_id');
        $this->db->select('t.name territory_name,t.id territory_id');
        $this->db->select('zone.name zone_name,zone.id zone_id');
        $this->db->select('division.name division_name,division.id division_id');
        $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        $this->db->where('tmf.id',$setup_id);
        $this->db->where('tmf.status','Active');
        $fsetup=$this->db->get()->row_array();
        if(!$fsetup)
        {

            System_helper::invalid_try('Save non-existing',$setup_id);
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }

        if(!$this->check_my_editable($fsetup))
        {
            System_helper::invalid_try('save not my area',$setup_id);
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
        }

        $visits=array();
        $infos=Query_helper::get_info($this->config->item('table_tm_visits'),'*',array('setup_id ='.$setup_id));
        foreach($infos as $info)
        {
            $visits[$info['day_no']]=$info;
        }
        $feedbacks=$this->input->post('feedback');

        $this->db->trans_start();
        for($i=1;$i<=$fsetup['num_picture'];$i++)
        {
            $data=array();
            if(isset($feedbacks[$i]) && (strlen($feedbacks[$i])>0))
            {
                $data['feedback']=$feedbacks[$i];
            }
            if($data)
            {
                if(isset($visits[$i]))
                {
                    $data['user_updated'] = $user->user_id;
                    $data['date_updated'] = $time;
                    $data['user_feedback'] = $user->user_id;
                    $data['date_feedback'] = $time;
                    Query_helper::update($this->config->item('table_tm_visits'),$data,array("id = ".$visits[$i]['id']));
                }
                else
                {
                    $data['setup_id'] = $setup_id;
                    $data['day_no'] = $i;
                    $data['user_created'] = $user->user_id;
                    $data['date_created'] = $time;
                    $data['user_feedback'] = $user->user_id;
                    $data['date_feedback'] = $time;
                    Query_helper::add($this->config->item('table_tm_visits'),$data);
                }
            }
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

        if(($this->locations['upazilla_id']>0)&&($this->locations['upazilla_id']!=$customer['upazilla_id']))
        {
            return false;
        }

        return true;
    }
    public function get_items()
    {
        //$this->db->from($this->config->item('table_csetup_other_customers').' cus');
        $this->db->from($this->config->item('table_tm_farmers').' tmf');
        $this->db->select('tmf.*');
        $this->db->select('upazilla.name upazilla_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->select('crop.name crop_name');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->select('v.name variety_name');
        $this->db->select('count(distinct case when vp.remarks IS NOT NULL or vp.picture_url IS NOT NULL then vp.id end) num_visit_done',true);
        $this->db->select('count(distinct case when vp.feedback IS NOT NULL then vp.id end) num_visit_done_feedback',true);
        $this->db->select('count(distinct case when vfp.remarks IS NOT NULL or vfp.picture_url IS NOT NULL then vfp.id end) num_fruit_picture',false);
        $this->db->select('count(distinct case when vfp.feedback IS NOT NULL then vfp.id end) num_fruit_picture_feedback',false);
        $this->db->select('count(distinct case when vdp.status="Active" then vdp.id end) num_disease_picture',false);
        $this->db->select('count(distinct case when vdp.status="Active" and vdp.feedback IS NOT NULL then vdp.id end) num_disease_picture_feedback',false);
        $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tmf.variety_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
        $this->db->join($this->config->item('table_tm_visits_picture').' vp','tmf.id =vp.setup_id','LEFT');
        $this->db->join($this->config->item('table_tm_visits_fruit_picture').' vfp','tmf.id =vfp.setup_id','LEFT');
        $this->db->join($this->config->item('table_tm_visits_disease_picture').' vdp','tmf.id =vdp.setup_id','LEFT');
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
                            $this->db->where('upazilla.id',$this->locations['upazilla_id']);
                        }
                    }
                }
            }
        }
        $this->db->where('tmf.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('tmf.id','DESC');
        $this->db->group_by('tmf.id');
        $items=$this->db->get()->result_array();
        //echo $this->db->last_query();
        foreach($items as &$item)
        {
            $item['date_sowing']=System_helper::display_date($item['date_sowing']);
        }

        $this->jsonReturn($items);
    }

}
