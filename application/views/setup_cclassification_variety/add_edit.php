<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $action_data=array();
    $action_data["action_back"]=base_url($CI->controller_url);
    $action_data["action_save"]='#save_form';
    $action_data["action_save_new"]='#save_form';
    $action_data["action_clear"]='#save_form';
    $CI->load->view("action_buttons",$action_data);
?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $variety['id']; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>


        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="crop_id" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($crops as $crop)
                    {?>
                        <option value="<?php echo $crop['value']?>" <?php if($crop['value']==$variety['crop_id']){ echo "selected";}?>><?php echo $crop['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>

        <div style="<?php if(!($variety['crop_type_id']>0)){echo 'display:none';} ?>" class="row show-grid" id="crop_type_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_CROP_TYPE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="crop_type_id" name="variety[crop_type_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($crop_types as $crop_type)
                    {?>
                        <option value="<?php echo $crop_type['value']?>" <?php if($crop_type['value']==$variety['crop_type_id']){ echo "selected";}?>><?php echo $crop_type['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_WHOSE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <div class="radio-inline">
                    <label><input type="radio" value="ARM" <?php if($variety['whose']=='ARM'){echo 'checked';} ?> name="variety[whose]">ARM</label>
                </div>
                <div class="radio-inline">
                    <label><input type="radio" value="Competitor" <?php if($variety['whose']=='Competitor'){echo 'checked';} ?> name="variety[whose]">Competitor</label>
                </div>
                <div class="radio-inline">
                    <label><input type="radio" value="Upcoming" <?php if($variety['whose']=='Upcoming'){echo 'checked';} ?> name="variety[whose]">Upcoming</label>
                </div>
            </div>
        </div>
        <div style="<?php if($variety['whose']!='Competitor'){echo 'display:none';} ?>" class="row show-grid" id="competitor_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_COMPETITOR_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="competitor_id" name="variety[competitor_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($competitors as $competitor)
                    {?>
                        <option value="<?php echo $competitor['value']?>" <?php if($competitor['value']==$variety['competitor_id']){ echo "selected";}?>><?php echo $competitor['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_NAME');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="variety[name]" id="name" class="form-control" value="<?php echo $variety['name'];?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_STOCK_ID');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="variety[stock_id]" id="stock_id" class="form-control" value="<?php echo $variety['stock_id'];?>"/>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_HYBRID');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status" name="variety[hybrid]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <option value="F1 Hybrid"
                        <?php
                        if ($variety['hybrid'] == 'F1 Hybrid') {
                            echo "selected='selected'";
                        }
                        ?> >F1 Hybrid
                    </option>
                    <option value="OP"
                        <?php
                        if ($variety['hybrid'] == 'OP') {
                            echo "selected='selected'";
                        }
                        ?> >OP
                    </option>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DESCRIPTION');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="variety[description]" id="description" class="form-control"><?php echo $variety['description'] ?></textarea>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ORDER');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="variety[ordering]" id="ordering" class="form-control" value="<?php echo $variety['ordering'] ?>" >
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('STATUS');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status" name="variety[status]" class="form-control">
                    <!--<option value=""></option>-->
                    <option value="<?php echo $CI->config->item('system_status_active'); ?>"
                        <?php
                        if ($variety['status'] == $CI->config->item('system_status_active')) {
                            echo "selected='selected'";
                        }
                        ?> ><?php echo $CI->lang->line('ACTIVE') ?>
                    </option>
                    <option value="<?php echo $CI->config->item('system_status_inactive'); ?>"
                        <?php
                        if ($variety['status'] == $CI->config->item('system_status_inactive')) {
                            echo "selected='selected'";
                        }
                        ?> ><?php echo $CI->lang->line('INACTIVE') ?></option>
                </select>
            </div>
        </div>
        <div class="row show-grid" id="principal_id_container">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PRINCIPAL_NAME');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="principal_id" name="variety[principal_id]" class="form-control">
                    <option value=""><?php echo $this->lang->line('SELECT');?></option>
                    <?php
                    foreach($principals as $principal)
                    {?>
                        <option value="<?php echo $principal['value']?>" <?php if($principal['value']==$variety['principal_id']){ echo "selected";}?>><?php echo $principal['text'];?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Import Name</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="variety[name_import]" id="name_import" class="form-control" value="<?php echo $variety['name_import'] ?>" >
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
<script type="text/javascript">

    jQuery(document).ready(function()
    {
        turn_off_triggers();
        $(document).on("change","#crop_id",function()
        {
            $("#crop_type_id").val("");
            var crop_id=$('#crop_id').val();
            if(crop_id>0)
            {
                $('#crop_type_id_container').show();
                $.ajax({
                    url: base_url+"common_controller/get_dropdown_croptypes_by_cropid/",
                    type: 'POST',
                    datatype: "JSON",
                    data:{crop_id:crop_id},
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
                $('#crop_type_id_container').hide();
            }
        });
        $(document).on("change",'input[name="variety[whose]"]:radio',function()
        {
            var whose=$(this).val();
            if(whose=='Competitor')
            {
                $("#competitor_id_container").show();
            }
            else
            {
                $("#competitor_id_container").hide();
            }


        });

    });
</script>
