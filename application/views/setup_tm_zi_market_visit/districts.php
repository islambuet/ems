<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
?>
<select class="form-control district_id" data-day="<?php echo $day_no; ?>" data-shift-id="<?php echo $shift_id; ?>">
    <option value=""><?php echo $this->lang->line('SELECT');?></option>
    <?php
        foreach($items as $item)
        {
            ?>
            <option value="<?php echo $item['value'];?>"><?php echo $item['text'];?></option>
            <?php
        }
    ?>
</select>