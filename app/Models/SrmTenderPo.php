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

    /**
     * Get active tender PO IDs for a company
     *
     * @param int $companyId
     * @return array
     */
    public static function getActiveTenderPOIds($companyId)
    {
        return self::where('company_id', $companyId)
            ->where('status', 1)
            ->pluck('po_id')
            ->toArray();
    }
}
