<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Revision
 * @package App\Models
 * @version January 15, 2025
 *
 * @property string revisionId
 * @property integer budgetPlanningId
 * @property string submittedBy
 * @property string submittedDate
 * @property string reviewComments
 * @property string revisionType
 * @property string reopenEditableSection
 * @property string selectedGlSections
 * @property integer revisionStatus
 * @property string sentDateTime
 * @property string completionComments
 * @property string completedDateTime
 * @property integer created_by
 * @property integer modified_by
 * @property string created_at
 * @property string modified_at
 * @property string deleted_at
 */
class Revision extends Model
{
    public $table = 'revisions';

    protected $primaryKey = 'id';

    protected $dates = ['deleted_at', 'submittedDate', 'sentDateTime', 'completedDateTime'];

    public $fillable = [
        'revisionId',
        'budgetPlanningId',
        'submittedBy',
        'submittedDate',
        'reviewComments',
        'revisionType',
        'reopenEditableSection',
        'selectedGlSections',
        'revisionStatus',
        'sentDateTime',
        'completionComments',
        'completedDateTime',
        'created_by',
        'modified_by',
        'created_at',
        'modified_at'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'revisionId' => 'string',
        'budgetPlanningId' => 'integer',
        'submittedBy' => 'string',
        'submittedDate' => 'date',
        'reviewComments' => 'string',
        'revisionType' => 'string',
        'reopenEditableSection' => 'string',
        'selectedGlSections' => 'string',
        'revisionStatus' => 'integer',
        'sentDateTime' => 'datetime',
        'completionComments' => 'string',
        'completedDateTime' => 'datetime',
        'created_by' => 'integer',
        'modified_by' => 'integer',
        'created_at' => 'datetime',
        'modified_at' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'revisionId' => 'required|string|max:255|unique:revisions',
        'budgetPlanningId' => 'required|integer',
        'submittedBy' => 'required|string|max:255',
        'submittedDate' => 'required|date',
        'reviewComments' => 'required|string',
        'revisionType' => 'required|string|max:255',
        'reopenEditableSection' => 'required|string|in:full_section,gl_section',
        'selectedGlSections' => 'nullable|string',
        'revisionStatus' => 'required|integer',
        'sentDateTime' => 'required|date',
        'completionComments' => 'string',
        'completedDateTime' => 'date'
    ];

    /**
     * Get the budget planning that owns the revision.
     */
    public function budgetPlanning()
    {
        return $this->belongsTo('App\Models\DepartmentBudgetPlanning', 'budgetPlanningId', 'id');
    }

    /**
     * Get the employee who created the revision.
     */
    public function createdBy()
    {
        return $this->belongsTo('App\Models\Employee', 'created_by', 'employeeSystemID');
    }

    /**
     * Get the employee who last modified the revision.
     */
    public function modifiedBy()
    {
        return $this->belongsTo('App\Models\Employee', 'modified_by', 'employeeSystemID');
    }

    /**
     * Get the attachments for the revision.
     */
    public function attachments()
    {
        return $this->hasMany('App\Models\RevisionAttachment', 'revisionId', 'id');
    }

    /**
     * Scope a query to only include active revisions.
     */
    public function scopeActive($query)
    {
        return $query->where('revisionStatus', 1);
    }

    /**
     * Scope a query to only include completed revisions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('revisionStatus', 2);
    }

    /**
     * Scope a query to only include cancelled revisions.
     */
    public function scopeCancelled($query)
    {
        return $query->where('revisionStatus', 3);
    }

    /**
     * Get revision status text
     */
    public function getRevisionStatusTextAttribute()
    {
        switch ($this->revisionStatus) {
            case 1:
                return 'Active';
            case 2:
                return 'Completed';
            case 3:
                return 'Cancelled';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get revision type text
     */
    public function getRevisionTypeTextAttribute()
    {
        $types = [
            'amount_adjustment' => 'Amount Adjustment',
            'missing_justification' => 'Missing Justification',
            'incorrect_gl_allocation' => 'Incorrect GL Allocation',
            'line_item_clarification' => 'Line-Item Clarification',
            'exceeds_threshold' => 'Exceeds Threshold',
            'template_mismatch' => 'Template Mismatch',
            'department_scope_error' => 'Department Scope Error',
            'other' => 'Other'
        ];

        return $types[$this->revisionType] ?? $this->revisionType;
    }
}
