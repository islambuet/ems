<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();

$action_buttons=array();
if(isset($CI->permissions['add'])&&($CI->permissions['add']==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line("ACTION_NEW"),
        'href'=>site_url($CI->controller_url.'/index/add')
    );
}
if(isset($CI->permissions['edit'])&&($CI->permissions['edit']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_EDIT"),
        'class'=>'button_action_batch',
        'id'=>'button_action_edit',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit')
    );
}
if(isset($CI->permissions['edit'])&&($CI->permissions['edit']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_REPORTING"),
        'class'=>'button_action_batch',
        'id'=>'button_action_edit',
        'data-action-link'=>site_url($CI->controller_url.'/index/reporting')
    );
}
if(isset($CI->permissions['edit'])&&($CI->permissions['edit']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DETAILS"),
        'class'=>'button_action_batch',
        'id'=>'button_action_edit',
        'data-action-link'=>site_url($CI->controller_url.'/index/details')
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
                { name: 'date_started', type: 'string' },
                { name: 'remarks_started', type: 'string' },
                { name: 'date_reported', type: 'string' },
                { name: 'remarks_reported', type: 'string' },
                { name: 'zsc_comment', type: 'string' },
                { name: 'attendance', type: 'string' }
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
                    { text: '<?php echo $CI->lang->line('LABEL_STARTING_TIME'); ?>',dataField: 'date_started',filtertype: 'list',width:'200',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_REMARKS_STARTING'); ?>',dataField: 'remarks_started',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_REPORTING_TIME'); ?>',dataField: 'date_reported',filtertype: 'list',width:'200',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_REMARKS_REPORTING'); ?>', dataField: 'remarks_reported',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_ZSC_COMMENT'); ?>', dataField: 'zsc_comment',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_ATTENDANCE_STATUS'); ?>', dataField: 'attendance',filtertype: 'list',width:'100',rendered:tooltiprenderer}
                ]
            });
    });
</script>