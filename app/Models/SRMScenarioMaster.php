<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="SRMScenarioMaster",
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
 *          property="document_id",
 *          description="document_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="email_scenario_code",
 *          description="email_scenario_code",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="email_scenario_name",
 *          description="email_scenario_name",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
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
 *          property="is_active",
 *          description="is_active",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      )
 * )
 */
class SRMScenarioMaster extends Model
{

    public $table = 'srm_email_scenario_master';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public $fillable = [
        'document_id',
        'email_scenario_code',
        'email_scenario_name',
        'company_id',
        'is_active'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'document_id' => 'integer',
        'email_scenario_code' => 'string',
        'email_scenario_name' => 'string',
        'company_id' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'document_id' => 'required',
        'email_scenario_code' => 'required',
        'email_scenario_name' => 'required'
    ];

    public static function getAllEmailMaster()
    {
        $query = self::select('id', 'document_id', 'email_scenario_name')
            ->where('is_active', 1);

        return $query;
    }

}
