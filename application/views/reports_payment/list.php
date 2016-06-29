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
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="sl_no"><?php echo $CI->lang->line('LABEL_SL_NO'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="division_name"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="zone_name"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="territory_name"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="district_name"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="customer_name"><?php echo $CI->lang->line('LABEL_CUSTOMER_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="payment_no">Payment ID</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="payment_date">Payment Date</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="payment_amount">Payment Amount</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="payment_bank">Payment Bank</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="receive_date">Receive Date</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="receive_amount">Receive Amount</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="receive_bank">Receive Bank</label>

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

        var url = "<?php echo base_url($CI->controller_url.'/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'division_name', type: 'string' },
                { name: 'zone_name', type: 'string' },
                { name: 'territory_name', type: 'string' },
                { name: 'district_name', type: 'string' },
                { name: 'customer_name', type: 'string' },
                { name: 'payment_no', type: 'string' },
                { name: 'payment_date', type: 'string' },
                { name: 'payment_amount', type: 'string' },
                { name: 'payment_bank', type: 'string' },
                { name: 'receive_date', type: 'string' },
                { name: 'receive_amount', type: 'string' },
                { name: 'receive_bank', type: 'string' }
            ],
            id: 'id',
            url: url,
            type: 'POST',
            data:{<?php echo $keys; ?>}
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            if (record.customer_name=="Total")
            {
                element.css({ 'background-color': grand_total_color,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }
            else
            {
                element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','whiteSpace':'normal'});
            }
            if(column=='sl')
            {
                if (record.customer_name!="Total")
                {
                    element.html(value+1);
                }

            }
            return element[0].outerHTML;

        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var aggregates=function (total, column, element, record)
        {
            if(record.customer_name=="Total")
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
                rowsheight: 35,
                showaggregates: true,
                showstatusbar: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_SL_NO'); ?>',datafield: 'sl',pinned:true,width:'80', columntype: 'number',cellsalign: 'right',sortable:false,filterable:false,cellsrenderer: cellsrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>',pinned:true, dataField: 'division_name',width:'100',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>',pinned:true, dataField: 'zone_name',width:'100',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>',pinned:true, dataField: 'territory_name',width:'100',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>',pinned:true, dataField: 'district_name',width:'100',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_CUSTOMER_NAME'); ?>',pinned:true, dataField: 'customer_name',width:'150',rendered:tooltiprenderer},
                    { text: 'Payment ID',dataField: 'payment_no',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center'},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_PAYMENT'); ?>',dataField: 'payment_date',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { text: 'Payment <?php echo $CI->lang->line('LABEL_AMOUNT'); ?>',dataField: 'payment_amount',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { text: 'Payment Bank',dataField: 'payment_bank',width:'200',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center'},
                    { text: 'Receive <?php echo $CI->lang->line('LABEL_DATE'); ?>',dataField: 'receive_date',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { text: 'Receive <?php echo $CI->lang->line('LABEL_AMOUNT'); ?>',dataField: 'receive_amount',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'Receive Bank',dataField: 'receive_bank',width:'200',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center'}

                ]

            });
    });
</script>