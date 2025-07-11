<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="WarehouseRights",
 *      required={""},
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPcID",
 *          description="modifiedPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseSystemCode",
 *          description="wareHouseSystemCode",
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
 *          property="employeeSystemID",
 *          description="employeeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyrightsID",
 *          description="companyrightsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="warehouseRightsID",
 *          description="warehouseRightsID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class WarehouseRights extends Model
{

    public $table = 'warehouserights';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey  = 'warehouseRightsID';

    public $fillable = [
        'timestamp',
        'modifiedDateTime',
        'modifiedPcID',
        'modifiedUserSystemID',
        'createdDateTime',
        'createdPcID',
        'createdUserSystemID',
        'wareHouseSystemCode',
        'companySystemID',
        'employeeSystemID',
        'companyrightsID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'timestamp' => 'datetime',
        'modifiedDateTime' => 'datetime',
        'modifiedPcID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'createdDateTime' => 'datetime',
        'createdPcID' => 'string',
        'createdUserSystemID' => 'integer',
        'wareHouseSystemCode' => 'integer',
        'companySystemID' => 'integer',
        'employeeSystemID' => 'integer',
        'companyrightsID' => 'integer',
        'warehouseRightsID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
    ];

    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

    public function warehouse(){
        return $this->belongsTo('App\Models\WarehouseMaster','wareHouseSystemCode','wareHouseSystemCode');
    }

    public function employee(){
        return $this->belongsTo('App\Models\Employee','employeeSystemID','employeeSystemID');
    }

    public static function getAssignedWarehouses($user, $subCompanies)
    {
        return WarehouseRights::where('employeeSystemID', $user)
            ->whereIn('companySystemID', $subCompanies)
            ->pluck('wareHouseSystemCode')
            ->toArray();
    }
}
