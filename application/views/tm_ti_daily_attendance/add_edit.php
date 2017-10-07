<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_data=array();
$action_data["action_back"]=base_url($CI->controller_url);
$action_data["action_save"]='#save_form';
$action_data["action_clear"]='#save_form';
$CI->load->view("action_buttons",$action_data);
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_attendance');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
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
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php echo System_helper::display_date($item['date_reported']);?>
            </div>
        </div>


        <div style="overflow-x: auto;" class="row show-grid">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="min-width: 250px;">Task List</th>
                    <th style="min-width: 250px;">Work Done</th>
                    <th style="max-width: 150px;">Work Done Document</th>
                    <th style="min-width: 50px;">Work Done Time</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($item_details as $details)
                {
                    ?>
                    <tr>
                        <td style="width: 200px;">
                            <?php echo $details['remarks_started']; ?>
                        </td>

                        <td style="width: 200px;">
                            <?php echo $details['remarks_reported']; ?>
                        </td>

                        <td>
                            <div class="preview_container_file" id="preview_container_file_<?php echo $details['id']; ?>">
                                <?php
                                if(substr($details['file_type'],0,5)=='image')
                                {
                                    ?>
                                    <img src="<?php echo base_url($details['file_location']); ?>" height="200px">
                                <?php
                                }
                                else
                                {
                                    ?>
                                    <a target="_blank" class="external" href="<?php echo base_url($details['file_location']); ?>"><?php echo $details['file_name']; ?></a>
                                <?php
                                }
                                ?>
                            </div>
                        </td>
                        <td>
                            <?php echo System_helper::display_date_time($details['date_reported_task']); ?>
                        </td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </div>

<!--        <div class="row show-grid">-->
<!--            <div class="col-xs-4">-->
<!--                <label class="control-label pull-right">--><?php //echo $this->lang->line('LABEL_REMARKS_STARTING');?><!--</label>-->
<!--            </div>-->
<!--            <div class="col-sm-4 col-xs-8">-->
<!--                --><?php
//                    $number=1;
//                    foreach($item_details as $detail)
//                    {
//                        if($detail['remarks_started']!=null)
//                        {
//                            echo $number.'. '.$detail['remarks_started'].'<br>';
//                            $number++;
//                        }
//                    }
//                ?>
<!--                --><?php ///*echo nl2br($item['remarks_started']);*/?>
<!--            </div>-->
<!--        </div>-->
<!--        <div class="row show-grid">-->
<!--            <div class="col-xs-4">-->
<!--                <label class="control-label pull-right">--><?php //echo $this->lang->line('LABEL_REMARKS_REPORTING');?><!--</label>-->
<!--            </div>-->
<!--            <div class="col-sm-4 col-xs-8">-->
<!--                --><?php
//                    $number=1;
//                    foreach($item_details as $detail)
//                    {
//                        if($detail['remarks_reported'])
//                        {
//                            echo $number.'. '.$detail['remarks_reported'].'<br>';
//                            $number++;
//                        }
//                    }
//                ?>
<!--            </div>-->
<!--        </div>-->
        <div class="row show-grid">
            <div class="col-xs-4">
                <label for="description" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZSC_COMMENT');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[zsc_comment]" class="form-control"><?php echo $item['zsc_comment'] ?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ATTENDANCE_STATUS');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select name="item[attendance]" class="form-control">
                    <!--<option value=""></option>-->
                    <option value="present"
                        <?php
                        if ($item['attendance'] == 'present') {
                            echo "selected='selected'";
                        }
                        ?> >Present
                    </option>
                    <option value="halfday"
                        <?php
                        if ($item['attendance'] == 'halfday') {
                            echo "selected='selected'";
                        }
                        ?> >Half Day</option>
                    <option value="absent"
                        <?php
                        if ($item['attendance'] == 'absent') {
                            echo "selected='selected'";
                        }
                        ?> >Absent</option>
                </select>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        turn_off_triggers();
        $(".datepicker").datepicker({dateFormat : display_date_format});
    });
</script>
