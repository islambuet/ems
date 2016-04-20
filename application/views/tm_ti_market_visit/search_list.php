<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
?>


    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div style="overflow-x: auto;" class="row show-grid" id="order_items_container">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_SHIFT'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CUSTOMER_NAME'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_ACTION'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(sizeof($schedules)>0)
                {
                    foreach($schedules as $schedule)
                    {
                        ?>
                        <tr>
                            <td><?php echo $schedule['shift_name']; ?></td>
                            <td><?php echo $schedule['district_name']; ?></td>
                            <td><?php echo $schedule['customer_name']; ?></td>
                            <td>
                                <?php
                                if(isset($visit_done[$schedule['shift_id']])&& in_array($schedule['customer_id'],$visit_done[$schedule['shift_id']]))
                                {
                                    ?>
                                    <label>Visit Done</label>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <form class="form_valid" id="search_form" action="<?php echo site_url($CI->controller_url.'/index/add');?>" method="post">
                                        <input type="hidden" name="date" value="<?php echo $date; ?>">
                                        <input type="hidden" name="setup_id" value="<?php echo $schedule['id']; ?>">
                                        <button type="submit" class="btn btn-primary">Visit</button>
                                    </form>
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }

                }
                else
                {
                    ?>
                    <tr>
                        <td colspan="20" class="text-center alert-danger">
                            No Schedule For this day
                        </td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="clearfix"></div>

