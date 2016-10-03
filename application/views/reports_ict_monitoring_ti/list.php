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
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date_reporting">Reporting Date</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="division_name"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="zone_name"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="territory_name"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="customer_visit">Customer Visit</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="demonstration">Demonstration</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="reporting">Reporting</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="others">Others</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="recommendation">Recommendation</label>
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
            html+='<div style="line-height: 1.8;">';
            html+='<div><b> Reporting Date:</b> '+row_info['date_reporting']+'<div>';
            html+='<div><b>Division Name:</b> '+row_info['division_name']+'<div>';
            html+='<div><b>Zone Name:</b> '+row_info['zone_name']+'<div>';
            html+='<div><b>Territory Name:</b> '+row_info['territory_name']+'<div>';

            html+='<div><b>Customer Visit:</b><div>';
            html+='<div>'+row_info['customer_visit']+'<div>';
            html+='<div><b>Demonstration:</b><div>';
            html+='<div>'+row_info['demonstration']+'<div>';
            html+='<div><b>Reporting:</b><div>';
            html+='<div>'+row_info['reporting']+'<div>';
            html+='<div><b>Others:</b><div>';
            html+='<div>'+row_info['others']+'<div>';

            html+='<div><b>Recommendation :</b><div>';
            html+='<div>'+row_info['recommendation']+'<div>';
            html+='</div>';
            //console.log(row_info.details)
            $('#popup_content').html(html);
            $("#popup_window").jqxWindow('open');


        });
        var grand_total_color='#AEC2DD';

        var url = "<?php echo base_url($CI->controller_url.'/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'date_reporting', type: 'string' },
                { name: 'division_name', type: 'string' },
                { name: 'zone_name', type: 'string' },
                { name: 'territory_name', type: 'string' },
                { name: 'customer_visit', type: 'string' },
                { name: 'demonstration', type: 'string' },
                { name: 'reporting', type: 'string' },
                { name: 'others', type: 'string' },
                { name: 'recommendation', type: 'string' }

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
            if(column=='sl')
            {
                element.html(value+1);

            }
            else if(column=='details_button')
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
                enabletooltips: true,
                rowsheight: 40,
                columns: [
                    {
                        text: '<?php echo $CI->lang->line('LABEL_SL_NO'); ?>',datafield: '',pinned:true,width:'50', columntype: 'number',cellsalign: 'right',sortable:false,filterable:false,
                        cellsrenderer: function(row, column, value, defaultHtml, columnSettings, record)
                        {
                            var element = $(defaultHtml);
                            element.html(value+1);
                            return element[0].outerHTML;
                        }
                    },
                    { text: 'Reporting Date',pinned:true, dataField: 'date_reporting',pinned:true,width:'100',cellsrenderer: cellsrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>',pinned:true,width:'100', dataField: 'division_name',filtertype: 'list',cellsrenderer: cellsrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>',pinned:true,width:'100', dataField: 'zone_name',cellsrenderer: cellsrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>',pinned:true,width:'100', dataField: 'territory_name',cellsrenderer: cellsrenderer},
                    { text: 'Customer Visit', dataField: 'customer_visit',width:'150',cellsrenderer: cellsrenderer},
                    { text: 'Demonstration', dataField: 'demonstration',width:'150',cellsrenderer: cellsrenderer},
                    { text: 'Reporting', dataField: 'reporting',width:'150',cellsrenderer: cellsrenderer},
                    { text: 'Others', dataField: 'others',width:'150',cellsrenderer: cellsrenderer},
                    { text: 'Recommendation', dataField: 'recommendation',width:'150',cellsrenderer: cellsrenderer},
                    { text: 'Details', dataField: 'details_button',width: '100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer}
                ]

            });
    });
</script>