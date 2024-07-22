<?php

namespace App\Models;

use Eloquent as Model;

class SupplierEvaluation extends Model
{
    public $table = 'supplier_evaluation';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'evaluationSerialNo',
        'evaluationCode',
        'evaluationType',
        'evaluationTemplate',
        'documentCode',
        'documentId',
        'documentSystemCode',
        'supplierId',
        'supplierCode',
        'supplierName',
        'companySystemID',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'evaluationSerialNo' => 'integer',
        'evaluationCode' => 'string',
        'evaluationType' => 'integer',
        'evaluationTemplate' => 'integer',
        'documentCode' => 'string',
        'documentId' => 'integer',
        'documentSystemCode' => 'string',
        'supplierId' => 'integer',
        'supplierCode' => 'string',
        'supplierName' => 'string',
        'companySystemID' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function createdBy()
    {
        return $this->belongsTo('App\Models\Employee', 'created_by', 'employeeSystemID');
    }
}
