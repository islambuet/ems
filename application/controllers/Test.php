<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller
{
    private  $message;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
    }
    public function index()
    {
        /*$this->db->from($this->config->item('table_stockin_varieties').' stv');
        $this->db->select('variety_id,pack_size_id,remarks');
        $this->db->select('(quantity) stock_in');
        $this->db->where('stv.variety_id',3);
        $results=$this->db->get()->result_array();*/

        /*$this->db->from($this->config->item('table_sales_po_details').' spd');
        $this->db->select('spd.sales_po_id,spd.variety_id,spd.pack_size_id');
        $this->db->select('(spd.quantity) sales');
        $this->db->join($this->config->item('table_sales_po').' sp','sp.id =spd.sales_po_id','INNER');



        $this->db->where('sp.status_approved',$this->config->item('system_status_po_approval_approved'));
        $this->db->where('spd.revision',1);
        $this->db->where('spd.variety_id',3);
        $results=$this->db->get()->result_array();
        */

        //sales return
        /*$time=System_helper::get_time('02-Aug-2017')-1;
        $this->db->from($this->config->item('table_sales_po_details').' spd');
        $this->db->select('variety_id,pack_size_id,spd.sales_po_id');
        $this->db->select('(quantity_return) sales_return');
        $this->db->join($this->config->item('table_sales_po').' sp','sp.id =spd.sales_po_id','INNER');
        $this->db->where('spd.variety_id',3);
        $this->db->where('sp.status_received',$this->config->item('system_status_po_received_received'));
        $this->db->where('spd.revision',1);
        $this->db->where('quantity_return >',0);
        $this->db->where('spd.date_return <=',$time);
        $results=$this->db->get()->result_array();*/
        $this->db->from($this->config->item('table_sales_po_returns').' ret');
        $this->db->select('sd.variety_id,ret.quantity_return,ret.sales_po_id,ret.revision');
        $this->db->join($this->config->item('table_sales_po_details').' sd','sd.id =ret.sales_po_detail_id','INNER');
        $this->db->order_by('ret.sales_po_id','DESC');
        $this->db->order_by('sd.variety_id','ASC');
        $this->db->order_by('ret.revision','ASC');
        $results=$this->db->get()->result_array();
        $returns=array();
        foreach($results as $result)
        {
            $returns[$result['sales_po_id']][$result['variety_id']][$result['revision']]=$result['quantity_return'];
        }
        ?>
        <table border="1">
            <thead>
                <th style="width: 100px;">Count</th>
                <th style="width: 100px;">PO NO</th>
                <th style="width: 300px">Remarks</th>
            </thead>
            <tbody>

        <?php
        $count=0;
        foreach($returns as $po_id=>$return)
        {

            foreach($return as $variety_id=>$revisions)
            {
                if(sizeof($revisions)>1)
                {
                    $invalid_transfer=false;
                    $remarks='';
                    for($i=1;$i<sizeof($revisions);$i++)
                    {
                        if($revisions[$i]>$revisions[$i+1])
                        {
                            $invalid_transfer=true;
                            $remarks.=$variety_id.' '.(sizeof($revisions)-$i+1).'<br>';
                        }
                    }
                    if($invalid_transfer)
                    {
                        ?>
                        <tr>
                            <td style="text-align: right"><?php echo ++$count?></td>
                            <td style="text-align: right"><?php echo $po_id?></td>
                            <td><?php echo $remarks;?></td>
                        </tr>
                        <?php
                    }
                }
            }

        }
        ?>

            </tbody>
        </table>
        <?php

    }

}
