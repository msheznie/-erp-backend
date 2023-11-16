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
}
