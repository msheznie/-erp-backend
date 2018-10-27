<?php
/**
 * =============================================
 * -- File Name : SegmentMaster.php
 * -- Project Name : ERP
 * -- Module Name :  Segment Master
 * -- Author : Nazir
 * -- Create date : 18 - April 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SegmentMaster
 * @package App\Models
 * @version March 19, 2018, 10:57 am UTC
 *
 * @property string ServiceLineCode
 * @property string serviceLineMasterCode
 * @property string companyID
 * @property string ServiceLineDes
 * @property integer locationID
 * @property integer isActive
 * @property integer isPublic
 * @property integer isServiceLine
 * @property integer isDepartment
 * @property string createdUserGroup
 * @property string createdPcID
 * @property string createdUserID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon createdDateTime
 * @property string|\Carbon\Carbon timeStamp
 */
class SegmentMaster extends Model
{
    //use SoftDeletes;

    public $table = 'serviceline';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'serviceLineSystemID';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'ServiceLineCode',
        'serviceLineMasterCode',
        'companySystemID',
        'companyID',
        'ServiceLineDes',
        'locationID',
        'isActive',
        'isMaster',
        'isPublic',
        'isServiceLine',
        'isDepartment',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'serviceLineSystemID' => 'integer',
        'ServiceLineCode' => 'string',
        'serviceLineMasterCode' => 'string',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'ServiceLineDes' => 'string',
        'locationID' => 'integer',
        'isActive' => 'integer',
        'isMaster' => 'integer',
        'isPublic' => 'integer',
        'isServiceLine' => 'integer',
        'isDepartment' => 'integer',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    /**
     * Scope a query to only include active serviceline.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeIsActive($query)
    {
        return $query->where('isActive',  1);
    }

    /**
     * Scope a query to only include active serviceline.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeIsPublic($query)
    {
        return $query->where('isPublic',  1);
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfCompany($query, $type)
    {
        return $query->whereIN('companySystemID',  $type);
    }

    /**
     * joining the company with serviceline table.
     */

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    
}
