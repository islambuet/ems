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
    if((isset($CI->permissions['add'])&&($CI->permissions['add']==1))||(isset($CI->permissions['edit'])&&($CI->permissions['edit']==1)))
    {
        $action_buttons[]=array(
            'type'=>'button',
            'label'=>$CI->lang->line("ACTION_EDIT"),
            'class'=>'button_jqx_action',
            'data-action-link'=>site_url($CI->controller_url.'/index/edit')
        );
        $action_buttons[]=array(
            'type'=>'button',
            'label'=>'Change status',
            'data-message-confirm'=>'Are you sure to Change Status?',
            'class'=>'button_jqx_action',
            'data-action-link'=>site_url($CI->controller_url.'/index/edit_status')
        );
        $action_buttons[]=array(
            'type'=>'button',
            'label'=>'Change Complete status',
            'data-message-confirm'=>'Are you sure to Change Complete Status?',
            'class'=>'button_jqx_action',
            'data-action-link'=>site_url($CI->controller_url.'/index/edit_status_complete')
        );
    }
    if(isset($CI->permissions['view'])&&($CI->permissions['view']==1))
    {
        $action_buttons[]=array(
            'type'=>'button',
            'label'=>$CI->lang->line('ACTION_DETAILS'),
            'class'=>'button_jqx_action',
            'data-action-link'=>site_url($CI->controller_url.'/index/details')
        );
    }
    if(isset($CI->permissions['delete'])&&($CI->permissions['delete']==1))
    {
        $action_buttons[]=array(
            'type'=>'button',
            'label'=>$CI->lang->line("ACTION_DELETE"),
            'data-message-confirm'=>'Are you sure to Delete?',
            'class'=>'button_jqx_action',
            'data-action-link'=>site_url($CI->controller_url.'/index/delete')
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
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_LOAD_MORE"),
    'id'=>'button_jqx_load_more'
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
            <div class="col-xs-12" style="margin-bottom: 20px;">
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="name">Farmer Name</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="year"><?php echo $CI->lang->line('LABEL_YEAR'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="season_name"><?php echo $CI->lang->line('LABEL_SEASON'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="division_name"><?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="zone_name"><?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="territory_name"><?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="district_name"><?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="upazilla_name"><?php echo $CI->lang->line('LABEL_UPAZILLA_NAME'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="contact_no">Contact No</label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="date_sowing"><?php echo $CI->lang->line('LABEL_DATE_SOWING'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="num_visits"><?php echo $CI->lang->line('LABEL_NUM_VISITS'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="interval"><?php echo $CI->lang->line('LABEL_INTERVAL'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="status"><?php echo $CI->lang->line('STATUS'); ?></label>
                <label class="checkbox-inline"><input type="checkbox" class="system_jqx_column"  checked value="status_complete"><?php echo $CI->lang->line('LABEL_STATUS_COMPLETE'); ?></label>
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
        var url = "<?php echo base_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'int' },
                { name: 'name', type: 'string' },
                { name: 'year', type: 'string' },
                { name: 'season_name', type: 'string' },
                { name: 'division_name', type: 'string' },
                { name: 'zone_name', type: 'string' },
                { name: 'territory_name', type: 'string' },
                { name: 'district_name', type: 'string' },
                { name: 'upazilla_name', type: 'string' },
                { name: 'contact_no', type: 'string' },
                { name: 'date_sowing', type: 'string' },
                { name: 'num_visits', type: 'string' },
                { name: 'interval', type: 'string' },
                { name: 'status', type: 'string' },
                { name: 'status_complete', type: 'string' }

            ],
            id: 'id',
            type: 'POST',
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
                pagesize:20,
                pagesizeoptions: ['20', '50', '100', '200','300','500'],
                selectionmode: 'singlerow',
                altrows: true,
                autoheight: true,
                enablebrowserselection:true,
                columnsreorder: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('ID'); ?>', dataField: 'id',width:'40',pinned:true,cellsalign: 'right'},
                    { text: 'Farmer Name', dataField: 'name',width:'200',pinned:true},
                    { text: '<?php echo $CI->lang->line('LABEL_YEAR'); ?>', dataField: 'year',width:'100',filtertype: 'list'},
                    { text: '<?php echo $CI->lang->line('LABEL_SEASON'); ?>', dataField: 'season_name',width:'100',filtertype: 'list'},
                    { text: '<?php echo $CI->lang->line('LABEL_DIVISION_NAME'); ?>', dataField: 'division_name',width:'100',filtertype: 'list'},
                    { text: '<?php echo $CI->lang->line('LABEL_ZONE_NAME'); ?>', dataField: 'zone_name',width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_TERRITORY_NAME'); ?>', dataField: 'territory_name',width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_DISTRICT_NAME'); ?>', dataField: 'district_name',width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_UPAZILLA_NAME'); ?>', dataField: 'upazilla_name',width:'100'},
                    { text: 'Contact No', dataField: 'contact_no',width:'100'},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_SOWING'); ?>', dataField: 'date_sowing',width:'100',cellsalign: 'right'},
                    { text: '<?php echo $CI->lang->line('LABEL_NUM_VISITS'); ?>', dataField: 'num_visits',width:'50',cellsalign: 'right',filtertype: 'list'},
                    { text: '<?php echo $CI->lang->line('LABEL_INTERVAL'); ?>', dataField: 'interval',width:'50',cellsalign: 'right',filtertype: 'list'},
                    { text: '<?php echo $CI->lang->line('STATUS'); ?>', dataField: 'status',width:'50',filtertype: 'list'},
                    { text: '<?php echo $CI->lang->line('LABEL_STATUS_COMPLETE'); ?>', dataField: 'status_complete',width:'50',filtertype: 'list'}
                ]
            });
    });
</script>