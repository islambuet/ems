<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_data=array();
$action_data["action_back"]=base_url($CI->controller_url);
$action_data["action_save"]='#save_form';
if(isset($CI->permissions['edit']) && ($CI->permissions['edit']==1) && $item['id']>0)
{
    $action_data["action_details_get"]=base_url($CI->controller_url."/index/details/".$item['id']);
}
$CI->load->view("action_buttons",$action_data);
?>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
<input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
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
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_BUDGET_PROPOSAL_DATE');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label pull-left"><?php echo System_helper::display_date($item['date']); ?></label>
    </div>
</div>

<div style="" class="row show-grid" id="crop_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label">
        <?php
        foreach($crops as $crop)
        {
            if($crop['value']==$item_info['crop_id']){ echo $crop['text'];}
        }
        ?>
        </label>
    </div>
</div>

<div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="crop_type_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label">
            <?php
                foreach($crop_types as $type)
                {
                    if($type['value']==$item_info['crop_type_id']){ echo $type['text'];}
                }
            ?>
        </label>
    </div>
</div>

<div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="variety_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label pull-left">
                <?php
                    foreach($crop_varieties as $variety)
                    {
                        if($variety['value']==$item_info['variety_id']){ echo $variety['text'];}
                    }
                ?>
        </label>
    </div>
</div>

<div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="competitor_variety_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_COMPETITOR_VARIETY');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label">
            <?php
                foreach($competitor_varieties as $competitor)
                {
                    if($competitor['value']==$item_info['competitor_variety_id']){ echo $competitor['text'];}
                }
            ?>
        </label>
    </div>
</div>


<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label">
            <?php
                foreach($divisions as $division)
                {
                    if($division['value']==$item_info['division_id']){ echo $division['text'];}
                }
            ?>
        </label>
    </div>
</div>

<div style="<?php if(!(sizeof($zones)>0)){echo 'display:none';}?>" class="row show-grid" id="zone_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label">
            <?php
                foreach($zones as $zone)
                {
                    if($zone['value']==$item_info['zone_id']){ echo $zone['text'];}
                }
            ?>
        </label>
    </div>
</div>

<div style="<?php if(!(sizeof($territories)>0)){echo 'display:none';}?>" class="row show-grid" id="territory_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label">
            <?php
                foreach($territories as $territory)
                {
                    if($territory['value']==$item_info['territory_id']){ echo $territory['text'];}
                }
            ?>
        </label>
    </div>
</div>

<div style="<?php if(!(sizeof($districts)>0)){echo 'display:none';}?>" class="row show-grid" id="district_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label">
            <?php
                foreach($districts as $district)
                    {
                        if($district['value']==$item_info['district_id']){ echo $district['text'];}
                    }
                ?>
        </label>
    </div>
</div>

<div style="<?php if(!(sizeof($upazillas)>0)){echo 'display:none';}?>" class="row show-grid" id="upazilla_id_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label">
            <?php
                foreach($upazillas as $upazilla)
                {
                    if($upazilla['value']==$item_info['upazilla_id']){ echo $upazilla['text'];}
                }
            ?>
        </label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ADDRESS');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item_info['address'];?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRESENT_CONDITION');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item_info['present_condition'];?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FARMERS_EVALUATION');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item_info['farmers_evaluation'];?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SPECIFIC_DIFFERENCE');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item_info['diff_wth_com'];?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_EXPECTED_DATE');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="item_info[expected_date]" id="expected_date" class="form-control datepicker" value="<?php echo System_helper::display_date($item_info['expected_date']); ?>"/>
    </div>
</div>

<div style="<?php if(!(sizeof($leading_farmers)>0)){echo 'display:none;';}?>" class="row show-grid" id="leading_farmer_container">

    <div id="leading_farmer_id" class="row show-grid">
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_LEAD_FARMER');?></label>
            </div>
        </div>
        <?php
        $total_participant=0;
        foreach($leading_farmers as $lead_farmer)
        {
        if(isset($participants[$lead_farmer['value']]) || $lead_farmer['status']=='Active'){
            ?>
            <div class="row show-grid">
                <div class="col-xs-5">
                    <label class="control-label pull-right"><?php echo $lead_farmer['text'].' ('.$lead_farmer['phone_no'].')';?><span style="color: red;">*</span></label>
                </div>
                <div class="col-sm-3 col-xs-9">
                    <input type="text" name="farmer_participant[<?php echo $lead_farmer['value'];?>]" class="participant_budget form-control integer_type_positive"
                           value="<?php if(isset($participants[$lead_farmer['value']]))
                           {
                               $total_participant+=$participants[$lead_farmer['value']]['number'];
                               echo $participants[$lead_farmer['value']]['number'];
                           }?>"/>
                </div>
            </div>
        <?php
        }}
        ?>

    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_CUSTOMER');?><span style="color:red;">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="item_info[participant_through_customer]" class="participant_budget form-control integer_type_positive" value="<?php if(isset($item_info['participant_through_customer'])) {$total_participant+=$item_info['participant_through_customer'];echo $item_info['participant_through_customer'];}?>"/>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_OTHERS');?><span style="color:red;">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="item_info[participant_through_others]" class="participant_budget form-control integer_type_positive" value="<?php if(isset($item_info['participant_through_others'])) {$total_participant+=$item_info['participant_through_others'];echo $item_info['participant_through_others'];}?>"/>
    </div>
</div>

<div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="row show-grid" id="total_participant_container">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_EXPECTED_PARTICIPANT');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label id="no_of_participant"><?php echo number_format($total_participant);?> (Person)</label>
    </div>
</div>


<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FIELD_DAY_BUDGET');?></label>
    </div>
</div>
<?php
$total_budget=0;
foreach($expense_items as $expense)
{
if(isset($expense_budget[$expense['value']]) || $expense['status']=='Active'){
    ?>
    <div class="row show-grid">
        <div class="col-xs-5">
            <label class="control-label pull-right"><?php echo $expense['text'];?> <span style="color:#FF0000">*</span></label>
        </div>
        <div class="col-sm-3 col-xs-9">
            <input type="text" name="expense_budget[<?php echo $expense['value'];?>]" class="expense_budget form-control float_type_positive"
                   value="<?php if(isset($expense_budget[$expense['value']]))
                   {
                       $total_budget+=$expense_budget[$expense['value']]['amount'];
                       echo $expense_budget[$expense['value']]['amount'];
                   }?>"/>
        </div>
    </div>
<?php
}}
?>
<div style="" class="row show-grid" id="total_budget_container">
    <div class="col-xs-5">
        <label class="control-label pull-right"> Total Budget (Tk.)</label>
    </div>
    <div class="col-sm-3 col-xs-9">
        <label id="total_budget"><?php echo number_format($total_budget,2);?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TOTAL_MARKET_SIZE');?> (kg)<span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="item_info[total_market_size]" id="total_market_size" class="form-control float_type_positive" value="<?php if(isset($item_info['total_market_size'])) {echo $item_info['total_market_size'];}?>"/>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ARM_MARKET_SIZE');?> (kg)<span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="item_info[arm_market_size]" id="arm_market_size" class="form-control float_type_positive" value="<?php if(isset($item_info['arm_market_size'])) {echo $item_info['arm_market_size'];}?>"/>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NEXT_SALES_TARGET');?> (kg)<span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <input type="text" name="item_info[sales_target]" id="sales_target" class="form-control float_type_positive" value="<?php echo $item_info['sales_target'];?>"/>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo 'TI '. $this->lang->line('LABEL_RECOMMENDATION');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <textarea class="form-control" id="remarks" name="item[remarks]"><?php echo $item['remarks']; ?></textarea>
    </div>
</div>



<div id="image" class="panel-collapse ">
    <div id="files_container" class="panel-collapse">
        <div style="overflow-x: auto;" class="row show-grid">

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="min-width: 60px;">Image Type</th>
                    <th colspan="2" style="max-width: 350px;">ARM</th>
                    <th colspan="2" style="max-width: 350px;">Competitor</th>
                </tr>
                </thead>

                <tbody>

                <?php
                foreach($picture_categories as $pic_cat)
                {
                    ?>

                    <tr>
                        <td style="min-width: 60px; color: #263238;"><b><?php echo $pic_cat['text'];?></b></td>

                        <td style="max-width: 270px; max-height: 200px;">
                            <div class="col-xs-4" id="image_arm_<?php echo $pic_cat['value'];?>">
                                <?php
                                $image='images/no_image.jpg';

                                if((isset($file_details[$pic_cat['value']]))&&(strlen($file_details[$pic_cat['value']]['arm_file_location'])>0))
                                {
                                    $image=$file_details[$pic_cat['value']]['arm_file_location'];
                                }
                                ?>
                                <img style="max-width: 270px;max-height: 200px;" src="<?php echo $CI->config->item('system_image_base_url').$image; ?>">
                            </div>
                        </td>
                        <td style="max-width: 80px; ">
                            <input type="file" class="browse_button" data-preview-container="#image_arm_<?php echo $pic_cat['value'];?>" name="arm_<?php echo $pic_cat['value'];?>">
                            <?php if($item['id']>0){ ?>
                                <input type="hidden"  name="image_info[<?php echo $pic_cat['value'];?>][arm_file_name]" value="<?php echo $file_details[$pic_cat['value']]['arm_file_name']?>">
                                <input type="hidden"  name="image_info[<?php echo $pic_cat['value'];?>][arm_file_location]" value="<?php echo $file_details[$pic_cat['value']]['arm_file_location']?>">
                            <?php }?>
                        </td>

                        <td style="max-width: 270px;max-height: 200px;">
                            <div class="col-xs-4" id="image_com_<?php echo $pic_cat['value'];?>">
                                <?php
                                $image='images/no_image.jpg';
                                if((isset($file_details[$pic_cat['value']]))&&(strlen($file_details[$pic_cat['value']]['competitor_file_location'])>0))
                                {
                                    $image=$file_details[$pic_cat['value']]['competitor_file_location'];
                                }
                                ?>
                                <img style="max-width: 270px;max-height: 200px;" src="<?php echo $CI->config->item('system_image_base_url').$image; ?>">
                            </div>
                        </td>
                        <td style="min-width: 80px;">
                            <input type="file" class="browse_button" data-preview-container="#image_com_<?php echo $pic_cat['value'];?>" name="competitor_<?php echo $pic_cat['value'];?>">
                            <?php if($item['id']>0){ ?>
                                <input type="hidden"  name="image_info[<?php echo $pic_cat['value'];?>][competitor_file_name]" value="<?php echo $file_details[$pic_cat['value']]['competitor_file_name']?>">
                                <input type="hidden"  name="image_info[<?php echo $pic_cat['value'];?>][competitor_file_location]" value="<?php echo $file_details[$pic_cat['value']]['competitor_file_location']?>">
                            <?php }?>
                        </td>

                    </tr>

                    <tr>
                        <td style="min-width: 60px; border: none;"></td>
                        <td style="min-width: 210px;border: none;">
                            <label>Remarks :</label>
                            <textarea class="form-control arm_remarks" name="arm_file_details_remarks[<?php echo $pic_cat['value'];?>]"><?php if(isset($file_details[$pic_cat['value']])){echo $file_details[$pic_cat['value']]['arm_file_remarks'];} ?></textarea>
                        </td>
                        <td style="min-width: 60px;border: none;"></td>
                        <td style="min-width: 210px;border: none;">
                            <label>Remarks :</label>
                            <textarea class="form-control com_remarks" name="com_file_details_remarks[<?php echo $pic_cat['value'];?>]"><?php if(isset($file_details[$pic_cat['value']])){echo $file_details[$pic_cat['value']]['competitor_file_remarks'];} ?></textarea>
                        </td>
                        <td style="min-width: 60px;border: none;"></td>

                    </tr>

                <?php } ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

</div>


<div class="clearfix"></div>
</form>



<script type="text/javascript">

jQuery(document).ready(function()
{
    turn_off_triggers();
    $(document).off("input",".expense_budget");
    $(document).off("input",".participant_budget");
    $(".datepicker").datepicker({dateFormat : display_date_format});


//    $(document).on("change","#division_id",function()
//    {
//        $("#zone_id").val("");
//        $("#territory_id").val("");
//        $("#district_id").val("");
//        $("#upazilla_id").val("");
//        $("#leading_farmer_id").val("");
//        var division_id=$('#division_id').val();
//        if(division_id>0)
//        {
//            $('#zone_id_container').show();
//            $('#territory_id_container').hide();
//            $('#district_id_container').hide();
//            $('#upazilla_id_container').hide();
//            $('#leading_farmer_container').hide();
//            $.ajax({
//                url: base_url+"common_controller/get_dropdown_zones_by_divisionid/",
//                type: 'POST',
//                datatype: "JSON",
//                data:{division_id:division_id},
//                success: function (data, status)
//                {
//
//                },
//                error: function (xhr, desc, err)
//                {
//                    console.log("error");
//
//                }
//            });
//        }
//        else
//        {
//            $('#zone_id_container').hide();
//            $('#territory_id_container').hide();
//            $('#district_id_container').hide();
//            $('#upazilla_id_container').hide();
//            $('#leading_farmer_container').hide();
//        }
//    });
//    $(document).on("change","#zone_id",function()
//    {
//        $("#territory_id").val("");
//        $("#district_id").val("");
//        $("#upazilla_id").val("");
//        $("#leading_farmer_id").val("");
//        var zone_id=$('#zone_id').val();
//        if(zone_id>0)
//        {
//            $('#territory_id_container').show();
//            $('#district_id_container').hide();
//            $('#upazilla_id_container').hide();
//            $('#leading_farmer_container').hide();
//            $.ajax({
//                url: base_url+"common_controller/get_dropdown_territories_by_zoneid/",
//                type: 'POST',
//                datatype: "JSON",
//                data:{zone_id:zone_id},
//                success: function (data, status)
//                {
//
//                },
//                error: function (xhr, desc, err)
//                {
//                    console.log("error");
//
//                }
//            });
//        }
//        else
//        {
//            $('#territory_id_container').hide();
//            $('#district_id_container').hide();
//            $('#upazilla_id_container').hide();
//            $('#leading_farmer_container').hide();
//        }
//    });
//    $(document).on("change","#territory_id",function()
//    {
//        $("#district_id").val("");
//        $("#upazilla_id").val("");
//        $("#leading_farmer_id").val("");
//        var territory_id=$('#territory_id').val();
//        if(territory_id>0)
//        {
//            $('#district_id_container').show();
//            $('#upazilla_id_container').hide();
//            $('#leading_farmer_container').hide();
//            $.ajax({
//                url: base_url+"common_controller/get_dropdown_districts_by_territoryid/",
//                type: 'POST',
//                datatype: "JSON",
//                data:{territory_id:territory_id},
//                success: function (data, status)
//                {
//
//                },
//                error: function (xhr, desc, err)
//                {
//                    console.log("error");
//
//                }
//            });
//        }
//        else
//        {
//            $('#upazilla_id_container').hide();
//            $('#district_id_container').hide();
//            $('#leading_farmer_container').hide();
//        }
//    });
//    $(document).on("change","#district_id",function()
//    {
//        $("#upazilla_id").val("");
//        $("#leading_farmer_id").val("");
//        var district_id=$('#district_id').val();
//        if(district_id>0)
//        {
//            $('#upazilla_id_container').show();
//            $('#leading_farmer_container').hide();
//            $.ajax({
//                url: base_url+"common_controller/get_dropdown_upazillas_by_districtid/",
//                type: 'POST',
//                datatype: "JSON",
//                data:{district_id:district_id},
//                success: function (data, status)
//                {
//
//                },
//                error: function (xhr, desc, err)
//                {
//                    console.log("error");
//
//                }
//            });
//        }
//        else
//        {
//            $('#upazilla_id_container').hide();
//            $('#leading_farmer_container').hide();
//        }
//    });
//
//    $(document).on("change","#upazilla_id",function()
//    {
//        $("#leading_farmer_id").val("");
//        var upazilla_id=$('#upazilla_id').val();
//        if(upazilla_id>0)
//        {
//            $('#leading_farmer_container').show();
//            $.ajax({
//                url: base_url+"common_controller/get_leading_farmers_by_upazillaid/",
//                type: 'POST',
//                datatype: "JSON",
//                data:{upazilla_id:upazilla_id},
//                success: function (data, status)
//                {
//
//                },
//                error: function (xhr, desc, err)
//                {
//                    console.log("error");
//
//                }
//            });
//        }
//        else
//        {
//            $('#leading_farmer_container').hide();
//        }
//    });
//
//
//    $(document).on("change","#crop_id",function()
//    {
//        $("#crop_type_id").val("");
//        $("#variety_id").val("");
//        $("#competitor_variety_id").val("");
//        var crop_id=$('#crop_id').val();
//        if(crop_id>0)
//        {
//            $('#crop_type_id_container').show();
//            $('#variety_id_container').hide();
//            $('#competitor_variety_id_container').hide();
//
//            $.ajax({
//                url: base_url+"common_controller/get_dropdown_croptypes_by_cropid/",
//                type: 'POST',
//                datatype: "JSON",
//                data:{crop_id:crop_id},
//                success: function (data, status)
//                {
//
//                },
//                error: function (xhr, desc, err)
//                {
//                    console.log("error");
//
//                }
//            });
//        }
//        else
//        {
//            $('#crop_type_id_container').hide();
//            $('#variety_id_container').hide();
//            $('#competitor_variety_id_container').hide();
//        }
//    });
//    $(document).on("change","#crop_type_id",function()
//    {
//        $("#variety_id").val("");
//        $("#competitor_variety_id").val("");
//        var crop_type_id=$('#crop_type_id').val();
//        if(crop_type_id>0)
//        {
//            $('#variety_id_container').show();
//            $('#competitor_variety_id_container').show();
//            $.ajax({
//                url: base_url+"common_controller/get_dropdown_arm_and_upcoming_varieties_by_croptypeid/",
//                type: 'POST',
//                datatype: "JSON",
//                data:{crop_type_id:crop_type_id},
//                success: function (data, status)
//                {
//
//                },
//                error: function (xhr, desc, err)
//                {
//                    console.log("error");
//
//                }
//            });
//
//            $.ajax({
//                url: base_url+"common_controller/get_dropdown_competitor_varieties_by_croptypeid/",
//                type: 'POST',
//                datatype: "JSON",
//                data:{crop_type_id:crop_type_id},
//                success: function (data, status)
//                {
//
//                },
//                error: function (xhr, desc, err)
//                {
//                    console.log("error");
//
//                }
//            });
//
//        }
//        else
//        {
//            $('#variety_id_container').hide();
//            $('#competitor_variety_id_container').hide();
//        }
//    });


    $(document).on("input",".expense_budget",function()
    {
        //findTotal();
        var total_budget=0;
        $(".expense_budget").each( function( index, element )
        {
            if($(this).val()==parseFloat($(this).val()))
            {
                total_budget=total_budget+parseFloat($(this).val());
            }
        });
        if(total_budget=>0)
        {
            $('#total_budget_container').show();
        }
        $('#total_budget').html(number_format(total_budget,2));
    });

    $(document).on("input",".participant_budget",function()
    {
        //findTotal_participant();
        var total_participant=0;
        $(".participant_budget").each( function( index, element )
        {
            if($(this).val()==parseFloat($(this).val()))
            {
                total_participant=total_participant+parseInt($(this).val());
            }
        });
        if(total_participant=>0)
        {
            $('#total_participant_container').show();
        }
        $('#no_of_participant').html(number_format(total_participant));
    });



    $(".browse_button").filestyle({input: false,icon: false,buttonText: "Upload",buttonName: "btn-primary"});

});
</script>

