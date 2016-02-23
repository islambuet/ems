<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{

	}
    public function divisions()
    {
        $divisions=$this->db->get('ait_division_info')->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($divisions as $division)
        {
            $data=array();
            $data['name']=$division['division_name'];
            $data['status']=$this->config->item('system_status_active');
            $data['ordering']=$division['id'];
            $data['date_created']=time();
            $data['user_created']=1;
            $this->db->insert('divisions',$data);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'success';
        }
        else
        {
            echo 'failed';
        }
    }
    public function zones()
    {
        $zones=$this->db->get('ait_zone_info')->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($zones as $zone)
        {
            $data=array();
            $data['id']=$zone['id'];
            $data['division_id']=intval(substr($zone['division_id'],3));
            $data['name']=$zone['zone_name'];
            $data['status']=$zone['status'];
            $data['ordering']=$zone['id'];
            $data['date_created']=time();
            $data['user_created']=1;
            $this->db->insert('zones',$data);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'success';
        }
        else
        {
            echo 'failed';
        }
    }
    public function territories()
    {
        $territories=$this->db->get('ait_territory_info')->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($territories as $territory)
        {
            $data=array();
            $data['id']=$territory['id'];
            $data['zone_id']=intval(substr($territory['zone_id'],3));
            $data['name']=$territory['territory_name'];
            $data['status']=$territory['status'];
            $data['ordering']=$territory['id'];
            $data['date_created']=time();
            $data['user_created']=1;
            $this->db->insert('territories',$data);
        }
        $this->db->trans_complete();   //DB Transaction Handle END
        if ($this->db->trans_status() === TRUE)
        {
            echo 'success';
        }
        else
        {
            echo 'failed';
        }
    }
}
