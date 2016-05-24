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
        $where_bonus='';
        if(sizeof($variety_pack_sizes)>0)
        {
            foreach($variety_pack_sizes as $i=>$vp)
            {
                if($i==0)
                {
                    $where='(variety_id='.$vp['variety_id'].' AND pack_size_id='.$vp['pack_size_id'].')';
                    $where_bonus='(variety_id='.$vp['variety_id'].' AND bonus_pack_size_id='.$vp['pack_size_id'].')';
                }
                else
                {
                    $where.='OR (variety_id='.$vp['variety_id'].' AND pack_size_id='.$vp['pack_size_id'].')';
                    $where_bonus.='OR (variety_id='.$vp['variety_id'].' AND bonus_pack_size_id='.$vp['pack_size_id'].')';
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
            $this->db->where('('.$where.')');
        }
        $this->db->where('status',$CI->config->item('system_status_active'));
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['stock_in']=$result['stock_in'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['excess']=0;
            $stocks[$result['variety_id']][$result['pack_size_id']]['stockout']=0;
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
            $this->db->where('('.$where.')');
        }
        $this->db->where('status',$CI->config->item('system_status_active'));
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['excess']=$result['stock_in'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['current_stock']+=$result['stock_in'];
        }
        //-stock out all
        $this->db->from($CI->config->item('table_stockout'));
        $this->db->select('variety_id,pack_size_id');
        $this->db->select('SUM(quantity) stockout');
        $this->db->group_by(array('variety_id','pack_size_id'));
        if(strlen($where)>0)
        {
            $this->db->where('('.$where.')');
        }
        $this->db->where('status',$CI->config->item('system_status_active'));
        $results=$this->db->get()->result_array();
        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['stockout']=$result['stockout'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['current_stock']-=$result['stockout'];
        }
        //-sales and sales return

        $this->db->from($CI->config->item('table_sales_po_details').' spd');
        $this->db->select('variety_id,pack_size_id');
        $this->db->select('SUM(quantity-quantity_return) sales');
        $this->db->join($CI->config->item('table_sales_po').' sp','sp.id =spd.sales_po_id','INNER');
        $this->db->group_by(array('variety_id','pack_size_id'));
        if(strlen($where)>0)
        {
            $this->db->where('('.$where.')');
        }
        $this->db->where('sp.status_approved',$CI->config->item('system_status_po_approval_approved'));
        $this->db->where('spd.revision',1);
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['sales']=$result['sales'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['current_stock']-=$result['sales'];
        }

        //-sales bonus and bonus return
        $this->db->from($CI->config->item('table_sales_po_details').' spd');
        $this->db->select('variety_id,bonus_pack_size_id pack_size_id');
        $this->db->select('SUM(quantity_bonus-quantity_bonus_return) sales');
        $this->db->join($CI->config->item('table_sales_po').' sp','sp.id =spd.sales_po_id','INNER');
        $this->db->group_by(array('variety_id','bonus_pack_size_id'));
        if(strlen($where_bonus)>0)
        {
            $this->db->where('('.$where_bonus.')');
        }
        $this->db->where('bonus_details_id >',0);
        $this->db->where('sp.status_approved',$CI->config->item('system_status_po_approval_approved'));
        $this->db->where('spd.revision',1);
        $results=$this->db->get()->result_array();

        foreach($results as $result)
        {
            $stocks[$result['variety_id']][$result['pack_size_id']]['sales']+=$result['sales'];
            $stocks[$result['variety_id']][$result['pack_size_id']]['current_stock']-=$result['sales'];
        }
        //-sample delivery pending
        return $stocks;

    }
    public function get_customer_current_credit($customer_id)
    {
        //0-payment+purchase-sales return
        //0-adjust-payment+purchase-sales return

        $CI = & get_instance();
        $current_credit=array('tp'=>0,'net'=>0);
        //adjust
        $this->db->from($CI->config->item('table_csetup_balance_adjust'));
        $this->db->select('SUM(amount_tp) total_tp');
        $this->db->select('SUM(amount_net) total_net');
        $this->db->where('customer_id',$customer_id);
        $this->db->where('status',$CI->config->item('system_status_active'));
        $result=$this->db->get()->row_array();
        if($result)
        {
            $current_credit['tp']-=$result['total_tp'];
            $current_credit['net']-=$result['total_net'];
        }

        //payment
        /*$this->db->from($CI->config->item('table_payment_payment'));
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
        $this->db->select('SUM(spd.variety_price*(spd.quantity-spd.quantity_return)) total_buy');
        $this->db->join($CI->config->item('table_sales_po').' sp','sp.id = spd.sales_po_id','INNER');
        $this->db->where('sp.customer_id',$customer_id);
        $this->db->where('spd.revision',1);
        $this->db->where('sp.status_approved',$CI->config->item('system_status_po_approval_approved'));
        $result=$this->db->get()->row_array();
        if($result)
        {
            $current_credit+=$result['total_buy'];//plus for relative to arm
        }*/

        return $current_credit;

    }

}