<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE"),
    'id'=>'button_action_save',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id'];?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $division_name;?></label>
            </div>

        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
            </div>

            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $zone_name;?></label>
            </div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label>
            </div>

            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $territory_name;?></label>
            </div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_EMPLOYEE_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $name;?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="widget-header">
                <div class="title">
                    Add Areas
                </div>
                <div class="clearfix"></div>
            </div>
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?></th>
                        <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME'); ?></th>
                        <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_AREA_NAME'); ?></th>
                        <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_AREA_ADDRESS'); ?></th>
                        <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_ARM_VARIETY'); ?></th>
                        <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_COMPETITOR_VARIETY'); ?></th>
                        <th style="min-width: 100px;">Action</th>
                    </tr>
                    </thead>
                    <tbody id="items_old_container">
                    <?php
                    foreach($old_item as $index=>$value)
                    {
                        ?>
                        <tr>
                            <td>
                                <label style="font-weight: normal;"><?php echo $value['district_name']; ?></label>
                            </td>
                            <td>
                                <label style="font-weight: normal;"><?php echo $value['upazilla_name']; ?></label>
                            </td>
                            <td>
                                <label style="font-weight: normal;"><?php echo $value['area_name']; ?></label>
                            </td>
                            <td>
                                <label style="font-weight: normal;"><?php echo $value['area_address']; ?></label>
                            </td>
                            <td>
                                <label style="font-weight: normal;"><?php echo $value['arm_variety']; ?></label>
                            </td>
                            <td>
                                <label style="font-weight: normal;"><?php echo $value['competitor_variety']; ?></label>
                            </td>
                            <?php
                            if(isset($CI->permissions['delete']) && ($CI->permissions['delete']==1))
                            {
                            ?>
                                <td>
                                    <select id="status" name="old_items[<?php echo $index;?>][status]" class="form-control" tabindex="-1">
                                        <option value="<?php echo $CI->config->item('system_status_active');?>"
                                            <?php
                                            if ($value['status'] == $CI->config->item('system_status_active')) {
                                                echo "selected='selected'";
                                            }
                                            ?>><?php echo $CI->lang->line('ACTIVE');?></option>
                                        <option value="<?php echo $CI->config->item('system_status_inactive');?>"
                                            <?php
                                            if ($value['status'] == $CI->config->item('system_status_inactive')) {
                                                echo "selected='selected'";
                                            }
                                            ?>><?php echo $CI->lang->line('INACTIVE');?></option>
                                        <option value="<?php echo $CI->config->item('system_status_delete');?>"
                                            <?php
                                            if ($value['status'] == $CI->config->item('system_status_delete')) {
                                                echo "selected='selected'";
                                            }
                                            ?>><?php echo $CI->lang->line('DELETE');?></option>
                                    </select>
                                </td>
                            <?php
                            }
                            ?>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">

                </div>
                <div class="col-xs-4">
                    <button type="button" class="btn btn-warning system_button_add_more" data-current-id="0"><?php echo $CI->lang->line('LABEL_ADD_MORE');?></button>
                </div>
                <div class="col-xs-4">

                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>

<div id="system_content_add_more" style="display: none;">
    <table>
        <tbody>
        <tr>
            <td>
                <select id="district_id" class="form-control district" tabindex="-1">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <?php
                        foreach($districts as $key=>$district)
                        {
                    ?>
                            <option value="<?php echo $key;?>"><?php echo $district;?></option>
                    <?php
                        }
                    ?>
                </select>
            </td>

            <td>
                <select id="upazilla_id" class="form-control upazilla" tabindex="-1">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                </select>
            </td>
            <td>
                <textarea class="form-control area_name"></textarea>
            </td>
            <td>
                <textarea class="form-control area_address"></textarea>
            </td>
            <td>
                <textarea class="form-control arm"></textarea>
            </td>
            <td>
                <textarea class="form-control competitor"></textarea>
            </td>
            <td>
                <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
    <div id="items_old">
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        $(document).off("click", ".system_button_add_more");
        $(document).on("click", ".system_button_add_more", function(event)
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            current_id=current_id+1;
            $(this).attr('data-current-id',current_id);
            var content_id='#system_content_add_more table tbody';

            $(content_id+' .district').attr('id','district_'+current_id);
            $(content_id+' .district').attr('data-current-id',current_id);
            $(content_id+' .upazilla').attr('id','upazilla_'+current_id);
            $(content_id+' .upazilla').attr('id','upazilla_'+current_id);
            $(content_id+' .upazilla').attr('name','items['+current_id+'][upazilla_id]');
            $(content_id+' .area_name').attr('name','items['+current_id+'][area_name]');
            $(content_id+' .area_address').attr('name','items['+current_id+'][area_address]');
            $(content_id+' .arm').attr('name','items['+current_id+'][arm_variety]');
            $(content_id+' .competitor').attr('name','items['+current_id+'][competitor_variety]');

            var html=$(content_id).html();
            $("#items_old_container").append(html);
        });

        $(document).off('click','.system_button_add_delete');
        $(document).on("click", ".system_button_add_delete", function(event)
        {
            $(this).closest('tr').remove();
        });

        $(document).off('change', '.district');
        $(document).on("change",".district",function()
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            $("#upazilla_"+current_id).val("");
            var district_id=$('#district_'+current_id).val();
            if(district_id>0)
            {
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_upazillas_by_districtid/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{district_id:district_id,html_container_id:'#upazilla_'+current_id},
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

            }
        });



    });
</script>
