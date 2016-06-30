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
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="characteristics">Characteristics</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="cultivation_period">Cultivation Period</label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="picture">Picture</label>
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

            var left=((($(window).width() - 550) / 2) +$(window).scrollLeft());
            var top=((($(window).height() - 550) / 2) +$(window).scrollTop());

            //$("#popup_window").jqxWindow({width: 630,height:550,position: { x: 60, y: 60  }});to change position always
            $("#popup_window").jqxWindow({position: { x: left, y: top  }});
            var row=$(this).attr('data-item-no');
            var row_info = $("#system_jqx_container").jqxGrid('getrowdata', row);
            var html='';
            html+='<html>';
            html+='<div><b>Crop Name:</b> '+row_info.details['crop_name']+'<div>';
            html+='<div><b>Crop Type:</b> '+row_info.details['crop_type_name']+'<div>';
            html+='<div><b>Variety Name:</b> '+row_info.details['variety_name']+'<div>';
            html+='<div><b>Characteristics:</b> '+row_info.details['characteristics']+'<div>';
            html+='<div><b>Cultivation Period:</b> '+row_info.details['cultivation_period']+'<div>';
            html+='<div><b>Picture:</b> <div>';
            html+='<div><img src="'+row_info.details['picture']+'" style="max-width: 100%;"></div>';
            html+='<div><b>Remarks:</b> '+row_info.details['remarks']+'<div>';
            html+='</html>';
            $('#popup_content').html(html);
            $("#popup_window").jqxWindow('open');


        });
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
                { name: 'characteristics', type: 'string' },
                { name: 'picture', type: 'string' },
                { name: 'cultivation_period', type: 'string' },
                { name: 'remarks', type: 'string' },
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
                height:'350px',
                source: dataAdapter,
                columnsresize: true,
                columnsreorder: true,
                altrows: true,
                rowsheight: 133,
                columns: [
                    { text: 'Crop Info', dataField: 'crop_info',width: '150',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: 'characteristics', dataField: 'characteristics',width: '250',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Cultivation Period', dataField: 'cultivation_period',width: '250',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Picture', dataField: 'picture',width: '250',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Remarks', dataField: 'remarks',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Details', dataField: 'details_button',width: '100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer}
                ]
            });
    });
</script>