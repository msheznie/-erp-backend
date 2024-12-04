<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SrmTenderPo extends Model
{
    protected $table = 'srm_tender_po';


    protected $fillable = [
        'po_id',
        'tender_id',
        'company_id',
        'status',
    ];

    public function procument_order()
    {
        return $this->belongsTo('App\Models\ProcumentOrder', 'po_id', 'purchaseOrderID');
    }
}
