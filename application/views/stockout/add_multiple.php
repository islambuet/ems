<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_data=array();
$action_data["action_back"]=base_url($CI->controller_url);
$action_data["action_save"]='#save_form';
$action_data["action_save_new"]='#save_form';
$action_data["action_clear"]='#save_form';
$CI->load->view("action_buttons",$action_data);
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_multiple');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $stock_out['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <input type="hidden" name="stock_out[warehouse_id]" value="<?php echo $stock_out['warehouse_id']; ?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
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
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_STOCK_OUT_PURPOSE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="purpose" name="stock_out[purpose]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <option value="<?php echo $CI->config->item('system_purpose_short'); ?>"><?php echo $CI->lang->line('LABEL_STOCK_OUT_PURPOSE_SHORT');?></option>
                    <option value="<?php echo $CI->config->item('system_purpose_rnd'); ?>"><?php echo $CI->lang->line('LABEL_STOCK_OUT_PURPOSE_RND');?></option>
                    <option value="<?php echo $CI->config->item('system_purpose_customer'); ?>"><?php echo $CI->lang->line('LABEL_STOCK_OUT_PURPOSE_CUSTOMER');?></option>
                    <option value="<?php echo $CI->config->item('system_purpose_demonstration'); ?>"><?php echo $CI->lang->line('LABEL_STOCK_OUT_PURPOSE_DEMONSTRATION');?></option>
                </select>
            </div>
        </div>
        <div style="display: none;" class="row show-grid" id="division_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="division_id" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($divisions as $division)
                    {?>
                        <option value="<?php echo $division['value']?>"><?php echo $division['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="display: none;" class="row show-grid" id="zone_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="zone_id" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                </select>
            </div>
        </div>
        <div style="display: none;" class="row show-grid" id="territory_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="territory_id" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                </select>

            </div>
        </div>
        <div style="display: none;" class="row show-grid" id="district_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="district_id" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                </select>

            </div>
        </div>
        <div style="display: none;" class="row show-grid" id="customer_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CUSTOMER_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="customer_id" name="stock_out[customer_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                </select>

            </div>
        </div>
        <div class="row show-grid" style="display: none;" id="customer_name_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CUSTOMER_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="stock_out[customer_name]" id="customer_name" class="form-control" value=""/>
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
        <div style="overflow-x: auto;" class="row show-grid" id="order_items_container">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_PACK_NAME'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CURRENT_STOCK'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_QUANTITY_PIECES'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('ACTION'); ?></th>

                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

        </div>
        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4">
                <button type="button" class="btn btn-warning system_button_add_more" data-current-id="1"><?php echo $CI->lang->line('LABEL_ADD_MORE');?></button>
            </div>
            <div class="col-xs-4">

            </div>
        </div>





    </div>

    <div class="clearfix"></div>
</form>
<div id="system_content_add_more" style="display: none;">
    <table>
        <tbody>
        <tr>
            <td>
                <select class="form-control crop_id">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($crops as $crop)
                    {?>
                        <option value="<?php echo $crop['value']?>"><?php echo $crop['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </td>
            <td>
                <div style="display: none;" class="crop_type_id_container">
                    <select class="form-control crop_type_id">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    </select>
                </div>
            </td>
            <td>
                <div style="display: none;" class="variety_id_container">
                    <select class="form-control variety_id">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    </select>
                </div>
            </td>
            <td>
                <div style="display: none;" class="pack_size_id_container">
                    <select class="form-control pack_size_id">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    </select>
                </div>
            </td>
            <td class="text-right">
                <label class="stock_current">&nbsp;</label>
            </td>
            <td class="text-right">
                <input type="text"class="form-control text-right quantity integer_type_positive" value=""/>
            </td>

            <td><button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button></td>
        </tr>
        </tbody>
    </table>
</div>
<script type="text/javascript">

jQuery(document).ready(function()
{
    turn_off_triggers();
    $(".datepicker").datepicker({dateFormat : display_date_format});
    $(".datelarge").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "-20:+20"});
    $(document).on("change","#purpose",function()
    {

        $("#customer_name").val("");
        $("#division_id").val("");
        $("#zone_id").val("");
        $("#territory_id").val("");
        $("#district_id").val("");
        $("#customer_id").val("");
        var purpose=$('#purpose').val();
        if(purpose=='<?php echo $CI->config->item('system_purpose_customer'); ?>')
        {
            $('#customer_name_container').show();
            $('#division_id_container').show();
            $('#zone_id_container').hide();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#customer_id_container').hide();
        }
        else
        {
            $('#customer_name_container').hide();
            $('#division_id_container').hide();
            $('#zone_id_container').hide();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#customer_id_container').hide();
        }
    });
    $(document).on("change","#division_id",function()
    {
        $('#customer_name_container').show();
        $("#customer_name").val("");
        $("#zone_id").val("");
        $("#territory_id").val("");
        $("#district_id").val("");
        $("#customer_id").val("");
        var division_id=$('#division_id').val();
        if(division_id>0)
        {
            $('#zone_id_container').show();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#customer_id_container').hide();
            $.ajax({
                url: base_url+"common_controller/get_dropdown_zones_by_divisionid/",
                type: 'POST',
                datatype: "JSON",
                data:{division_id:division_id},
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
            $('#zone_id_container').hide();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#customer_id_container').hide();
        }
    });
    $(document).on("change","#zone_id",function()
    {
        $('#customer_name_container').show();
        $("#customer_name").val("");
        $("#territory_id").val("");
        $("#district_id").val("");
        $("#customer_id").val("");
        var zone_id=$('#zone_id').val();
        if(zone_id>0)
        {
            $('#territory_id_container').show();
            $('#district_id_container').hide();
            $('#customer_id_container').hide();
            $.ajax({
                url: base_url+"common_controller/get_dropdown_territories_by_zoneid/",
                type: 'POST',
                datatype: "JSON",
                data:{zone_id:zone_id},
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
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#customer_id_container').hide();
        }
    });
    $(document).on("change","#territory_id",function()
    {
        $('#customer_name_container').show();
        $("#customer_name").val("");
        $("#district_id").val("");
        $("#customer_id").val("");
        var territory_id=$('#territory_id').val();
        if(territory_id>0)
        {
            $('#district_id_container').show();
            $('#customer_id_container').hide();
            $.ajax({
                url: base_url+"common_controller/get_dropdown_districts_by_territoryid/",
                type: 'POST',
                datatype: "JSON",
                data:{territory_id:territory_id},
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
            $('#customer_id_container').hide();
            $('#district_id_container').hide();
        }
    });
    $(document).on("change","#district_id",function()
    {
        $('#customer_name_container').show();
        $("#customer_name").val("");
        $("#customer_id").val("");
        var district_id=$('#district_id').val();
        if(district_id>0)
        {
            $('#customer_id_container').show();
            $.ajax({
                url: base_url+"common_controller/get_dropdown_customers_by_districtid/",
                type: 'POST',
                datatype: "JSON",
                data:{district_id:district_id},
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
            $('#customer_id_container').hide();
        }
    });
    $(document).on("change","#customer_id",function()
    {
        var customer_id=$('#customer_id').val();
        if(customer_id>0)
        {
            $("#customer_name").val($("#customer_id :selected").text());
            $('#customer_name_container').hide();
        }
        else
        {
            $('#customer_name_container').show();
        }
    });
    $(document).on("click", ".system_button_add_more", function(event)
    {
        var current_id=parseInt($(this).attr('data-current-id'));
        current_id=current_id+1;
        $(this).attr('data-current-id',current_id);
        var content_id='#system_content_add_more table tbody';
        $(content_id+' .crop_id').attr('id','crop_id_'+current_id);
        $(content_id+' .crop_id').attr('data-current-id',current_id);

        $(content_id+' .crop_type_id').attr('id','crop_type_id_'+current_id);
        $(content_id+' .crop_type_id').attr('data-current-id',current_id);
        $(content_id+' .crop_type_id_container').attr('id','crop_type_id_container_'+current_id);

        $(content_id+' .variety_id').attr('id','variety_id_'+current_id);
        $(content_id+' .variety_id').attr('data-current-id',current_id);
        $(content_id+' .variety_id').attr('name','items['+current_id+'][variety_id]');
        $(content_id+' .variety_id_container').attr('id','variety_id_container_'+current_id);

        $(content_id+' .pack_size_id').attr('id','pack_size_id_'+current_id);
        $(content_id+' .pack_size_id').attr('data-current-id',current_id);
        $(content_id+' .pack_size_id').attr('name','items['+current_id+'][pack_size_id]');
        $(content_id+' .pack_size_id_container').attr('id','pack_size_id_container_'+current_id);

        $(content_id+' .stock_current').attr('id','stock_current_'+current_id);
        $(content_id+' .stock_current').attr('data-current-id',current_id);

        $(content_id+' .quantity').attr('id','quantity_'+current_id);
        $(content_id+' .quantity').attr('data-current-id',current_id);
        $(content_id+' .quantity').attr('name','items['+current_id+'][quantity]');

        var html=$(content_id).html();
        $("#order_items_container tbody").append(html);
        $(content_id+' .crop_id').removeAttr('id');
        $(content_id+' .crop_type_id').removeAttr('id');
        $(content_id+' .crop_type_id_container').removeAttr('id');
        $(content_id+' .variety_id').removeAttr('id');
        $(content_id+' .variety_id_container').removeAttr('id');
        $(content_id+' .pack_size_id').removeAttr('id');
        $(content_id+' .pack_size_id_container').removeAttr('id');
        $(content_id+' .stock_current').removeAttr('id');
        $(content_id+' .quantity').removeAttr('id');

    });
    $(document).on("click", ".system_button_add_delete", function(event)
    {
        $(this).closest('tr').remove();

    });
    $(document).on("change",".crop_id",function()
    {

        var active_id=parseInt($(this).attr('data-current-id'));
        $("#crop_type_id_"+active_id).val("");
        $("#variety_id_"+active_id).val("");
        $("#pack_size_id_"+active_id).val("");
        $("#stock_current_"+active_id).html("");
        $("#quantity_"+active_id).val("");
        var crop_id=$('#crop_id_'+active_id).val();

        $('#variety_id_container_'+active_id).hide();
        $('#pack_size_id_container_'+active_id).hide();
        if(crop_id>0)
        {
            $('#crop_type_id_container_'+active_id).show();
            $.ajax({
                url: base_url+"common_controller/get_dropdown_croptypes_by_cropid/",
                type: 'POST',
                datatype: "JSON",
                data:{crop_id:crop_id,html_container_id:'#crop_type_id_'+active_id},
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
            $('#crop_type_id_container_'+active_id).hide();

        }
    });
    $(document).on("change",".crop_type_id",function()
    {
        var active_id=parseInt($(this).attr('data-current-id'));

        $("#variety_id_"+active_id).val("");
        $("#pack_size_id_"+active_id).val("");
        $("#stock_current_"+active_id).html("");
        $("#quantity_"+active_id).val("");
        $('#pack_size_id_container_'+active_id).hide();
        var crop_type_id=$('#crop_type_id_'+active_id).val();
        if(crop_type_id>0)
        {
            $('#variety_id_container_'+active_id).show();

            $.ajax({
                url: base_url+"common_controller/get_dropdown_armvarieties_by_croptypeid/",
                type: 'POST',
                datatype: "JSON",
                data:{crop_type_id:crop_type_id,html_container_id:'#variety_id_'+active_id},
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
            $('#variety_id_container_'+active_id).hide();
        }
    });
    $(document).on("change",".variety_id",function()
    {
        var active_id=parseInt($(this).attr('data-current-id'));
        $("#pack_size_id_"+active_id).val("");
        $("#stock_current_"+active_id).html("");
        $("#quantity_"+active_id).val("");
        var variety_id=$('#variety_id_'+active_id).val();
        var warehouse_id=$('#warehouse_id').val();

        if(variety_id>0)
        {
            $('#pack_size_id_container_'+active_id).show();
            $.ajax({
                url: base_url+"common_controller/get_dropdown_packsizes_by_variety_warehouse/",
                type: 'POST',
                datatype: "JSON",
                data:{variety_id:variety_id,warehouse_id:warehouse_id,html_container_id:'#pack_size_id_'+active_id},
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
            $('#pack_size_id_container_'+active_id).hide();
        }
    });
    $(document).on("change",".pack_size_id",function()
    {
        var active_id=parseInt($(this).attr('data-current-id'));
        $("#stock_current_"+active_id).html("");

        var variety_id=$('#variety_id_'+active_id).val();
        var pack_size_id=$('#pack_size_id_'+active_id).val();
        $("#quantity_"+active_id).val("");
        if(variety_id>0 && pack_size_id>0)
        {
            $.ajax({
                url: base_url+"common_controller/get_dropdown_curent_stock_by_variety_pack_size_id/",
                type: 'POST',
                datatype: "JSON",
                data:{variety_id:variety_id,pack_size_id:pack_size_id,html_container_id:'#stock_current_'+active_id},
                success: function (data, status)
                {

                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });
        }
    });

});
</script>
