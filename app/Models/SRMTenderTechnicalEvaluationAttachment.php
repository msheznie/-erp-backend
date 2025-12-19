<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Model;

class SRMTenderTechnicalEvaluationAttachment extends Model
{
    use Compoships;
    public $table = 'srm_tender_technical_evaluation_attachment';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'uuid',
        'comment',
        'document_system_id',
        'document_id',
        'tender_id',
        'company_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'comment' => 'string',
        'document_system_id' => 'integer',
        'document_id' => 'string',
        'tender_id' => 'integer',
        'company_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    public static function getEvaluationComment($companyId, $tenderId){
        $comment = SRMTenderTechnicalEvaluationAttachment::with([
            'DocumentAttachment' => function ($q)
            {
                $q->select('attachmentID', 'companySystemID', 'companyID', 'documentSystemID', 'documentID',
                    'documentSystemCode', 'path', 'originalFileName', 'myFileName');
            }
        ])
            ->select('id', 'uuid', 'comment', 'document_system_id', 'document_id', 'tender_id', 'company_id')
            ->where('tender_id', $tenderId)
            ->where('company_id', $companyId)
            ->first();

        return $comment ?? null;
    }

    public static function getEvaluationData($companyId, $tenderId){
        $evaluationData = SRMTenderTechnicalEvaluationAttachment::where('tender_id', $tenderId)
            ->where('company_id', $companyId)
            ->first();

        return $evaluationData;
    }

    public function DocumentAttachment()
    {
        return $this->hasOne('App\Models\DocumentAttachments',['documentSystemID', 'documentSystemCode',
            'companySystemID'], ['document_system_id', 'tender_id', 'company_id']);
    }

    public static function hasEvaluationComment($companyId, $tenderId){
        $evaluationData = SRMTenderTechnicalEvaluationAttachment::where('tender_id', $tenderId)
            ->where('company_id', $companyId)
            ->exists();

        return $evaluationData;
    }
}
