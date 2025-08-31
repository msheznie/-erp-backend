<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentBudgetPlanningDetail extends Model
{
    protected $table = 'department_budget_planning_details';

    protected $appends = ['internal_status_text'];

    protected $fillable = [
        'department_planning_id',
        'budget_template_id',
        'department_segment_id',
        'budget_template_gl_id',
        'request_amount',
        'responsible_person',
        'responsible_person_type',
        'time_for_submission',
        'previous_year_budget',
        'current_year_budget',
        'difference_last_current_year',
        'amount_given_by_finance',
        'amount_given_by_hod',
        'internal_status',
        'difference_current_request'
    ];

    protected $dates = [
        'time_for_submission',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'request_amount' => 'double',
        'previous_year_budget' => 'double',
        'current_year_budget' => 'double',
        'difference_last_current_year' => 'double',
        'amount_given_by_finance' => 'double',
        'amount_given_by_hod' => 'double',
        'difference_current_request' => 'double',
        'responsible_person_type' => 'integer',
        'internal_status' => 'integer'
    ];

    /**
     * Get the department budget planning that owns the detail.
     */
    public function departmentBudgetPlanning()
    {
        return $this->belongsTo(DepartmentBudgetPlanning::class, 'department_planning_id');
    }

    /**
     * Get the budget template.
     */
    public function budgetTemplate()
    {
        return $this->belongsTo(\App\Models\BudgetTemplate::class, 'budget_template_id');
    }

    /**
     * Get the department segment.
     */
    public function departmentSegment()
    {
        return $this->belongsTo(\App\Models\CompanyDepartmentSegment::class, 'department_segment_id', 'departmentSegmentSystemID');
    }

    /**
     * Get the budget template GL.
     */
    public function budgetTemplateGl()
    {
        return $this->belongsTo(\App\Models\DepBudgetTemplateGl::class, 'budget_template_gl_id', 'depBudgetTemplateGlID');
    }

    /**
     * Get the responsible person (employee).
     */
    public function responsiblePerson()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'responsible_person', 'employeeSystemID');
    }

    /**
     * Scope a query to only include details for a specific department planning.
     */
    public function scopeForDepartmentPlanning($query, $departmentPlanningId)
    {
        return $query->where('department_planning_id', $departmentPlanningId);
    }

    /**
     * Scope a query to only include details with specific status.
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('internal_status', $status);
    }

    /**
     * Get the comments for this budget detail.
     */
    public function comments()
    {
        return $this->hasMany(\App\Models\BudgetTemplateComment::class, 'budget_detail_id', 'id');
    }

    /**
     * Scope a query to only include details for a specific budget template.
     */
    public function scopeForBudgetTemplate($query, $budgetTemplateId)
    {
        return $query->where('budget_template_id', $budgetTemplateId);
    }

    /**
     * Get responsible person type label.
     */
    public function getResponsiblePersonTypeTextAttribute()
    {
        switch ($this->responsible_person_type) {
            case 1:
                return 'HOD';
            case 2:
                return 'Delegate';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get internal status label.
     */
    public function getInternalStatusTextAttribute()
    {
        switch ($this->internal_status) {
            case 1:
                return 'Pending';
            case 2:
                return 'Approved';
            case 3:
                return 'Rejected';
            case 4:
                return 'Under Review';
            default:
                return 'Unknown';
        }
    }

    /**
     * Calculate and update differences automatically.
     */
    public function calculateDifferences()
    {
        // Calculate difference between last year and current year
        $this->difference_last_current_year = $this->current_year_budget - $this->previous_year_budget;
        
        // Calculate difference between current year and request amount
        $this->difference_current_request = $this->request_amount - $this->current_year_budget;
        
        return $this;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically calculate differences when creating or updating
        static::saving(function ($model) {
            $model->calculateDifferences();
        });
    }

    public static function getBudgetPlaningCompany($budgetPlaningDetID)
    {
        return self::with('departmentBudgetPlanning.masterBudgetPlannings.company')
            ->find($budgetPlaningDetID);
    }
}

