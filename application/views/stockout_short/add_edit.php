<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_save"]='#save_form';
    $action_data["action_save_new"]='#save_form';
    $action_data["action_clear"]='#save_form';
    $CI->load->view("action_buttons",$action_data);
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $stock_out['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div style="" class="row show-grid" id="warehouse_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($stock_out['id']>0)
                {
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
                <?php
                }
                else
                {
                    ?>
                    <select id="warehouse_id" name="stock_out[warehouse_id]" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        foreach($warehouses as $warehouse)
                        {?>
                            <option value="<?php echo $warehouse['value']?>"><?php echo $warehouse['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>
        <div style="<?php if(!($stock_out['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="crop_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($stock_out['id']>0)
                {
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
                <?php
                }
                else
                {
                    ?>
                    <select id="crop_id" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>
        <div style="<?php if(!($stock_out['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="crop_type_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($stock_out['id']>0)
                {
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
                <?php
                }
                else
                {
                    ?>
                    <select id="crop_type_id" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    </select>
                <?php
                }
                ?>

            </div>
        </div>
        <div style="<?php if(!($stock_out['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="variety_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($stock_out['id']>0)
                {
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
                <?php
                }
                else
                {
                    ?>
                    <select id="variety_id" name="stock_out[variety_id]" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    </select>
                <?php
                }
                ?>

            </div>
        </div>
        <div style="<?php if(!($stock_out['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="pack_size_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PACK_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($stock_out['id']>0)
                {
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
                <?php
                }
                else
                {
                    ?>
                    <select id="pack_size_id" name="stock_out[pack_size_id]" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    </select>
                <?php
                }
                ?>

            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_QUANTITY_PIECES');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="stock_out[quantity]" id="quantity" class="form-control" value="<?php echo $stock_out['quantity'];?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_STOCK_OUT');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="stock_out[date_stock_out]" id="date_stock_out" class="form-control datepicker" value="<?php echo System_helper::display_date($stock_out['date_stock_out']);?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" id="remarks" name="stock_out[remarks]"><?php echo $stock_out['remarks']; ?></textarea>
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
        $(".datelarge").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "-20:+20"});
        $(document).on("change","#warehouse_id",function()
        {
            $("#crop_id").val("");
            $("#crop_type_id").val("");
            $("#variety_id").val("");
            $("#pack_size_id").val("");
            var warehouse_id=$('#warehouse_id').val();
            if(warehouse_id>0)
            {
                $('#crop_id_container').show();
                $('#crop_type_id_container').hide();
                $('#variety_id_container').hide();
                $('#pack_size_id_container').hide();
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_crops_by_warehouseid/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{warehouse_id:warehouse_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#crop_id_container').hide();
                $('#crop_type_id_container').hide();
                $('#variety_id_container').hide();
                $('#pack_size_id_container').hide();
            }
        });
        $(document).on("change","#crop_id",function()
        {
            $("#crop_type_id").val("");
            $("#variety_id").val("");
            $("#pack_size_id").val("");
            var crop_id=$('#crop_id').val();
            if(crop_id>0)
            {
                $('#crop_type_id_container').show();
                $('#variety_id_container').hide();
                $('#pack_size_id_container').hide();
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_croptypes_by_cropid/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{crop_id:crop_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#crop_type_id_container').hide();
                $('#variety_id_container').hide();
                $('#pack_size_id_container').hide();
            }
        });
        $(document).on("change","#crop_type_id",function()
        {

            $("#variety_id").val("");
            $("#pack_size_id").val("");
            var crop_type_id=$('#crop_type_id').val();
            if(crop_type_id>0)
            {
                $('#variety_id_container').show();
                $('#pack_size_id_container').hide();
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_armvarieties_by_croptypeid/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{crop_type_id:crop_type_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#variety_id_container').hide();
                $('#pack_size_id_container').hide();
            }
        });
        $(document).on("change","#variety_id",function()
        {
            $("#pack_size_id").val("");
            var variety_id=$('#variety_id').val();
            var warehouse_id=$('#warehouse_id').val();
            if(variety_id>0)
            {
                $('#pack_size_id_container').show();
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_packsizes_by_variety_warehouse/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{variety_id:variety_id,warehouse_id:warehouse_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#pack_size_id_container').hide();
            }
        });

    });
</script>
