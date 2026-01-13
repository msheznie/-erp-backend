<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Audit Log Translation Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for audit log events and messages
    |
    */

    // Event Types
    'login' => 'Login',
    'logout' => 'Logout',
    'login_failed' => 'Login Failed',
    'token_expired' => 'Token Expired',
    'session_expired' => 'Session Expired',

    // Status
    'success' => 'Success',
    'failed' => 'Failed',
    'expired' => 'Expired',

    // Failure Reasons
    'invalid_credentials' => 'Invalid Credentials',
    'account_locked' => 'Account Locked',
    'login_disabled' => 'Login Disabled',
    'employee_not_found' => 'Employee Not Found',
    'employee_discharged' => 'Employee Discharged',
    'account_not_activated' => 'Account Not Activated',
    'employee_inactive' => 'Employee Inactive',
    'token_validation_failed' => 'Token Validation Failed',
    'invalid_or_expired_login_token' => 'Invalid or Expired Login Token',
    'token_creation_failed' => 'Token Creation Failed',

    // General
    'unknown' => 'Unknown',
    'unknown_os' => 'Unknown OS',
    'unknown_browser' => 'Unknown Browser',
    'unknown_ip' => 'Unknown IP',

    // Operating Systems
    'windows' => 'Windows',
    'macos' => 'macOS',
    'linux' => 'Linux',
    'android' => 'Android',
    'ios' => 'iOS',

    // Browsers
    'chrome' => 'Chrome',
    'firefox' => 'Firefox',
    'safari' => 'Safari',
    'edge' => 'Edge',
    
    // Navigation
    'unknown_screen' => 'Unknown Screen',
    
    // Access Types
    'read' => 'Read',
    'create' => 'Create',
    'edit' => 'Edit',
    'delete' => 'Delete',
    'print' => 'Print',
    'export' => 'Export',
    
    // Audit Log Narrations - Exact patterns from original calls
    // Static narrations
    'employee_assigned_to_department' => 'Employee assigned to department',
    'department_employee_assignment_updated' => 'Department employee assignment updated',
    'employee_removed_from_department' => 'Employee removed from department',
    'budget_planning_detail_record_has_been_created' => 'Budget planning detail record has been created',
    'budget_planning_detail_record_has_been_updated' => 'Budget planning detail record has been updated',
    'budget_planning_detail_record_has_been_deleted' => 'Budget planning detail record has been deleted',
    'segment_assigned_to_department' => 'Segment assigned to department',
    'department_segment_assignment_updated' => 'Department segment assignment updated',
    'segment_removed_from_department' => 'Segment removed from department',
    'hod_action_has_been_added_to_workflow_configuration' => 'HOD Action has been Added to Workflow Configuration',
    'hod_action_has_been_deleted_during_workflow_update' => 'HOD Action has been deleted during workflow update',
    'budget_template_assigned_to_department' => 'Budget template assigned to department',
    'department_budget_template_updated' => 'Department budget template updated',
    'department_budget_template_deleted' => 'Department budget template deleted',
    'budget_template_default_status_updated' => 'Budget template default status updated',
    'budget_template_link_request_amount_updated' => 'Budget template link request amount updated',
    'asset_finance_category_has_updated' => 'Asset Finance Category has updated',
    'budget_controls_updated_for_user' => 'Budget controls updated for user',
    'budget_template_column_added' => 'Budget template column added',
    'budget_template_column_updated' => 'Budget template column updated',
    'budget_template_column_deleted' => 'Budget template column deleted',
    'budget_template_column_removed_from_template' => 'Budget template column removed from template',
    
    // Dynamic narrations (with {variable} placeholder)
    'department_budget_planning_variable_has_been_updated' => 'Department Budget Planning {variable} has been updated',
    'time_extension_request_variable_has_been_created' => 'Time extension request {variable} has been created',
    'time_extension_request_variable_has_been_updated' => 'Time extension request {variable} has been updated',
    'time_extension_request_variable_has_been_deleted' => 'Time extension request {variable} has been deleted',
    'time_extension_request_variable_has_been_accepted' => 'Time extension request {variable} has been accepted',
    'variable_has_updated' => '{variable} has updated',
    'segment_master_variable_has_been_deleted' => 'Segment master {variable} has been deleted',
    'segment_master_variable_has_been_updated' => 'Segment master {variable} has been updated',
    'department_master_variable_has_been_created' => 'Department master {variable} has been created',
    'department_master_variable_has_been_updated' => 'Department master {variable} has been updated',
    'department_master_variable_has_been_deleted' => 'Department master {variable} has been deleted',
    'attribute_variable_has_deleted' => 'Attribute {variable} has deleted',
    'attribute_variable_has_been_deleted' => 'Attribute {variable} has been deleted',
    'attribute_variable_has_been_updated' => 'Attribute {variable} has been updated',
    'attribute_dropdown_value_variable_has_been_updated' => 'Attribute dropdown value {variable} has been updated',
    'workflow_configuration_variable_has_been_created' => 'Workflow Configuration {variable} has been created',
    'workflow_configuration_variable_has_been_updated' => 'Workflow Configuration {variable} has been updated',
    'workflow_configuration_variable_has_been_deleted' => 'Workflow Configuration {variable} has been deleted',
    'budget_template_variable_has_been_created' => 'Budget template {variable} has been created',
    'budget_template_variable_has_been_updated' => 'Budget template {variable} has been updated',
    'budget_template_variable_has_been_deleted' => 'Budget template {variable} has been deleted',
    'department_budget_planning_variable_has_been_created' => 'Department budget planning {variable} has been created',
    'attribute_variable_has_created' => 'Attribute {variable} has created',
    'company_assign_variable_has_been_updated' => 'Company Assign {variable} has been updated',
    'company_assign_variable_has_been_created' => 'Company Assign {variable} has been created',
    'company_assign_variable_has_been_deleted' => 'Company Assign {variable} has been deleted',
    'attribute_dropdown_value_variable_has_been_created' => 'Attribute dropdown value {variable} has been created',
    'attribute_dropdown_value_variable_has_been_deleted' => 'Attribute dropdown value {variable} has been deleted',

    // User Group Audit Log Narrations
    'user_group_has_been_created' => 'User group {variable} has been created',
    'user_group_has_updated' => 'User group {variable} has been updated',
    'user_group_has_been_deleted' => 'User group {variable} has been deleted',

    // Employee Navigation Assign Audit Log Narrations
    'employee_has_been_assigned_to_user_group' => 'Employee {variable} has been assigned to user group',
    'employee_has_been_unassigned_from_user_group' => 'Employee {variable} has been unassigned from user group',
];

