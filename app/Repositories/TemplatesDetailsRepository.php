<?php

namespace App\Repositories;

use App\Models\TemplatesDetails;
use App\Repositories\BaseRepository;

/**
 * Class TemplatesDetailsRepository
 * @package App\Repositories
 * @version October 17, 2018, 6:26 am UTC
 *
 * @method TemplatesDetails findWithoutFail($id, $columns = ['*'])
 * @method TemplatesDetails find($id, $columns = ['*'])
 * @method TemplatesDetails first($columns = ['*'])
*/
class TemplatesDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'templatesMasterAutoID',
        'templateDetailDescription',
        'controlAccountID',
        'controlAccountSubID',
        'sortOrder',
        'cashflowid',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TemplatesDetails::class;
    }
}
