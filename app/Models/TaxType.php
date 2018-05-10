<?php
/**
 * =============================================
 * -- File Name : TaxType.php
 * -- Project Name : ERP
 * -- Module Name :  Tax Setup
 * -- Author : Mubashir
 * -- Create date : 23 - April 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TaxType
 * @package App\Models
 * @version April 23, 2018, 8:04 am UTC
 *
 * @property string typeDescription
 */
class TaxType extends Model
{
    //use SoftDeletes;

    public $table = 'erp_taxtype_new';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'typeDescription'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'taxTypeID' => 'integer',
        'typeDescription' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
