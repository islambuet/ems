<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/list');?>" method="post">
<div class="row widget">
<div class="widget-header">
    <div class="title">
        <?php echo $title; ?>
    </div>
    <div class="clearfix"></div>
</div>

<div class="row show-grid">
    <div class="col-xs-7">

        <div style="" class="row show-grid">
            <div class="col-xs-6">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_EMPLOYEE_NAME');?></label>
            </div>
            <div class="col-xs-6">

                    <?php if($user_counter<2){?>
                       <?php foreach($user_info as $user){?>
                    <?php echo $user['name'].'('.$user['designation_name'].')';?>
                    <input type="hidden" name="report[user_id]" value="<?php echo $user['user_id']?>">
                        <?php } ?>
                    <?php } else{?>
                    <select name="report[user_id]" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php foreach($user_info as $user){?>
                        <option value="<?php echo $user['user_id']?>"><?php echo $user['name'].'-'.$user['employee_id'].' ('.$user['designation_name'].')';?></option>
                        <?php } ?>

                    </select>
                <?php } ?>
            </div>

        </div>
    </div>
    <div class="col-xs-5">
        <div class="row show-grid">
            <div class="col-xs-6">
                <input type="text" id="date_start" name="report[date_start]" class="form-control date_large" value="<?php echo $date_start; ?>">
            </div>
            <div class="col-xs-6">
                <label class="control-label"><?php echo $this->lang->line('LABEL_DATE_START');?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-6">
                <input type="text" id="date_end" name="report[date_end]" class="form-control date_large" value="<?php echo $date_end; ?>">
            </div>
            <div class="col-xs-6">
                <label class="control-label"><?php echo $this->lang->line('LABEL_DATE_END');?></label>
            </div>
        </div>
    </div>
</div>

<div class="row show-grid">

</div>
<div class="row show-grid">
    <div class="col-xs-4">
    </div>
    <div class="col-xs-5">
        <div class="action_button pull-right">
            <button id="button_action_report" type="button" class="btn" data-form="#save_form"><?php echo $CI->lang->line("ACTION_REPORT"); ?></button>
        </div>

    </div>
    <div class="col-xs-3">

    </div>
</div>

</div>

<div class="clearfix"></div>
</form>
<div id="system_report_container">

</div>
<script type="text/javascript">

jQuery(document).ready(function()
{
    //turn_off_triggers();
    $(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+0"});
    $(document).off('change', '#division_id');
    $(document).on('change','#division_id',function()
    {
        //alert('hi');
        $('#zone_id').val('');
        $('#territory_id').val('');
        $('#district_id').val('');
        $('#outlet_id').val('');
        var division_id=$('#division_id').val();
        $('#zone_id_container').hide();
        $('#territory_id_container').hide();
        $('#district_id_container').hide();
        $('#outlet_id_container').hide();
        if(division_id>0)
        {
            if(system_zones[division_id]!==undefined)
            {
                $('#zone_id_container').show();
                $('#zone_id').html(get_dropdown_with_select(system_zones[division_id]));
            }
        }

    });
    $(document).off('change', '#zone_id');
    $(document).on('change','#zone_id',function()
    {
        $('#territory_id').val('');
        $('#district_id').val('');
        $('#outlet_id').val('');
        var zone_id=$('#zone_id').val();
        $('#territory_id_container').hide();
        $('#district_id_container').hide();
        $('#outlet_id_container').hide();
        if(zone_id>0)
        {
            if(system_territories[zone_id]!==undefined)
            {
                $('#territory_id_container').show();
                $('#territory_id').html(get_dropdown_with_select(system_territories[zone_id]));
            }
        }
    });
    $(document).off('change', '#territory_id');
});
</script>
