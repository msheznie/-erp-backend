<?php
/**
 * =============================================
 * -- File Name : UserActivityLog.php
 * -- Project Name : ERP
 * -- Module Name : UserActivityLog
 * -- Author : Mohamed Rilwan
 * -- Create date : 05- Nov 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="UserActivityLog",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="user_id",
 *          description="user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="document_id",
 *          description="document_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="description",
 *          description="description",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="previous_value",
 *          description="previous_value",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="current_value",
 *          description="current_value",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="activity_at",
 *          description="activity_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="user_pc",
 *          description="user_pc",
 *          type="string"
 *      )
 * )
 */
class UserActivityLog extends Model
{

    public $table = 'user_activity_log';
    public $timestamps = false;

    public $fillable = [
        'user_id',
        'document_id',
        'company_id',
        'module_id',
        'description',
        'previous_value',
        'current_value',
        'column_name',
        'activity_at',
        'user_pc'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'document_id' => 'integer',
        'company_id' => 'integer',
        'module_id' => 'integer',
        'description' => 'string',
        'previous_value' => 'string',
        'current_value' => 'string',
        'column_name' => 'string',
        'activity_at' => 'datetime',
        'user_pc' => 'string'
    ];

    public function employee(){
        return $this->belongsTo('App\Models\Employee','user_id','employeeSystemID');
    }

    public function document(){
        return $this->belongsTo('App\Models\DocumentMaster','document_id','documentSystemID');
    }

}
