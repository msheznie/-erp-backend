<?php
/**
 * =============================================
 * -- File Name : UsersLogHistory.php
 * -- Project Name : ERP
 * -- Module Name : Users Log History
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UsersLogHistory
 * @package App\Models
 * @version May 1, 2018, 9:29 am UTC
 *
 * @property integer employee_id
 * @property string empID
 * @property string loginPCId
 */
class UploadBudgets extends Model
{

    public $table = 'upload_budgets';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $primaryKey = 'id';


    public $fillable = [
        'uploadComment',
        'uploadedDate',
        'uploadedBy',
        'uploadStatus',
        'companySystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function uploaded_by()
    {
        return $this->belongsTo('App\Models\Employee', 'uploadedBy', 'empID');
    }

    public function log()
    {
        return $this->hasMany('App\Models\logUploadBudget', 'bugdet_upload_id', 'id');
    }

}
