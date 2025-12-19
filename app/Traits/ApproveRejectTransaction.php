<?php

namespace App\Traits;
use App\Models\DocumentApproved;
use App\Models\UserActivityLog;
use App\Scopes\ActiveScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
trait ApproveRejectTransaction
{

    /**
     * @param int $documentSystemID
     * @param int $documentSystemCode
     * @param int $rollLevelOrder
     * @param string $companySystemID
     * @return array
     */
    public static function previousDocumentApprovers($documentSystemID, $documentSystemCode, $rollLevelOrder, $companySystemID = null)
    {
        return DocumentApproved::where("documentSystemID", $documentSystemID)
            ->where("documentSystemCode", $documentSystemCode)
            ->where("rollLevelOrder", "<" ,$rollLevelOrder)
            ->where("approvedYN", -1)
            ->when($companySystemID != null, function ($q) use($companySystemID){
                return $q->where('companySystemID', $companySystemID);
            })
            ->get();
    }

    public static function getConfirmedUser($documentSystemID, $documentSystemCode, $companySystemID)
    {
        return DocumentApproved::where("documentSystemID", $documentSystemID)
            ->where("documentSystemCode", $documentSystemCode)
            ->where("companySystemID", $companySystemID)
            ->where("approvedYN", -1)
            ->first();
    }

}
