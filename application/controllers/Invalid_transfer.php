<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Invalid_transfer extends CI_Controller
{
    private  $message;
    public function __construct()
    {
        parent::__construct();
        $this->message="";
    }
    public function index()
    {
        $results=Query_helper::get_info($this->config->item('table_setup_classification_varieties'),array('id value,name text,crop_type_id'),array());
        $varieties=array();
        foreach($results as $result)
        {
            $varieties[$result['value']]=$result['text'];
        }

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
            $invalid_transfer=false;
            $remarks='';
            foreach($return as $variety_id=>$revisions)
            {
                if(sizeof($revisions)>1)
                {

                    for($i=1;$i<sizeof($revisions);$i++)
                    {
                        if($revisions[$i]<$revisions[$i+1])
                        {
                            $invalid_transfer=true;
                            $remarks.=$varieties[$variety_id].' '.(sizeof($revisions)-$i+1).'<br>';
                        }
                    }
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
        ?>

            </tbody>
        </table>
        <?php

    }

}
