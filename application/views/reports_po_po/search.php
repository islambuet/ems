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
            <div class="col-xs-6">
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_FISCAL_YEAR');?></label>
                    </div>
                    <div class="col-xs-6">
                        <select id="fiscal_year_id" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            <?php
                            foreach($fiscal_years as $year)
                            {?>
                                <option value="<?php echo $year['value']?>"><?php echo $year['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>

                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_START');?></label>
                    </div>
                    <div class="col-xs-6">
                        <input type="text" id="date_start" name="report[date_start]" class="form-control date_large" value="<?php echo System_helper::display_date(time());?>">
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_END');?></label>
                    </div>
                    <div class="col-xs-6">
                        <input type="text" id="date_end" name="report[date_end]" class="form-control date_large" value="<?php echo System_helper::display_date(time());?>">
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right">PO Request Status</label>
                    </div>
                    <div class="col-xs-6">
                        <select id="status_requested" name="report[status_requested]" class="form-control">
                            <option value="<?php echo $this->config->item('system_status_po_request_requested')?>">Requested</option>
                            <option value="<?php echo $this->config->item('system_status_po_request_pending')?>">Pending</option>
                        </select>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right">PO Approve Status</label>
                    </div>
                    <div class="col-xs-6">
                        <select id="status_approved" name="report[status_approved]" class="form-control">
                            <option value="<?php echo $this->config->item('system_status_po_approval_approved')?>">Approved</option>
                            <option value="<?php echo $this->config->item('system_status_po_approval_rejected')?>">Rejected</option>
                            <option value="<?php echo $this->config->item('system_status_po_approval_pending')?>">Pending</option>
                            <option value="">ALL</option>
                        </select>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right">PO Delivery Status</label>
                    </div>
                    <div class="col-xs-6">
                        <select id="status_delivered" name="report[status_delivered]" class="form-control">
                            <option value="<?php echo $this->config->item('system_status_po_delivery_delivered')?>">Delivered</option>
                            <option value="<?php echo $this->config->item('system_status_po_delivery_pending')?>">Pending</option>
                            <option value="">ALL</option>
                        </select>
                    </div>
                </div>
                <div class="row show-grid">
                    <div class="col-xs-6">
                        <label class="control-label pull-right">PO Receive Status</label>
                    </div>
                    <div class="col-xs-6">
                        <select id="status_received" name="report[status_received]" class="form-control">
                            <option value="<?php echo $this->config->item('system_status_po_received_pending')?>">Pending</option>
                            <option value="<?php echo $this->config->item('system_status_po_received_received')?>">Received</option>
                            <option value="">ALL</option>
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
                <div style="<?php if(!(sizeof($customers)>0)){echo 'display:none';} ?>" class="row show-grid" id="customer_id_container">
                    <div class="col-xs-6">
                        <select id="customer_id" name="report[customer_id]" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            <?php
                            foreach($customers as $customer)
                            {?>
                                <option value="<?php echo $customer['value']?>"><?php echo $customer['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-xs-6">
                        <label class="control-label"><?php echo $CI->lang->line('LABEL_CUSTOMER_NAME');?></label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4">
                <div class="action_button pull-right">
                    <button id="button_action_report" type="button" class="btn" data-form="#save_form"><?php echo $CI->lang->line("ACTION_REPORT"); ?></button>
                </div>

            </div>
            <div class="col-xs-4">

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
        turn_off_triggers();
        $(".date_large").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true,yearRange: "2015:+0"});
        $(document).on("change","#fiscal_year_id",function()
        {

            var fiscal_year_ranges=$('#fiscal_year_id').val();
            if(fiscal_year_ranges!='')
            {
                var dates = fiscal_year_ranges.split("/");
                $("#date_start").val(dates[0]);
                $("#date_end").val(dates[1]);

            }
        });

        $(document).on("change","#division_id",function()
        {
            $("#zone_id").val("");
            $("#territory_id").val("");
            $("#district_id").val("");
            $("#customer_id").val("");
            var division_id=$('#division_id').val();
            $('#zone_id_container').hide();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#customer_id_container').hide();
            if(division_id>0)
            {
                $('#zone_id_container').show();
                if(system_zones[division_id]!==undefined)
                {
                    $("#zone_id").html(get_dropdown_with_select(system_zones[division_id]));
                }
            }

        });
        $(document).on("change","#zone_id",function()
        {
            $("#territory_id").val("");
            $("#district_id").val("");
            $("#customer_id").val("");
            var zone_id=$('#zone_id').val();
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#customer_id_container').hide();
            if(zone_id>0)
            {
                $('#territory_id_container').show();
                if(system_territories[zone_id]!==undefined)
                {
                    $("#territory_id").html(get_dropdown_with_select(system_territories[zone_id]));
                }
            }
        });
        $(document).on("change","#territory_id",function()
        {
            $("#district_id").val("");
            $("#customer_id").val("");
            $('#customer_id_container').hide();
            $('#district_id_container').hide();
            var territory_id=$('#territory_id').val();
            if(territory_id>0)
            {
                $('#district_id_container').show();
                if(system_districts[territory_id]!==undefined)
                {
                    $("#district_id").html(get_dropdown_with_select(system_districts[territory_id]));
                }

            }
        });
        $(document).on("change","#district_id",function()
        {
            $("#customer_id").val("");
            var district_id=$('#district_id').val();
            if(district_id>0)
            {
                $('#customer_id_container').show();
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_outlets_by_districtid/",
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
        $(document).off("click", ".pop_up");
        $(document).on("click", ".pop_up", function(event)
        {
            $('#popup_content').html('');
            var left=((($(window).width() - 550) / 2) +$(window).scrollLeft());
            var top=((($(window).height() - 550) / 2) +$(window).scrollTop());
            $("#popup_window").jqxWindow({position: { x: left, y: top  }});
            $.ajax(
                {
                    url: $(this).attr('data-action-link'),
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
            $("#popup_window").jqxWindow('open');
        });

    });
</script>
