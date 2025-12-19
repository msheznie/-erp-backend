<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="LeaveGroup",
 *      required={""},
 *      @SWG\Property(
 *          property="leaveGroupID",
 *          description="leaveGroupID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isMonthly",
 *          description="isMonthly",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isDefault",
 *          description="isDefault",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvalLevels",
 *          description="approvalLevels",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdUserName",
 *          description="createdUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class LeaveGroup extends Model
{

    public $table = 'srp_erp_leavegroup';

    protected $primaryKey = 'leaveGroupID';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'modifiedDateTime';




    public $fillable = [
        'description',
        'companyID',
        'isMonthly',
        'isDefault',
        'approvalLevels',
        'timestamp',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'leaveGroupID' => 'integer',
        'description' => 'string',
        'companyID' => 'integer',
        'isMonthly' => 'integer',
        'isDefault' => 'integer',
        'approvalLevels' => 'integer',
        'timestamp' => 'datetime',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdDateTime' => 'datetime',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedDateTime' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function details(){
        return $this->hasMany(LeaveGroupDetails::class, 'leaveGroupID', 'leaveGroupID');
    }

    public static function get_leave_group_details($company, $policy, $isDailyBasis=false){
        $data = LeaveGroup::selectRaw('leaveGroupID, description')
            ->where('companyID', $company);

        $data = $data->whereHas('details', function ($q) use ($policy, $isDailyBasis){
           $q->where('policyMasterID', $policy);

            if($isDailyBasis){
                $q->where('isDailyBasisAccrual', 1);
            }
        });

        $data = $data->with(['details' => function ($q) use ($policy, $isDailyBasis){
            $q->selectRaw('leaveGroupDetailID,leaveGroupID,leaveTypeID,policyMasterID,isDailyBasisAccrual,noOfDays')
                ->where('policyMasterID', $policy);

            if($isDailyBasis){
                $q->where('isDailyBasisAccrual', 1);
            }

        }]);

        return $data->get();
    }
}
