<?php


namespace App\helper;


use Carbon\Carbon;

class CustomValidation
{


    /**
     * send emails
     * @param $documentSystemID : accept parameters as an integer
     * @param $entity : accept parameters as an object
     * @param $action : accept parameters as an integer // 1 - Create , 2 - Update , 3 - Delete
     * @return mixed
     */
    public static function validation($documentSystemID, $entity, $action, $input)
    {
        $docInfoArr = array();
        $errorMessage = "";
        $detailsExist = false;
        $updateAlreadyConfirmed = "Cannot update. Document already confirmed";
        $deleteAlreadyConfirmed = "Cannot delete. Document already confirmed";

        switch ($documentSystemID) {
            case 11: // Supplier Invoice
                $docInfoArr["modelName"] = 'BookInvSuppMaster';
                $docInfoArr["primaryKey"] = 'bookingSuppMasInvAutoID';
                $docInfoArr["approvedColumnName"] = 'approved';
                $docInfoArr["confirmedYN"] = "confirmedYN";
                $docInfoArr["detailRelation"] = "detail";
                $docInfoArr["keys"] = ['documentType', 'supplierID', 'supplierTransactionCurrencyID', 'financeYear'];
                break;
            case 4: // Payment voucher
                $docInfoArr["modelName"] = 'PaySupplierInvoiceMaster';
                $docInfoArr["primaryKey"] = 'PayMasterAutoId';
                $docInfoArr["approvedColumnName"] = 'approved';
                $docInfoArr["confirmedYN"] = 'confirmedYN';
                $docInfoArr["detailRelation"] = 'directdetail';
//                $docInfoArr["keys"] = ['invoiceType', 'payeeType', 'BPVsupplierID', 'directPaymentPayeeEmpID','supplierTransCurrencyID',
//                    'BPVbank','BPVAccount','expenseClaimOrPettyCash','directPaymentPayee','companyFinanceYearID'];
                $docInfoArr["keys"] = [];
                break;
            case 70: // Matching Document
                $docInfoArr["modelName"] = 'MatchDocumentMaster';
                $docInfoArr["primaryKey"] = 'matchDocumentMasterAutoID';
                $docInfoArr["approvedColumnName"] = 'approved';
                $docInfoArr["confirmedYN"] = 'matchingConfirmedYN';
                $docInfoArr["detailRelation"] = 'detail';
                $docInfoArr["keys"] = ['matchingDocdate'];
                break;
            case 21: // Receipt Voucher
                $docInfoArr["modelName"] = 'CustomerReceivePayment';
                $docInfoArr["primaryKey"] = 'custReceivePaymentAutoID';
                $docInfoArr["approvedColumnName"] = 'approved';
                $docInfoArr["confirmedYN"] = 'confirmedYN';
                $docInfoArr["detailRelation"] = 'details';
                $docInfoArr["keys"] = ['customerID','custTransactionCurrencyID','narration','bankID','bankAccount','bankCurrency',
                    'companyFinanceYearID','custChequeNo','expenseClaimOrPettyCash']; //'custChequeDate'
                break;
            default:
                return ['success' => false, 'message' => 'Document ID not found'];
        }

        if (!empty($entity)) {

            if ($entity[$docInfoArr["confirmedYN"]]) {
                if ($action == 2) {
                    $errorMessage = $updateAlreadyConfirmed;
                } else if ($action == 3) {
                    $errorMessage = $deleteAlreadyConfirmed;
                }
                return self::sendError($errorMessage);
            }

            switch ($documentSystemID) {
                case 11: // Supplier Invoice
                    if (($entity->directdetail && count($entity->directdetail)) || ($entity->detail && count($entity->detail)) || ($entity->item_details && count($entity->item_details))) {
                        $detailsExist = true;
                    }
                    break;
                case 4: // Payment voucher
                    if (($entity->directdetail && count($entity->directdetail)) ||
                        ($entity->advancedetail && count($entity->advancedetail)) ||
                        ($entity->supplierdetail && count($entity->supplierdetail))
                    ) {
                        $detailsExist = true;
                    }
                    break;
                case 70: //  Matching Document
                    if ($entity->detail && count($entity->detail)) {
                        $detailsExist = true;
                    }
                    break;
                case 21: // Receipt Voucher
                    if ( ($entity->details && count($entity->details)) || ($entity->directdetails && count($entity->directdetails))) {
                        $detailsExist = true;
                    }
                    break;
                default:
                    return ['success' => true, 'message' => 'Document is successfully validated'];
            }

            if($detailsExist){
                $errorMessage = self::checkValidate($entity,$input,$action,$docInfoArr["keys"]);
            }

            if ($errorMessage) {
                return self::sendError($errorMessage);
            }
        }

        return ['success' => true, 'message' => 'Document is successfully validated'];
    }

    private static function sendError($errorMessage)
    {
        return ['success' => false, 'message' => $errorMessage];
    }

    private static function checkValidate($entity,$input,$action,$keys){

        $updateDetailsAdded = "Cannot update. Details added";
        $deleteDetailsAdded = "Cannot delete. Details added";
        $errorMessage = "";
        if ($action == 2) {
            $changedKeys = self::checkValueChanges($entity, $input, $keys);
            if ($changedKeys) {
                $errorMessage = $updateDetailsAdded;
            }
        } else if ($action == 3) {
            $errorMessage = $deleteDetailsAdded;
        }

        return $errorMessage;
    }

    private static function checkValueChanges($entity, $input, $keys)
    {
        $dates = ['matchingDocdate'];
        $changeKeys = array();
        foreach ($keys as $key) {
            if (isset($entity[$key]) && isset($input[$key]) && $entity[$key] != $input[$key]) {
                if(in_array($key,$dates)){
                    $entity[$key]  = Carbon::parse($entity[$key])->format('Y-m-d');
                    $input[$key]   = Carbon::parse($input[$key])->format('Y-m-d');
                    if($entity[$key] != $input[$key]){
                        array_push($changeKeys, $key);
                    }
                }else{
                    array_push($changeKeys, $key);
                }
            }
        }
        return $changeKeys;
    }

}
