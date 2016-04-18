<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $CI->load->view("action_buttons",$action_data);
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
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $setup['division_name'];?></label>
            </div>
        </div>

        <div class="row show-grid" id="zone_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $setup['zone_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="territory_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $setup['territory_name'];?></label>

            </div>
        </div>
    </div>

    <div class="clearfix"></div>
    <div id="system_report_container">
    <?php
    $territory_id=$setup['territory_id'];
    $shifts=Query_helper::get_info($this->config->item('table_setup_tm_shifts'),array('id value','name text'),array('status ="'.$this->config->item('system_status_active').'"'));
    $districts=Query_helper::get_info($this->config->item('table_setup_location_districts'),array('id value','name text'),array('territory_id ='.$territory_id));

    $CI->db->from($this->config->item('table_setup_tm_market_visit').' stmv');
    $CI->db->select('stmv.*');
    $CI->db->select('cus.district_id');
    $CI->db->select('cus.id value,CONCAT(cus.customer_code," - ",cus.name) text');
    $this->db->select('d.name district_name');
    $CI->db->join($this->config->item('table_csetup_customers').' cus','cus.id = stmv.customer_id','INNER');
    $CI->db->join($this->config->item('table_setup_location_districts').' d','d.id = cus.district_id','INNER');
    $CI->db->where('stmv.revision',1);
    $CI->db->where('stmv.territory_id',$territory_id);
    $results=$CI->db->get()->result_array();
    $old_customers=array();
    foreach($results as $result)
    {
        $old_customers[$result['day_no']][$result['shift_id']]['district_name']=$result['district_name'];
        $old_customers[$result['day_no']][$result['shift_id']]['customers'][]=$result['text'];
    }
    ?>
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
                                    <?php
                                        if(isset($old_customers[$day%7][$shift['value']]))
                                        {
                                            ?>
                                            <label class="control-label"><?php echo $old_customers[$day%7][$shift['value']]['district_name'];?></label>
                                            <?php
                                        }

                                    ?>

                                </td>
                                <td>
                                    <?php
                                    if(isset($old_customers[$day%7][$shift['value']]))
                                    {
                                        foreach($old_customers[$day%7][$shift['value']]['customers'] as $item)
                                        {
                                            ?>
                                            <div><label class="control-label"><?php echo $item;?></label></div>
                                            <?php
                                        }
                                    }
                                    ?>

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
    </div>
<script type="text/javascript">
jQuery(document).ready(function()
{
    turn_off_triggers();
});
</script>
