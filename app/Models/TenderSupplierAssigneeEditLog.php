<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Schema(
 *      schema="TenderSupplierAssigneeEditLog",
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
 *          property="created_by",
 *          description="created_by",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
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
 *          property="is_deleted",
 *          description="is_deleted",
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
 *          property="mail_sent",
 *          description="mail_sent",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="registration_link_id",
 *          description="registration_link_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="registration_number",
 *          description="registration_number",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="supplier_assigned_id",
 *          description="supplier_assigned_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="supplier_email",
 *          description="supplier_email",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="supplier_name",
 *          description="supplier_name",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="tender_master_id",
 *          description="tender_master_id",
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
 *          property="updated_by",
 *          description="updated_by",
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
 *      )
 * )
 */
class TenderSupplierAssigneeEditLog extends Model
{

    public $table = 'srm_tender_supplier_assignee_edit_log';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'amd_id';


    public $fillable = [
        'company_id',
        'created_by',
        'id',
        'is_deleted',
        'level_no',
        'mail_sent',
        'registration_link_id',
        'registration_number',
        'supplier_assigned_id',
        'supplier_email',
        'supplier_name',
        'tender_master_id',
        'updated_by',
        'version_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amd_id' => 'integer',
        'company_id' => 'integer',
        'created_by' => 'integer',
        'id' => 'integer',
        'is_deleted' => 'integer',
        'level_no' => 'integer',
        'mail_sent' => 'integer',
        'registration_link_id' => 'integer',
        'registration_number' => 'string',
        'supplier_assigned_id' => 'integer',
        'supplier_email' => 'string',
        'supplier_name' => 'string',
        'tender_master_id' => 'integer',
        'updated_by' => 'integer',
        'version_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function supplierAssigned(){
        return $this->hasOne('App\Models\SupplierAssigned', 'supplierAssignedID','supplier_assigned_id');

    }

    public static function getLevelNo($attachmentID){
        return max(1, (self::where('id', $attachmentID)->max('level_no') ?? 0) + 1);
    }
    public static function getAssignSupplierCount($companyId, $id, $versionID)
    {
        return self::where('company_id', $companyId)
            ->where('tender_master_id', $id)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->count();
    }
    public static function getSupplierAssignedListQry($companyId, $tenderMasterId, $versionID){
        return self::with(['supplierAssigned'])
            ->where('company_id', $companyId)
            ->where('tender_master_id', $tenderMasterId)
            ->where('version_id', $versionID)
            ->where('is_deleted', 0);
    }
    public static function getAmendRecords($versionID, $tenderMasterID, $onlyNullRecords){
        return self::where('version_id', $versionID)
            ->where('tender_master_id', $tenderMasterID)
            ->where('is_deleted', 0)
            ->when($onlyNullRecords, function ($q) {
                $q->whereNull('id');
            })
            ->when(!$onlyNullRecords, function ($q) {
                $q->whereNotNull('id');
            })->get();
    }
    public static function getAssignSupplier($companySystemID, $tenderMasterId, $versionID)
    {
        return TenderSupplierAssigneeEditLog::select('amd_id', 'tender_master_id', 'company_id', 'supplier_assigned_id')
            ->with(['supplierAssigned' => function ($q) {
                $q->select('supplierAssignedID', 'supplierCodeSytem', 'supplierName');
                $q->with([
                    'supplierRegistrationLink' => function ($q) {
                        $q->select(
                            DB::raw('id as purchased_by'),
                            'name', 'supplier_master_id');
                    },
                ]);
            },
            ])
            ->where('version_id', $versionID)
            ->where('is_deleted', 0)
            ->where('company_id', $companySystemID)
            ->where('tender_master_id', $tenderMasterId)
            ->get();
    }
}
