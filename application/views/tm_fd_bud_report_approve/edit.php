<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_data=array();
$action_data["action_back"]=base_url($CI->controller_url);
$action_data["action_save"]='#save_form';
if($item['id']==0){
    $action_data["action_clear"]='#save_form';
}
$CI->load->view("action_buttons",$action_data);
?>
<style>
    .arm_remarks{
        width: 300px;
    }
    .com_remarks{
        width: 300px;
    }
</style>

<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
<input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
<input type="hidden" id="budget_id" name="item[budget_id]" value="<?php echo $item['budget_id']; ?>" />
<div class="row widget">
<div class="widget-header">
    <div class="title">
        <?php echo $title; ?>
    </div>
    <div class="clearfix"></div>
</div>

<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REPORTING_DATE');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-3 col-xs-9">
        <input type="text" name="item[date]" id="date" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date']);?>" />
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FIELD_DAY_DATE');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-3 col-xs-9">
        <input type="text" name="item[date_of_fd]" id="date_of_fd" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_of_fd']);?>" />
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item_info['crop_name'];?></label>
    </div>
</div>

<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item_info['crop_type_name'];?></label>
    </div>
</div>

<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_VARIETY_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-4">
        <label class="control-label"><?php echo $item_info['variety_name'];?></label>
    </div>
</div>

<?php
if($item_info['com_variety_name']){
    ?>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_COMPETITOR_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $item_info['com_variety_name'];?></label>
        </div>
    </div>
<?php }?>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item_info['division_name'];?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item_info['zone_name'];?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item_info['territory_name'];?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item_info['district_name'];?></label>
    </div>
</div>
<div style="" class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME');?></label>
    </div>
    <div class="col-sm-4 col-xs-8">
        <label class="control-label"><?php echo $item_info['upazilla_name'];?></label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_GUEST');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-3 col-xs-9">
        <input type="text" name="new_item[guest]" id="guest" class="participant_budget form-control integer_type_positive" value="<?php echo $new_item['guest']; ?>"/>
    </div>
</div>
<div class="row show-grid">
    <div class="col-sm-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_LEAD_FARMER');?></label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-sm-4">
    </div>
    <div class="col-sm-2 col-sm-4">
        <label class="control-label"><u>Expected</u></label>
    </div>
    <div class="col-sm-2 col-sm-4">
        <label class="control-label"><u>Actual</u></label>
    </div>
</div>
<?php
foreach($participants as $key=>$participant)
{
    ?>
    <div class="row show-grid">
        <div class="col-sm-4">
            <label class="control-label pull-right"><?php echo $leading_farmers[$key]['text'].' ('.$leading_farmers[$key]['phone_no'].')';?><span style="color: red;">*</span></label>
        </div>
        <div class="col-sm-2 col-sm-4">
            <label class="control-label"><?php if(isset($participant['number'])){echo $participant['number'];}?></label>
        </div>
        <div class="col-sm-2 col-sm-4">
            <input type="text" name="farmers[<?php echo $leading_farmers[$key]['value'];?>]" id="farmers[<?php echo $leading_farmers[$key]['value'];?>]" class="participant_budget form-control integer_type_positive" value="<?php if(isset($farmers[$leading_farmers[$key]['value']])){echo $farmers[$leading_farmers[$key]['value']]['number'];}?>"/>
        </div>
    </div>
<?php
}
?>

<div class="row show-grid">
    <div class="col-sm-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_CUSTOMER');?><span style="color: red;">*</span></label>
    </div>
    <div class="col-sm-2 col-sm-4">
        <label class="control-label"><?php echo $item_info['participant_through_customer'];?></label>
    </div>
    <div class="col-sm-2 col-sm-4">
        <input type="text" name="new_item[participant_through_customer]" id="participant_through_customer" class="participant_budget form-control integer_type_positive" value="<?php if(isset($new_item['participant_through_customer'])){echo $new_item['participant_through_customer'];}?>"/>
    </div>
</div>
<div class="row show-grid">
    <div class="col-sm-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_THROUGH_OTHERS');?><span style="color: red;">*</span></label>
    </div>
    <div class="col-sm-2 col-sm-4">
        <label class="control-label"><?php echo $item_info['participant_through_others'];?></label>
    </div>
    <div class="col-sm-2 col-sm-4">
        <input type="text" name="new_item[participant_through_others]" id="participant_through_others" class="participant_budget form-control integer_type_positive" value="<?php if(isset($new_item['participant_through_others'])){echo $new_item['participant_through_others'];}?>"/>
    </div>
</div>

<div style="" class="row show-grid">
    <div class="col-sm-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_EXPECTED_PARTICIPANT');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-2 col-sm-4">
        <label class="control-label"><?php echo number_format($item_info['no_of_participant']);?> (Person)</label>
    </div>
    <div style="<?php if(!($item['id']>0)){echo 'display:none';} ?>" class="col-sm-2 col-sm-4" id="total_participant_container">
        <label id="no_of_participant"><?php if(isset($new_item['total_participant'])) {echo number_format($new_item['total_participant']);}?> (Persons)</label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-sm-4">
        <label class="control-label pull-right">Field Day Expense :</label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-sm-4">
    </div>
    <div class="col-sm-2 col-sm-4">
        <label class="control-label"><u>Budgeted</u></label>
    </div>
    <div class="col-sm-2 col-sm-4">
        <label class="control-label"><u>Actual</u></label>
    </div>
</div>
<?php
foreach($expense_budget as $key=>$budget)
{
    ?>
    <div class="row show-grid">

        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $expense_items[$key]['text'];?><span style="color: red;">*</span></label>
        </div>
        <div class="col-sm-2 col-xs-4">
            <label class="control-label"><?php if(isset($budget['amount'])){echo number_format($budget['amount'],2);}?></label>
        </div>
        <div class="col-sm-2 col-xs-4">
            <input type="text" name="expense_report[<?php echo $key;?>]" id="expense_report[<?php echo $key;?>]" class="expense_budget form-control float_type_positive total_expense" value="<?php if(isset($expense_report[$key]['amount'])){echo $expense_report[$key]['amount'];}?>"/>
        </div>

        <div class="col-xs-4">
            <div class="panel panel-default" id="expense_files_container_<?php echo $key;?>">
                <div class="panel-heading">
                    <h4 class="panel-title" style="text-align: center;">
                        <a class="accordion-toggle external" data-toggle="collapse"  data-target="#collapse_<?php echo $key;?>" href="#">
                            <p style="text-align: left; margin-left: 25%">Add Vouchers (<?php echo $expense_items[$key]['text'];?>)</p>
                        </a>
                    </h4>
                </div>

                <div id="collapse_<?php echo $key;?>" class="panel-collapse collapse">

                    <table class="table table-responsive" style="table-layout: fixed;">
                        <tbody>

                        <?php
                        $id=0;
                        if(isset($expense_files[$key]))
                        {
                            foreach($expense_files[$key] as $expense_file)
                            {
                                $id=$id+1;
                                ?>
                                <tr style="width: 100%;">
                                    <td style="width: 25%;">
                                        <input type="file" id="expense_file_<?php echo $key;?>_<?php echo $id;?>" capture name="expense_file_<?php echo $key;?>_<?php echo $id;?>" data-dynamic-id="<?php echo $key;?>_<?php echo $id;?>" data-id="popup_image_<?php echo $key;?>_<?php echo $id;?>" class="expense_file" class="browse button"/>
                                        <input type="hidden" id="h_expense_files_<?php echo $key;?>_<?php echo $id;?>" name="h_expense_files[<?php echo $key;?>][<?php echo $id;?>]" value="<?php if(isset($expense_file['file_name'])) {echo $expense_file['file_name'];}?>">
                                        <input type="hidden" name="demo[<?php echo $key;?>][<?php echo $id;?>]" value="<?php echo $id;?>">
                                    </td>
                                    <td style="width: 50%; margin: 0 auto;">
                                        <p class="popup_image" id="popup_image_<?php echo $key;?>_<?php echo $id;?>" data-popup-id="<?php echo $key;?>_<?php echo $id;?>" style="font-weight:bold;  text-align: center; margin-top: 5px;"  width="300" height="200" title="<?php echo $expense_file['file_name'];?>" file-link="<?php echo $CI->config->item('system_image_base_url').$expense_file['file_location'];?>"><?php echo $expense_file['file_name'];?></p>
                                        <div id="myModal_<?php echo $key;?>_<?php echo $id;?>" class="modal" style="display: none;">
                                            <span class="close" data-popup-id="<?php echo $key;?>_<?php echo $id;?>">&times;</span>
                                            <img class="modal-content" id="modal-image_<?php echo $key;?>_<?php echo $id;?>">
                                            <div id="caption_<?php echo $key;?>_<?php echo $id;?>" class="caption"></div>
                                        </div>
                                    </td>
                                    <td style="width: 25%;">
                                        <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
                                    </td>
                                </tr>
                            <?php
                            }
                        }
                        ?>

                        </tbody>
                    </table>
                    <div style="text-align: center;">
                        <button type="button" class="btn btn-warning system_button_exp_add_more" data-current-id="<?php echo $key;?>" data-dynamic-id="<?php echo $id;?>" ><?php echo $CI->lang->line('LABEL_ADD_MORE');?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}
?>

<div style="display: none;">
    <table id="expense_add_more_file_contents">
        <tbody>
        <tr style="width: 100%;">
            <td style="width: 25%;">
                <input type="file" capture class="input_type_file">
                <input type="hidden" class="demo" name="" value="">
            </td>
            <td style="width: 50%; margin: 0 auto;">
                <p class="popup_image_new" id="" style="font-weight:bold; text-align: center; margin-top: 5px;">No File Selected</p>
            </td>
            <td style="width: 25%;">
                <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div style="" class="row show-grid">
    <div class="col-sm-4">
        <label class="control-label pull-right"> Total Amount :</label>
    </div>
    <div class="col-sm-2 col-sm-4">
        <label id="total_budget"><?php echo number_format($item_info['total_budget'],2);?> Tk.</label>
    </div>
    <div class="col-sm-2 col-sm-4" id="total_expense_container" style="<?php if($item['id']==0){echo 'display: none';}?>">
        <label id="total_expense"><?php echo number_format($new_item['total_expense'],2);?> Tk.</label>
    </div>
</div>

<div class="row show-grid">
    <div class="col-sm-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NEXT_SALES_TARGET');?> (kg)<span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-2 col-sm-4">
        <label id="sales_target"><?php echo $item_info['sales_target'];?> kg</label>
    </div>
    <div class="col-sm-2 col-sm-4">
        <input type="text" name="new_item[next_sales_target]" id="next_sales_target" class="form-control float_type_positive" value="<?php echo $new_item['next_sales_target']; ?>"/>
    </div>
</div>
<div class="row show-grid">
    <div class="col-sm-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ARM_MARKET_SIZE');?> (kg)</label>
    </div>
    <div class="col-sm-2 col-sm-4">
        <label id="sales_target"><?php echo $item_info['arm_market_size'];?> kg</label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-sm-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TOTAL_MARKET_SIZE');?> (kg)</label>
    </div>
    <div class="col-sm-2 col-sm-4">
        <label id="sales_target"><?php echo $item_info['total_market_size'];?> kg</label>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARTICIPANT_COMMENT');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-3 col-xs-9">
        <textarea class="form-control" id="participant_comment" name="new_item[participant_comment]"><?php echo $new_item['participant_comment'];?></textarea>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_RECOMMENDATION');?><span style="color:#FF0000">*</span></label>
    </div>
    <div class="col-sm-3 col-xs-9">
        <textarea class="form-control" id="recommendation" name="item[recommendation]"><?php echo $item['recommendation']; ?></textarea>
    </div>
</div>

<div class="row show-grid">
    <?php
    $type=substr($video_file_details['file_type'],0,5);
    $is_video=false;
    if($type=='video')
    {
        $is_video=true;
    }
    ?>
    <div class="col-xs-2">
        <label class="control-label">Upload a Video File<span style="color: red;">*</span></label>
    </div>
    <div class="col-xs-4">
        <div style="<?php if(isset($video_file_details['file_location'])){echo 'display:block;';}else{echo 'display:none;';}?>" id="video_preview_id">
            <video width="300" controls id="video_preview_id">
                <source src="<?php if(isset($video_file_details['file_location'])){ echo $CI->config->item('system_image_base_url').$video_file_details['file_location'];}?>" id="video_here">
            </video>
        </div>
        <div>
            <input type="file" class="browse_button file_multi_video" data-preview-container="#video" name="video" class="" accept="video/*">
            <input type="hidden" class="hidden_file" name="video_file[file_name]" value="<?php echo $video_file_details['file_name'];?>">
            <input type="hidden" class="hidden_file" name="video_file[file_type]" value="<?php echo $video_file_details['file_type'];?>">
            <h4 id="video">
                <?php
                if($is_video)
                {
                    echo $video_file_details['file_name'];
                }

                ?></h4>
        </div>
    </div>
</div>

<div id="files_container">
    <div style="overflow-x: auto;" class="row show-grid">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th style="min-width: 250px;">Program's Picture</th>
                <th style="min-width: 50px;">Upload</th>
                <th style="max-width: 150px;">Remarks</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach($file_details as $index=>$file)
            {
                $type=substr($file['file_type'],0,5);
                $is_image=false;
                if($type=='image')
                {
                    $is_image=true;
                }
                ?>
                <tr>
                    <td>
                        <div class="preview_container_file" id="preview_container_file_<?php echo $index+1;?>">
                            <?php
                            if($is_image)
                            {
                                ?>
                                <img style="max-width: 250px;" src="<?php echo $CI->config->item('system_image_base_url').$file['file_location']; ?>">
                            <?php
                            }
                            else
                            {
                                echo $file['file_name'];
                            }
                            ?>
                        </div>
                    </td>
                    <td>
                        <input type="file" id="file_<?php echo $index+1; ?>" name="file_<?php echo $index+1; ?>" data-current-id="<?php echo $index+1;?>" data-preview-container="#preview_container_file_<?php echo $index+1;?>" class="browse_button"><br>
                        <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
                        <input type="hidden" name="files[file_<?php echo $index+1;?>]" value="<?php  echo $file['file_name'];?>">
                        <input type="hidden" name="files[file_type_<?php echo $index+1;?>]" value="<?php  echo $file['file_type'];?>">
                    </td>
                    <td style="max-width: 100px;">
                        <label>Remarks :</label>
                        <textarea class="form-control remarks" id="remarks" name="remarks[<?php echo $index+1;?>]"><?php if(isset($file['file_remarks'])){echo $file['file_remarks'];} ?></textarea>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row show-grid">
    <div class="col-xs-4">

    </div>
    <div class="col-xs-4">
        <button type="button" class="btn btn-warning system_button_add_more" data-current-id="<?php echo sizeof($file_details);?>"><?php echo $CI->lang->line('LABEL_ADD_MORE');?></button>
    </div>
    <div class="col-xs-4">

    </div>
</div>

</div>

<div class="clearfix"></div>
</form>

<div id="system_content_add_more" style="display: none;">
    <table>
        <tbody>
        <tr>
            <td>
                <div class="preview_container_file">
                </div>
            </td>
            <td>
                <input type="file" class="browse_button_new"><br>
                <button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
                <input type="hidden" class="is_preview" name="" value="0">
            </td>
            <td>
                <textarea class="form-control remarks"></textarea>
            </td>
        </tr>
        </tbody>
    </table>
</div>



<script type="text/javascript">

    jQuery(document).ready(function()
    {
        turn_off_triggers();
        $(document).off("input",".expense_budget");
        $(document).off("input",".participant_budget");

        $('.expense_file').filestyle({
            input: false,
            icon: false,
            buttonText: "Edit File",
            buttonBefore: true,
            buttonName: "btn-primary"});

        $(document).on("click", ".system_button_exp_add_more", function(event)
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            var dynamic_id=parseInt($(this).attr('data-dynamic-id'));
            dynamic_id=dynamic_id+1;
            $(this).attr('data-dynamic-id',dynamic_id)
            var content_id='#expense_add_more_file_contents tbody';
            $(content_id+' .input_type_file').attr('name','expense_file_'+current_id+'_'+dynamic_id);
            $(content_id+' .demo').attr('name','demo['+current_id+']['+dynamic_id+']');
            $(content_id+' .input_type_file').attr('id','expense_file_'+current_id+'_'+dynamic_id);
            $(content_id+' .input_type_file').attr('data-id','popup_image_new_id_'+current_id+'_'+dynamic_id);
            $(content_id+' .popup_image_new').attr('id','popup_image_new_id_'+current_id+'_'+dynamic_id);
            var html=$(content_id).html();
            $('#collapse_'+current_id+' table tbody').append(html);
            $('#expense_file_'+current_id+'_'+dynamic_id).filestyle({
                input: false,
                icon: false,
                buttonBefore: true,
                buttonName: "btn-primary",
                buttonText: "Select File"
            });
            $(content_id+' .input_type_file').removeAttr('name');
            $(content_id+' .demo').removeAttr('name');
            $(content_id+' .input_type_file').removeAttr('id');
        });


        $(document).on("change", ".file_multi_video", function(evt) {

            $('.file_multi_video').attr('name','video');
            $('#video_preview_container_id').show();
            var $source = $('#video_here');
            $source[0].src = URL.createObjectURL(this.files[0]);
            $source.parent()[0].load();
            var video=document.createElement('video');
            video.src=URL.createObjectURL(this.files[0]);
            video.onloadedmetadata=function()
            {
                window.URL.revokeObjectURL(this.src);
                if(parseInt(video.duration)>119)
                {
                    $('.file_multi_video').removeAttr('name');
                    $('.hidden_file').removeAttr('name');
                    alert('Exceeded Video Length!! This Video is not applicable for save.');
                }
            }
        });


        $(".datepicker").datepicker({dateFormat : display_date_format});
        $(".browse_button").filestyle({input: false,icon: false,buttonText: "Upload",buttonName: "btn-primary"});
        $(document).on("click", ".system_button_add_more", function(event)
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            current_id=current_id+1;
            $(this).attr('data-current-id',current_id);
            var content_id='#system_content_add_more table tbody';

            $(content_id+' .browse_button_new').attr('data-preview-container','#preview_container_file_'+current_id);
            $(content_id+' .browse_button_new').attr('name','file_'+current_id);
            $(content_id+' .browse_button_new').attr('id','file_'+current_id);
            $(content_id+' .remarks').attr('name','remarks['+current_id+']');
            $(content_id+' .preview_container_file').attr('id','preview_container_file_'+current_id);
            $(content_id+' .is_preview').attr('name','files['+current_id+']');

            var html=$(content_id).html();
            $("#files_container tbody").append(html);

            $(content_id+' .browse_button_new').removeAttr('name');
            $(content_id+' .browse_button_new').removeAttr('data-preview-container');
            $(content_id+' .browse_button_new').removeAttr('id');
            $(content_id+' .preview_container_file').removeAttr('id');
            $('#file_'+current_id).filestyle({input: false,icon: false,buttonText: "Upload",buttonName: "btn-primary"});

        });
        $(document).on("click", ".system_button_add_delete", function(event)
        {
            $(this).closest('tr').remove();
        });

        $(document).on("input",".expense_budget",function()
        {
            //findTotal();
            var total=0;
            $(".expense_budget").each( function( index, element )
            {
                if($(this).val()==parseFloat($(this).val()))
                {
                    total=total+parseFloat($(this).val());
                }
            });
            if(total=>0)
            {
                $('#total_expense_container').show();
            }
            $('#total_expense').html(number_format(total,2));
        });

        $(document).on("input",".participant_budget",function()
        {
            //findTotal_participant();
            var total=0;
            $(".participant_budget").each( function( index, element )
            {
                if($(this).val()==parseFloat($(this).val()))
                {
                    total=total+parseInt($(this).val());
                }
            });
            if(total=>0)
            {
                $('#total_participant_container').show();
            }
            $('#no_of_participant').html(number_format(total));
        });

    });

    jQuery(document).ready(function()
    {
        $(document).on('click','.popup_image',function()
        {
            var popup_id=$(this).attr('data-popup-id');
            $('#myModal_'+popup_id).show();
            $('#modal-image_'+popup_id).attr('src',$('#popup_image_'+popup_id).attr('file-link'));
            $('#caption_'+popup_id).html($('#popup_image_'+popup_id).attr('title'));
        });
        $(document).on('click','.close',function(e)
        {
            var popup_id=$(this).attr('data-popup-id');
            $('#myModal_'+popup_id).hide();
        });
    });

    $(document).on("change",".expense_file",function()
    {
        var id=$(this).attr('data-id');
        var dynamic_id=$(this).attr('data-dynamic-id');
        $('#'+id).html(this.files[0].name);
        $('#'+id).removeAttr('class');
        $('#h_expense_files_'+dynamic_id).removeAttr('name');
    });
    $(document).on("change",".input_type_file",function()
    {
        var id=$(this).attr('data-id');
        $('#'+id).html(this.files[0].name);
        $('#'+id).removeAttr('class');
    });

</script>
