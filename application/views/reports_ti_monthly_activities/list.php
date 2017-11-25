<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$CI = & get_instance();
$action_buttons=array();
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
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Name :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $employee_info['name']?>
        </div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Employee ID :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $employee_info['employee_id']?>
        </div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Designation :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $employee_info['designation_name']?>
        </div>
    </div>
    <div style="" class="row show-grid">
        <div class="col-xs-4">
            <label class="control-label pull-right">Area :</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <?php echo $area_name['area_name']?>
        </div>
    </div>

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
            var left=((($(window).width()-550)/2)+$(window).scrollLeft());
            var top=((($(window).height()-550)/2)+$(window).scrollTop());
            $("#popup_window").jqxWindow({width: 1200,height:550,position:{x:left,y:top}}); //to change position always
            //$("#popup_window").jqxWindow({position:{x:left,y:top}});
            var row=$(this).attr('data-item-no');
            var id=$("#system_jqx_container").jqxGrid('getrowdata',row).id;
            $.ajax(
                {
                    url: "<?php echo site_url($CI->controller_url.'/index/details') ?>",
                    type: 'POST',
                    datatype: "JSON",
                    data:
                    {
                        html_container_id:'#popup_content',
                        id:id
                    },
                    success: function (data, status)
                    {

                    },
                    error: function (xhr, desc, err)
                    {
                        console.log("error");
                    }
                });
            $("#popup_window").jqxWindow('open');
        });


        var url = "<?php echo base_url($CI->controller_url.'/index/get_items');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                { name: 'id', type: 'string' },
                { name: 'month_id', type: 'string' },
                { name: 'achievement', type: 'string' },
                { name: 'work_done', type: 'string' },
                { name: 'next_month_crop_variety', type: 'string' },
                { name: 'amount_self_target', type: 'string' },
                { name: 'reason_self_target', type: 'string' },
                { name: 'value_marking', type: 'string' },
                { name: 'reason_marking', type: 'string' },


                { name: 'details', type: 'string' }

            ],
            id: 'id',
            url: url,
            type: 'POST',
            data:JSON.parse('<?php echo json_encode($options);?>')
        };

        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            element.css({'margin': '0px','width': '100%', 'height': '100%',padding:'5px'});
            if(column=='details_button')
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
                autorowheight: true,
                autoheight: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_MONTH'); ?>',dataField: 'month_id',pinned:true,filtertype: 'list',width:'200',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_PREVIOUS_MONTH_ACHIEVEMENT'); ?>',dataField: 'achievement',width:'140',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_SUCCESSFULLY_WORK_DONE'); ?>',dataField: 'work_done',width:'140',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_NEXT_MONTHS_CROP_VARIETY'); ?>',dataField: 'next_month_crop_variety',width:'140',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_SELF_TARGET').' (Amount)'; ?>', dataField: 'amount_self_target',width:'140',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_SELF_TARGET').' (Reason)'; ?>', dataField: 'reason_self_target',width:'140',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_SELF_MARKING').' (Out of 10)'; ?>', dataField: 'value_marking',width:'140',rendered:tooltiprenderer},
                    { text: '<?php echo $CI->lang->line('LABEL_SELF_MARKING').' (Reason)'; ?>', dataField: 'reason_marking',width:'140',rendered:tooltiprenderer},
                    { text: 'Details', dataField: 'details_button',width: '85',cellsrenderer: cellsrenderer,rendered: tooltiprenderer}

                ]
            });
    });
</script>