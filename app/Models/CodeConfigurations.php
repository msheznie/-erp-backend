<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodeConfigurations extends Model
{
    public $table = 'cm_code_configuration';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];
    protected $hidden = ['id', 'created_by', 'updated_by'];


    public $fillable = [
        'uuid',
        'serialization_based_on',
        'code_pattern',
        'company_id',
        'company_system_id',
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
        'serialization_based_on' => 'integer',
        'code_pattern' => 'string',
        'company_id' => 'string',
        'company_system_id' => 'integer',
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

    public static function getDocumentCodePattern($companySystemID, $serializationOn)
    {
        $codeConfig = CodeConfigurations::select('code_pattern')->where(
            [
                'company_system_id' => $companySystemID,
                'serialization_based_on' => $serializationOn
            ]
        )->first();
        return $codeConfig->code_pattern ?? null;
    }
}
