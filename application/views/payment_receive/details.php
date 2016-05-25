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

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Payment <?php echo $this->lang->line('LABEL_DATE_PAYMENT');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo System_helper::display_date($payment['date_payment_customer']);?></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DIVISION_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $payment['division_name'];?></label>
            </div>
        </div>

        <div class="row show-grid" id="zone_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ZONE_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $payment['zone_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="territory_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $payment['territory_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="district_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $payment['district_name'];?></label>
            </div>
        </div>
        <div class="row show-grid" id="customer_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CUSTOMER_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $payment['customer_name'];?></label>

            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PAYMENT_WAY');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $payment['payment_way'];?></label>
            </div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_AMOUNT');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo number_format($payment['amount_customer'],2);?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_CHEQUE_NO');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $payment['cheque_no'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_BANK_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                $text='';
                foreach($banks as $bank)
                {
                    if($bank['value']==$payment['bank_id'])
                    {
                        $text=$bank['text'];
                    }
                }
                ?>
                <label class="control-label"><?php echo $text;?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_BANK_BRANCH_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <label class="control-label"><?php echo $payment['bank_branch'];?></label>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Receive Status</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if($payment['date_payment_receive']>0)
                {
                    ?>
                    <label class="control-label">Received</label>
                <?php
                }
                else
                {
                    ?>
                    <label class="control-label">Pending</label>
                <?php

                }
                ?>
            </div>
        </div>
        <?php
        if($payment['date_payment_receive']>0)
        {
        ?>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_RECEIVE');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo System_helper::display_date($payment['date_payment_receive']);?></label>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Received <?php echo $this->lang->line('LABEL_AMOUNT');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <label class="control-label"><?php echo number_format($payment['amount'],2);?></label>
                </div>
            </div>
            <div class="row show-grid">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Received <?php echo $this->lang->line('LABEL_ARM_BANK_NAME');?></label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <?php
                    $text='';
                    foreach($arm_banks as $arm_bank)
                    {
                        if($arm_bank['value']==$payment['arm_bank_id'])
                        {
                            $text=$arm_bank['text'];
                        }
                    }
                    ?>
                    <label class="control-label"><?php echo $text;?></label>
                </div>
            </div>
        <?php
        }
        ?>
    </div>

    <div class="clearfix"></div>

<script type="text/javascript">

    jQuery(document).ready(function()
    {
        turn_off_triggers();

    });
</script>
