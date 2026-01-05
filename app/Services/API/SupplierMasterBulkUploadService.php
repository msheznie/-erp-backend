<?php

namespace App\Services\API;

use App\helper\Helper;
use App\helper\SupplierAssignService;
use App\Models\BankMemoSupplier;
use App\Models\BankMemoTypes;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\CurrencyMaster;
use App\Models\DocumentMaster;
use App\Models\SupplierCategory;
use App\Models\SupplierCurrency;
use App\Models\SupplierGroup;
use App\Models\SupplierImportance;
use App\Models\SupplierMaster;
use App\Models\suppliernature;
use App\Models\SupplierType;
use App\Models\PaymentType;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierMasterBulkUploadService
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
            $normalized[$cleanKey] = $value;
        }
        
        return $normalized;
    }

    /**
     * Main entry point for processing supplier bulk upload
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
        
        $totalCount = count($excelData);
        $allRowsEmpty = true;

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
            
            $validationResult = self::validateSupplierRow($normalizedRow, $input, $lineNumber);
            
            if (!$validationResult['isValid']) {
                $errorsByLine[$lineNumber] = $validationResult['errors'];
                continue;
            }
            
            $supplierData = self::prepareSupplierData($normalizedRow, $input, $employee);
            
            $createResult = self::createSupplierRecord($supplierData, $employee, $input['companySystemID']);
            
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
     * Validate a single supplier row
     *
     * @param array $row
     * @param array $input
     * @param int $lineNumber
     * @return array ['isValid' => bool, 'errors' => array]
     */
    private static function validateSupplierRow($row, $input, $lineNumber): array
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
        
        $groupResult = self::validateSupplierGroup($row['supplier_group'] ?? null);
        if (!$groupResult['valid']) {
            $errors[] = [
                'field' => 'Supplier Group',
                'message' => $groupResult['message'],
                'value' => $row['supplier_group'] ?? ''
            ];
        }
        
        $categoryResult = self::validateSupplierCategory($row['supplier_category'] ?? null);
        if (!$categoryResult['valid']) {
            $errors[] = [
                'field' => 'Supplier Category',
                'message' => $categoryResult['message'],
                'value' => $row['supplier_category'] ?? ''
            ];
        }
        
        if (empty($row['supplier_name']) || is_null($row['supplier_name'])) {
            $errors[] = [
                'field' => 'Supplier Name',
                'message' => self::getTranslatedMessage('custom.supplier_name_is_mandatory', 'Supplier Name is mandatory'),
                'value' => ''
            ];
        } elseif (is_numeric($row['supplier_name'])) {
            $errors[] = [
                'field' => 'Supplier Name',
                'message' => self::getTranslatedMessage('custom.not_only_numeric_value', 'Supplier Name cannot be only numeric'),
                'value' => $row['supplier_name']
            ];
        }
        
        if (empty($row['address']) || is_null($row['address'])) {
            $errors[] = [
                'field' => 'Address',
                'message' => self::getTranslatedMessage('custom.address_is_mandatory', 'Address is mandatory'),
                'value' => ''
            ];
        } elseif (is_numeric($row['address'])) {
            $errors[] = [
                'field' => 'Address',
                'message' => self::getTranslatedMessage('custom.not_only_numeric_value', 'Address cannot be only numeric'),
                'value' => $row['address']
            ];
        }
        
        $countryResult = self::validateCountry($row['supplier_country'] ?? null);
        if (!$countryResult['valid']) {
            $errors[] = [
                'field' => 'Supplier Country',
                'message' => $countryResult['message'],
                'value' => $row['supplier_country'] ?? ''
            ];
        }
        
        if (empty($row['telephone']) || is_null($row['telephone'])) {
            $errors[] = [
                'field' => 'Telephone',
                'message' => self::getTranslatedMessage('custom.telephone_is_mandatory', 'Telephone is mandatory'),
                'value' => ''
            ];
        } else {
            $telephoneResult = self::validateAlphanumeric($row['telephone'], 'Telephone', true);
            if (!$telephoneResult['valid']) {
                $errors[] = [
                    'field' => 'Telephone',
                    'message' => $telephoneResult['message'],
                    'value' => $row['telephone']
                ];
            }
        }
        
        $emailResult = self::validateEmail($row['email'] ?? null);
        if (!$emailResult['valid']) {
            $errors[] = [
                'field' => 'Email',
                'message' => $emailResult['message'],
                'value' => $row['email'] ?? ''
            ];
        }
        
        if (empty($row['registration_number']) || is_null($row['registration_number'])) {
            $errors[] = [
                'field' => 'Registration Number',
                'message' => self::getTranslatedMessage('custom.registration_number_is_mandatory', 'Registration Number is mandatory'),
                'value' => ''
            ];
        } else {
            $regNumberResult = self::validateAlphanumeric($row['registration_number'], 'Registration Number', false);
            if (!$regNumberResult['valid']) {
                $errors[] = [
                    'field' => 'Registration Number',
                    'message' => $regNumberResult['message'],
                    'value' => $row['registration_number']
                ];
            }
        }
        
        $regExpiryResult = self::validateDate($row['registration_expiry'] ?? null, 'Registration Expiry', 'DD-MM-YYYY');
        if (!$regExpiryResult['valid']) {
            $errors[] = [
                'field' => 'Registration Expiry',
                'message' => $regExpiryResult['message'],
                'value' => $row['registration_expiry'] ?? ''
            ];
        }
        
        $liabilityResult = self::validateChartOfAccount(
            $row['liability_account'] ?? null,
            'Liability Account',
            $input['companySystemID'] ?? null,
            4,
            'BS'
        );
        if (!$liabilityResult['valid']) {
            $errors[] = [
                'field' => 'Liability Account',
                'message' => $liabilityResult['message'],
                'value' => $row['liability_account'] ?? ''
            ];
        }
        
        $unbilledResult = self::validateChartOfAccount(
            $row['unbilled_account'] ?? null,
            'Unbilled Account',
            $input['companySystemID'] ?? null,
            4,
            'BS'
        );
        if (!$unbilledResult['valid']) {
            $errors[] = [
                'field' => 'Unbilled Account',
                'message' => $unbilledResult['message'],
                'value' => $row['unbilled_account'] ?? ''
            ];
        }
        
        $advanceResult = self::validateChartOfAccount(
            $row['advance_account'] ?? null,
            'Advance Account',
            $input['companySystemID'] ?? null,
            [3, 4, 5],
            'BS'
        );
        if (!$advanceResult['valid']) {
            $errors[] = [
                'field' => 'Advance Account',
                'message' => $advanceResult['message'],
                'value' => $row['advance_account'] ?? ''
            ];
        }
        
        if (isset($row['liability_account']) && isset($row['unbilled_account']) && 
            $row['liability_account'] == $row['unbilled_account']) {
            $errors[] = [
                'field' => 'GL Accounts',
                'message' => self::getTranslatedMessage('custom.liability_account_and_unbilled_account_cannot_be_s', 'Liability Account and Unbilled Account cannot be the same'),
                'value' => $row['liability_account']
            ];
        }
        
        $creditLimitResult = self::validatePositiveNumber($row['credit_limit'] ?? null, 'Credit Limit');
        if (!$creditLimitResult['valid']) {
            $errors[] = [
                'field' => 'Credit Limit',
                'message' => $creditLimitResult['message'],
                'value' => $row['credit_limit'] ?? ''
            ];
        }
        
        $creditPeriodResult = self::validatePositiveNumber($row['credit_period'] ?? null, 'Credit Period');
        if (!$creditPeriodResult['valid']) {
            $errors[] = [
                'field' => 'Credit Period',
                'message' => $creditPeriodResult['message'],
                'value' => $row['credit_period'] ?? ''
            ];
        } else {
            $creditPeriodStr = trim((string)($row['credit_period'] ?? ''));
            $creditPeriodDigits = preg_replace('/[^0-9]/', '', $creditPeriodStr);
            if (strlen($creditPeriodDigits) > 10) {
                $errors[] = [
                    'field' => 'Credit Period',
                    'message' => self::getTranslatedMessage('custom.credit_period_cannot_exceed_max_digits', 'Credit Period cannot exceed the maximum allowed number of digits.'),
                    'value' => $row['credit_period'] ?? ''
                ];
            }
        }
        
        $currencyResult = self::validateCurrency($row['currency'] ?? null);
        if (!$currencyResult['valid']) {
            $errors[] = [
                'field' => 'Currency',
                'message' => $currencyResult['message'],
                'value' => $row['currency'] ?? ''
            ];
        }
        
        $whtApplicable = $row['wht_applicable'] ?? 'No';
        $whtResult = self::validateWHTFields(
            $whtApplicable,
            $row['wht_type'] ?? null,
            $row['wht_payment_method'] ?? null
        );
        if (!$whtResult['valid']) {
            foreach ($whtResult['errors'] as $error) {
                $errors[] = [
                    'field' => $error['field'],
                    'message' => $error['message'],
                    'value' => $error['value'] ?? ''
                ];
            }
        }
        
        $molApplicable = $row['mol_contribution_applicable'] ?? 'No';
        $molResult = self::validateMOLFields(
            $molApplicable,
            $row['mol_rate'] ?? null,
            $row['mol_payment_mode'] ?? null
        );
        if (!$molResult['valid']) {
            foreach ($molResult['errors'] as $error) {
                $errors[] = [
                    'field' => $error['field'],
                    'message' => $error['message'],
                    'value' => $error['value'] ?? ''
                ];
            }
        }
        
        $omanizationResult = self::validatePercentage($row['omanization'] ?? null, 'Omanization %');
        if (!$omanizationResult['valid']) {
            $errors[] = [
                'field' => 'Omanization %',
                'message' => $omanizationResult['message'],
                'value' => $row['omanization'] ?? ''
            ];
        }
        
        $vatPercentageResult = self::validatePercentage($row['vat_percentage'] ?? null, 'VAT Percentage');
        if (!$vatPercentageResult['valid']) {
            $errors[] = [
                'field' => 'VAT Percentage',
                'message' => $vatPercentageResult['message'],
                'value' => $row['vat_percentage'] ?? ''
            ];
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
        
        if (!empty($row['vat_number']) && !is_null($row['vat_number'])) {
            $vatNumberResult = self::validateAlphanumeric($row['vat_number'], 'VAT Number', true);
            if (!$vatNumberResult['valid']) {
                $errors[] = [
                    'field' => 'VAT Number',
                    'message' => $vatNumberResult['message'],
                    'value' => $row['vat_number']
                ];
            }
        }
        
        $retentionResult = self::validatePercentage($row['retention'] ?? null, 'Retention %');
        if (!$retentionResult['valid']) {
            $errors[] = [
                'field' => 'Retention %',
                'message' => $retentionResult['message'],
                'value' => $row['retention'] ?? ''
            ];
        }
        
        return [
            'isValid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Prepare supplier data array from Excel row
     *
     * @param array $row
     * @param array $input
     * @param object $employee
     * @return array
     */
    private static function prepareSupplierData($row, $input, $employee): array
    {
        $supplierData = [];
        
        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        $supplierData['primaryCompanySystemID'] = $company->companySystemID;
        $supplierData['primaryCompanyID'] = $company->CompanyID;
        
        $supplierGroup = SupplierGroup::where('group', $row['supplier_group'])
            ->where('is_active', true)
            ->where('is_deleted', false)
            ->first();
        $supplierData['supplier_group_id'] = $supplierGroup->id;
        
        $supplierCategory = SupplierCategory::where('category', $row['supplier_category'])
            ->where('is_active', true)
            ->where('is_deleted', false)
            ->first();
        $supplierData['supplier_category_id'] = $supplierCategory->id;
        
        $supplierData['supplierName'] = $row['supplier_name'];
        $supplierData['address'] = $row['address'];
        $supplierData['nameOnPaymentCheque'] = $row['name_on_the_cheque'] ?? $row['supplier_name'];
        
        $country = CountryMaster::where('countryName', $row['supplier_country'])->first();
        $supplierData['supplierCountryID'] = $country->countryID;
        $supplierData['countryID'] = $country->countryID;
        
        $supplierData['telephone'] = $row['telephone'];
        $supplierData['fax'] = $row['fax'] ?? null;
        $supplierData['supEmail'] = $row['email'];
        $supplierData['webAddress'] = $row['web_address'] ?? null;
        
        $supplierData['registrationNumber'] = $row['registration_number'];
        $supplierData['registrationExprity'] = Carbon::createFromFormat('d-m-Y', $row['registration_expiry'])->format('Y-m-d H:i:s');
        
        $liabilityAccount = ChartOfAccount::where('AccountCode', $row['liability_account'])->first();
        $supplierData['liabilityAccountSysemID'] = $liabilityAccount->chartOfAccountSystemID;
        $supplierData['liabilityAccount'] = $liabilityAccount->AccountCode;
        
        $unbilledAccount = ChartOfAccount::where('AccountCode', $row['unbilled_account'])->first();
        $supplierData['UnbilledGRVAccountSystemID'] = $unbilledAccount->chartOfAccountSystemID;
        $supplierData['UnbilledGRVAccount'] = $unbilledAccount->AccountCode;
        
        $advanceAccount = ChartOfAccount::where('AccountCode', $row['advance_account'])->first();
        $supplierData['advanceAccountSystemID'] = $advanceAccount->chartOfAccountSystemID;
        $supplierData['AdvanceAccount'] = $advanceAccount->AccountCode;
        
        $supplierData['creditLimit'] = $row['credit_limit'];
        $supplierData['creditPeriod'] = $row['credit_period'];
        
        $currency = CurrencyMaster::where('CurrencyCode', $row['currency'])->first();
        $supplierData['currency'] = $currency->currencyID;
        
        $supplierData['supplierImportanceID'] = null;
        if (!empty($row['importance'])) {
            $importance = SupplierImportance::where('importanceDescription', $row['importance'])->first();
            if ($importance) {
                $supplierData['supplierImportanceID'] = $importance->supplierImportanceID;
            }
        }
        
        $supplierData['supplierNatureID'] = null;
        if (!empty($row['nature'])) {
            $nature = suppliernature::where('natureDescription', $row['nature'])->first();
            if ($nature) {
                $supplierData['supplierNatureID'] = $nature->supplierNatureID;
            }
        }
        
        $supplierData['supplierTypeID'] = null;
        if (!empty($row['type'])) {
            $type = SupplierType::where('typeDescription', $row['type'])->first();
            if ($type) {
                $supplierData['supplierTypeID'] = $type->supplierTypeID;
            }
        }
        
        $whtApplicable = $row['wht_applicable'] ?? 'No';
        if (empty($whtApplicable) || is_null($whtApplicable) || trim((string)$whtApplicable) === '') {
            $whtApplicable = 'No';
        } else {
            $whtApplicable = trim((string)$whtApplicable);
            $whtApplicableLower = strtolower($whtApplicable);
            if ($whtApplicableLower === 'yes') {
                $whtApplicable = 'Yes';
            } else {
                $whtApplicable = 'No';
            }
        }
        $supplierData['whtApplicableYN'] = ($whtApplicable == 'Yes') ? 1 : 0;
        if ($supplierData['whtApplicableYN'] == 0) {
            $supplierData['whtType'] = null;
            $supplierData['paymentMethod'] = 0;
        } else {
            if (!empty($row['wht_type'])) {
                $tax = Tax::where('taxDescription', $row['wht_type'])->first();
                if ($tax) {
                    $supplierData['whtType'] = $tax->taxMasterAutoID;
                }
            }
            if (!empty($row['wht_payment_method'])) {
                $supplierData['paymentMethod'] = $row['wht_payment_method'];
            }
        }
        
        $molApplicable = $row['mol_contribution_applicable'] ?? 'No';
        if (empty($molApplicable) || is_null($molApplicable) || trim((string)$molApplicable) === '') {
            $molApplicable = 'No';
        } else {
            $molApplicable = trim((string)$molApplicable);
            $molApplicableLower = strtolower($molApplicable);
            if ($molApplicableLower === 'yes') {
                $molApplicable = 'Yes';
            } else {
                $molApplicable = 'No';
            }
        }
        $supplierData['mol_applicable'] = ($molApplicable == 'Yes') ? 1 : 0;
        if (!empty($row['mol_rate'])) {
            $supplierData['mol_rate'] = $row['mol_rate'];
        }
        if (!empty($row['mol_payment_mode'])) {
            $supplierData['mol_payment_mode'] = $row['mol_payment_mode'];
        }
        
        if (!empty($row['omanization'])) {
            $supplierData['omanization'] = $row['omanization'];
        }
        
        if (!empty($row['retention'])) {
            $supplierData['retentionPercentage'] = $row['retention'];
        }
        
        $supplierData['vatEligible'] = 0;
        if (!empty($row['vat_eligible'])) {
            $vatEligible = trim((string)$row['vat_eligible']);
            $vatEligibleLower = strtolower($vatEligible);
            if ($vatEligibleLower === 'yes') {
                $supplierData['vatEligible'] = 1;
            } else {
                $supplierData['vatEligible'] = 0;
            }
        }
        
        $supplierData['vatNumber'] = $row['vat_number'] ?? null;
        $supplierData['vatPercentage'] = $row['vat_percentage'] ?? null;
        
        $supplierData['jsrsNo'] = $row['jsrs_number'] ?? null;
        if (!empty($row['jsrs_expiry'])) {
            try {
                $supplierData['jsrsExpiry'] = Carbon::createFromFormat('d-m-Y', $row['jsrs_expiry'])->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                $supplierData['jsrsExpiry'] = Carbon::parse($row['jsrs_expiry'])->format('Y-m-d H:i:s');
            }
        }
        
        $supplierData['createdPcID'] = gethostname();
        $supplierData['createdUserID'] = $employee->empID;
        $supplierData['createdUserSystemID'] = $employee->employeeSystemID;
        $supplierData['uniqueTextcode'] = 'S';
        
        $document = DocumentMaster::where('documentID', 'SUPM')->first();
        $supplierData['documentSystemID'] = $document->documentSystemID;
        $supplierData['documentID'] = $document->documentID;
        
        $supplierData['isActive'] = 1;
        $supplierData['approvedYN'] = 1;
        $supplierData['supplierConfirmedYN'] = 1;
        $supplierData['approvedby'] = $employee->empID;
        $supplierData['approvedEmpSystemID'] = $employee->employeeSystemID;
        $supplierData['approvedDate'] = Carbon::now();
        $supplierData['supplierConfirmedEmpID'] = $employee->empID;
        $supplierData['supplierConfirmedEmpSystemID'] = $employee->employeeSystemID;
        $supplierData['supplierConfirmedEmpName'] = $employee->empName;
        $supplierData['supplierConfirmedDate'] = Carbon::now();
        
        return $supplierData;
    }

    /**
     * Create supplier record with related records
     *
     * @param array $supplierData
     * @param object $employee
     * @param int $companySystemID
     * @return array
     */
    private static function createSupplierRecord($supplierData, $employee, $companySystemID): array
    {
        DB::beginTransaction();
        try {
            $supplierMaster = SupplierMaster::create($supplierData);
            
            $supplierMaster->primarySupplierCode = 'S0' . strval($supplierMaster->supplierCodeSystem);
            $supplierMaster->save();
            
            $empId = $employee->empID;
            $empName = $employee->empName;
            
            $supplierCurrency = new SupplierCurrency();
            $supplierCurrency->supplierCodeSystem = $supplierMaster->supplierCodeSystem;
            $supplierCurrency->currencyID = $supplierData['currency'];
            $supplierCurrency->isAssigned = -1;
            $supplierCurrency->isDefault = -1;
            $supplierCurrency->save();
            
            $companyDefaultBankMemos = BankMemoTypes::orderBy('sortOrder', 'asc')->get();
            
            foreach ($companyDefaultBankMemos as $value) {
                $temBankMemo = new BankMemoSupplier();
                $temBankMemo->memoHeader = $value['bankMemoHeader'];
                $temBankMemo->bankMemoTypeID = $value['bankMemoTypeID'];
                $temBankMemo->memoDetail = '';
                $temBankMemo->supplierCodeSystem = $supplierMaster->supplierCodeSystem;
                $temBankMemo->supplierCurrencyID = $supplierCurrency->supplierCurrencyID;
                $temBankMemo->updatedByUserID = $empId;
                $temBankMemo->updatedByUserName = $empName;
                $temBankMemo->save();
            }
            
            $assignResult = SupplierAssignService::assignSupplier($supplierMaster->supplierCodeSystem, $companySystemID);
            
            if (!$assignResult['status']) {
                DB::rollBack();
                return [
                    'status' => false,
                    'message' => self::getTranslatedMessage('custom.error_assign_supplier', 'Error assigning supplier to company')
                ];
            }
            
            DB::commit();
            return [
                'status' => true,
                'data' => $supplierMaster
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Supplier bulk upload creation error: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Collect errors by line number
     *
     * @param array $errors
     * @return array
     */
    private static function collectErrorsByLine($errors): array
    {
        $errorsByLine = [];
        foreach ($errors as $error) {
            $lineNumber = $error['line'] ?? 0;
            if (!isset($errorsByLine[$lineNumber])) {
                $errorsByLine[$lineNumber] = [];
            }
            $errorsByLine[$lineNumber][] = $error;
        }
        return $errorsByLine;
    }

    private static function generateResponse($successCount, $totalCount, $errorsByLine): array
    {
        $message = "Successfully uploaded {$successCount} suppliers out of {$totalCount}";
        
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

    /**
     * Validate company
     */
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

    private static function validateSupplierGroup($groupName): array
    {
        if (empty($groupName) || is_null($groupName)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.supplier_group_is_mandatory', 'Supplier Group is mandatory')
            ];
        }
        
        $group = SupplierGroup::where('group', $groupName)
            ->where('is_active', true)
            ->where('is_deleted', false)
            ->first();
        
        if (!$group) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.supplier_group_not_matching_with_system', 'The Supplier Group not matching with system')
            ];
        }
        
        return ['valid' => true];
    }

    private static function validateSupplierCategory($categoryCode): array
    {
        if (empty($categoryCode) || is_null($categoryCode)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.supplier_category_is_mandatory', 'Supplier Category is mandatory')
            ];
        }
        
        $category = SupplierCategory::where('category', $categoryCode)
            ->where('is_active', true)
            ->where('is_deleted', false)
            ->first();
        
        if (!$category) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.supplier_category_not_matching_with_system', 'The Supplier Category not matching with system')
            ];
        }
        
        return ['valid' => true];
    }

    private static function validateCountry($countryName): array
    {
        if (empty($countryName) || is_null($countryName)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.country_is_mandatory', 'Country is mandatory')
            ];
        }
        
        $country = CountryMaster::where('countryName', $countryName)->first();
        if (!$country) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.country_not_matching_with_system', 'Country not matching with system')
            ];
        }
        
        return ['valid' => true];
    }

    private static function validateCurrency($currencyCode): array
    {
        if (empty($currencyCode) || is_null($currencyCode)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.currency_is_mandatory', 'Currency is mandatory')
            ];
        }
        
        $currency = CurrencyMaster::where('CurrencyCode', $currencyCode)->first();
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

    private static function validateEmail($email): array
    {
        if (empty($email) || is_null($email)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.email_is_mandatory', 'Email is mandatory')
            ];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strpos($email, '@') === false) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.please_enter_valid_email_address', 'Please enter valid email address')
            ];
        }
        
        return ['valid' => true];
    }

    private static function validateAlphanumeric($value, $fieldName, $allowPhoneChars = false): array
    {
        if (empty($value) || is_null($value)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.field_is_mandatory', "{$fieldName} is mandatory", ['field' => $fieldName])
            ];
        }
        
        $value = trim((string)$value);
        
        if ($allowPhoneChars) {
            $pattern = '/^[a-zA-Z0-9\s\-\(\)\+\/]+$/';
            if (!preg_match($pattern, $value)) {
                return [
                    'valid' => false,
                    'message' => self::getTranslatedMessage('custom.field_should_contain_only_text_and_numbers', "{$fieldName} should contain only text and numbers", ['field' => $fieldName])
                ];
            }
        } else {
            $pattern = '/^[a-zA-Z0-9]+$/';
            if (!preg_match($pattern, $value)) {
                return [
                    'valid' => false,
                    'message' => self::getTranslatedMessage('custom.field_should_contain_only_text_and_numbers', "{$fieldName} should contain only text and numbers", ['field' => $fieldName])
                ];
            }
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

    private static function validatePercentage($value, $fieldName): array
    {
        if (empty($value) || is_null($value)) {
            return ['valid' => true];
        }
        
        $valueStr = trim((string)$value);
        if (!is_numeric($valueStr)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.percentage_should_be_numeric', "{$fieldName} should be numeric", ['field' => $fieldName])
            ];
        }
        
        $valueFloat = (float)$valueStr;
        if ($valueFloat != (int)$valueFloat) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.percentage_should_be_integer', "{$fieldName} should be an integer", ['field' => $fieldName])
            ];
        }
        
        $valueInt = (int)$valueFloat;
        if ($valueInt < 0 || $valueInt > 100) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.percentage_should_be_between_0_and_100', "{$fieldName} should be between 0 and 100", ['field' => $fieldName])
            ];
        }
        
        return ['valid' => true];
    }

    private static function validateDate($dateValue, $fieldName, $format = 'DD-MM-YYYY'): array
    {
        if (empty($dateValue) || is_null($dateValue)) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.field_is_mandatory', "{$fieldName} is mandatory", ['field' => $fieldName])
            ];
        }
        
        try {
            if ($format == 'DD-MM-YYYY') {
                $date = Carbon::createFromFormat('d-m-Y', $dateValue);
                if ($date->format('d-m-Y') !== $dateValue) {
                    throw new \Exception('Date format mismatch');
                }
            } else {
                $date = Carbon::parse($dateValue);
            }
        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => self::getTranslatedMessage('custom.date_format_not_matching', "{$fieldName} date format not matching (Format {$format})", ['field' => $fieldName, 'format' => $format])
            ];
        }
        
        return ['valid' => true];
    }

    private static function validateWHTFields($whtApplicable, $whtType, $whtPaymentMethod): array
    {
        $errors = [];
        
        if (empty($whtApplicable) || is_null($whtApplicable) || trim((string)$whtApplicable) === '') {
            $whtApplicable = 'No';
        } else {
            $whtApplicable = trim((string)$whtApplicable);
        }
        
        $whtApplicableLower = strtolower($whtApplicable);
        if ($whtApplicableLower !== 'yes' && $whtApplicableLower !== 'no') {
            $errors[] = [
                'field' => 'WHT Applicable',
                'message' => self::getTranslatedMessage('custom.wht_applicable_value_not_matching_with_system', 'The WHT Applicable is value not matching with system'),
                'value' => $whtApplicable
            ];
            return [
                'valid' => false,
                'errors' => $errors
            ];
        }
        
        $whtApplicable = ucfirst($whtApplicableLower);
        
        if ($whtApplicable == 'Yes') {
            if (empty($whtType) || is_null($whtType)) {
                $errors[] = [
                    'field' => 'WHT Type',
                    'message' => self::getTranslatedMessage('custom.wht_type_method_is_mandatory', 'WHT Type method is mandatory'),
                    'value' => ''
                ];
            } else {
                $tax = Tax::where('taxDescription', $whtType)->first();
                if (!$tax) {
                    $errors[] = [
                        'field' => 'WHT Type',
                        'message' => self::getTranslatedMessage('custom.wht_type_not_matching_with_system', 'WHT Type not matching with system'),
                        'value' => $whtType
                    ];
                }
            }
            
            if (empty($whtPaymentMethod) || is_null($whtPaymentMethod)) {
                $errors[] = [
                    'field' => 'WHT Payment Method',
                    'message' => self::getTranslatedMessage('custom.wht_payment_method_is_mandatory', 'WHT payment method is mandatory'),
                    'value' => ''
                ];
            } else {
                $whtPaymentMethodStr = trim((string)$whtPaymentMethod);
                if ($whtPaymentMethodStr !== '1' && $whtPaymentMethodStr !== '2') {
                    $errors[] = [
                        'field' => 'WHT Payment Method',
                        'message' => self::getTranslatedMessage('custom.wht_payment_method_invalid', 'WHT Payment Method should be either 1 (Deduct from Invoice (Supplier)) or 2 (Organization Bears WHT)'),
                        'value' => $whtPaymentMethod
                    ];
                }
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Validate MOL fields
     */
    private static function validateMOLFields($molApplicable, $molRate, $molPaymentMode): array
    {
        $errors = [];
        
        if (empty($molApplicable) || is_null($molApplicable) || trim((string)$molApplicable) === '') {
            $molApplicable = 'No';
        } else {
            $molApplicable = trim((string)$molApplicable);
        }
        
        $molApplicableLower = strtolower($molApplicable);
        if ($molApplicableLower !== 'yes' && $molApplicableLower !== 'no') {
            $errors[] = [
                'field' => 'MOL Applicable',
                'message' => self::getTranslatedMessage('custom.mol_applicable_value_not_matching_with_system', 'The MOL Applicable is value not matching with system'),
                'value' => $molApplicable
            ];
            return [
                'valid' => false,
                'errors' => $errors
            ];
        }
        
        $molApplicable = ucfirst($molApplicableLower);
        
        if ($molApplicable == 'Yes') {
            $molRateResult = self::validatePercentage($molRate ?? null, 'MOL Rate');
            if (!$molRateResult['valid']) {
                $errors[] = [
                    'field' => 'MOL Rate',
                    'message' => $molRateResult['message'],
                    'value' => $molRate ?? ''
                ];
            }
            
            if (empty($molPaymentMode) || is_null($molPaymentMode)) {
                $errors[] = [
                    'field' => 'MOL Payment Mode',
                    'message' => self::getTranslatedMessage('custom.mol_payment_method_is_mandatory', 'MOL payment Mode is mandatory'),
                    'value' => ''
                ];
            } else {
                $molPaymentModeStr = trim((string)$molPaymentMode);
                if ($molPaymentModeStr !== '1') {
                    $errors[] = [
                        'field' => 'MOL Payment Mode',
                        'message' => self::getTranslatedMessage('custom.mol_payment_mode_invalid', 'MOL Payment Mode should be 1 (Deduct from invoice)'),
                        'value' => $molPaymentMode
                    ];
                }
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}

