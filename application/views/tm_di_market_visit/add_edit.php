<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_save"]='#save_form';

    $action_data["action_clear"]='#save_form';
    $CI->load->view("action_buttons",$action_data);
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $visit['id']; ?>" />
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
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="visit[date]" id="date" class="form-control datepicker" value="<?php echo System_helper::display_date($visit['date']);?>"/>
            </div>
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
                    <input type="hidden" name="visit[division_id]" value="<?php echo $CI->locations['division_id'];?>">
                <?php
                }
                else
                {
                    ?>
                    <select id="division_id" name="visit[division_id]" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        foreach($divisions as $division)
                        {?>
                            <option value="<?php echo $division['value']?>" <?php if($division['value']==$visit['division_id']){ echo "selected";}?>><?php echo $division['text'];?></option>
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
                            <option value="<?php echo $zone['value']?>" <?php if($zone['value']==$visit['zone_id']){ echo "selected";}?>><?php echo $zone['text'];?></option>
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
                            <option value="<?php echo $territory['value']?>" <?php if($territory['value']==$visit['territory_id']){ echo "selected";}?>><?php echo $territory['text'];?></option>
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
                    <input type="hidden" name="visit[district_id]" value="<?php echo $CI->locations['district_id'];?>">
                <?php
                }
                else
                {
                    ?>
                    <select id="district_id" class="form-control" name="visit[district_id]">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        foreach($districts as $district)
                        {?>
                            <option value="<?php echo $district['value']?>" <?php if($district['value']==$visit['district_id']){ echo "selected";}?>><?php echo $district['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>

            </div>
        </div>
        <div style="<?php if(!(sizeof($customers)>0)){echo 'display:none';} ?>" class="row show-grid" id="customer_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CUSTOMER_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="customer_id" name="visit[customer_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($customers as $customer)
                    {?>
                        <option value="<?php echo $customer['value']?>" <?php if($customer['value']==$visit['customer_id']){ echo "selected";}?>><?php echo $customer['text'];?></option>
                    <?php
                    }
                    ?>
                </select>

            </div>
        </div>
        <div style="" class="row show-grid" id="customer_name_container">
            <div class="col-xs-4">
                <label class="control-label pull-right">Other <?php echo $CI->lang->line('LABEL_CUSTOMER_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="visit[customer_name]" class="form-control" value="<?php echo $visit['customer_name'];?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Activities</label>
            </div>
            <div class="col-xs-4">
                <textarea name="visit[activities]" id="activities" class="form-control"><?php echo $visit['activities'] ?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Activities Picture</label>
            </div>
            <div class="col-xs-4" id="image_activities">
                <?php
                $image=base_url().'images/no_image.jpg';
                if(strlen($visit['picture_url_activities'])>0)
                {
                    $image=$visit['picture_url_activities'];
                }
                ?>
                <img style="max-width: 250px;" src="<?php echo $image;?>">
            </div>
            <div class="col-xs-4">
                <input type="file" class="browse_button" data-preview-container="#image_activities" name="image_activities">
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Problem</label>
            </div>
            <div class="col-xs-4">
                <textarea name="visit[problem]" id="problem" class="form-control"><?php echo $visit['problem'] ?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Problem Picture</label>
            </div>
            <div class="col-xs-4" id="image_problem">
                <?php
                $image=base_url().'images/no_image.jpg';
                if(strlen($visit['picture_url_problem'])>0)
                {
                    $image=$visit['picture_url_problem'];
                }
                ?>
                <img style="max-width: 250px;" src="<?php echo $image;?>">
            </div>
            <div class="col-xs-4">
                <input type="file" class="browse_button" data-preview-container="#image_problem" name="image_problem">
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Recommendation<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="visit[recommendation]" id="problem" class="form-control"><?php echo $visit['recommendation'] ?></textarea>
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
        $(".browse_button").filestyle({input: false,icon: false,buttonText: "Upload",buttonName: "btn-primary"});
        $(document).on("change","#division_id",function()
        {
            $("#zone_id").val("");
            $("#territory_id").val("");
            $("#district_id").val("");
            $("#customer_id").val("");
            $('#territory_id_container').hide();
            $('#district_id_container').hide();
            $('#customer_id_container').hide();
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
            $("#customer_id").val("");
            $('#district_id_container').hide();
            $('#customer_id_container').hide();
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
            $("#district_id").val("");
            $("#customer_id").val("");
            $("#customer_id_container").hide();
            var territory_id=$('#territory_id').val();
            if(territory_id>0)
            {
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
                $('#customer_id_container').hide();
            }
        });
        $(document).on("change","#district_id",function()
        {
            $("#customer_id").val("");
            var district_id=$('#district_id').val();
            if(district_id>0)
            {
                $('#customer_id_container').show();
                $('#customer_name_container').show();
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_customers_by_districtid/",
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

    });
</script>
