<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CompanyJobs",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="system_job_id",
 *          description="system_job_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          description="company_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="is_active",
 *          description="is_active",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class CompanyJobs extends Model
{

    public $table = 'company_jobs';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'system_job_id',
        'company_id',
        'is_active'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'system_job_id' => 'integer',
        'company_id' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'system_job_id' => 'required',
        'company_id' => 'required',
        'is_active' => 'required'
    ];

    public function master(){
        return $this->belongsTo(SystemJobs::class, 'system_job_id', 'id');
    }

    public static function getActiveCompanies($signature){
        return CompanyJobs::select('company_id', 'system_job_id')        
            ->where('is_active', 1)
            ->whereHas('master', function($q) use ($signature){
                $q->where('job_signature', $signature)
                ->where('is_active', 1);
            })
            ->get();
    }
    
}
