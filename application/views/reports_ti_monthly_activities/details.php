<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-left"><?php echo $this->lang->line('LABEL_MONTH');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php if($item_details['month_id']){echo date('F', mktime(0, 0, 0, $item_details['month_id'], 1));} ?>
        </div>
    </div>

    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-left"><?php echo $this->lang->line('LABEL_PREVIOUS_MONTH_ACHIEVEMENT');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item_details['achievement']; ?>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-left"><?php echo $this->lang->line('LABEL_SUCCESSFULLY_WORK_DONE');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item_details['work_done']; ?>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-left"><?php echo $this->lang->line('LABEL_NEXT_MONTHS_CROP_VARIETY');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item_details['next_month_crop_variety']; ?>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-left"><?php echo $this->lang->line('LABEL_SELF_TARGET').' (Amount)';?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item_details['amount_self_target']; ?>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-left"><?php echo $this->lang->line('LABEL_SELF_TARGET').' (Reason)';?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item_details['reason_self_target']; ?>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-left"><?php echo $this->lang->line('LABEL_SELF_MARKING').' (Out of 10)';?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item_details['value_marking']; ?>
        </div>
    </div>
    <div class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-left"><?php echo $this->lang->line('LABEL_SELF_MARKING').' (Reason)';?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $item_details['reason_marking']; ?>
        </div>
    </div>
</div>



<div class="clearfix"></div>