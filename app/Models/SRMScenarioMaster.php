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

    public static function getAllEmailMaster($params = [])
    {
        $query = self::select('id', 'document_id', 'email_scenario_name', 'email_scenario_code')
            ->with(['scenarioDetails' => function ($q) use ($params) {

                $q->select('scenario_master_id', 'cc_emails')
                ->where('company_system_id', $params['companyId']);
            }])
            ->where('is_active', 1);

        return $query;
    }

    public function scenarioDetails()
    {
        return $this->hasOne(SRMScenarioDetails::class,'scenario_master_id', 'id');
    }

    public static function getSrmScenarioMaster($scenarioId)
    {
        return self::where('id', $scenarioId)->where('is_active', 1)->first();
    }

    public static function getScenarioMasterIdByDocumentAndCode(int $documentId, string $emailScenarioCode, ?int $companyId = null): ?int
    {
        $query = self::where('document_id', $documentId)
            ->where('email_scenario_code', $emailScenarioCode)
            ->where('is_active', 1);
        if ($companyId !== null) {
            $query->where('company_id', $companyId);
        }
        $master = $query->first();
        return $master ? (int) $master->id : null;
    }
}
