<?php
/**
=============================================
-- File Name : StockAdjustmentReason.php
-- Project Name : ERP
-- Module Name :  System Admin
-- Author : Saravanan
-- Create date : 11 - March 2022
-- Description : This file is used to interact with database table and it contains relationships to the tables.
-- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Unit
 * @package App\Models
 * @version March 22, 2018, 6:41 am UTC
 *
 * @property string reason
 * @property string is_active
 */
class StockAdjustmentReason extends Model
{
    //use SoftDeletes;

    public $table = 'stockadjustment_reasons';
    
    protected $primaryKey  = 'id';


    public $fillable = [
        'reason',
        'is_active	',
        'timeStamp'
    ];


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

 
}
