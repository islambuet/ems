<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    foreach($items as $item)
    {
        ?>
        <div class="checkbox">
            <label><input type="checkbox" name="data[<?php echo $day_no; ?>][<?php echo $shift_id; ?>][other_customer][<?php echo $item['value']; ?>]" value="<?php echo $item['value']; ?>"><?php echo $item['text'];if($item['status']!=$CI->config->item('system_status_active')){echo '('.$item['status'].')';} ?></label>
        </div>
        <?php
    }
?>
