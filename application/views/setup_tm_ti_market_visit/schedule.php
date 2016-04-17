<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $shifts=Query_helper::get_info($this->config->item('table_setup_tm_shifts'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
    $filter=array();
    $filter[]=array('territory_id'=>$territory_id);
    if($CI->locations['district_id']>0);
    {
        //$filter[]=array('id'=>$CI->locations['district_id']);
    }
    $districts=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),$filter);

?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" name="territory_id" value="<?php echo $territory_id; ?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                Schedule
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="col-xs-12" style="overflow-x: auto;">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th style="width: 200px;">Day</th>
                        <th style="width: 200px;">Shift</th>
                        <th style="width: 200px;">District</th>
                        <th>Customers</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    for($day=6;$day<13;$day++)
                    {
                        foreach($shifts as $shift_index=>$shift)
                        {
                            ?>
                            <tr>
                                <td>
                                    <?php
                                        if($shift_index==0)
                                        {
                                            ?>
                                            <label class="label label-primary"><?php echo date('l',259200+($day%7)*86400); ?></label>
                                            <?php
                                        }
                                    ?>
                                </td>
                                <td>
                                    <label class="label <?php if($shift_index%2){echo 'label-warning';}else{echo 'label-info';}?>"><?php echo $shift['text']; ?></label>
                                </td>
                                <td>
                                    <select class="form-control district_id" data-day="<?php echo ($day%7); ?>" data-shift-id="<?php echo $shift['value']; ?>" data-customer-container="#customers_container_<?php echo ($day%7); ?>_<?php echo $shift['value']; ?>">
                                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                        <?php
                                        foreach($districts as $district)
                                        {?>
                                            <option value="<?php echo $district['value']?>"><?php echo $district['text'];?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <div id="customers_container_<?php echo ($day%7); ?>_<?php echo $shift['value']; ?>">

                                    </div>
                                </td>
                            </tr>
                            <?php
                        }

                    }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
