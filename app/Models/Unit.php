<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Unit
 * @package App\Models
 * @version March 22, 2018, 6:41 am UTC
 *
 * @property string UnitShortCode
 * @property string UnitDes
 * @property string createdUserGroup
 * @property string createdPcID
 * @property string createdUserID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon createdDateTime
 * @property string|\Carbon\Carbon timeStamp
 */
class Unit extends Model
{
    //use SoftDeletes;

    public $table = 'units';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $dates = ['deleted_at'];
    protected $primaryKey  = 'UnitID';


    public $fillable = [
        'UnitShortCode',
        'UnitDes',
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
        'UnitID' => 'integer',
        'UnitShortCode' => 'string',
        'UnitDes' => 'string',
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

    public function unitConversion()
    {
        return $this->hasMany('App\Models\UnitConversion', 'masterUnitID', 'UnitID');
    }
    
}
