<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if(!isset($selected_value))
{
    $selected_value='';
}
?>

<div class="row show-grid">
    <div class="col-xs-4">
        <label class="control-label pull-right">Participant Through Dealer :</label>
    </div>
</div>

<?php
foreach($items as $item)
{
    ?>
    <div class="row show-grid">
    <div class="col-xs-5">
        <label class="control-label pull-right"><?php echo $item['text'].' ('.$item['phone_no'].')';?><span style="color: red;">*</span></label>
    </div>
    <div class="col-sm-3 col-xs-9">
        <input type="text" name="farmer_participant[<?php echo $item['value'];?>]" class="participant_budget form-control float_type_positive" value=""/>
    </div>
    </div>
<?php
}
?>