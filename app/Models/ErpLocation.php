<?php
/**
=============================================
-- File Name : ErpLocation.php
-- Project Name : ERP
-- Module Name :  System Admin
-- Author : Pasan Madhuranga
-- Create date : 21 - March 2018
-- Description : This file is used to interact with database table and it contains relationships to the tables.
-- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ErpLocation
 * @package App\Models
 * @version March 15, 2018, 9:41 am UTC
 *
 * @property string locationName
 */
class ErpLocation extends Model
{
    //use SoftDeletes;

    public $table = 'erp_location';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'locationName',
        'is_deleted',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'locationID' => 'integer',
        'locationName' => 'string',
        'is_deleted' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
