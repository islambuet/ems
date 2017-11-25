<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
if(isset($CI->permissions['add'])&&($CI->permissions['add']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_REPORTING"),
        'class'=>'button_action_batch',
        'id'=>'button_action_edit',
        'data-action-link'=>site_url($CI->controller_url.'/index/reporting')
    );
}
if(isset($CI->permissions['print'])&&($CI->permissions['print']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'id'=>'button_action_print',
        'data-title'=>'PO LIST'
    );
}
if(isset($CI->permissions['download'])&&($CI->permissions['download']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'id'=>'button_action_csv',
        'data-title'=>'PO LIST'
    );
}

$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list')

);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
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
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  value="id"><?php echo $CI->lang->line('ID'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  value="employee_name"><?php echo $CI->lang->line('LABEL_EMPLOYEE_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="area_name"><?php echo $CI->lang->line('LABEL_AREA_NAME'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="area_address"><?php echo $CI->lang->line('LABEL_AREA_ADDRESS'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="arm_variety"><?php echo $CI->lang->line('LABEL_ARM_VARIETY'); ?></label>
            <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="competitor_variety"><?php echo $CI->lang->line('LABEL_COMPETITOR_VARIETY'); ?></label>
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
        var url = "<?php echo base_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'employee_name', type: 'string' },
                { name: 'area_name', type: 'string' },
                { name: 'area_address', type: 'string' },
                { name: 'arm_variety', type: 'string' },
                { name: 'competitor_variety', type: 'string' }
            ],
            id: 'id',
            type: 'POST',
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
                pagesize:20,
                pagesizeoptions: ['20', '50', '100', '200','300','500'],
                selectionmode: 'singlerow',
                altrows: true,
                //rowsheight: 80,
                autorowheight: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('ID'); ?>',dataField: 'id',width:'75',rendered:tooltiprenderer,hidden:true},
                    { text: '<?php echo $CI->lang->line('LABEL_EMPLOYEE_NAME'); ?>',dataField: 'employee_name',filtertype: 'list',width:'200',rendered:tooltiprenderer,hidden:true},
                    { text: '<?php echo $CI->lang->line('LABEL_AREA_NAME'); ?>',dataField: 'area_name',width:'200',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_AREA_ADDRESS'); ?>',dataField: 'area_address',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_ARM_VARIETY'); ?>',dataField: 'arm_variety',width:'250',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_COMPETITOR_VARIETY'); ?>',dataField: 'competitor_variety',width:'250',rendered:tooltiprenderer}
                ]
            });
    });
</script>