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
        'supplier_id',
        'status',
    ];

    public function procument_order()
    {
        return $this->belongsTo('App\Models\ProcumentOrder', 'po_id', 'purchaseOrderID');
    }

    public function supplier()
    {
        return $this->belongsTo(SupplierRegistrationLink::class, 'supplier_id', 'id');
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

    public static function getActivePOsByTenderAndSuppliers(int $tenderId, array $supplierIds)
    {
        if (empty($supplierIds)) {
            return collect();
        }
        return self::where('tender_id', $tenderId)
            ->where('status', 1)
            ->whereIn('supplier_id', $supplierIds)
            ->with(['procument_order' => function ($q) {
                $q->select('purchaseOrderID', 'purchaseOrderCode');
            }])
            ->get();
    }
}
