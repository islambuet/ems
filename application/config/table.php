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
$config['table_system_assigned_group'] = 'system_assigned_group';
$config['table_system_assigned_area'] = 'system_assigned_area';

$config['table_system_user_group'] = 'system_user_group';
$config['table_system_task'] = 'system_task';
$config['table_system_user_group_role'] = 'system_user_group_role';
$config['table_history'] = 'history';
//location setup
$config['table_setup_location_divisions'] = 'divisions';
$config['table_setup_location_zones'] = 'zones';
$config['table_setup_location_territories'] = 'territories';
$config['table_setup_location_districts'] = 'districts';
$config['table_setup_location_upazillas'] = 'upazillas';
$config['table_setup_location_unions'] = 'unions';
//crop classification
$config['table_setup_classification_crops'] = 'crops';
$config['table_setup_classification_crop_types'] = 'crop_types';
$config['table_setup_classification_varieties'] = 'varieties';
$config['table_setup_classification_vpack_size'] = 'variety_pack_size';
$config['table_setup_classification_variety_price'] = 'variety_price';
$config['table_setup_classification_variety_bonus'] = 'variety_bonus';
$config['table_setup_classification_variety_bonus_details'] = 'variety_bonus_details';
//basic setup
$config['table_basic_setup_warehouse'] = 'basic_setup_warehouse';
$config['table_basic_setup_warehouse_crops'] = 'basic_setup_warehouse_crops';
$config['table_basic_setup_bank'] = 'basic_setup_bank';
$config['table_basic_setup_arm_bank'] = 'basic_setup_arm_bank';
$config['table_basic_setup_arm_bank_accounts'] = 'basic_setup_arm_bank_accounts';
$config['table_basic_setup_fiscal_year'] = 'basic_setup_fiscal_year';
$config['table_basic_setup_competitor'] = 'basic_setup_competitor';
//customer setup
$config['table_csetup_customers'] = 'csetup_customers';
$config['table_csetup_other_customers'] = 'csetup_other_customers';
//stock in
$config['table_stockin_varieties'] = 'stockin_varieties';
$config['table_stockin_excess_inventory'] = 'stockin_excess_inventory';
//payment
$config['table_payment_payment'] = 'payment_payment';
//po
$config['table_sales_po'] = 'sales_po';
$config['table_sales_po_details'] = 'sales_po_details';

//stock out
$config['table_stockout'] = 'stockout';