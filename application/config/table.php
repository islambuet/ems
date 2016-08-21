<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//those tables from login site
$config['table_setup_user'] = 'setup_user';
$config['table_setup_user_info'] = 'setup_user_info';
$config['table_setup_users_other_sites'] = 'setup_users_other_sites';
$config['table_system_other_sites'] = 'system_other_sites';
$config['table_other_sites_visit'] = 'other_sites_visit';
$config['table_setup_designation'] = 'setup_designation';

//ems site
$config['table_system_assigned_group'] = 'ems_system_assigned_group';
$config['table_system_assigned_area'] = 'ems_system_assigned_area';

$config['table_system_user_group'] = 'ems_system_user_group';
$config['table_system_task'] = 'ems_system_task';
$config['table_system_user_group_role'] = 'ems_system_user_group_role';
$config['table_history'] = 'ems_history';
$config['table_system_site_offline'] = 'ems_system_site_offline';
$config['table_system_po_status_change'] = 'ems_system_po_status_change';
//location setup
$config['table_setup_location_divisions'] = 'ems_divisions';
$config['table_setup_location_zones'] = 'ems_zones';
$config['table_setup_location_territories'] = 'ems_territories';
$config['table_setup_location_districts'] = 'ems_districts';
$config['table_setup_location_upazillas'] = 'ems_upazillas';
$config['table_setup_location_unions'] = 'ems_unions';
//crop classification
$config['table_setup_classification_crops'] = 'ems_crops';
$config['table_setup_classification_crop_types'] = 'ems_crop_types';
$config['table_setup_classification_varieties'] = 'ems_varieties';
$config['table_setup_classification_vpack_size'] = 'ems_variety_pack_size';
$config['table_setup_classification_variety_price'] = 'ems_variety_price';
$config['table_setup_classification_variety_price_kg'] = 'ems_variety_price_kg';
$config['table_setup_classification_variety_bonus'] = 'ems_variety_bonus';
$config['table_setup_classification_variety_bonus_details'] = 'ems_variety_bonus_details';
$config['table_setup_classification_variety_time'] = 'ems_variety_time';
//basic setup
$config['table_basic_setup_warehouse'] = 'ems_basic_setup_warehouse';
$config['table_basic_setup_warehouse_crops'] = 'ems_basic_setup_warehouse_crops';
$config['table_basic_setup_bank'] = 'ems_basic_setup_bank';
$config['table_basic_setup_arm_bank'] = 'ems_basic_setup_arm_bank';
$config['table_basic_setup_arm_bank_accounts'] = 'ems_basic_setup_arm_bank_accounts';
$config['table_basic_setup_fiscal_year'] = 'ems_basic_setup_fiscal_year';
$config['table_basic_setup_competitor'] = 'ems_basic_setup_competitor';
$config['table_basic_setup_couriers'] = 'ems_basic_setup_couriers';
$config['table_basic_setup_vcolors'] = 'ems_basic_setup_vcolors';
$config['table_basic_setup_principal'] = 'ems_basic_setup_principal';
//customer setup
$config['table_csetup_customers'] = 'ems_csetup_customers';
$config['table_csetup_other_customers'] = 'ems_csetup_other_customers';
$config['table_csetup_balance_adjust'] = 'ems_balance_adjust';
//stock in
$config['table_stockin_varieties'] = 'ems_stockin_varieties';
$config['table_stockin_excess_inventory'] = 'ems_stockin_excess_inventory';
//payment
$config['table_payment_payment'] = 'ems_payment_payment';
//po
$config['table_sales_po'] = 'ems_sales_po';
$config['table_sales_po_details'] = 'ems_sales_po_details';

//stock out
$config['table_stockout'] = 'ems_stockout';
//delivery
$config['table_sales_po_delivery'] = 'ems_sales_po_delivery';
$config['table_sales_po_receives'] = 'ems_sales_po_receives';
$config['table_sales_po_returns'] = 'ems_sales_po_returns';
//primary market survey
$config['table_survey_primary'] = 'ems_survey_primary';
$config['table_survey_primary_customers'] = 'ems_survey_primary_customers';
$config['table_survey_primary_customer_survey'] = 'ems_survey_primary_customer_survey';
$config['table_survey_primary_quantity_survey'] = 'ems_survey_primary_quantity_survey';
$config['table_survey_product'] = 'ems_survey_product';

//tm setup
$config['table_setup_tm_seasons'] = 'ems_setup_tm_seasons';
$config['table_setup_tm_shifts'] = 'ems_setup_tm_shifts';
$config['table_setup_tm_fruit_picture'] = 'ems_setup_tm_fruit_picture';
$config['table_setup_tm_market_visit'] = 'ems_setup_tm_market_visit';//for ti
$config['table_setup_tm_market_visit_zi'] = 'ems_setup_tm_market_visit_zi';
$config['table_setup_tm_market_visit_zi_details'] = 'ems_setup_tm_market_visit_zi_details';
$config['table_setup_tm_market_visit_di'] = 'ems_setup_tm_market_visit_di';//for di different from ti zi
$config['table_setup_tm_market_visit_trainer'] = 'ems_setup_tm_market_visit_trainer';//same as di
//task management
$config['table_tm_farmers'] = 'ems_tm_farmers';
$config['table_tm_farmer_varieties'] = 'ems_tm_farmer_varieties';

$config['table_tm_visits_picture'] = 'ems_tm_visits_picture';
$config['table_tm_visits_fruit_picture'] = 'ems_tm_visits_fruit_picture';
$config['table_tm_visits_disease_picture'] = 'ems_tm_visits_disease_picture';
$config['table_tm_popular_variety'] = 'ems_tm_popular_variety';
$config['table_tm_popular_variety_details'] = 'ems_tm_popular_variety_details';
$config['table_tm_market_visit_ti'] = 'ems_tm_market_visit_ti';
$config['table_tm_market_visit_zi'] = 'ems_tm_market_visit_zi';
$config['table_tm_market_visit_solution_ti'] = 'ems_tm_market_visit_solution_ti';
$config['table_tm_market_visit_solution_zi'] = 'ems_tm_market_visit_solution_zi';
$config['table_tm_market_visit_di'] = 'ems_tm_market_visit_di';
$config['table_tm_market_visit_solution_di'] = 'ems_tm_market_visit_solution_di';
$config['table_tm_market_visit_trainer'] = 'ems_tm_market_visit_trainer';
$config['table_tm_market_visit_solution_trainer'] = 'ems_tm_market_visit_solution_trainer';

$config['table_tm_rnd_demo_setup'] = 'ems_tm_rnd_demo_setup';
$config['table_tm_rnd_demo_varieties'] = 'ems_tm_rnd_demo_varieties';
$config['table_tm_rnd_demo_picture'] = 'ems_tm_rnd_demo_picture';
$config['table_tm_rnd_demo_fruit_picture'] = 'ems_tm_rnd_demo_fruit_picture';
$config['table_tm_rnd_demo_disease_picture'] = 'ems_tm_rnd_demo_disease_picture';