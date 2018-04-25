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
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="outlet_name">Outlet Name</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="po_no"><?php echo $CI->lang->line('LABEL_PO_NO'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date_po"><?php echo $CI->lang->line('LABEL_DATE_PO'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="time_po">PO Entry Time</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="details_button"><?php echo $CI->lang->line('ACTION_DETAILS'); ?></label>
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
                { name: 'po_no', type: 'string' },
                { name: 'date_po', type: 'string' },
                { name: 'outlet_name', type: 'string' },
                { name: 'time_po', type: 'string' }
            ],
            url: url,
            type: 'POST',
            data:{<?php echo $keys; ?>}
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px','whiteSpace':'normal'});
            if(column=='details_button')
            {
                element.html('<div><button class="btn btn-primary pop_up" data-action-link="<?php echo site_url($CI->controller_url.'/index/details'); ?>/'+record.id+'">View Details</button></div>');


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
                rowsheight: 40,
                enablebrowserselection:true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_SL_NO'); ?>',datafield: 'sl_no',pinned:true,width:'50', columntype: 'number',cellsalign: 'right',sortable:false,filterable:false,
                        cellsrenderer: function(row, column, value, defaultHtml, columnSettings, record)
                        {
                            var element = $(defaultHtml);
                            element.html(value+1);
                            return element[0].outerHTML;
                        }
                    },
                    { text: 'Outlet Name',pinned:true, dataField: 'outlet_name',width:'150',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_PO_NO'); ?>',dataField: 'po_no',width:'70',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_PO'); ?>',dataField: 'date_po',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { text: 'PO Entry Time',dataField: 'time_po',width:'200',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,align:'center',cellsAlign:'right'},
                    { text: '<?php echo $CI->lang->line('ACTION_DETAILS'); ?>',dataField: 'details_button',width:'120',cellsrenderer:cellsrenderer}
                ]

            });
    });
</script>