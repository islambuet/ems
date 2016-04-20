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


        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date($visit['date']);?></label>
            </div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DAY');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo date('l',$visit['date']);?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SHIFT');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $visit['shift_name'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CUSTOMER_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $visit['customer_name'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Activities</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $visit['activities'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Activities Picture</label>
            </div>
            <div class="col-xs-4">
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

            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Problem</label>
            </div>
            <div class="col-xs-4">
                <label class="control-label"><?php echo $visit['problem'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Problem Picture</label>
            </div>
            <div class="col-xs-4">
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

            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Recommendation</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $visit['recommendation'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Solutions</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                Solution history will show here after solution task
            </div>
        </div>
    </div>

    <div class="clearfix"></div>

<script type="text/javascript">

    jQuery(document).ready(function()
    {
        turn_off_triggers();

    });
</script>
