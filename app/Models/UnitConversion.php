<?php
/**
=============================================
-- File Name : Unit.php
-- Project Name : ERP
-- Module Name :  System Admin
-- Author : Pasan Madhuranga
-- Create date : 22 - March 2018
-- Description : This file is used to interact with database table and it contains relationships to the tables.
-- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UnitConversion
 * @package App\Models
 * @version March 22, 2018, 10:07 am UTC
 *
 * @property integer masterUnitID
 * @property integer subUnitID
 * @property float conversion
 * @property string|\Carbon\Carbon timestamp
 */
class UnitConversion extends Model
{
    //use SoftDeletes;

    public $table = 'erp_unitsconversion';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $dates = ['deleted_at'];
    protected $primaryKey  = 'unitsConversionAutoID';


    public $fillable = [
        'masterUnitID',
        'subUnitID',
        'conversion',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'unitsConversionAutoID' => 'integer',
        'masterUnitID' => 'integer',
        'subUnitID' => 'integer',
        'conversion' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'masterUnitID', 'UnitID');
    }

    public function sub_unit()
    {
        return $this->belongsTo('App\Models\Unit','subUnitID','UnitID');
    }
}
