<?php

namespace App\Models;

use Eloquent as Model;

class SrmTenderBidEmployeeDetails extends Model
{
    public $table = 'srm_tender_bid_employee_details';

    const CREATED_AT = 'created_at';


    public $fillable = [
        'tender_id',
        'emp_id',
        'status',
        'remarks',
        'created_at',
        'commercial_eval_status',
        'commercial_eval_remarks',
        'tender_award_commite_mem_status',
        'tender_award_commite_mem_comment'

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'emp_id' => 'integer',
        'tender_id' => 'integer',
        'status' => 'integer',
        'created_at' => 'date',
    
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];

    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'employeeSystemID', 'emp_id');
    }

    public static function getTendetBidEmployeeDetails($tenderId)
    {
        return self::where('tender_id', $tenderId)
            ->where('status', true)
            ->get();
    }

    public static function getTenderBidEmployees($tenderID){
        return SrmTenderBidEmployeeDetails::select('id')
            ->where('tender_id', $tenderID)
            ->get();
    }
}
