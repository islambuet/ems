<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<?php
    $CI = & get_instance();

?>
<div class="row widget hidden-print" style="padding-bottom: 0px;">

    <?php
    if(isset($action_new))
    {
        ?>
        <div class="action_button">
            <a class="btn" href="<?php echo $action_new; ?>"><?php echo $CI->lang->line("ACTION_NEW"); ?></a>
        </div>
        <?php
    }
    ?>
    <?php
    if(isset($action_edit))
    {
        ?>
        <div class="action_button">
            <button id="button_action_edit" class="btn button_action_batch" data-action-link="<?php echo $action_edit; ?>"><?php echo $CI->lang->line("ACTION_EDIT"); ?></button>
        </div>
        <?php
    }
    ?>
    <?php
    if(isset($action_details))
    {
        ?>
        <div class="action_button">
                <button id="button_action_details" class="btn button_action_batch" data-action-link="<?php echo $action_details; ?>"><?php echo $CI->lang->line("ACTION_DETAILS"); ?></button>
            </div>
            <?php
        }
    ?>
    <?php
    if(isset($action_delete))
    {
        ?>
        <div class="action_button">
            <button id="button_action_delete" class="btn" data-action-link="<?php echo $action_delete; ?>"><?php echo $CI->lang->line("ACTION_DELETE"); ?></button>
        </div>
    <?php
    }
    ?>
    <?php
    if(isset($action_back))
    {
        ?>
        <div class="action_button">
            <a class="btn" href="<?php echo $action_back; ?>"><?php echo $CI->lang->line("ACTION_BACK"); ?></a>
        </div>
        <?php
    }
    ?>
    <?php
    if(isset($action_save))
    {
        ?>
        <div class="action_button">
            <button id="button_action_save" class="btn" data-form="<?php echo $action_save; ?>"><?php echo $CI->lang->line("ACTION_SAVE"); ?></button>
        </div>
        <?php
    }
    ?>
    <?php
    if(isset($action_save_new))
    {
        ?>
        <div class="action_button">
                <button id="button_action_save_new" class="btn" data-form="<?php echo $action_save_new; ?>"><?php echo $CI->lang->line("ACTION_SAVE_NEW"); ?></button>
            </div>
            <?php
        }
    ?>
    <?php
    if(isset($action_clear))
    {
        ?>
        <div class="action_button">
            <button id="button_action_clear" class="btn" data-form="<?php echo $action_clear; ?>"><?php echo $CI->lang->line("ACTION_CLEAR"); ?></button>
        </div>
    <?php
    }
    ?>
    <?php
    if(isset($action_refresh))
    {
        ?>
        <div class="action_button">
            <a class="btn" href="<?php echo $action_refresh; ?>"><?php echo $CI->lang->line("ACTION_REFRESH"); ?></a>
        </div>
    <?php
    }
    ?>
</div>
<div class="clearfix"></div>