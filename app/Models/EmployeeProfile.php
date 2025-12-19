<?php

namespace App\Models;

use App\helper\Helper;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="EmployeeProfile",
 *      required={""},
 *      @SWG\Property(
 *          property="empPorfileID",
 *          description="empPorfileID",
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
 *          property="empID",
 *          description="empID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="profileImage",
 *          description="profileImage",
 *          type="string"
 *      )
 * )
 */
class EmployeeProfile extends Model
{

    public $table = 'web_employee_profile';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'empPorfileID';
    protected $appends = ['profile_image_url'];

    public $fillable = [
        'employeeSystemID',
        'empID',
        'profileImage',
        'modifiedDate',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'empPorfileID' => 'integer',
        'employeeSystemID' => 'integer',
        'empID' => 'string',
        'profileImage' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function getProfileImageUrlAttribute(){
        return asset($this->profileImage);
    }

    public function getProfileImageAttribute($value){
        return Helper::getFileUrlFromS3($value);
    }
}
