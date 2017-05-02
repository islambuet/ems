//system_redirect_url for redirect page
//page url to set current page link
//system_content for replace views
//system_message to display a message
//system_page_title for title of the page
//system_style for setting style for elements
//system_redirect_url will redirect page

//$("#system_save_new_status") mandatory for save buttons as form input field
//for browse buttons data-preview-container and data-preview-height for image display
//system_loading will show on ajaxstart and hide on ajaxcomplete
//data-form attribute contains form name for save,save and new, clear buttons

//function number format like php
function number_format(number, decimals, dec_point, thousands_sep)
{
    number = (number + '')
        .replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function(n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k)
                .toFixed(prec);
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
        .split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '')
        .length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1)
            .join('0');
    }
    return s.join(dec);
}

$(document).ready(function()
{

    $(document).ajaxStart(function()
    {
        $("#system_loading").show();

    });
    $(document).ajaxStop(function ()
    {

    });
    $(document).ajaxSuccess(function(event,xhr,options)
    {
        if(xhr.responseJSON)
        {
            if(xhr.responseJSON.system_content)
            {
                load_template(xhr.responseJSON.system_content);
            }
            if(xhr.responseJSON.system_style)
            {
                load_style(xhr.responseJSON.system_style);
            }

        }
    });
    $(document).ajaxComplete(function(event,xhr,options)
    {
        if(xhr.responseJSON)
        {
            if(xhr.responseJSON.system_redirect_url)
            {
                system_resized_image_files=[];
                window.location.replace(xhr.responseJSON.system_redirect_url);

                //window.history.pushState(null, "Search Results",xhr.responseJSON.page_url);
                //window.history.replaceState(null, "Search Results",xhr.responseJSON.system_page_url);
            }
            if(xhr.responseJSON.system_page_url)
            {
                system_resized_image_files=[];
                window.history.pushState(null, "Search Results",xhr.responseJSON.system_page_url);
                //window.history.replaceState(null, "Search Results",xhr.responseJSON.system_page_url);
            }

            //$("#loading").hide();
            $("#system_loading").hide();
            if(xhr.responseJSON.system_message)
            {
                animate_message(xhr.responseJSON.system_message);
            }
            if(xhr.responseJSON.system_page_title)
            {
                $('title').html(xhr.responseJSON.system_page_title);
            }

        }
        $("#system_loading").hide();
    });
    $(document).ajaxError(function(event,xhr,options)
    {

        $("#system_loading").hide();
        animate_message("Request Error");

    });
    //binds form submission with ajax
    $(document).on("submit", "form", function(event)
    {
        if($(this).is('[class*="report_form"]'))
        {
            window.open('','form_popup','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=1300,height=500,left = 10,top = 10,scrollbars=yes');
            this.target = 'form_popup';
            return true;
        }

        if($(this).is('[class*="external"]'))
        {
            return true;
        }
        var form_data=new FormData(this);
        var file;
        for(var i=0;i<system_resized_image_files.length;i++)
        {
            file=system_resized_image_files[i];
            if(form_data.has(file.key))
            {
                form_data.set(file.key,file.value,file.name);
            }
        }
        system_resized_image_files=[];
        event.preventDefault();
        $.ajax({
            url: $(this).attr("action"),
            type: $(this).attr("method"),
            dataType: "JSON",
            data: form_data,
            processData: false,
            contentType: false,
            success: function (data, status)
            {

            },
            error: function (xhr, desc, err)
            {


            }
        });
    });
    //bind any anchor tag to ajax request
    $(document).on("click", "a", function(event)
    {
        if(($(this).attr('href')=='#')||($(this).attr('href')==''))
        {
            event.preventDefault();
            return;
        }

        if(($(this).is('[class*="jqx"]'))||($(this).is('[class*="dropdown"]'))||($(this).is('[class*="external"]'))||($(this).is('[class*="ui-corner-all"]')))
        {
            return;
        }
        event.preventDefault();
        $.ajax({
            url: $(this).attr("href"),
            type: 'POST',
            dataType: "JSON",
            success: function (data, status)
            {

            },
            error: function (xhr, desc, err)
            {
                console.log("error");

            }
        });

    });
    $(document).on("click", "#button_action_clear", function(event)
    {

        $($(this).attr('data-form')).trigger('reset');

    });
    $(document).on("click", "#button_action_report", function(event)
    {
        $('#system_report_container').html('');
        $($(this).attr('data-form')).submit();

    });
    $(document).on("click", "#button_action_save", function(event)
    {
        $("#system_save_new_status").val(0);
        $($(this).attr('data-form')).submit();

    });
    $(document).on("click", "#button_action_save_new", function(event)
    {
        $("#system_save_new_status").val(1);
        $($(this).attr('data-form')).submit();

    });

    $(document).on("click", ".button_action_batch", function(event)
    {
        /*if($(this).attr('id')=='button_action_request_po_approve')
        {

            var sure = confirm('Are You sure?');
            if(!sure)
            {
                return;
            }
        }*/

        var jqxgrid_id='#system_jqx_container';

        var selected_row_indexes = $(jqxgrid_id).jqxGrid('getselectedrowindexes');



        if (selected_row_indexes.length > 0)
        {
            //var selectedRowData = $(jqxgrid_id).jqxGrid('getrowdata', selected_row_indexes[0]);//only first selected
            var selectedRowData = $(jqxgrid_id).jqxGrid('getrowdata', selected_row_indexes[selected_row_indexes.length-1]);//only last selected

            $.ajax({
                url: $(this).attr('data-action-link'),
                type: 'POST',
                dataType: "JSON",
                data:{'id':selectedRowData.id},
                success: function (data, status)
                {

                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });


        }
        else
        {
            alert(SELECT_ONE_ITEM);
        }

    });
    $(document).on("click", "#button_action_delete", function(event)
    {
        var jqxgrid_id='#system_jqx_container';

        var selected_row_indexes = $(jqxgrid_id).jqxGrid('getselectedrowindexes');
        if (selected_row_indexes.length > 0)
        {
            var sure = confirm(DELETE_CONFIRM);
            if(!sure)
            {
                return;
            }
            var ids=[];
            for (var i = 0; i < selected_row_indexes.length; i++)
            {
                ids.push($(jqxgrid_id).jqxGrid('getrowdata', selected_row_indexes[i]).id);
            }
            $.ajax({
                url: $(this).attr('data-action-link'),
                type: 'POST',
                dataType: "JSON",
                data:{'ids':ids},
                success: function (data, status)
                {

                },
                error: function (xhr, desc, err)
                {
                    console.log("error");

                }
            });


        }
        else
        {
            alert(SELECT_ONE_ITEM);
        }

    });

    //load the current page content
    load_current_content();
    // binds form submission and fields to the validation engine
    $(document).on("change", ":file", function(event)
    {
        if(($(this).is('[class*="file_external"]')))
        {
            return;
        }
        if(this.files && this.files[0])
        {
            var input_file=$(this);
            var container=$(this).attr('data-preview-container');
            var file=this.files[0];
            var file_type=file.type;
            if(file_type && file_type.substr(0,5)=="image")
            {
                var key=input_file.attr('name');
                var file_name=file.name.replace(/\.[^/.]+$/,"");
                //var file_name=file.name;
                var preview_height=200;
                if($(this).attr('data-preview-height'))
                {
                    preview_height=$(this).attr('data-preview-height');
                }
                var path=URL.createObjectURL(file);
                if(container)
                {
                    var img_tag='<img height="'+preview_height+'" src="'+path+'" >';
                    $(container).html(img_tag);
                }
                var img=new Image();
                img.src=path;
                img.onload=function()
                {
                    var MAX_WIDTH = 800;
                    var MAX_HEIGHT = 600;
                    var width = img.naturalWidth;
                    var height = img.naturalHeight;
                    if((width>MAX_WIDTH)||(height>MAX_HEIGHT))
                    {
                        if((width/height)>(MAX_WIDTH/MAX_HEIGHT))
                        {
                            height *= MAX_WIDTH / width;
                            width = MAX_WIDTH;
                        }
                        else
                        {
                            width *= MAX_HEIGHT / height;
                            height = MAX_HEIGHT;
                        }
                        var canvas = document.createElement("canvas");
                        canvas.width = width;
                        canvas.height = height;
                        var context = canvas.getContext("2d");
                        context.drawImage(img, 0, 0, width, height);
                        canvas.toBlob(function(blob)
                        {
                            system_resized_image_files[system_resized_image_files.length]={
                                key:key,
                                value:blob,
                                name:file_name+'.png'
                            };
                            //saveAs(blob, file.name);
                            input_file.val(null);
                            //input_file.parent().find('.badge').remove();
                        });
                        //console.log('with resize');

                    }
                    //console.log('without resize');
                };
            }
            else if(container)
            {
                $(container).html(file.name);
            }
            else
            {
                console.log('no container for file');
            }
        }
        else
        {
            console.log('no file attached');
        }
    });
    $(document).on("click", "#button_jqx_load_more", function(event)
    {
        var jqx_grid_id='#system_jqx_container';
        $(jqx_grid_id).jqxGrid('clearfilters',false);
        var jqx_source=$(jqx_grid_id).jqxGrid('source');
        var url=jqx_source['_source']['url'];
        var data=jqx_source['_source']['data'];
        var type=jqx_source['_source']['type'];
        var total_records = $(jqx_grid_id).jqxGrid('getboundrows').length;
        if(data!==undefined)
        {
            data['total_records']=total_records;
        }
        else
        {
            data={};
            data['total_records']=total_records;
        }
        var datainformation = $(jqx_grid_id).jqxGrid('getdatainformation');
        data['pagesize']=datainformation.paginginformation.pagesize;
        $.ajax({
            url: url,
            type: type,
            dataType: "JSON",
            data: data,
            success: function (data, status)
            {
                if(data.length>0)
                {
                    $(jqx_grid_id).jqxGrid('beginupdate');
                    for (var i = 0; i < data.length; i++) {

                        $(jqx_grid_id).jqxGrid('addrow', id, data[i]);
                    }
                    $(jqx_grid_id).jqxGrid('endupdate');
                    $(jqx_grid_id).jqxGrid('refreshfilterrow');
                }
                else
                {
                    animate_message('No More Record available');
                }
            },
            error: function (xhr, desc, err)
            {


            }
        });
    });
    $(document).on("click", "#button_action_print", function(event)
    {
        var jqxgrid_id='#system_jqx_container';

        var gridContent = $(jqxgrid_id).jqxGrid('exportdata', 'html');
        var newWindow = window.open('', '', 'width=800, height=500'),
            document = newWindow.document.open(),
            pageContent =
                '<!DOCTYPE html>\n' +
                    '<html>\n' +
                    '<head>\n' +
                    '<meta charset="utf-8" />\n' +
                    '<title>'+$(this).attr('data-title')+'</title>\n' +
                    '</head>\n' +
                    '<body>\n' + gridContent + '\n</body>\n</html>';
        document.write(pageContent);
        document.close();
        newWindow.print();

    });
    $(document).on("click", "#button_action_csv", function(event)
    {
        //previous csv file
        /*var jqxgrid_id='#system_jqx_container';
        $(jqxgrid_id).jqxGrid('exportdata', 'csv', $(this).attr('data-title'));*/
        var jqxgrid_id='#system_jqx_container';

        var gridContent = $(jqxgrid_id).jqxGrid('exportdata', 'html');
        var newWindow = window.open('', '', 'width=800, height=500,menubar=yes,toolbar=no,scrollbars=yes'),
            document = newWindow.document.open(),
            pageContent =
                '<!DOCTYPE html>\n' +
                    '<html>\n' +
                    '<head>\n' +
                    '<meta charset="utf-8" />\n' +
                    '<title>'+$(this).attr('data-title')+'</title>\n' +
                    '</head>\n' +
                    '<body>\n' + gridContent + '\n</body>\n</html>';
        document.write(pageContent);
        document.close();

    });
    $(document).on("click", ".system_jqx_column", function(event)
    {
        var jqxgrid_id='#system_jqx_container';
        $(jqxgrid_id).jqxGrid('beginupdate');
        if($(this).is(':checked'))
        {
            $(jqxgrid_id).jqxGrid('showcolumn', $(this).val());
        }
        else
        {
            $(jqxgrid_id).jqxGrid('hidecolumn', $(this).val());
        }
        $(jqxgrid_id).jqxGrid('endupdate');

    });

    $(document).on("input", ".float_type_positive", function(event)
    {
        this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
    });
    $(document).on("input", ".integer_type_positive", function(event)
    {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    $(document).on("input", ".float_type_all", function(event)
    {
        this.value = this.value.replace(/[^0-9.-]/g, '').replace(/(\..*)\./g, '$1').replace(/(?!^)-/g, '');
    });
    $(document).on("input", ".integer_type_all", function(event)
    {
        this.value = this.value.replace(/[^0-9-]/g, '').replace(/(?!^)-/g, '');
    });
    $("#popup_window").jqxWindow({
        width: 550,height:550, resizable: true,  isModal: true, autoOpen: false, modalOpacity: 0.01,position: { x: 60, y: 60 }
    });


});
function load_current_content()
{
    $.ajax({
        url: location,
        type: 'POST',
        dataType: "JSON",
        success: function (data, status)
        {

        },
        error: function (xhr, desc, err)
        {
            console.log("error");

        }
    });
}
function load_template(content)
{
    for(var i=0;i<content.length;i++)
    {
        $(content[i].id).html(content[i].html);

    }
}
function load_style(content)
{
    for(var i=0;i<content.length;i++)
    {
        if(content[i].style)
        {
            $(content[i].id).attr('style',content[i].style);
        }
        if(content[i].display)
        {
            $(content[i].id).show();
        }
        else
        {
            $(content[i].id).hide();
        }
    }
}
function animate_message(message)
{
    $("#system_message").html(message);
    $("#system_message").animate({right:"100px"}).animate({right:"30px"}).delay(3000).animate({right:"100px"}).animate({right:"-5000px"});
}

function turn_off_triggers()
{
    $(document).off("click", ".task_action_all");
    $(document).off("click", ".task_header_all");

    //location setup
    $(document).off("change", "#division_id");
    $(document).off("change", "#zone_id");
    $(document).off("change", "#territory_id");
    $(document).off("change", "#district_id");
    $(document).off("change", "#upazilla_id");

    //classification
    $(document).off("change", "#crop_id");
    $(document).off("change", "#crop_type_id");
    $(document).off("change",'input[name="variety[whose]:radio');//at create_crop_variety
    //stock in
    $(document).off("change", "#fiscal_year_id");
    $(document).off("change", "#warehouse_id");
    $(document).off("change", "#variety_id");
    $(document).off("change", "#arm_bank_id");
    //po
    $(document).off("click", ".system_button_add_more");
    $(document).off("click", ".system_button_add_delete");
    $(document).off("change", ".crop_id");
    $(document).off("change", ".crop_type_id");
    $(document).off("change", ".variety_id");
    $(document).off("change", ".pack_size_id");
    $(document).off("change", ".quantity");

    //stock out
    $(document).off("change", "#purpose");
    $(document).off("change", "#customer_id");
    $(document).off("change", "#date");
    

}
function get_dropdown_with_select(items,selected_value = '')
{
    var dropdown_html='<option value="">Select</option>';
    for(var i=0;i<items.length;++i)
    {
        dropdown_html+='<option value="'+items[i].value+'"';
        if(items[i].value==selected_value)
        {
            dropdown_html+=' selected';
        }
        dropdown_html+='>'+items[i].text+'</option>';
    }
    return dropdown_html;
}