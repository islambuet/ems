<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    if(isset($CI->permissions['print'])&&($CI->permissions['print']==1))
    {
        $action_data["action_print"]='print';
    }
    if(isset($CI->permissions['download'])&&($CI->permissions['download']==1))
    {
        $action_data["action_csv"]='csv';
    }
    if(sizeof($action_data)>0)
    {
        $CI->load->view("action_buttons",$action_data);
    }

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
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="crop_name"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="crop_type_name"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="variety_name"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="pack_size_name"><?php echo $CI->lang->line('LABEL_PACK_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="starting_stock">Starting Stock</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="stock_in">Stock In</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="excess">Excess</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="sales">Sales</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="sales_return">Sales Return</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="sales_bonus">Sales Bonus</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="sales_return_bonus">Sales Bonus Return</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="short">Short</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="rnd">Rnd Sample</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="sample">Sample</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="current">Current Stock</label>
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

        var url = "<?php echo base_url($CI->controller_url.'/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'crop_name', type: 'string' },
                { name: 'crop_type_name', type: 'string' },
                { name: 'variety_name', type: 'string' },
                { name: 'pack_size_name', type: 'numeric' },
                { name: 'starting_stock', type: 'string' },
                { name: 'stock_in', type: 'string' },
                { name: 'excess', type: 'string' },
                { name: 'sales', type: 'string' },
                { name: 'sales_return', type: 'string' },
                { name: 'sales_bonus', type: 'string' },
                { name: 'sales_return_bonus', type: 'string' },
                { name: 'short', type: 'string' },
                { name: 'rnd', type: 'string' },
                { name: 'sample', type: 'string' },
                { name: 'current', type: 'string' }
            ],
            id: 'id',
            url: url,
            type: 'POST',
            data:{<?php echo $keys; ?>}
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                height:300,
                source: dataAdapter,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                columnsheight:'60',

                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',width: '100'},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'crop_type_name',width: '100'},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',width: '100'},
                    { text: 'Pack Size<br>(gm)', dataField: 'pack_size_name',cellsalign: 'right',width: '100'},
                    { text: 'Starting<br>Stock', dataField: 'starting_stock',width:'100',cellsalign: 'right'},
                    { text: 'Stock In', dataField: 'stock_in',width:'100',cellsalign: 'right'},
                    { text: 'Excess', dataField: 'excess',width:'100',cellsalign: 'right'},
                    { text: 'Sales', dataField: 'sales',width:'100',cellsalign: 'right'},
                    { text: 'Sales Return', dataField: 'sales_return',width:'100',cellsalign: 'right'},
                    { text: 'Sales Bonus', dataField: 'sales_bonus',width:'100',cellsalign: 'right'},
                    { text: 'Sales Bonus<br> Return', dataField: 'sales_return_bonus',width:'100',cellsalign: 'right'},
                    { text: 'Short', dataField: 'short',width:'100',cellsalign: 'right'},
                    { text: 'Rnd Sample', dataField: 'rnd',width:'100',cellsalign: 'right'},
                    { text: 'Sample', dataField: 'sample',width:'100',cellsalign: 'right'},
                    { text: 'Current Stock', dataField: 'current',width:'100',cellsalign: 'right'}
                ]
            });
    });
</script>