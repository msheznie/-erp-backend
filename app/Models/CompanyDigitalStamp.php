<?php

namespace App\Models;

use Eloquent as Model;
use App\helper\Helper;


/**
 * @SWG\Definition(
 *      definition="CompanyDigitalStamp",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="path",
 *          description="path",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_system_id",
 *          description="company_system_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="is_default",
 *          description="is_default",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="created_by",
 *          description="created_by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_by",
 *          description="updated_by",
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
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class CompanyDigitalStamp extends Model
{

    public $table = 'company_digital_stamp';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $appends = ['image_url'];





    public $fillable = [
        'path',
        'company_system_id',
        'is_default',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'path' => 'string',
        'company_system_id' => 'integer',
        'is_default' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

   
    public function getImageUrlAttribute(){

        $awsPolicy = Helper::checkPolicy($this->company_system_id, 50);

        if ($awsPolicy) {
            return Helper::getFileUrlFromS3($this->path);    
        } else {
            return $this->path;
        }
    }



    
}
