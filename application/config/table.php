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
$config['table_setup_classification_variety_bonus'] = 'ems_variety_bonus';
$config['table_setup_classification_variety_bonus_details'] = 'ems_variety_bonus_details';
//basic setup
$config['table_basic_setup_warehouse'] = 'ems_basic_setup_warehouse';
$config['table_basic_setup_warehouse_crops'] = 'ems_basic_setup_warehouse_crops';
$config['table_basic_setup_bank'] = 'ems_basic_setup_bank';
$config['table_basic_setup_arm_bank'] = 'ems_basic_setup_arm_bank';
$config['table_basic_setup_arm_bank_accounts'] = 'ems_basic_setup_arm_bank_accounts';
$config['table_basic_setup_fiscal_year'] = 'ems_basic_setup_fiscal_year';
$config['table_basic_setup_competitor'] = 'ems_basic_setup_competitor';
$config['table_basic_setup_couriers'] = 'ems_basic_setup_couriers';
//customer setup
$config['table_csetup_customers'] = 'ems_csetup_customers';
$config['table_csetup_other_customers'] = 'ems_csetup_other_customers';
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