<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SecondaryCompany",
 *      required={""},
 *      @SWG\Property(
 *          property="secondaryCompanyID",
 *          description="secondaryCompanyID",
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
 *          property="logo",
 *          description="logo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      )
 * )
 */
class SecondaryCompany extends Model
{

    public $table = 'secondarycompany';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $primaryKey = 'secondaryCompanyID';


    public $fillable = [
        'companySystemID',
        'logo',
        'name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'secondaryCompanyID' => 'integer',
        'companySystemID' => 'integer',
        'logo' => 'string',
        'name' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'secondaryCompanyID' => 'required'
    ];

    
}
