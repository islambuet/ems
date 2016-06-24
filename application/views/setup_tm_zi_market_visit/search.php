<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_save"]='#save_form';
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
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_YEAR');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                    <select id="year" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        for($i=2016;$i<=Date('Y')+1;$i++)
                        {?>
                            <option value="<?php echo $i?>" <?php if($i==$setup['year']){ echo "selected";}?>><?php echo $i;?></option>
                        <?php
                        }
                        ?>
                    </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_MONTH');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="month" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    for($i=1;$i<13;$i++)
                    {?>
                        <option value="<?php echo $i?>" <?php if($i==$setup['month']){ echo "selected";}?>><?php echo date("M", mktime(0, 0, 0,  $i,1, 2000));?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($setup['division_id']>0)
                {
                    $division_name='';
                    foreach($divisions as $division)
                    {
                        if($division['value']==$setup['division_id'])
                        {
                            $division_name=$division['text'];
                        }
                    }
                    ?>
                    <label class="control-label"><?php echo $division_name;;?></label>
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
                if($setup['zone_id']>0)
                {
                    $zone_name='';
                    foreach($zones as $zone)
                    {
                        if($zone['value']==$setup['zone_id'])
                        {
                            $zone_name=$zone['text'];
                        }
                    }
                    ?>
                    <label class="control-label"><?php echo $zone_name;?></label>
                    <input type="hidden" id="zone_id" value="<?php echo $setup['zone_id'];?>"/>
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
    </div>

    <div class="clearfix"></div>
    <div id="system_report_container">

    </div>
<script type="text/javascript">
    function load_schedule()
    {
        var zone_id=$('#zone_id').val();
        var year=$('#year').val();
        var month=$('#month').val();
        if(zone_id>0 && year>0 && month>0)
        {
            $.ajax({
                url: '<?php echo site_url($CI->controller_url.'/index/get_schedule');?>',
                type: 'POST',
                datatype: "JSON",
                data:{zone_id:zone_id,year:year,month:month},
                success: function (data, status)
                {

                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });
        }
    }
jQuery(document).ready(function()
{
    turn_off_triggers();
    load_schedule();
    $(document).off("change", "#year");
    $(document).off("change", "#month");
    $(document).off("change", ".territory_id");
    $(document).off("change", ".district_id");

    $(document).on("change","#year",function()
    {
        $("#system_report_container").html("");
        $("#month").val("");
    });
    $(document).on("change","#month",function()
    {
        $("#system_report_container").html("");
        load_schedule();
    });
    $(document).on("change","#division_id",function()
    {
        $("#system_report_container").html("");
        $("#zone_id").val("");
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
        load_schedule();
    });

    $(document).on("change",".territory_id",function()
    {
        var territory_id=$(this).val();
        var day=$(this).attr('data-day');
        var shift_id=$(this).attr('data-shift-id');
        $("#district_container_"+day+"_"+shift_id).html('hi');
        if(territory_id>0)
        {
            var districts=zone_info[territory_id]['districts'];
            var html='<select class="form-control district_id" data-day="'+day+'" data-shift-id="'+shift_id+'" data-territory-id="'+territory_id+'">';
            html+='<option value=""><?php echo $CI->lang->line('SELECT');?></option>';
            $.each(districts, function(index, value)
            {
                html+='<option value="'+value['district_id']+'">'+ value['district_name']+'</option>';
            });
            html+='</select>';
            $("#district_container_"+day+"_"+shift_id).html(html);
        }

    });
    $(document).on("change",".district_id",function()
    {
        var district_id=$(this).val();
        var day=$(this).attr('data-day');
        var shift_id=$(this).attr('data-shift-id');
        var territory_id=$(this).attr('data-territory-id');
        $('#customers_container_'+day+'_'+shift_id).html('');
        if(district_id>0)
        {
            var customers=zone_info[territory_id]['districts'][district_id]['customers'];
            var html='';
            $.each(customers, function(index, value)
            {
                html+='<div class="checkbox">';
                html+='<label><input type="checkbox" name="data['+day+']['+shift_id+'][customer]['+value['customer_id']+']" value="'+value['customer_id']+'">'+value['customer_name']+'</label>';
                html+='</div>';
            });
            $('#customers_container_'+day+'_'+shift_id).html(html);
        }
    });
});
</script>
