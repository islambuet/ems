<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();

?>
<form class="form_valid" id="search_form" action="<?php echo site_url($CI->controller_url.'/index/list_variety');?>" method="post">
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-6">
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_YEAR');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="year" name="report[year]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            <?php
                            foreach($years as $year)
                            {?>
                                <option value="<?php echo $year['year'];?>"><?php echo $year['year'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_SEASON');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="season_id" name="report[season_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            <?php
                            foreach($seasons as $season)
                            {?>
                                <option value="<?php echo $season['value'];?>"><?php echo $season['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div style="" class="row show-grid" id="crop_id_container">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="crop_id" name="report[crop_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            <?php
                            foreach($crops as $crop)
                            {?>
                                <option value="<?php echo $crop['value']?>"><?php echo $crop['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div style="display: none;" class="row show-grid" id="crop_type_id_container">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="crop_type_id" name="report[crop_type_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div style="" class="row show-grid">
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
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
                    </div>
                </div>

                <div style="<?php if(!(sizeof($zones)>0)){echo 'display:none';} ?>" class="row show-grid" id="zone_id_container">
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
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
                    </div>
                </div>
                <div style="<?php if(!(sizeof($territories)>0)){echo 'display:none';} ?>" class="row show-grid" id="territory_id_container">

                    <div class="col-xs-6">
                        <?php
                        if($CI->locations['territory_id']>0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['territory_name'];?></label>
                            <input type="hidden" name="report[territory_id]" value="<?php echo $CI->locations['territory_id'];?>">
                        <?php
                        }
                        else
                        {
                            ?>
                            <select id="territory_id" class="form-control" name="report[territory_id]">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                <?php
                                foreach($territories as $territory)
                                {?>
                                    <option value="<?php echo $territory['value']?>"><?php echo $territory['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label>
                    </div>
                </div>
                <div style="<?php if(!(sizeof($districts)>0)){echo 'display:none';} ?>" class="row show-grid" id="district_id_container">
                    <div class="col-xs-6">
                        <?php
                        if($CI->locations['district_id']>0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['district_name'];?></label>
                            <input type="hidden" name="report[district_id]" value="<?php echo $CI->locations['district_id'];?>">
                        <?php
                        }
                        else
                        {
                            ?>
                            <select id="district_id" class="form-control" name="report[district_id]">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                <?php
                                foreach($districts as $district)
                                {?>
                                    <option value="<?php echo $district['value']?>"><?php echo $district['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label>
                    </div>
                </div>
                <div style="<?php if(!(sizeof($upazillas)>0)){echo 'display:none';} ?>" class="row show-grid" id="upazilla_id_container">
                    <div class="col-xs-6">
                        <?php
                        if($CI->locations['upazilla_id']>0)
                        {
                            ?>
                            <label class="control-label"><?php echo $CI->locations['upazilla_name'];?></label>
                            <input type="hidden" name="report[upazilla_id]" value="<?php echo $CI->locations['upazilla_id'];?>">
                        <?php
                        }
                        else
                        {
                            ?>
                            <select id="upazilla_id" name="report[upazilla_id]" class="form-control">
                                <option value=""><?php echo $this->lang->line('SELECT');?></option>
                                <?php
                                foreach($upazillas as $upazilla)
                                {?>
                                    <option value="<?php echo $upazilla['value']?>"><?php echo $upazilla['text'];?></option>
                                <?php
                                }
                                ?>
                            </select>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME');?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <div class="action_button pull-right">
                    <button type="button" class="btn" id="but_load_crop">Load Crop</button>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="action_button pull-right">
                    <button type="submit" class="btn" data-form="#search_form"><?php echo $CI->lang->line("LABEL_LOAD_VARIETY"); ?></button>
                </div>

            </div>
            <div class="col-xs-4">

            </div>
        </div>

    </div>
    <div class="clearfix"></div>
</form>
<div id="variety_list_container">

</div>

<div id="system_report_container">

</div>

<script type="text/javascript">

    jQuery(document).ready(function()
    {
        turn_off_triggers();
        $(document).off("click", ".pop_up");


        $(document).on("click", ".pop_up", function(event)
        {

            var left=((($(window).width() - 550) / 2) +$(window).scrollLeft());
            var top=((($(window).height() - 550) / 2) +$(window).scrollTop());

            //$("#popup_window").jqxWindow({width: 630,height:550,position: { x: 60, y: 60  }});to change position always
            $("#popup_window").jqxWindow({position: { x: left, y: top  }});
            var row=$(this).attr('data-item-no');
            var key=$(this).attr('data-key');
            var row_info = $("#system_jqx_container").jqxGrid('getrowdata', row);
            $('#popup_content').html(row_info.details[key]);
            $("#popup_window").jqxWindow('open');


        });

        $(document).off("change", "#select_all_arm");
        $(document).off("change", "#select_all_competitor");
        $(document).off("change", "#select_all_upcoming");

        $(document).on("change","#select_all_arm",function()
        {
            if($(this).is(':checked'))
            {
                $('.setup_arm').prop('checked', true);
            }
            else
            {
                $('.setup_arm').prop('checked', false);
            }

        });
        $(document).on("change","#select_all_competitor",function()
        {
            if($(this).is(':checked'))
            {
                $('.setup_competitor').prop('checked', true);
            }
            else
            {
                $('.setup_competitor').prop('checked', false);
            }

        });
        $(document).on("change","#select_all_upcoming",function()
        {
            if($(this).is(':checked'))
            {
                $('.setup_upcoming').prop('checked', true);
            }
            else
            {
                $('.setup_upcoming').prop('checked', false);
            }

        });
        $(document).off("click", "#but_load_crop");
        $(document).on("click","#but_load_crop",function()
        {
            $.ajax({
                url: '<?php echo site_url($CI->controller_url.'/index/load_crops');?>',
                type: 'post',
                dataType: "JSON",
                data: new FormData(document.getElementById('search_form')),
                processData: false,
                contentType: false,
                success: function (data, status)
                {

                },
                error: function (xhr, desc, err)
                {


                }
            });

        });
        $(document).on("change","#year",function()
        {
            $('#variety_list_container').html('');
        });
        $(document).on("change","#season_id",function()
        {
            $('#variety_list_container').html('');
        });
        $(document).on("change","#crop_id",function()
        {
            $('#variety_list_container').html('');
            $('#system_report_container').html('');
            $("#crop_type_id").val("");
            $("#variety_id").val("");

            var crop_id=$('#crop_id').val();
            if(crop_id>0)
            {
                $('#crop_type_id_container').show();
                $('#variety_id_container').hide();

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

            }
        });
        $(document).on("change","#division_id",function()
        {
            $('#variety_list_container').html('');
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
                $('#upazilla_id_container').hide();
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
                $('#upazilla_id_container').hide();
            }
        });
        $(document).on("change","#zone_id",function()
        {
            $('#variety_list_container').html('');
            $("#territory_id").val("");
            $("#district_id").val("");
            $("#customer_id").val("");
            var zone_id=$('#zone_id').val();
            if(zone_id>0)
            {
                $('#territory_id_container').show();
                $('#district_id_container').hide();
                $('#upazilla_id_container').hide();
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
                $('#upazilla_id_container').hide();
            }
        });
        $(document).on("change","#territory_id",function()
        {
            $('#variety_list_container').html('');
            $("#district_id").val("");
            $("#customer_id").val("");
            var territory_id=$('#territory_id').val();
            if(territory_id>0)
            {
                $('#district_id_container').show();
                $('#upazilla_id_container').hide();
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
                $('#upazilla_id_container').hide();
                $('#district_id_container').hide();
            }
        });
        $(document).on("change","#district_id",function()
        {
            $('#variety_list_container').html('');
            $("#customer_id").val("");
            var district_id=$('#district_id').val();
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

    });
</script>
