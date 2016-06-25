<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    if(isset($CI->permissions['add'])&&($CI->permissions['add']==1))
    {
        $action_data["action_new"]=base_url($CI->controller_url."/index/search");
    }
    if(isset($CI->permissions['edit'])&&($CI->permissions['edit']==1))
    {
        $action_data["action_edit"]=base_url($CI->controller_url."/index/edit");
    }
    if(isset($CI->permissions['view'])&&($CI->permissions['view']==1))
    {
        $action_data["action_details"]=base_url($CI->controller_url."/index/details");
    }
    $action_data["action_refresh"]=base_url($CI->controller_url."/index/list");
    $CI->load->view("action_buttons",$action_data);
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
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date"><?php echo $CI->lang->line('LABEL_DATE'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="day"><?php echo $CI->lang->line('LABEL_DAY'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="shift_name"><?php echo $CI->lang->line('LABEL_SHIFT'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="division_name"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="zone_name"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="territory_name"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="district_name"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="customer_name"><?php echo $CI->lang->line('LABEL_CUSTOMER_NAME'); ?></label>
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
        turn_off_triggers();
        var url = "<?php echo base_url($CI->controller_url.'/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'date', type: 'string' },
                { name: 'day', type: 'string' },
                { name: 'shift_name', type: 'string' },
                { name: 'division_name', type: 'string' },
                { name: 'zone_name', type: 'string' },
                { name: 'territory_name', type: 'string' },
                { name: 'district_name', type: 'string' },
                { name: 'customer_name', type: 'string' }

            ],
            id: 'id',
            url: url
        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                source: dataAdapter,
                pageable: true,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                pagesize:50,
                pagesizeoptions: ['20', '50', '100', '200','300','500'],
                selectionmode: 'checkbox',
                altrows: true,
                autoheight: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_DATE'); ?>',pinned:true, dataField: 'date',width:'100',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_DAY'); ?>',pinned:true, dataField: 'day',width:'100',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_SHIFT'); ?>',pinned:true, dataField: 'shift_name',width:'100',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>', dataField: 'division_name',width:'100',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>', dataField: 'zone_name',width:'100',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>', dataField: 'territory_name',width:'100',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>', dataField: 'district_name',width:'100',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_CUSTOMER_NAME'); ?>', dataField: 'customer_name',rendered:tooltiprenderer}
                ]
            });
    });
</script>