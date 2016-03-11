<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();

    if(isset($CI->permissions['request_approve'])&&($CI->permissions['request_approve']==1)&&($po['status_requested']==$CI->config->item('system_status_po_request_pending')))
    {
        $action_data["action_request_po_approve_get"]=base_url($CI->controller_url."/index/request_approve/".$po['id']);
    }
    if(isset($CI->permissions['edit'])&&($CI->permissions['edit']==1)&&($po['status_requested']==$CI->config->item('system_status_po_request_pending')))
    {
        $action_data["action_edit_get"]=base_url($CI->controller_url."/index/edit/".$po['id']);
    }
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
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PO_TIME_DELIVERED');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo System_helper::display_date_time($po['date_delivered']);?></label>
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
                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PO_TIME_RECEIVED');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo System_helper::display_date_time($po['date_received']);?></label>
                </div>
            </div>
        <?php
        }
        ?>
        <div class="panel-group" id="accordion">
            <?php
            $index=0;
            $revisions=array_keys($po_details);
            $max_revision=$revisions[sizeof($revisions)-1];

            foreach($po_details as $revision=>$details)
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
                                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PO_TIME_CREATED');?></label>
                            </div>
                            <div class="col-sm-4 col-xs-8">
                                <label class="control-label"><?php echo System_helper::display_date_time($details[0]['date_created']);?></label>
                            </div>
                        </div>
                        <div style="overflow-x: auto;" class="row show-grid">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?></th>
                                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                                    <th style="min-width: 80px;"><?php echo $CI->lang->line('LABEL_PACK_NAME'); ?></th>
                                    <th style="min-width: 80px;"><?php echo $CI->lang->line('LABEL_PRICE_PACK'); ?></th>
                                    <th style="min-width: 80px;"><?php echo $CI->lang->line('LABEL_QUANTITY_PIECES'); ?></th>
                                    <th style="min-width: 80px;"><?php echo $CI->lang->line('LABEL_WEIGHT_KG'); ?></th>
                                    <th style="min-width: 80px;"><?php echo $CI->lang->line('LABEL_TOTAL_PRICE'); ?></th>
                                    <th style="min-width: 80px;"><?php echo $CI->lang->line('LABEL_BONUS_QUANTITY_PIECES'); ?></th>
                                    <th style="min-width: 80px;"><?php echo $CI->lang->line('LABEL_BONUS_PACK_NAME'); ?></th>
                                    <th style="min-width: 80px;"><?php echo $CI->lang->line('LABEL_BONUS_WEIGHT_KG'); ?></th>

                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach($details as $detail)
                                {
                                    ?>
                                    <tr>
                                        <td>
                                            <label><?php echo $detail['crop_name']; ?></label>
                                        </td>
                                        <td>
                                            <label><?php echo $detail['crop_type_name']; ?></label>
                                        </td>
                                        <td>
                                            <label><?php echo $detail['variety_name']; ?></label>
                                        </td>
                                        <td>
                                            <label><?php echo $detail['pack_size']; ?></label>
                                        </td>
                                        <td class="text-right">
                                            <label><?php echo $detail['variety_price']; ?></label>
                                        </td>
                                        <td class="text-right">
                                            <label><?php echo $detail['quantity']; ?></label>
                                        </td>
                                        <td class="text-right">
                                            <label><?php echo number_format($detail['pack_size']*$detail['quantity']/1000,3,'.',''); ?></label>
                                        </td>
                                        <td class="text-right">
                                            <label><?php echo number_format($detail['variety_price']*$detail['quantity'],2); ?></label>
                                        </td>
                                        <td class="text-right">
                                            <label><?php echo $detail['quantity_bonus']; ?></label>
                                        </td>
                                        <td class="text-right">
                                            <label><?php if($detail['bonus_details_id']>0){echo $detail['bonus_pack_size'];}else{echo 'N/A';} ?></label>
                                        </td>
                                        <td class="text-right">
                                            <label><?php echo number_format($detail['quantity_bonus']*$detail['bonus_pack_size']/1000,3,'.',''); ?></label>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <div class="clearfix"></div>

<script type="text/javascript">

    jQuery(document).ready(function()
    {
        turn_off_triggers();
    });
</script>
