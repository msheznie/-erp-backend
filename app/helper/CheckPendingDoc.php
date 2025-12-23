<?php

namespace App\helper;

class CheckPendingDoc
{

    public static function process($documentSystemID,$supplierID,$companyID)
    {
        switch ($documentSystemID) {
            case 11:
                $docInforArr["tableName"] = 'erp_bookinvsuppmaster';
                $docInforArr["modelName"] = 'BookInvSuppMaster';
                $docInforArr["supplierID"] = 'supplierID';
                $docInforArr["confirmedYN"] = 'confirmedYN';
                $docInforArr["approved"] = 'approved';
                $docInforArr["companyID"] = 'companySystemID';
                break;
            case 3:
                $docInforArr["tableName"] = 'erp_grvmaster';
                $docInforArr["modelName"] = 'GRVMaster';
                $docInforArr["supplierID"] = 'supplierID';
                $docInforArr["confirmedYN"] = 'grvConfirmedYN';
                $docInforArr["approved"] = 'approved';
                $docInforArr["companyID"] = 'companySystemID';
                break;
            case 4:
                $docInforArr["tableName"] = 'erp_paysupplierinvoicemaster';
                $docInforArr["modelName"] = 'PaySupplierInvoiceMaster';
                $docInforArr["supplierID"] = 'BPVsupplierID';
                $docInforArr["confirmedYN"] = 'confirmedYN';
                $docInforArr["approved"] = 'approved';
                $docInforArr["companyID"] = 'companySystemID';
                break;
            default:
                return ['success' => false, 'message' => 'Document ID not set'];
    }

    $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; 
    $document = $namespacedModel::where($docInforArr["supplierID"], $supplierID)->where($docInforArr["companyID"], $companyID)->where($docInforArr["confirmedYN"], 1)->where($docInforArr["approved"], 0)->first();

    
    if ($document) {
        $output['value'] = false;
        return $output;
    }
 
    $output['value'] = true;
    return $output;
}
    
}
