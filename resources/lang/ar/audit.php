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
];

