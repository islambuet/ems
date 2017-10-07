<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_data=array();
$action_data["action_back"]=base_url($CI->controller_url);
$action_data["action_save"]='#save_form';
$action_data["action_clear"]='#save_form';
$CI->load->view("action_buttons",$action_data);
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-3">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE');?></label>
            </div>
            <div class="col-sm-4 col-xs-9">
                <?php echo System_helper::display_date($item['date_started']);?>
            </div>
        </div>

        <div id="tasks_container">
            <div style="overflow-x: auto;" class="row show-grid">
                <div class="col-xs-3"></div>
                <div class="col-xs-6">
                    <table class="table table-bordered">
                        <tbody>
                            <?php
                                foreach($item_details as $details)
                                {
                                    ?>
                                    <tr>
                                        <td>
                                            <textarea rows="1" class="form-control remarks_started" name="remarks_started_old[<?php echo $details['id']; ?>]"><?php echo $details['remarks_started']; ?></textarea>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-xs-3"></div>
            </div>
        </div>

        <div style="" class="row show-grid">
            <div class="col-xs-3"></div>
            <div class="col-xs-4">
                <button type="button" class="btn btn-warning system_button_add_more" data-current-id="0"><?php echo $CI->lang->line('LABEL_ADD_MORE');?></button>
            </div>
            <div class="col-xs-5"></div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>


<div id="system_content_add_more" style="display: none;">
    <div style="" class="row show-grid">
        <div class="col-xs-3"></div>
        <div class="col-xs-6">
            <table>
                <tbody>
                    <tr>
                        <td>
                            <textarea rows="1" class="form-control remarks_started"></textarea>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-xs-3"></div>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        turn_off_triggers();

        $(document).off("click", ".system_button_add_more");
        $(document).off("click", ".system_button_add_delete");

        $(document).on("click", ".system_button_add_more", function(event)
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            current_id=current_id+1;
            $(this).attr('data-current-id',current_id);
            var content_id='#system_content_add_more table tbody';
            $(content_id+' .remarks_started').attr('name','remarks_started_new['+current_id+']');
            var html=$(content_id).html();
            $("#tasks_container tbody").append(html);
        });
        $(document).on("click", ".system_button_add_delete", function(event)
        {
            $(this).closest('tr').remove();
        });
    });
</script>
