<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_data=array();
$action_data["action_back"]=base_url($CI->controller_url);
$action_data["action_save"]='#save_form';
$action_data["action_clear"]='#save_form';
$CI->load->view("action_buttons",$action_data);
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_reporting');?>" method="post">
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

        <div id="files_container">
            <div style="overflow-x: auto;" class="row show-grid">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="min-width: 250px;">Task List</th>
                            <th style="min-width: 250px;">Work Done</th>
                            <th style="max-width: 150px;">Work Done Document</th>
                            <th style="min-width: 50px;">Upload</th>
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
                                     <textarea class="form-control" name="items[<?php echo $details['id']; ?>]"><?php echo $details['remarks_reported']; ?></textarea>
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
                                    <input type="file" name="file_<?php echo $details['id']; ?>" data-preview-container="#preview_container_file_<?php echo $details['id']; ?>" class="browse_button"><br>
<!--                                    <button type="button" class="btn btn-danger system_button_add_delete">--><?php //echo $CI->lang->line('DELETE'); ?><!--</button>-->
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        $('.browse_button').filestyle({input: false,icon: false,buttonText: "Upload",buttonName: "btn-primary"});
        $(document).off('.system_button_add_delete');
    });
</script>
