<?php
/**
 * =============================================
 * -- File Name : BankMemoSupplier.php
 * -- Project Name : ERP
 * -- Module Name : Bank Memo Supplier
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankMemoSupplier
 * @package App\Models
 * @version March 8, 2018, 4:56 am UTC
 *
 * @property string memoHeader
 * @property string memoDetail
 * @property integer supplierCodeSystem
 * @property integer supplierCurrencyID
 * @property string updatedByUserID
 * @property string updatedByUserName
 * @property string|\Carbon\Carbon updatedDate
 * @property string|\Carbon\Carbon timestamp
 */
class BankMemoSupplier extends Model
{
    //use SoftDeletes;

    public $table = 'erp_bankmemosupplier';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'updatedDate';
    protected $primaryKey  = 'bankMemoID';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'memoHeader',
        'memoDetail',
        'supplierCodeSystem',
        'supplierCurrencyID',
        'updatedByUserID',
        'updatedByUserName',
        'updatedDate',
        'bankMemoTypeID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'bankMemoID' => 'integer',
        'memoHeader' => 'string',
        'memoDetail' => 'string',
        'supplierCodeSystem' => 'integer',
        'supplierCurrencyID' => 'integer',
        'bankMemoTypeID' => 'integer',
        'updatedByUserID' => 'string',
        'updatedByUserName' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function scopeOrderBySort($q){

        return $q->join('erp_bankmemotypes', 'erp_bankmemosupplier.bankMemoTypeID', '=', 'erp_bankmemotypes.bankMemoTypeID')
            ->addSelect('erp_bankmemosupplier.*', 'erp_bankmemotypes.sortOrder')
            ->orderBy('erp_bankmemotypes.sortOrder', 'asc');
    }

    public function getMemoHeaderAttribute($value) 
    { 
        $languageCode = app()->getLocale() ?: 'en';
        if (strpos($value, 'custom.') === 0) 
        { 
            return trans($value, [], $languageCode); 
        } 

        $snakeCase = strtolower(str_replace(' ', '_', $value)); 
        $translationKey = 'custom.' . $snakeCase; 
        $translationResult = trans($translationKey, [], $languageCode); 

        if ($translationResult !== $translationKey) 
        { 
            return $translationResult; 
        } 
    
    
            return $value; 
        
    } 
        
    public function toArray() {

        $array = parent::toArray();
        $array['memoHeader'] = $this->memoHeader; return $array;
    }

}
