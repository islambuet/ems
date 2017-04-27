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
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="crop_name"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="crop_type_name"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="variety_name"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="competitor_variety_name"><?php echo $CI->lang->line('LABEL_COMPETITOR_NAME'); ?></label>
                <br>
                <br>


                <?php
                foreach($areas as $area)
                {
                    ?>

                    <label class="checkbox-inline"><input type="checkbox" class="action_all" data-id="<?php echo $area['value'];?>" checked value=""><?php echo $area['text'];?></label>

                    <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column action_<?php echo $area['value'];?>" checked value="total_market_size_<?php echo $area['value'];?>"><?php echo $area['text'].' Total M. Size'; ?></label>
                    <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column action_<?php echo $area['value'];?>" checked value="arm_market_size_<?php echo $area['value'];?>"><?php echo $area['text'].' ARM M. SIze'; ?></label>
                    <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column action_<?php echo $area['value'];?>" checked value="next_sales_target_<?php echo $area['value'];?>"><?php echo $area['text'].' Nxt Yr Sales Target'; ?></label>
                    <br>
                <?php
                }
                ?>

                <br>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="total_size">Total Size</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="total_arm_mrt_size">Total ARM Size</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column" checked value="total_sales_target">Total Sales Target</label>
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

        var url = "<?php echo base_url($CI->controller_url.'/index/get_items_area');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'crop_name', type: 'string' },
                { name: 'crop_type_name', type: 'string' },
                { name: 'variety_name', type: 'string' },
                { name: 'competitor_variety_name', type: 'string' },
                <?php
                    foreach($areas as $area)
                    {
                ?>
                { name: '<?php echo 'total_market_size_'.$area['value'];?>', type: 'string' },
                { name: '<?php echo 'arm_market_size_'.$area['value'];?>', type: 'string' },
                { name: '<?php echo 'next_sales_target_'.$area['value'];?>', type: 'string' },
                <?php
                    }
//                ?>

                { name: 'total_size', type: 'string' },
                { name: 'total_arm_mrt_size', type: 'string' },
                { name: 'total_sales_target', type: 'string' }
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

            if (record.crop_type_name=="Total Crop")
            {
                if(column!='crop_name')
                {
                    element.css({ 'background-color': '#6CAB44','margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                }
            }
            else if (record.crop_name=="Grand Total")
            {

                element.css({ 'background-color': grand_total_color,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});

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
        var aggregates=function (total, column, element, record)
        {
            if(record.crop_name=="Grand Total")
            {
                //console.log(element);
                return record[element];
            }
            return total;
            //return grand_starting_stock;
        };
        var aggregatesrenderer=function (aggregates)
        {
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+grand_total_color+';">' +aggregates['total']+'</div>';
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
                showaggregates: true,
                showstatusbar: true,
                rowsheight: 35,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',pinned:true,align:'center',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'crop_type_name',pinned:true,align:'center',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',pinned:true,align:'center',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_COMPETITOR_NAME'); ?>', dataField: 'competitor_variety_name',pinned:true,align:'center',cellsalign: 'right',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    <?php
                    foreach($areas as $area)
                        {
                    ?>
                    { columngroup: '<?php echo $area['text']; ?>',text: 'Mrk Size', dataField: '<?php echo 'total_market_size_'.$area['value'];?>',align:'center',cellsalign: 'right',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { columngroup: '<?php echo $area['text']; ?>',text: 'ARM Mrk Size', dataField: '<?php echo 'arm_market_size_'.$area['value'];?>',align:'center',cellsalign: 'right',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { columngroup: '<?php echo $area['text']; ?>',text: 'Nxt Yr Sales trg', dataField: '<?php echo 'next_sales_target_'.$area['value'];?>',align:'center',cellsalign: 'right',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    <?php
                        }
                    ?>
                    { text: 'T. M. Size', dataField: 'total_size',align:'center',cellsalign: 'right',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'T. ARM Size', dataField: 'total_arm_mrt_size',align:'center',cellsalign: 'right',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { text: 'T. Sales Trg', dataField: 'total_sales_target',align:'center',cellsalign: 'right',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer}
                ],
                columngroups:
                    [
                            <?php
                                foreach($areas as $area)
                                {?>{ text: '<?php echo $area['text']; ?>', align: 'center', name: '<?php echo $area['text']; ?>' },
                        <?php
                            }
                        ?>
                    ]


            });
    });
</script>


<script type="text/javascript">

    jQuery(document).ready(function()
    {
        //trigger off korte hobe used class gulate

        $(document).off("click", ".action_all");
        $(document).on("click",'.action_all',function()
        {
            $('.action_'+$(this).attr('data-id')).trigger('click');
        });
    });

</script>
