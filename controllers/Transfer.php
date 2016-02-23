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
    public function districts()
    {
        $this->db->from('ait_territory_assign_district tad');
        $this->db->select('tad.territory_id');
        $this->db->select('z.zillaid,z.zillanameeng');
        $this->db->join('ait_zilla z','z.zillaid =tad.zilla_id','LEFT');

        $districts=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($districts as $i=>$district)
        {
            $data=array();
            $data['territory_id']=intval(substr($district['territory_id'],3));
            $data['name']=$district['zillanameeng'];
            $data['status']=$this->config->item('system_status_active');
            $data['ordering']=$i+1;
            $data['date_created']=time();
            $data['user_created']=1;
            $data['old_zilla_id']=$district['zillaid'];
            $this->db->insert('districts',$data);
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
    public function upazillas()
    {
        $this->db->from('ait_upazilla_new un');
        $this->db->select('un.upazilla_id old_upazilla_id,upazilla_name name');
        $this->db->select('d.id district_id');
        $this->db->join('districts d','d.old_zilla_id =un.zilla_id','LEFT');
        $this->db->order_by('un.upazilla_id');
        $upazillas=$this->db->get()->result_array();

        $this->db->trans_start();  //DB Transaction Handle START
        foreach($upazillas as $i=>$upazilla)
        {
            if($upazilla['district_id']>0)
            {
                $data=array();
                $data['district_id']=$upazilla['district_id'];
                $data['name']=$upazilla['name'];
                $data['status']=$this->config->item('system_status_active');
                $data['ordering']=$i+1;
                $data['date_created']=time();
                $data['user_created']=1;
                $data['old_upazilla_id']=$upazilla['old_upazilla_id'];
                $this->db->insert('upazillas',$data);
            }

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
    public function unions()
    {
        $this->db->from('ait_union union');
        $this->db->select('union.union_id old_union_id,union.union_name name');
        $this->db->select('up.id upazilla_id');
        $this->db->join('upazillas up','up.old_upazilla_id =union.upazilla_id','LEFT');
        $this->db->order_by('union.union_id');
        $unions=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($unions as $i=>$union)
        {
            if($union['upazilla_id']>0)
            {
                $data=array();
                $data['upazilla_id']=$union['upazilla_id'];
                $data['name']=$union['name'];
                $data['status']=$this->config->item('system_status_active');
                $data['ordering']=$i+1;
                $data['date_created']=time();
                $data['user_created']=1;
                $data['old_union_id']=$union['old_union_id'];
                $this->db->insert('unions',$data);
            }

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
    public function crops()
    {
        $this->db->from('ait_crop_info');
        $this->db->order_by('id');
        $crops=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($crops as $crop)
        {

            {
                $data=array();
                $data['name']=$crop['crop_name'];
                $data['description']=$crop['description'];
                $data['status']=$crop['status'];
                $data['ordering']=$crop['order_crop'];
                $data['date_created']=time();
                $data['user_created']=1;
                $this->db->insert('crops',$data);
            }

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
