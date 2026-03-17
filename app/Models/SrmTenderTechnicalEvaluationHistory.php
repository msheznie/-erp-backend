<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SrmTenderTechnicalEvaluationHistory extends Model
{
    public $table = 'srm_tender_technical_evaluation_history';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'tender_id',
        'company_id',
        'negotiation_id',
        'negotiation_code',
        'round_no',
        'comment',
        'attachment_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'id' => 'integer',
        'tender_id' => 'integer',
        'company_id' => 'integer',
        'negotiation_id' => 'integer',
        'round_no' => 'integer',
        'attachment_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function attachment()
    {
        return $this->hasOne(DocumentAttachments::class, 'attachmentID', 'attachment_id');
    }

    public function createdByEmployee()
    {
        return $this->hasOne(Employee::class, 'employeeSystemID', 'created_by');
    }

    public static function getCurrentNegotiaitionData($tenderId, $companyId, $latestNegotiation) {
        return self::with([
            'attachment' => function ($q) {
                $q->select('attachmentID', 'attachmentDescription', 'originalFileName');
            },
        ])
            ->where('tender_id', $tenderId)
            ->where('company_id', $companyId)
            ->where('negotiation_id', $latestNegotiation->id)
            ->where('round_no', $latestNegotiation->version)
            ->first();
    }
    public static function tenderNegotiaitonTechEvaluationHistory($tenderId, $companyId){
        return self::with([
            'attachment' => function ($q) {
                $q->select('attachmentID', 'attachmentDescription', 'originalFileName');
            },
            'createdByEmployee' => function ($q) {
                $q->select('employeeSystemID', 'empFullName');
            },
        ])
            ->where('tender_id', $tenderId)
            ->where('company_id', $companyId)
            ->orderBy('round_no', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public static function checkExistHistory($documentSystemCode, $companySystemID, $negotiationId, $roundNo){
        return self::where('tender_id', $documentSystemCode)
            ->where('company_id', $companySystemID)
            ->where('negotiation_id', $negotiationId)
            ->where('round_no', $roundNo)
            ->exists();
    }
}

