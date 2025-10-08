<?php
/**
 * =============================================
 * -- File Name : AllocationMasterTranslation.php
 * -- Project Name : ERP
 * -- Module Name :  Allocation Master Translation
 * -- Author : System Generated
 * -- Create date : 13- September 2025
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AllocationMasterTranslation
 * @package App\Models
 * @version September 13, 2025, 3:00 pm UTC
 *
 * @property integer AutoID
 * @property string languageCode
 * @property string Desciption
 */
class AllocationMasterTranslation extends Model
{
    //use SoftDeletes;

    public $table = 'erp_allocation_master_translation';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'AutoID',
        'languageCode',
        'Desciption'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'AutoID' => 'integer',
        'languageCode' => 'string',
        'Desciption' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'AutoID' => 'required|integer',
        'languageCode' => 'required|string|max:10',
        'Desciption' => 'required|string|max:255'
    ];

    /**
     * Get the allocation master that owns the translation.
     */
    public function allocationMaster()
    {
        return $this->belongsTo(AllocationMaster::class, 'AutoID', 'AutoID');
    }
}
