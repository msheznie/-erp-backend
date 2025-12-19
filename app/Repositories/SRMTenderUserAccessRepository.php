<?php

namespace App\Repositories;

use App\Models\SRMTenderUserAccess;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SRMTenderUserAccessRepository
 * @package App\Repositories
 * @version May 15, 2024, 10:59 am +04
 *
 * @method SRMTenderUserAccess findWithoutFail($id, $columns = ['*'])
 * @method SRMTenderUserAccess find($id, $columns = ['*'])
 * @method SRMTenderUserAccess first($columns = ['*'])
*/
class SRMTenderUserAccessRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'user_id',
        'module_id',
        'company_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SRMTenderUserAccess::class;
    }
}
