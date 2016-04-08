<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $CI->load->view("action_buttons",$action_data);
?>
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $pv['division_name'];?></label>
            </div>
        </div>

        <div class="row show-grid" id="zone_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $pv['zone_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="territory_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $pv['territory_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="district_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $pv['district_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="upazilla_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $pv['upazilla_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="crop_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $pv['crop_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="crop_type_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $pv['crop_type_name'];?></label>
            </div>
        </div>
        <div style="<?php if(!($pv['variety_name'])){echo 'display:none';} ?>" class="row show-grid" id="variety_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $pv['variety_name'];?></label>
            </div>
        </div>
        <div style="<?php if(!(strlen($pv['other_variety_name'])>0)){echo 'display:none';} ?>" class="row show-grid" id="other_variety_name_container">
            <div class="col-xs-4">
                <label class="control-label pull-right">Other Variety</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $pv['other_variety_name'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Farmer's Name</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $pv['name'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ADDRESS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $pv['address'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Contact no</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $pv['contact_no'];?></label>
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
                    <th style="width: 100px;"><?php echo $CI->lang->line('LABEL_DATE'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_REMARKS'); ?></th>
                    <th style="min-width: 150px;">Picture</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($details as $index=>$detail)
                {
                    ?>
                    <tr>
                        <td>
                            <?php echo System_helper::display_date($detail['date_remarks']); ?>
                        </td>
                        <td>
                            <?php echo $detail['remarks']; ?>
                        </td>
                        <td>
                            <div class="image_container" id="image_<?php echo $index+1;?>">
                                <?php
                                $image='images/no_image.jpg';
                                if(strlen($detail['picture'])>0)
                                {
                                    $image=$detail['picture'];
                                }
                                ?>
                                <img style="max-width: 250px;" src="<?php echo base_url().$image;?>">
                            </div>
                        </td>
                    </tr>
                <?php
                }
                ?>

                </tbody>
            </table>

        </div>
    </div>

    <div class="clearfix"></div>

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
    });
</script>
