<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\DocCodeSetupCommon;
use App\Models\DocCodeSetupTypeBased;
use App\Models\DocumentCodeMaster;
use App\Models\ProcumentOrder;
use App\Models\PurchaseRequest;
use Carbon\Carbon;

class DocumentCodeConfigurationService
{
    public function getDocumentCodeConfiguration($companyID,$input,$lastSerialNumber,$documentCodeMasterID,$segmentCode=null)

    {

        $companyID = $companyID;

        $documentCodeMaster = DocumentCodeMaster::with('document_code_transactions')->where('id', $documentCodeMasterID)->first();
        if(!$documentCodeMaster){
            return ['status' => false,'message'=>'Document Code Master not found'];
        }
        $formats = $this->getDocumentCodeSetupValues($companyID, $segmentCode);


        $docCodeSetupCommon = DocCodeSetupCommon::with('document_code_transactions')->where('master_id', $documentCodeMasterID)->first();


        switch ($documentCodeMasterID)
        {
            case 1: //Purchase Request
                if($docCodeSetupCommon){
                    $prefix = $docCodeSetupCommon->document_code_transactions->master_prefix; //format5
                    $formats[5] = $prefix;

                    $docLastSerialNumber = $lastSerialNumber;

                    if($documentCodeMaster->numbering_sequence_id == 2){
                        $RequestedDatefinanceYear = CompanyFinanceYear::where('companySystemID', $companyID)
                        ->whereYear('bigginingDate', date('Y', strtotime($input['PRRequestedDate'])))
                        ->where('isActive' , -1)
                        ->first();

                        if($RequestedDatefinanceYear){
                            $isDocForYear = PurchaseRequest::where('companySystemID', $input['companySystemID'])
                                    ->where('documentSystemID', $input['documentSystemID'])
                                    ->whereYear('PRRequestedDate', date('Y', strtotime($RequestedDatefinanceYear->bigginingDate)))
                                    ->orderBy('purchaseRequestID', 'desc')
                                    ->first();
                            
                            if($isDocForYear){
                                $docLastSerialNumber = intval($isDocForYear->serialNumber) + 1;
                            } else {
                                $docLastSerialNumber = 1;
                            }
                        }
                    }
        
                    $serialCode = str_pad($docLastSerialNumber, $documentCodeMaster->serial_length, '0', STR_PAD_LEFT);
                    $finalCode = $this->generateFinalCode($docCodeSetupCommon,$formats,$serialCode);

                    return ['status' => true,'message'=>'Document Code generated','documentCode'=>$finalCode,'docLastSerialNumber'=>$docLastSerialNumber];

                } else {
                    return ['status' => false,'message'=>'Document Code not generated'];
                }

                break;
            case 2: //Purchase Order

                $docLastSerialNumber = $lastSerialNumber;

                if($documentCodeMaster->numbering_sequence_id == 2){
                    $PORequestedDate = now();
                    $orderedDatefinanceYear = CompanyFinanceYear::where('companySystemID', $companyID)
                                            ->whereYear('bigginingDate', date('Y', strtotime($input['POOrderedDate'])))
                                            ->where('isActive' , -1)
                                            ->first();

                    if($orderedDatefinanceYear){

                        if ($input['documentSystemID'] == 5 && $input['poType_N'] == 5) {
                            $isPoDocForYear = ProcumentOrder::where('companySystemID', $input['companySystemID'])
                                ->where('documentSystemID', $input['documentSystemID'])
                                ->where('poType_N', 5)
                                ->whereYear('POOrderedDate', date('Y', strtotime($orderedDatefinanceYear->bigginingDate)))
                                ->orderBy('purchaseOrderID', 'desc')
                                ->first();
                        } else {
                            $isPoDocForYear = ProcumentOrder::where('companySystemID', $input['companySystemID'])
                                ->where('documentSystemID', $input['documentSystemID'])
                                ->whereYear('POOrderedDate', date('Y', strtotime($orderedDatefinanceYear->bigginingDate)))
                                ->orderBy('purchaseOrderID', 'desc')
                                ->first();
                        }
                        
                        if($isPoDocForYear){
                            $docLastSerialNumber = intval($isPoDocForYear->serialNumber) + 1;
                        } else {
                            $docLastSerialNumber = 1;
                        }
                    }
                }
                $serialCode = str_pad($docLastSerialNumber, $documentCodeMaster->serial_length, '0', STR_PAD_LEFT);
                

                if($documentCodeMaster->serialization == 0){
                    if($docCodeSetupCommon){
                        $prefix = $docCodeSetupCommon->document_code_transactions->master_prefix; //format5
                        $formats[5] = $prefix;
    
                        $finalCode = $this->generateFinalCode($docCodeSetupCommon,$formats,$serialCode);
    
                        return ['status' => true,'message'=>'Document Code generated','documentCode'=>$finalCode,'docLastSerialNumber'=>$docLastSerialNumber];
    
                    } else {
                        return ['status' => false,'message'=>'Document Code not generated'];
                    }
                } else {

                    if($input['poTypeID'] == 1){ // From Request
                        $typeID = 2;
                    } else { // Direct
                        $typeID = 1;
                    }

                    $docCodeSetupTypeBased = DocCodeSetupTypeBased::with('type')->where('type_id', $typeID)->where('master_id', $documentCodeMasterID)->first();
                    if($docCodeSetupTypeBased){
                        $prefix = $docCodeSetupTypeBased->type->type_prefix; //format5
                        $formats[5] = $prefix;
    
                       $finalCode = $this->generateFinalCode($docCodeSetupTypeBased,$formats,$serialCode);
    
                        return ['status' => true,'message'=>'Document Code generated','documentCode'=>$finalCode,'docLastSerialNumber'=>$docLastSerialNumber];
                    } else {
                        return ['status' => false,'message'=>'Document Code not generated'];
                    }

                }
                break;
            default:
            return ['status' => false,'message'=>'Document Code not generated'];
        }
        
    }

    function getDocumentCodeSetupValues($companyID, $segmentCode) {

        $company = Company::with('country')->find($companyID);

        // Formatting Values
        $companyCode = $company->CompanyID; // format1
        $countryName = $company->country->countryName; // format2
        $segmentCode = $segmentCode; // format3
        $blank = ' '; // format4
    
        $financeYear = CompanyFinanceYear::where('companySystemID', $companyID)
            ->where('isCurrent', -1)
            ->where('isActive', -1)
            ->first();
    
        if ($financeYear) {
    
            if (Carbon::parse($financeYear->bigginingDate)->format('Y') != Carbon::parse($financeYear->endingDate)->format('Y')) {
                $YYYY = Carbon::parse($financeYear->bigginingDate)->format('Y') . '-' . Carbon::parse($financeYear->endingDate)->format('y'); // format6
                $YY = Carbon::parse($financeYear->bigginingDate)->format('y') . '-' . Carbon::parse($financeYear->endingDate)->format('y'); // format7
            } else {
                $YYYY = Carbon::parse($financeYear->bigginingDate)->format('Y'); // format6
                $YY = $financeYear->bigginingDate ? Carbon::parse($financeYear->bigginingDate)->format('y') : ''; // format7
            }
    
            $financePeriod = CompanyFinancePeriod::where('companySystemID', $companyID)
                ->where('companyFinanceYearID', $financeYear->companyFinanceYearID)
                ->where('isCurrent', -1)
                ->where('isActive', -1)
                ->first();
            if ($financePeriod) {
                $MM = Carbon::parse($financePeriod->dateFrom)->format('m'); // format8
            } else {
                $MM = ''; // format8
            }
        } else {
            $YYYY = ''; // format6
            $YY = ''; // format7
            $MM = ''; // format8
        }
    
        $slash = '\\'; // format9
        $dash = '-'; // format10
    
        $formats = [
            1 => $companyCode,
            2 => $countryName,
            3 => $segmentCode,
            4 => $blank,
            6 => $YYYY,
            7 => $YY,
            8 => $MM,
            9 => $slash,
            10 => $dash,
        ];
    
        return $formats;
    }

    function generateFinalCode($docCodeSetup,$formats,$serialCode)
    {

        $formatsArray = [];
        for ($i = 1; $i <= 12; $i++) {
            $format = 'format' . $i;
            $formatsArray[] = $formats[$docCodeSetup->$format] ?? '';
        }

        return implode('', $formatsArray) . $serialCode;
    }
}