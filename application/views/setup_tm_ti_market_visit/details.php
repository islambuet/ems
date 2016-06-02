<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $CI->load->view("action_buttons",$action_data);

    $shifts=Query_helper::get_info($this->config->item('table_setup_tm_shifts'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
    $districts=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$setup['territory_id'],'status !="'.$this->config->item('system_status_delete').'"'));

    $customers=array();
    $CI->db->from($this->config->item('table_csetup_customers').' cus');
    $CI->db->select('cus.district_id');
    $CI->db->select('cus.id value,CONCAT(cus.customer_code," - ",cus.name) text,cus.status');
    $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
    $CI->db->where('d.territory_id',$setup['territory_id']);
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
    $CI->db->where('d.territory_id',$setup['territory_id']);
    $results=$CI->db->get()->result_array();
    foreach($results as $result)
    {
        $other_customers[$result['district_id']][]=$result;

    }

    $CI->db->from($this->config->item('table_setup_tm_market_visit').' stmv');
    $CI->db->select('stmv.*');
    $CI->db->where('revision',1);
    $CI->db->where('territory_id',$setup['territory_id']);
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
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $setup['division_name'];?></label>
        </div>
    </div>

    <div class="row show-grid" id="zone_id_container">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $setup['zone_name'];?></label>
        </div>
    </div>
    <div class="row show-grid" id="territory_id_container">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $setup['territory_name'];?></label>

        </div>
    </div>
</div>

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
                                    <?php

                                    if(isset($prev_setup[$day%7][$shift['value']]['district_id']))
                                    {
                                        $district_id=$prev_setup[$day%7][$shift['value']]['district_id'];;
                                    }
                                    foreach($districts as $district)
                                    {
                                        if($district['value']==$district_id){ echo $district['text'];}
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div id="customers_container_<?php echo ($day%7); ?>_<?php echo $shift['value']; ?>">
                                        <?php
                                        if($district_id>0 && isset($customers[$district_id]))
                                        {
                                            foreach($customers[$district_id] as $item)
                                            {
                                                if(isset($prev_setup[$day%7][$shift['value']]['customers']))
                                                {
                                                    if(in_array($item['value'],$prev_setup[$day%7][$shift['value']]['customers']))
                                                    {
                                                        ?>
                                                        <div class="checkbox">
                                                            <label><?php echo $item['text'];if($item['status']!=$CI->config->item('system_status_active')){echo '('.$item['status'].')';} ?></label>
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                                ?>

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

                                                if(isset($prev_setup[$day%7][$shift['value']]['other_customers']))
                                                {
                                                    if(in_array($item['value'],$prev_setup[$day%7][$shift['value']]['other_customers']))
                                                    {
                                                        ?>
                                                        <div class="checkbox">
                                                            <label><?php echo $item['text'];if($item['status']!=$CI->config->item('system_status_active')){echo '('.$item['status'].')';} ?></label>
                                                        </div>
                                                    <?php
                                                    }
                                                }
                                                ?>
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
                                            if(isset($prev_setup[$day%7][$shift['value']]['special']))
                                            {
                                                echo $prev_setup[$day%7][$shift['value']]['special'];;
                                            }
                                            ?>
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

<script type="text/javascript">
    jQuery(document).ready(function()
    {
        turn_off_triggers();
    });
</script>