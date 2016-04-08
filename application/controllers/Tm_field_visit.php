<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tm_field_visit extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_field_visit');
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
        $this->controller_url='tm_field_visit';
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
            $data['title']="Field Visit List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_field_visit/list",$data,true));
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

        if((isset($this->permissions['add'])&&($this->permissions['add']==1))||(isset($this->permissions['edit'])&&($this->permissions['edit']==1)))
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
            $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tmf.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
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
            $data['visits']=array();
            $visits=Query_helper::get_info($this->config->item('table_tm_visits'),'*',array('setup_id ='.$setup_id));
            foreach($visits as $visit)
            {
                $data['visits'][$visit['day_no']]=$visit;
            }

            $data['title']="Edit Field Visit";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_field_visit/add_edit",$data,true));
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
            $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
            $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
            $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
            $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
            $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tmf.variety_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
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
            $data['visits']=array();
            $visits=Query_helper::get_info($this->config->item('table_tm_visits'),'*',array('setup_id ='.$setup_id));
            foreach($visits as $visit)
            {
                $data['visits'][$visit['day_no']]=$visit;
            }

            $data['title']="Edit Field Visit";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_field_visit/details",$data,true));
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
        $file_folder='images/field_visit/'.$setup_id;
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
                $ajax['status']=false;
                $ajax['system_message']=$file['message'];
                $this->jsonReturn($ajax);
                die();
            }
        }
        $visits=array();
        $infos=Query_helper::get_info($this->config->item('table_tm_visits'),'*',array('setup_id ='.$setup_id));
        foreach($infos as $info)
        {
            $visits[$info['day_no']]=$info;
        }
        $remarks=$this->input->post('remarks');

        $this->db->trans_start();
        for($i=1;$i<=$fsetup['num_picture'];$i++)
        {
            $data=array();
            if(isset($remarks[$i]) && (strlen($remarks[$i])>0))
            {
                $data['remarks']=$remarks[$i];
            }
            if(isset($uploaded_files['image_'.$i]))
            {
                $data['picture']=$file_folder.'/'.$uploaded_files['image_'.$i]['info']['file_name'];
                $data['picture_file_name']=$uploaded_files['image_'.$i]['info']['file_name'];
            }
            if($data)
            {
                if(isset($visits[$i]))
                {
                    $data['user_updated'] = $user->user_id;
                    $data['date_updated'] = $time;
                    Query_helper::update($this->config->item('table_tm_visits'),$data,array("id = ".$visits[$i]['id']));
                }
                else
                {
                    $data['setup_id'] = $setup_id;
                    $data['day_no'] = $i;
                    $data['user_created'] = $user->user_id;
                    $data['date_created'] = $time;
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
        $this->db->select('COUNT(visits.setup_id) num_visit');
        $this->db->join($this->config->item('table_setup_location_upazillas').' upazilla','upazilla.id = tmf.upazilla_id','INNER');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = upazilla.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        $this->db->join($this->config->item('table_setup_location_divisions').' division','division.id = zone.division_id','INNER');

        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tmf.variety_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
        $this->db->join($this->config->item('table_tm_visits').' visits','tmf.id =visits.setup_id','LEFT');
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
        foreach($items as &$item)
        {
            $item['date_sowing']=System_helper::display_date($item['date_sowing']);
        }

        $this->jsonReturn($items);
    }

}
