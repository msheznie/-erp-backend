<?php

namespace App\Services\API;

use App\helper\Helper;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\CurrencyMaster;
use App\Models\CustomerMaster;
use App\Models\CustomerMasterCategory;
use App\Models\DocumentMaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerMasterBulkUploadService
{
    private static function getTranslatedMessage($key, $fallback, $replace = []): string
    {
        $translated = trans($key, $replace);
        return ($translated !== $key) ? $translated : $fallback;
    }

    private static function normalizeRowKeys($row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            $cleanKey = trim($key);
            $cleanKey = preg_replace('/\*+$/', '', $cleanKey);
            $cleanKey = trim($cleanKey);
            // Trim string values
            if (is_string($value)) {
                $value = trim($value);
            }
            $normalized[$cleanKey] = $value;
        }

        return $normalized;
    }

    /**
     * Main entry point for processing customer bulk upload
     *
     * @param array $excelData
     * @param array $input
     * @param object $employee
     * @return array
     */
    public static function processBulkUpload($excelData, $input, $employee): array
    {
        $errorsByLine = [];
        $successCount = 0;

        if (empty($excelData) || count($excelData) == 0) {
            return [
                'successCount' => 0,
                'totalCount' => 0,
                'message' => self::getTranslatedMessage('custom.no_valid_records_found_in_excel', 'No valid records found in Excel file. Please ensure the file contains data rows.'),
                'errors' => [],
                'success' => false
            ];
        }

        // Maximum record limit check (e.g., 1000 records)
        $maxRecords = 1000;
        $totalCount = count($excelData);
        if ($totalCount > $maxRecords) {
            return [
                'successCount' => 0,
                'totalCount' => $totalCount,
                'message' => self::getTranslatedMessage('custom.maximum_record_limit_exceeded', "Maximum record limit exceeded. Please upload a maximum of {$maxRecords} records at a time."),
                'errors' => [],
                'success' => false
            ];
        }

        $allRowsEmpty = true;
        $secondaryCodesInUpload = []; // Track secondary codes within this upload for duplicate detection
        $rowHashes = []; // Track row content hashes for duplicate row detection

        foreach ($excelData as $index => $row) {
            $lineNumber = $index + 10;

            $normalizedRow = self::normalizeRowKeys($row);

            $rowHasData = false;
            foreach ($normalizedRow as $value) {
                if ($value !== null && $value !== '' && trim((string)$value) !== '') {
                    $rowHasData = true;
                    $allRowsEmpty = false;
                    break;
                }
            }

            if (!$rowHasData) {
                continue;
            }

            // Check for duplicate rows within the same upload
            $rowHash = md5(json_encode($normalizedRow));
            if (isset($rowHashes[$rowHash])) {
                $errorsByLine[$lineNumber] = [[
                    'field' => 'Row',
                    'message' => self::getTranslatedMessage('custom.duplicate_row_detected', 'Duplicate row detected in the upload file.'),
                    'value' => ''
                ]];
                continue;
            }
            $rowHashes[$rowHash] = true;

            $validationResult = self::validateCustomerRow($normalizedRow, $input, $lineNumber, $secondaryCodesInUpload);

            if (!$validationResult['isValid']) {
                $errorsByLine[$lineNumber] = $validationResult['errors'];
                continue;
            }

            $customerData = self::prepareCustomerData($normalizedRow, $input, $employee);

            $createResult = self::createCustomerRecord($customerData, $employee, $input['companySystemID']);

            if ($createResult['status']) {
                $successCount++;
            } else {
                $errorsByLine[$lineNumber] = [[
                    'field' => 'System',
                    'message' => $createResult['message'],
                    'value' => ''
                ]];
            }
        }

        if ($allRowsEmpty) {
            return [
                'successCount' => 0,
                'totalCount' => $totalCount,
                'message' => self::getTranslatedMessage('custom.no_valid_records_found_in_excel', 'No valid records found in Excel file. Please ensure the file contains data rows.'),
                'errors' => [],
                'success' => false
            ];
        }

        return self::generateResponse($successCount, $totalCount, $errorsByLine);
    }

    /**
     * Validate a single customer row
     *
     * @param array $row
     * @param array $input
     * @param int $lineNumber
     * @param array $secondaryCodesInUpload Reference to track secondary codes in current upload
     * @return array ['isValid' => bool, 'errors' => array]
     */
    private static function validateCustomerRow($row, $input, $lineNumber, &$secondaryCodesInUpload = []): array
    {
        $errors = [];

        $companyResult = self::validateCompany($input['companySystemID'] ?? null, $input);
        if (!$companyResult['valid']) {
            $errors[] = [
                'field' => 'Primary Company',
                'message' => $companyResult['message'],
                'value' => ''
            ];
        }

        if (empty($row['secondary_code']) || is_null($row['secondary_code'])) {
            $errors[] = [
                'field' => 'Secondary Code',
                'message' => self::getTranslatedMessage('custom.secondary_code_is_mandatory', 'Secondary Code is mandatory'),
                'value' => ''
            ];
        } else {
            $secondaryCode = trim((string)$row['secondary_code']);

            // Check for duplicate within the same upload
            if (isset($secondaryCodesInUpload[$secondaryCode])) {
                $errors[] = [
                    'field' => 'Secondary Code',
                    'message' => self::getTranslatedMessage('custom.secondary_code_duplicate_in_upload', 'Secondary Code is duplicated within the upload file.'),
                    'value' => $secondaryCode
                ];
            } else {
                $secondaryCodesInUpload[$secondaryCode] = true;
            }

            // Check for duplicate against existing records
            $existingCustomer = CustomerMaster::where('customerShortCode', $secondaryCode)->first();
            if ($existingCustomer) {
                $errors[] = [
                    'field' => 'Secondary Code',
                    'message' => self::getTranslatedMessage('custom.secondary_code_already_exists', 'Secondary Code already exists in the system.'),
                    'value' => $secondaryCode
                ];
            }
        }

        if (empty($row['customer_name']) || is_null($row['customer_name'])) {
            $errors[] = [
                'field' => 'Customer Name',
                'message' => self::getTranslatedMessage('custom.customer_name_is_mandatory', 'Customer Name is mandatory'),
                'value' => ''
            ];
        } else {
            $customerName = trim((string)$row['customer_name']);

            // Check if only numeric
            if (is_numeric($customerName)) {
                $errors[] = [
                    'field' => 'Customer Name',
                    'message' => self::getTranslatedMessage('custom.not_only_numeric_value', 'Customer Name cannot be only numeric'),
                    'value' => $customerName
                ];
            }

            // Check max length (e.g., 255 characters)
            $maxCustomerNameLength = 255;
            if (mb_strlen($customerName) > $maxCustomerNameLength) {
                $errors[] = [
                    'field' => 'Customer Name',
                    'message' => self::getTranslatedMessage('custom.customer_name_max_length_exceeded', "Customer Name cannot exceed {$maxCustomerNameLength} characters.", ['max' => $maxCustomerNameLength]),
                    'value' => $customerName
                ];
            }
        }

        $countryResult = self::validateCountry($row['country'] ?? null, true); // true for case-insensitive
        if (!$countryResult['valid']) {
            $errors[] = [
                'field' => 'Country',
                'message' => $countryResult['message'],
                'value' => $row['country'] ?? ''
            ];
        }

        $glAccountResult = self::validateChartOfAccount(
            $row['receivables_account'] ?? null,
            'Receivable Account',
            $input['companySystemID'] ?? null,
            3,
            'BS'
        );
        if (!$glAccountResult['valid']) {
            $errors[] = [
                'field' => 'Receivable Account',
                'message' => $glAccountResult['message'],
                'value' => $row['receivables_account'] ?? ''
            ];
        }

        $unbilledAccountResult = self::validateChartOfAccount(
            $row['unbilled_account'] ?? null,
            'Unbilled Account',
            $input['companySystemID'] ?? null,
            3,
            'BS'
        );
        if (!$unbilledAccountResult['valid']) {
            $errors[] = [
                'field' => 'Unbilled Account',
                'message' => $unbilledAccountResult['message'],
                'value' => $row['unbilled_account'] ?? ''
            ];
        }

        $advanceAccountResult = self::validateChartOfAccount(
            $row['advance_account'] ?? null,
            'Advance Account',
            $input['companySystemID'] ?? null,
            [3, 4, 5],
            'BS'
        );
        if (!$advanceAccountResult['valid']) {
            $errors[] = [
                'field' => 'Advance Account',
                'message' => $advanceAccountResult['message'],
                'value' => $row['advance_account'] ?? ''
            ];
        }

        $receivableAccountCode = $row['receivables_account'] ?? null;
        if ($receivableAccountCode && isset($row['unbilled_account']) &&
            $receivableAccountCode == $row['unbilled_account']) {
            $errors[] = [
                'field' => 'GL Accounts',
                'message' => self::getTranslatedMessage('custom.receivable_account_and_unbilled_account_cannot_be_same', 'Receivable Account and Unbilled Account cannot be the same'),
                'value' => $receivableAccountCode
            ];
        }

        $creditLimitResult = self::validateCreditLimit($row['credit_limit_omr'] ?? $row['credit_limit'] ?? null);
        if (!$creditLimitResult['valid']) {
            $errors[] = [
                'field' => 'Credit Limit (OMR)',
                'message' => $creditLimitResult['message'],
                'value' => $row['credit_limit_omr'] ?? $row['credit_limit'] ?? ''
            ];
        }

        $creditPeriodResult = self::validateCreditPeriod($row['credit_period'] ?? null);
        if (!$creditPeriodResult['valid']) {
            $errors[] = [
                'field' => 'Credit Period',
                'message' => $creditPeriodResult['message'],
                'value' => $row['credit_period'] ?? ''
            ];
        }

        if (!empty($row['category'])) {
            $categoryResult = self::validateCategory($row['category'], $input);
            if (!$categoryResult['valid']) {
                $errors[] = [
                    'field' => 'Category',
                    'message' => $categoryResult['message'],
                    'value' => $row['category']
                ];
            }
        }

        if (!empty($row['vat_eligible'])) {
            $vatEligible = trim((string)$row['vat_eligible']);
            $vatEligibleLower = strtolower($vatEligible);
            if ($vatEligibleLower !== 'yes' && $vatEligibleLower !== 'no') {
                $errors[] = [
                    'field' => 'VAT Eligible',
                    'message' => self::getTranslatedMessage('custom.vat_eligible_value_not_matching_with_system', 'The Vat Eligible values is not matching with system'),
                    'value' => $vatEligible
                ];
            }
        }

        // VAT Number dependency: If VAT Number is provided, VAT Eligible must be "Yes"
        if (!empty($row['vat_number']) && trim((string)$row['vat_number']) !== '') {
            $vatEligible = !empty($row['vat_eligible']) ? trim((string)$row['vat_eligible']) : '';
            $vatEligibleLower = strtolower($vatEligible);
            if ($vatEligibleLower !== 'yes') {
                $errors[] = [
                    'field' => 'VAT Number',
                    'message' => self::getTranslatedMessage('custom.vat_number_requires_vat_eligible', 'VAT Number requires VAT Eligible to be "Yes".'),
                    'value' => $row['vat_number']
                ];
            }
        }

        if (!empty($row['vat_percentage'])) {
            if (!is_numeric($row['vat_percentage'])) {
                $errors[] = [
                    'field' => 'VAT Percentage',
                    'message' => self::getTranslatedMessage('custom.vat_percentage_should_numbers_only', 'VAT Percentage should numbers only'),
                    'value' => $row['vat_percentage']
                ];
            } else {
                // Validate VAT percentage decimal precision (max 2 decimal places)
                $vatPercentage = (float)$row['vat_percentage'];
                $decimalPlaces = strlen(substr(strrchr((string)$vatPercentage, "."), 1));
                if ($decimalPlaces > 2) {
                    $errors[] = [
                        'field' => 'VAT Percentage',
                        'message' => self::getTranslatedMessage('custom.vat_percentage_decimal_precision', 'VAT Percentage cannot have more than 2 decimal places.'),
                        'value' => $row['vat_percentage']
                    ];
                }

                // Validate VAT percentage range (0-100)
                if ($vatPercentage < 0 || $vatPercentage > 100) {
                    $errors[] = [
                        'field' => 'VAT Percentage',
                        'message' => self::getTranslatedMessage('custom.customer_vat_percentage_range', 'VAT Percentage must be between 0 and 100.'),
                        'value' => $row['vat_percentage']
                    ];
                }
            }
        }

        if (!empty($row['currency'])) {
            $currencyResult = self::validateCurrency($row['currency'], true); // true for case-insensitive
            if (!$currencyResult['valid']) {
                $errors[] = [
                    'field' => 'Currency',
                    'message' => $currencyResult['message'],
                    'value' => $row['currency']
                ];
            }
        }

        // Validate web address if provided
        if (!empty($row['web_address']) && trim((string)$row['web_address']) !== '') {
            $webAddressResult = self::validateWebAddress($row['web_address']);
            if (!$webAddressResult['valid']) {
                $errors[] = [
                    'field' => 'Web Address',
                    'message' => $webAddressResult['message'],
                    'value' => $row['web_address']
                ];
            }
        }

        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Prepare customer data array from Excel row
     *
     * @param array $row
     * @param array $input
     * @param object $employee
     * @return array
     */
    private static function prepareCustomerData($row, $input, $employee): array
    {
        $customerData = [];

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        $customerData['primaryCompanySystemID'] = $company->companySystemID;
        $customerData['primaryCompanyID'] = $company->CompanyID;

        $customerData['customerShortCode'] = trim((string)$row['secondary_code']);
        $customerData['CustomerName'] = trim((string)$row['customer_name']);

        if (!empty($row['report_title']) && !is_null($row['report_title'])) {
            $customerData['ReportTitle'] = trim((string)$row['report_title']);
        } else {
            $customerData['ReportTitle'] = trim((string)$row['customer_name']);
        }

        $customerData['customerAddress1'] = !empty($row['address_1']) ? trim((string)$row['address_1']) : null;
        $customerData['customerAddress2'] = !empty($row['address_2']) ? trim((string)$row['address_2']) : null;
        $customerData['customerCity'] = !empty($row['city']) ? trim((string)$row['city']) : null;
        $customerData['customerLogo'] = !empty($row['logo']) ? trim((string)$row['logo']) : null;
        $customerData['webAddress'] = !empty($row['web_address']) ? trim((string)$row['web_address']) : null;

        // Case-insensitive country lookup
        $country = CountryMaster::whereRaw('LOWER(countryName) = ?', [strtolower(trim($row['country']))])->first();
        $customerData['customerCountry'] = $country->countryID;

        $receivableAccountCode = $row['receivables_account'] ?? null;
        $receivableAccount = ChartOfAccount::where('AccountCode', $receivableAccountCode)->first();
        $customerData['custGLAccountSystemID'] = $receivableAccount->chartOfAccountSystemID;
        $customerData['custGLaccount'] = $receivableAccount->AccountCode;

        $unbilledAccount = ChartOfAccount::where('AccountCode', $row['unbilled_account'])->first();
        $customerData['custUnbilledAccountSystemID'] = $unbilledAccount->chartOfAccountSystemID;
        $customerData['custUnbilledAccount'] = $unbilledAccount->AccountCode;

        $advanceAccount = ChartOfAccount::where('AccountCode', $row['advance_account'])->first();
        $customerData['custAdvanceAccountSystemID'] = $advanceAccount->chartOfAccountSystemID;
        $customerData['custAdvanceAccount'] = $advanceAccount->AccountCode;

        $customerData['creditLimit'] = $row['credit_limit_omr'] ?? $row['credit_limit'] ?? 0;
        $customerData['creditDays'] = $row['credit_period'];

        if (!empty($row['category'])) {
            $category = CustomerMasterCategory::where('categoryDescription', $row['category'])
                ->whereHas('category_assigned', function ($query) use ($input) {
                    $query->when(isset($input['companySystemID']), function($query) use ($input){
                        $query->where('companySystemID', $input['companySystemID']);
                    })
                        ->when(isset($input['companySystemIDFilter']), function($query) use ($input){
                            $companyId = $input['companySystemIDFilter'];
                            $isGroup = \Helper::checkIsCompanyGroup($companyId);
                            if ($isGroup) {
                                $childCompanies = \Helper::getGroupCompany($companyId);
                            } else {
                                $childCompanies = [$companyId];
                            }
                            $query->whereIn('companySystemID', $childCompanies);
                        })
                        ->where('isAssigned', 1);
                })
                ->first();
            if ($category) {
                $customerData['customerCategoryID'] = $category->categoryID;
            }
        }

        $customerData['customerRegistrationNo'] = !empty($row['registration_no']) ? trim((string)$row['registration_no']) : null;

        $customerData['vatEligible'] = 0;
        if (!empty($row['vat_eligible'])) {
            $vatEligible = trim((string)$row['vat_eligible']);
            $vatEligibleLower = strtolower($vatEligible);
            if ($vatEligibleLower === 'yes') {
                $customerData['vatEligible'] = 1;
            } else {
                $customerData['vatEligible'] = 0;
            }
        }

        $customerData['vatNumber'] = !empty($row['vat_number']) ? trim((string)$row['vat_number']) : null;
        $customerData['vatPercentage'] = !empty($row['vat_percentage']) ? $row['vat_percentage'] : null;

        if (!empty($row['currency'])) {
            // Case-insensitive currency lookup
            $currency = CurrencyMaster::whereRaw('UPPER(CurrencyCode) = ?', [strtoupper(trim($row['currency']))])->first();
            if ($currency) {
                $customerData['defaultCurrencyID'] = $currency->currencyID;
            }
        }

        $customerData['isCustomerActive'] = 1;
        $customerData['createdPcID'] = gethostname();
        $customerData['createdUserID'] = $employee->empID;

        $document = DocumentMaster::where('documentID', 'CUSTM')->first();
        $customerData['documentSystemID'] = $document->documentSystemID;
        $customerData['documentID'] = $document->documentID;

        return $customerData;
    }

    /**
     * Create customer record
     *
     * @param array $customerData
     * @param object $employee
     * @param int $companySystemID
     * @return array
     */
    private static function createCustomerRecord($customerData, $employee, $companySystemID): array
    {
        DB::beginTransaction();
        try {
            // Generate Primary Code sequence within transaction to avoid duplicates
            $lastCustomer = CustomerMaster::lockForUpdate()->orderBy('customerCodeSystem', 'DESC')->first();
            $lastSerialOrder = 1;
            if(!empty($lastCustomer)){
                $lastSerialOrder = $lastCustomer->lastSerialOrder + 1;
            }

            $customerCode = 'C' . str_pad($lastSerialOrder, 7, '0', STR_PAD_LEFT);
            $customerData['lastSerialOrder'] = $lastSerialOrder;
            $customerData['CutomerCode'] = $customerCode;

            $customerMaster = CustomerMaster::create($customerData);

            if (isset($customerData['defaultCurrencyID']) && $customerData['defaultCurrencyID'] > 0) {
                $customerCurrency = new \App\Models\CustomerCurrency();
                $customerCurrency->customerCodeSystem = $customerMaster->customerCodeSystem;
                $customerCurrency->currencyID = $customerData['defaultCurrencyID'];
                $customerCurrency->isAssigned = -1;
                $customerCurrency->isDefault = -1;
                $customerCurrency->save();
            }

            DB::commit();
            return [
                'status' => true,
                'data' => $customerMaster
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Customer bulk upload creation error: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private static function generateResponse($successCount, $totalCount, $errorsByLine): array
    {
        $message = "Successfully uploaded {$successCount} customers out of {$totalCount}";

        if ($successCount == $totalCount) {
            $message = self::getTranslatedMessage('custom.all_record_upload_successfully', 'All records uploaded successfully');
        } elseif ($successCount == 0) {
            $message = self::getTranslatedMessage('custom.nothing_uploaded_try_agian', 'Nothing uploaded, please try again');
        }

        return [
            'successCount' => $successCount,
            'totalCount' => $totalCount,
            'message' => $message,
            'errors' => $errorsByLine,
            'success' => $successCount > 0
        ];
    }

    private static function validateCompany($companySystemID, $input): array
    {
        if (empty($companySystemID) || is_null($companySystemID)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.company_is_mandatory', 'Company is mandatory')
            ];
        }

        $company = Company::where('companySystemID', $companySystemID)->first();
        if (!$company) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.company_not_exist', 'Company not found')
            ];
        }

        $validatorResult = \Helper::checkCompanyForMasters($company->companySystemID);
        if (!$validatorResult['success']) {
            return [
                'valid' => false,
                'message' => $validatorResult['message'] ?: self::getTranslatedMessage('custom.company_not_valid', 'Company not valid')
            ];
        }

        return ['valid' => true];
    }

    private static function validateCountry($countryName, $caseInsensitive = false): array
    {
        if (empty($countryName) || is_null($countryName)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.country_is_mandatory', 'Country is mandatory')
            ];
        }

        $countryName = trim((string)$countryName);

        if ($caseInsensitive) {
            $country = CountryMaster::whereRaw('LOWER(countryName) = ?', [strtolower($countryName)])->first();
        } else {
            $country = CountryMaster::where('countryName', $countryName)->first();
        }

        if (!$country) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.country_not_matching_with_system', 'Country not matching with system')
            ];
        }

        return ['valid' => true];
    }

    private static function validateCurrency($currencyCode, $caseInsensitive = false): array
    {
        if (empty($currencyCode)) {
            return ['valid' => true]; // Currency is optional
        }

        $currencyCode = trim((string)$currencyCode);

        if ($caseInsensitive) {
            $currency = CurrencyMaster::whereRaw('UPPER(CurrencyCode) = ?', [strtoupper($currencyCode)])->first();
        } else {
            $currency = CurrencyMaster::where('CurrencyCode', $currencyCode)->first();
        }

        if (!$currency) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.currency_not_matching_with_system', 'Currency not matching with system')
            ];
        }

        return ['valid' => true];
    }

    private static function validateChartOfAccount($accountCode, $glAreaName, $companySystemID, $controlAccountSystemIDs, $blOrPl): array
    {
        if (empty($accountCode) || is_null($accountCode)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.gl_code_is_mandatory', "GL Code is mandatory ({$glAreaName})", ['area' => $glAreaName])
            ];
        }

        $query = ChartOfAccount::where('AccountCode', $accountCode);

        if ($companySystemID) {
            $query->whereHas('chartofaccount_assigned', function($q) use ($companySystemID) {
                $q->where('companySystemID', $companySystemID)
                    ->where('isAssigned', -1)
                    ->where('isActive', 1);
            });
        }

        if (is_array($controlAccountSystemIDs)) {
            $query->whereIn('controlAccountsSystemID', $controlAccountSystemIDs);
        } else {
            $query->where('controlAccountsSystemID', $controlAccountSystemIDs);
        }

        $query->where('catogaryBLorPL', $blOrPl);
        $query->where('controllAccountYN', 1);
        $query->where('isApproved', 1)->where('isActive', 1);

        $account = $query->first();

        if (!$account) {
            $baseAccount = ChartOfAccount::where('AccountCode', $accountCode)->first();
            if (!$baseAccount) {
                return [
                    'valid' => false,
                    'message' => self::getTranslatedMessage('custom.gl_code_not_matching_with_system', "The selected GL code not matching with system ({$glAreaName})", ['area' => $glAreaName])
                ];
            }

            if (!$baseAccount->isActive) {
                return [
                    'valid' => false,
                    'message' => self::getTranslatedMessage('custom.gl_code_not_active', "The Selected GL code not active ({$glAreaName})", ['area' => $glAreaName])
                ];
            }

            if (!$baseAccount->isApproved) {
                return [
                    'valid' => false,
                    'message' => self::getTranslatedMessage('custom.gl_code_not_approved', "The selected Gl code not approved ({$glAreaName})", ['area' => $glAreaName])
                ];
            }

            if ($baseAccount->controllAccountYN != 1) {
                return [
                    'valid' => false,
                    'message' => self::getTranslatedMessage('custom.gl_code_not_control_account', "The Selected GL code not control account ({$glAreaName})", ['area' => $glAreaName])
                ];
            }

            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.gl_type_not_matching', "The selected GL type is not matching ({$glAreaName})", ['area' => $glAreaName])
            ];
        }

        return ['valid' => true];
    }

    private static function validatePositiveNumber($value, $fieldName): array
    {
        if (empty($value) || is_null($value)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.field_is_mandatory', "{$fieldName} is mandatory", ['field' => $fieldName])
            ];
        }

        if (!is_numeric($value)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.field_should_be_numeric', "{$fieldName} should be numeric", ['field' => $fieldName])
            ];
        }

        if ($value < 0) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.field_should_be_positive', "The values {$fieldName} should positive number", ['field' => $fieldName])
            ];
        }

        return ['valid' => true];
    }

    private static function validateCreditPeriod($value): array
    {
        if (empty($value) || is_null($value)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.field_is_mandatory', 'Credit Period is mandatory', ['field' => 'Credit Period'])
            ];
        }

        if (!is_numeric($value)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.field_should_be_numeric', 'Credit Period should be numeric', ['field' => 'Credit Period'])
            ];
        }

        // Allow zero value for Credit Period
        if ($value < 0) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.field_should_be_positive', 'The values Credit Period should positive number', ['field' => 'Credit Period'])
            ];
        }

        // Check maximum number of digits (10 digits max)
        $valueString = (string)$value;
        $valueString = preg_replace('/\.0+$/', '', $valueString); // Remove trailing .0
        $valueString = preg_replace('/\./', '', $valueString); // Remove decimal point for digit count

        if (strlen($valueString) > 10) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.credit_period_cannot_exceed_maximum_digits', 'Credit Period cannot exceed the maximum allowed number of digits.')
            ];
        }

        return ['valid' => true];
    }

    private static function validateCreditLimit($value): array
    {
        if (empty($value) || is_null($value)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.field_is_mandatory', 'Credit Limit (OMR) is mandatory', ['field' => 'Credit Limit (OMR)'])
            ];
        }

        if (!is_numeric($value)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.field_should_be_numeric', 'Credit Limit (OMR) should be numeric', ['field' => 'Credit Limit (OMR)'])
            ];
        }

        if ($value < 0) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.field_should_be_positive', 'The values Credit Limit (OMR) should positive number', ['field' => 'Credit Limit (OMR)'])
            ];
        }

        // Check for overflow - maximum value (e.g., PHP_INT_MAX or a reasonable limit like 999999999999.99)
        $maxCreditLimit = 999999999999.99;
        if ($value > $maxCreditLimit) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.credit_limit_overflow', 'Credit Limit exceeds the maximum allowed value.')
            ];
        }

        // Check maximum number of digits (18 digits max including decimals)
        $valueString = (string)$value;
        $valueString = preg_replace('/\.0+$/', '', $valueString); // Remove trailing .0
        $parts = explode('.', $valueString);
        $integerPart = $parts[0];
        $decimalPart = isset($parts[1]) ? $parts[1] : '';

        // Total digits should not exceed 18
        if (strlen($integerPart) + strlen($decimalPart) > 18) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.credit_limit_cannot_exceed_maximum_digits', 'Credit Limit cannot exceed the maximum allowed number of digits.')
            ];
        }

        return ['valid' => true];
    }

    private static function validateWebAddress($url): array
    {
        if (empty($url)) {
            return ['valid' => true]; // Web address is optional
        }

        $url = trim((string)$url);

        // Only check for www. prefix, not http/https
        if (!preg_match('/^www\./i', $url)) {
            // If it doesn't start with www., validate as domain format
            if (!preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?)*$/i', $url)) {
                return [
                    'valid' => false,
                    'message' => self::getTranslatedMessage('custom.invalid_web_address', 'Invalid web address format. Must start with www. or be a valid domain.')
                ];
            }
        }

        return ['valid' => true];
    }

    private static function validateCategory($categoryDescription, $input): array
    {
        $category = CustomerMasterCategory::where('categoryDescription', $categoryDescription)
            ->whereHas('category_assigned', function ($query) use ($input) {
                $query->when(isset($input['companySystemID']), function($query) use ($input){
                    $query->where('companySystemID', $input['companySystemID']);
                })
                    ->when(isset($input['companySystemIDFilter']), function($query) use ($input){
                        $companyId = $input['companySystemIDFilter'];
                        $isGroup = \Helper::checkIsCompanyGroup($companyId);
                        if ($isGroup) {
                            $childCompanies = \Helper::getGroupCompany($companyId);
                        } else {
                            $childCompanies = [$companyId];
                        }
                        $query->whereIn('companySystemID', $childCompanies);
                    })
                    ->where('isAssigned', 1);
            })
            ->first();

        if (!$category) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.customer_category_not_matching_with_system', 'The customer Category not matching with system')
            ];
        }

        return ['valid' => true];
    }
}
