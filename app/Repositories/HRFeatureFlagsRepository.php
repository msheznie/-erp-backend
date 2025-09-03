<?php

namespace App\Repositories;

use App\Models\HRFeatureFlags;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class HRFeatureFlagsRepository
 * @package App\Repositories
 * @version September 2, 2025, 3:25 pm +04
 *
 * @method HRFeatureFlags findWithoutFail($id, $columns = ['*'])
 * @method HRFeatureFlags find($id, $columns = ['*'])
 * @method HRFeatureFlags first($columns = ['*'])
*/
class HRFeatureFlagsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'feature_name',
        'is_enabled',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HRFeatureFlags::class;
    }
}
