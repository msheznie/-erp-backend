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

    'accepted' => 'حقل :attribute يجب قبوله.',
    'active_url' => 'حقل :attribute ليس عنوان URL صالحًا.',
    'after' => 'حقل :attribute يجب أن يكون تاريخًا بعد :date.',
    'after_or_equal' => 'حقل :attribute يجب أن يكون تاريخًا بعد :date أو مساويًا له.',
    'alpha' => 'حقل :attribute يجب أن يحتوي على أحرف فقط.',
    'alpha_dash' => 'حقل :attribute لا يجوز أن يحتوي إلا على أحرف وأرقام وشرطات.',
    'alpha_num' => 'حقل :attribute لا يجوز أن يحتوي إلا على أحرف وأرقام.',
    'array' => 'حقل :attribute يجب أن يكون مصفوفة.',
    'before' => 'حقل :attribute يجب أن يكون تاريخًا قبل :date.',
    'before_or_equal' => 'حقل :attribute يجب أن يكون تاريخًا يسبق :date أو مساويًا له.',
    'between' => [
        'numeric' => 'حقل :attribute يجب أن يكون بين :min و :max.',
        'file' => 'حقل :attribute يجب أن يكون بين :min و :max كيلوبايت.',
        'string' => 'حقل :attribute يجب أن يكون بين :min و :max من الأحرف.',
        'array' => 'حقل :attribute يجب أن يحتوي على ما بين :min و :max من العناصر.',
    ],
    'boolean' => 'حقل :attribute يجب أن يكون صحيح أو خطأ.',
    'confirmed' => 'تأكيد حقل :attribute غير مطابق.',
    'date' => 'حقل :attribute ليس تاريخًا صالحًا.',
    'date_format' => 'حقل :attribute لا يطابق التنسيق :format.',
    'different' => 'حقل :attribute و :other يجب أن يكونا مختلفين.',
    'digits' => 'حقل :attribute يجب أن يكون :digits أرقام.',
    'digits_between' => 'حقل :attribute يجب أن يكون بين :min و :max أرقام.',
    'dimensions' => 'حقل :attribute له أبعاد صورة غير صالحة.',
    'distinct' => 'حقل :attribute يحتوي على قيمة مكررة.',
    'email' => 'حقل :attribute يجب أن يكون عنوان بريد إلكتروني صالحًا.',
    'exists' => 'حقل :attribute المحدد غير صالح.',
    'file' => 'حقل :attribute يجب أن يكون ملفًا.',
    'filled' => 'حقل :attribute يجب أن يحتوي على قيمة.',
    'image' => 'حقل :attribute يجب أن يكون صورة.',
    'in' => 'حقل :attribute المحدد غير صالح.',
    'in_array' => 'حقل :attribute غير موجود في :other.',
    'integer' => 'حقل :attribute يجب أن يكون عددًا صحيحًا.',
    'ip' => 'حقل :attribute يجب أن يكون عنوان IP صالحًا.',
    'ipv4' => 'حقل :attribute يجب أن يكون عنوان IPv4 صالحًا.',
    'ipv6' => 'حقل :attribute يجب أن يكون عنوان IPv6 صالحًا.',
    'json' => 'حقل :attribute يجب أن يكون سلسلة JSON صالحة.',
    'max' => [
        'numeric' => 'حقل :attribute لا يجوز أن يكون أكبر من :max.',
        'file' => 'حقل :attribute لا يجوز أن يكون أكبر من :max كيلوبايت.',
        'string' => 'حقل :attribute لا يجوز أن يكون أكبر من :max من الأحرف.',
        'array' => 'حقل :attribute لا يجوز أن يحتوي على أكثر من :max عنصر.',
    ],
    'mimes' => 'حقل :attribute يجب أن يكون ملفًا من النوع: :values.',
    'mimetypes' => 'حقل :attribute يجب أن يكون ملفًا من النوع: :values.',
    'min' => [
        'numeric' => 'حقل :attribute يجب أن يكون على الأقل :min.',
        'file' => 'حقل :attribute يجب أن يكون على الأقل :min كيلوبايت.',
        'string' => 'حقل :attribute يجب ألا يقل عن :min حرف.',
        'array' => 'حقل :attribute يجب أن يحتوي على الأقل على :min عنصر.',
    ],
    'not_in' => 'حقل :attribute المحدد غير صالح.',
    'numeric' => 'حقل :attribute يجب أن يكون رقمًا.',
    'present' => 'حقل :attribute يجب أن يكون موجودًا.',
    'regex' => 'تنسيق حقل :attribute غير صالح.',
    'required' => 'حقل :attribute مطلوب.',
    'required_if' => 'حقل :attribute مطلوب عندما :other هو :value.',
    'required_unless' => 'حقل :attribute مطلوب إلا إذا كان :other في :values.',
    'required_with' => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_with_all' => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_without' => 'حقل :attribute مطلوب عندما :values غير موجودة.',
    'required_without_all' => 'حقل :attribute مطلوب في حالة عدم وجود أي من :values.',
    'same' => 'حقل :attribute و :other يجب أن يتطابقا.',
    'size' => [
        'numeric' => 'حقل :attribute يجب أن يكون :size.',
        'file' => 'حقل :attribute يجب أن يكون :size كيلوبايت.',
        'string' => 'حقل :attribute يجب أن يكون :size حرف.',
        'array' => 'حقل :attribute يجب أن يحتوي على :size عنصر.',
    ],
    'string' => 'حقل :attribute يجب أن يكون سلسلة.',
    'timezone' => 'حقل :attribute يجب أن يكون منطقة صالحة.',
    'unique' => 'حقل :attribute تم استخدامه بالفعل.',
    'uploaded' => 'فشل تحميل حقل :attribute.',
    'url' => 'تنسيق حقل :attribute غير صالح.',

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
            'rule-name' => 'رسالة-مخصصة',
        ],
        'select' => [
            'select' => 'حقل :attribute مطلوب.'
        ],
        'reportType' => [
            'required' => 'حقل نوع التقرير مطلوب.',
            'not_in' => 'حقل نوع التقرير مطلوب.'
        ],
        'reportType.*' => [
            'required' => 'حقل نوع التقرير مطلوب.',
            'not_in' => 'حقل نوع التقرير مطلوب.'
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
        'locationFrom' => 'من موقع',
        'locationTo' => 'إلى موقع',
        'companyFinanceYearID' => 'السنة المالية',
        'companyFinancePeriodID' => 'الفترة المالية',
        'tranferDate' => 'تاريخ التحويل',
        'companyToSystemID' => 'إلى شركة',
        'companyFromSystemID' => 'من شركة',
        'wareHouseFrom' => 'من مستودع',
        'wareHouseLocation' => 'موقع المستودع',
        'purchaseReturnLocation' => 'الموقع',
        'serviceLineSystemID' => 'القسم',
        'ReturnDate' => 'تاريخ الإسترجاع',
        'purchaseReturnDate' => 'تاريخ إسترجاع المشتريات',
        'issueDate' => 'تاريخ الصرف',
        'receivedDate' => 'تاريخ الإستلام',
        'issueRefNo' => 'رقم المرجع',
        'refNo' => 'رقم المرجع',
        'comment' => 'تعليق',
        'comments' => 'تعليقات',
        'customerSystemID' => 'العميل',
        'customerID' => 'العميل',
        'issueType' => 'نوع الصرف',
        'ReturnType' => 'نوع الإسترجاع',
        'ReturnRefNo' => 'رقم المرجع',
        'purchaseReturnRefNo' => 'رقم المرجع',
        'narration' => 'ملاحظة',
        'supplierID' => 'المورد',
        'supplierTransactionCurrencyID' => 'العملة',
        'stockAdjustmentDate' => 'تاريخ المستند',
        'location' => 'الموقع',
        'priority' => 'الأولولية',
        'wareHouseSystemCode' => 'مستودع',
        'companySystemID' => 'الشركة',
        'documentDate' => 'تاريخ',
        'bankRecAsOf' => 'اعتبارًا من تاريخ',
        'bankAccountID' => 'حساب البنك',
        'bankID' => 'البنك',
        'templatesMasterAutoID' => 'قالب',
        'budgetTransferFormAutoID' => 'معرف تحويل الميزانية',
        'fromTemplateDetailID' => 'من تفاصيل القالب',
        'fromServiceLineSystemID' => 'من القسم',
        'fromChartOfAccountSystemID' => 'من رمز الحساب',
        'toTemplateDetailID' => 'إلى تفاصيل القالب',
        'toServiceLineSystemID' => 'إلى قسم',
        'toChartOfAccountSystemID' => 'إلى رقم الحساب',
        'adjustmentAmountRpt' => 'المبلغ ',
        'remarks' => 'ملاحظة',
        'templateMasterID' => 'قالب',
        'processPeriod' => 'الشهر',
        'empType' => 'النوع',
        'expenseClaimCategoriesAutoID' => 'الفئة',
        'yearID' => 'السنة',
        'reportTypeID' => 'معرف نوع التقرير',
        'chartOfAccountSystemID' => 'رمز الحساب العام',
        'accountCurrencyID' => 'العملة',
        'financeCategorySub' => 'الفئة المالية الفرعية',
        'primaryCompanySystemID' => 'الشركة الأساسية',
        'financeCategoryMaster' => 'الفئة المالية',
        'wareHouseDescription' => 'الوصف',
        'counterCode'   => 'رمز صندوق المحاسبة',
        'counterName'   => 'اسم صندوق المحاسبة',
        'companyID'   => 'الشركة',
        'companyId'   => 'الشركة',
        'startingBalance_transaction' => 'الرصيد الإفتتاحي',
        'endingBalance_transaction' => 'الرصيد الختامي',
        'counterID' => 'صندوق المحاسبة',
        'secondaryItemCode' => 'رقم الجزء / الرقم المرجعي',
        'itemDescription' => 'وصف المادة',
        'unit' => 'وحدة القياس',
        'shiftID' => 'الوردية',
        'wareHouseAutoID' => 'المنفذ',
        'supCategoryICVMasterID' => 'فئة إعتماد الموردين',
        'supCategorySubICVID' => 'الفئة الفرعية لإعتماد الموردين',
        'isLCCYN' => 'محلي',
        'COSTGLCODESystemID' => 'حساب التكلفة',
        'ACCDEPGLCODESystemID' => 'حساب الإهلاك',
        'DEPGLCODESystemID' => 'حساب الإهلاك',
        'DISPOGLCODESystemID' => 'حساب التخلص',
        'financeCatDescription' => 'وصف الفئة',
        'catDescription' => 'وصف الفئة',
        'report_master_id' => 'التقرير',
        'BPVchequeDate' => 'تاريخ الشيك',
        'BPVdate' => 'تاريخ فاتورة الدفع',
        'invoiceType' => 'نوع الدفع',
        'BPVNarration' => 'ملاحظة',
        'BPVbank' => 'البنك',
        'BPVAccount' => 'حساب البنك',
        'BPVbankCurrency' => 'العملة',
        'supplierTransCurrencyID' => 'العملة',
        'BPVsupplierID' => 'المورد',
        'directPaymentPayeeEmpID' => 'المدفوع له',
        'directPaymentPayee' => 'أخر',
        'asOfDate' => 'حتى تاريخ',
        'currencyID' => 'معرف العملة',
        'warehouse' => 'المستودع',
        'fromDate' => 'من تاريخ',
        'toDate' => 'إلى تاريخ',
        'Items' => 'المواد',
        'reportType' => 'نوع التقرير',
        'segment' => 'القطاع',
        'suppliers' => 'المورّدون',
        'supplierGroup' => 'مجموعة المورّدين',
        'supEmpId' => 'معرّف موظف المورّد'
    ],

];
