<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractSectionsMaster extends Model
{
    public $table = 'cm_contract_sections_master';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'cmSection_detail',
        'csm_active',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'cmSection_id' => 'integer',
        'cmSection_detail' => 'string',
        'csm_active' => 'boolean',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'cmSection_detail' => 'nullable|string|max:200',
        'csm_active' => 'required|boolean',
        'timestamp' => 'nullable'
    ];

    public function sectionDetail()
    {
        return $this->hasMany(ContractSectionDetail::class, 'sectionMasterId', 'cmSection_id');
    }
}
