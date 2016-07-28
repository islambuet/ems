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
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="arm_variety_name">ARM <?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="arm_weight">ARM Market Size</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="competitor_variety_name">Competitor <?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="competitor_weight">Competitor Market Size</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="op_weight">OP Market Size</label>
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
                { name: 'crop_name', type: 'string' },
                { name: 'crop_type_name', type: 'string' },
                { name: 'arm_variety_name', type: 'string' },
                { name: 'arm_weight', type: 'string' },
                { name: 'competitor_variety_name', type: 'string' },
                { name: 'competitor_weight', type: 'string' },
                { name: 'op_weight', type: 'string' }

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

            if (record.crop_type_name=="Total")
            {
                element.css({ 'background-color': grand_total_color,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});

            }
            else if (record.crop_type_name=="Percentage")
            {
                element.css({ 'background-color': '#FEE3B4','margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
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
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',width: '100',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'crop_type_name',width: '100',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { columngroup: 'arm',text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'arm_variety_name',align:'center',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { columngroup: 'arm',text: 'Market Size', dataField: 'arm_weight',align:'center',cellsalign: 'right',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { columngroup: 'competitor',text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'competitor_variety_name',align:'center',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { columngroup: 'competitor',text: 'Market Size', dataField: 'competitor_weight',align:'center',cellsalign: 'right',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Others variety', dataField: 'op_weight',align:'center',cellsalign: 'right',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer}
                ],
                columngroups:
                    [
                        { text: 'ARM', align: 'center', name: 'arm' },
                        { text: 'Competitor', align: 'center', name: 'competitor' }
                    ]
            });
    });
</script>