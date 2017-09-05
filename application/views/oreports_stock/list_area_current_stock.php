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
    if(isset($CI->permissions['column_headers']) && ($CI->permissions['column_headers']==1))
    {

        ?>
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="crop_name"><?php echo $CI->lang->line('LABEL_CROP_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="type_name"><?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="variety_name"><?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="pack_size"><?php echo $CI->lang->line('LABEL_PACK_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  value="current_unit_price">Current unit Price</label>
            <?php
            foreach($areas as $area)
            {
                ?>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  value="current_stock_pkt_<?php echo $area['value'];?>"><?php echo $area['text'].' CS(pkt)'; ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="current_stock_kg_<?php echo $area['value'];?>"><?php echo $area['text'].' CS(kg)'; ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="current_stock_price_<?php echo $area['value'];?>"><?php echo $area['text'].' CS Price'; ?></label>
            <?php
            }
            ?>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="total_price">Total Stock Price</label>
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

        var url = "<?php echo base_url($CI->controller_url.'/index/get_items_area_current');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'crop_name', type: 'string' },
                { name: 'type_name', type: 'string' },
                { name: 'variety_name', type: 'string' },
                { name: 'pack_size', type: 'string' },
                { name: 'current_unit_price', type: 'string' },
                <?php
                    foreach($areas as $area)
                    {?>{ name: '<?php echo 'current_stock_pkt_'.$area['value'];?>', type: 'string' },
                { name: '<?php echo 'current_stock_kg_'.$area['value'];?>', type: 'string' },
                { name: '<?php echo 'current_stock_price_'.$area['value'];?>', type: 'string' },
                <?php
                    }
                ?>
                { name: 'total_price', type: 'string' }
            ],
            id: 'id',
            url: url,
            type: 'POST',
            data:JSON.parse('<?php echo json_encode($options);?>')
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
           // console.log(defaultHtml);

            if (record.variety_name=="Total Type")
            {
                if(!((column=='crop_name')||(column=='type_name')))
                {
                    element.css({ 'background-color': '#6CAB44','margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
                }
            }
            else if (record.type_name=="Total Crop")
            {


                if((column!='crop_name'))
                {
                    element.css({ 'background-color': '#0CA2C5','margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});

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
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_NAME'); ?>', dataField: 'crop_name',width: '100',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_CROP_TYPE'); ?>', dataField: 'type_name',width: '100',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_VARIETY_NAME'); ?>', dataField: 'variety_name',width: '100',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: 'Pack Size(gm)', dataField: 'pack_size',cellsalign: 'right',width: '100',cellsrenderer: cellsrenderer,pinned:true,rendered: tooltiprenderer},
                    { text: 'Current unit Price',hidden:true, dataField: 'current_unit_price',width:'100',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                        <?php
                            foreach($areas as $area)
                            {?>{ columngroup: '<?php echo $area['text']; ?>',text: 'CS(pkt)',hidden:true, dataField: '<?php echo 'current_stock_pkt_'.$area['value'];?>',align:'center',cellsalign: 'right',width:'100',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { columngroup: '<?php echo $area['text']; ?>',text: 'CS(kg)', dataField: '<?php echo 'current_stock_kg_'.$area['value'];?>',align:'center',cellsalign: 'right',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    { columngroup: '<?php echo $area['text']; ?>',text: 'CS Price', dataField: '<?php echo 'current_stock_price_'.$area['value'];?>',align:'center',cellsalign: 'right',width:'150',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer},
                    <?php
                        }
                    ?>
                    { text: 'Total Stock Price', dataField: 'total_price',width:'150',cellsalign: 'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer,aggregates: [{ 'total':aggregates}],aggregatesrenderer:aggregatesrenderer}
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