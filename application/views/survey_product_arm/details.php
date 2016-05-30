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
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $variety['crop_name'];?></label>
            </div>
        </div>

        <div style="<?php if(!($variety['crop_type_id']>0)){echo 'display:none';} ?>" class="row show-grid" id="crop_type_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $variety['type_name'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $variety['name'];?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Characteristics</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo nl2br($survey['characteristics']);?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Cultivation Period</label>
            </div>
            <div class="col-xs-2">
                <label class="form-control"><?php if($survey['date_start']!=0){echo date('d-F',$survey['date_start']);}?></label>
            </div>
            <div class="col-xs-2">
                <label class="form-control"><?php if($survey['date_end']!=0){echo date('d-F',$survey['date_end']);}?></label>

            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Picture</label>
            </div>
            <div class="col-xs-4" id="image">
                <?php
                $image=base_url().'images/no_image.jpg';
                if(strlen($survey['picture_url'])>0)
                {
                    $image=$survey['picture_url'];
                }
                ?>
                <img style="max-width: 250px;" src="<?php echo $image;?>">
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo nl2br($survey['remarks']);?></label>
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
