<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadAssetCosting extends Model
{
    public $table = 'upload_asset_costing';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'uploadComment',
        'uploadedDate',
        'uploadedBy',
        'uploadStatus',
        'counter',
        'companySystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'uploadComment' => 'string',
        'uploadedDate' => 'date',
        'uploadedBy' => 'string',
        'uploadStatus' => 'integer',
        'totalInvoices' => 'integer',
        'counter' => 'integer',
        'companySystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function uploaded_by()
    {
        return $this->belongsTo('App\Models\Employee', 'uploadedBy', 'empID');
    }
}
