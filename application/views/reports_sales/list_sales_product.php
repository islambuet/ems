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
            <div class="col-xs-12" style="margin-bottom: 20px;">
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="crop_name"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="crop_type_name"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="variety_name"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="pack_size"><?php echo $CI->lang->line('LABEL_PACK_NAME'); ?></label>


                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="po_quantity">Po Quantity</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  value="bonus_quantity">Bonus Quantity</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  value="return_quantity">Sales Return</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  value="bonus_return_quantity">Bonus Return</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="total_po_quantity">Total PO Quantity</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="actual_quantity">Actual Quantity</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="po_price">PO price</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="actual_price">Actual Price</label>
            </div>
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
        //var grand_total_color='#AEC2DD';
        var grand_total_color='#AEC2DD';

        var url = "<?php echo base_url($CI->controller_url.'/get_items_sales_product');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'crop_name', type: 'string' },
                { name: 'crop_type_name', type: 'string' },
                { name: 'variety_name', type: 'string' },
                { name: 'pack_size', type: 'string' },
                { name: 'po_quantity', type: 'string' },
                { name: 'bonus_quantity', type: 'string' },
                { name: 'return_quantity', type: 'string' },
                { name: 'bonus_return_quantity', type: 'string' },
                { name: 'total_po_quantity', type: 'string' },
                { name: 'actual_quantity', type: 'string' },
                { name: 'po_price', type: 'string' },
                { name: 'actual_price', type: 'string' }

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

            if (record.crop_type_name=="Crop Total")
            {
                if(column!='crop_name')
                {
                    element.css({ 'background-color': '#6CAB44','margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                }
            }
            else if (record.crop_name=="Grand Total")
            {

                element.css({ 'background-color': grand_total_color,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});

            }
            else
            {
                element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }

            return element[0].outerHTML;

        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var aggregates=function (total, column, element, record)
        {
            if(record.crop_name=="Grand Total")
            {
                //console.log(element);
                return record[element];

            }
            return total;
            //return grand_starting_stock;
        };
        var aggregatesrenderer=function (aggregates)
        {
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+grand_total_color+';">' +aggregates['total']+'</div>';

        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                height:'350px',
                source: dataAdapter,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                enabletooltips: true,
                showaggregates: true,
                showstatusbar: true,
                rowsheight: 35,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'crop_type_name',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_PACK_NAME'); ?>', dataField: 'pack_size',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Po Quantity', dataField: 'po_quantity',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Bonus Quantity',hidden:true, dataField: 'bonus_quantity',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Sales Return',hidden:true, dataField: 'return_quantity',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Bonus Return',hidden:true, dataField: 'bonus_return_quantity',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Total PO Quantity', dataField: 'total_po_quantity',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Actual Quantity', dataField: 'actual_quantity',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'PO price', dataField: 'po_price',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Actual Price', dataField: 'actual_price',width:'150',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer}
                ]
            });
    });
</script>