<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => explode(',', (string) env('LOG_STACK', 'single')),
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'audit' => [
            'driver' => 'daily',
            'path' => storage_path('logs/audit.log'),
            'level' => 'info',
            'days' => 14,
        ],

        'audit_log' => [
            'driver' => 'daily',
            'path' => storage_path('logs/audit.log'),
            'level' => 'info',
            'days' => 14,
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => env('LOG_DAILY_DAYS', 14),
            'replace_placeholders' => true,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => env('LOG_SLACK_USERNAME', 'Laravel Log'),
            'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => env('LOG_SYSLOG_FACILITY', LOG_USER),
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'birthday_wishes_service' => [
            'driver' => 'single',
            'path' => storage_path('logs/birthday_wishes_service.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'notification_service' => [
            'driver' => 'single',
            'path' => storage_path('logs/notification_service.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'absent_notification' => [
            'driver' => 'single',
            'path' => storage_path('logs/absent-notification.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'account_recivable_report' => [
            'driver' => 'single',
            'path' => storage_path('logs/account-recivable-report.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'accounts_payable_ledger_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/accounts-payable-ledger-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'accounts_receivable_ledger_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/accounts-receivable-ledger-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'accumulated_dep_job' => [
            'driver' => 'single',
            'path' => storage_path('logs/accumulated-dep-job.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'after_document_created' => [
            'driver' => 'single',
            'path' => storage_path('logs/after-document-created.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'approval_setup' => [
            'driver' => 'single',
            'path' => storage_path('logs/approval-setup.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'approve_bulk_document' => [
            'driver' => 'single',
            'path' => storage_path('logs/approve-bulk-document.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'asset_costing_bulk_insert' => [
            'driver' => 'single',
            'path' => storage_path('logs/asset-costing-bulk-insert.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'attendance_cross_day_job_service' => [
            'driver' => 'single',
            'path' => storage_path('logs/attendance-cross-day-job-service.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'attendance_job_service' => [
            'driver' => 'single',
            'path' => storage_path('logs/attendance-job-service.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'bank_ledger_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/bank-ledger-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'bank_statement_match' => [
            'driver' => 'single',
            'path' => storage_path('logs/bank-statement-match.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'budget_addition_adjustment_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/budget-addition-adjustment-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'budget_adjustment_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/budget-adjustment-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'budget_cutoff_po' => [
            'driver' => 'single',
            'path' => storage_path('logs/budget-cutoff-po.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'budget_deadline_notification' => [
            'driver' => 'single',
            'path' => storage_path('logs/budget-deadline-notification.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'budget_submission_deadline_reached_notification' => [
            'driver' => 'single',
            'path' => storage_path('logs/budget-submission-deadline-reached-notification.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'create_console_jv_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/create-console-jv-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'create_credit_note' => [
            'driver' => 'single',
            'path' => storage_path('logs/create-credit-note.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'create_customer_invoice_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/create-customer-invoice-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'create_payment_voucher' => [
            'driver' => 'single',
            'path' => storage_path('logs/create-payment-voucher.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'create_receipt_matching' => [
            'driver' => 'single',
            'path' => storage_path('logs/create-receipt-matching.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'create_receipt_voucher_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/create-receipt-voucher-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'create_stock_receive_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/create-stock-receive-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'create_supplier_invoice_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/create-supplier-invoice-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'create_user_web_hook' => [
            'driver' => 'single',
            'path' => storage_path('logs/create-user-web-hook.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'customer_invoice_bulk_insert' => [
            'driver' => 'single',
            'path' => storage_path('logs/customer-invoice-bulk-insert.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'delegation' => [
            'driver' => 'single',
            'path' => storage_path('logs/delegation.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'department_budget_planning_details_process' => [
            'driver' => 'single',
            'path' => storage_path('logs/department-budget-planning-details-process.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'department_budget_process' => [
            'driver' => 'single',
            'path' => storage_path('logs/department-budget-process.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'depreciation_amend_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/depreciation-amend-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'depreciation_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/depreciation-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'emp_create_profile_service' => [
            'driver' => 'single',
            'path' => storage_path('logs/emp-create-profile-service.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'emp_designation_update_notification' => [
            'driver' => 'single',
            'path' => storage_path('logs/emp-designation-update-notification.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'employee_ledger_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/employee-ledger-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'general_ledger_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/general-ledger-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'generate_bank_reconciliation' => [
            'driver' => 'single',
            'path' => storage_path('logs/generate-bank-reconciliation.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'hr_document_service' => [
            'driver' => 'single',
            'path' => storage_path('logs/hr-document-service.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'item_assign_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/item-assign-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'item_ledger_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/item-ledger-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'job_error_log' => [
            'driver' => 'single',
            'path' => storage_path('logs/job-error-log.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'leave_accrual_service' => [
            'driver' => 'single',
            'path' => storage_path('logs/leave-accrual-service.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'leave_carry_forward_service' => [
            'driver' => 'single',
            'path' => storage_path('logs/leave-carry-forward-service.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'mr_bulk_item' => [
            'driver' => 'single',
            'path' => storage_path('logs/mr-bulk-item.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'payment_released_to_supplier' => [
            'driver' => 'single',
            'path' => storage_path('logs/payment-released-to-supplier.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'pdc_double_entry_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/pdc-double-entry-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'po_detail_excel_export' => [
            'driver' => 'single',
            'path' => storage_path('logs/po-detail-excel-export.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'po_updated' => [
            'driver' => 'single',
            'path' => storage_path('logs/po-updated.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'pr_bulk_item' => [
            'driver' => 'single',
            'path' => storage_path('logs/pr-bulk-item.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'receipt_voucher_api_confirmation_logs' => [
            'driver' => 'single',
            'path' => storage_path('logs/receipt-voucher-api-confirmation-logs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'recurring_voucher_service' => [
            'driver' => 'single',
            'path' => storage_path('logs/recurring-voucher-service.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'return_to_work_service' => [
            'driver' => 'single',
            'path' => storage_path('logs/return-to-work-service.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'rollback_approval_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/rollback-approval-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'sales_order_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/sales-order-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'stage_create_customer_invoice' => [
            'driver' => 'single',
            'path' => storage_path('logs/stage-create-customer-invoice.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'stage_create_receipt_voucher' => [
            'driver' => 'single',
            'path' => storage_path('logs/stage-create-receipt-voucher.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'stock_count_job' => [
            'driver' => 'single',
            'path' => storage_path('logs/stock-count-job.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'supplier_statement_sent' => [
            'driver' => 'single',
            'path' => storage_path('logs/supplier-statement-sent.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'tax_ledger_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/tax-ledger-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'travel_request_service' => [
            'driver' => 'single',
            'path' => storage_path('logs/travel-request-service.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'unbilled_grv_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/unbilled-grv-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'update_missing_docs' => [
            'driver' => 'single',
            'path' => storage_path('logs/update-missing-docs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'upload_bank_statement' => [
            'driver' => 'single',
            'path' => storage_path('logs/upload-bank-statement.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'warehouse_item_update_jobs' => [
            'driver' => 'single',
            'path' => storage_path('logs/warehouse-item-update-jobs.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'web_push' => [
            'driver' => 'single',
            'path' => storage_path('logs/web-push.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],



    ],

];
