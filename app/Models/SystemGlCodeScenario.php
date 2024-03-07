<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SystemGlCodeScenario",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      )
 * )
 */
class SystemGlCodeScenario extends Model
{

    public $table = 'system_gl_code_scenario';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'documentSystemID',
        'isActive',
        'description',
        'department_master_id'

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'isActive' => 'integer',
        'documentSystemID' => 'integer',
        'description' => 'string',
        'department_master_id' => 'integer'

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function company_scenario(){
        return $this->hasOne(SystemGlCodeScenarioDetail::class, 'systemGlScenarioID', 'id');
    }

    public function detail()
    {
        return $this->belongsTo('App\Models\SystemGlCodeScenarioDetail', 'id', 'systemGlScenarioID');
    }
}
