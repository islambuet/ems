<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tm_rnd_demo_picture extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Tm_rnd_demo_picture');
        $this->controller_url='tm_rnd_demo_picture';
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
            $data['title']="R&D Demo Picture List";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_rnd_demo_picture/list",$data,true));
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
        $this->db->select('count(distinct vp.day_no) num_visit_done',true);
        $this->db->select('count(distinct vfp.picture_id) num_fruit_picture',false);
        $this->db->select('count(distinct case when vdp.status="Active" then vdp.id end) num_disease_picture',false);

        $this->db->join($this->config->item('table_setup_tm_seasons').' season','season.id =tmf.season_id','INNER');
        $this->db->join($this->config->item('table_tm_rnd_demo_picture').' vp','tmf.id =vp.setup_id','LEFT');
        $this->db->join($this->config->item('table_tm_rnd_demo_fruit_picture').' vfp','tmf.id =vfp.setup_id','LEFT');
        $this->db->join($this->config->item('table_tm_rnd_demo_disease_picture').' vdp','tmf.id =vdp.setup_id','LEFT');

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
            $data['previous_varieties']=array();//active and inactive
            $this->db->from($this->config->item('table_tm_rnd_demo_varieties').' tfv');
            $this->db->select('tfv.*');
            $this->db->select('v.name variety_name,v.whose');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tfv.variety_id','INNER');
            $this->db->where('tfv.setup_id',$setup_id);
            $this->db->where('tfv.status',$this->config->item('system_status_active'));
            $this->db->order_by('v.whose ASC');
            $this->db->order_by('v.ordering ASC');
            $results=$this->db->get()->result_array();
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

            $data['visits_picture']=array();
            $results=Query_helper::get_info($this->config->item('table_tm_rnd_demo_picture'),'*',array('setup_id ='.$setup_id));
            foreach($results as $result)
            {
                $data['visits_picture'][$result['day_no']][$result['variety_id']]=$result;
            }
            $data['fruits_picture_headers']=Query_helper::get_info($this->config->item('table_setup_tm_fruit_picture'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            $data['fruits_picture']=array();
            $results=Query_helper::get_info($this->config->item('table_tm_rnd_demo_fruit_picture'),'*',array('setup_id ='.$setup_id));
            foreach($results as $result)
            {
                $data['fruits_picture'][$result['picture_id']][$result['variety_id']]=$result;
            }
            $data['disease_picture']=Query_helper::get_info($this->config->item('table_tm_rnd_demo_disease_picture'),'*',array('setup_id ='.$setup_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('id'));
            $data['title']="Edit R&D Demo Picture";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_rnd_demo_picture/add_edit",$data,true));
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
            $this->db->from($this->config->item('table_tm_rnd_demo_varieties').' tfv');
            $this->db->select('tfv.*');
            $this->db->select('v.name variety_name,v.whose');
            $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tfv.variety_id','INNER');
            $this->db->where('tfv.setup_id',$setup_id);
            $this->db->where('tfv.status',$this->config->item('system_status_active'));
            $this->db->order_by('v.whose ASC');
            $this->db->order_by('v.ordering ASC');
            $results=$this->db->get()->result_array();
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

            $data['visits_picture']=array();
            $results=Query_helper::get_info($this->config->item('table_tm_rnd_demo_picture'),'*',array('setup_id ='.$setup_id));
            foreach($results as $result)
            {
                $data['visits_picture'][$result['day_no']][$result['variety_id']]=$result;
            }
            $data['fruits_picture_headers']=Query_helper::get_info($this->config->item('table_setup_tm_fruit_picture'),'*',array('status ="'.$this->config->item('system_status_active').'"'),0,0,array('ordering ASC'));
            $data['fruits_picture']=array();
            $results=Query_helper::get_info($this->config->item('table_tm_rnd_demo_fruit_picture'),'*',array('setup_id ='.$setup_id));
            foreach($results as $result)
            {
                $data['fruits_picture'][$result['picture_id']][$result['variety_id']]=$result;
            }
            $data['disease_picture']=Query_helper::get_info($this->config->item('table_tm_rnd_demo_disease_picture'),'*',array('setup_id ='.$setup_id,'status ="'.$this->config->item('system_status_active').'"'),0,0,array('id'));
            $data['users']=System_helper::get_users_info(array());

            $data['title']="Details of R&D Demo Picture";
            $ajax['status']=true;
            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view("tm_rnd_demo_picture/details",$data,true));
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
        $this->db->from($this->config->item('table_tm_rnd_demo_setup').' tmf');
        $this->db->select('tmf.*');


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


        $this->db->from($this->config->item('table_tm_rnd_demo_varieties').' tfv');
        $this->db->select('tfv.*');
        $this->db->select('v.name variety_name,v.whose');
        $this->db->join($this->config->item('table_setup_classification_varieties').' v','v.id =tfv.variety_id','INNER');
        $this->db->where('tfv.setup_id',$setup_id);
        $this->db->where('tfv.status',$this->config->item('system_status_active'));
        $this->db->order_by('v.whose ASC');
        $this->db->order_by('v.ordering ASC');
        $previous_varieties=$this->db->get()->result_array();

        $file_folder='images/rnd_demo/'.$setup_id;
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
        $visits_picture=array();
        $results=Query_helper::get_info($this->config->item('table_tm_rnd_demo_picture'),'*',array('setup_id ='.$setup_id));
        foreach($results as $result)
        {
            $visits_picture[$result['day_no']][$result['variety_id']]=$result;
        }
        $visit_remarks=$this->input->post('visit_remarks');


        $fruits_picture_headers=Query_helper::get_info($this->config->item('table_setup_tm_fruit_picture'),'*',array('status ="'.$this->config->item('system_status_active').'"'));
        $fruits_picture=array();
        $results=Query_helper::get_info($this->config->item('table_tm_rnd_demo_fruit_picture'),'*',array('setup_id ='.$setup_id));
        foreach($results as $result)
        {
            $fruits_picture[$result['picture_id']][$result['variety_id']]=$result;
        }
        $fruit_remarks=$this->input->post('fruit_remarks');

        $this->db->trans_start();
        for($i=1;$i<=$fsetup['num_visits'];$i++)
        {
            foreach($previous_varieties as $variety)
            {
                $data=array();
                if(isset($visit_remarks[$i][$variety['variety_id']]))
                {
                    if((strlen($visit_remarks[$i][$variety['variety_id']]))>0)
                    {
                        $data['remarks']=$visit_remarks[$i][$variety['variety_id']];
                    }
                    elseif(isset($visits_picture[$i][$variety['variety_id']]))
                    {
                        $data['remarks']='';
                    }
                }
                if(isset($uploaded_files['visit_image_'.$i.'_'.$variety['variety_id']]))
                {
                    $data['picture_url']=base_url().$file_folder.'/'.$uploaded_files['visit_image_'.$i.'_'.$variety['variety_id']]['info']['file_name'];
                    $data['picture_file_full']=$file_folder.'/'.$uploaded_files['visit_image_'.$i.'_'.$variety['variety_id']]['info']['file_name'];
                    $data['picture_file_name']=$uploaded_files['visit_image_'.$i.'_'.$variety['variety_id']]['info']['file_name'];
                }
                if($data)
                {
                    if(isset($visits_picture[$i][$variety['variety_id']]))
                    {
                        $data['user_updated'] = $user->user_id;
                        $data['date_updated'] = $time;
                        Query_helper::update($this->config->item('table_tm_rnd_demo_picture'),$data,array("id = ".$visits_picture[$i][$variety['variety_id']]['id']));
                    }
                    else
                    {
                        $data['setup_id'] = $setup_id;
                        $data['day_no'] = $i;
                        $data['variety_id'] = $variety['variety_id'];
                        $data['user_created'] = $user->user_id;
                        $data['date_created'] = $time;
                        Query_helper::add($this->config->item('table_tm_rnd_demo_picture'),$data);
                    }
                }

            }
        }

        foreach($fruits_picture_headers as $header)
        {
            foreach($previous_varieties as $variety)
            {
                $data=array();
                if(isset($fruit_remarks[$header['id']][$variety['variety_id']]))
                {
                    if((strlen($fruit_remarks[$header['id']][$variety['variety_id']]))>0)
                    {
                        $data['remarks']=$fruit_remarks[$header['id']][$variety['variety_id']];
                    }

                    elseif(isset($fruits_picture[$header['id']][$variety['variety_id']]))
                    {
                        $data['remarks']='';
                    }
                }
                if(isset($uploaded_files['fruit_image_'.$header['id'].'_'.$variety['variety_id']]))
                {
                    $data['picture_url']=base_url().$file_folder.'/'.$uploaded_files['fruit_image_'.$header['id'].'_'.$variety['variety_id']]['info']['file_name'];
                    $data['picture_file_full']=$file_folder.'/'.$uploaded_files['fruit_image_'.$header['id'].'_'.$variety['variety_id']]['info']['file_name'];
                    $data['picture_file_name']=$uploaded_files['fruit_image_'.$header['id'].'_'.$variety['variety_id']]['info']['file_name'];
                }
                if($data)
                {
                    if(isset($fruits_picture[$header['id']][$variety['variety_id']]))
                    {
                        $data['user_updated'] = $user->user_id;
                        $data['date_updated'] = $time;
                        Query_helper::update($this->config->item('table_tm_rnd_demo_fruit_picture'),$data,array("id = ".$fruits_picture[$header['id']][$variety['variety_id']]['id']));
                    }
                    else
                    {
                        $data['setup_id'] = $setup_id;
                        $data['picture_id'] = $header['id'];
                        $data['variety_id'] = $variety['variety_id'];;
                        $data['user_created'] = $user->user_id;
                        $data['date_created'] = $time;
                        Query_helper::add($this->config->item('table_tm_rnd_demo_fruit_picture'),$data);
                    }
                }

            }
        }
        $this->db->where('setup_id',$setup_id);
        $this->db->set('status', $this->config->item('system_status_delete'));
        $this->db->update($this->config->item('table_tm_rnd_demo_disease_picture'));

        $diseases=$this->input->post('disease');
        if(sizeof($diseases)>0)
        {
            foreach($diseases as $i=>$disease)
            {
                $data=array();
                $data['remarks']=$disease['remarks'];
                if(isset($uploaded_files['disease_image_'.$i]))
                {
                    $data['picture_url']=base_url().$file_folder.'/'.$uploaded_files['disease_image_'.$i]['info']['file_name'];
                    $data['picture_file_full']=$file_folder.'/'.$uploaded_files['disease_image_'.$i]['info']['file_name'];
                    $data['picture_file_name']=$uploaded_files['disease_image_'.$i]['info']['file_name'];
                }
                if($disease['id']>0)
                {
                    $data['user_updated'] = $user->user_id;
                    $data['date_updated'] = $time;
                    $data['status']=$this->config->item('system_status_active');
                    Query_helper::update($this->config->item('table_tm_rnd_demo_disease_picture'),$data,array("id = ".$disease['id']));
                }
                else
                {
                    $data['setup_id'] = $setup_id;
                    $data['variety_id'] = $disease['variety_id'];
                    $data['user_created'] = $user->user_id;
                    $data['date_created'] = $time;
                    $data['status']=$this->config->item('system_status_active');
                    Query_helper::add($this->config->item('table_tm_rnd_demo_disease_picture'),$data);
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
}
