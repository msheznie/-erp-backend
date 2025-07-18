<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="TenderPurchaseRequestEditLog",
 *      required={""},
 *      @OA\Property(
 *          property="amd_id",
 *          description="amd_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="company_id",
 *          description="company_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
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
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="level_no",
 *          description="level_no",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="purchase_request_id",
 *          description="purchase_request_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="tender_id",
 *          description="tender_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="version_id",
 *          description="version_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class TenderPurchaseRequestEditLog extends Model
{

    public $table = 'srm_tender_purchase_request_edit_log';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'amd_id';


    public $fillable = [
        'company_id',
        'id',
        'level_no',
        'purchase_request_id',
        'tender_id',
        'version_id',
        'is_deleted'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amd_id' => 'integer',
        'company_id' => 'integer',
        'id' => 'integer',
        'level_no' => 'integer',
        'purchase_request_id' => 'integer',
        'tender_id' => 'integer',
        'version_id' => 'integer',
        'is_deleted' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function purchase_request()
    {
        return $this->belongsTo(PurchaseRequest::class, 'purchase_request_id', 'purchaseRequestID');
    }

    public function tender()
    {
        return $this->belongsTo(SrmTenderMasterEditLog::class, 'tender_id', 'id');
    }

    public static function getLevelNo($id){
        return max(1, (self::where('id', $id)->max('level_no') ?? 0) + 1);
    }
    public static function getTenderPurchaseForEdit($tenderMasterID, $versionID){
        return self::select('purchase_request_id as id', 'erp_purchaserequest.purchaseRequestCode as itemName')
            ->leftJoin('erp_purchaserequest', 'erp_purchaserequest.purchaseRequestID', '=', 'purchase_request_id')
            ->where('tender_id', $tenderMasterID)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->get();
    }

    public static function getPurchaseRequests($tenderID, $versionID){
        return self::where('tender_id', $tenderID)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->get();
    }
    public static function getAmendRecords($versionID, $tenderMasterID, $onlyNullRecords){
        return self::where('version_id', $versionID)
            ->where('tender_id', $tenderMasterID)
            ->where('is_deleted', 0)
            ->when($onlyNullRecords, function ($q) {
                $q->whereNull('id');
            })
            ->when(!$onlyNullRecords, function ($q) {
                $q->whereNotNull('id');
            })
            ->get();
    }
}
