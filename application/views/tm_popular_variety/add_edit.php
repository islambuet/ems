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
    <input type="hidden" id="id" name="id" value="<?php echo $pv['id']; ?>" />
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
                            <option value="<?php echo $division['value']?>" <?php if($division['value']==$pv['division_id']){ echo "selected";}?>><?php echo $division['text'];?></option>
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
                            <option value="<?php echo $zone['value']?>" <?php if($zone['value']==$pv['zone_id']){ echo "selected";}?>><?php echo $zone['text'];?></option>
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
                            <option value="<?php echo $territory['value']?>" <?php if($territory['value']==$pv['territory_id']){ echo "selected";}?>><?php echo $territory['text'];?></option>
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
                            <option value="<?php echo $district['value']?>" <?php if($district['value']==$pv['district_id']){ echo "selected";}?>><?php echo $district['text'];?></option>
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
                    <input type="hidden" id="upazilla_id" name="pv[upazilla_id]" value="<?php echo $CI->locations['upazilla_id']; ?>">
                <?php
                }
                else
                {
                    ?>
                    <select id="upazilla_id" name="pv[upazilla_id]" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        foreach($upazillas as $upazilla)
                        {?>
                            <option value="<?php echo $upazilla['value']?>" <?php if($upazilla['value']==$pv['upazilla_id']){ echo "selected";}?>><?php echo $upazilla['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>
        <div style="<?php if(!($pv['upazilla_id']>0)){echo 'display:none';} ?>" class="row show-grid" id="crop_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="crop_id" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($crops as $crop)
                    {?>
                        <option value="<?php echo $crop['value']?>" <?php if($crop['value']==$pv['crop_id']){ echo "selected";}?>><?php echo $crop['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="<?php if(!($pv['crop_id']>0)){echo 'display:none';} ?>" class="row show-grid" id="crop_type_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="crop_type_id" name="pv[crop_type_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($types as $type)
                    {?>
                        <option value="<?php echo $type['value']?>" <?php if($type['value']==$pv['type_id']){ echo "selected";}?>><?php echo $type['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="<?php if(!($pv['type_id']>0)){echo 'display:none';} ?>" class="row show-grid" id="variety_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="variety_id" name="pv[variety_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($varieties as $variety)
                    {?>
                        <option value="<?php echo $variety['value']?>" <?php if($variety['value']==$pv['variety_id']){ echo "selected";}?>><?php echo $variety['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="<?php if(!(strlen($pv['other_variety_name'])>0)){echo 'display:none';} ?>" class="row show-grid" id="other_variety_name_container">
            <div class="col-xs-4">
                <label class="control-label pull-right">Other Variety</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="pv[other_variety_name]" id="other_variety_name" class="form-control" value="<?php echo $pv['other_variety_name']; ?>">
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Farmer's Name<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="pv[name]" class="form-control" value="<?php echo $pv['name']; ?>">
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ADDRESS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="pv[address]" class="form-control"><?php echo $pv['address']; ?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Contact no</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="pv[contact_no]" class="form-control" value="<?php echo $pv['contact_no']; ?>">
            </div>
        </div>
    </div>
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                Picture and Information
            </div>
            <div class="clearfix"></div>
        </div>
        <div style="overflow-x: auto;" class="row show-grid" id="details_container">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_DATE'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_REMARKS'); ?></th>
                    <th style="min-width: 150px;">Picture</th>
                    <th style="width: 150px;"><?php echo $CI->lang->line('ACTION'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($details as $index=>$detail)
                {
                    ?>
                    <tr>
                        <td class="text-right">
                            <input id="date_remarks_<?php echo $index+1;?>" name="details[<?php echo $index+1; ?>][date_remarks]" data-current-id="<?php echo $index+1;?>" type="text" class="form-control date_remarks olddatepicker" value="<?php echo System_helper::display_date($detail['date_remarks']); ?>">
                        </td>
                        <td class="text-right">
                            <textarea id="remarks<?php echo $index+1;?>" name="details[<?php echo $index+1; ?>][remarks]" data-current-id="<?php echo $index+1;?>" class="form-control remarks"><?php echo $detail['remarks']; ?></textarea>
                        </td>
                        <td>
                            <div class="image_container" id="image_<?php echo $index+1;?>">
                                <?php
                                $image=base_url().'images/no_image.jpg';
                                if(strlen($detail['picture_url'])>0)
                                {
                                    $image=$detail['picture_url'];
                                }
                                ?>
                                <img style="max-width: 250px;" src="<?php echo $image;?>">
                                <?php
                                if(strlen($detail['picture_file_name'])>0)
                                {
                                    ?>
                                    <input type="hidden" name="details[<?php echo $index+1; ?>][old_picture]" value="<?php echo $detail['picture_file_name']; ?>">
                                    <?php
                                }
                                ?>
                            </div>
                        </td>
                        <td>
                            <input type="file" id="image_browse_<?php echo $index+1; ?>" name="image_<?php echo $index+1; ?>" data-current-id="<?php echo $index+1;?>" data-preview-container="#image_<?php echo $index+1;?>" class="browse_button_old"><br>
                            <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
                        </td>


                    </tr>
                <?php
                }
                ?>

                </tbody>
            </table>

        </div>
        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4">
                <button type="button" class="btn btn-warning system_button_add_more" data-current-id="<?php echo sizeof($details);?>"><?php echo $CI->lang->line('LABEL_ADD_MORE');?></button>
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
            <td class="text-right">
                <input type="text"class="form-control date_remarks" value="<?php echo System_helper::display_date(time()); ?>"/>
            </td>
            <td class="text-right">
                <textarea class="form-control remarks"></textarea>
            </td>
            <td>
                <div class="image_container"><img style="max-width: 250px;" src="<?php echo base_url().'images/no_image.jpg';?>"></div>
            </td>
            <td>
                <input type="file" class="browse_button"><br>
                <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>

        </tr>
        </tbody>
    </table>
</div>
<script type="text/javascript">
jQuery(document).ready(function()
{
    turn_off_triggers();
    $(".olddatepicker").datepicker({dateFormat : display_date_format});
    $('.browse_button_old').filestyle({input: false,icon: false,buttonText: "Upload",buttonName: "btn-primary"});
    $(document).on("click", ".system_button_add_more", function(event)
    {
        var current_id=parseInt($(this).attr('data-current-id'));
        current_id=current_id+1;
        $(this).attr('data-current-id',current_id);
        var content_id='#system_content_add_more table tbody';
        $(content_id+' .date_remarks').attr('id','date_remarks_'+current_id);
        $(content_id+' .date_remarks').attr('data-current-id',current_id);
        $(content_id+' .date_remarks').attr('name','details['+current_id+'][date_remarks]');
        $(content_id+' .date_remarks').addClass('datepicker');

        $(content_id+' .remarks').attr('id','remarks'+current_id);
        $(content_id+' .remarks').attr('data-current-id',current_id);
        $(content_id+' .remarks').attr('name','details['+current_id+'][remarks]');

        $(content_id+' .browse_button').attr('data-preview-container','#image_'+current_id);
        $(content_id+' .browse_button').attr('name','image_'+current_id);
        $(content_id+' .browse_button').attr('id','image_browse_'+current_id);
        $(content_id+' .image_container').attr('id','image_'+current_id);

        var html=$(content_id).html();
        $("#details_container tbody").append(html);

        $(content_id+' .date_remarks').removeAttr('id');
        $(content_id+' .date_remarks').removeClass('datepicker');
        $(content_id+' .remarks').removeAttr('id');
        $(content_id+' .image_container').removeAttr('id');
        $(content_id+' .browse_button').removeAttr('name');
        $(content_id+' .browse_button').removeAttr('data-preview-container');
        $(content_id+' .browse_button').removeAttr('id');
        $("#date_remarks_"+current_id).datepicker({dateFormat : display_date_format});
        $('#image_browse_'+current_id).filestyle({input: false,icon: false,buttonText: "Upload",buttonName: "btn-primary"});

    });
    $(document).on("click", ".system_button_add_delete", function(event)
    {
        $(this).closest('tr').remove();
    });

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
        $('#other_variety_name_container').hide();
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
        $('#other_variety_name_container').hide();
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
        $('#other_variety_name_container').hide();
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
        $('#other_variety_name_container').hide();
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
        $('#other_variety_name_container').hide();

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
        $('#other_variety_name_container').hide();
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
            $('#other_variety_name_container').show();
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
            $('#other_variety_name_container').hide();
        }
    });
    $(document).on("change","#variety_id",function()
    {
        var variety_id=$('#variety_id').val();
        if(variety_id>0)
        {
            $("#other_variety_name").val("");
            $('#other_variety_name_container').hide();
        }
        else
        {
            $('#other_variety_name_container').show();
        }
    });

});
</script>
