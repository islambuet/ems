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
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="opening_balance_tp"><?php echo $CI->lang->line('LABEL_OPENING_BALANCE'); ?> TP</label>

                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date_po">PO Date</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="po_no">Po NO</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="sales_tp">Sales</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="payment_no">Payment ID</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="payment_date">Payment Date</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="payment_amount">Payment Amount</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="payment_bank">Payment Bank</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="receive_date">Receive Amount</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="receive_amount">Receive Amount</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="receive_bank">Receive Amount</label>


                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="adjust_date">Adjust Date</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="adjust_tp">Adjust TP</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date_return">Sale Return Date</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="return_po_no">Return Po NO</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="return_tp">Sales Return</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="balance_tp">Balance TP</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="payment_percentage_tp">Payment % Tp</label>
                <?php
                if(isset($CI->permissions['add'])&&($CI->permissions['add']==1))
                {

                ?>
                    <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="opening_balance_net"><?php echo $CI->lang->line('LABEL_OPENING_BALANCE'); ?> NET</label>
                    <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="sales_net">Sales NET</label>
                    <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="adjust_net">Adjust NET</label>
                    <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="return_net">Sales Return Net</label>
                    <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="balance_net">Balance Net</label>
                    <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="payment_percentage_net">Payment % Net</label>
                <?php
                }
                ?>
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

        var url = "<?php echo base_url($CI->controller_url.'/get_customer_statement');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'opening_balance_tp', type: 'string' },
                { name: 'opening_balance_net', type: 'string' },
                { name: 'date_po', type: 'string' },
                { name: 'po_no', type: 'string' },
                { name: 'sales_tp', type: 'string' },
                { name: 'sales_net', type: 'string' },
                { name: 'payment_no', type: 'string' },
                { name: 'payment_date', type: 'string' },
                { name: 'payment_amount', type: 'string' },
                { name: 'payment_bank', type: 'string' },
                { name: 'receive_date', type: 'string' },
                { name: 'receive_amount', type: 'string' },
                { name: 'receive_bank', type: 'string' },

                { name: 'adjust_date', type: 'string' },
                { name: 'adjust_tp', type: 'string' },
                { name: 'adjust_net', type: 'string' },
                { name: 'date_return', type: 'string' },
                { name: 'return_po_no', type: 'string' },
                { name: 'return_tp', type: 'string' },
                { name: 'return_net', type: 'string' },
                { name: 'balance_tp', type: 'string' },
                { name: 'balance_net', type: 'string' },
                { name: 'payment_percentage_tp', type: 'string' },
                { name: 'payment_percentage_net', type: 'string' }

            ],
            id: 'id',
            url: url,
            type: 'POST',
            data:{<?php echo $keys; ?>}
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            if (record.opening_balance_tp=="Total")
            {
                element.css({ 'background-color': grand_total_color,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }
            else
            {
                element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','whiteSpace':'normal'});
            }
            if(column=='sl')
            {
                if (record.opening_balance_tp!="Total")
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
            if(record.opening_balance_tp=="Total")
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
                    { columngroup: 'opening_balance',text: 'TP',dataField: 'opening_balance_tp',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    <?php
                        if(isset($CI->permissions['add'])&&($CI->permissions['add']==1))
                        {
                            ?>
                            { columngroup: 'opening_balance',text: 'NET',dataField: 'opening_balance_net',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                            <?php
                        }
                    ?>
                    { columngroup: 'sales',text: 'PO Date',dataField: 'date_po',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { columngroup: 'sales',text: '<?php echo $CI->lang->line('LABEL_PO_NO'); ?>',dataField: 'po_no',width:'80',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center'},
                    { columngroup: 'sales',text: 'Amount',dataField: 'sales_tp',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    <?php
                        if(isset($CI->permissions['add'])&&($CI->permissions['add']==1))
                        {
                            ?>
                            { columngroup: 'sales',text: 'NET Amount',dataField: 'sales_net',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                            <?php
                        }
                    ?>
                    { columngroup: 'payment',text: 'Payment ID',dataField: 'payment_no',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center'},
                    { columngroup: 'payment',text: '<?php echo $CI->lang->line('LABEL_DATE_PAYMENT'); ?>',dataField: 'payment_date',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { columngroup: 'payment',text: 'Payment <?php echo $CI->lang->line('LABEL_AMOUNT'); ?>',dataField: 'payment_amount',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { columngroup: 'payment',text: 'Payment Bank',dataField: 'payment_bank',width:'200',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center'},
                    { columngroup: 'payment',text: 'Receive <?php echo $CI->lang->line('LABEL_DATE'); ?>',dataField: 'receive_date',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { columngroup: 'payment',text: 'Receive <?php echo $CI->lang->line('LABEL_AMOUNT'); ?>',dataField: 'receive_amount',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { columngroup: 'payment',text: 'Receive Bank',dataField: 'receive_bank',width:'200',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center'},
                    { columngroup: 'adjustment',text: 'Adjust Date', dataField: 'adjust_date',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',width:'100'},
                    { columngroup: 'adjustment',text: 'Amount', dataField: 'adjust_tp',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsalign: 'right',width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    <?php
                        if(isset($CI->permissions['add'])&&($CI->permissions['add']==1))
                        {
                            ?>
                            { columngroup: 'adjustment',text: 'NET Amount', dataField: 'adjust_net',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsalign: 'right',width:'100',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                            <?php
                        }
                    ?>
                    { columngroup: 'sales_return',text: 'Return Date',dataField: 'date_return',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { columngroup: 'sales_return',text: '<?php echo $CI->lang->line('LABEL_PO_NO'); ?>',dataField: 'return_po_no',width:'80',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center'},
                    { columngroup: 'sales_return',text: 'Amount',dataField: 'return_tp',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    <?php
                        if(isset($CI->permissions['add'])&&($CI->permissions['add']==1))
                        {
                            ?>
                            { columngroup: 'sales_return',text: 'NET Amount',dataField: 'return_net',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right',aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                            <?php
                        }
                    ?>
                    { columngroup: 'balance',text: 'Amount', dataField: 'balance_tp',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsalign: 'right',width:'100'},
                    <?php
                        if(isset($CI->permissions['add'])&&($CI->permissions['add']==1))
                        {
                            ?>
                            { columngroup: 'balance',text: 'NET Amount', dataField: 'balance_net',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsalign: 'right',width:'100'},
                            <?php
                        }
                    ?>
                    { columngroup: 'payment_percentage',text: 'TP', dataField: 'payment_percentage_tp',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsalign: 'right',width:'50'},
                    <?php
                        if(isset($CI->permissions['add'])&&($CI->permissions['add']==1))
                        {
                            ?>
                            { columngroup: 'payment_percentage',text: 'NET', dataField: 'payment_percentage_net',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsalign: 'right',width:'50'}
                            <?php
                        }
                    ?>
                ],
                columngroups:
                [
                    { text: 'Opening Balance', align: 'center', name: 'opening_balance' },
                    { text: 'Sales', align: 'center', name: 'sales' },
                    { text: 'Payment', align: 'center', name: 'payment' },
                    { text: 'Adjustment', align: 'center', name: 'adjustment' },
                    { text: 'Sales Return', align: 'center', name: 'sales_return' },
                    { text: 'Current Balance', align: 'center', name: 'balance' },
                    { text: 'Payment %', align: 'center', name: 'payment_percentage' }
                ]

            });
    });
</script>