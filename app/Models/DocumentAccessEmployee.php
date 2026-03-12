<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentAccessEmployee extends Model
{
    protected $table = 'document_access_role_employees';
    
    protected $primaryKey = 'id';
    
    public $fillable = [
        'document_access_role_id',
        'employee_id',
        'document_access_type',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'document_access_role_id' => 'integer',
        'employee_id' => 'integer',
        'document_access_type' => 'integer',
    ];

    /**
     * Get the document access role that owns this employee assignment.
     */
    public function documentAccessRole()
    {
        return $this->belongsTo('App\Models\DocumentAccessRole', 'document_access_role_id', 'id');
    }

    /**
     * Get the employee assigned to this access role.
     */
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id', 'employeeSystemID');
    }
}
