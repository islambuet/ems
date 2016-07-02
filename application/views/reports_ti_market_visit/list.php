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
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="customer_name"><?php echo $CI->lang->line('LABEL_CUSTOMER_NAME').'/'.$CI->lang->line('LABEL_TITLE'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="activities">Activities</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="activities_picture">Activities Picture</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="problem">Problem</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="problem_picture">Problem Picture</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="recommendation">Recommendation</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="solution">Solution</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="details_button">Details</label>
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
            html+='<div><b>Date:</b> '+row_info.details['date']+'<div>';
            html+='<div><b>Day:</b> '+row_info.details['day']+'<div>';
            html+='<div><b>Division Name:</b> '+row_info.details['division_name']+'<div>';
            html+='<div><b>Zone Name:</b> '+row_info.details['zone_name']+'<div>';
            html+='<div><b>Territory Name:</b> '+row_info.details['territory_name']+'<div>';
            html+='<div><b>District Name:</b> '+row_info.details['district_name']+'<div>';
            html+='<div><b>Shift:</b> '+row_info['shift_name']+'<div>';
            html+='<div><b>Customer/Title:</b> '+row_info['customer_name']+'<div>';
            html+='<div><b>Activities:</b><div>';
            html+='<div>'+row_info['activities']+'<div>';
            html+='<div><b>Activities Picture:</b> <div>';
            html+='<div><img src="'+row_info.details['activities_picture']+'" style="max-width: 100%;"></div>';
            html+='<div><b>Problem:</b><div>';
            html+='<div>'+row_info['problem']+'<div>';
            html+='<div><b>Problem Picture:</b> <div>';
            html+='<div><img src="'+row_info.details['problem_picture']+'" style="max-width: 100%;"></div>';
            html+='<div><b>Recommendation :</b><div>';
            html+='<div>'+row_info['recommendation']+'<div>';
            html+='<div><b>Recommendation By:</b> '+row_info.details['user_created']+'<div>';
            html+='<div><b>Recommendation Time:</b> '+row_info.details['time_created']+'<div>';
            html+='<div><b>Solutions:</b><div>';
            if(row_info.details['solutions'].length>0)
            {
                $.each( row_info.details['solutions'], function( key, solution )
                {
                    html+='<div><b>'+solution['created_user']+' at '+solution['created_time']+':</b> '+solution['solution']+'<div>';

                });

            }
            else
            {
                html+='<div>No Solution Given Yet<div>';
            }
            html+='</div>';
            //console.log(row_info.details)
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
                rowsheight: 110,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_SL_NO'); ?>',datafield: 'sl',pinned:true,width:'40', columntype: 'number',cellsalign: 'right',sortable:false,filterable:false,cellsrenderer: cellsrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE'); ?>', dataField: 'date_visit',pinned:true,width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Locations', dataField: 'location',pinned:true,width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_SHIFT'); ?>',pinned:true,width:'80', dataField: 'shift_name',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_CUSTOMER_NAME').'/'.$CI->lang->line('LABEL_TITLE'); ?>',pinned:true,width:'150', dataField: 'customer_name',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Activities', dataField: 'activities',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    <?php
                        if($activities_picture)
                        {
                            ?>
                    { text: 'Activities Picture', dataField: 'activities_picture',width:'143',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                            <?php
                        }
                    ?>
                    { text: 'Problem', dataField: 'problem',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    <?php
                        if($problem_picture)
                        {
                            ?>
                    { text: 'Problem Picture', dataField: 'problem_picture',width:'143',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    <?php
                        }
                    ?>

                    { text: 'Recommendation', dataField: 'recommendation',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Solution', dataField: 'solution',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Details', dataField: 'details_button',width: '100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer}
                ]

            });
    });
</script>