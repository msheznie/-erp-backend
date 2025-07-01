<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="CircularSuppliersEditLog",
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
 *          property="circular_id",
 *          description="circular_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
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
 *          property="status",
 *          description="status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="supplier_id",
 *          description="supplier_id",
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
class CircularSuppliersEditLog extends Model
{

    public $table = 'srm_circular_suppliers_edit_log';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'circular_id',
        'created_by',
        'id',
        'is_deleted',
        'level_no',
        'status',
        'supplier_id',
        'updated_by',
        'created_by',
        'version_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'amd_id' => 'integer',
        'circular_id' => 'integer',
        'created_by' => 'integer',
        'id' => 'integer',
        'is_deleted' => 'integer',
        'level_no' => 'integer',
        'status' => 'integer',
        'supplier_id' => 'integer',
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
    public function supplier_registration_link()
    {
        return $this->belongsTo('App\Models\SupplierRegistrationLink', 'supplier_id', 'id');
    }

    public function srm_circular_amendments()
    {
        return $this->belongsTo('App\Models\CircularAmendmentsEditLog', 'circular_id', 'circular_id');
    }
    public static function getCircularSuppliers($circularID, $versionID)
    {
        return self::where('circular_id', $circularID)->where('version_id', $versionID)->where('is_deleted', 0)->get();
    }
    public static function getAmendRecords($circularID, $versionID, $onlyNullRecords){
        return self::where('circular_id', $circularID)
            ->where('version_id', $versionID)
            ->when($onlyNullRecords, function ($q) {
                $q->whereNull('id');
            })
            ->when(!$onlyNullRecords, function ($q) {
                $q->whereNotNull('id');
            })
            ->where('is_deleted', 0)->get();
    }
}
