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
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="current_price">Current unit(pieces) Price</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="current_total_price">Current Stock Price</label>
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
                { name: 'current', type: 'string' },
                { name: 'current_price', type: 'string' },
                { name: 'current_total_price', type: 'string' }
            ],
            id: 'id',
            url: url,
            type: 'POST',
            data:{<?php echo $keys; ?>}
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
           // console.log(defaultHtml);

            if (record.variety_name=="Total Type")
            {
                if(!((column=='crop_name')||(column=='crop_type_name')))
                {
                    element.css({ 'background-color': '#6CAB44','margin': '0px','width': '100%', 'height': '100%',padding:'4px 2px 2px 4px'});
                }
            }
            else if (record.crop_type_name=="Total Crop")
            {


                if((column!='crop_name'))
                {
                    element.css({ 'background-color': '#0CA2C5','margin': '0px','width': '100%', 'height': '100%',padding:'4px 2px 2px 4px'});
                }

            }
            else if (record.crop_name=="Grand Total")
            {

                element.css({ 'background-color': '#AEC2DD','margin': '0px','width': '100%', 'height': '100%',padding:'4px 2px 2px 4px'});

            }

            return element[0].outerHTML;

        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                height:'600px',
                source: dataAdapter,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                enabletooltips: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',width: '100',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'crop_type_name',width: '100',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',width: '100',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: 'Pack Size(gm)', dataField: 'pack_size_name',cellsalign: 'right',width: '100',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: 'Starting Stock', dataField: 'starting_stock',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Stock In', dataField: 'stock_in',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Excess', dataField: 'excess',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Sales', dataField: 'sales',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Sales Return', dataField: 'sales_return',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Sales Bonus', dataField: 'sales_bonus',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Sales Bonus Return', dataField: 'sales_return_bonus',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Short', dataField: 'short',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Rnd Sample', dataField: 'rnd',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Sample', dataField: 'sample',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Current Stock', dataField: 'current',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Current unit(pieces) Price', dataField: 'current_price',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Current Stock Price', dataField: 'current_total_price',width:'150',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer}
                ]
            });
    });
</script>