<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The :attribute must be accepted.',
    'active_url' => 'The :attribute is not a valid URL.',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.',
    'alpha' => 'The :attribute may only contain letters.',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, and dashes.',
    'alpha_num' => 'The :attribute may only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'date' => 'The :attribute is not a valid date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => 'The :attribute has invalid image dimensions.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => 'The :attribute must be a valid email address.',
    'exists' => 'The selected :attribute is invalid.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field must have a value.',
    'image' => 'The :attribute must be an image.',
    'in' => 'The selected :attribute is invalid.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => 'The :attribute must be an integer.',
    'ip' => 'The :attribute must be a valid IP address.',
    'ipv4' => 'The :attribute must be a valid IPv4 address.',
    'ipv6' => 'The :attribute must be a valid IPv6 address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => 'The :attribute may not be greater than :max characters.',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'mimetypes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'The :attribute field is required.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'not_in' => 'The selected :attribute is invalid.',
    'numeric' => 'The :attribute must be a number.',
    'present' => 'The :attribute field must be present.',
    'regex' => 'The :attribute format is invalid.',
    'required' => 'The :attribute field is required.',
    'required_if' => 'The :attribute field is required when :other is :value.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'The :attribute field is required when :values is present.',
    'required_with_all' => 'The :attribute field is required when :values is present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => 'The :attribute has already been taken.',
    'uploaded' => 'The :attribute failed to upload.',
    'url' => 'The :attribute format is invalid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'select' => [
            'select' => 'The :attribute field is required.'
        ],
        'reportType' => [
            'required' => 'The report type field is required.',
            'not_in' => 'The report type field is required.'
        ],
        'reportType.*' => [
            'required' => 'The report type field is required.',
            'not_in' => 'The report type field is required.'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'locationFrom' => 'Location from',
        'locationTo' => 'Location to',
        'companyFinanceYearID' => 'Finance year',
        'companyFinancePeriodID' => 'Finance period',
        'tranferDate' => 'Transfer Date',
        'companyToSystemID' => 'Company to',
        'companyFromSystemID' => 'Company from',
        'wareHouseFrom' => 'Warehouse',
        'wareHouseLocation' => 'Warehouse',
        'purchaseReturnLocation' => 'Location',
        'serviceLineSystemID' => 'Department',
        'ReturnDate' => 'Return date',
        'purchaseReturnDate' => 'Purchase return date',
        'issueDate' => 'Issue date',
        'receivedDate' => 'Received date',
        'issueRefNo' => 'Reference No',
        'refNo' => 'Reference No',
        'comment' => 'Comment',
        'comments' => 'Comments',
        'customerSystemID' => 'Customer',
        'customerID' => 'Customer',
        'issueType' => 'Issues Type',
        'ReturnType' => 'Return Type',
        'ReturnRefNo' => 'Reference No',
        'purchaseReturnRefNo' => 'Reference No',
        'narration' => 'Narration',
        'supplierID' => 'Supplier',
        'supplierTransactionCurrencyID' => 'Currency',
        'stockAdjustmentDate' => 'Document Date',
        'location' => 'Location',
        'priority' => 'Priority',
        'wareHouseSystemCode' => 'WareHouse',
        'companySystemID' => 'Company',
        'documentDate' => 'Document Date',
        'bankRecAsOf' => 'As of Date',
        'bankAccountID' => 'Bank Account',
        'bankID' => 'Bank',
        'templatesMasterAutoID' => 'Template',
        'budgetTransferFormAutoID' => 'Budget Transfer ID',
        'fromTemplateDetailID' => 'From Template Detail',
        'fromServiceLineSystemID' => 'From Department',
        'fromChartOfAccountSystemID' => 'From Account Code',
        'toTemplateDetailID' => 'To Template Detail',
        'toServiceLineSystemID' => 'To Department',
        'toChartOfAccountSystemID' => 'To Account Code',
        'adjustmentAmountRpt' => 'Amount ',
        'remarks' => 'Remarks',
        'templateMasterID' => 'Template',
        'processPeriod' => 'Month',
        'empType' => 'Type',
        'expenseClaimCategoriesAutoID' => 'Category',
        'yearID' => 'Year',
        'reportTypeID' => 'Report Type',
        'chartOfAccountSystemID' => 'GL Code',
        'accountCurrencyID' => 'Currency',
        'financeCategorySub' => 'Finance Sub Category',
        'primaryCompanySystemID' => 'Primary Company',
        'financeCategoryMaster' => 'Finance Category',
        'wareHouseDescription' => 'Description',
        'counterCode'   => 'Counter code',
        'counterName'   => 'Counter name',
        'companyID'   => 'Company',
        'companyId'   => 'Company',
        'startingBalance_transaction' => 'Starting Balance',
        'endingBalance_transaction' => 'Closing Balance',
        'counterID' => 'Counter',
        'secondaryItemCode' => 'Part No / Ref.Number',
        'itemDescription' => 'Item Description',
        'unit' => 'Unit of Measure',
        'shiftID' => 'Shift',
        'wareHouseAutoID' => 'Outlet',
        'supCategoryICVMasterID' => 'ICV Category',
        'supCategorySubICVID' => 'ICV Sub Category',
        'isLCCYN' => 'LCC',
        'COSTGLCODESystemID' => 'Cost Account',
        'ACCDEPGLCODESystemID' => 'Acc Depreciation Account',
        'DEPGLCODESystemID' => 'Depreciation Account',
        'DISPOGLCODESystemID' => 'Disposal Account',
        'financeCatDescription' => 'Category Description',
        'catDescription' => 'Category Description',
        'report_master_id' => 'Report Master',
        'BPVchequeDate' => 'Cheque Date',
        'BPVdate' => 'Pay Invoice Date',
        'invoiceType' => 'Payment Type',
        'BPVNarration' => 'Narration',
        'BPVbank' => 'Bank',
        'BPVAccount' => 'Bank Account',
        'BPVbankCurrency' => 'Currency',
        'supplierTransCurrencyID' => 'Currency',
        'BPVsupplierID' => 'Supplier',
        'directPaymentPayeeEmpID' => 'Payee',
        'directPaymentPayee' => 'Other',
        'fromDate' => 'From Date',
        'toDate' => 'To Date',
        'Items' => 'Items',
        'reportType' => 'Report Type'
    ],

];
