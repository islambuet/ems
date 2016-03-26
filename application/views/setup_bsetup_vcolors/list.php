<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();

    $action_data=array();
    if(isset($CI->permissions['add'])&&($CI->permissions['add']==1))
    {
        $action_data["action_new"]=base_url($CI->controller_url."/index/add");
    }
    if(isset($CI->permissions['edit'])&&($CI->permissions['edit']==1))
    {
        $action_data["action_edit"]=base_url($CI->controller_url."/index/edit");
    }
    if(isset($CI->permissions['delete'])&&($CI->permissions['delete']==1))
    {
        $action_data["action_delete"]=base_url($CI->controller_url."/index/delete");
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
                { name: 'name', type: 'string' },
                { name: 'days', type: 'numeric' },
                { name: 'color_code', type: 'string' }
            ],
            id: 'id',
            url: url
        };
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            if (column=="color_code")
            {
                element.css({ 'background-color': value,'margin': '0px','width': '100%', 'height': '100%',padding:'5px','line-height':'25px'});
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
                rowsheight: 35,
                enabletooltips: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_NAME'); ?>', dataField: 'name',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Days', dataField: 'days',width:'90',align:'center',cellsAlign:'right',cellsrenderer: cellsrenderer,rendered: tooltiprenderer},
                    { text: 'Color', dataField: 'color_code',align:'center',width:'200',cellsrenderer: cellsrenderer,rendered: tooltiprenderer}

                ]
            });
    });
</script>