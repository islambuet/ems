<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_save"]='#save_form';
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
                <?php
                foreach($previous_varieties as $variety)
                {
                    ?>
                    <div class="">
                        <label><?php  echo $variety['variety_name'].' ('.$variety['whose'].')';?></label>
                    </div>
                    <?php
                }
                ?>
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
                    ?>
                    <div class="row show-grid">
                        <div class="col-xs-4">
                            <label class="control-label pull-right"><?php echo 'Picture - '.$i.' - '. $this->lang->line('LABEL_DATE');?></label>
                        </div>
                        <div class="col-xs-4">
                            <label class="form-control" style="background-color: #F5F5F5;"><?php echo System_helper::display_date($fsetup['date_sowing']+24*3600*$i*$fsetup['interval']); ?></label>
                        </div>
                    </div>
                    <?php
                    if(isset($visits_picture[$i]))
                    {
                        ?>
                            <div style="overflow-x: auto;" class="row show-grid">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                                        <th style="min-width: 250px;">Picture</th>
                                        <th style="min-width: 150px;"><?php echo $this->lang->line('LABEL_REMARKS');?></th>
                                        <th style="min-width: 150px;"><?php echo $this->lang->line('LABEL_FEEDBACK');?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach($previous_varieties as $variety)
                                    {
                                        ?>
                                        <tr>
                                            <td><?php echo $variety['variety_name']; ?></td>
                                            <td>
                                                <?php
                                                $image=base_url().'images/no_image.jpg';
                                                if(isset($visits_picture[$i][$variety['variety_id']]['picture_url'])&&strlen($visits_picture[$i][$variety['variety_id']]['picture_url'])>0)
                                                {
                                                    $image=$visits_picture[$i][$variety['variety_id']]['picture_url'];

                                                }
                                                ?>
                                                <img style="max-width: 250px;" src="<?php echo $image;?>">
                                            </td>
                                            <td>
                                                <?php
                                                $text='';
                                                if(isset($visits_picture[$i][$variety['variety_id']]))
                                                {
                                                    $text.='<b>Entry By</b>:'.$users[$visits_picture[$i][$variety['variety_id']]['user_created']]['name'];
                                                    $text.='<br><b>Entry Time</b>:'.System_helper::display_date_time($visits_picture[$i][$variety['variety_id']]['date_created']);
                                                    $text.='<br><b>Remarks</b>:<br>'.nl2br($visits_picture[$i][$variety['variety_id']]['remarks']);
                                                }
                                                echo $text;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $text='';
                                                if((isset($visits_picture[$i][$variety['variety_id']]['user_feedback']))&&(($visits_picture[$i][$variety['variety_id']]['user_feedback'])>0))
                                                {
                                                    $text.='<b>Entry By</b>:'.$users[$visits_picture[$i][$variety['variety_id']]['user_feedback']]['name'];
                                                    $text.='<br><b>Entry Time</b>:'.System_helper::display_date_time($visits_picture[$i][$variety['variety_id']]['date_feedback']);
                                                    $text.='<br><b>Feedback</b>:<br>'.nl2br($visits_picture[$i][$variety['variety_id']]['feedback']);
                                                }
                                                else
                                                {
                                                    $text=$CI->lang->line('LABEL_FEEDBACK_NOT_GIVEN');
                                                }
                                                echo $text;
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php
                    }
                    else
                    {
                        ?>
                        <div class="row show-grid">
                            <div class="col-xs-4">

                            </div>
                            <div class="col-xs-4">
                                <label class="control-label">Visit Not Done Yet</label>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
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
                            </div>
                            <div class="col-xs-4">
                                <label class="control-label"><?php echo $headers['name'];?></label>
                            </div>
                        </div>
                            <div style="overflow-x: auto;" class="row show-grid">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                                        <th style="min-width: 250px;">Picture</th>
                                        <th style="min-width: 150px;"><?php echo $this->lang->line('LABEL_REMARKS');?></th>
                                        <th style="min-width: 150px;"><?php echo $this->lang->line('LABEL_FEEDBACK');?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    foreach($previous_varieties as $variety)
                                    {
                                        ?>
                                        <tr>
                                            <td><?php echo $variety['variety_name']; ?></td>
                                            <td>
                                                <?php
                                                $image=base_url().'images/no_image.jpg';
                                                if(isset($fruits_picture[$headers['id']][$variety['variety_id']]['picture_url'])&&strlen($fruits_picture[$headers['id']][$variety['variety_id']]['picture_url'])>0)
                                                {
                                                    $image=$fruits_picture[$headers['id']][$variety['variety_id']]['picture_url'];
                                                }
                                                ?>
                                                <img style="max-width: 250px;" src="<?php echo $image;?>">
                                            </td>
                                            <td>
                                                <?php
                                                $text='';
                                                if(isset($fruits_picture[$headers['id']][$variety['variety_id']]))
                                                {
                                                    $text.='<b>Entry By</b>:'.$users[$fruits_picture[$headers['id']][$variety['variety_id']]['user_created']]['name'];
                                                    $text.='<br><b>Entry Time</b>:'.System_helper::display_date_time($fruits_picture[$headers['id']][$variety['variety_id']]['date_created']);
                                                    $text.='<br><b>Remarks</b>:<br>'.nl2br($fruits_picture[$headers['id']][$variety['variety_id']]['remarks']);
                                                }
                                                echo $text;
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                $text='';
                                                if((isset($fruits_picture[$headers['id']][$variety['variety_id']]['user_feedback']))&&(($fruits_picture[$headers['id']][$variety['variety_id']]['user_feedback'])>0))
                                                {
                                                    $text.='<b>Entry By</b>:'.$users[$fruits_picture[$headers['id']][$variety['variety_id']]['user_feedback']]['name'];
                                                    $text.='<br><b>Entry Time</b>:'.System_helper::display_date_time($fruits_picture[$headers['id']][$variety['variety_id']]['date_feedback']);
                                                    $text.='<br><b>Feedback</b>:<br>'.nl2br($fruits_picture[$headers['id']][$variety['variety_id']]['feedback']);
                                                }
                                                else
                                                {
                                                    $text=$CI->lang->line('LABEL_FEEDBACK_NOT_GIVEN');
                                                }
                                                echo $text;
                                                ?>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php
                    }
                    else
                    {
                        ?>
                        <div class="row show-grid">
                            <div class="col-xs-4">
                                <label class="control-label pull-right"><?php echo $headers['name'];?></label>
                            </div>
                            <div class="col-xs-4">
                                <label class="control-label">Visit Not Done Yet</label>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a class="accordion-toggle external" data-toggle="collapse"  data-target="#collapse_disease_picture" href="#">Disease Picture and remarks</a>
                </h4>
            </div>
            <div id="collapse_disease_picture" class="panel-collapse collapse">
                <?php
                if(sizeof($disease_picture)>0)
                {
                    ?>
                    <div id="disease_container">
                        <div style="overflow-x: auto;" class="row show-grid">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                                    <th style="min-width: 250px;">Picture</th>
                                    <th style="min-width: 150px;"><?php echo $this->lang->line('LABEL_REMARKS');?></th>
                                    <th style="min-width: 150px;"><?php echo $this->lang->line('LABEL_FEEDBACK');?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach($disease_picture as $index=>$disease_info)
                                {
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo $previous_varieties[$disease_info['variety_id']]['variety_name']; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $image=base_url().'images/no_image.jpg';
                                            if(strlen($disease_info['picture_url'])>0)
                                            {
                                                $image=$disease_info['picture_url'];
                                            }
                                            ?>
                                            <img style="max-width: 250px;" src="<?php echo $image;?>">
                                        </td>
                                        <td>
                                            <?php
                                            $text='';
                                            {
                                                $text.='<b>Entry By</b>:'.$users[$disease_info['user_created']]['name'];
                                                $text.='<br><b>Entry Time</b>:'.System_helper::display_date_time($disease_info['date_created']);
                                                $text.='<br><b>Remarks</b>:<br>'.nl2br($disease_info['remarks']);
                                            }
                                            echo $text;
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $text='';
                                            if((isset($disease_info['user_feedback']))&&(($disease_info['user_feedback'])>0))
                                            {
                                                $text.='<b>Entry By</b>:'.$users[$disease_info['user_feedback']]['name'];
                                                $text.='<br><b>Entry Time</b>:'.System_helper::display_date_time($disease_info['date_feedback']);
                                                $text.='<br><b>Feedback</b>:<br>'.nl2br($disease_info['feedback']);
                                            }
                                            else
                                            {
                                                $text=$CI->lang->line('LABEL_FEEDBACK_NOT_GIVEN');
                                            }
                                            echo $text;
                                            ?>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
                }
                else
                {
                    ?>
                    <div class="row show-grid">
                        <div class="col-xs-4">

                        </div>
                        <div class="col-xs-4">
                            <label class="control-label">No Disease Found Yet</label>
                        </div>
                    </div>
                    <?php
                }
                ?>
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