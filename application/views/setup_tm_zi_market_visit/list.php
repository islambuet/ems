<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();

    $action_data=array();
    if(isset($CI->permissions['add'])&&($CI->permissions['add']==1))
    {
        $action_data["action_new"]=base_url($CI->controller_url."/index/add");
    }
    if((isset($CI->permissions['add'])&&($CI->permissions['add']==1))||(isset($CI->permissions['edit'])&&($CI->permissions['edit']==1)))
    {
        $action_data["action_edit"]=base_url($CI->controller_url."/index/edit");
    }
    if(isset($CI->permissions['view'])&&($CI->permissions['view']==1))
    {
        $action_data["action_details"]=base_url($CI->controller_url."/index/details");
    }
    if(isset($CI->permissions['print'])&&($CI->permissions['print']==1))
    {
        $action_data["action_print"]='print';
    }
    if(isset($CI->permissions['download'])&&($CI->permissions['download']==1))
    {
        $action_data["action_csv"]='csv';
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
            <div class="col-xs-12" style="margin-bottom: 20px;">
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="division_name"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="zone_name"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?></label>
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
        turn_off_triggers();
        var url = "<?php echo base_url($CI->controller_url.'/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'division_name', type: 'string' },
                { name: 'zone_name', type: 'string' }
            ],
            id: 'id',
            url: url
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
                    { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>', dataField: 'division_name',width:'100',filtertype: 'list'},
                    { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>', dataField: 'zone_name'}
                ]
            });
    });
</script>