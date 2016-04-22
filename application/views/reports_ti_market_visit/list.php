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
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date_visit"><?php echo $CI->lang->line('LABEL_DATE'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="location">Locations</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="shift_name"><?php echo $CI->lang->line('LABEL_SHIFT'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="customer_name"><?php echo $CI->lang->line('LABEL_CUSTOMER_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="activities">Activities</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="activities_picture">Activities Picture</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="problem">Problem</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="problem_picture">Problem Picture</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="recommendation">Recommendation</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="solution">Solution</label>
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
                { name: 'date_visit', type: 'string' },
                { name: 'location', type: 'string' },
                { name: 'shift_name', type: 'string' },
                { name: 'customer_name', type: 'string' },
                { name: 'activities', type: 'string' },
                { name: 'activities_picture', type: 'string' },
                { name: 'problem', type: 'string' },
                { name: 'problem_picture', type: 'string' },
                { name: 'recommendation', type: 'string' },
                { name: 'solution', type: 'string' },
                { name: 'details', type: 'string' }
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
                rowsheight: 110,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_SL_NO'); ?>', dataField: 'sl_no',pinned:true,width:'40',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE'); ?>', dataField: 'date_visit',pinned:true,width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Locations', dataField: 'location',pinned:true,width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_SHIFT'); ?>',pinned:true,width:'150', dataField: 'shift_name',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_CUSTOMER_NAME'); ?>',pinned:true,width:'150', dataField: 'customer_name',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Activities', dataField: 'activities',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Activities Picture', dataField: 'activities_picture',width:'143',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Problem', dataField: 'problem',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Problem Picture', dataField: 'problem_picture',width:'143',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Recommendation', dataField: 'recommendation',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Solution', dataField: 'solution',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer}
                ]

            });
    });
</script>