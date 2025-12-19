<?php

namespace App\Repositories;

use App\Models\BudgetDelegateAccess;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BudgetDelegateAccessRepository
 * @package App\Repositories
 * @version January 3, 2024, 12:00 am UTC
*/

class BudgetDelegateAccessRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'slug',
        'details',
        'is_active'
    ];

    /**
     * Return searchable fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BudgetDelegateAccess::class;
    }

    /**
     * Get all active access types
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveAccessTypes()
    {
        return $this->model()::where('is_active', true)
                              ->orderBy('description')
                              ->get();
    }

    /**
     * Get access type by slug
     *
     * @param string $slug
     * @return BudgetDelegateAccess|null
     */
    public function getBySlug($slug)
    {
        return $this->model()::where('slug', $slug)
                              ->where('is_active', true)
                              ->first();
    }

    /**
     * Get access types by slugs
     *
     * @param array $slugs
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBySlugs($slugs)
    {
        return $this->model()::whereIn('slug', $slugs)
                              ->where('is_active', true)
                              ->orderBy('description')
                              ->get();
    }
} 