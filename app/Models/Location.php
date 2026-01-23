<?php
/**
 * =============================================
 * -- File Name : Location.php
 * -- Project Name : ERP
 * -- Module Name : Location
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Location
 * @package App\Models
 * @version March 26, 2018, 10:54 am UTC
 *
 * @property string locationName
 */
class Location extends Model
{
    // use SoftDeletes;

    public $table = 'erp_location';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    protected $primaryKey = 'locationID';


    public $fillable = [
        'locationName'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'locationID' => 'integer',
        'locationName' => 'string',
        'deleted_at' => 'datetime',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
