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
            <br>
            <?php echo '(No. of Field Days : '.$number.')';?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    if(isset($CI->permissions['column_headers'])&&($CI->permissions['column_headers']==1))
    {
        ?>
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date_of_fd">Date of Field Day</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="crop_info">Crop Info</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="location_info">Location Info</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="total_participant">Total Participant</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="total_expense">Total Expense</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="sales_target">Next Year Sales Target</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="recommendation">Recommendation</label>
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
        $(document).off("click", ".pop_up");

        $(document).on("click", ".pop_up", function(event)
        {
            var left=((($(window).width()-550)/2)+$(window).scrollLeft());
            var top=((($(window).height()-550)/2)+$(window).scrollTop());
            $("#popup_window").jqxWindow({width: 1200,height:550,position:{x:left,y:top}}); //to change position always
            //$("#popup_window").jqxWindow({position:{x:left,y:top}});
            var row=$(this).attr('data-item-no');
            var id=$("#system_jqx_container").jqxGrid('getrowdata',row).id;
            $.ajax(
                {
                    url: "<?php echo site_url($CI->controller_url.'/index/details') ?>",
                    type: 'POST',
                    datatype: "JSON",
                    data:
                    {
                        html_container_id:'#popup_content',
                        id:id
                    },
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");
                    }
                });
            $("#popup_window").jqxWindow('open');
        });
        //var grand_total_color='#AEC2DD';
        var grand_total_color='#AEC2DD';

        var url = "<?php echo base_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'date_of_fd', type: 'string' },
                { name: 'crop_info', type: 'string' },
                { name: 'location_info', type: 'string' },
                { name: 'total_participant', type: 'string' },
                { name: 'total_expense', type: 'string' },
                { name: 'sales_target', type: 'string' },
                { name: 'recommendation', type: 'string' },
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
            if(column=='details_button')
            {
                element.html('<div><button class="btn btn-primary pop_up" data-item-no="'+row+'">Details</button></div>');
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
                height:'480px',
                source: dataAdapter,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                rowsheight: 110,
                columns: [
                    { text: 'Date of Field Day', dataField: 'date_of_fd',width: '125',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: 'Crop Info', dataField: 'crop_info',width: '140',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: 'Location Info', dataField: 'location_info',width: '150',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: 'No. of Participant', dataField: 'total_participant',width: '134',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Total Expense', dataField: 'total_expense',width: '120',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Next Year Sales Target', dataField: 'sales_target',width: '163',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Recommendation', dataField: 'recommendation',width: '330',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Details', dataField: 'details_button',width: '105',cellsrenderer: cellsrenderer,rendered: tooltiprenderer}
                ]
            });
    });
</script>