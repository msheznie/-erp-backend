<?php

namespace App\Models;

use Eloquent as Model;
use App\Models\Company;
use App\helper\Helper;

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

     protected $appends = ['logo_url'];

    public $fillable = [
        'companySystemID',
        'logo',
        'logoPath',
        'name',
        'cutOffDate'
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
        'logoPath' => 'string',
        'cutOffDate' => 'datetime',
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

    public function getLogoUrlAttribute(){

        $companyData = Company::find($this->companySystemID);

        $awsPolicy = Helper::checkPolicy($companyData->masterCompanySystemIDReorting, 50);

        if ($awsPolicy) {
            return Helper::getFileUrlFromS3($this->logoPath);    
        } else {
            return $this->logoPath;
        }
    }
}
