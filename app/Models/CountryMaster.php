<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CountryMaster
 * @package App\Models
 * @version February 27, 2018, 11:30 am UTC
 *
 * @property string countryCode
 * @property string countryName
 * @property string countryName_O
 * @property string nationality
 * @property integer isLocal
 * @property string countryFlag
 */
class CountryMaster extends Model
{
    //use SoftDeletes;

    public $table = 'countrymaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'countryCode',
        'countryName',
        'countryName_O',
        'nationality',
        'isLocal',
        'countryFlag'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'countryID' => 'integer',
        'countryCode' => 'string',
        'countryName' => 'string',
        'countryName_O' => 'string',
        'nationality' => 'string',
        'isLocal' => 'integer',
        'countryFlag' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
