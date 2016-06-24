<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    if($purpose=='approve')
    {
        $action_data["action_edit_get"]=base_url($CI->controller_url."/index/edit/".$setup_info['id']);
        if($setup_info['status_approve']==$CI->config->item('system_status_pending'))
        {
            $action_data["action_save"]='#save_form';
        }
    }
    else
    {
        if((isset($this->permissions['edit'])&&($this->permissions['edit']==1)))
        {
            $action_data["action_edit_get"]=base_url($CI->controller_url."/index/edit/".$setup_info['id']);
            if($setup_info['status_approve']==$CI->config->item('system_status_pending'))
            {
                $action_data["action_approve_get"]=base_url($CI->controller_url."/index/approve/".$setup_info['id']);

            }
        }
    }
    $action_data["action_back"]=base_url($CI->controller_url);
    $CI->load->view("action_buttons",$action_data);

?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            ZI Market Visit Setup
        </div>
        <div class="clearfix"></div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_YEAR');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo $setup_info['year'];?></label>
        </div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_MONTH');?></label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <label class="control-label"><?php echo date("F", mktime(0, 0, 0,  $setup_info['month'],1, 2000));?></label>
        </div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Approval Status</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php
            if($purpose=='approve')
            {
                if($setup_info['status_approve']==$CI->config->item('system_status_pending'))
                {
                    ?>
                    <form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_approve');?>" method="post">
                        <input type="hidden" name="setup_id" value="<?php echo $setup_info['id']; ?>" />
                        <select id="status_approved" name="status_approve" class="form-control">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <option value="<?php echo $CI->config->item('system_status_po_approval_approved');?>"><?php echo $CI->config->item('system_status_po_approval_approved');?></option>
                        </select>
                    </form>
                <?php
                }
            }
            else
            {
                echo $setup_info['status_approve'];
            }
            ?>

        </div>
    </div>

</div>
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                Schedules
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
                                    <?php
                                    $territory_id='';
                                    $territory_name='';
                                    if(isset($previous_setup[$day][$shift['value']][$this->config->item('system_host_type_customer')]))
                                    {
                                        $first=reset($previous_setup[$day][$shift['value']][$this->config->item('system_host_type_customer')]);
                                        $territory_id=$first['territory_id'];
                                    }
                                    foreach($zone_details as $territory)
                                    {
                                        if($territory['territory_id']==$territory_id)
                                        {
                                            $territory_name=$territory['territory_name'];
                                        }
                                    }
                                    echo $territory_name;
                                    ?>
                                </td>
                                <td>
                                    <div id="district_container_<?php echo ($day); ?>_<?php echo $shift['value']; ?>">
                                        <?php
                                        $district_id='';
                                        $district_name='';
                                        if($territory_id>0)
                                        {
                                            if(isset($previous_setup[$day][$shift['value']][$this->config->item('system_host_type_customer')]))
                                            {
                                                $first=reset($previous_setup[$day][$shift['value']][$this->config->item('system_host_type_customer')]);
                                                $district_id=$first['district_id'];
                                            }
                                            foreach($zone_details[$territory_id]['districts'] as $district)
                                            {
                                                if($district['district_id']==$district_id)
                                                {
                                                    $district_name=$district['district_name'];
                                                }
                                            }
                                            echo $district_name;
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
                                                if(isset($previous_setup[$day][$shift['value']][$this->config->item('system_host_type_customer')][$customer['customer_id']]))
                                                {
                                                    ?>
                                                    <div class="checkbox">
                                                        <?php echo $customer['customer_name']; ?>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if(isset($previous_setup[$day][$shift['value']][$this->config->item('system_host_type_special')])){echo sizeof($previous_setup[$day][$shift['value']][$this->config->item('system_host_type_special')]);} ?>
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

