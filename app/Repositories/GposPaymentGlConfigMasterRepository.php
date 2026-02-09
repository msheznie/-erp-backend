<?php

namespace App\Repositories;

use App\Models\GposPaymentGlConfigMaster;
use App\Repositories\BaseRepository;

/**
 * Class GposPaymentGlConfigMasterRepository
 * @package App\Repositories
 * @version January 8, 2019, 8:45 am +04
 *
 * @method GposPaymentGlConfigMaster findWithoutFail($id, $columns = ['*'])
 * @method GposPaymentGlConfigMaster find($id, $columns = ['*'])
 * @method GposPaymentGlConfigMaster first($columns = ['*'])
*/
class GposPaymentGlConfigMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'glAccountType',
        'queryString',
        'image',
        'isActive',
        'sortOrder',
        'selectBoxName',
        'timesstamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return GposPaymentGlConfigMaster::class;
    }
}
