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
    public function crop_types()
    {
        $this->db->from('ait_product_type');
        $this->db->order_by('id');
        $types=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($types as $type)
        {

            {
                $data=array();
                $data['crop_id']=intval(substr($type['crop_id'],3));
                $data['name']=$type['product_type'];
                $data['description']=$type['description'];
                $data['status']=$type['status'];
                $data['ordering']=$type['order_type'];
                $data['date_created']=time();
                $data['user_created']=1;
                $this->db->insert('crop_types',$data);
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
    public function banks()
    {
        $this->db->from('ait_bank_info');
        $this->db->order_by('id');
        $banks=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($banks as $bank)
        {

            {
                $data=array();
                $data['name']=$bank['bank_name'];
                $data['description']=$bank['description'];
                $data['status']=$bank['status'];
                $data['ordering']=$bank['id'];
                $data['date_created']=time();
                $data['user_created']=1;
                $this->db->insert('basic_setup_bank',$data);
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
    public function branches()
    {
        $this->db->from('ait_bank_branch_info');
        $this->db->order_by('id');
        $branches=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($branches as $branch)
        {

            {
                $data=array();
                $data['bank_id']=intval(substr($branch['bank_id'],3));
                $data['name']=$branch['branch_name'];
                $data['description']=$branch['description'];
                $data['status']=$branch['status'];
                $data['ordering']=$branch['id'];
                $data['date_created']=time();
                $data['user_created']=1;
                $this->db->insert('basic_setup_bank_branch',$data);
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
    public function competitors()
    {
        $this->db->from('ait_varriety_info');
        $this->db->order_by('id');
        $this->db->group_by('company_name');
        $competitors=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        $i=0;
        foreach($competitors as $competitor)
        {
            if($competitor['company_name'])
            {
                $i++;
                $data=array();
                $data['name']=$competitor['company_name'];
                $data['description']='';
                $data['status']=$this->config->item('system_status_active');
                $data['ordering']=$i;
                $data['date_created']=time();
                $data['user_created']=1;
                $this->db->insert('basic_setup_competitor',$data);
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
    public function varieties()
    {
        $this->db->from('ait_varriety_info avi');
        $this->db->order_by('avi.id');
        $this->db->select('avi.*');
        $this->db->select('c.id competitor_id');
        $this->db->join('basic_setup_competitor c','c.name = avi.company_name','LEFT');

        $varieties=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($varieties as $variety)
        {

            {

                $data=array();
                $data['name']=$variety['varriety_name'];
                $data['crop_type_id']=intval(substr($variety['product_type_id'],3));
                $data['whose']='';
                $data['competitor_id']='';
                if($variety['type']==0)
                {
                    $data['whose']='ARM';
                }
                elseif($variety['type']==1)
                {
                    $data['whose']='Competitor';
                    $data['competitor_id']=$variety['competitor_id'];
                }
                elseif($variety['type']==2)
                {
                    $data['whose']='Upcoming';
                }

                $data['stock_id']=$variety['stock_id'];
                $data['hybrid']=$variety['hybrid'];
                $data['description']=$variety['description'];
                $data['status']=$variety['status'];
                $data['ordering']=$variety['order_variety'];
                $data['date_created']=time();
                $data['user_created']=1;
                $this->db->insert('varieties',$data);
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
    public function pack_size()
    {
        $this->db->from('ait_product_pack_size');
        $this->db->order_by('id');
        $packs=$this->db->get()->result_array();
        $this->db->trans_start();  //DB Transaction Handle START
        foreach($packs as $pack)
        {

            {
                $data=array();
                $data['name']=$pack['pack_size_name'];
                $data['description']=$pack['description'];
                $data['status']=$pack['status'];
                $data['date_created']=time();
                $data['user_created']=1;
                $this->db->insert('variety_pack_size',$data);
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
