<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons=array();
if(isset($CI->permissions['edit'])&&($CI->permissions['edit']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_EDIT"),
        'class'=>'button_action_batch',
        'id'=>'button_action_edit',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit')
    );
}
if(isset($CI->permissions['view'])&&($CI->permissions['view']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DETAILS"),
        'class'=>'button_action_batch',
        'id'=>'button_action_details',
        'data-action-link'=>site_url($CI->controller_url.'/index/details')
    );
}
if(isset($CI->permissions['print'])&&($CI->permissions['print']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'id'=>'button_action_print',
        'data-title'=>'PO LIST'
    );
}
if(isset($CI->permissions['download'])&&($CI->permissions['download']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'id'=>'button_action_csv',
        'data-title'=>'PO LIST'
    );
}

$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list')

);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_LOAD_MORE"),
    'id'=>'button_jqx_load_more'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    if(isset($CI->permissions['column_headers'])&&($CI->permissions['column_headers']==1))
    {

        ?>
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="po_no"><?php echo $CI->lang->line('LABEL_PO_NO'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="name"><?php echo $CI->lang->line('LABEL_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date_po"><?php echo $CI->lang->line('LABEL_DATE_PO'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="customer_code"><?php echo $CI->lang->line('LABEL_CUSTOMER_CODE'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="division_name"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="zone_name"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="territory_name"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="district_name"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="quantity_total"><?php echo $CI->lang->line('LABEL_QUANTITY_PIECES'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="quantity_weight"><?php echo $CI->lang->line('LABEL_WEIGHT_KG'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="price_total"><?php echo $CI->lang->line('LABEL_TOTAL_PRICE'); ?></label>

            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="quantity_total"><?php echo $CI->lang->line('LABEL_QUANTITY_RETURN_PIECES'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="quantity_weight"><?php echo $CI->lang->line('LABEL_WEIGHT_RETURN_KG'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="price_total"><?php echo $CI->lang->line('LABEL_TOTAL_PRICE_RETURN'); ?></label>

            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="quantity_total"><?php echo $CI->lang->line('LABEL_QUANTITY_ACTUAL_PIECES'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="quantity_weight"><?php echo $CI->lang->line('LABEL_WEIGHT_ACTUAL_KG'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="price_total"><?php echo $CI->lang->line('LABEL_TOTAL_PRICE_ACTUAL'); ?></label>

            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="status_received"><?php echo $CI->lang->line('LABEL_RECEIVED'); ?></label>
        </div>
    <?php
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        turn_off_triggers();
        var url = "<?php echo base_url($CI->controller_url.'/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'po_no', type: 'string' },
                { name: 'date_po', type: 'string' },
                { name: 'name', type: 'string' },
                { name: 'customer_code', type: 'string' },
                { name: 'division_name', type: 'string' },
                { name: 'zone_name', type: 'string' },
                { name: 'territory_name', type: 'string' },
                { name: 'district_name', type: 'string' },
                { name: 'quantity_total', type: 'number' },
                { name: 'quantity_weight', type: 'string' },
                { name: 'price_total', type: 'string' },
                { name: 'quantity_return_total', type: 'number' },
                { name: 'quantity_return_weight', type: 'string' },
                { name: 'price_return_total', type: 'string' },
                { name: 'quantity_actual_total', type: 'number' },
                { name: 'quantity_actual_weight', type: 'string' },
                { name: 'price_actual_total', type: 'string' },
                { name: 'status_received', type: 'string' }

            ],
            id: 'id',
            type: 'POST',
            url: url
        };
        var dataAdapter = new $.jqx.dataAdapter(source);

        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                source: dataAdapter,
                pageable: true,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                pagesize:20,
                pagesizeoptions: ['20', '50', '100', '200','300','500'],
                selectionmode: 'singlerow',
                altrows: true,
                autoheight: true,
                autorowheight: true,
                columnsreorder: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_PO_NO'); ?>', dataField: 'po_no',width:'60',cellsalign: 'right',pinned:true},
                    { text: '<?php echo $CI->lang->line('LABEL_NAME'); ?>', dataField: 'name',width:'150',pinned:true},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_PO'); ?>', dataField: 'date_po',width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_CUSTOMER_CODE'); ?>', dataField: 'customer_code',width:'80'},
                    { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>', dataField: 'division_name',filtertype: 'list',width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>', dataField: 'zone_name',width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>', dataField: 'territory_name',width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>', dataField: 'district_name',width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_PIECES'); ?>', dataField: 'quantity_total',width:'80',cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_WEIGHT_KG'); ?>', dataField: 'quantity_weight',width:'100',cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_TOTAL_PRICE'); ?>', dataField: 'price_total',width:'150',cellsalign: 'right'},

                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_RETURN_PIECES'); ?>', dataField: 'quantity_return_total',width:'80',cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_WEIGHT_RETURN_KG'); ?>', dataField: 'quantity_return_weight',width:'100',cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_TOTAL_PRICE_RETURN'); ?>', dataField: 'price_return_total',width:'150',cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_ACTUAL_PIECES'); ?>', dataField: 'quantity_actual_total',width:'80',cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_WEIGHT_ACTUAL_KG'); ?>', dataField: 'quantity_actual_weight',width:'100',cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_TOTAL_PRICE_ACTUAL'); ?>', dataField: 'price_actual_total',width:'150',cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_RECEIVED'); ?>', dataField: 'status_received',width:'80',cellsalign: 'right',filtertype: 'list'}

                ]
            });
        //var listSource = [{ label: 'Name', value: 'name', checked: false }, { label: 'Beverage Type', value: 'type', checked: true }, { label: 'Calories', value: 'calories', checked: true }, { label: 'Total Fat', value: 'totalfat', checked: true }, { label: 'Protein', value: 'protein', checked: true}];

        //$("#jqxlistbox").jqxListBox({ source: listSource,   checkboxes: true });

    });
</script>