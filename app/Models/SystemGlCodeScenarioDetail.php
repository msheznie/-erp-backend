<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SystemGlCodeScenarioDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="systemGlScenarioID",
 *          description="systemGlScenarioID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountSystemID",
 *          description="chartOfAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SystemGlCodeScenarioDetail extends Model
{

    public $table = 'system_gl_code_scenario_details';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';




    public $fillable = [
        'systemGlScenarioID',
        'companySystemID',
        'chartOfAccountSystemID',
        'serviceLineSystemID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'systemGlScenarioID' => 'integer',
        'companySystemID' => 'integer',
        'chartOfAccountSystemID' => 'integer',
        'serviceLineSystemID' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function master()
    {
        return $this->belongsTo('App\Models\SystemGlCodeScenario', 'systemGlScenarioID', 'id');
    }

    public function chart_of_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountSystemID', 'chartOfAccountSystemID');
    }


    public static function getGlByScenario($companySystemID, $documentSystemID, $systemGlScenarioID)
    {
        $res = SystemGlCodeScenarioDetail::whereHas('master', function($query) use ($documentSystemID) {
                                            $query->where('documentSystemID', $documentSystemID);
                                        })
                                        ->where('companySystemID', $companySystemID)
                                        ->where('systemGlScenarioID', $systemGlScenarioID)
                                        ->first();

        return (($res) ? $res->chartOfAccountSystemID : null);
    }

    public static function getGlCodeByScenario($companySystemID, $documentSystemID, $systemGlScenarioID)
    {
        $res = SystemGlCodeScenarioDetail::whereHas('master', function($query) use ($documentSystemID) {
                                            $query->where('documentSystemID', $documentSystemID);
                                        })
                                        ->with(['chart_of_account'])
                                        ->whereHas('chart_of_account')
                                        ->where('companySystemID', $companySystemID)
                                        ->where('systemGlScenarioID', $systemGlScenarioID)
                                        ->first();

        return (($res) ? $res->chart_of_account->AccountCode : null);
    }
}
