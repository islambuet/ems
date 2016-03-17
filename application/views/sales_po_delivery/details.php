<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $CI->load->view("action_buttons",$action_data);

?>


    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $po['division_name'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $po['zone_name'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $po['territory_name'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $po['district_name'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CUSTOMER_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $po['customer_name'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_PO');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date($po['date_po']);?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PO_TIME_CREATED');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date_time($po['date_created']);?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PO_USER_CREATED');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $users[$po['user_created']]['name'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REQUESTED');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $po['status_requested'];?></label>
            </div>
        </div>
        <?php
        if($po['status_requested']==$CI->config->item('system_status_po_request_requested'))
        {
            ?>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PO_TIME_REQUESTED');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo System_helper::display_date_time($po['date_requested']);?></label>
                </div>
            </div>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PO_USER_REQUESTED');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $users[$po['user_requested']]['name'];?></label>
                </div>
            </div>
        <?php
        }
        ?>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_APPROVAL');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $po['status_approved'];?></label>
            </div>
        </div>
        <?php
        if(($po['status_approved']==$CI->config->item('system_status_po_approval_approved'))||($po['status_approved']==$CI->config->item('system_status_po_approval_rejected')))
        {
            ?>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PO_TIME_APPROVED');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo System_helper::display_date_time($po['date_approved']);?></label>
                </div>
            </div>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PO_USER_APPROVED');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $users[$po['user_approved']]['name'];?></label>
                </div>
            </div>
        <?php
        }
        ?>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DELIVERY');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $po['status_delivered'];?></label>
            </div>
        </div>
        <?php
        if($po['status_delivered']==$CI->config->item('system_status_po_delivery_delivered'))
        {
            ?>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_DELIVERY');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo System_helper::display_date($po['date_delivery']);?></label>
                </div>
            </div>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PO_TIME_DELIVERED');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo System_helper::display_date_time($po['date_delivered']);?></label>
                </div>
            </div>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PO_USER_DELIVERED');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $users[$po['user_delivered']]['name'];?></label>
                </div>
            </div>
        <?php
        }
        ?>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_RECEIVED');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $po['status_received'];?></label>
            </div>
        </div>
        <?php
        if($po['status_received']==$CI->config->item('system_status_po_received_received'))
        {
            ?>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_RECEIVED');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo System_helper::display_date($po['date_receive']);?></label>
                </div>
            </div>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PO_TIME_RECEIVED');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo System_helper::display_date_time($po['date_received']);?></label>
                </div>
            </div>
            <div style="" class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PO_USER_RECEIVED');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo $users[$po['user_received']]['name'];?></label>
                </div>
            </div>
        <?php
        }
        ?>
        <div style="" class="row show-grid" id="warehouse_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $po['warehouse_name'];?></label>
            </div>
        </div>
        <div class="widget-header">
            <div class="title">
                Order Items
            </div>
            <div class="clearfix"></div>
        </div>

        <div style="overflow-x: auto;" class="row show-grid" id="order_items_container">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_PACK_NAME'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_QUANTITY_PIECES'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_WEIGHT_KG'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_BONUS_QUANTITY_PIECES'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_BONUS_PACK_NAME'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_BONUS_WEIGHT_KG'); ?></th>

                </tr>
                </thead>
                <tbody>
                <?php

                foreach($po_varieties as $index=>$po_variety)
                {

                    ?>
                    <tr>
                        <td>
                            <label><?php echo $po_variety['crop_name']; ?></label>
                        </td>
                        <td>
                            <label><?php echo $po_variety['crop_type_name']; ?></label>
                        </td>
                        <td>
                            <label><?php echo $po_variety['variety_name']; ?></label>
                        </td>
                        <td>
                            <label><?php echo $po_variety['pack_size']; ?></label>
                        </td>

                        <td class="text-right">
                            <label><?php echo $po_variety['quantity']; ?></label>
                        </td>
                        <td class="text-right">
                            <label><?php echo number_format($po_variety['pack_size']*$po_variety['quantity']/1000,3,'.',''); ?></label>
                        </td>
                        <td class="text-right">
                            <label><?php echo $po_variety['quantity_bonus']; ?></label>
                        </td>
                        <td class="text-right">
                            <label><?php if($po_variety['bonus_details_id']>0){echo $po_variety['bonus_pack_size'];}else{echo 'N/A';} ?></label>
                        </td>
                        <td class="text-right">
                            <label><?php echo number_format($po_variety['quantity_bonus']*$po_variety['bonus_pack_size']/1000,3,'.',''); ?></label>
                        </td>
                    </tr>
                <?php
                }
                ?>

                </tbody>
            </table>
        </div>
        <div class="widget-header">
            <div class="title">
                Vareity Info
            </div>
            <div class="clearfix"></div>
        </div>
        <?php
        //echo '<PRE>';
        //print_r($stocks_current);
        //echo '</PRE>';
        ?>
        <div style="overflow-x: auto;" class="row show-grid">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_PACK_NAME'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_QUANTITY_PIECES'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_WEIGHT_KG'); ?></th>
                </tr>
                </thead>
                <tbody>

                    <?php
                    foreach($customer_varieties_quantity as $variety_id=>$v)
                    {
                        foreach($v as $pack_size_id=>$variety)
                        {
                            ?>
                            <tr>
                                <td><?php echo $variety['crop_name']; ?></td>
                                <td><?php echo $variety['crop_type_name']; ?></td>
                                <td><?php echo $variety['variety_name']; ?></td>
                                <td class="text-right"><?php echo $variety['pack_size']; ?></td>
                                <td class="text-right"><?php echo $variety['quantity']; ?></td>
                                <td class="text-right"><?php echo number_format($variety['pack_size']*$variety['quantity']/1000,3,'.',''); ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
        if($po['status_delivered']==$CI->config->item('system_status_po_delivery_delivered'))
        {
            ?>
            <div class="panel-group" id="accordion">
                <?php
                $index=0;
                $revisions=array_keys($delivery_details);
                $max_revision=$revisions[sizeof($revisions)-1];


                foreach($delivery_details as $revision=>$details)
                {
                    $index++;
                    ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle external" data-toggle="collapse"  data-target="#collapse_<?php echo $index; ?>" href="#">
                                    Revision : <?php echo ($max_revision-$revision+1);if($index==1){echo '(Latest Revision)';} ?></a>
                            </h4>
                        </div>
                        <div id="collapse_<?php echo $index; ?>" class="panel-collapse collapse <?php if($index==1){echo 'in';} ?>">
                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PO_TIME_DELIVERED');?></label>
                                </div>
                                <div class="col-sm-4 col-xs-8">
                                    <label class="control-label"><?php echo System_helper::display_date_time($details['date_created']);?></label>
                                </div>
                            </div>
                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PO_USER_DELIVERED');?></label>
                                </div>
                                <div class="col-sm-4 col-xs-8">
                                    <label class="control-label"><?php echo $users[$details['user_created']]['name'];?></label>
                                </div>
                            </div>
                            <div style="" class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_DELIVERY');?></label>
                                </div>
                                <div class="col-sm-4 col-xs-8">
                                    <label class="control-label"><?php echo System_helper::display_date($details['date_delivery']);?></label>
                                </div>
                            </div>
                            <div style="" class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_INVOICE');?></label>
                                </div>
                                <div class="col-sm-4 col-xs-8">
                                    <label class="control-label"><?php if($details['date_invoice']>0){echo System_helper::display_date($details['date_invoice']);};?></label>
                                </div>
                            </div>
                            <div style="" class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_INVOICE_NO');?></label>
                                </div>
                                <div class="col-sm-4 col-xs-8">
                                    <label class="control-label"><?php echo $details['invoice_no'];?></label>
                                </div>
                            </div>
                            <div style="" class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NAME_COURIER');?></label>
                                </div>
                                <div class="col-sm-4 col-xs-8">

                                    <?php
                                    $courier_name='';
                                    foreach($couriers as $courier)
                                    {

                                        if($courier['value']==$details['courier_id'])
                                        {
                                            $courier_name=$courier['text'];
                                        }
                                    }
                                    ?>
                                    <label class="control-label"><?php echo $courier_name;?></label>
                                </div>
                            </div>
                            <div style="" class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_COURIER_TRACK_NO');?></label>
                                </div>
                                <div class="col-sm-4 col-xs-8">
                                    <label class="control-label"><?php echo $details['track_no'];?></label>
                                </div>
                            </div>
                            <div style="" class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_BOOKING');?></label>
                                </div>
                                <div class="col-sm-4 col-xs-8">
                                    <label class="control-label"><?php if($details['date_booking']>0){echo System_helper::display_date($details['date_booking']);}?></label>
                                </div>
                            </div>
                            <div style="" class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
                                </div>
                                <div class="col-sm-4 col-xs-8">
                                    <label class="control-label"><?php echo $details['remarks'];?></label>
                                </div>
                            </div>

                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        <?php
        }
        ?>
    </div>

    <div class="clearfix"></div>

<script type="text/javascript">
    jQuery(document).ready(function()
    {
        turn_off_triggers();
        $(".datepicker").datepicker({dateFormat : display_date_format});
    });
</script>
