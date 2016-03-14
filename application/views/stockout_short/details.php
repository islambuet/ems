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
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                $warehouse_name='';
                foreach($warehouses as $warehouse)
                {
                    if($warehouse['value']==$stock_out['warehouse_id'])
                    {
                        $warehouse_name=$warehouse['text'];
                    }
                }
                ?>
                <label class="control-label"><?php echo $warehouse_name;;?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                $crop_name='';
                foreach($crops as $crop)
                {
                    if($crop['value']==$stock_out['crop_id'])
                    {
                        $crop_name=$crop['text'];
                    }
                }
                ?>
                <label class="control-label"><?php echo $crop_name;;?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                $crop_type_name='';
                foreach($crop_types as $crop_type)
                {
                    if($crop_type['value']==$stock_out['crop_type_id'])
                    {
                        $crop_type_name=$crop_type['text'];
                    }
                }
                ?>
                <label class="control-label"><?php echo $crop_type_name;;?></label>

            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                $variety_name='';
                foreach($varieties as $variety)
                {
                    if($variety['value']==$stock_out['variety_id'])
                    {
                        $variety_name=$variety['text'];
                    }
                }
                ?>
                <label class="control-label"><?php echo $variety_name;;?></label>

            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PACK_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                $pack_size_name='';
                foreach($pack_sizes as $pack_size)
                {
                    if($pack_size['value']==$stock_out['pack_size_id'])
                    {
                        $pack_size_name=$pack_size['text'];
                    }
                }
                ?>
                <label class="control-label"><?php echo $pack_size_name;;?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_QUANTITY_PIECES');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $stock_out['quantity'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_STOCK_OUT');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date($stock_out['date_stock_out']);?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $stock_out['remarks'];?></label>
            </div>
        </div>

    </div>

    <div class="clearfix"></div>

<script type="text/javascript">

    jQuery(document).ready(function()
    {
        turn_off_triggers();
    });
</script>
