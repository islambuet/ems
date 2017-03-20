<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
$action_buttons=array();
    if(isset($CI->permissions['add'])&&($CI->permissions['add']==1))
    {
        $action_buttons[]=array(
            'label'=>$CI->lang->line("ACTION_NEW"),
            'href'=>site_url($CI->controller_url.'/index/add')
        );
        $action_buttons[]=array(
            'label'=>$CI->lang->line("ACTION_NEW_MULTIPLE"),
            'href'=>site_url($CI->controller_url.'/index/add_multiple')
        );
    }
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
    if(isset($CI->permissions['delete'])&&($CI->permissions['delete']==1))
    {
        $action_buttons[]=array(
            'type'=>'button',
            'label'=>$CI->lang->line("ACTION_DELETE"),
            'id'=>'button_action_delete',
            'data-action-link'=>site_url($CI->controller_url.'/index/delete')
        );
    }
if(isset($CI->permissions['print'])&&($CI->permissions['print']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'id'=>'button_action_print',
        'data-title'=>'PRINT'
    );
}
if(isset($CI->permissions['download'])&&($CI->permissions['download']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'id'=>'button_action_csv',
        'data-title'=>'Download'
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
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="fiscal_year_name"><?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date_stock_out"><?php echo $CI->lang->line('LABEL_DATE_STOCK_OUT'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="purpose"><?php echo $CI->lang->line('LABEL_STOCK_OUT_PURPOSE'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="warehouse_name"><?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="crop_name"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="crop_type_name"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="variety_name"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="pack_size_name"><?php echo $CI->lang->line('LABEL_PACK_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="quantity"><?php echo $CI->lang->line('LABEL_QUANTITY_PIECES'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="quantity_weight"><?php echo $CI->lang->line('LABEL_WEIGHT_KG'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="customer_name"><?php echo $CI->lang->line('LABEL_CUSTOMER_NAME'); ?></label>
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
                { name: 'fiscal_year_name', type: 'string' },
                { name: 'date_stock_out', type: 'string' },
                { name: 'purpose', type: 'string' },
                { name: 'warehouse_name', type: 'string' },
                { name: 'crop_name', type: 'string' },
                { name: 'crop_type_name', type: 'string' },
                { name: 'variety_name', type: 'string' },
                { name: 'pack_size_name', type: 'string' },
                { name: 'quantity', type: 'number' },
                { name: 'quantity_weight', type: 'string' },
                { name: 'customer_name', type: 'string' }
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
                    { text: '<?php echo $CI->lang->line('LABEL_FISCAL_YEAR'); ?>', dataField: 'fiscal_year_name',filtertype: 'list'},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_STOCK_OUT'); ?>', dataField: 'date_stock_out',width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_STOCK_OUT_PURPOSE'); ?>', dataField: 'purpose',filtertype: 'list',width:'150'},
                    { text: '<?php echo $CI->lang->line('LABEL_WAREHOUSE_NAME'); ?>', dataField: 'warehouse_name',filtertype: 'list',width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',filtertype: 'list',width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'crop_type_name',width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name'},
                    { text: '<?php echo $CI->lang->line('LABEL_PACK_NAME'); ?>', dataField: 'pack_size_name',width:'150',filtertype: 'list'},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_PIECES'); ?>', dataField: 'quantity',width:'150',cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_WEIGHT_KG'); ?>', dataField: 'quantity_weight',width:'150',cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_CUSTOMER_NAME'); ?>', dataField: 'customer_name',width:'150'}
                ]
            });
    });
</script>