<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_save"]='#save_form';
    $action_data["action_save_new"]='#save_form';
    $action_data["action_clear"]='#save_form';
    $CI->load->view("action_buttons",$action_data);
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_unfilled');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $fsetup['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['division_name'];?></label>
            </div>
        </div>

        <div class="row show-grid" id="zone_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['zone_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="territory_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['territory_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="district_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['district_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="upazilla_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['upazilla_name'];?></label>
                <input type="hidden" id="upazilla_id" name="fsetup[upazilla_id]" value="<?php echo $fsetup['upazilla_id']; ?>">
            </div>
        </div>
        <div class="row show-grid" id="crop_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['crop_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="crop_type_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['crop_type_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="variety_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['variety_name'];?></label>
                <input type="hidden" id="variety_id" name="fsetup[variety_id]" value="<?php echo $fsetup['variety_id']; ?>">
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Farmer's Name<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['name'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ADDRESS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="fsetup[address]" class="form-control"><?php echo $fsetup['address']; ?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Contact no</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="fsetup[contact_no]" class="form-control" value="<?php echo $fsetup['contact_no']; ?>">
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_SOWING');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date($fsetup['date_sowing']); ?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_TRANSPLANT');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="fsetup[date_transplant]" class="form-control datepicker" value="<?php if($fsetup['date_transplant']>0){echo System_helper::display_date($fsetup['date_transplant']); }?>">
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NUM_PICTURE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['num_picture'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_INTERVAL');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['interval'];?></label>
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
    $(document).on("change","#division_id",function()
    {
        $("#zone_id").val("");
        $("#territory_id").val("");
        $("#district_id").val("");
        $("#upazilla_id").val("");
        $("#crop_id").val("");
        $("#crop_type_id").val("");
        $("#variety_id").val("");
        $('#territory_id_container').hide();
        $('#district_id_container').hide();
        $('#upazilla_id_container').hide();
        $('#crop_id_container').hide();
        $('#crop_type_id_container').hide();
        $('#variety_id_container').hide();
        var division_id=$('#division_id').val();
        if(division_id>0)
        {
            $('#zone_id_container').show();
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
        }
    });
    $(document).on("change","#zone_id",function()
    {

        $("#territory_id").val("");
        $("#district_id").val("");
        $("#upazilla_id").val("");
        $("#crop_id").val("");
        $("#crop_type_id").val("");
        $("#variety_id").val("");
        $('#district_id_container').hide();
        $('#upazilla_id_container').hide();
        $('#crop_id_container').hide();
        $('#crop_type_id_container').hide();
        $('#variety_id_container').hide();
        var zone_id=$('#zone_id').val();
        if(zone_id>0)
        {
            $('#territory_id_container').show();
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

        }
    });
    $(document).on("change","#territory_id",function()
    {

        $("#upazilla_id").val("");
        $("#crop_id").val("");
        $("#crop_type_id").val("");
        $("#variety_id").val("");
        $('#upazilla_id_container').hide();
        $('#crop_id_container').hide();
        $('#crop_type_id_container').hide();
        $('#variety_id_container').hide();
        var territory_id=$('#territory_id').val();
        if(territory_id>0)            {
            $('#district_id_container').show();

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
            $('#district_id_container').hide();
        }
    });
    $(document).on("change","#district_id",function()
    {

        $("#upazilla_id").val("");
        $("#crop_id").val("");
        $("#crop_type_id").val("");
        $("#variety_id").val("");
        $('#crop_id_container').hide();
        $('#crop_type_id_container').hide();
        $('#variety_id_container').hide();
        var district_id=$("#district_id").val();
        if(district_id>0)
        {
            $('#upazilla_id_container').show();
            $.ajax({
                url: base_url+"common_controller/get_dropdown_upazillas_by_districtid/",
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
            $('#upazilla_id_container').hide();
        }

    });
    $(document).on("change","#upazilla_id",function()
    {

        $("#crop_id").val("");
        $("#crop_type_id").val("");
        $("#variety_id").val("");
        $('#crop_type_id_container').hide();
        $('#variety_id_container').hide();

        var upazilla_id=$("#upazilla_id").val();
        if(upazilla_id>0)
        {
            $('#crop_id_container').show();
        }
        else
        {
            $('#crop_id_container').hide();
        }
    });
    $(document).on("change","#crop_id",function()
    {
        $("#crop_type_id").val("");
        $("#variety_id").val("");
        var crop_id=$('#crop_id').val();
        $('#variety_id_container').hide();
        if(crop_id>0)
        {
            $('#crop_type_id_container').show();
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
        }
    });
    $(document).on("change","#crop_type_id",function()
    {
        $("#variety_id").val("");
        var crop_type_id=$('#crop_type_id').val();
        if(crop_type_id>0)
        {
            $('#variety_id_container').show();
            $.ajax({
                url: base_url+"common_controller/get_dropdown_varieties_by_croptypeid/",
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
        }
    });

});
</script>
