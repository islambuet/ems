<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();

    /*$shifts=Query_helper::get_info($this->config->item('table_setup_tm_shifts'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
    $territories=Query_helper::get_info($this->config->item('table_setup_location_territories'),array('id value','name text'),array('zone_id ='.$zone_id));

    $CI->db->from($this->config->item('table_setup_location_districts').' d');
    $CI->db->select('d.id value,d.name text,d.territory_id');
    $CI->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
    $CI->db->where('t.zone_id',$zone_id);
    $results=$CI->db->get()->result_array();
    $districts=array();
    foreach($results as $result)
    {
        $districts[$result['territory_id']][]=$result;
    }
    $CI->db->from($this->config->item('table_setup_tm_market_visit_zi').' stmv');
    $CI->db->select('stmv.*');
    $CI->db->select('cus.district_id');
    $CI->db->select('d.territory_id');
    $CI->db->join($this->config->item('table_csetup_customers').' cus','cus.id = stmv.customer_id','INNER');
    $CI->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
    $CI->db->where('stmv.revision',1);
    $CI->db->where('stmv.zone_id',$zone_id);
    $results=$CI->db->get()->result_array();
    $old_customers=array();
    foreach($results as $result)
    {
        $old_customers[$result['day_no']][$result['shift_id']]['district_id']=$result['district_id'];
        $old_customers[$result['day_no']][$result['shift_id']]['territory_id']=$result['territory_id'];
        $old_customers[$result['day_no']][$result['shift_id']]['customers'][]=$result['customer_id'];
    }
    $customers=array();
    if($old_customers)
    {
        $CI->db->from($this->config->item('table_csetup_customers').' cus');
        $CI->db->select('cus.district_id');
        $CI->db->select('cus.id value,CONCAT(cus.customer_code," - ",cus.name) text');
        $this->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
        $this->db->join($this->config->item('table_setup_location_territories').' t','t.id = d.territory_id','INNER');
        $CI->db->where('t.zone_id',$zone_id);
        $results=$CI->db->get()->result_array();
        foreach($results as $result)
        {
            $customers[$result['district_id']][]=$result;

        }
    }*/

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
                                        /*if(isset($old_customers[$day%7][$shift['value']]))
                                        {
                                            $territory_id=$old_customers[$day%7][$shift['value']]['territory_id'];
                                        }*/
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
                                        if($territory_id>0)
                                        {
                                            ?>
                                            <select class="form-control district_id" data-day="<?php echo $day; ?>" data-shift-id="<?php echo $shift['value']; ?>" data-territory-id="<?php echo $territory_id; ?>">
                                                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                                <?php
                                                $district_id='';
                                                /*if(isset($old_customers[$day%7][$shift['value']]))
                                                {
                                                    $district_id=$old_customers[$day%7][$shift['value']]['district_id'];
                                                }*/
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
                                        if(isset($old_customers[$day%7][$shift['value']]))
                                        {
                                            foreach($customers[$old_customers[$day%7][$shift['value']]['district_id']] as $item)
                                            {

                                                ?>
                                                <div class="checkbox">
                                                    <label><input type="checkbox" name="data[<?php echo ($day); ?>][<?php echo $shift['value']; ?>][customer][<?php echo $item['value']; ?>]" value="<?php echo $item['value']; ?>" <?php if(in_array($item['value'],$old_customers[$day%7][$shift['value']]['customers'])){ echo 'checked';} ?>><?php echo $item['text']; ?></label>
                                                </div>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <input type="text" class="form-control integer_type_positive" name="data[<?php echo ($day); ?>][<?php echo $shift['value']; ?>][special]" value="">
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
