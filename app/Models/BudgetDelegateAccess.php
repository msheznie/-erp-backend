<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BudgetDelegateAccess
 * @package App\Models
 * @version January 3, 2024, 12:00 am UTC
 *
 * @property integer $id
 * @property string $description
 * @property string $slug
 * @property string $details
 * @property boolean $is_active
 */
class BudgetDelegateAccess extends Model
{
    public $table = 'budget_delegate_access';
    
    public $primaryKey = 'id';

    public $fillable = [
        'description',
        'slug',
        'details',
        'is_active'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'description' => 'string',
        'slug' => 'string',
        'details' => 'string',
        'is_active' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'description' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:budget_delegate_access,slug',
        'details' => 'nullable|string',
        'is_active' => 'boolean'
    ];

    /**
     * Get active access types
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveAccessTypes()
    {
        return self::where('is_active', true)->orderBy('description')->get();
    }

    /**
     * Get access type by slug
     *
     * @param string $slug
     * @return BudgetDelegateAccess|null
     */
    public static function getBySlug($slug)
    {
        return self::where('slug', $slug)->where('is_active', true)->first();
    }
} 