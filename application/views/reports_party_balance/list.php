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
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="areas"><?php echo $areas; ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="opening_balance_tp"><?php echo $CI->lang->line('LABEL_OPENING_BALANCE'); ?> TP</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="opening_balance_net"><?php echo $CI->lang->line('LABEL_OPENING_BALANCE'); ?> NET</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="sales_tp">Sales TP</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="sales_net">Sales NET</label>


                <?php
                foreach($arm_banks as $arm_bank)
                {?>
                    <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="<?php echo 'payment_'.$arm_bank['value']; ?>"><?php echo $arm_bank['text']; ?></label>
                <?php
                }
                ?>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="total_payment">Total Payment</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="adjust_tp">Adjust TP</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="adjust_net">Adjust NET</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="balance_tp">Balance TP</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="balance_net">Balance Net</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="payment_percentage_tp">Payment % Tp</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="payment_percentage_net">Payment % Net</label>
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
                { name: 'areas', type: 'string' },
                { name: 'opening_balance_tp', type: 'string' },
                { name: 'opening_balance_net', type: 'string' },
                { name: 'sales_tp', type: 'string' },
                { name: 'sales_net', type: 'string' },
                <?php
                    foreach($arm_banks as $arm_bank)
                    {?>{ name: '<?php echo 'payment_'.$arm_bank['value'];?>', type: 'string' },
                        <?php
                    }
                ?>
                { name: 'total_payment', type: 'string' },
                { name: 'adjust_tp', type: 'string' },
                { name: 'adjust_net', type: 'string' },
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
            if (record.areas=="Total")
            {
                element.css({ 'background-color': grand_total_color,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
            }
            else
            {
                element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','whiteSpace':'normal'});
            }
            if(column=='sl')
            {
                if (record.areas!="Total")
                {
                    element.html(value+1);
                }

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
                height:'350px',
                source: dataAdapter,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                enabletooltips: true,
                rowsheight: 35,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_SL_NO'); ?>',datafield: 'sl',pinned:true,width:'80', columntype: 'number',cellsalign: 'right',sortable:false,filterable:false,cellsrenderer: cellsrenderer},
                    { text: '<?php echo $areas; ?>',pinned:true ,dataField: 'areas',pinned:true,width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { columngroup: 'opening_balance',text: 'TP',dataField: 'opening_balance_tp',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { columngroup: 'opening_balance',text: 'NET',dataField: 'opening_balance_net',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { columngroup: 'sales',text: 'TP',dataField: 'sales_tp',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { columngroup: 'sales',text: 'NET',dataField: 'sales_net',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    <?php
                        foreach($arm_banks as $arm_bank)
                        {?>{ columngroup: 'arm_bank_account',text: '<?php echo $arm_bank['text'];?>', dataField: '<?php echo 'payment_'.$arm_bank['value'];?>',align:'center',cellsalign: 'right',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                            <?php
                        }
                    ?>
                    { text: 'Total Payment', dataField: 'total_payment',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsalign: 'right',width:'150'},
                    { columngroup: 'adjustment',text: 'TP', dataField: 'adjust_tp',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsalign: 'right',width:'150'},
                    { columngroup: 'adjustment',text: 'NET', dataField: 'adjust_net',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsalign: 'right',width:'150'},
                    { columngroup: 'balance',text: 'TP', dataField: 'balance_tp',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsalign: 'right',width:'150'},
                    { columngroup: 'balance',text: 'NET', dataField: 'balance_net',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsalign: 'right',width:'150'},
                    { columngroup: 'payment_percentage',text: 'TP', dataField: 'payment_percentage_tp',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsalign: 'right',width:'50'},
                    { columngroup: 'payment_percentage',text: 'NET', dataField: 'payment_percentage_net',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsalign: 'right',width:'50'}

                ],
                columngroups:
                [
                    { text: 'Opening Balance', align: 'center', name: 'opening_balance' },
                    { text: 'Sales', align: 'center', name: 'sales' },
                    { text: 'Adjustment', align: 'center', name: 'adjustment' },
                    { text: 'Current Balance', align: 'center', name: 'balance' },
                    { text: 'ARM Bank Account', align: 'center', name: 'arm_bank_account' },
                    { text: 'Payment %', align: 'center', name: 'payment_percentage' }
                ]

            });
    });
</script>