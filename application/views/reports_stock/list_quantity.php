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
                { name: 'current', type: 'string' },
                { name: 'current_price', type: 'string' }
            ],
            updaterow: function (rowid, rowdata, commit) {
                // synchronize with the server - send update command
                commit(true);
            },
            id: 'id',
            url: url,
            type: 'POST',
            data:{<?php echo $keys; ?>}
        };
        var crop_starting_stock=0;
        var type_starting_stock=0;

        var crop_stock_in=0;
        var type_stock_in=0;
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
           // console.log(defaultHtml);

            if (record.crop_type_name=="Total Type")
            {
                if(column!='crop_name')
                {
                    element.css({ 'background-color': '#6CAB44','margin': '0px','width': '100%', 'height': '100%',padding:'4px 2px 2px 4px'});
                }
                if(column=='starting_stock')
                {
                    //element.html(type_starting_stock);
                    element.html(number_format(type_starting_stock,0,'.',''));
                    type_starting_stock=0;
                }
                else if(column=='stock_in')
                {
                    element.html(number_format(type_stock_in,0,'.',''));
                    type_stock_in=0;
                }
            }
            else if (record.crop_name=="Total Crop")
            {

                var element = $(defaultHtml);
                element.css({ 'background-color': '#0CA2C5','margin': '0px','width': '100%', 'height': '100%',padding:'4px 2px 2px 4px'});
                if(column=='starting_stock')
                {
                    //element.html(crop_starting_stock);
                    element.html(number_format(crop_starting_stock,0,'.',''));
                    crop_starting_stock=0;
                }
                else if(column=='stock_in')
                {
                    //element.html(crop_stock_in);
                    element.html(number_format(crop_stock_in,0,'.',''));
                    crop_stock_in=0;
                }
            }
            else
            {
                if(column=='starting_stock')
                {

                    //grand_starting_stock+=value;
                    //console.log(grand_starting_stock+' '+value);
                    //crop_starting_stock+=value;
                    //type_starting_stock+=value

                    var weight=number_format((value),0,'.','');
                    element.html(weight);
                    crop_starting_stock+=parseFloat(String(weight).replace(/,/g,''));
                    type_starting_stock+=parseFloat(String(weight).replace(/,/g,''));
                }
                else if(column=='stock_in')
                {
                    //crop_stock_in+=value;
                    //type_stock_in+=value

                    var weight=number_format((value),0,'.','');
                    element.html(weight);
                    crop_stock_in+=parseFloat(String(weight).replace(/,/g,''));
                    type_stock_in+=parseFloat(String(weight).replace(/,/g,''));

                }
            }

            return element[0].outerHTML;

        };
        var grand_starting_stock=function (total, column, element, record)
        {
            if(element=="starting_stock")
            {
                var qtn=parseFloat(String(record[element]).replace(/,/g,''));
                if(qtn)
                {

                    return total+qtn;
                }


            }
            return total;
            //return grand_starting_stock;
        };
        var grand_stock=function (total, column, element, record)
        {
            if(element=="stock_in")
            {
                var qtn=parseFloat(String(record[element]).replace(/,/g,''));
                if(qtn)
                {

                    return total+qtn;
                }
            }
            return total;

        };
        var aggregatesrenderer=function (aggregates)
            {
                return '<div style="position: relative; margin: 0px;padding: 4px;width: 100%;height: 100%; overflow: hidden;">' +number_format(aggregates['total'],0,'.','')+'</div>';

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
                showaggregates: true,
                showstatusbar: true,
                statusbarheight: 50,


                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',width: '100',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'crop_type_name',width: '100',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',width: '100',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: 'Pack Size(gm)', dataField: 'pack_size_name',cellsalign: 'right',width: '100',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: 'Starting Stock', dataField: 'starting_stock',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':grand_starting_stock}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Stock In', dataField: 'stock_in',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':grand_stock}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Excess', dataField: 'excess',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Sales', dataField: 'sales',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Sales Return', dataField: 'sales_return',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Sales Bonus', dataField: 'sales_bonus',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Sales Bonus Return', dataField: 'sales_return_bonus',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Short', dataField: 'short',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Rnd Sample', dataField: 'rnd',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Sample', dataField: 'sample',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Current Stock', dataField: 'current',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Current Price', dataField: 'current_price',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer}
                ]
            });
    });
</script>