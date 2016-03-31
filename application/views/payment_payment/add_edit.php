<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_save"]='#save_form';
    $action_data["action_save_new"]='#save_form';
    $action_data["action_clear"]='#save_form';
    $CI->load->view("action_buttons",$action_data);
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $payment['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_PAYMENT');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="payment[date_payment]" id="date_payment" class="form-control datepicker" value="<?php echo System_helper::display_date($payment['date_payment']);?>"/>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                    if($CI->locations['division_id']>0)
                    {
                        ?>
                        <label class="control-label"><?php echo $CI->locations['division_name'];?></label>
                        <?php
                    }
                    else
                    {
                        ?>
                        <select id="division_id" class="form-control">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                            <?php
                            foreach($divisions as $division)
                            {?>
                                <option value="<?php echo $division['value']?>" <?php if($division['value']==$payment['division_id']){ echo "selected";}?>><?php echo $division['text'];?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <?php
                    }
                    ?>
            </div>
        </div>

        <div style="<?php if(!(sizeof($zones)>0)){echo 'display:none';} ?>" class="row show-grid" id="zone_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['zone_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['zone_name'];?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="zone_id" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        foreach($zones as $zone)
                        {?>
                            <option value="<?php echo $zone['value']?>" <?php if($zone['value']==$payment['zone_id']){ echo "selected";}?>><?php echo $zone['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>
            </div>
        </div>
        <div style="<?php if(!(sizeof($territories)>0)){echo 'display:none';} ?>" class="row show-grid" id="territory_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['territory_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['territory_name'];?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="territory_id" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        foreach($territories as $territory)
                        {?>
                            <option value="<?php echo $territory['value']?>" <?php if($territory['value']==$payment['territory_id']){ echo "selected";}?>><?php echo $territory['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>

            </div>
        </div>
        <div style="<?php if(!(sizeof($districts)>0)){echo 'display:none';} ?>" class="row show-grid" id="district_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($CI->locations['district_id']>0)
                {
                    ?>
                    <label class="control-label"><?php echo $CI->locations['district_name'];?></label>
                <?php
                }
                else
                {
                    ?>
                    <select id="district_id" class="form-control">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        foreach($districts as $district)
                        {?>
                            <option value="<?php echo $district['value']?>" <?php if($district['value']==$payment['district_id']){ echo "selected";}?>><?php echo $district['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>

            </div>
        </div>
        <div style="<?php if(!(sizeof($customers)>0)){echo 'display:none';} ?>" class="row show-grid" id="customer_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CUSTOMER_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="customer_id" name="payment[customer_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($customers as $customer)
                    {?>
                        <option value="<?php echo $customer['value']?>" <?php if($customer['value']==$payment['customer_id']){ echo "selected";}?>><?php echo $customer['text'];?></option>
                    <?php
                    }
                    ?>
                </select>

            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PAYMENT_WAY');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="payment_way" name="payment[payment_way]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <option value="<?php echo $CI->config->item('system_payment_way_cash'); ?>"
                        <?php
                        if ($payment['payment_way'] == $CI->config->item('system_payment_way_cash')) {
                            echo "selected='selected'";
                        }
                        ?> ><?php echo $CI->config->item('system_payment_way_cash'); ?>
                    </option>
                    <option value="<?php echo $CI->config->item('system_payment_way_pay_order'); ?>"
                        <?php
                        if ($payment['payment_way'] == $CI->config->item('system_payment_way_pay_order')) {
                            echo "selected='selected'";
                        }
                        ?> ><?php echo $CI->config->item('system_payment_way_pay_order'); ?>
                    </option>
                    <option value="<?php echo $CI->config->item('system_payment_way_cheque'); ?>"
                        <?php
                        if ($payment['payment_way'] == $CI->config->item('system_payment_way_cheque')) {
                            echo "selected='selected'";
                        }
                        ?> ><?php echo $CI->config->item('system_payment_way_cheque'); ?>
                    </option>
                    <option value="<?php echo $CI->config->item('system_payment_way_tt'); ?>"
                        <?php
                        if ($payment['payment_way'] == $CI->config->item('system_payment_way_tt')) {
                            echo "selected='selected'";
                        }
                        ?> ><?php echo $CI->config->item('system_payment_way_tt'); ?>
                    </option>
                    <option value="<?php echo $CI->config->item('system_payment_way_dd'); ?>"
                        <?php
                        if ($payment['payment_way'] == $CI->config->item('system_payment_way_dd')) {
                            echo "selected='selected'";
                        }
                        ?> ><?php echo $CI->config->item('system_payment_way_dd'); ?>
                    </option>
                    <option value="<?php echo $CI->config->item('system_payment_way_online_payment'); ?>"
                        <?php
                        if ($payment['payment_way'] == $CI->config->item('system_payment_way_online_payment')) {
                            echo "selected='selected'";
                        }
                        ?> ><?php echo $CI->config->item('system_payment_way_online_payment'); ?>
                    </option>

                </select>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_AMOUNT');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="payment[amount]" id="amount" class="form-control float_type_positive" value="<?php echo $payment['amount'];?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CHEQUE_NO');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="payment[cheque_no]" id="cheque_no" class="form-control" value="<?php echo $payment['cheque_no'];?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_BANK_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="bank_id" name="payment[bank_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($banks as $bank)
                    {?>
                        <option value="<?php echo $bank['value']?>" <?php if($bank['value']==$payment['bank_id']){ echo "selected";}?>><?php echo $bank['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_BANK_BRANCH_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="payment[bank_branch]" id="bank_branch" class="form-control" value="<?php echo $payment['bank_branch'];?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ARM_BANK_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="arm_bank_id" name="payment[arm_bank_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($arm_banks as $arm_bank)
                    {?>
                        <option value="<?php echo $arm_bank['value']?>" <?php if($arm_bank['value']==$payment['arm_bank_id']){ echo "selected";}?>><?php echo $arm_bank['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="<?php if(!(sizeof($arm_bank_accounts)>0)){echo 'display:none';} ?>" class="row show-grid" id="arm_bank_account_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_ACCOUNT_NO');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="arm_bank_account_id" name="payment[arm_bank_account_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($arm_bank_accounts as $arm_bank_account)
                    {?>
                        <option value="<?php echo $arm_bank_account['value']?>" <?php if($arm_bank_account['value']==$payment['arm_bank_account_id']){ echo "selected";}?>><?php echo $arm_bank_account['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        turn_off_triggers();
        $(".datepicker").datepicker({dateFormat : display_date_format});
        $(document).on("change","#division_id",function()
        {
            $("#zone_id").val("");
            $("#territory_id").val("");
            $("#district_id").val("");
            $("#customer_id").val("");
            var division_id=$('#division_id').val();
            if(division_id>0)
            {
                $('#zone_id_container').show();
                $('#territory_id_container').hide();
                $('#district_id_container').hide();
                $('#customer_id_container').hide();
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_zones_by_divisionid/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{division_id:division_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#zone_id_container').hide();
                $('#territory_id_container').hide();
                $('#district_id_container').hide();
                $('#customer_id_container').hide();
            }
        });
        $(document).on("change","#zone_id",function()
        {
            $("#territory_id").val("");
            $("#district_id").val("");
            $("#customer_id").val("");
            var zone_id=$('#zone_id').val();
            if(zone_id>0)
            {
                $('#territory_id_container').show();
                $('#district_id_container').hide();
                $('#customer_id_container').hide();
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_territories_by_zoneid/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{zone_id:zone_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#territory_id_container').hide();
                $('#district_id_container').hide();
                $('#customer_id_container').hide();
            }
        });
        $(document).on("change","#territory_id",function()
        {
            $("#district_id").val("");
            $("#customer_id").val("");
            var territory_id=$('#territory_id').val();
            if(territory_id>0)
            {
                $('#district_id_container').show();
                $('#customer_id_container').hide();
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_districts_by_territoryid/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{territory_id:territory_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#customer_id_container').hide();
                $('#district_id_container').hide();
            }
        });
        $(document).on("change","#district_id",function()
        {
            $("#customer_id").val("");
            var district_id=$('#district_id').val();
            if(district_id>0)
            {
                $('#customer_id_container').show();
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_customers_by_districtid/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{district_id:district_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#customer_id_container').hide();
            }
        });
        $(document).on("change","#arm_bank_id",function()
        {
            $("#arm_bank_account_id").val("");
            var arm_bank_id=$('#arm_bank_id').val();
            if(arm_bank_id>0)
            {
                $('#arm_bank_account_id_container').show();
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_armbankaccounts_by_armbankid/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{arm_bank_id:arm_bank_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {
                $('#arm_bank_account_id_container').hide();
            }
        });

    });
</script>
