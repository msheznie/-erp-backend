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



    public $fillable = [
        'systemGlScenarioID',
        'companySystemID',
        'chartOfAccountSystemID',
        'serviceLineSystemID',
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

    public function company()
    {
        return $this->belongsTo(Company::class, 'companySystemID');
    }

    public function chart_of_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountSystemID', 'chartOfAccountSystemID');
    }

    public function chart_of_account_assigned()
    {
        return $this->belongsTo('App\Models\ChartOfAccountsAssigned', 'chartOfAccountSystemID', 'chartOfAccountSystemID');
    }


    public static function getGlByScenario($companySystemID, $documentSystemID, $slug)
    {
            $companySystemIDs = explode(',', $companySystemID);

            $systemGlScenario = SystemGlCodeScenario::where('slug', $slug)->first();
            if ($systemGlScenario) {
                $id = $systemGlScenario->id;
            } else {
                $id = null;
            }

            $res = SystemGlCodeScenarioDetail::whereIn('companySystemID', $companySystemIDs)
                ->where('systemGlScenarioID', $id)
                ->whereHas('chart_of_account_assigned', function($query) use ($companySystemIDs) {
                    $query->whereIn('companySystemID', $companySystemIDs)
                        ->where('isAssigned', -1);
                })->first();

            return ($res) ? $res->chartOfAccountSystemID : null;
    }

    public static function getGlCodeByScenario($companySystemID, $documentSystemID, $slug)
    {
        $systemGlScenario = SystemGlCodeScenario::where('slug', $slug)->first();
        if ($systemGlScenario) {
            $id = $systemGlScenario->id;
        } else {
            $id = null;
        }

        $res = SystemGlCodeScenarioDetail::with(['chart_of_account'])
                                        ->whereHas('chart_of_account')
                                        ->where('companySystemID', $companySystemID)
                                        ->where('systemGlScenarioID', $id)
                                        ->first();

        return (($res) ? $res->chart_of_account->AccountCode : null);
    }

    public static function getGlDescriptionByScenario($companySystemID, $documentSystemID, $slug)
    {
        $systemGlScenario = SystemGlCodeScenario::where('slug', $slug)->first();
        if ($systemGlScenario) {
            $id = $systemGlScenario->id;
        } else {
            $id = null;
        }

        $res = SystemGlCodeScenarioDetail::with(['chart_of_account'])
                                        ->whereHas('chart_of_account')
                                        ->where('companySystemID', $companySystemID)
                                        ->where('systemGlScenarioID', $id)
                                        ->first();

        return (($res) ? $res->chart_of_account->AccountDescription : null);
    }
}
