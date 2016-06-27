<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();

?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" name="zone_id" value="<?php echo $zone_id; ?>" />
    <input type="hidden" name="year" value="<?php echo $year; ?>" />
    <input type="hidden" name="month" value="<?php echo $month; ?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="col-xs-12" style="overflow-x: auto;">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th style="width: 200px;">Day</th>
                        <th style="width: 200px;">Shift</th>
                        <th style="width: 200px;">Territory</th>
                        <th style="width: 200px;">District</th>
                        <th>Customers</th>
                        <th>Num Special</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    for($day=1;$day<=date("t",mktime(0, 0, 0,  $month,1, $year));$day++)
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
                                            <label class="label label-primary"><?php echo $day.'-'.date('l',mktime(0, 0, 0,  $month,$day, $year)); ?></label>
                                            <?php
                                        }
                                    ?>
                                </td>
                                <td>
                                    <label class="label <?php if($shift_index%2){echo 'label-warning';}else{echo 'label-info';}?>"><?php echo $shift['text']; ?></label>
                                </td>
                                <td>
                                    <select class="form-control territory_id" data-day="<?php echo $day; ?>" data-shift-id="<?php echo $shift['value']; ?>">
                                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                        <?php
                                        $territory_id='';
                                        if(isset($previous_setup[$day][$shift['value']][$this->config->item('system_host_type_customer')]))
                                        {
                                            $first=reset($previous_setup[$day][$shift['value']][$this->config->item('system_host_type_customer')]);
                                            $territory_id=$first['territory_id'];
                                        }
                                        foreach($zone_details as $territory)
                                        {?>
                                            <option value="<?php echo $territory['territory_id']?>" <?php if($territory['territory_id']==$territory_id){echo 'selected';} ?>><?php echo $territory['territory_name'];?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <div id="district_container_<?php echo ($day); ?>_<?php echo $shift['value']; ?>">
                                        <?php
                                        $district_id='';
                                        if($territory_id>0)
                                        {
                                            ?>
                                            <select class="form-control district_id" data-day="<?php echo $day; ?>" data-shift-id="<?php echo $shift['value']; ?>" data-territory-id="<?php echo $territory_id; ?>">
                                                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                                <?php
                                                if(isset($previous_setup[$day][$shift['value']][$this->config->item('system_host_type_customer')]))
                                                {
                                                    $first=reset($previous_setup[$day][$shift['value']][$this->config->item('system_host_type_customer')]);
                                                    $district_id=$first['district_id'];
                                                }
                                                foreach($zone_details[$territory_id]['districts'] as $district)
                                                {?>
                                                    <option value="<?php echo $district['district_id']?>" <?php if($district['district_id']==$district_id){echo 'selected';} ?>><?php echo $district['district_name'];?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <div id="customers_container_<?php echo $day; ?>_<?php echo $shift['value']; ?>">
                                        <?php
                                        if($district_id>0)
                                        {
                                            foreach($zone_details[$territory_id]['districts'][$district_id]['customers'] as $customer)
                                            {

                                                ?>
                                                <div class="checkbox">
                                                    <label><input type="checkbox" name="data[<?php echo ($day); ?>][<?php echo $shift['value']; ?>][customer][<?php echo $customer['customer_id']; ?>]" value="<?php echo $customer['customer_id']; ?>" <?php if(isset($previous_setup[$day][$shift['value']][$this->config->item('system_host_type_customer')][$customer['customer_id']])){ echo 'checked';} ?>><?php echo $customer['customer_name']; ?></label>
                                                </div>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control integer_type_positive" name="data[<?php echo ($day); ?>][<?php echo $shift['value']; ?>][special]" value="<?php if(isset($previous_setup[$day][$shift['value']][$this->config->item('system_host_type_special')])){echo sizeof($previous_setup[$day][$shift['value']][$this->config->item('system_host_type_special')]);}elseif($setup_id==0){echo '2';} ?>">
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
<script type="text/javascript">
    var zone_info=<?php echo json_encode($zone_details); ?>;
</script>
