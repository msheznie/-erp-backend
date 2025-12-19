<?php

namespace App\Repositories;

use App\Models\ProjectGlDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ProjectGlDetailRepository
 * @package App\Repositories
 * @version September 17, 2021, 10:20 am +04
 *
 * @method ProjectGlDetail findWithoutFail($id, $columns = ['*'])
 * @method ProjectGlDetail find($id, $columns = ['*'])
 * @method ProjectGlDetail first($columns = ['*'])
*/
class ProjectGlDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'projectID',
        'chartOfAccountSystemID',
        'companySystemID',
        'amount',
        'createdBy',
        'updatedBy'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ProjectGlDetail::class;
    }
}
