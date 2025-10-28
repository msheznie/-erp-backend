<?php

namespace App\Services;

use App\Models\BankMemoSupplier;
use App\Models\BankMemoTypes;
use App\Models\Company;
use App\Models\DocumentAttachments;
use App\Models\DocumentMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierBusinessCategoryAssign;
use App\Models\SupplierContactDetails;
use App\Models\SupplierCurrency;
use App\Models\SupplierMaster;
use App\Models\SupplierRegistrationLink;
use App\Models\SupplierSubCategoryAssign;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierRegistrationService
{

    public static function compareSupplierData($input): array{
        $selectedSupplierID = $input['selectedSupplierID'];
        $supplierKyc = $input['supplierKyc'];
        $uuid = $input['uuid'];
        $message = trans('srm_supplier_management.matching_data');

        $supplierMaster = SupplierMaster::where('supplierCodeSystem', $selectedSupplierID)->first();
        if(empty($supplierMaster)){
            return self::sendError(trans('srm_supplier_management.supplier_not_found'));
        }
        $section = [1, 3, 5];
        $mappingData = self::getMappingData($supplierKyc, $section);
        $isNotMapping = self::checkSupplierDataMatching($mappingData, $supplierMaster);

        if($isNotMapping){
            $message = trans('srm_supplier_management.supplier_details_are_not_same_do_you_wish_to_replace_the_supplier_master_details_with_the_supplier_KYC_details');
        }

        return self::sendSuccessResponse($message, $isNotMapping);

    }

    public static function getMappingData($supplierKyc, $formSectionIDs, $formFieldID=[]): array{
        return collect($supplierKyc ?? [])
            ->whereIn('form_section_id', $formSectionIDs)
            ->when(!empty($formFieldID), function ($q) use ($formFieldID) {
                return $q->whereIn('form_field_id', $formFieldID);
            })
            ->map(function ($item) {
                return [
                    'form_field_id' => $item['form_field_id'],
                    'form_group_id' => $item['form_group_id'],
                    'value' => $item['value']
                ];
            })
            ->values()
            ->toArray();
    }
    public static function checkSupplierDataMatching($mappingData, $supplierMaster){
        $isNotMapping = false;
        $hasCompanyRegistration = false;

        foreach($mappingData as $map){
            if($map['form_group_id'] == 3 && $map['form_field_id'] == 11 && $map['value'] == 'Company Registration Certificate'){
                $hasCompanyRegistration = true;
            }
            if($map['form_field_id'] == 8){
                $isNotMapping = $map['value'] != $supplierMaster->registrationNumber ?? '';
            }
            if($hasCompanyRegistration && $map['form_field_id'] == 14){
                $isNotMapping = $map['value'] != $supplierMaster->registrationExprity ?? '';
                $hasCompanyRegistration = false;
            }
            if($map['form_field_id'] == 28){
                $isNotMapping = $map['value'] != $supplierMaster->currency ?? 0;
            }
            if($map['form_field_id'] == 46){
                $isNotMapping = $map['value'] != $supplierMaster->countryID ?? 0;
            }
            if($map['form_field_id'] == 47){
                $isNotMapping = $map['value'] != $supplierMaster->address ?? '';
            }
            if($map['form_field_id'] == 50){
                $isNotMapping = $map['value'] != $supplierMaster->telephone ?? '';
            }
            if($map['form_field_id'] == 69){
                $isNotMapping = $map['value'] != $supplierMaster->supEmail ?? '';
            }
            if($map['form_field_id'] == 74){
                $isNotMapping = $map['value'] != $supplierMaster->supplier_category_id ?? 0;
            }
            if($map['form_field_id'] == 75){
                $isNotMapping = $map['value'] != $supplierMaster->supplier_group_id ?? 0;
            }
        }
        return $isNotMapping;
    }

    public static function linkKYCWithSupplier($selectedSupplier, $supplierKyc, $selectedKYC, $companyID){
        try{
            return DB::transaction(function () use ($selectedSupplier, $supplierKyc, $selectedKYC, $companyID) {
                $supplierDetails = self::updateSupplierDetails($selectedSupplier, $supplierKyc);
                if (!$supplierDetails['success']) {
                    return self::sendError($supplierDetails['message'] ?? trans('srm_supplier_management.failed_to_update_supplier_details'));
                }

                $attachment = self::updateAttachments($selectedSupplier, $supplierKyc, $companyID);
                if (!$attachment['success']) {
                    return self::sendError($attachment['message'] ?? trans('srm_supplier_management.failed_to_update_attachments'));
                }

                $businessCat = self::updateBusinessCategory($selectedSupplier, $supplierKyc);
                if (!$businessCat['success']) {
                    return self::sendError($businessCat['message'] ?? trans('srm_supplier_management.failed_to_update_business_category'));
                }

                $businessSubCat = self::updateBusinessSubCategory($selectedSupplier, $supplierKyc);
                if (!$businessSubCat['success']) {
                    return self::sendError($businessSubCat['message'] ?? trans('srm_supplier_management.failed_to_update_business_subcategory'));
                }

                $contactDetails = self::updateContactDetails($selectedSupplier, $supplierKyc);
                if (!$contactDetails['success']) {
                    return self::sendError($contactDetails['message'] ?? trans('srm_supplier_management.failed_to_update_contact_details'));
                }

                $assignCurrency = self::updateAssignCurrency($selectedSupplier, $supplierKyc);

                if (!$assignCurrency['success']) {
                    return self::sendError($assignCurrency['message'] ?? trans('srm_supplier_management.failed_to_update_currency_details'));
                }

                SupplierRegistrationLink::where('id', $selectedKYC)
                    ->update([
                        'supplier_master_id' => $selectedSupplier
                    ]);

                return self::sendSuccessResponse(trans('srm_supplier_management.supplier_linked_successfully'));
            });
        } catch (\Exception $exception){
            return self::sendError(trans('srm_supplier_management.unexpected_error') . $exception->getMessage());
        }
    }

    protected static function updateSupplierDetails($selectedSupplier, $supplierKyc){
        try {
            return DB::transaction(function () use ($selectedSupplier, $supplierKyc) {
                $section = [1, 3, 5];
                $mappingData = self::getMappingData($supplierKyc, $section);
                $updateData = [];
                $previousCertification = null;

                $updateData += [
                    'vatEligible' => 0,
                    'vatPercentage' => 0,
                    'vatNumber' => 0
                ];
                $commonColumnNo = ['isSMEYN' => 0];

                foreach ($mappingData as $map) {
                    $fieldID = $map['form_field_id'];
                    $value = $map['value'];

                    if($fieldID == 11){
                        $previousCertification = $value;
                    }

                    switch ($fieldID) {
                        case 4:
                            $updateData['supplierName'] = trim($value);
                            break;
                        case 8:
                            $updateData['registrationNumber'] = trim($value);
                            break;

                        case 14:
                            if($previousCertification == 'Company Registration Certificate'){
                                $updateData['registrationExprity'] = !empty($value) ? Carbon::parse($value)->format('Y-m-d') : null;
                            }
                            break;
                        case 30:
                            $updateData['vatEligible'] = trim($value);
                            break;
                        case 31:
                            $updateData['vatPercentage'] = trim($value);
                            break;
                        case 36:
                            $updateData['vatNumber'] = trim($value);
                            break;
                        case 46:
                            $updateData['countryID'] = $value;
                            $updateData['supplierCountryID'] = $value;
                            break;

                        case 47:
                            $updateData['address'] = trim($value);
                            break;

                        case 50:
                            $updateData['telephone'] = $value;
                            break;

                        case 69:
                            $updateData['supEmail'] = trim($value);
                            break;

                        case 74:
                            $updateData['supplier_category_id'] = $value ?? null;
                            break;

                        case 75:
                            $updateData['supplier_group_id'] = $value ?? null;
                            break;
                        case 76:
                            $commonColumnNo['isSMEYN'] = trim($value);
                            break;
                        default:
                            break;
                    }
                }
                $supplierUpdate = array_merge($updateData, $commonColumnNo);
                SupplierMaster::where('supplierCodeSystem', $selectedSupplier)->update($supplierUpdate);
                SupplierAssigned::where('supplierCodeSytem', $selectedSupplier)->update($updateData);
                return self::sendSuccessResponse(trans('srm_supplier_management.supplier_details_updated_successfully'));
            });
        } catch (\Exception $ex){
            return self::sendError(trans('srm_supplier_management.unexpected_error'). $ex->getMessage());
        }
    }
    protected static function updateAttachments($selectedSupplier, $supplierKyc, $companyID){
        try{
            return DB::transaction(function () use ($selectedSupplier, $supplierKyc, $companyID) {
                DocumentAttachments::where('documentSystemID', 56)
                                    ->where('documentSystemCode', $selectedSupplier)
                                    ->delete();

                $section = [1];
                $formField = [11, 12, 14];
                $attachments = self::getMappingData($supplierKyc, $section, $formField);

                $initial = true;
                $insertData = [];
                $tempData = [];

                $documentMaster = DocumentMaster::getDocumentData(56);
                $companyCode = Company::getComanyCode($companyID);

                $commonData = [
                    'companySystemID' => $companyID,
                    'companyID' => $companyCode,
                    'documentSystemID' => 56,
                    'documentID' => $documentMaster['documentID'],
                    'documentSystemCode' => $selectedSupplier,
                    'attachmentType' => 11,
                    'isUploaded' => 1
                ];

                foreach ($attachments as $map){
                    $fieldID = $map['form_field_id'];
                    $value = trim($map['value']);

                    if($fieldID == 11){
                        if (!$initial) {
                            if (!empty($tempData['path']) && $tempData['path'] !== '-') {
                                $insertData[] = array_merge($tempData, $commonData);
                            }
                        }

                        $tempData = [];
                        $tempData['attachmentDescription'] = $value;
                        $tempData['originalFileName'] = $value;
                        $tempData['myFileName'] = $value;
                        $tempData['docExpirtyDate'] = null;
                        $initial = false;
                    }

                    if ($fieldID == 12) {
                        $tempData['path'] = $value;
                    }

                    if ($fieldID == 14) {
                        $tempData['docExpirtyDate'] = (!empty($value) && $value !== '-') ? Carbon::parse($value)->format('Y-m-d') : null;
                    }
                }

                if (!empty($tempData['path']) && $tempData['path'] !== '-') {
                    $insertData[] = array_merge($tempData, $commonData);
                }

                if (!empty($insertData)) {
                    DocumentAttachments::insert($insertData);
                }
                return self::sendSuccessResponse(trans('srm_supplier_management.attachment_updated_successfully'));
            });
        } catch(\Exception $ex){
            return self::sendError(trans('srm_supplier_management.unexpected_error'). $ex->getMessage());
        }
    }
    protected static function updateBusinessCategory($selectedSupplier, $supplierKyc){
        try{
            return DB::transaction(function () use ($selectedSupplier, $supplierKyc) {
                $section = [1];
                $formField = [1];
                $businessCat = self::getMappingData($supplierKyc, $section, $formField);
                $commonData = ['supplierID' => $selectedSupplier, 'timestamp' => now()];
                $insertData = [];

                if(!empty($businessCat)){
                    SupplierBusinessCategoryAssign::where('supplierID', $selectedSupplier)->delete();
                    foreach($businessCat as $key => $map){
                        $value = trim($map['value']);
                        $insertData[$key]['supCategoryMasterID'] = $value;
                    }

                    $finalInsertData = [];
                    foreach($insertData as $data){
                        $finalInsertData[] = array_merge($data, $commonData);
                    }
                    SupplierBusinessCategoryAssign::insert($finalInsertData);
                }
                return self::sendSuccessResponse(trans('srm_supplier_management.supplier_business_category_updated_successfully'));
            });
        } catch(\Exception $ex){
            return self::sendError(trans('srm_supplier_management.unexpected_error'). $ex->getMessage());
        }
    }
    protected static function updateBusinessSubCategory($selectedSupplier, $supplierKyc){
        try{
            return DB::transaction(function () use ($selectedSupplier, $supplierKyc) {
                $section = [1];
                $formField = [2];
                $businessCat = self::getMappingData($supplierKyc, $section, $formField);
                $commonData = ['supplierID' => $selectedSupplier, 'timestamp' => now()];
                $insertData = [];

                if(!empty($businessCat)){
                    SupplierSubCategoryAssign::where('supplierID', $selectedSupplier)->delete();
                    foreach($businessCat as $key => $map){
                        $value = trim($map['value']);
                        $insertData[$key]['supSubCategoryID'] = $value;
                    }

                    $finalInsertData = [];
                    foreach($insertData as $data){
                        $finalInsertData[] = array_merge($data, $commonData);
                    }
                    SupplierSubCategoryAssign::insert($finalInsertData);
                }
                return self::sendSuccessResponse(trans('srm_supplier_management.supplier_business_category_updated_successfully'));
            });
        } catch(\Exception $ex){
            return self::sendError(trans('srm_supplier_management.unexpected_error'). $ex->getMessage());
        }
    }
    protected static function updateContactDetails($selectedSupplier, $supplierKyc){
        try{
            return DB::transaction(function () use ($selectedSupplier, $supplierKyc) {
                $section = [6];
                $contactDetails = self::getMappingData($supplierKyc, $section);

                if(!empty($contactDetails)){
                    SupplierContactDetails::where('supplierID', $selectedSupplier)->delete();
                    $x = 0;
                    $initial = true;
                    $insertData = [];

                    foreach($contactDetails as $map){
                        $fieldID = $map['form_field_id'];
                        $value = trim($map['value']);

                        if($fieldID == 54 && !$initial){
                            $x++;
                        }
                        $insertData[$x]['contactPersonFax'] = null;

                        switch($fieldID){
                            case 70:
                                $insertData[$x]['contactTypeID'] = $value;
                                break;
                            case 56:
                                $insertData[$x]['contactPersonName'] = $value;
                                break;
                            case 59:
                                $insertData[$x]['contactPersonTelephone'] = $value;
                                break;
                            case 61:
                                $insertData[$x]['contactPersonEmail'] = $value;
                                break;
                            case 62:
                                $insertData[$x]['contactPersonFax'] = $value;
                                break;
                            case 63:
                                $insertData[$x]['isDefault'] = $value == 1 ? -1 : 0;
                                break;
                        }

                        $initial = false;
                    }

                    $finalInsertData = [];
                    $commonData = ['supplierID' => $selectedSupplier, 'timestamp' => now()];
                    foreach($insertData as $data){
                        $finalInsertData[] = array_merge($data, $commonData);
                    }
                    SupplierContactDetails::insert($finalInsertData);
                }
                return self::sendSuccessResponse(trans('srm_supplier_management.supplier_contact_details_updated_successfully'));
            });
        } catch(\Exception $ex){
            return self::sendError(trans('srm_supplier_management.unexpected_error'). $ex->getMessage());
        }
    }

    protected static function updateAssignCurrency($selectedSupplier, $supplierKyc)
    {
        try{
            return DB::transaction(function () use ($selectedSupplier, $supplierKyc) {
                $section = [3];
                $formField = [28];
                $currencyData = self::getMappingData($supplierKyc, $section, $formField);
                $currency = $currencyData[0]['value'];
                $supplierCurrencyCheck = SupplierCurrency::getCurrency($selectedSupplier,$currency);
                $insertData = [];

                if(!empty($currency) && empty($supplierCurrencyCheck)){
                    SupplierCurrency::where('supplierCodeSystem', $selectedSupplier)
                        ->update(['isDefault' => 0]);

                    $dataCurrency = [
                        'supplierCodeSystem' => $selectedSupplier,
                        'currencyID' => $currency,
                        'isAssigned' => -1,
                        'isDefault' => 1,
                    ];

                    SupplierCurrency::create($dataCurrency);

                    $supplier = SupplierMaster::getSupplierData($selectedSupplier);
                    $companyDefaultBankMemos = BankMemoTypes::getBankMemo();

                    foreach ($companyDefaultBankMemos as $value) {
                        $temBankMemo = [
                            'memoHeader' => $value['bankMemoHeader'],
                            'bankMemoTypeID' => $value['bankMemoTypeID'],
                            'memoDetail' => '',
                            'supplierCodeSystem' => $supplier->supplierCodeSystem,
                            'supplierCurrencyID' => $currency
                        ];
                        BankMemoSupplier::create($temBankMemo);
                    }
                }
                return self::sendSuccessResponse(trans('srm_supplier_management.supplier_currency_assigned_successfully'));
            });
        } catch(\Exception $ex){
            return self::sendError(trans('srm_supplier_management.unexpected_error'). $ex->getMessage());
        }
    }
    protected static function sendError($message = ''): array{
        return [
            'success' => false,
            'message' => $message,
            'data' => []
        ];
    }
    protected static function sendSuccessResponse($message='', $data=[]): array {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }
}
