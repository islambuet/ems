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
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="opening_balance"><?php echo $CI->lang->line('LABEL_OPENING_BALANCE'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="sales">Sales</label>

                <?php
                foreach($arm_banks as $arm_bank)
                {?>
                    <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="<?php echo 'payment_'.$arm_bank['value']; ?>"><?php echo $arm_bank['text']; ?></label>
                <?php
                }
                ?>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="total_payment">Total Payment</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="balance">Balance</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="percentage">Payment Percentage</label>
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
                { name: 'sl_no', type: 'int' },
                { name: 'areas', type: 'string' },
                { name: 'opening_balance', type: 'string' },
                { name: 'sales', type: 'string' },
                <?php
                    foreach($arm_banks as $arm_bank)
                    {?>{ name: '<?php echo 'payment_'.$arm_bank['value'];?>', type: 'string' },
                        <?php
                    }
                ?>
                { name: 'total_payment', type: 'string' },
                { name: 'balance', type: 'string' },
                { name: 'percentage', type: 'string' }

            ],
            id: 'id',
            url: url,
            type: 'POST',
            data:{<?php echo $keys; ?>}
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','whiteSpace':'normal'});
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
                    { text: '<?php echo $CI->lang->line('LABEL_SL_NO'); ?>', dataField: 'sl_no',pinned:true,width:'40',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: '<?php echo $areas; ?>',pinned:true ,dataField: 'date_visit',pinned:true,width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_OPENING_BALANCE'); ?>',dataField: 'opening_balance',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { text: 'Sales',dataField: 'sales',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    <?php
                        foreach($arm_banks as $arm_bank)
                        {?>{ columngroup: 'arm_bank_account',text: '<?php echo $arm_bank['text'];?>', dataField: '<?php echo 'payment_'.$arm_bank['value'];?>',align:'center',cellsalign: 'right',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                            <?php
                        }
                    ?>
                    { text: 'Total Payment', dataField: 'total_payment',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsalign: 'right',width:'150'},
                    { text: 'Balance', dataField: 'balance',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsalign: 'right',width:'150'},
                    { text: 'Payment Percentage', dataField: 'percentage',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsalign: 'right',width:'150'}
                ],
                columngroups:
                [
                    { text: 'ARM Bank Account', align: 'center', name: 'arm_bank_account' }
                ]

            });
    });
</script>