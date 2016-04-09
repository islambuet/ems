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
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="crop_info">Crop Info</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="location">Locations</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="details">Details</label>
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
                { name: 'crop_info', type: 'string' },
                { name: 'location', type: 'string' },
                { name: 'images', type: 'string' },
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
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px'});
            /*if(column=='details')
            {


                if(record.details.length>0)
                {
                    var html='';
                    for(i=0;i<record.details.length;i++)
                    {
                        html+='<div style="height: 125px;width: 133px;margin-right:10px;  float: left;" title="'+record.details[i]['remarks']+'">';
                        html+='<div style="height:100px;"><img src="'+record.details[i]['picture']+'" style="max-height: 100px;max-width: 133px;"></div>';
                        html+='<div style="height: 25px;text-align: center; ">'+record.details[i]['date_remarks']+'</div>';
                        html+='</div>';
                    }
                    element.html(html);
                }
            }*/

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
                rowsheight: 133,
                columns: [
                    { text: 'Crop Info', dataField: 'crop_info',width: '150',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: 'Locations', dataField: 'location',width: '150',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: 'Images', dataField: 'images',cellsrenderer: cellsrenderer,rendered: tooltiprenderer}
                ]
            });
    });
</script>