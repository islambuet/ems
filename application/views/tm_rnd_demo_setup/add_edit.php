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
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_YEAR');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label><?php echo $fsetup['year'];?></label>
                <input type="hidden" name="fsetup[year]" value="<?php echo $fsetup['year']; ?>" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SEASON');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="season_id" name="fsetup[season_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($seasons as $season)
                    {?>
                        <option value="<?php echo $season['value']?>" <?php if($season['value']==$fsetup['season_id']){ echo "selected";}?>><?php echo $season['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid" id="crop_id_container">
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
            <div class="col-sm-4 col-xs-8" id="variety_list_container">
                <?php
                foreach($varieties as $variety)
                {
                    ?>
                    <div class="checkbox">
                        <label><input type="checkbox" name="variety_ids[]" value="<?php echo $variety['value']; ?>" <?php if(isset($previous_varieties[$variety['value']])) echo 'checked'; ?>><?php echo $variety['text'].' ('.$variety['whose'].')'; ?></label>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">PRI's Name<span style="color:#FF0000">*</span></label>
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
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_TRANSPLANT');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="fsetup[date_transplant]" class="form-control datepicker" value="<?php if($fsetup['date_transplant']>0){echo System_helper::display_date($fsetup['date_transplant']); }?>">
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
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NUM_VISITS');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="num_picture" name="fsetup[num_visits]" class="form-control">
                    <?php
                    for($i=0;$i<=30;$i++)
                    {
                        ?>
                        <option value="<?php echo $i;?>" <?php if($i==$fsetup['num_visits']){ echo "selected";}?>><?php echo $i;?></option>
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

    $(document).on("change","#crop_id",function()
    {
        $("#crop_type_id").val("");
        $('#variety_list_container').html('');
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
        $('#variety_list_container').html('');
        var crop_type_id=$('#crop_type_id').val();
        if(crop_type_id>0)
        {
            $('#variety_id_container').show();
            $.ajax({
                url: '<?php echo base_url($CI->controller_url.'/index/list_variety');?>',
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
