<?php
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ControlAccount
 * @package App\Models
 * @version March 16, 2018, 4:52 am UTC
 *
 * @property string controlAccountCode
 * @property string description
 * @property string itemLedgerShymbol
 * @property string|\Carbon\Carbon timeStamp
 */


class ControlAccount extends Model
{
    //use SoftDeletes;

    public $table = 'controlaccounts';

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'supplierCodeSystem';

    protected $dates = ['timeStamp'];


    public $fillable = [
        'controlAccountCode',
        'description',
        'itemLedgerShymbol',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'controlAccountsSystemID' => 'integer',
        'controlAccountsID' => 'string',
        'controlAccountCode' => 'string',
        'description' => 'string',
        'itemLedgerShymbol' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];


}
