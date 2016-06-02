<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $shifts=Query_helper::get_info($this->config->item('table_setup_tm_shifts'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
    $districts=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$territory_id,'status !="'.$this->config->item('system_status_delete').'"'));

    $customers=array();
    $CI->db->from($this->config->item('table_csetup_customers').' cus');
    $CI->db->select('cus.district_id');
    $CI->db->select('cus.id value,CONCAT(cus.customer_code," - ",cus.name) text,cus.status');
    $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
    $CI->db->where('d.territory_id',$territory_id);
    $results=$CI->db->get()->result_array();
    foreach($results as $result)
    {
        $customers[$result['district_id']][]=$result;

    }
    $other_customers=array();
    $CI->db->from($this->config->item('table_csetup_other_customers').' cus');
    $CI->db->select('cus.district_id');
    $CI->db->select('cus.id value,cus.name text,cus.status');
    $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
    $CI->db->where('d.territory_id',$territory_id);
    $results=$CI->db->get()->result_array();
    foreach($results as $result)
    {
        $other_customers[$result['district_id']][]=$result;

    }

    $CI->db->from($this->config->item('table_setup_tm_market_visit').' stmv');
    $CI->db->select('stmv.*');
    $CI->db->where('revision',1);
    $CI->db->where('territory_id',$territory_id);
    $results=$CI->db->get()->result_array();
    $prev_setup=array();
    foreach($results as $result)
    {
        $prev_setup[$result['day_no']][$result['shift_id']]['district_id']=$result['district_id'];
        if($result['host_type']==$CI->config->item('system_host_type_customer'))
        {
            $prev_setup[$result['day_no']][$result['shift_id']]['customers'][]=$result['host_id'];
        }
        elseif($result['host_type']==$CI->config->item('system_host_type_other_customer'))
        {
            $prev_setup[$result['day_no']][$result['shift_id']]['other_customers'][]=$result['host_id'];
        }
        elseif($result['host_type']==$CI->config->item('system_host_type_special'))
        {
            if(isset($prev_setup[$result['day_no']][$result['shift_id']]['special']))
            {
                $prev_setup[$result['day_no']][$result['shift_id']]['special']+=1;
            }
            else
            {
                $prev_setup[$result['day_no']][$result['shift_id']]['special']=1;
            }
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
                        <th>Other Customers</th>
                        <th>Num Special</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    for($day=6;$day<13;$day++)
                    {
                        foreach($shifts as $shift_index=>$shift)
                        {
                            $district_id='';
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
                                    <select class="form-control district_id" data-day="<?php echo ($day%7); ?>" data-shift-id="<?php echo $shift['value']; ?>" name="data[<?php echo ($day%7); ?>][<?php echo $shift['value']; ?>][district_id]">
                                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                        <?php

                                        if(isset($prev_setup[$day%7][$shift['value']]['district_id']))
                                        {
                                            $district_id=$prev_setup[$day%7][$shift['value']]['district_id'];;
                                        }
                                        foreach($districts as $district)
                                        {?>
                                            <option value="<?php echo $district['value']?>" <?php if($district['value']==$district_id){ echo "selected";}?>><?php echo $district['text'];?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td>
                                    <div id="customers_container_<?php echo ($day%7); ?>_<?php echo $shift['value']; ?>">
                                        <?php
                                        if($district_id>0 && isset($customers[$district_id]))
                                        {
                                            foreach($customers[$district_id] as $item)
                                            {
                                                $checked=false;
                                                if(isset($prev_setup[$day%7][$shift['value']]['customers']))
                                                {
                                                    if(in_array($item['value'],$prev_setup[$day%7][$shift['value']]['customers']))
                                                    {
                                                        $checked=true;
                                                    }
                                                }
                                                ?>
                                                <div class="checkbox">
                                                    <label><input type="checkbox" <?php if($checked){ echo 'checked';} ?> name="data[<?php echo $day%7; ?>][<?php echo $shift['value']; ?>][customer][<?php echo $item['value']; ?>]" value="<?php echo $item['value']; ?>"><?php echo $item['text'];if($item['status']!=$CI->config->item('system_status_active')){echo '('.$item['status'].')';} ?></label>
                                                </div>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <div id="other_customers_container_<?php echo ($day%7); ?>_<?php echo $shift['value']; ?>">
                                        <?php
                                        if($district_id>0 && isset($other_customers[$district_id]))
                                        {
                                            foreach($other_customers[$district_id] as $item)
                                            {
                                                $checked=false;
                                                if(isset($prev_setup[$day%7][$shift['value']]['other_customers']))
                                                {
                                                    if(in_array($item['value'],$prev_setup[$day%7][$shift['value']]['other_customers']))
                                                    {
                                                        $checked=true;
                                                    }
                                                }
                                                ?>
                                                <div class="checkbox">
                                                    <label><input type="checkbox" <?php if($checked){ echo 'checked';} ?> name="data[<?php echo $day%7; ?>][<?php echo $shift['value']; ?>][customer][<?php echo $item['value']; ?>]" value="<?php echo $item['value']; ?>"><?php echo $item['text'];if($item['status']!=$CI->config->item('system_status_active')){echo '('.$item['status'].')';} ?></label>
                                                </div>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <div id="special_container_<?php echo ($day%7); ?>_<?php echo $shift['value']; ?>">
                                        <?php
                                        if($district_id>0)
                                        {
                                            $value='';
                                            if(isset($prev_setup[$day%7][$shift['value']]['special']))
                                            {
                                                $value=$prev_setup[$day%7][$shift['value']]['special'];;
                                            }
                                            ?>
                                            <input name="data[<?php echo ($day%7); ?>][<?php echo $shift['value']; ?>][special]" type="text" class="form-control integer_type_positive" value="<?php echo $value; ?>">
                                            <?php
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
