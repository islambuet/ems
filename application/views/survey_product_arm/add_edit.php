<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_save"]='#save_form';
    $CI->load->view("action_buttons",$action_data);
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $variety['id']; ?>" />
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
                <textarea class="form-control" id="remarks" name="survey[characteristics]"><?php echo $survey['characteristics']; ?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Cultivation Period 1</label>
            </div>
            <div class="col-xs-2">
                <input type="text" name="date_start" class="form-control datepicker" value="<?php if($survey['date_start']!=0){echo date('d-F',$survey['date_start']);}?>"/>
            </div>
            <div class="col-xs-2">
                <input type="text" name="date_end" class="form-control datepicker" value="<?php if($survey['date_end']!=0){echo date('d-F',$survey['date_end']);}?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Cultivation Period 2</label>
            </div>
            <div class="col-xs-2">
                <input type="text" name="date_start2" class="form-control datepicker" value="<?php if($survey['date_start2']!=0){echo date('d-F',$survey['date_start2']);}?>"/>
            </div>
            <div class="col-xs-2">
                <input type="text" name="date_end2" class="form-control datepicker" value="<?php if($survey['date_end2']!=0){echo date('d-F',$survey['date_end2']);}?>"/>
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
            <div class="col-xs-4">
                <input type="file" class="browse_button" data-preview-container="#image" name="image">
            </div>

        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Compare With Other Variety</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" id="remarks" name="survey[comparison]"><?php echo $survey['comparison']; ?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" id="remarks" name="survey[remarks]"><?php echo $survey['remarks']; ?></textarea>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        turn_off_triggers();
        $(".datepicker").datepicker({dateFormat : 'dd-MM'});
        $(".browse_button").filestyle({input: false,icon: false,buttonText: "Upload",buttonName: "btn-primary"});

    });
</script>
