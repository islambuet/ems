<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_edit_get"]=base_url($CI->controller_url."/index/edit/".$po['id']);
    $action_data["action_save"]='#save_form';
    $CI->load->view("action_buttons",$action_data);

?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_approve');?>" method="post">
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
        <div style="" class="row show-grid" id="remarks_po">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $remarks;?></label>
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
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_PRICE_PACK'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_QUANTITY_PIECES'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_WEIGHT_KG'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_TOTAL_PRICE'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_BONUS_QUANTITY_PIECES'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_BONUS_PACK_NAME'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_BONUS_WEIGHT_KG'); ?></th>

                </tr>
                </thead>
                <tbody>
                <?php
                $total_total_quantity=0;
                $total_total_weight=0;
                $total_total_price=0;
                $total_total_bonus_quantity=0;
                $total_total_bonus_weight=0;
                foreach($po_varieties as $index=>$po_variety)
                {
                    $total_total_quantity+=$po_variety['quantity'];
                    $total_total_weight+=$po_variety['pack_size']*$po_variety['quantity'];
                    $total_total_price+=$po_variety['variety_price']*$po_variety['quantity'];
                    $total_total_bonus_quantity+=$po_variety['quantity_bonus'];
                    if($po_variety['bonus_details_id']>0)
                    {
                        $total_total_bonus_weight+=$po_variety['quantity_bonus']*$po_variety['bonus_pack_size'];
                    }

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
                            <label><?php echo $po_variety['variety_price']; ?></label>
                        </td>
                        <td class="text-right">
                            <label><?php echo $po_variety['quantity']; ?></label>
                        </td>
                        <td class="text-right">
                            <label><?php echo number_format($po_variety['pack_size']*$po_variety['quantity']/1000,3,'.',''); ?></label>
                        </td>
                        <td class="text-right">
                            <label><?php echo number_format($po_variety['variety_price']*$po_variety['quantity'],2); ?></label>
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
                <tfoot>
                <tr>
                    <td class="text-right" colspan="5"><label><?php echo $CI->lang->line('LABEL_TOTAL'); ?></label></td>
                    <td class="text-right"><label><?php echo number_format($total_total_quantity,0,'.',''); ?></label></td>
                    <td class="text-right"><label><?php echo number_format($total_total_weight/1000,3,'.',''); ?></label></td>
                    <td class="text-right"><label><?php echo number_format($total_total_price,2); ?></label></td>
                    <td class="text-right"><label><?php echo number_format($total_total_bonus_quantity,0,'.',''); ?></label></td>
                    <td>&nbsp;</td>
                    <td class="text-right"><label id="total_total_bonus_weight"><?php echo number_format($total_total_bonus_weight/1000,3,'.',''); ?></label></td>
                </tr>
                <tr>
                    <td class="text-right" colspan="7"><label><?php echo $CI->lang->line('LABEL_CUSTOMER_CURRENT_CREDIT'); ?></label></td>
                    <td class="text-right"><label><?php echo number_format($customer_current_credit['tp'],2); ?></label></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <?php
                $back_color='lightgreen';
                if($po['credit_limit']<($customer_current_credit['tp']+$total_total_price))
                {
                    $back_color='red';
                }
                ?>
                <tr>
                    <td class="text-right" colspan="7"><label><?php echo $CI->lang->line('LABEL_CUSTOMER_NEW_CREDIT'); ?></label></td>
                    <td class="text-right" style="background-color: <?php echo $back_color;?>"><label><?php echo number_format($customer_current_credit['tp']+$total_total_price,2); ?></label></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td class="text-right" colspan="7"><label><?php echo $CI->lang->line('LABEL_CUSTOMER_CREDIT_LIMIT'); ?></label></td>
                    <td class="text-right"><label><?php echo number_format($po['credit_limit'],2); ?></label></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                </tfoot>
            </table>
        </div>
        <div class="widget-header">
            <div class="title">
                Stock Info
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
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_CURRENT_STOCK'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_CURRENT_STOCK_KG'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_QUANTITY_PIECES'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_WEIGHT_KG'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_NEW_STOCK'); ?></th>
                    <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_NEW_STOCK_KG'); ?></th>
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
                                <td class="text-right"><?php echo number_format($stocks_current[$variety_id][$pack_size_id]['current_stock'],0,'.','');?></td>
                                <td class="text-right"><?php echo number_format($stocks_current[$variety_id][$pack_size_id]['current_stock']*$variety['pack_size']/1000,3,'.','');?></td>
                                <td class="text-right"><?php echo $variety['quantity']; ?></td>
                                <td class="text-right"><?php echo number_format($variety['pack_size']*$variety['quantity']/1000,3,'.',''); ?></td>
                                <?php
                                $back_color='lightgreen';
                                if($stocks_current[$variety_id][$pack_size_id]['current_stock']<$variety['quantity'])
                                {
                                    $back_color='red';
                                }
                                ?>
                                <td class="text-right" style="background-color: <?php echo $back_color;?>"><?php echo number_format($stocks_current[$variety_id][$pack_size_id]['current_stock']-$variety['quantity'],0,'.','');?></td>
                                <td class="text-right" style="background-color: <?php echo $back_color;?>"><?php echo number_format(($stocks_current[$variety_id][$pack_size_id]['current_stock']-$variety['quantity'])*$variety['pack_size']/1000,3,'.','');?></td></td>
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
                Approval
            </div>
            <div class="clearfix"></div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('ACTION_APPROVE_REJECT');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status_approved" name="approval[status_approved]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <option value="<?php echo $CI->config->item('system_status_po_approval_approved');?>"><?php echo $CI->config->item('system_status_po_approval_approved');?></option>
                    <option value="<?php echo $CI->config->item('system_status_po_approval_rejected');?>"><?php echo $CI->config->item('system_status_po_approval_rejected');?></option>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" name="approval[remarks_approved]"></textarea>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        turn_off_triggers();
    });
</script>
