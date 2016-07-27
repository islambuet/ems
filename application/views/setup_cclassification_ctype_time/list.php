<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
?>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" name="type_time[crop_type_id]" value="<?php echo $type_time['crop_type_id']; ?>" />
    <input type="hidden" name="type_time[territory_id]" value="<?php echo $type_time['territory_id']; ?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Months</label>
            </div>
            <div class="col-xs-8">

                    <?php
                    for($i=1;$i<13;$i++)
                    {

                        ?>
                        <div class="checkbox">
                            <label><input type="checkbox" name="type_time[month_<?php echo $i;?>]" value="1" <?php if($type_time['month_'.$i]==1){echo 'checked';} ?>><?php echo date("M", mktime(0, 0, 0,$i,1, 2000));?></label>
                        </div>

                    <?php
                    }
                    ?>

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
