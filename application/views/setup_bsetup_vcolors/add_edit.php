<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_save"]='#save_form';
    $action_data["action_save_new"]='#save_form';
    $action_data["action_clear"]='#save_form';
    $CI->load->view("action_buttons",$action_data);
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $color['id']; ?>" />
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
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="color[name]" id="name" class="form-control" value="<?php echo $color['name'];?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Days<span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="color[days]" id="days" class="form-control" value="<?php echo $color['days'];?>"/>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Select Color</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <div id="colorPicker"></div>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Color Code</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="color[color_code]" id="color_code" class="form-control" value="<?php echo $color['color_code'];?>"/>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        turn_off_triggers();
        $("#colorPicker").jqxColorPicker({
            width: 300,
            height: 300
        });
        $("#colorPicker").on('colorchange', function (event) {
            $("#color_code").val("#" + event.args.color.hex);
        });

    });
</script>
