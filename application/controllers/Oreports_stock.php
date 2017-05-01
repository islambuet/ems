<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Oreports_stock extends Root_Controller
{
    private  $message;
    public $permissions;
    public $controller_url;
    public $locations;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
        $this->permissions=User_helper::get_permission('Oreports_stock');
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
        $this->controller_url='oreports_stock';
    }
    public function index($action="search",$id=0)
    {
        if($action=="search")
        {
            $this->system_search();
        }
        elseif($action=="list")
        {
            $this->system_list();
        }
        elseif($action=="get_items_area_stock")
        {
            $this->system_get_items_area_stock();
        }
        else
        {
            $this->system_search();
        }
    }
    private function system_search()
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            $data['title']="Stock Report";
            $ajax['status']=true;
            $data['divisions']=Query_helper::get_info($this->config->item('table_setup_location_divisions'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
            $data['zones']=array();
            $data['territories']=array();
            $data['districts']=array();
            $data['customers']=array();
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
                            $data['customers']=Query_helper::get_info($this->config->item('table_csetup_customers'),array('id value','name text'),array('district_id ='.$this->locations['district_id'],'status ="'.$this->config->item('system_status_active').'"','type ="Outlet"'));
                        }
                    }
                }
            }
            $fiscal_years=Query_helper::get_info($this->config->item('table_basic_setup_fiscal_year'),'*',array());
            $data['fiscal_years']=array();
            foreach($fiscal_years as $year)
            {
                $data['fiscal_years'][]=array('text'=>$year['name'],'value'=>System_helper::display_date($year['date_start']).'/'.System_helper::display_date($year['date_end']));
            }

            $ajax['system_content'][]=array("id"=>"#system_content","html"=>$this->load->view($this->controller_url."/search",$data,true));
            $ajax['system_page_url']=site_url($this->controller_url);

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
    private function system_list()
    {
        if(isset($this->permissions['view'])&&($this->permissions['view']==1))
        {
            $reports=$this->input->post('report');
            $reports['date_end']=System_helper::get_time($reports['date_end']);
            $reports['date_end']=$reports['date_end']+3600*24-1;
            $reports['date_start']=System_helper::get_time($reports['date_start']);
            if($reports['date_start']>=$reports['date_end'])
            {
                $ajax['status']=false;
                $ajax['system_message']='Starting Date should be less than End date';
                $this->jsonReturn($ajax);
            }
            $data['options']=$reports;
            $ajax['status']=true;
            $data['title']="Stock Report";
            $ajax['system_content'][]=array("id"=>"#system_report_container","html"=>$this->load->view($this->controller_url."/list_area_stock",$data,true));

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
    private function system_get_items_area_stock()
    {
        $items=array();
        $report_unit=$this->input->post('unit');
        $division_id=$this->input->post('division_id');
        $zone_id=$this->input->post('zone_id');
        $territory_id=$this->input->post('territory_id');
        $district_id=$this->input->post('district_id');
        $customer_id=$this->input->post('customer_id');
        $crop_id=$this->input->post('crop_id');
        $crop_type_id=$this->input->post('crop_type_id');
        $variety_id=$this->input->post('variety_id');
        $date_end=$this->input->post('date_end');
        $date_start=$this->input->post('date_start');

        //get customer ids
        $this->db->from($this->config->item('table_csetup_customers').' cus');
        $this->db->select('cus.id,cus.name outlet_name');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $this->db->join($this->config->item('table_setup_location_zones').' zone','zone.id = t.zone_id','INNER');
        if($division_id>0)
        {
            $this->db->where('zone.division_id',$division_id);
            if($zone_id>0)
            {
                $this->db->where('zone.id',$zone_id);
                if($territory_id>0)
                {
                    $this->db->where('t.id',$territory_id);
                    if($district_id>0)
                    {
                        $this->db->where('d.id',$district_id);
                        if($customer_id>0)
                        {
                            $this->db->where('cus.id',$customer_id);
                        }
                    }
                }
            }
        }
        $this->db->where('cus.type','Outlet');
        $this->db->where('cus.status',$this->config->item('system_status_active'));
        $results=$this->db->get()->result_array();
        $outlet_ids=array();
        foreach($results as $result)
        {
            $outlet_ids[$result['id']]=$result['id'];
        }
        if(sizeof($outlet_ids)>0)
        {
            //get stock in and out from ems
            //sale receive and return quantity till end date
            $this->db->from($this->config->item('system_db_ems').'.'.$this->config->item('table_sales_po_receives').' por');
            $this->db->select('por.quantity_receive,por.quantity_bonus_receive,por.date_receive');
            $this->db->select('pod.pack_size_id,pod.pack_size');
            $this->db->select('pod.bonus_pack_size_id,pod.bonus_pack_size');
            $this->db->select('pod.quantity_return,pod.quantity_bonus_return,pod.date_return');
            $this->db->select('v.id variety_id,v.name variety_name');
            $this->db->select('type.id type_id,type.name type_name');
            $this->db->select('crop.id crop_id,crop.name crop_name');


            $this->db->join($this->config->item('system_db_ems').'.'.$this->config->item('table_sales_po_details').' pod','pod.id =por.sales_po_detail_id','INNER');
            $this->db->join($this->config->item('system_db_ems').'.'.$this->config->item('table_sales_po').' po','po.id =pod.sales_po_id','INNER');
            $this->db->join($this->config->item('system_db_ems').'.'.$this->config->item('table_setup_classification_varieties').' v','v.id =pod.variety_id','INNER');
            $this->db->join($this->config->item('system_db_ems').'.'.$this->config->item('table_setup_classification_crop_types').' type','type.id =v.crop_type_id','INNER');
            $this->db->join($this->config->item('system_db_ems').'.'.$this->config->item('table_setup_classification_crops').' crop','crop.id =type.crop_id','INNER');

            $this->db->where('po.status_received',$this->config->item('system_status_po_received_received'));
            $this->db->where_in('po.customer_id',$outlet_ids);
            $this->db->where('por.revision',1);
            $this->db->where('pod.revision',1);
            $this->db->where('por.date_receive <=',$date_end);
            if($crop_id>0)
            {
                $this->db->where('crop.id',$crop_id);
                if($crop_type_id>0)
                {
                    $this->db->where('type.id',$crop_type_id);
                    if($variety_id>0)
                    {
                        $this->db->where('v.id',$variety_id);
                    }
                }
            }
            $this->db->order_by('crop.ordering ASC');
            $this->db->order_by('type.ordering ASC');
            $this->db->order_by('v.ordering ASC');
            $results=$this->db->get()->result_array();
            $stocks=array();
            $variety_ids=array();
            foreach($results as $result)
            {
                if(!(isset($stocks[$result['variety_id']][$result['pack_size_id']])))
                {
                    $stocks[$result['variety_id']][$result['pack_size_id']]['crop_id']=$result['crop_id'];
                    $stocks[$result['variety_id']][$result['pack_size_id']]['crop_name']=$result['crop_name'];
                    $stocks[$result['variety_id']][$result['pack_size_id']]['type_id']=$result['type_id'];
                    $stocks[$result['variety_id']][$result['pack_size_id']]['type_name']=$result['type_name'];
                    $stocks[$result['variety_id']][$result['pack_size_id']]['variety_id']=$result['variety_id'];
                    $stocks[$result['variety_id']][$result['pack_size_id']]['variety_name']=$result['variety_name'];
                    $stocks[$result['variety_id']][$result['pack_size_id']]['pack_size_id']=$result['pack_size_id'];
                    $stocks[$result['variety_id']][$result['pack_size_id']]['pack_size']=$result['pack_size'];
                    $stocks[$result['variety_id']][$result['pack_size_id']]['starting_stock']=0;
                    $stocks[$result['variety_id']][$result['pack_size_id']]['stock_in']=0;
                    $stocks[$result['variety_id']][$result['pack_size_id']]['stock_return']=0;
                    $stocks[$result['variety_id']][$result['pack_size_id']]['sales']=0;
                    $stocks[$result['variety_id']][$result['pack_size_id']]['sales_cancel']=0;
                    $variety_ids[$result['variety_id']]=$result['variety_id'];
                }
                if($result['bonus_pack_size_id']>0)
                {
                    if(!(isset($stocks[$result['variety_id']][$result['bonus_pack_size_id']])))
                    {
                        $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['crop_id']=$result['crop_id'];
                        $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['crop_name']=$result['crop_name'];
                        $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['type_id']=$result['type_id'];
                        $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['type_name']=$result['type_name'];
                        $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['variety_id']=$result['variety_id'];
                        $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['variety_name']=$result['variety_name'];
                        $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['pack_size_id']=$result['bonus_pack_size_id'];
                        $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['pack_size']=$result['bonus_pack_size'];
                        $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['starting_stock']=0;
                        $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['stock_in']=0;
                        $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['stock_return']=0;
                        $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['sales']=0;
                        $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['sales_cancel']=0;
                        $variety_ids[$result['variety_id']]=$result['variety_id'];
                    }
                }
                if($report_unit=='weight')
                {
                    $result['quantity_receive']=$result['quantity_receive']*$result['pack_size'];
                    $result['quantity_bonus_receive']=$result['quantity_bonus_receive']*$result['bonus_pack_size'];
                    $result['quantity_return']=$result['quantity_return']*$result['pack_size'];
                    $result['quantity_bonus_return']=$result['quantity_bonus_return']*$result['bonus_pack_size'];

                }
                if(($date_start>0) && ($date_start>$result['date_receive']))
                {
                    $stocks[$result['variety_id']][$result['pack_size_id']]['starting_stock']+=($result['quantity_receive']);
                    if($result['bonus_pack_size_id']>0)
                    {
                        $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['starting_stock']+=($result['quantity_bonus_receive']);
                    }
                }
                else
                {
                    $stocks[$result['variety_id']][$result['pack_size_id']]['stock_in']+=($result['quantity_receive']);
                    if($result['bonus_pack_size_id']>0)
                    {
                        $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['stock_in']+=($result['quantity_bonus_receive']);
                    }
                }
                if($result['date_return']>0)
                {
                    if(($date_start>0) && ($date_start>$result['date_return']))
                    {
                        $stocks[$result['variety_id']][$result['pack_size_id']]['starting_stock']-=($result['quantity_return']);
                        if($result['bonus_pack_size_id']>0)
                        {
                            $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['starting_stock']-=($result['quantity_bonus_return']);
                        }
                    }
                    else//if date_return greater than date_end should not calculate.Ignored
                    {
                        $stocks[$result['variety_id']][$result['pack_size_id']]['stock_return']+=($result['quantity_return']);
                        if($result['bonus_pack_size_id']>0)
                        {
                            $stocks[$result['variety_id']][$result['bonus_pack_size_id']]['stock_return']+=($result['quantity_bonus_return']);
                        }
                    }
                }
            }
            if(sizeof($variety_ids)>0)
            {
                //sale upto starting days
                if($date_start>0)
                {
                    $this->db->from($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale_details').' pod');
                    $this->db->select('pod.variety_id,pod.pack_size_id,pod.pack_size');
                    $this->db->select('SUM(pod.quantity_sale) quantity');
                    $this->db->join($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale').' sale','sale.id =pod.sale_id','INNER');
                    //$this->db->where('sale.status',$this->config->item('system_status_active'));
                    $this->db->where('pod.revision',1);
                    $this->db->where('sale.date_sale <',$date_start);
                    $this->db->where_in('sale.customer_id',$outlet_ids);
                    $this->db->where_in('pod.variety_id',$variety_ids);

                    $this->db->group_by(array('pod.variety_id','pod.pack_size_id'));
                    $results=$this->db->get()->result_array();
                    foreach($results as $result)
                    {
                        if($report_unit=='weight')
                        {
                            $result['quantity']=$result['quantity']*$result['pack_size'];
                        }
                        $stocks[$result['variety_id']][$result['pack_size_id']]['starting_stock']-=$result['quantity'];
                    }
                    //sale cancel
                    $this->db->from($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale_details').' pod');
                    $this->db->select('pod.variety_id,pod.pack_size_id,pod.pack_size');
                    $this->db->select('SUM(pod.quantity_sale) quantity');
                    $this->db->join($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale').' sale','sale.id =pod.sale_id','INNER');

                    $this->db->where('sale.status',$this->config->item('system_status_inactive'));
                    $this->db->where('pod.revision',1);
                    $this->db->where('sale.date_canceled <',$date_start);
                    $this->db->where_in('sale.customer_id',$outlet_ids);
                    $this->db->where_in('pod.variety_id',$variety_ids);
                    $this->db->group_by(array('pod.variety_id','pod.pack_size_id'));
                    $results=$this->db->get()->result_array();
                    foreach($results as $result)
                    {
                        if($report_unit=='weight')
                        {
                            $result['quantity']=$result['quantity']*$result['pack_size'];
                        }
                        $stocks[$result['variety_id']][$result['pack_size_id']]['starting_stock']+=$result['quantity'];
                    }

                }
                //sale start to end
                $this->db->from($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale_details').' pod');
                $this->db->select('pod.variety_id,pod.pack_size_id,pod.pack_size');
                $this->db->select('SUM(pod.quantity_sale) quantity');
                $this->db->join($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale').' sale','sale.id =pod.sale_id','INNER');
                //$this->db->where('sale.status',$this->config->item('system_status_active'));
                $this->db->where('pod.revision',1);
                $this->db->where('sale.date_sale <=',$date_end);
                $this->db->where('sale.date_sale >=',$date_start);
                $this->db->where_in('sale.customer_id',$outlet_ids);
                $this->db->where_in('pod.variety_id',$variety_ids);

                $this->db->group_by(array('pod.variety_id','pod.pack_size_id'));
                $results=$this->db->get()->result_array();
                foreach($results as $result)
                {
                    if($report_unit=='weight')
                    {
                        $result['quantity']=$result['quantity']*$result['pack_size'];
                    }
                    $stocks[$result['variety_id']][$result['pack_size_id']]['sales']=$result['quantity'];
                }
                //sale cancel
                $this->db->from($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale_details').' pod');
                $this->db->select('pod.variety_id,pod.pack_size_id,pod.pack_size');
                $this->db->select('SUM(pod.quantity_sale) quantity');
                $this->db->join($this->config->item('system_db_pos').'.'.$this->config->item('table_pos_sale').' sale','sale.id =pod.sale_id','INNER');
                $this->db->where('sale.status',$this->config->item('system_status_inactive'));
                $this->db->where('pod.revision',1);
                $this->db->where('sale.date_canceled <=',$date_end);
                $this->db->where('sale.date_canceled >=',$date_start);
                $this->db->where_in('sale.customer_id',$outlet_ids);
                $this->db->where_in('pod.variety_id',$variety_ids);
                $this->db->group_by(array('pod.variety_id','pod.pack_size_id'));
                $results=$this->db->get()->result_array();
                foreach($results as $result)
                {
                    if($report_unit=='weight')
                    {
                        $result['quantity']=$result['quantity']*$result['pack_size'];
                    }
                    $stocks[$result['variety_id']][$result['pack_size_id']]['sales_cancel']=$result['quantity'];
                }
                $this->db->from($this->config->item('system_db_ems').'.'.$this->config->item('table_setup_classification_variety_price').' vp');
                $this->db->select('vp.variety_id,vp.pack_size_id,vp.price');
                $this->db->where('vp.revision',1);
                $this->db->where_in('vp.variety_id',$variety_ids);
                $prices=array();

                $results=$this->db->get()->result_array();

                foreach($results as $result)
                {
                    $prices[$result['variety_id']][$result['pack_size_id']]=$result['price'];
                }


                $type_total=array();
                $crop_total=array();
                $grand_total=array();

                $type_total['crop_name']='';
                $type_total['type_name']='';
                $type_total['variety_name']='Total Type';

                $crop_total['crop_name']='';
                $crop_total['type_name']='Total Crop';
                $crop_total['variety_name']='';

                $grand_total['crop_name']='Grand Total';
                $grand_total['type_name']='';
                $grand_total['variety_name']='';

                $grand_total['pack_size']=$crop_total['pack_size']=$type_total['pack_size']='';
                $grand_total['starting_stock']=$crop_total['starting_stock']=$type_total['starting_stock']=0;
                $grand_total['stock_in']=$crop_total['stock_in']=$type_total['stock_in']=0;
                $grand_total['stock_return']=$crop_total['stock_return']=$type_total['stock_return']=0;
                $grand_total['sales']=$crop_total['sales']=$type_total['sales']=0;
                $grand_total['sales_cancel']=$crop_total['sales_cancel']=$type_total['sales_cancel']=0;
                $grand_total['current_unit_price']=$crop_total['current_unit_price']=$type_total['current_unit_price']='';
                $grand_total['current_total_price']=$crop_total['current_total_price']=$type_total['current_total_price']=0;

                $items=array();
                $prev_crop_name='';
                $prev_type_name='';
                $first_row=true;
                foreach($stocks as $variety_id=>$varieties)
                {
                    foreach($varieties as $pack_size_id=>$info)
                    {
                        if(!$first_row)
                        {
                            if($prev_crop_name!=$info['crop_name'])
                            {
                                $items[]=$this->get_area_stock_row($type_total,$report_unit);
                                $items[]=$this->get_area_stock_row($crop_total,$report_unit);
                                $crop_total['starting_stock']=$type_total['starting_stock']=0;
                                $crop_total['stock_in']=$type_total['stock_in']=0;
                                $crop_total['stock_return']=$type_total['stock_return']=0;
                                $crop_total['sales']=$type_total['sales']=0;
                                $crop_total['sales_cancel']=$type_total['sales_cancel']=0;
                                $crop_total['current_total_price']=$type_total['current_total_price']=0;
                                $prev_crop_name=$info['crop_name'];
                                $prev_type_name=$info['type_name'];
                                //sum and reset type total
                                //sum and reset crop total
                            }
                            elseif($prev_type_name!=$info['type_name'])
                            {
                                $items[]=$this->get_area_stock_row($type_total,$report_unit);
                                $type_total['starting_stock']=0;
                                $type_total['stock_in']=0;
                                $type_total['stock_return']=0;
                                $type_total['sales']=0;
                                $type_total['sales_cancel']=0;
                                $type_total['current_total_price']=0;
                                $info['crop_name']='';
                                $prev_type_name=$info['type_name'];
                                //sum and reset type total
                            }
                            else
                            {
                                $info['crop_name']='';
                                $info['type_name']='';
                            }
                        }
                        else
                        {
                            $prev_crop_name=$info['crop_name'];
                            $prev_type_name=$info['type_name'];
                            $first_row=false;
                        }
                        $grand_total['starting_stock']+=$info['starting_stock'];
                        $crop_total['starting_stock']+=$info['starting_stock'];
                        $type_total['starting_stock']+=$info['starting_stock'];
                        $grand_total['stock_in']+=$info['stock_in'];
                        $crop_total['stock_in']+=$info['stock_in'];
                        $type_total['stock_in']+=$info['stock_in'];
                        $grand_total['stock_return']+=$info['stock_return'];
                        $crop_total['stock_return']+=$info['stock_return'];
                        $type_total['stock_return']+=$info['stock_return'];
                        $grand_total['sales']+=$info['sales'];
                        $crop_total['sales']+=$info['sales'];
                        $type_total['sales']+=$info['sales'];
                        $grand_total['sales_cancel']+=$info['sales_cancel'];
                        $crop_total['sales_cancel']+=$info['sales_cancel'];
                        $type_total['sales_cancel']+=$info['sales_cancel'];
                        if(isset($prices[$variety_id][$pack_size_id]))
                        {
                            $cur_stock=$info['starting_stock']+$info['stock_in']-$info['stock_return']-$info['sales']+$info['sales_cancel'];
                            $info['current_unit_price']=$prices[$variety_id][$pack_size_id];

                            if($report_unit=='weight')
                            {
                                $info['current_unit_price']=($prices[$variety_id][$pack_size_id]*1000/$info['pack_size']);
                                $info['current_total_price']=$cur_stock*$prices[$variety_id][$pack_size_id]/$info['pack_size'];
                            }
                            else
                            {
                                $info['current_total_price']=$cur_stock*$prices[$variety_id][$pack_size_id];
                            }
                        }
                        else
                        {
                            $info['current_unit_price']=0;
                            $info['current_total_price']=0;
                        }
                        $grand_total['current_total_price']+=$info['current_total_price'];
                        $crop_total['current_total_price']+=$info['current_total_price'];
                        $type_total['current_total_price']+=$info['current_total_price'];
                        $items[]=$this->get_area_stock_row($info,$report_unit);
                    }
                }
                $items[]=$this->get_area_stock_row($type_total,$report_unit);
                $items[]=$this->get_area_stock_row($crop_total,$report_unit);
                $items[]=$this->get_area_stock_row($grand_total,$report_unit);

            }
        }
        $this->jsonReturn($items);
    }
    private function get_area_stock_row($info,$report_type)
    {
        $row=array();
        $row['crop_name']=$info['crop_name'];
        $row['type_name']=$info['type_name'];
        $row['variety_name']=$info['variety_name'];
        $row['pack_size']=$info['pack_size'];
        if($report_type=='weight')
        {
            $row['starting_stock']=number_format($info['starting_stock']/1000,3,'.','');
        }
        else
        {
            $row['starting_stock']=$info['starting_stock'];
        }
        if($report_type=='weight')
        {
            $row['stock_in']=number_format($info['stock_in']/1000,3,'.','');
        }
        else
        {
            $row['stock_in']=$info['stock_in'];
        }
        if($report_type=='weight')
        {
            $row['stock_return']=number_format($info['stock_return']/1000,3,'.','');
        }
        else
        {
            $row['stock_return']=$info['stock_return'];
        }
        if($report_type=='weight')
        {
            $row['sales']=number_format($info['sales']/1000,3,'.','');
        }
        else
        {
            $row['sales']=$info['sales'];
        }
        if($report_type=='weight')
        {
            $row['sales_cancel']=number_format($info['sales_cancel']/1000,3,'.','');
        }
        else
        {
            $row['sales_cancel']=$info['sales_cancel'];
        }
        if($report_type=='weight')
        {
            $row['sales_actual']=number_format(($info['sales']-$info['sales_cancel'])/1000,3,'.','');
        }
        else
        {
            $row['sales_actual']=$info['sales']-$info['sales_cancel'];
        }


        $row['current_stock']=$info['starting_stock']+$info['stock_in']-$info['stock_return']-$info['sales']+$info['sales_cancel'];
        if($report_type=='weight')
        {
            $row['current_stock']=number_format($row['current_stock']/1000,3,'.','');
        }
        if($info['current_unit_price']!='')
        {
            $row['current_unit_price']=number_format($info['current_unit_price'],2);
        }
        else
        {
            $row['current_unit_price']='';
        }

        $row['current_total_price']=number_format($info['current_total_price'],2);
        return $row;

    }
}
