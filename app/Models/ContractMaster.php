<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Facades\DB;

class ContractMaster extends Model
{
    public $table = 'cm_contract_master';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    protected $hidden = ['contractType' , 'created_by'];



    public $fillable = [
        'uuid',
        'contractCode',
        'serial_no',
        'documentMasterId',
        'title',
        'description',
        'contractType',
        'counterParty',
        'counterPartyName',
        'referenceCode',
        'contractOwner',
        'contractAmount',
        'effective_date',
        'startDate',
        'endDate',
        'agreementSignDate',
        'contractTermPeriod',
        'contractRenewalDate',
        'contractExtensionDate',
        'contractTerminateDate',
        'contractRevisionDate',
        'primaryCounterParty',
        'primaryEmail',
        'primaryPhoneNumber',
        'secondaryCounterParty',
        'secondaryEmail',
        'secondaryPhoneNumber',
        'status',
        'confirmed_yn',
        'confirmed_date',
        'confirm_by',
        'confirmed_comment',
        'rollLevelOrder',
        'refferedBackYN',
        'approved_yn',
        'approved_by',
        'approved_date',
        'timesReferred',
        'companySystemID',
        'created_by',
        'updated_by',
        'is_amendment',
        'is_addendum',
        'is_renewal',
        'is_extension',
        'is_revision',
        'is_termination',
        'parent_id',
        'tender_id',
        'contract_history_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'contractCode' => 'string',
        'documentMasterId' => 'integer',
        'serial_no' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'contractType' => 'integer',
        'counterParty' => 'integer',
        'counterPartyName' => 'integer',
        'referenceCode' => 'string',
        'contractOwner' => 'integer',
        'contractAmount' => 'double',
        'effective_date' => 'integer',
        'startDate' => 'string',
        'endDate' => 'string',
        'agreementSignDate' => 'string',
        'contractTermPeriod' => 'string',
        'contractRenewalDate' => 'string',
        'contractExtensionDate' => 'string',
        'contractTerminateDate' => 'string',
        'contractRevisionDate' => 'string',
        'primaryCounterParty' => 'string',
        'primaryEmail' => 'string',
        'primaryPhoneNumber' => 'string',
        'secondaryCounterParty' => 'string',
        'secondaryEmail' => 'string',
        'secondaryPhoneNumber' => 'string',
        'status' => 'integer',
        'confirmed_yn' => 'integer',
        'confirmed_date' => 'datetime',
        'confirm_by' => 'integer',
        'confirmed_comment' => 'string',
        'rollLevelOrder' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'approved_yn' => 'integer',
        'approved_by' => 'integer',
        'approved_date' => 'datetime',
        'companySystemID' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'is_amendment' => 'integer',
        'is_addendum' => 'integer',
        'is_renewal' => 'integer',
        'is_extension' => 'integer',
        'is_revision' => 'integer',
        'is_termination' => 'integer',
        'parent_id' => 'integer',
        'tender_id' => 'integer',
        'contract_history_id' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'supplierId'  => 'required',
    ];

    public static function getContractUuid($companySystemId, $contractId)
    {
        return ContractMaster::select('uuid')
            ->where('companySystemID', $companySystemId)
            ->where('id', $contractId)
            ->first();
    }

    public static function getContractDataBySupplier($supplierId)
    {
        return self::with(['contractTypes'])
            ->with(['contractUsers' => function ($q) use ($supplierId) {
                $q->where('contractUserId', $supplierId)
                    ->with(['contractSupplierUser']);
            }])
            ->where('approved_yn', 1)
            ->where('counterParty', 1)
            ->whereHas('contractUsers', function ($q) use ($supplierId) {
                $q->where('contractUserId', $supplierId);
            });
    }

    public function contractTypes()
    {
        return $this->belongsTo(ContractTypes::class, 'contractType', 'contract_typeId');
    }

    public function contractUsers()
    {
        return $this->belongsTo(ContractUsers::class, 'counterPartyName', 'id');
    }

    public function contract_status()
    {
        return $this->hasMany(ContractStatusHistory::class, 'contract_id','id');
    }
    public static function getStandaloneContractsForReport($companyId, $linkedContractIds, $dateFrom = null, $dateTo = null)
    {
        return self::query()
            ->where('companySystemID', $companyId)
            ->when(!empty($linkedContractIds), function ($q) use ($linkedContractIds) {
                return $q->whereNotIn('id', $linkedContractIds);
            })
            ->when($dateFrom, function ($q) use ($dateFrom) {
                return $q->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo, function ($q) use ($dateTo) {
                return $q->whereDate('created_at', '<=', $dateTo);
            })
            ->select([
                'id',
                'contractCode',
                'startDate',
                'endDate',
                'agreementSignDate',
                'created_at'
            ])
            ->orderByDesc('created_at');
    }
    public static function loadContractRelationshipsForReport(array $contractIds): array
    {
        if (empty($contractIds)) {
            return [];
        }

        $contracts = DB::table('cm_contract_master')
            ->whereIn('id', $contractIds)
            ->select('id', 'contractCode', 'startDate', 'endDate', 'agreementSignDate')
            ->get()
            ->keyBy('id');

        $statuses = DB::table('cm_contract_status_history')
            ->whereIn('contract_id', $contractIds)
            ->whereIn('status', [1, 2, 3, 4, 5, 6])
            ->select('contract_id', 'status')
            ->get()
            ->groupBy('contract_id');

        return $contracts->mapWithKeys(function ($contract, $contractId) use ($statuses) {
            return [
                $contractId => [
                    'contract' => $contract,
                    'statuses' => $statuses->get($contractId, collect()),
                ]
            ];
        })->toArray();
    }
    
    public function contractOwners()
    {
        return $this->belongsTo(ContractUsers::class, 'contractOwner', 'id');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(Employee::class, 'confirm_by', 'employeeSystemID');
    }

    public function tenderMaster()
    {
        return $this->belongsTo(TenderMaster::class, 'tender_id', 'id');
    }
    public function counterParties()
    {
        return $this->belongsTo(ContractCounterPartyMaster::class, 'counterParty', 'cmCounterParty_id');
    }

    public static function getContractMasterById($uuid)
    {
        return self::select('id', 'uuid', 'contractCode', 'title', 'description', 'contractType',
            'counterParty', 'counterPartyName', 'referenceCode', 'contractOwner', 'contractAmount', 'startDate',
            'endDate', 'agreementSignDate', 'contractTermPeriod', 'contractRenewalDate', 'companySystemID',
            'contractExtensionDate', 'contractTerminateDate', 'contractRevisionDate', 'primaryCounterParty',
            'primaryEmail', 'primaryPhoneNumber', 'secondaryCounterParty', 'secondaryEmail', 'secondaryPhoneNumber',
            'tender_id', 'effective_date', 'confirmed_yn', 'confirmed_date', 'confirm_by'
        )
            ->with([
                'contractTypes' => function ($q)
                {
                    $q->select('contract_typeId', 'cm_type_name');
                }, 'contractOwners' => function ($q)
                {
                    $q->select('id', 'contractUserCode', 'contractUserName');
                }, 'counterParties' => function ($q)
                {
                    $q->select('cmCounterParty_id', 'cmCounterParty_name');
                }, 'contractUsers' => function ($q)
                {
                    $q->select('id', 'contractUserCode', 'contractUserName');
                }, 'tenderMaster' => function ($q)
                {
                    $q->select('id', 'title');
                }, 'confirmedBy' => function ($q1)
                {
                    $q1->select('employeeSystemID', 'empName');
                }
            ])
            ->where('uuid', $uuid)
            ->first();
    }

    public static function getConfirmationData($uuid)
    {
        return self::select('confirmed_yn', 'confirmed_date', 'confirm_by')
            ->with(['confirmedBy' => function ($q1)
            {
                $q1->select('employeeSystemID', 'empName');
            }])
            ->where('uuid', $uuid)->first();
    }
}
