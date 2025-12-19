<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="TenderBudgetItemEditLog",
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
 *          property="budget_amount",
 *          description="budget_amount",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
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
 *          property="item_id",
 *          description="item_id",
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
class TenderBudgetItemEditLog extends Model
{

    public $table = 'srm_tender_budget_items_edit_log';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'amd_id';


    public $fillable = [
        'budget_amount',
        'id',
        'item_id',
        'level_no',
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
        'budget_amount' => 'float',
        'id' => 'integer',
        'item_id' => 'integer',
        'level_no' => 'integer',
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
        'id' => 'required'
    ];

    public static function getLevelNo($id){
        return max(1, (self::where('id', $id)->max('level_no') ?? 0) + 1);
    }
    public static function getExistingBudgetItem($itemId, $tenderMasterId, $versionID){
        return self::where('item_id', $itemId)
            ->where('tender_id', $tenderMasterId)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->first();
    }

    public static function getExistingTenderBudgetItemList($tenderID, $versionID){
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
