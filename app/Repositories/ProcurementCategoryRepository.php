<?php

namespace App\Repositories;

use App\Models\TenderProcurementCategory;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ProcurementCategoryRepository
 * @package App\Repositories
 */
class ProcurementCategoryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'description_in_secondary',
        'is_active',
        'created_pc',
        'created_by',
        'created_at',
        'updated_pc',
        'updated_by',
        'updated_at',
        'deleted_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderProcurementCategory::class;
    }
}
