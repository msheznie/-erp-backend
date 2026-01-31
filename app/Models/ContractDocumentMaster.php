<?php

namespace App\Models;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractDocumentMaster extends Model
{
    use SoftDeletes;
    public $table = 'cm_document_master';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    protected $hidden = ['id'];


    public $fillable = [
        'uuid',
        'documentType',
        'description',
        'status',
        'companySystemID',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'documentType' => 'string',
        'description' => 'string',
        'status' => 'boolean',
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
        'uuid' => 'required|string|max:255',
        'documentType' => 'required|string|max:255',
        'description' => 'nullable|string|max:255',
        'status' => 'nullable|boolean',
        'companySystemID' => 'required|integer',
        'created_by' => 'nullable|integer',
        'updated_by' => 'nullable|integer',
        'deleted_at' => 'nullable',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];


    public function contractDocument() {
        return $this->hasMany(ContractDocument::class, 'documentType', 'id');
    }
}
