<?php

namespace App\Models;

use Eloquent as Model;

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
}
