<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ProcumentActivity",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="tender_id",
 *          description="tender_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="category_id",
 *          description="category_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          description="company_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="created_by",
 *          description="created_by",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class ProcumentActivity extends Model
{

    public $table = 'srm_procument_activity';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'created_at';




    public $fillable = [
        'tender_id',
        'category_id',
        'company_id',
        'created_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'tender_id' => 'integer',
        'category_id' => 'integer',
        'company_id' => 'integer',
        'created_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function tender_procurement_category()
    {
        return $this->hasOne('App\Models\TenderProcurementCategory', 'id', 'category_id');
    }

    public function tender_master()
    {
        return $this->hasOne('App\Models\TenderMaster', 'id', 'tender_id');
    }

    public static function getProcumentActivityForAmd($tender_id){
        return self::where('tender_id', $tender_id)->get();
    }

    public static function getTenderProcurements($tenderMasterId, $companySystemID){
        return self::with([
            'tender_procurement_category'
        ])
            ->where('tender_id', $tenderMasterId)
            ->where('company_id', $companySystemID)
            ->get();
    }


}
