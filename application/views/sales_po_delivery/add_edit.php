<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_save"]='#save_form';
    $CI->load->view("action_buttons",$action_data);

?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $po['id']; ?>" />
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
        <div class="widget-header">
            <div class="title">
                Delivery Info
            </div>
            <div class="clearfix"></div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_DELIVERY');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="delivery[date_delivery]" id="date_delivery" class="form-control datepicker" value="<?php echo System_helper::display_date($delivery_info['date_delivery']);?>"/>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_INVOICE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="delivery[date_invoice]" id="date_invoice" class="form-control datepicker" value="<?php if($delivery_info['date_invoice']>0){echo System_helper::display_date($delivery_info['date_invoice']);};?>"/>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_INVOICE_NO');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="delivery[invoice_no]" id="invoice_no" class="form-control" value="<?php echo $delivery_info['invoice_no'];?>"/>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NAME_COURIER');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="courier_id" class="form-control" name="delivery[courier_id]">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <?php
                    foreach($couriers as $courier)
                    {?>
                        <option value="<?php echo $courier['value']?>" <?php if($courier['value']==$delivery_info['courier_id']){ echo "selected";}?>><?php echo $courier['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_COURIER_TRACK_NO');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="delivery[track_no]" id="track_no" class="form-control" value="<?php echo $delivery_info['track_no'];?>"/>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_BOOKING');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="delivery[date_booking]" id="date_booking" class="form-control datepicker" value="<?php if($delivery_info['date_booking']>0){echo System_helper::display_date($delivery_info['date_booking']);}?>"/>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" name="delivery[remarks]"><?php echo $delivery_info['remarks']; ?></textarea>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        turn_off_triggers();
        $(".datepicker").datepicker({dateFormat : display_date_format});
    });
</script>
