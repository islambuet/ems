<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $CI = & get_instance();
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
                <label class="checkbox-inline" style="font-size:12px;padding: 5px 5px 5px 25px;background-color: #0C865B; color: #fff; "><input type="checkbox"><?php echo $union['text']; ?></label>
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
                    <th>
                        F1/OP
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
                    <th colspan="2"></th>
                    <?php
                    for($i=1;$i<=$max_customers_number;$i++)
                    {
                        ?>
                        <th colspan="2">
                            <input type="text" class="form-control" value="">
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
                        <td>
                            <?php echo $variety['hybrid']; ?>
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
                        <td>
                            <?php echo $variety['hybrid']; ?>
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
    </div>

    <div class="clearfix"></div>
</form>
