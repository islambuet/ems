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
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="po_no"><?php echo $CI->lang->line('LABEL_PO_NO'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date_po"><?php echo $CI->lang->line('LABEL_DATE_PO'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date_approved">Approved Date</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date_delivery">Delivered Date</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="quantity_total"><?php echo $CI->lang->line('LABEL_QUANTITY_PIECES'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="quantity_weight"><?php echo $CI->lang->line('LABEL_WEIGHT_KG'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="invoice_no"><?php echo $CI->lang->line('LABEL_INVOICE_NO'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="courier_name"><?php echo $CI->lang->line('LABEL_NAME_COURIER'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date_booking"><?php echo $CI->lang->line('LABEL_DATE_BOOKING'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="track_no"><?php echo $CI->lang->line('LABEL_COURIER_TRACK_NO'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="remarks"><?php echo $CI->lang->line('LABEL_REMARKS'); ?></label>
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
                { name: 'po_no', type: 'string' },
                { name: 'date_po', type: 'string' },
                { name: 'date_approved', type: 'string' },
                { name: 'date_delivery', type: 'string' },
                { name: 'quantity_total', type: 'string' },
                { name: 'quantity_weight', type: 'string' },
                { name: 'invoice_no', type: 'string' },
                { name: 'courier_name', type: 'string' },
                { name: 'date_booking', type: 'string' },
                { name: 'track_no', type: 'string' },
                { name: 'remarks', type: 'string' }

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
                    { text: '<?php echo $CI->lang->line('LABEL_SL_NO'); ?>',datafield: 'sl',pinned:true,width:'30', columntype: 'number',cellsalign: 'right',sortable:false,filterable:false,cellsrenderer: cellsrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>',pinned:true, dataField: 'division_name',width:'80',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>',pinned:true, dataField: 'zone_name',width:'80',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>',pinned:true, dataField: 'territory_name',width:'100',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>',pinned:true, dataField: 'district_name',width:'100',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_CUSTOMER_NAME'); ?>',pinned:true, dataField: 'customer_name',width:'150',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_PO_NO'); ?>',dataField: 'po_no',width:'70',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_PO'); ?>',dataField: 'date_po',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { text: 'Approved Date',dataField: 'date_approved',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { text: 'Delivered Date',dataField: 'date_delivery',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_PIECES'); ?>', dataField: 'quantity_total',width:'60',cellsrenderer: cellsrenderer,cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_WEIGHT_KG'); ?>', dataField: 'quantity_weight',width:'80',cellsrenderer: cellsrenderer,cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_INVOICE_NO'); ?>',dataField: 'invoice_no',width:'150',cellsrenderer: cellsrenderer,rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_NAME_COURIER'); ?>',dataField: 'courier_name',width:'150',cellsrenderer: cellsrenderer,rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_BOOKING'); ?>',dataField: 'date_booking',width:'100',cellsrenderer: cellsrenderer,rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_COURIER_TRACK_NO'); ?>',dataField: 'track_no',width:'150',cellsrenderer: cellsrenderer,rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_REMARKS'); ?>',dataField: 'remarks',width:'100',cellsrenderer: cellsrenderer,rendered:tooltiprenderer}

                ]

            });
    });
</script>