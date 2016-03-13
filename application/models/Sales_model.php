<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sales_model extends CI_Model
{

    public function __construct() {
        parent::__construct();
    }
    public function get_stocks($variety_pack_sizes)
    {
        $CI = & get_instance();
        $stocks=array();

        $where='';
        if(sizeof($variety_pack_sizes)>0)
        {
            foreach($variety_pack_sizes as $i=>$vp)
            {
                if($i==0)
                {
                    $where='(variety_id='.$vp['variety_id'].' AND pack_size_id='.$vp['pack_size_id'].')';
                }
                else
                {
                    $where.='OR (variety_id='.$vp['variety_id'].' AND pack_size_id='.$vp['pack_size_id'].')';
                }
            }
        }

        //get stock in
        $this->db->from($CI->config->item('table_stockin_varieties'));
        $this->db->select('variety_id,pack_size_id');
        $this->db->select('SUM(quantity) stock_in');
        $this->db->group_by(array('variety_id','pack_size_id'));
        if(strlen($where)>0)
        {
            $this->db->where($where);
        }
        $this->db->where('status',$CI->config->item('system_status_active'));
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_in']=$result['stock_in'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['excess']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['sales_return']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['short']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['sample']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['sales']=0;

            $stocks[$result['variety_id']][$result['pack_size_id']]['current_stock']=$result['stock_in'];
        }

        $this->db->from($CI->config->item('table_stockin_excess_inventory'));
        $this->db->select('variety_id,pack_size_id');
        $this->db->select('SUM(quantity) stock_in');
        $this->db->group_by(array('variety_id','pack_size_id'));
        if(strlen($where)>0)
        {
            $this->db->where($where);
        }
        $this->db->where('status',$CI->config->item('system_status_active'));
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['excess']=$result['stock_in'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['current_stock']+=$result['stock_in'];
        }
        return $stocks;

    }

}