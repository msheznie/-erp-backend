<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Schema(
 *      schema="SrmTenderUserAccessEditLog",
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
 *          property="version_id",
 *          description="version_id",
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
 *          property="user_id",
 *          description="user_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="module_id",
 *          description="module_id",
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
 *          property="updated_at",
 *          description="updated_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SrmTenderUserAccessEditLog extends Model
{

    public $table = 'srm_tender_user_access_edit_log';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $primaryKey = 'amd_id';

    public $fillable = [
        'id',
        'version_id',
        'level_no',
        'tender_id',
        'user_id',
        'module_id',
        'company_id',
        'is_deleted'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amd_id' => 'integer',
        'id' => 'integer',
        'version_id' => 'integer',
        'level_no' => 'integer',
        'tender_id' => 'integer',
        'user_id' => 'integer',
        'module_id' => 'integer',
        'company_id' => 'integer',
        'is_deleted' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'employeeSystemID', 'user_id');
    }
    public static function getLevelNo($id){
        return max(1, (self::where('id', $id)->max('level_no') ?? 0) + 1);
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
    public static function getTenderUserAccessDetails($tenderId, $companyId, $versionID){
        return self::select('id','tender_id','user_id','company_id','module_id')
            ->with(['employee' => function ($q){
                $q->select('employeeSystemID',DB::raw("CONCAT(empID, ' | ', empFullName) as empFullDetails"));
            }])
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->where('tender_id',$tenderId)
            ->where('company_id',$companyId)
            ->get();
    }
}
