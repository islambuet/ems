<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if(isset($CI->permissions['print'])&&($CI->permissions['print']==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'id'=>'button_action_print',
        'data-title'=>'PO LIST'
    );
}
if(isset($CI->permissions['download'])&&($CI->permissions['download']==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'id'=>'button_action_csv',
        'data-title'=>'PO LIST'
    );
}
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
                    Areas
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
                        <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_AREA__ADDRESS'); ?></th>
                        <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_ARM_VARIETY'); ?></th>
                        <th style="min-width: 100px;"><?php echo $CI->lang->line('LABEL_COMPETITOR_VARIETY'); ?></th>
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
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>