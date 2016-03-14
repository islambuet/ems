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

        //+get stock in
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
        //+excess Inventory
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
        //+sales return

        //-short

        //-sales

        //-sales bonus

        //-sample delivery
        return $stocks;

    }
    public function get_customer_current_credit($customer_id)
    {
        //0-payment+purchase-sales return

        $CI = & get_instance();
        $current_credit=0;
        //payment
        $this->db->from($CI->config->item('table_payment_payment'));
        $this->db->select('SUM(amount) total_paid');
        $this->db->where('customer_id',$customer_id);
        $this->db->where('status',$CI->config->item('system_status_active'));
        $result=$this->db->get()->row_array();
        if($result)
        {
            $current_credit-=$result['total_paid'];//minus for relative to arm
        }
        //purchase
        $this->db->from($CI->config->item('table_sales_po_details').' spd');
        $this->db->select('SUM(spd.variety_price*spd.quantity) total_buy');
        $this->db->join($CI->config->item('table_sales_po').' sp','sp.id = spd.sales_po_id','INNER');
        $this->db->where('sp.customer_id',$customer_id);
        $this->db->where('spd.revision',1);
        $this->db->where('sp.status_approved',$CI->config->item('system_status_po_approval_approved'));
        $result=$this->db->get()->row_array();
        if($result)
        {
            $current_credit+=$result['total_buy'];//plus for relative to arm
        }
        //sales _return pending
        return $current_credit;

    }

}