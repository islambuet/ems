<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
?>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" name="crop_type_id" value="<?php echo $crop_type_id; ?>" />
    <input type="hidden" name="territory_id" value="<?php echo $territory_id; ?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_START');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="date_start" class="form-control datepicker" value="<?php echo date('d-M',$date_start);?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_END');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="date_end" class="form-control datepicker" value="<?php echo date('d-M',$date_end);?>"/>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<style>
    .ui-datepicker-year{
        display:none;
    }
</style>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        $(".datepicker").datepicker({dateFormat : 'dd-M'});
    });
</script>
