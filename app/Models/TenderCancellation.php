<?php

namespace App\Models;

use Eloquent as Model;
use App\Models\DocumentApproved;

class TenderCancellation extends Model
{
    public $table = 'srm_tender_cancellation';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'tender_id',
        'internal_comment',
        'external_comment',
        'document_system_id',
        'document_id',
        'company_id',
        'confirmed_yn',
        'confirmed_by_emp_system_id',
        'confirmed_by_name',
        'confirmed_date',
        'approved',
        'approved_date',
        'approved_by_user_system_id',
        'approval_remarks',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'approved_by_emp_name',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'id' => 'integer',
        'tender_id' => 'integer',
        'internal_comment' => 'string',
        'external_comment' => 'string',
        'document_system_id' => 'integer',
        'document_id' => 'string',
        'company_id' => 'integer',
        'confirmed_yn' => 'integer',
        'confirmed_by_emp_system_id' => 'integer',
        'confirmed_by_name' => 'string',
        'confirmed_date' => 'datetime',
        'approved' => 'integer',
        'approved_date' => 'datetime',
        'approved_by_user_system_id' => 'integer',
        'approval_remarks' => 'string',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'approved_by_emp_name' => 'string',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function tender()
    {
        return $this->belongsTo(TenderMaster::class, 'tender_id', 'id');
    }

    public function approved_user()
    {
        return $this->belongsTo(Employee::class, 'approved_by_user_system_id', 'employeeSystemID');
    }

    public function confirmed_user()
    {   
        return $this->belongsTo(Employee::class, 'confirmed_by_emp_system_id', 'employeeSystemID');
    }

    public static function getCancellationHistory(int $tenderId, int $companyId)
    {
        return self::select('id', 'tender_id', 'company_id', 'confirmed_yn', 'confirmed_by_emp_system_id', 'confirmed_by_name',
                    'confirmed_date', 'approved', 'approved_by_user_system_id', 'approved_date',
                    'approved_by_emp_name', 'internal_comment', 'external_comment', 'refferedBackYN'
            )
            ->where('tender_id', $tenderId)
            ->where('company_id', $companyId)
            ->with([
                'approved_user' => function ($q) {
                    $q->select('employeeSystemID', 'empName');
                }, 
                'confirmed_user' => function ($q) {
                    $q->select('employeeSystemID', 'empName');
                }
            ])
            ->orderBy('id', 'desc')
            ->get();
    }

    public static function getCancellationApprovalHistoryByTender(int $tenderId, int $companyId)
    {
        $cancellationIds = self::where('tender_id', $tenderId)
            ->where('company_id', $companyId)
            ->pluck('id')
            ->toArray();

        if (empty($cancellationIds)) {
            return collect([]);
        }

        return DocumentApproved::select(
            'documentApprovedID',
            'companySystemID',
            'documentSystemID',
            'documentSystemCode',
            'approvedComments',
            'employeeSystemID',
            'approvedDate',
            'rejectedYN',
            'approvedYN',
            'rejectedDate',
            'rejectedComments'
        )->with(['employee' => function ($q) {
            $q->select('employeeSystemID', 'empFullName');
        }])->where('documentSystemID', 134)
            ->whereIn('documentSystemCode', $cancellationIds)
            ->get();
    }

    public static function getByIdWithTender(int $id): ?self
    {
        return self::with('tender')->find($id);
    }

    public static function getPendingApprovalRequest(int $tenderId, int $companyId): ?self
    {
        return self::where('tender_id', $tenderId)
            ->where('company_id', $companyId)
            ->where('confirmed_yn', 1)
            ->where('approved', 0)
            ->where('refferedBackYN', 0)
            ->latest('id')
            ->first();
    }

    public static function getLatestByTenderAndCompany(int $tenderId, int $companyId): ?self
    {
        return self::where('tender_id', $tenderId)
            ->where('company_id', $companyId)
            ->latest('id')
            ->first();
    }
}

