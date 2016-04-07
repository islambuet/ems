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
    <input type="hidden" id="id" name="id" value="<?php echo $fsetup['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['division_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['division_name'];?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="division_id" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        foreach($divisions as $division)
                        {?>
                            <option value="<?php echo $division['value']?>" <?php if($division['value']==$fsetup['division_id']){ echo "selected";}?>><?php echo $division['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>

        <div style="<?php if(!(sizeof($zones)>0)){echo 'display:none';} ?>" class="row show-grid" id="zone_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['zone_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['zone_name'];?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="zone_id" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        foreach($zones as $zone)
                        {?>
                            <option value="<?php echo $zone['value']?>" <?php if($zone['value']==$fsetup['zone_id']){ echo "selected";}?>><?php echo $zone['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>
        <div style="<?php if(!(sizeof($territories)>0)){echo 'display:none';} ?>" class="row show-grid" id="territory_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['territory_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['territory_name'];?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="territory_id" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        foreach($territories as $territory)
                        {?>
                            <option value="<?php echo $territory['value']?>" <?php if($territory['value']==$fsetup['territory_id']){ echo "selected";}?>><?php echo $territory['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>

            </div>
        </div>
        <div style="<?php if(!(sizeof($districts)>0)){echo 'display:none';} ?>" class="row show-grid" id="district_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['district_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['district_name'];?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="district_id" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        foreach($districts as $district)
                        {?>
                            <option value="<?php echo $district['value']?>" <?php if($district['value']==$fsetup['district_id']){ echo "selected";}?>><?php echo $district['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>

            </div>
        </div>
        <div style="<?php if(!(sizeof($upazillas)>0)){echo 'display:none';} ?>" class="row show-grid" id="upazilla_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['upazilla_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['upazilla_name'];?></label>
                    <input type="hidden" id="upazilla_id" name="fsetup[upazilla_id]" value="<?php echo $CI->locations['upazilla_id']; ?>">
                <?php
                }
                else
                {
                    ?>
                    <select id="upazilla_id" name="fsetup[upazilla_id]" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        foreach($upazillas as $upazilla)
                        {?>
                            <option value="<?php echo $upazilla['value']?>" <?php if($upazilla['value']==$fsetup['upazilla_id']){ echo "selected";}?>><?php echo $upazilla['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>
        <div style="<?php if(!($fsetup['upazilla_id']>0)){echo 'display:none';} ?>" class="row show-grid" id="crop_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="crop_id" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($crops as $crop)
                    {?>
                        <option value="<?php echo $crop['value']?>" <?php if($crop['value']==$fsetup['crop_id']){ echo "selected";}?>><?php echo $crop['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="<?php if(!($fsetup['crop_id']>0)){echo 'display:none';} ?>" class="row show-grid" id="crop_type_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="crop_type_id" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($types as $type)
                    {?>
                        <option value="<?php echo $type['value']?>" <?php if($type['value']==$fsetup['type_id']){ echo "selected";}?>><?php echo $type['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="<?php if(!($fsetup['type_id']>0)){echo 'display:none';} ?>" class="row show-grid" id="variety_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="variety_id" name="fsetup[variety_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($varieties as $variety)
                    {?>
                        <option value="<?php echo $variety['value']?>" <?php if($variety['value']==$fsetup['variety_id']){ echo "selected";}?>><?php echo $variety['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Farmer's Name<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="fsetup[name]" class="form-control" value="<?php echo $fsetup['name']; ?>">
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
                <input type="text" name="fsetup[date_sowing]" class="form-control datepicker" value="<?php echo System_helper::display_date($fsetup['date_sowing']); ?>">
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NUM_PICTURE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="num_picture" name="fsetup[num_picture]" class="form-control">
                    <?php
                    for($i=0;$i<=30;$i++)
                    {
                        ?>
                        <option value="<?php echo $i;?>" <?php if($i==$fsetup['num_picture']){ echo "selected";}?>><?php echo $i;?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_INTERVAL');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="interval" name="fsetup[interval]" class="form-control">
                    <?php
                    for($i=0;$i<=30;$i++)
                    {
                        ?>
                        <option value="<?php echo $i;?>" <?php if($i==$fsetup['interval']){ echo "selected";}?>><?php echo $i;?></option>
                    <?php
                    }
                    ?>
                </select>
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
