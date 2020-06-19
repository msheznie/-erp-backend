<?php


namespace App\helper;


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
                case 11:
                    if (($entity->directdetail && count($entity->directdetail)) || (($entity->detail && count($entity->detail)))) {
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
        $changeKeys = array();
        foreach ($keys as $key) {
            if (isset($entity[$key]) && isset($input[$key]) && $entity[$key] != $input[$key]) {
                array_push($changeKeys, $key);
            }
        }
        return $changeKeys;
    }

}
