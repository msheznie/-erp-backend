<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="SRMScenarioDetails",
 *      required={"scenario_master_id","email_body"},
 *      @OA\Property(property="id", type="integer", format="int64", readOnly=true),
 *      @OA\Property(property="scenario_master_id", type="integer"),
 *      @OA\Property(property="email_subject", type="string", maxLength=500, nullable=true),
 *      @OA\Property(
 *          property="cc_emails",
 *          type="array",
 *          @OA\Items(type="string", format="email"),
 *          nullable=true,
 *          description="Optional CC email list stored as JSON array"
 *      ),
 *      @OA\Property(property="email_body", type="string"),
 *      @OA\Property(property="company_system_id", type="integer", nullable=true),
 *      @OA\Property(property="created_by", type="integer", nullable=true),
 *      @OA\Property(property="created_at", type="string", format="date-time", readOnly=true),
 *      @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true)
 * )
 */
class SRMScenarioDetails extends Model
{
    public $table = 'srm_email_scenario_details';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'scenario_master_id',
        'email_subject',
        'cc_emails',
        'email_body',
        'company_system_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'scenario_master_id' => 'integer',
            'email_subject' => 'string',
            'cc_emails' => 'array',
            'email_body' => 'string',
            'company_system_id' => 'integer',
            'created_by' => 'integer',
        ];
    }

    public static function getScenarioDetailsById($data)
    {
        return self::select('id','email_subject','scenario_master_id','cc_emails','email_body')
            ->with(['attachments'])
            ->where('scenario_master_id', $data['scenarioId'])
            ->where('company_system_id', $data['companyId'])
            ->first();
    }

    public function attachments()
    {
        return $this->hasMany(SRMScenarioAttachments::class, 'scenario_detail_id','id');
    }
}
