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
                if(in_array($union['value'],$union_ids))
                {

                ?>
                <label class="checkbox-inline" style="font-size:12px;padding: 5px 5px 5px 5px;background-color: #0C865B; color: #fff; "><?php echo $union['text']; ?></label>
                <?php
                }
            }
            ?>
        </div>
        <div class="col-xs-12" style="overflow-x: auto;">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th colspan="50">ARM Variety</th>
                    </tr>
                    <tr>
                        <th style="width: 100px;">
                            Variety
                        </th>
                        <?php
                        for($i=1;$i<=$max_customers_number;$i++)
                        {
                            ?>
                            <th style="width: 100px;">
                                Individual Sales Quantity
                            </th>
                            <th style="width: 100px;">
                                Market Size
                            </th>
                            <?php
                        }
                        ?>
                        <th style="width: 100px;">
                            Assumed Market Size
                        </th>
                        <th style="width: 100px;">
                            ZI Assumed Market Size
                        </th>
                    </tr>
                    <tr>
                        <th></th>
                        <?php
                        for($i=1;$i<=$max_customers_number;$i++)
                        {
                            ?>
                            <th colspan="2">
                                <label class="text-center form-control" style="background-color: #F5F5F5;">
                                    <?php
                                    if(isset($customers[$i])&&strlen($customers[$i])>0)
                                    {
                                        echo $customers[$i];
                                    }
                                    ?>
                                </label>
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
                                    <label class="text-right form-control" style="background-color: #F5F5F5;">
                                        <?php
                                        if(isset($survey_customer_survey[$variety['id']][$i]['weight_sales']))
                                        {
                                            echo $survey_customer_survey[$variety['id']][$i]['weight_sales'];
                                        }
                                        ?>
                                    </label>
                                </td>
                                <td>
                                    <label class="text-right form-control" style="background-color: #F5F5F5;">
                                        <?php
                                        if(isset($survey_customer_survey[$variety['id']][$i]['weight_market']))
                                        {
                                            echo $survey_customer_survey[$variety['id']][$i]['weight_market'];
                                        }
                                        ?>
                                    </label>
                                </td>
                                <?php
                            }
                            ?>
                            <td>
                                <label class="text-right form-control" style="background-color: #F5F5F5;">
                                    <?php
                                    if(isset($survey_quantity_survey[$variety['id']]['weight_assumed']))
                                    {
                                        echo $survey_quantity_survey[$variety['id']]['weight_assumed'];
                                    }
                                    ?>
                                </label>
                            </td>
                            <td>
                                <?php
                                $editable=false;
                                $weight_final='';
                                if(isset($survey_quantity_survey[$variety['id']]['weight_final'])&&($survey_quantity_survey[$variety['id']]['weight_final']>0))
                                {
                                    $weight_final=$survey_quantity_survey[$variety['id']]['weight_final'];
                                    if(isset($CI->permissions['edit'])&&($CI->permissions['edit']==1))
                                    {
                                        $editable=true;
                                    }
                                    else
                                    {
                                        $editable=false;
                                    }

                                }
                                else
                                {
                                    $editable=true;
                                    if(isset($survey_quantity_survey[$variety['id']]['weight_assumed'])&&($survey_quantity_survey[$variety['id']]['weight_assumed']>0))
                                    {
                                        $weight_final=$survey_quantity_survey[$variety['id']]['weight_assumed'];
                                    }
                                }
                                if($editable)
                                {
                                    ?>
                                    <input type="text" name="weight_final[<?php echo $variety['id'];?>]" class="form-control integer_type_positive text-right" value="<?php echo $weight_final; ?>">
                                <?php
                                }
                                else
                                {
                                    ?>
                                    <label class="text-right form-control" style="background-color: #F5F5F5;"><?php echo $weight_final; ?></label>
                                <?php
                                }
                                ?>
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
                                    <label class="text-right form-control" style="background-color: #F5F5F5;">
                                        <?php
                                        if(isset($survey_customer_survey[$variety['id']][$i]['weight_sales']))
                                        {
                                            echo $survey_customer_survey[$variety['id']][$i]['weight_sales'];
                                        }
                                        ?>
                                    </label>
                                </td>
                                <td>
                                    <label class="text-right form-control" style="background-color: #F5F5F5;">
                                        <?php
                                        if(isset($survey_customer_survey[$variety['id']][$i]['weight_market']))
                                        {
                                            echo $survey_customer_survey[$variety['id']][$i]['weight_market'];
                                        }
                                        ?>
                                    </label>
                                </td>
                            <?php
                            }
                            ?>
                            <td>
                                <label class="text-right form-control" style="background-color: #F5F5F5;">
                                    <?php
                                    if(isset($survey_quantity_survey[$variety['id']]['weight_assumed']))
                                    {
                                        echo $survey_quantity_survey[$variety['id']]['weight_assumed'];
                                    }
                                    ?>
                                </label>
                            </td>
                            <td>
                                <?php
                                $editable=false;
                                $weight_final='';
                                if(isset($survey_quantity_survey[$variety['id']]['weight_final'])&&($survey_quantity_survey[$variety['id']]['weight_final']>0))
                                {
                                    $weight_final=$survey_quantity_survey[$variety['id']]['weight_final'];
                                    if(isset($CI->permissions['edit'])&&($CI->permissions['edit']==1))
                                    {
                                        $editable=true;
                                    }
                                    else
                                    {
                                        $editable=false;
                                    }

                                }
                                else
                                {
                                    $editable=true;
                                    if(isset($survey_quantity_survey[$variety['id']]['weight_assumed'])&&($survey_quantity_survey[$variety['id']]['weight_assumed']>0))
                                    {
                                        $weight_final=$survey_quantity_survey[$variety['id']]['weight_assumed'];
                                    }
                                }
                                if($editable)
                                {
                                    ?>
                                    <input type="text" name="weight_final[<?php echo $variety['id'];?>]" class="form-control integer_type_positive text-right" value="<?php echo $weight_final; ?>">
                                <?php
                                }
                                else
                                {
                                    ?>
                                    <label class="text-right form-control" style="background-color: #F5F5F5;"><?php echo $weight_final; ?></label>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <th colspan="21">
                            Others variety
                        </th>
                    </tr>
                    <tr>
                        <td>
                            Others
                        </td>
                        <?php
                        for($i=1;$i<=$max_customers_number;$i++)
                        {
                            ?>
                            <td>
                                <label class="text-right form-control" style="background-color: #F5F5F5;">
                                    <?php
                                    if(isset($survey_customer_survey[0][$i]['weight_sales']))
                                    {
                                        echo $survey_customer_survey[0][$i]['weight_sales'];
                                    }
                                    ?>
                                </label>
                            </td>
                            <td>
                                <label class="text-right form-control" style="background-color: #F5F5F5;">
                                    <?php
                                    if(isset($survey_customer_survey[0][$i]['weight_market']))
                                    {
                                        echo $survey_customer_survey[0][$i]['weight_market'];
                                    }
                                    ?>
                                </label>
                            </td>
                        <?php
                        }
                        ?>
                        <td>
                            <label class="text-right form-control" style="background-color: #F5F5F5;">
                                <?php
                                if(isset($survey_quantity_survey[0]['weight_assumed']))
                                {
                                    echo $survey_quantity_survey[0]['weight_assumed'];
                                }
                                ?>
                            </label>
                        </td>
                        <td>
                            <?php
                            $editable=false;
                            $weight_final='';
                            if(isset($survey_quantity_survey[0]['weight_final'])&&($survey_quantity_survey[0]['weight_final']>0))
                            {
                                $weight_final=$survey_quantity_survey[0]['weight_final'];
                                if(isset($CI->permissions['edit'])&&($CI->permissions['edit']==1))
                                {
                                    $editable=true;
                                }
                                else
                                {
                                    $editable=false;
                                }

                            }
                            else
                            {
                                $editable=true;
                                if(isset($survey_quantity_survey[0]['weight_assumed'])&&($survey_quantity_survey[0]['weight_assumed']>0))
                                {
                                    $weight_final=$survey_quantity_survey[0]['weight_assumed'];
                                }
                            }
                            if($editable)
                            {
                                ?>
                                <input type="text" name="weight_final[0]" class="form-control integer_type_positive text-right" value="<?php echo $weight_final; ?>">
                            <?php
                            }
                            else
                            {
                                ?>
                                <label class="text-right form-control" style="background-color: #F5F5F5;"><?php echo $weight_final; ?></label>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="clearfix"></div>
</form>
