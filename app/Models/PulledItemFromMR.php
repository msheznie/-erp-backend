<?php
/**
 * =============================================
 * -- File Name : PulledItemFromMR.php
 * -- Project Name : ERP
 * -- Module Name :  PulledItemFromMR
 * -- Author : saravanan
 * -- Create date : 28- Oct 2021
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * Class PulledItemFromMR
 * @property integer itemCodeSystem
 * @property integer RequestID
 * @property string itemPrimaryCode
 * @property integer mr_qnty
 * @property integer pr_qnty
 * @property integer createdUserSystemID
 * @property integer modifiedUserSystemID
 * @property integer companySystemID
 * @property string modifiedDate
 * @property string createdDate
 */
class PulledItemFromMR extends Model
{
    public $table = 'erp_pulled_from_mr';
    
    const CREATED_AT = 'createdDate';
    protected $primaryKey  = 'id';




    public $fillable = [
        'itemCodeSystem',
        'purcahseRequestID',
        'RequestID',
        'RequestDetailsID',
        'itemPrimaryCode',
        'mr_qnty',
        'pr_qnty',
        'createdUserSystemID',
        'modifiedUserSystemID',
        'companySystemID',
        'updated_at',
        'createdDate'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'purcahseRequestID' => 'integer',
        'itemPrimaryCode' => "string",
        'itemCodeSystem' => 'integer',
        'mr_qnty' => 'integer',
        'pr_qnty' => 'integer',
        'RequestID' => 'integer',
        'RequestDetailsID' => 'integer',
        'createdUserSystemID' => 'integer',
        'modifiedUserSystemID' => 'integer',
        'companySystemID' => 'integer',
        'updated_at' => 'string',
        'createdDate' => 'string'
    ];

    public static $rules = [
        'purcahseRequestID' => 'required',
        'itemPrimaryCode' => 'required',
        'itemCodeSystem' => 'required',
        'pr_qnty' => 'required',
        'RequestID' => 'required'
    ];

    public function mrRequests(){
        return $this->hasMany('App\Models\MaterielRequest','RequestID','RequestID');
    }

}
