<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
    $union_ids=array();
    $customers=array();
    $remarks='';
    if($survey)
    {
        if($survey['union_ids'])
        {
            $union_ids=json_decode($survey['union_ids'],true);
        }

        $customers=json_decode($survey['customers'],true);
        $remarks=$survey['remarks'];
    }

?>
<form class="form_valid" id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" name="year" value="<?php echo $year; ?>" />
    <input type="hidden" name="crop_type_id" value="<?php echo $crop_type_id; ?>" />
    <input type="hidden" name="upazilla_id" value="<?php echo $upazilla_id; ?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="col-xs-12" style="margin-bottom: 20px;">
            <?php
            foreach($unions as $union)
            {
                ?>
                <label class="checkbox-inline" style="font-size:12px;padding: 5px 5px 5px 25px;background-color: #0C865B; color: #fff; "><input type="checkbox" name="unions[]" value="<?php echo $union['value']; ?>" <?php if(in_array($union['value'],$union_ids)){echo 'checked';} ?>><?php echo $union['text']; ?></label>
                <?php
            }
            ?>
        </div>
        <table class="table table-condensed table-striped table-bordered table-hover no-margin">
            <thead>
                <tr>
                    <th colspan="50">ARM Variety</th>
                </tr>
                <tr>
                    <th>
                        Variety
                    </th>
                    <?php
                    for($i=1;$i<=$max_customers_number;$i++)
                    {
                        ?>
                        <th style="width: 150px;">
                            Individual Sales Quantity
                        </th>
                        <th style="width: 150px;">
                            Market Size
                        </th>
                        <?php
                    }
                    ?>
                    <th>
                        Assumed Market Size
                    </th>
                </tr>
                <tr>
                    <th></th>
                    <?php
                    for($i=1;$i<=$max_customers_number;$i++)
                    {
                        ?>
                        <th colspan="2">
                            <input type="text" name="customers[<?php echo $i;?>]" class="form-control" value="<?php if(isset($customers[$i])){echo $customers[$i]; } ?>">
                        </th>
                    <?php
                    }
                    ?>
                    <th>
                        &nbsp;
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach($varieties_arm as $variety)
                {
                    ?>
                    <tr>
                        <td>
                            <?php echo $variety['name']; ?>
                        </td>
                        <?php
                        for($i=1;$i<=$max_customers_number;$i++)
                        {
                            ?>
                            <td>
                                <input type="text" class="form-control integer_type_positive text-right" value="">
                            </td>
                            <td>
                                <input type="text" class="form-control integer_type_positive text-right" value="">
                            </td>
                        <?php
                        }
                        ?>
                        <td>
                            <input type="text" class="form-control integer_type_positive text-right" value="">
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <!--check variety-->
                <tr>
                    <th colspan="21">
                        Competitor Variety
                    </th>
                </tr>
                <?php
                foreach($varieties_competitor as $variety)
                {
                    ?>
                    <tr>
                        <td>
                            <?php echo $variety['name']; ?>
                        </td>
                        <?php
                        for($i=1;$i<=$max_customers_number;$i++)
                        {
                            ?>
                            <td>
                                <input type="text" class="form-control integer_type_positive text-right" value="">
                            </td>
                            <td>
                                <input type="text" class="form-control integer_type_positive text-right" value="">
                            </td>
                        <?php
                        }
                        ?>
                        <td>
                            <input type="text" class="form-control integer_type_positive text-right" value="">
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $this->lang->line('LABEL_REMARKS');?></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea class="form-control" id="remarks" name="remarks"><?php echo $remarks; ?></textarea>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
