<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="TenderBoqItemsEditLog",
 *      required={""},
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
 *          property="description",
 *          description="description",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
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
 *          property="item_name",
 *          description="item_name",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="main_work_id",
 *          description="main_work_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="master_id",
 *          description="master_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="modify_type",
 *          description="modify_type",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="qty",
 *          description="qty",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="number",
 *          format="number"
 *      ),
 *      @OA\Property(
 *          property="tender_edit_version_id",
 *          description="tender_edit_version_id",
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
 *          property="tender_ranking_line_item",
 *          description="tender_ranking_line_item",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="uom",
 *          description="uom",
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
 *      )
 * )
 */
class TenderBoqItemsEditLog extends Model
{

    public $table = 'srm_tender_boq_items_edit_log';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $primaryKey = 'amd_id';

    public $fillable = [
        'id',
        'company_id',
        'description',
        'item_name',
        'main_work_id',
        'amd_main_work_id',
        'master_id',
        'modify_type',
        'qty',
        'tender_edit_version_id',
        'tender_id',
        'tender_ranking_line_item',
        'uom',
        'ref_log_id',
        'updated_by',
        'created_by',
        'level_no',
        'is_deleted',
        'purchase_request_id',
        'item_primary_code',
        'origin'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amd_id' => 'integer',
        'company_id' => 'integer',
        'description' => 'string',
        'id' => 'integer',
        'item_name' => 'string',
        'main_work_id' => 'integer',
        'amd_main_work_id' => 'integer',
        'master_id' => 'integer',
        'modify_type' => 'integer',
        'qty' => 'float',
        'tender_edit_version_id' => 'integer',
        'tender_id' => 'integer',
        'tender_ranking_line_item' => 'integer',
        'uom' => 'integer',
        'level_no' => 'integer',
        'is_deleted' => 'integer',
        'item_primary_code' => 'string',
        'origin' => 'integer',
        'purchase_request_id' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public static function getLevelNo($id){
        return max(1, (self::where('id', $id)->max('level_no') ?? 0) + 1);
    }
    public static function checkExistsBoqItem($scheduleDetailAmdID, $versionID){
        return self::where('amd_main_work_id', $scheduleDetailAmdID)->where('tender_edit_version_id', $versionID)->where('is_deleted', 0)->exists();
    }
    public static function getTenderBoqItemList($main_work_id, $versionID){
        return self::where('amd_main_work_id', $main_work_id)->where('tender_edit_version_id', $versionID)->where('is_deleted', 0)->get();
    }
    public static function getAmendRecords($tenderID, $versionID, $amd_mainWorkID, $onlyNullRecords){
        return self::where('tender_id', $tenderID)
            ->where('amd_main_work_id', $amd_mainWorkID)
            ->where('tender_edit_version_id', $versionID)
            ->where('is_deleted', 0)
            ->when($onlyNullRecords, function ($q) {
                $q->whereNull('id');
            })->when(!$onlyNullRecords, function ($q) {
                $q->whereNotNull('id');
            })->get();
    }

    public static function checkItemNameExists($itemName, $amd_mainWorkID, $id = 0){
        return self::where('item_name',$itemName)
            ->when($id > 0, function ($q) use ($id) {
                $q->where('amd_id', '!=', $id);
                $q->whereNull('id');
            })
            ->where('is_deleted', 0)
            ->where('amd_main_work_id', $amd_mainWorkID)->first();
    }
    public static function checkPRAlreadyAdded($tender_id, $purchaseRequestIDToCheck, $main_work_id, $versionID){
        return self::where('tender_id', $tender_id)
            ->whereRaw("FIND_IN_SET('$purchaseRequestIDToCheck', purchase_request_id) > 0")
            ->where('amd_main_work_id', $main_work_id)
            ->where('tender_edit_version_id', $versionID)
            ->where('is_deleted', 0)
            ->first();
    }
}
