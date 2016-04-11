<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tm_popular_variety extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_popular_variety');
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
        $this->controller_url='tm_popular_variety';
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
            $data['title']="Farmer and Field Visit Setup List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_popular_variety/list",$data,true));
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
            $data['title']="New Popular Variety";
            $data["pv"] = Array(
                'id'=>0,
                'division_id'=>$this->locations['division_id'],
                'zone_id'=>$this->locations['zone_id'],
                'territory_id'=>$this->locations['territory_id'],
                'district_id'=>$this->locations['district_id'],
                'upazilla_id'=>$this->locations['upazilla_id'],
                'crop_id'=>'',
                'type_id'=>'',
                'variety_id'=>'',
                'other_variety_name'=>'',
                'name'=>'',
                'address' => '',
                'contact_no' => ''
            );
            $data['details']=array();
            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['upazillas']=array();
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
                            $data['upazillas']=Query_helper::get_info($this->config->item('table_setup_location_upazillas'),array('id value','name text'),array('district_id ='.$this->locations['district_id'],'status ="'.$this->config->item('system_status_active').'"'));
                        }
                    }
                }
            }
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['types']=array();
            $data['varieties']=array();
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_popular_variety/add_edit",$data,true));
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
                $setup_id=$this->input->post('id');
            }
            else
            {
                $setup_id=$id;
            }

            $this->db->from($this->config->item('table_tm_popular_variety').' tmpv');
            $this->db->select('tmpv.*');
            $this->db->select('upazilla.name upazilla_name');
            $this->db->select('d.name district_name,d.id district_id');
            $this->db->select('t.name territory_name,t.id territory_id');
            $this->db->select('zone.name zone_name,zone.id zone_id');
            $this->db->select('division.name division_name,division.id division_id');
            $this->db->select('crop_type.id type_id,crop_type.name crop_type_name');
            $this->db->select('crop.id crop_id,crop.name crop_name');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmpv.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

            $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =tmpv.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tmpv.variety_id','LEFT');

            $this->db->where('tmpv.id',$setup_id);
            $this->db->where('tmpv.status','Active');
            $data['pv']=$this->db->get()->row_array();


            if(!$data['pv'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

            if(!$this->check_my_editable($data['pv']))
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }


            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=Query_helper::get_info($this->config->item('table_setup_location_zones'),array('id value','name text'),array('division_id ='.$data['pv']['division_id']));
            $data['territories']=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$data['pv']['zone_id']));
            $data['districts']=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$data['pv']['territory_id']));

            $data['upazillas']=Query_helper::get_info($this->config->item('table_setup_location_upazillas'),array('id value','name text'),array('district_id ='.$data['pv']['district_id'],'status ="'.$this->config->item('system_status_active').'"'));

            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['types']=Query_helper::get_info($this->config->item('table_setup_classification_crop_types'),array('id value','name text'),array('crop_id ='.$data['pv']['crop_id'],'status ="'.$this->config->item('system_status_active').'"'));
            $data['varieties']=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),array('id value','name text'),array('crop_type_id ='.$data['pv']['type_id'],'status ="'.$this->config->item('system_status_active').'"'));

            $data['details']=Query_helper::get_info($this->config->item('table_tm_popular_variety_details'),'*',array('setup_id ='.$setup_id,'status ="'.$this->config->item('system_status_active').'"'));

            $data['title']="Edit Popular Variety";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_popular_variety/add_edit",$data,true));
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

            $this->db->from($this->config->item('table_tm_popular_variety').' tmpv');
            $this->db->select('tmpv.*');
            $this->db->select('upazilla.name upazilla_name');
            $this->db->select('d.name district_name,d.id district_id');
            $this->db->select('t.name territory_name,t.id territory_id');
            $this->db->select('zone.name zone_name,zone.id zone_id');
            $this->db->select('division.name division_name,division.id division_id');
            $this->db->select('crop_type.id type_id,crop_type.name crop_type_name');
            $this->db->select('crop.id crop_id,crop.name crop_name');
            $this->db->select('v.name variety_name');
            $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmpv.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

            $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =tmpv.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tmpv.variety_id','LEFT');

            $this->db->where('tmpv.id',$setup_id);
            $this->db->where('tmpv.status','Active');
            $data['pv']=$this->db->get()->row_array();


            if(!$data['pv'])
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

            if(!$this->check_my_editable($data['pv']))
            {
                System_helper::invalid_try($this->config->item('system_edit_others'),$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $data['details']=Query_helper::get_info($this->config->item('table_tm_popular_variety_details'),'*',array('setup_id ='.$setup_id,'status ="'.$this->config->item('system_status_active').'"'));
            $data['title']="Detail of Popular Variety";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_popular_variety/details",$data,true));
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
                Query_helper::update($this->config->item('table_tm_popular_variety'),array('status'=>$this->config->item('system_status_delete'),'user_updated'=>$user->user_id,'date_updated'=>$time),array("id = ".$id));
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
        $time=time();
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
            $data=$this->input->post('pv');
            $this->db->trans_start();  //DB Transaction Handle START
            if($id>0)
            {
                $setup_id=$id;
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = $time;

                Query_helper::update($this->config->item('table_tm_popular_variety'),$data,array("id = ".$setup_id));

            }
            else
            {

                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                $setup_id=Query_helper::add($this->config->item('table_tm_popular_variety'),$data);
                if($setup_id===false)
                {
                    $this->db->trans_complete();
                    $ajax['status']=false;
                    $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                    $this->jsonReturn($ajax);
                    die();
                }
            }
            $file_folder='images/popular_variety/'.$setup_id;
            $dir=(FCPATH).$file_folder;
            if(!is_dir($dir))
            {
                mkdir($dir, 0777);
            }
            $uploaded_files = System_helper::upload_file($file_folder);

            foreach($uploaded_files as $file)
            {
                if(!$file['status'])
                {
                    $this->db->trans_rollback();
                    $this->db->trans_complete();
                    $ajax['status']=false;
                    $ajax['system_message']=$file['message'];
                    $this->jsonReturn($ajax);
                    die();
                }
            }

            $final_details=array();

            $details=$this->input->post('details');
            foreach($details as $i=>$detail)
            {
                $data=array();
                $data['id']=0;
                $data['setup_id']=$setup_id;
                $data['date_remarks']=System_helper::get_time($detail['date_remarks']);
                $data['remarks']=$detail['remarks'];
                if(isset($uploaded_files['image_'.$i]))
                {
                    $data['picture_url']=base_url().$file_folder.'/'.$uploaded_files['image_'.$i]['info']['file_name'];
                    $data['picture_file_full']=$file_folder.'/'.$uploaded_files['image_'.$i]['info']['file_name'];
                    $data['picture_file_name']=$uploaded_files['image_'.$i]['info']['file_name'];
                }
                elseif(isset($detail['old_picture']))
                {
                    $data['picture_url']=base_url().$file_folder.'/'.$detail['old_picture'];
                    $data['picture_file_full']=$file_folder.'/'.$detail['old_picture'];
                    $data['picture_file_name']=$detail['old_picture'];
                }
                $data['user_created'] = $user->user_id;
                $data['date_created'] = $time;
                $final_details[]=$data;

            }
            $old_details=Query_helper::get_info($this->config->item('table_tm_popular_variety_details'),'*',array('setup_id ='.$setup_id,'status ="'.$this->config->item('system_status_active').'"'));

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
            }
            foreach($final_details as $detail)
            {
                $detail_id=$detail['id'];
                unset($detail['id']);
                if($detail_id>0)
                {
                    Query_helper::update($this->config->item('table_tm_popular_variety_details'),$detail,array("id = ".$detail_id));
                }
                else
                {
                    Query_helper::add($this->config->item('table_tm_popular_variety_details'),$detail);
                }
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
    private function check_validation()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('pv[upazilla_id]',$this->lang->line('LABEL_UPAZILLA_NAME'),'required|numeric');
        $this->form_validation->set_rules('pv[name]',"Farmer's Name",'required');


        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        $pv=$this->input->post('pv');
        if(!(($pv['variety_id']>0)||(strlen($pv['other_variety_name'])>0)))
        {
            $this->message="Select a Variety or fill Other Variety";
            return false;
        }
        $details=$this->input->post('details');
        if(sizeof($details)>0)
        {
            foreach($details as $detail)
            {
                if(!(strlen($detail['date_remarks'])>0))
                {
                    $this->message="Please Fill Date";
                    return false;
                }
                if(!(strlen($detail['remarks'])>0))
                {
                    $this->message="Please Fill remarks";
                    return false;
                }
            }
        }
        else
        {
            $this->message="Minimum One Information Required";
            return false;
        }
        $id=$this->input->post('id');
        if($id>0)
        {
            $this->db->from($this->config->item('table_tm_popular_variety').' tmpv');
            $this->db->select('upazilla.name upazilla_name,upazilla.id upazilla_id');
            $this->db->select('d.name district_name,d.id district_id');
            $this->db->select('t.name territory_name,t.id territory_id');
            $this->db->select('zone.name zone_name,zone.id zone_id');
            $this->db->select('division.name division_name,division.id division_id');
            $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmpv.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->where('tmpv.id',$id);
            $result=$this->db->get()->row_array();
            if(!$result)
            {
                System_helper::invalid_try($this->config->item('system_save'),$id,'Hack trying to edit an id that does not exits');
                $this->message="Invalid Try";
                return false;
            }
            if(!$this->check_my_editable($result))
            {
                System_helper::invalid_try($this->config->item('system_save'),$id,'Hack To edit other customer that does not in my area');
                $this->message="Invalid Try";
                return false;
            }
        }
        {

            $upazilla_id=$pv['upazilla_id'];
            $variety_id=$pv['variety_id'];
            $crop_type_id=$pv['crop_type_id'];
            $other_variety_name=$pv['other_variety_name'];
            if($variety_id>0)
            {
                $this->db->from($this->config->item('table_tm_popular_variety').' tmpv');
                $this->db->where('upazilla_id',$upazilla_id);
                $this->db->where('variety_id',$variety_id);
                $this->db->where('tmpv.id !=',$id);
                $result=$this->db->get()->row_array();
            }
            else
            {
                $this->db->from($this->config->item('table_tm_popular_variety').' tmpv');
                $this->db->where('upazilla_id',$upazilla_id);
                $this->db->where('crop_type_id',$crop_type_id);
                $this->db->where('other_variety_name',$other_variety_name);
                $this->db->where('tmpv.id !=',$id);
                $result=$this->db->get()->row_array();
            }
            if($result)
            {
                $this->message="This setup already Exits.Please edit that";
                return false;
            }

        }
        return true;
    }
    public function get_items()
    {
        //$this->db->from($this->config->item('table_csetup_other_customers').' cus');
        $this->db->from($this->config->item('table_tm_popular_variety').' tmpv');
        $this->db->select('tmpv.*');
        $this->db->select('upazilla.name upazilla_name');
        $this->db->select('d.name district_name');
        $this->db->select('t.name territory_name');
        $this->db->select('zone.name zone_name');
        $this->db->select('division.name division_name');
        $this->db->select('crop.name crop_name');
        $this->db->select('crop_type.name crop_type_name');
        $this->db->select('v.name variety_name');
        $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmpv.upazilla_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =tmpv.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tmpv.variety_id','LEFT');
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
        $this->db->where('tmpv.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('tmpv.id','DESC');

        $items=$this->db->get()->result_array();
        $this->jsonReturn($items);
    }

}
