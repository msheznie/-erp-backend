<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BudgetTemplate
 * @package App\Models
 * @version January 6, 2024, 12:00 am UTC
 *
 * @property string description
 * @property string type
 * @property integer isActive
 * @property integer isDefault
 * @property integer companySystemID
 * @property integer createdUserSystemID
 * @property integer modifiedUserSystemID
 */
class BudgetTemplate extends Model
{
    public $table = 'budget_templates';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $primaryKey = 'budgetTemplateID';

    public $fillable = [
        'description',
        'type',
        'isActive',
        'isDefault',
        'companySystemID',
        'linkRequestAmount',
        'createdUserSystemID',
        'modifiedUserSystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'budgetTemplateID' => 'integer',
        'description' => 'string',
        'type' => 'integer',
        'isActive' => 'integer',
        'isDefault' => 'integer',
        'linkRequestAmount' => 'integer',
        'companySystemID' => 'integer',
        'createdUserSystemID' => 'integer',
        'modifiedUserSystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'description' => 'required|string|max:255',
        'type' => 'required|integer|in:1,2,3',
        'isActive' => 'boolean',
        'isDefault' => 'boolean',
        'companySystemID' => 'required|integer'
    ];

    public function getDescriptionAttribute($value)
    {
        return is_string($value) ? htmlspecialchars_decode($value) : $value;
    }
} 