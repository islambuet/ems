<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    foreach($items as $item)
    {
        ?>
        <div class="checkbox">
            <label><input type="checkbox" name="customers[<?php echo $day_no; ?>][<?php echo $shift_id; ?>][<?php echo $item['value']; ?>]" value="<?php echo $item['value']; ?>"><?php echo $item['text']; ?></label>
        </div>
        <?php
    }
?>
