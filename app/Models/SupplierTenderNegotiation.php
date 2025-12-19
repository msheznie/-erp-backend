<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Schema(
 *      schema="SupplierTenderNegotiation",
 *      required={""},
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="tender_negotiation_id",
 *          description="tender_negotiation_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="suppliermaster_id",
 *          description="suppliermaster_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          description="created_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SupplierTenderNegotiation extends Model
{

    public $table = 'suppliers_tender_negotiations';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'tender_negotiation_id',
        'suppliermaster_id',
        'srm_bid_submission_master_id',
        'bidSubmissionCode'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tender_negotiation_id' => 'integer',
        'suppliermaster_id' => 'integer',
        'srm_bid_submission_master_id' => 'integer',
        'bidSubmissionCode' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'tenderNegotiationId'=> 'required',
    ];


    public function bid_submission_master() {
        return $this->belongsTo('App\Models\BidSubmissionMaster', 'srm_bid_submission_master_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\SupplierRegistrationLink', 'suppliermaster_id', 'id');
    }

    public function supplierCustomEmail()
    {
        return $this->belongsTo('App\Models\TenderCustomEmail', 'suppliermaster_id', 'supplier_id');
    }

    public function SrmTenderBidNegotiation() {
        return $this->belongsTo('App\Models\TenderBidNegotiation', 'srm_bid_submission_master_id', 'bid_submission_master_id_old');
    }

    public static function getSupplierList($negotiationId, $tenderId)
    {
        $suppliers = SupplierTenderNegotiation::select('id', 'suppliermaster_id')->where('tender_negotiation_id', $negotiationId)
            ->whereDoesntHave('supplierCustomEmail', function ($query) use ($tenderId, $negotiationId) {
                $query->where('tender_id', $tenderId)->where('negotiation_id', $negotiationId);
            })
            ->with(['supplier' => function ($query) {
                $query->select('id','uuid', 'name');
            }])
            ->get()
            ->pluck('supplier');

        return $suppliers->map(function ($supplier) {
            return [
                'id' => $supplier->uuid,
                'name' => $supplier->name
            ];
        });
    }
}
