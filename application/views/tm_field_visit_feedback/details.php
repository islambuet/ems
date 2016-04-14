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
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_YEAR');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label><?php echo $fsetup['year'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SEASON');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label><?php echo $fsetup['season_name'];?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['division_name'];?></label>
            </div>
        </div>

        <div class="row show-grid" id="zone_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['zone_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="territory_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['territory_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="district_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['district_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="customer_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['upazilla_name'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['crop_name'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['crop_type_name'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['variety_name'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Farmer's Name</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['name'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ADDRESS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['address'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_SOWING');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date($fsetup['date_sowing']); ?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_TRANSPLANT');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php if($fsetup['date_transplant']>0){echo System_helper::display_date($fsetup['date_transplant']); }?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NUM_VISITS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['num_visits'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_INTERVAL');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $fsetup['interval'];?></label>
            </div>
        </div>

    </div>
    <div class="panel-group" id="accordion">
        <?php
        if(sizeof($visits_picture)>0)
        {
            ?>
            <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle external" data-toggle="collapse"  data-target="#collapse_visits_picture" href="#">Visit Picture and remarks</a>
                </h4>
            </div>
            <div id="collapse_visits_picture" class="panel-collapse collapse in">
                <?php
                for($i=1;$i<=$fsetup['num_visits'];$i++)
                {
                    if(isset($visits_picture[$i]))
                    {
                        ?>
                        <div class="row show-grid">
                            <div class="col-xs-4">
                                <label class="control-label pull-right">Picture - <?php echo $i;?></label>
                            </div>
                            <div class="col-xs-4" id="visit_image_<?php echo $i; ?>">
                                <?php
                                $image=base_url().'images/no_image.jpg';
                                if(isset($visits_picture[$i]['picture_url'])&&strlen($visits_picture[$i]['picture_url'])>0)
                                {
                                    $image=$visits_picture[$i]['picture_url'];
                                }
                                ?>
                                <img style="max-width: 250px;" src="<?php echo $image;?>">
                            </div>
                            <div class="col-xs-4">
                            </div>
                        </div>
                        <div class="row show-grid">
                            <div class="col-xs-4">
                                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE');?></label>
                            </div>
                            <div class="col-xs-4">
                                <?php echo System_helper::display_date($fsetup['date_sowing']+24*3600*$i*$fsetup['interval']); ?>
                            </div>
                        </div>
                        <div class="row show-grid">
                            <div class="col-xs-4">
                                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
                            </div>
                            <div class="col-xs-4">
                                <?php
                                $remarks='';
                                if(isset($visits_picture[$i]['remarks'])&&strlen($visits_picture[$i]['remarks'])>0)
                                {
                                    $remarks=$visits_picture[$i]['remarks'];
                                }
                                echo $remarks;
                                ?>
                            </div>
                        </div>
                        <div class="row show-grid">
                            <div class="col-xs-4">
                                <label class="control-label pull-right"><?php if(strlen($remarks)>0){echo 'Remarks';}else{ echo 'Picture';} ?> <?php echo $CI->lang->line('LABEL_ENTRY_TIME');?></label>
                            </div>
                            <div class="col-xs-4">
                                <?php if(isset($visits_picture[$i]['date_created'])){ echo System_helper::display_date_time($visits_picture[$i]['date_created']);} ?>
                            </div>
                        </div>
                        <div class="row show-grid">
                            <div class="col-xs-4">
                                <label class="control-label pull-right"><?php if(strlen($remarks)>0){echo 'Remarks';}else{ echo 'Picture';} ?> By</label>
                            </div>
                            <div class="col-xs-4">
                                <?php echo $users[$visits_picture[$i]['user_created']]['name'];?>
                            </div>
                        </div>
                        <div class="row show-grid">
                            <div class="col-xs-4">
                                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FEEDBACK');?></label>
                            </div>
                            <div class="col-xs-8">
                                <?php
                                $feedback='';
                                if(isset($visits_picture[$i]['feedback'])&&strlen($visits_picture[$i]['feedback'])>0)
                                {
                                    $feedback=$visits_picture[$i]['feedback'];
                                }
                                ?>
                                <?php echo $feedback?$feedback:$CI->lang->line('LABEL_FEEDBACK_NOT_GIVEN'); ?>
                            </div>
                        </div>
                        <?php
                        if($feedback)
                        {
                            ?>
                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FEEDBACK');?> <?php echo $CI->lang->line('LABEL_ENTRY_TIME');?></label>
                                </div>
                                <div class="col-xs-8">
                                    <?php echo System_helper::display_date_time($visits_picture[$i]['date_feedback']); ?>
                                </div>
                            </div>
                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FEEDBACK');?> By</label>
                                </div>
                                <div class="col-xs-8">
                                    <?php echo $users[$visits_picture[$i]['user_feedback']]['name'];?>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                <?php
                }
                ?>
            </div>
        </div>
            <?php
        }
        if(sizeof($fruits_picture)>0)
        {
            ?>
            <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle external" data-toggle="collapse"  data-target="#collapse_fruit_picture" href="#">Fruit Picture and remarks</a>
                </h4>
            </div>
            <div id="collapse_fruit_picture" class="panel-collapse collapse">
                <?php
                foreach($fruits_picture_headers as $headers)
                {
                    if(isset($fruits_picture[$headers['id']]))
                    {
                        ?>
                        <div class="row show-grid">
                            <div class="col-xs-4">
                                <label class="control-label pull-right"><?php echo $headers['name'];?></label>
                            </div>
                            <div class="col-xs-4" id="fruit_image_<?php echo $headers['id']; ?>">
                                <?php
                                $image=base_url().'images/no_image.jpg';
                                if(isset($fruits_picture[$headers['id']]['picture_url'])&&strlen($fruits_picture[$headers['id']]['picture_url'])>0)
                                {
                                    $image=$fruits_picture[$headers['id']]['picture_url'];
                                }
                                ?>
                                <img style="max-width: 250px;" src="<?php echo $image;?>">
                            </div>
                            <div class="col-xs-4">
                            </div>
                        </div>

                        <div class="row show-grid">
                            <div class="col-xs-4">
                                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
                            </div>
                            <div class="col-xs-4">
                                <?php
                                $remarks='';
                                if(isset($fruits_picture[$headers['id']]['remarks'])&&strlen($fruits_picture[$headers['id']]['remarks'])>0)
                                {
                                    $remarks=$fruits_picture[$headers['id']]['remarks'];
                                }
                                echo $remarks;
                                ?>
                            </div>
                        </div>
                        <div class="row show-grid">
                            <div class="col-xs-4">
                                <label class="control-label pull-right"><?php if(strlen($remarks)>0){echo 'Remarks';}else{ echo 'Picture';} ?> <?php echo $CI->lang->line('LABEL_ENTRY_TIME');?></label>
                            </div>
                            <div class="col-xs-4">
                                <?php if(isset($fruits_picture[$headers['id']]['date_created'])){ echo System_helper::display_date_time($fruits_picture[$headers['id']]['date_created']);} ?>
                            </div>
                        </div>
                        <div class="row show-grid">
                            <div class="col-xs-4">
                                <label class="control-label pull-right"><?php if(strlen($remarks)>0){echo 'Remarks';}else{ echo 'Picture';} ?> By</label>
                            </div>
                            <div class="col-xs-4">
                                <?php echo $users[$fruits_picture[$headers['id']]['user_created']]['name'];?>
                            </div>
                        </div>
                        <div class="row show-grid">
                            <div class="col-xs-4">
                                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FEEDBACK');?></label>
                            </div>
                            <div class="col-xs-8">
                                <?php
                                $feedback='';
                                if(isset($fruits_picture[$headers['id']]['feedback'])&&strlen($fruits_picture[$headers['id']]['feedback'])>0)
                                {
                                    $feedback=$fruits_picture[$headers['id']]['feedback'];
                                }
                                ?>
                                <?php echo $feedback?$feedback:$CI->lang->line('LABEL_FEEDBACK_NOT_GIVEN'); ?>
                            </div>
                        </div>
                        <?php
                        if($feedback)
                        {
                            ?>
                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FEEDBACK');?> <?php echo $CI->lang->line('LABEL_ENTRY_TIME');?></label>
                                </div>
                                <div class="col-xs-8">
                                    <?php echo System_helper::display_date_time($fruits_picture[$headers['id']]['date_feedback']); ?>
                                </div>
                            </div>
                            <div class="row show-grid">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FEEDBACK');?> By</label>
                                </div>
                                <div class="col-xs-8">
                                    <?php echo $users[$fruits_picture[$headers['id']]['user_feedback']]['name'];?>
                                </div>
                            </div>
                        <?php
                        }
                    }
                }
                ?>
            </div>
        </div>
            <?php
        }
        if(sizeof($disease_picture)>0)
        {
            ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a class="accordion-toggle external" data-toggle="collapse"  data-target="#collapse_disease_picture" href="#">Disease Picture and remarks</a>
                    </h4>
                </div>
                <div id="collapse_disease_picture" class="panel-collapse collapse">
                    <div id="disease_container">
                        <?php
                        foreach($disease_picture as $index=>$detail)
                        {
                            ?>
                                <div class="row show-grid">
                                    <div class="col-xs-4">
                                        <label class="control-label pull-right">Picture</label>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="disease_image_container" id="disease_image_<?php echo $index+1;?>">
                                            <?php
                                            $image=base_url().'images/no_image.jpg';
                                            if(strlen($detail['picture_url'])>0)
                                            {
                                                $image=$detail['picture_url'];
                                            }
                                            ?>
                                            <img style="max-width: 250px;" src="<?php echo $image;?>">
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                    </div>
                                </div>
                                <div class="row show-grid">
                                    <div class="col-xs-4">
                                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
                                    </div>
                                    <div class="col-xs-4">
                                        <?php
                                        $remarks='';
                                        if(isset($detail['remarks'])&&strlen($detail['remarks'])>0)
                                        {
                                            $remarks=$detail['remarks'];
                                        }
                                        echo $remarks;
                                        ?>
                                    </div>
                                </div>
                                <div class="row show-grid">
                                    <div class="col-xs-4">
                                        <label class="control-label pull-right"><?php if(strlen($remarks)>0){echo 'Remarks';}else{ echo 'Picture';} ?> <?php echo $CI->lang->line('LABEL_ENTRY_TIME');?></label>
                                    </div>
                                    <div class="col-xs-4">
                                        <?php if(isset($detail['date_created'])){ echo System_helper::display_date_time($detail['date_created']);} ?>
                                    </div>
                                </div>
                                <div class="row show-grid">
                                    <div class="col-xs-4">
                                        <label class="control-label pull-right"><?php if(strlen($remarks)>0){echo 'Remarks';}else{ echo 'Picture';} ?> By</label>
                                    </div>
                                    <div class="col-xs-4">
                                        <?php echo $users[$detail['user_created']]['name'];?>
                                    </div>
                                </div>
                                <div class="row show-grid">
                                    <div class="col-xs-4">
                                        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FEEDBACK');?></label>
                                    </div>
                                    <div class="col-xs-8">
                                        <?php
                                        $feedback='';
                                        if(isset($detail['feedback'])&&strlen($detail['feedback'])>0)
                                        {
                                            $feedback=$detail['feedback'];
                                        }
                                        ?>
                                        <?php echo $feedback?$feedback:$CI->lang->line('LABEL_FEEDBACK_NOT_GIVEN'); ?>
                                    </div>
                                </div>
                                <?php
                                if($feedback)
                                {
                                    ?>
                                    <div class="row show-grid">
                                        <div class="col-xs-4">
                                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FEEDBACK');?> <?php echo $CI->lang->line('LABEL_ENTRY_TIME');?></label>
                                        </div>
                                        <div class="col-xs-8">
                                            <?php echo System_helper::display_date_time($detail['date_feedback']); ?>
                                        </div>
                                    </div>
                                    <div class="row show-grid">
                                        <div class="col-xs-4">
                                            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FEEDBACK');?> By</label>
                                        </div>
                                        <div class="col-xs-8">
                                            <?php echo $users[$detail['user_feedback']]['name'];?>
                                        </div>
                                    </div>
                                <?php
                                }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="clearfix"></div>

<script type="text/javascript">

    jQuery(document).ready(function()
    {
        turn_off_triggers();
    });
</script>
