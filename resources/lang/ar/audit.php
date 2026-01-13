<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Audit Log Translation Lines (Arabic)
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for audit log events and messages
    |
    */

    // Event Types
    'login' => 'تسجيل الدخول',
    'logout' => 'تسجيل الخروج',
    'login_failed' => 'فشل تسجيل الدخول',
    'token_expired' => 'انتهت صلاحية الجلسة',
    'session_expired' => 'انتهت صلاحية الجلسة',

    // Status
    'success' => 'نجح',
    'failed' => 'فشل',
    'expired' => 'منتهي الصلاحية',

    // Failure Reasons
    'invalid_credentials' => 'بيانات اعتماد غير صحيحة',
    'account_locked' => 'تم قفل الحساب',
    'login_disabled' => 'تم تعطيل تسجيل الدخول',
    'employee_not_found' => 'الموظف غير موجود',
    'employee_discharged' => 'تم تسريح الموظف',
    'account_not_activated' => 'الحساب غير مفعل',
    'employee_inactive' => 'الموظف غير نشط',
    'token_validation_failed' => 'فشل التحقق من الرمز',
    'invalid_or_expired_login_token' => 'رمز تسجيل الدخول غير صالح أو منتهي الصلاحية',
    'token_creation_failed' => 'فشل إنشاء الرمز',

    // General
    'unknown' => 'مجهول',
    'unknown_os' => 'مجهول أوس',
    'unknown_browser' => 'مجهول المتصفح',
    'unknown_ip' => 'مجهول العنوان الإيبي',

    // Operating Systems
    'windows' => 'نظام ويندوز',
    'macos' => 'ماك أو إس',
    'linux' => 'لينكس',
    'android' => 'أندرويد',
    'ios' => 'آي أو إس',

    // Browsers
    'chrome' => 'شريط العتاد',
    'firefox' => 'فايرفوكس',
    'safari' => 'سفاري',
    'edge' => 'أيجد',
    
    // Navigation
    'unknown_screen' => 'شاشة غير معروفة',
    
    // Access Types
    'read' => 'قراءة',
    'create' => 'إنشاء',
    'edit' => 'تعديل',
    'delete' => 'حذف',
    'print' => 'طباعة',
    'export' => 'تصدير',
    
    // Audit Log Narrations - Exact patterns from original calls (Arabic translations)
    // Static narrations
    'employee_assigned_to_department' => 'تم تعيين الموظف إلى القسم',
    'department_employee_assignment_updated' => 'تم تحديث تعيين موظف القسم',
    'employee_removed_from_department' => 'تم إزالة الموظف من القسم',
    'budget_planning_detail_record_has_been_created' => 'تم إنشاء سجل تفاصيل تخطيط الميزانية',
    'budget_planning_detail_record_has_been_updated' => 'تم تحديث سجل تفاصيل تخطيط الميزانية',
    'budget_planning_detail_record_has_been_deleted' => 'تم حذف سجل تفاصيل تخطيط الميزانية',
    'segment_assigned_to_department' => 'تم تعيين القطاع إلى القسم',
    'department_segment_assignment_updated' => 'تم تحديث تعيين قطاع القسم',
    'segment_removed_from_department' => 'تم إزالة القطاع من القسم',
    'hod_action_has_been_added_to_workflow_configuration' => 'تمت إضافة إجراء HOD إلى تكوين سير العمل',
    'hod_action_has_been_deleted_during_workflow_update' => 'تم حذف إجراء HOD أثناء تحديث سير العمل',
    'budget_template_assigned_to_department' => 'تم تعيين قالب الميزانية إلى القسم',
    'department_budget_template_updated' => 'تم تحديث قالب ميزانية القسم',
    'department_budget_template_deleted' => 'تم حذف قالب ميزانية القسم',
    'budget_template_default_status_updated' => 'تم تحديث حالة قالب الميزانية الافتراضية',
    'budget_template_link_request_amount_updated' => 'تم تحديث مبلغ طلب ارتباط قالب الميزانية',
    'asset_finance_category_has_updated' => 'تم تحديث فئة التمويل للأصول',
    'budget_controls_updated_for_user' => 'تم تحديث ضوابط الميزانية للمستخدم',
    'budget_template_column_added' => 'تمت إضافة عمود قالب الميزانية',
    'budget_template_column_updated' => 'تم تحديث عمود قالب الميزانية',
    'budget_template_column_deleted' => 'تم حذف عمود قالب الميزانية',
    'budget_template_column_removed_from_template' => 'تمت إزالة عمود قالب الميزانية من القالب',
    
    // Dynamic narrations (with {variable} placeholder)
    // Variable position matches English structure
    'department_budget_planning_variable_has_been_updated' => 'تخطيط ميزانية القسم {variable} تم تحديثه',
    'time_extension_request_variable_has_been_created' => 'طلب تمديد الوقت {variable} تم إنشاؤه',
    'time_extension_request_variable_has_been_updated' => 'طلب تمديد الوقت {variable} تم تحديثه',
    'time_extension_request_variable_has_been_deleted' => 'طلب تمديد الوقت {variable} تم حذفه',
    'time_extension_request_variable_has_been_accepted' => 'طلب تمديد الوقت {variable} تم قبوله',
    'variable_has_updated' => '{variable} تم التحديث',
    'segment_master_variable_has_been_deleted' => 'رئيس القطاع {variable} تم حذفه',
    'segment_master_variable_has_been_updated' => 'رئيس القطاع {variable} تم تحديثه',
    'department_master_variable_has_been_created' => 'رئيس القسم {variable} تم إنشاؤه',
    'department_master_variable_has_been_updated' => 'رئيس القسم {variable} تم تحديثه',
    'department_master_variable_has_been_deleted' => 'رئيس القسم {variable} تم حذفه',
    'attribute_variable_has_deleted' => 'السمة {variable} تم حذفها',
    'attribute_variable_has_been_deleted' => 'السمة {variable} تم حذفها',
    'attribute_variable_has_been_updated' => 'السمة {variable} تم تحديثها',
    'attribute_dropdown_value_variable_has_been_updated' => 'قيمة القائمة المنسدلة للسمة {variable} تم تحديثها',
    'workflow_configuration_variable_has_been_created' => 'تكوين سير العمل {variable} تم إنشاؤه',
    'workflow_configuration_variable_has_been_updated' => 'تكوين سير العمل {variable} تم تحديثه',
    'workflow_configuration_variable_has_been_deleted' => 'تكوين سير العمل {variable} تم حذفه',
    'budget_template_variable_has_been_created' => 'قالب الميزانية {variable} تم إنشاؤه',
    'budget_template_variable_has_been_updated' => 'قالب الميزانية {variable} تم تحديثه',
    'budget_template_variable_has_been_deleted' => 'قالب الميزانية {variable} تم حذفه',
    'department_budget_planning_variable_has_been_created' => 'تخطيط ميزانية القسم {variable} تم إنشاؤه',
    'attribute_variable_has_created' => 'السمة {variable} تم إنشاؤها',
    'company_assign_variable_has_been_updated' => 'تعيين الشركة {variable} تم تحديثه',
    'company_assign_variable_has_been_created' => 'تعيين الشركة {variable} تم إنشاؤه',
    'company_assign_variable_has_been_deleted' => 'تعيين الشركة {variable} تم حذفه',
    'attribute_dropdown_value_variable_has_been_created' => 'قيمة القائمة المنسدلة للسمة {variable} تم إنشاؤها',
    'attribute_dropdown_value_variable_has_been_deleted' => 'قيمة القائمة المنسدلة للسمة {variable} تم حذفها',

    // User Group Audit Log Narrations
    'user_group_has_been_created' => 'مجموعة المستخدمين {variable} تم إنشاؤها',
    'user_group_has_updated' => 'مجموعة المستخدمين {variable} تم تحديثها',
    'user_group_has_been_deleted' => 'مجموعة المستخدمين {variable} تم حذفها',

    // Employee Navigation Assign Audit Log Narrations
    'employee_has_been_assigned_to_user_group' => 'تم تعيين الموظف {variable} إلى مجموعة المستخدمين',
    'employee_has_been_unassigned_from_user_group' => 'تم إلغاء تعيين الموظف {variable} من مجموعة المستخدمين',
];

