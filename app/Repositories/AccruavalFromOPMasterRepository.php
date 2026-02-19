<?php

namespace App\Repositories;

use App\Models\AccruavalFromOPMaster;
use App\Repositories\BaseRepository;

/**
 * Class AccruavalFromOPMasterRepository
 * @package App\Repositories
 * @version October 5, 2018, 5:11 am UTC
 *
 * @method AccruavalFromOPMaster findWithoutFail($id, $columns = ['*'])
 * @method AccruavalFromOPMaster find($id, $columns = ['*'])
 * @method AccruavalFromOPMaster first($columns = ['*'])
*/
class AccruavalFromOPMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'accruvalNarration',
        'accrualDateAsOF',
        'serialNo',
        'companyID',
        'accmonth',
        'accYear',
        'accConfirmedYN',
        'accConfirmedBy',
        'accConfirmedDate',
        'jvMasterAutoID',
        'accJVpostedYN',
        'jvPostedBy',
        'jvPostedDate',
        'createdby',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AccruavalFromOPMaster::class;
    }
}
