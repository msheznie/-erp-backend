<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class LogUploadAssetCosting extends Model
{
    public $table = 'log_upload_asset_costing';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'assetCostingUploadID',
        'companySystemID',
        'isFailed',
        'logMessage',
        'errorLine'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'assetCostingUploadID' => 'integer',
        'companySystemID' => 'integer',
        'isFailed' => 'boolean',
        'logMessage' => 'string',
        'errorLine' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'assetCostingUploadID' => 'required',
        'isFailed' => 'required'
    ];
}
