<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$employee_info_for_division=array();
foreach($user_info as $user)
{
    $employee_info_for_division[$user['division_id']][]=$user;
}
$employee_info_for_zone=array();
foreach($user_info as $user)
{
    $employee_info_for_zone[$user['zone_id']][]=$user;
}
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
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
                    </div>
                    <div class="col-xs-6">
                        <?php
                        if($CI->locations['division_id']>0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['division_name'];?></label>
                            <input type="hidden" name="report[division_id]" value="<?php echo $CI->locations['division_id'];?>">
                        <?php
                        }
                        else
                        {
                            ?>
                            <select id="division_id" name="report[division_id]" class="form-control">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                <?php
                                foreach($divisions as $division)
                                {?>
                                    <option value="<?php echo $division['value']?>"><?php echo $division['text'];?></option>
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
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
                    </div>

                    <div class="col-xs-6">
                        <?php
                        if($CI->locations['zone_id']>0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['zone_name'];?></label>
                            <input type="hidden" name="report[zone_id]" value="<?php echo $CI->locations['zone_id'];?>">
                        <?php
                        }
                        else
                        {
                            ?>
                            <select id="zone_id" class="form-control" name="report[zone_id]">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                <?php
                                foreach($zones as $zone)
                                {?>
                                    <option value="<?php echo $zone['value']?>"><?php echo $zone['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                </div>

                <div style="" class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_EMPLOYEE_NAME');?></label>
                    </div>
                    <div class="col-xs-6">

                        <?php if($user_counter<2){?>
                            <?php foreach($user_info as $user){?>
                                <?php echo $user['text'];?>
                                <input type="hidden" name="report[user_id]" value="<?php echo $user['value']?>">
                            <?php } ?>
                        <?php } else{?>
                            <select id="employee_info_id" name="report[user_id]" class="form-control">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                <?php foreach($user_info as $user){?>
                                    <option value="<?php echo $user['value']?>"><?php echo $user['text'];?></option>
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
        $(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+0"});
        $(document).off('change', '#division_id');
        $(document).on('change','#division_id',function()
        {
            $('#zone_id').val('');
            var division_id=$('#division_id').val();
            $('#zone_id_container').hide();
            if(division_id>0)
            {
                if(system_zones[division_id]!==undefined)
                {
                    $('#zone_id_container').show();
                    $('#zone_id').html(get_dropdown_with_select(system_zones[division_id]));
                }
                if(employee_info_list_division[division_id]!==undefined)
                {
                    $('#employee_info_id').html('');
                    $('#employee_info_id').html(get_dropdown_with_select(employee_info_list_division[division_id]));
                }
            }else
            {
                $('#zone_id_container').hide();
                $('#employee_info_id').html('');
                $.ajax({
                    url: base_url+"Reports_ti_daily_attendance/get_employee_info_list/",
                    type: 'POST',
                    datatype: "JSON",
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
        $(document).off('change', '#zone_id');
        $(document).on('change','#zone_id',function()
        {
            var division_id=$('#division_id').val();
            var zone_id=$('#zone_id').val();
            if(zone_id>0)
            {
                if(employee_info_list_zone[zone_id]!==undefined)
                {
                    $('#employee_info_id').html('');
                    $('#employee_info_id').html(get_dropdown_with_select(employee_info_list_zone[zone_id]));
                }
            }else
            {
                if(division_id==undefined)
                {
                    var division_id='<?php echo $CI->locations['division_id']?>';
                }
                $('#employee_info_id').html('');
                $('#employee_info_id').html(get_dropdown_with_select(employee_info_list_division[division_id]));
            }
        });
    });
</script>


<script type="text/javascript">
    var employee_info_list_division=JSON.parse('<?php echo json_encode($employee_info_for_division);?>');
    var employee_info_list_zone=JSON.parse('<?php echo json_encode($employee_info_for_zone);?>');
</script>

