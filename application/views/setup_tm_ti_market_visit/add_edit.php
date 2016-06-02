<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_save"]='#save_form';
    $action_data["action_save_new"]='#save_form';
    $action_data["action_clear"]='#save_form';
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
                            <option value="<?php echo $division['value']?>" <?php if($division['value']==$setup['division_id']){ echo "selected";}?>><?php echo $division['text'];?></option>
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
                            <option value="<?php echo $zone['value']?>" <?php if($zone['value']==$setup['zone_id']){ echo "selected";}?>><?php echo $zone['text'];?></option>
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
                            <option value="<?php echo $territory['value']?>" <?php if($territory['value']==$setup['territory_id']){ echo "selected";}?>><?php echo $territory['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>

            </div>
        </div>
    </div>

    <div class="clearfix"></div>
    <div id="system_report_container">
    <?php
    if($setup['territory_id']>0)
    {
        $CI->load->view("setup_tm_ti_market_visit/schedule",array('territory_id'=>$setup['territory_id']));
    }
    ?>
    </div>
<script type="text/javascript">
jQuery(document).ready(function()
{
    turn_off_triggers();
    $(document).on("change","#division_id",function()
    {
        $("#system_report_container").html("");
        $("#zone_id").val("");
        $("#territory_id").val("");
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
        $("#system_report_container").html("");
        $("#territory_id").val("");
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

        $("#system_report_container").html("");
        var territory_id=$('#territory_id').val();
        if(territory_id>0)
        {
            $.ajax({
                url: "<?php echo site_url($CI->controller_url.'/index/get_schedule')?>",
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
    });
    $(document).on("change",".district_id",function()
    {
        var district_id=$(this).val();
        var day_no=$(this).attr('data-day');
        var shift_id=$(this).attr('data-shift-id');
        $('#customers_container_'+day_no+'_'+shift_id).html('');
        $('#other_customers_container_'+day_no+'_'+shift_id).html('');
        $('#special_container_'+day_no+'_'+shift_id).html('');
        if(district_id>0)
        {
            $.ajax({
                url: "<?php echo site_url($CI->controller_url.'/get_customers')?>",
                type: 'POST',
                datatype: "JSON",
                data:{district_id:district_id,day_no:day_no,shift_id:shift_id},
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
