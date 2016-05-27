<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_save"]='#save_form';
    $action_data["action_save_new"]='#save_form';

    $CI->load->view("action_buttons",$action_data);
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $po['id']; ?>" />
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
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_DATE_PO');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="po[date_po]" id="date_po" class="form-control datepicker" value="<?php echo System_helper::display_date($po['date_po']);?>"/>
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
                                <option value="<?php echo $division['value']?>" <?php if($division['value']==$po['division_id']){ echo "selected";}?>><?php echo $division['text'];?></option>
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
                            <option value="<?php echo $zone['value']?>" <?php if($zone['value']==$po['zone_id']){ echo "selected";}?>><?php echo $zone['text'];?></option>
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
                            <option value="<?php echo $territory['value']?>" <?php if($territory['value']==$po['territory_id']){ echo "selected";}?>><?php echo $territory['text'];?></option>
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
                            <option value="<?php echo $district['value']?>" <?php if($district['value']==$po['district_id']){ echo "selected";}?>><?php echo $district['text'];?></option>
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
                <select id="customer_id" name="po[customer_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($customers as $customer)
                    {?>
                        <option value="<?php echo $customer['value']?>" <?php if($customer['value']==$po['customer_id']){ echo "selected";}?>><?php echo $customer['text'];?></option>
                    <?php
                    }
                    ?>
                </select>

            </div>
        </div>
        <div style="display: none;" class="row show-grid" id="warehouse_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="warehouse_id" name="po[warehouse_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($warehouses as $warehouse)
                    {?>
                        <option value="<?php echo $warehouse['value']?>" <?php if($warehouse['value']==$po['warehouse_id']){ echo "selected";}?>><?php echo $warehouse['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid" id="remarks_po">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" name="remarks"><?php echo $remarks; ?></textarea>
            </div>
        </div>
        <div class="widget-header">
            <div class="title">
                Order Items
            </div>
            <div class="clearfix"></div>
        </div>
        <?php
            if($po['id']>0)
            {
                ?>
                <div class="alert alert-warning">
                    <?php echo $CI->lang->line('MSG_PO_EDIT_WARNING'); ?>
                </div>
                <?php
            }
        ?>

        <div style="overflow-x: auto;" class="row show-grid" id="order_items_container">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_PACK_NAME'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_PRICE_PACK'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_QUANTITY_PIECES'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_WEIGHT_KG'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_TOTAL_PRICE'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_BONUS_QUANTITY_PIECES'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_BONUS_PACK_NAME'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('LABEL_BONUS_WEIGHT_KG'); ?></th>
                    <th style="min-width: 150px;"><?php echo $CI->lang->line('ACTION'); ?></th>

                </tr>
                </thead>
                <tbody>
                    <?php
                    $total_total_quantity=0;
                    $total_total_weight=0;
                    $total_total_price=0;
                    $total_total_bonus_quantity=0;
                    $total_total_bonus_weight=0;
                    foreach($po_varieties as $index=>$po_variety)
                    {
                        $total_total_quantity+=$po_variety['quantity'];
                        $total_total_weight+=$po_variety['pack_size']*$po_variety['quantity'];
                        $total_total_price+=$po_variety['variety_price']*$po_variety['quantity'];
                        $total_total_bonus_quantity+=$po_variety['quantity_bonus'];
                        if($po_variety['bonus_details_id']>0)
                        {
                            $total_total_bonus_weight+=$po_variety['quantity_bonus']*$po_variety['bonus_pack_size'];
                        }

                        ?>
                        <tr>
                            <td>
                                <label><?php echo $po_variety['crop_name']; ?></label>
                            </td>
                            <td>
                                <label><?php echo $po_variety['crop_type_name']; ?></label>
                            </td>
                            <td>
                                <label><?php echo $po_variety['variety_name']; ?></label>
                                <input type="hidden" class="variety_id" id="variety_id_<?php echo $index+1;?>" name="po_varieties[<?php echo $index+1;?>][variety_id]" value="<?php echo $po_variety['variety_id']; ?>" />
                            </td>
                            <td>
                                <label><?php echo $po_variety['pack_size']; ?></label>
                                <input type="hidden" class="pack_size_id" id="pack_size_id_<?php echo $index+1;?>" name="po_varieties[<?php echo $index+1;?>][pack_size_id]" value="<?php echo $po_variety['pack_size_id']; ?>" />

                            </td>
                            <td class="text-right">
                                <label><?php echo $po_variety['variety_price']; ?></label>
                            </td>
                            <td class="text-right">
                                <input type="text" value="<?php echo $po_variety['quantity']; ?>" class="form-control text-right quantity" id="quantity_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>" name="po_varieties[<?php echo $index+1;?>][quantity]">
                            </td>
                            <td class="text-right">
                                <label data-current-id="<?php echo $index+1;?>" id="total_weight_<?php echo $index+1;?>" class="total_weight">
                                    <span><?php echo number_format($po_variety['pack_size']*$po_variety['quantity']/1000,3,'.',''); ?></span>
                                    <input name="po_varieties[<?php echo $index+1;?>][pack_size]" value="<?php echo $po_variety['pack_size'];?>" type="hidden">
                                </label>
                            </td>
                            <td class="text-right">
                                <label class="total_price" id="total_price_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                    <span><?php echo number_format($po_variety['variety_price']*$po_variety['quantity'],2); ?></span>
                                    <input type="hidden" value="<?php echo $po_variety['variety_price']; ?>" name="po_varieties[<?php echo $index+1;?>][variety_price]">
                                    <input type="hidden" value="<?php echo $po_variety['variety_price_net']; ?>" name="po_varieties[<?php echo $index+1;?>][variety_price_net]">
                                    <input type="hidden" value="<?php echo $po_variety['variety_price_id']; ?>" name="po_varieties[<?php echo $index+1;?>][variety_price_id]">
                                </label>
                            </td>
                            <td class="text-right">
                                <label class="bonus_quantity" id="bonus_quantity_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>">
                                    <span><?php echo $po_variety['quantity_bonus']; ?></span>
                                    <input type="hidden" value="<?php echo $po_variety['quantity_bonus']; ?>" name="po_varieties[<?php echo $index+1;?>][quantity_bonus]">
                                    <input type="hidden" value="<?php echo $po_variety['bonus_details_id']; ?>" name="po_varieties[<?php echo $index+1;?>][bonus_details_id]">
                                    <input type="hidden" value="<?php echo $po_variety['bonus_pack_size']; ?>" name="po_varieties[<?php echo $index+1;?>][bonus_pack_size]">
                                    <input type="hidden" value="<?php echo $po_variety['bonus_pack_size_id']; ?>" name="po_varieties[<?php echo $index+1;?>][bonus_pack_size_id]">
                                </label>
                            </td>
                            <td class="text-right">
                                <label class="bonus_pack_size_name" id="bonus_pack_size_name_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>"><?php if($po_variety['bonus_details_id']>0){echo $po_variety['bonus_pack_size'];}else{echo 'N/A';} ?></label>
                            </td>
                            <td class="text-right">
                                <label class="bonus_total_weight" id="bonus_total_weight_<?php echo $index+1;?>" data-current-id="<?php echo $index+1;?>"><span><?php echo number_format($po_variety['quantity_bonus']*$po_variety['bonus_pack_size']/1000,3,'.',''); ?></span></label>
                            </td>
                            <td>
                                <button class="btn btn-danger system_button_add_delete" type="button"><?php echo $CI->lang->line('DELETE'); ?></button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>

                </tbody>
                <tfoot>
                <tr>
                    <td class="text-right" colspan="5"><label><?php echo $CI->lang->line('LABEL_TOTAL'); ?></label></td>
                    <td class="text-right"><label id="total_total_quantity"><?php echo number_format($total_total_quantity,0,'.',''); ?></label></td>
                    <td class="text-right"><label id="total_total_weight"><?php echo number_format($total_total_weight/1000,3,'.',''); ?></label></td>
                    <td class="text-right"><label id="total_total_price"><?php echo number_format($total_total_price,2); ?></label></td>
                    <td class="text-right"><label id="total_total_bonus_quantity"><?php echo number_format($total_total_bonus_quantity,0,'.',''); ?></label></td>
                    <td>&nbsp;</td>
                    <td class="text-right"><label id="total_total_bonus_weight"><?php echo number_format($total_total_bonus_weight/1000,3,'.',''); ?></label></td>
                    <td>&nbsp;</td>

                </tr>
                </tfoot>
            </table>

        </div>
        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4">
                <button type="button" class="btn btn-warning system_button_add_more" data-current-id="<?php echo sizeof($po_varieties);?>"><?php echo $CI->lang->line('LABEL_ADD_MORE');?></button>
            </div>
            <div class="col-xs-4">

            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<div id="system_content_add_more" style="display: none;">
    <table>
        <tbody>
            <tr>
                <td>
                    <select class="form-control crop_id">
                        <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        <?php
                        foreach($crops as $crop)
                        {?>
                            <option value="<?php echo $crop['value']?>"><?php echo $crop['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <div style="display: none;" class="crop_type_id_container">
                        <select class="form-control crop_type_id">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        </select>
                    </div>
                </td>
                <td>
                    <div style="display: none;" class="variety_id_container">
                        <select class="form-control variety_id">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        </select>
                    </div>
                </td>
                <td>
                    <div style="display: none;" class="pack_size_id_container">
                        <select class="form-control pack_size_id">
                            <option value=""><?php echo $this->lang->line('SELECT');?></option>
                        </select>
                    </div>
                </td>
                <td class="text-right">
                    <label class="pack_size_price">&nbsp;</label>
                </td>
                <td class="text-right">
                    <input type="text"class="form-control text-right quantity" value=""/>
                </td>
                <td class="text-right">
                    <label class="total_weight">&nbsp;</label>
                </td>
                <td class="text-right">
                    <label class="total_price">&nbsp;</label>
                </td>
                <td class="text-right">
                    <label class="bonus_quantity">&nbsp;</label>
                </td>
                <td class="text-right">
                    <label class="bonus_pack_size_name">&nbsp;</label>
                </td>
                <td class="text-right">
                    <label class="bonus_total_weight">&nbsp;</label>
                </td>

                <td><button type="button" class="btn btn-danger system_button_add_delete"><?php echo $CI->lang->line('DELETE'); ?></button></td>
            </tr>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    function set_rest_blank(active_id)
    {
        $('#total_weight_'+active_id).html('');
        $('#total_price_'+active_id).html('');
        $('#bonus_quantity_'+active_id).html('');
        $('#bonus_pack_size_name_'+active_id).html('');
        $('#bonus_total_weight_'+active_id).html('');
    }
    function calculate_total()
    {
        var total_quantity=0;
        $("#order_items_container tbody .quantity").each( function( index, element )
        {
            if($(this).val()==parseFloat($(this).val()))
            {
                total_quantity=total_quantity+parseFloat($(this).val());
            }
        });
        $('#total_total_quantity').html(number_format(total_quantity,'0','.',''));
        var total_price=0;
        $("#order_items_container tbody .total_price span").each( function( index, element ){
            total_price=total_price+parseFloat($(this).html().replace(/,/g,''));
        });
        $('#total_total_price').html(number_format(total_price,2));

        var total_weight=0;
        $("#order_items_container tbody .total_weight span").each( function( index, element ){
            total_weight=total_weight+parseFloat($(this).html().replace(/,/g,''));
        });
        $('#total_total_weight').html(number_format(total_weight,3,'.',''));

        var total_bonus_quantity=0;
        $("#order_items_container tbody .bonus_quantity span").each( function( index, element ){
            total_bonus_quantity=total_bonus_quantity+parseFloat($(this).html().replace(/,/g,''));
        });
        $('#total_total_bonus_quantity').html(number_format(total_bonus_quantity,0,',',''));

        var total_bonus_total_weight=0;
        $("#order_items_container tbody .bonus_total_weight span").each( function( index, element ){
            total_bonus_total_weight=total_bonus_total_weight+parseFloat($(this).html().replace(/,/g,''));
        });
        $('#total_total_bonus_weight').html(number_format(total_bonus_total_weight,3,'.',''));
    }
    jQuery(document).ready(function()
    {
        turn_off_triggers();
        $(document).on("click", ".system_button_add_more", function(event)
        {
            var current_id=parseInt($(this).attr('data-current-id'));
            current_id=current_id+1;
            $(this).attr('data-current-id',current_id);
            var content_id='#system_content_add_more table tbody';
            $(content_id+' .crop_id').attr('id','crop_id_'+current_id);
            $(content_id+' .crop_id').attr('data-current-id',current_id);

            $(content_id+' .crop_type_id').attr('id','crop_type_id_'+current_id);
            $(content_id+' .crop_type_id').attr('data-current-id',current_id);
            $(content_id+' .crop_type_id_container').attr('id','crop_type_id_container_'+current_id);

            $(content_id+' .variety_id').attr('id','variety_id_'+current_id);
            $(content_id+' .variety_id').attr('data-current-id',current_id);
            $(content_id+' .variety_id').attr('name','po_varieties['+current_id+'][variety_id]');
            $(content_id+' .variety_id_container').attr('id','variety_id_container_'+current_id);

            $(content_id+' .pack_size_id').attr('id','pack_size_id_'+current_id);
            $(content_id+' .pack_size_id').attr('data-current-id',current_id);
            $(content_id+' .pack_size_id').attr('name','po_varieties['+current_id+'][pack_size_id]');
            $(content_id+' .pack_size_id_container').attr('id','pack_size_id_container_'+current_id);

            $(content_id+' .pack_size_price').attr('id','pack_size_price_'+current_id);
            $(content_id+' .pack_size_price').attr('data-current-id',current_id);

            $(content_id+' .quantity').attr('id','quantity_'+current_id);
            $(content_id+' .quantity').attr('data-current-id',current_id);
            $(content_id+' .quantity').attr('name','po_varieties['+current_id+'][quantity]');

            $(content_id+' .total_weight').attr('id','total_weight_'+current_id);
            $(content_id+' .total_weight').attr('data-current-id',current_id);

            $(content_id+' .total_price').attr('id','total_price_'+current_id);
            $(content_id+' .total_price').attr('data-current-id',current_id);

            $(content_id+' .bonus_quantity').attr('id','bonus_quantity_'+current_id);
            $(content_id+' .bonus_quantity').attr('data-current-id',current_id);

            $(content_id+' .bonus_pack_size_name').attr('id','bonus_pack_size_name_'+current_id);
            $(content_id+' .bonus_pack_size_name').attr('data-current-id',current_id);

            $(content_id+' .bonus_total_weight').attr('id','bonus_total_weight_'+current_id);
            $(content_id+' .bonus_total_weight').attr('data-current-id',current_id);

            //$('#system_add_more_content .date').attr('name','booked_varieties['+current_id+'][date]');
            //$('#system_add_more_content .variety').attr('name','booked_varieties['+current_id+'][id]');
            //$('#system_add_more_content .quantity').attr('name','booked_varieties['+current_id+'][quantity]');
            var html=$(content_id).html();
            $("#order_items_container tbody").append(html);
            $(content_id+' .crop_id').removeAttr('id');
            $(content_id+' .crop_type_id').removeAttr('id');
            $(content_id+' .crop_type_id_container').removeAttr('id');
            $(content_id+' .variety_id').removeAttr('id');
            $(content_id+' .variety_id_container').removeAttr('id');
            $(content_id+' .pack_size_id').removeAttr('id');
            $(content_id+' .pack_size_id_container').removeAttr('id');
            $(content_id+' .pack_size_price').removeAttr('id');
            $(content_id+' .quantity').removeAttr('id');
            $(content_id+' .total_weight').removeAttr('id');
            $(content_id+' .total_price').removeAttr('id');
            $(content_id+' .bonus_quantity').removeAttr('id');
            $(content_id+' .bonus_pack_size_name').removeAttr('id');
            //$(content_id+' .bonus_rule_id').removeAttr('id');
            $(content_id+' .bonus_total_weight').removeAttr('id');
            //$("#system_add_more_container .date").datepicker({dateFormat : display_date_format});

        });
        $(document).on("change",".crop_id",function()
        {

            var active_id=parseInt($(this).attr('data-current-id'));
            $("#crop_type_id_"+active_id).val("");
            $("#variety_id_"+active_id).val("");
            $("#pack_size_id"+active_id).val("");
            $("#pack_size_price_"+active_id).html("");
            $("#quantity_"+active_id).val("");
            set_rest_blank(active_id);
            calculate_total();
            var crop_id=$('#crop_id_'+active_id).val();
            if(crop_id>0)
            {
                $('#crop_type_id_container_'+active_id).show();
                $('#variety_id_container_'+active_id).hide();
                $('#pack_size_id_container_'+active_id).hide();
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_croptypes_by_cropid/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{crop_id:crop_id,html_container_id:'#crop_type_id_'+active_id},
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
                $('#crop_type_id_container_'+active_id).hide();
                $('#variety_id_container_'+active_id).hide();
                $('#pack_size_id_container_'+active_id).hide();
            }
        });
        $(document).on("change",".crop_type_id",function()
        {
            var active_id=parseInt($(this).attr('data-current-id'));

            $("#variety_id_"+active_id).val("");
            $("#pack_size_id"+active_id).val("");
            $("#pack_size_price_"+active_id).html("");
            $("#quantity_"+active_id).val("");
            set_rest_blank(active_id);
            calculate_total();
            var crop_type_id=$('#crop_type_id_'+active_id).val();
            if(crop_type_id>0)
            {
                $('#variety_id_container_'+active_id).show();
                $('#pack_size_id_container_'+active_id).hide();
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_armvarieties_by_croptypeid/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{crop_type_id:crop_type_id,html_container_id:'#variety_id_'+active_id},
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
                $('#variety_id_container_'+active_id).hide();
                $('#pack_size_id_container_'+active_id).hide();
            }
        });
        $(document).on("change",".variety_id",function()
        {
            var active_id=parseInt($(this).attr('data-current-id'));
            $("#pack_size_id"+active_id).val("");
            $("#pack_size_price_"+active_id).html("");
            $("#quantity_"+active_id).val("");
            set_rest_blank(active_id);
            calculate_total();
            var variety_id=$('#variety_id_'+active_id).val();
            var warehouse_id=$('#warehouse_id').val();

            if(variety_id>0)
            {
                $('#pack_size_id_container_'+active_id).show();
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_packsizes_by_variety_warehouse/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{variety_id:variety_id,warehouse_id:warehouse_id,html_container_id:'#pack_size_id_'+active_id},
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
                $('#pack_size_id_container_'+active_id).hide();
            }
        });
        $(document).on("change",".pack_size_id",function()
        {
            var active_id=parseInt($(this).attr('data-current-id'));
            $("#pack_size_price_"+active_id).html("");

            var variety_id=$('#variety_id_'+active_id).val();
            var pack_size_id=$('#pack_size_id_'+active_id).val();
            $("#quantity_"+active_id).val("");
            set_rest_blank(active_id);
            calculate_total();
            if(variety_id>0 && pack_size_id>0)
            {
                $.ajax({
                    url: base_url+"common_controller/get_price_by_variety_pack_size_id/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{variety_id:variety_id,pack_size_id:pack_size_id,html_container_id:'#pack_size_price_'+active_id},
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
        });
        $(document).on("change",".quantity",function()
        {
            var active_id=parseInt($(this).attr('data-current-id'));
            var quantity=parseInt($('#quantity_'+active_id).val());
            var variety_id=$('#variety_id_'+active_id).val();
            var pack_size_id=$('#pack_size_id_'+active_id).val();
            set_rest_blank(active_id);
            if((quantity==$('#quantity_'+active_id).val())&&(quantity>0)&&pack_size_id>0&&variety_id>0)
            {
                $.ajax({
                    url: base_url+"<?php echo $CI->controller_url;?>/get_bonus_and_total",
                    type: 'POST',
                    datatype: "JSON",
                    data:{variety_id:variety_id,pack_size_id:pack_size_id,quantity:quantity,active_id:active_id},
                    success: function (data, status)
                    {

                    },
                    complete:function(xhr,status)
                    {
                        calculate_total();
                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");

                    }
                });
            }
            else
            {

            }

        });
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
        $(document).on("change","#warehouse_id",function()
        {
            $("#order_items_container table tbody").html("");
            var warehouse_id=$('#warehouse_id').val();
            if(warehouse_id>0)
            {
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_crops_by_warehouseid/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{warehouse_id:warehouse_id,html_container_id:'.crop_id'},
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

            }
        });

        // Delete more button
        $(document).on("click", ".system_button_add_delete", function(event)
        {
//            console.log('allah is one');
            $(this).closest('tr').remove();
            calculate_total();
        });
    });
</script>
