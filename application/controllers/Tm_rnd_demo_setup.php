<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tm_rnd_demo_setup extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_rnd_demo_setup');
        $this->controller_url='tm_rnd_demo_setup';
    }

    public function index($action="list",$id=0)
    {
        if($action=="list")
        {
            $this->system_list($id);
        }
        elseif($action=="get_items")
        {
            $this->get_items();
        }
        elseif($action=="list_variety")
        {
            $this->system_list_variety();
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
        elseif($action=="save_unfilled")
        {
            $this->system_save_unfilled();
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
            $data['title']="R&D Demo Variety Setup List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_rnd_demo_setup/list",$data,true));
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
    private function get_items()
    {
        //$this->db->from($this->config->item('table_csetup_other_customers').' cus');
        $this->db->from($this->config->item('table_tm_rnd_demo_setup').' tmf');
        $this->db->select('tmf.*');
        $this->db->select('season.name season_name');

        $this->db->select('crop.name crop_name');
        $this->db->select('crop_type.name type_name');
        $this->db->join($this->config->item('table_tm_rnd_demo_varieties').' tfv','tfv.setup_id =tmf.id','INNER');
        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tfv.variety_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
        $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');

        $this->db->join($this->config->item('table_setup_tm_seasons').' season','season.id =tmf.season_id','INNER');
        $this->db->where('tmf.status !=',$this->config->item('system_status_delete'));
        $this->db->where('tfv.status !=',$this->config->item('system_status_delete'));
        $this->db->order_by('tmf.id','DESC');
        $this->db->group_by('tmf.id');

        $items=$this->db->get()->result_array();
        foreach($items as &$item)
        {
            $item['date_sowing']=System_helper::display_date($item['date_sowing']);
        }

        $this->jsonReturn($items);
    }
    private function system_list_variety()
    {
        $crop_type_id=$this->input->post('crop_type_id');

        //ARM
        $this->db->from($this->config->item('table_setup_classification_varieties').' v');
        $this->db->select('v.id variety_id,v.name variety_name,v.whose');
        $this->db->where('v.crop_type_id',$crop_type_id);
        $this->db->where('v.status',$this->config->item('system_status_active'));
        $this->db->order_by('v.whose','ASC');
        $this->db->order_by('v.ordering','ASC');
        $data['varieties']=$this->db->get()->result_array();
        $ajax['status']=true;
        $ajax['system_content'][]=array("id"=>"#variety_list_container","html"=>$this->load->view("tm_rnd_demo_setup/list_variety",$data,true));

        if($this->message)
        {
            $ajax['system_message']=$this->message;
        }
        $this->jsonReturn($ajax);
    }
    private function system_add()
    {
        if(isset($this->permissions['add'])&&($this->permissions['add']==1))
        {
            $data['title']="New R&D Demo Variety Setup";
            $data["fsetup"] = Array(
                'id'=>0,
                'year' => date('Y'),
                'season_id' => '',
                'crop_id'=>'',
                'type_id'=>'',
                'name'=>'',
                'address' => '',
                'contact_no' => '',
                'date_sowing' => time(),
                'date_transplant' => '',
                'num_visits' => 1,
                'interval' => 2

            );
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['types']=array();
            $data['varieties']=array();
            $data['seasons']=Query_helper::get_info($this->config->item('table_setup_tm_seasons'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $ajax['system_page_url']=site_url($this->controller_url."/index/add");

            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_rnd_demo_setup/add_edit",$data,true));
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

        if((isset($this->permissions['edit'])&&($this->permissions['edit']==1))||(isset($this->permissions['add'])&&($this->permissions['add']==1)))
        {
            if(($this->input->post('id')))
            {
                $setup_id=$this->input->post('id');
            }
            else
            {
                $setup_id=$id;
            }
            $data['previous_varieties']=array();//active and inactive
            $results=Query_helper::get_info($this->config->item('table_tm_rnd_demo_varieties'),'*',array('setup_id ='.$setup_id,'status ="'.$this->config->item('system_status_active').'"'));
            if(!$results)
            {
                System_helper::invalid_try($this->config->item('system_edit_not_exists'),$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $variety_id=0;
            foreach($results as $i=>$result)
            {
                if($i==0)
                {
                    $variety_id=$result['variety_id'];
                }
                $data['previous_varieties'][$result['variety_id']]=$result;
            }

            $this->db->from($this->config->item('table_tm_rnd_demo_setup').' tmf');
            $this->db->select('tmf.*');

            $this->db->select('crop_type.id type_id,crop_type.name crop_type_name');
            $this->db->select('crop.id crop_id,crop.name crop_name');
            $this->db->select('v.name variety_name');//invalid
            $this->db->select('season.name season_name');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id ='.$variety_id,'INNER');
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
            $data['crops']=Query_helper::get_info($this->config->item('table_setup_classification_crops'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['types']=Query_helper::get_info($this->config->item('table_setup_classification_crop_types'),array('id value','name text'),array('crop_id ='.$data['fsetup']['crop_id'],'status ="'.$this->config->item('system_status_active').'"'));
            $data['varieties']=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),array('id value','name text','whose'),array('crop_type_id ='.$data['fsetup']['type_id'],'status ="'.$this->config->item('system_status_active').'"'),0,0,array('whose ASC','ordering ASC'));

            $data['seasons']=Query_helper::get_info($this->config->item('table_setup_tm_seasons'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));

            $data['title']="Edit R&N Demo Variety Setup";
            $ajax['status']=true;
            if(isset($this->permissions['edit'])&&($this->permissions['edit']==1))
            {
                $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_rnd_demo_setup/add_edit",$data,true));
            }
            elseif(isset($this->permissions['add'])&&($this->permissions['add']==1))
            {
                $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_rnd_demo_setup/edit_unfilled",$data,true));
            }
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
            $data['previous_varieties']=array();//active and inactive
            $results=Query_helper::get_info($this->config->item('table_tm_rnd_demo_varieties'),'*',array('setup_id ='.$setup_id,'status ="'.$this->config->item('system_status_active').'"'));
            if(!$results)
            {
                System_helper::invalid_try('details not exists',$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }
            $variety_id=0;
            foreach($results as $i=>$result)
            {
                if($i==0)
                {
                    $variety_id=$result['variety_id'];
                }
                $data['previous_varieties'][$result['variety_id']]=$result;
            }

            $this->db->from($this->config->item('table_tm_rnd_demo_setup').' tmf');
            $this->db->select('tmf.*');

            $this->db->select('crop.name crop_name');
            $this->db->select('crop_type.id type_id,crop_type.name crop_type_name');
            $this->db->select('v.name variety_name');//invalid
            $this->db->select('season.name season_name');

            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id ='.$variety_id,'INNER');
            $this->db->join($this->config->item('table_setup_classification_crop_types').' crop_type','crop_type.id =v.crop_type_id','INNER');
            $this->db->join($this->config->item('table_setup_classification_crops').' crop','crop.id =crop_type.crop_id','INNER');
            $this->db->join($this->config->item('table_setup_tm_seasons').' season','season.id =tmf.season_id','INNER');
            $this->db->where('tmf.id',$setup_id);
            $this->db->where('tmf.status','Active');
            $data['fsetup']=$this->db->get()->row_array();
            if(!$data['fsetup'])
            {
                System_helper::invalid_try($this->config->item('system_view_not_exists'),$setup_id);
                $ajax['status']=false;
                $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
                $this->jsonReturn($ajax);
            }

            $data['title']="Detail R&D Demo Variety Setup";
            $data['varieties']=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),array('id value','name text','whose'),array('crop_type_id ='.$data['fsetup']['type_id'],'status ="'.$this->config->item('system_status_active').'"'),0,0,array('whose ASC','ordering ASC'));
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_rnd_demo_setup/details",$data,true));
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
                Query_helper::update($this->config->item('table_tm_rnd_demo_setup'),array('status'=>$this->config->item('system_status_delete'),'user_updated'=>$user->user_id,'date_updated'=>$time),array("id = ".$id));
                Query_helper::update($this->config->item('table_tm_rnd_demo_varieties'),array('status'=>$this->config->item('system_status_delete'),'user_updated'=>$user->user_id,'date_updated'=>$time),array("setup_id = ".$id));
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
            $data=$this->input->post('fsetup');
            $data['date_sowing']=System_helper::get_time($data['date_sowing']);
            $data['date_transplant']=System_helper::get_time($data['date_transplant']);


            $this->db->trans_start();  //DB Transaction Handle START
            if($id==0)
            {
                $data['user_created'] = $user->user_id;
                $data['date_created'] =$time;
                $setup_id=Query_helper::add($this->config->item('table_tm_rnd_demo_setup'),$data);
                if($setup_id===false)
                {
                    $this->db->trans_complete();
                    $ajax['status']=false;
                    $ajax['system_message']=$this->lang->line("MSG_SAVED_FAIL");
                    $this->jsonReturn($ajax);
                    die();
                }
                else
                {
                    $id=$setup_id;
                }
            }
            else
            {
                $data['user_updated'] = $user->user_id;
                $data['date_updated'] = $time;
                Query_helper::update($this->config->item('table_tm_rnd_demo_setup'),$data,array("id = ".$id));
            }
            $previous_varieties=array();//active and inactive
            $results=Query_helper::get_info($this->config->item('table_tm_rnd_demo_varieties'),'*',array('setup_id ='.$id));
            foreach($results as $result)
            {
                $previous_varieties[$result['variety_id']]=$result;
            }
            $this->db->where('setup_id',$id);
            $this->db->set('status', $this->config->item('system_status_delete'));
            $this->db->set('date_updated', $time);
            $this->db->set('user_updated', $user->user_id);
            $this->db->update($this->config->item('table_tm_rnd_demo_varieties'));
            $variety_ids=$this->input->post('variety_ids');
            foreach($variety_ids as $variety_id)
            {
                $data=array();
                $data['setup_id']=$id;
                $data['variety_id']=$variety_id;
                $data['status']=$this->config->item('system_status_active');
                if(isset($previous_varieties[$variety_id]))
                {

                    $data['user_updated'] = $user->user_id;
                    $data['date_updated'] = $time;
                    Query_helper::update($this->config->item('table_tm_rnd_demo_varieties'),$data,array("id = ".$previous_varieties[$variety_id]['id']));
                }
                else
                {
                    $data['user_created'] = $user->user_id;
                    $data['date_created'] =$time;
                    Query_helper::add($this->config->item('table_tm_rnd_demo_varieties'),$data);
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
    private function system_save_unfilled()
    {
        $id = $this->input->post("id");
        $user = User_helper::get_user();
        if(!(isset($this->permissions['add'])&&($this->permissions['add']==1)))
        {
            $ajax['status']=false;
            $ajax['system_message']=$this->lang->line("YOU_DONT_HAVE_ACCESS");
            $this->jsonReturn($ajax);
            die();

        }

        {
            $data=$this->input->post('fsetup');
            $data['date_transplant']=System_helper::get_time($data['date_transplant']);
            $this->db->trans_start();  //DB Transaction Handle START
            $data['user_updated'] = $user->user_id;
            $data['date_updated'] = time();

            Query_helper::update($this->config->item('table_tm_rnd_demo_setup'),$data,array("id = ".$id));

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
        $this->load->library('form_validation');
        $this->form_validation->set_rules('fsetup[year]',$this->lang->line('LABEL_YEAR'),'required|numeric');
        $this->form_validation->set_rules('fsetup[season_id]',$this->lang->line('LABEL_SEASON'),'required|numeric');

        $this->form_validation->set_rules('fsetup[name]',"PRI's Name",'required');
        $this->form_validation->set_rules('fsetup[date_sowing]',$this->lang->line('LABEL_DATE_SOWING'),'required');
        $this->form_validation->set_rules('fsetup[num_visits]',$this->lang->line('LABEL_NUM_VISITS'),'required|numeric');
        $this->form_validation->set_rules('fsetup[interval]',$this->lang->line('LABEL_INTERVAL'),'required|numeric');
        if($this->form_validation->run() == FALSE)
        {
            $this->message=validation_errors();
            return false;
        }
        $variety_ids=$this->input->post('variety_ids');

        if(!((sizeof($variety_ids)>0)))
        {
            $this->message="Please Select at lease One Variety";
            return false;
        }
        return true;
    }


}
