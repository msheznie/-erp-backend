<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenderPurchaseRequest extends Model
{
    public $table = 'srm_tender_purchase_request';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = ['tender_id', 'purchase_request_id', 'company_id', 'created_at', 'updated_at'];

    protected $casts = [
        'id' => 'integer',
        'tender_id' => 'integer',
        'purchase_request_id' => 'integer',
        'company_id' => 'integer'
    ];

    public function purchase_request()
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id', 'purchaseRequestID');
    }

    public function tender()
    {
        return $this->belongsTo(TenderMaster::class, 'tender_id', 'id');
    }

    public static function getTenderPurchaseRequestForAmd($tenderID){
        return self::where('tender_id', $tenderID)->get();
    }
    public static function getTenderPurchaseForEdit($tenderMasterID){
        return self::select(
            'purchase_request_id as id',
            'erp_purchaserequest.purchaseRequestCode as itemName'
        )
            ->leftJoin('erp_purchaserequest', 'erp_purchaserequest.purchaseRequestID', '=', 'purchase_request_id')
            ->where('tender_id', $tenderMasterID)
            ->get();
    }
}
