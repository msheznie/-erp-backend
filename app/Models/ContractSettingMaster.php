<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractSettingMaster extends Model
{
    public $table = 'cm_contract_setting_master';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'uuid',
        'contractId',
        'contractTypeSectionId',
        'isActive'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'contractId' => 'integer',
        'contractTypeSectionId' => 'integer',
        'isActive' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'contractId' => 'required|integer',
        'contractTypeSectionId' => 'required|integer',
        'isActive' => 'nullable|boolean',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    public function contractTypeSection()
    {
        return $this->belongsTo(ContractTypeSections::class, 'contractTypeSectionId', 'ct_sectionId');
    }

    public static function getContractTypeSectionDetail($contractMasterId)
    {
        return ContractSettingMaster::with([
            'contractTypeSection' => function ($q) {
                $q->select('ct_sectionId', 'cmSection_id', 'contract_typeId', 'companySystemID')
                    ->with(['contractSectionWithTypes' => function ($q1)
                    {
                        $q1->select('cmSection_id','cmSection_detail')
                            ->with(['sectionDetail']);
                    }]);
            }
        ])->where('contractId', $contractMasterId)
            ->get();
    }
}
