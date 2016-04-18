<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $shifts=Query_helper::get_info($this->config->item('table_setup_tm_shifts'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
    $districts=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$territory_id));

    $CI->db->from($this->config->item('table_setup_tm_market_visit').' stmv');
    $CI->db->select('stmv.*');
    $CI->db->select('cus.district_id');
    $CI->db->join($this->config->item('table_csetup_customers').' cus','cus.id = stmv.customer_id','INNER');
    $CI->db->where('revision',1);
    $CI->db->where('territory_id',$territory_id);
    $results=$CI->db->get()->result_array();
    $old_customers=array();
    foreach($results as $result)
    {
        $old_customers[$result['day_no']][$result['shift_id']]['district_id']=$result['district_id'];
        $old_customers[$result['day_no']][$result['shift_id']]['customers'][]=$result['customer_id'];
    }
    $customers=array();
    if($old_customers)
    {
        $CI->db->from($this->config->item('table_csetup_customers').' cus');
        $CI->db->select('cus.district_id');
        $CI->db->select('cus.id value,CONCAT(cus.customer_code," - ",cus.name) text');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $CI->db->where('d.territory_id',$territory_id);
        $results=$CI->db->get()->result_array();
        foreach($results as $result)
        {
            $customers[$result['district_id']][]=$result;

        }
    }

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
                                        $district_id='';
                                        if(isset($old_customers[$day%7][$shift['value']]))
                                        {
                                            $district_id=$old_customers[$day%7][$shift['value']]['district_id'];
                                        }
                                        foreach($districts as $district)
                                        {?>
                                            <option value="<?php echo $district['value']?>" <?php if($district['value']==$district_id){echo 'selected';} ?>><?php echo $district['text'];?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <div id="customers_container_<?php echo ($day%7); ?>_<?php echo $shift['value']; ?>">
                                        <?php
                                        if(isset($old_customers[$day%7][$shift['value']]))
                                        {
                                            foreach($customers[$old_customers[$day%7][$shift['value']]['district_id']] as $item)
                                            {

                                                ?>
                                                <div class="checkbox">
                                                    <label><input type="checkbox" name="customers[<?php echo $day; ?>][<?php echo $shift['value']; ?>][<?php echo $item['value']; ?>]" value="<?php echo $item['value']; ?>" <?php if(in_array($item['value'],$old_customers[$day%7][$shift['value']]['customers'])){ echo 'checked';} ?>><?php echo $item['text']; ?></label>
                                                </div>
                                            <?php
                                            }
                                        }
                                        ?>
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
