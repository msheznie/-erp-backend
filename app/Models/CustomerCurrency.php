<?php
/**
 * =============================================
 * -- File Name : CustomerCurrency.php
 * -- Project Name : ERP
 * -- Module Name : Customer Currency
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CustomerCurrency
 * @package App\Models
 * @version March 21, 2018, 4:46 am UTC
 *
 * @property integer customerCodeSystem
 * @property string customerCode
 * @property integer currencyID
 * @property integer isDefault
 * @property integer isAssigned
 * @property string createdBy
 * @property string|\Carbon\Carbon createdDateTime
 * @property string|\Carbon\Carbon timestamp
 */
class CustomerCurrency extends Model
{
    //use SoftDeletes;

    public $table = 'customercurrency';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'custCurrencyAutoID';




    public $fillable = [
        'customerCodeSystem',
        'customerCode',
        'currencyID',
        'isDefault',
        'isAssigned',
        'createdBy',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'custCurrencyAutoID' => 'integer',
        'customerCodeSystem' => 'integer',
        'customerCode' => 'string',
        'currencyID' => 'integer',
        'isDefault' => 'integer',
        'isAssigned' => 'integer',
        'createdBy' => 'string',
        'deleted_at' => 'datetime',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    public function currencyMaster()
    {
        return $this->belongsTo('App\Models\CurrencyMaster','currencyID','currencyID');
    }

    
}
