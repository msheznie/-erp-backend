<?php

namespace App\Repositories;

use App\Models\FixedAssetInsuranceDetail;
use App\Repositories\BaseRepository;

/**
 * Class FixedAssetInsuranceDetailRepository
 * @package App\Repositories
 * @version October 11, 2018, 4:57 am UTC
 *
 * @method FixedAssetInsuranceDetail findWithoutFail($id, $columns = ['*'])
 * @method FixedAssetInsuranceDetail find($id, $columns = ['*'])
 * @method FixedAssetInsuranceDetail first($columns = ['*'])
*/
class FixedAssetInsuranceDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyID',
        'faID',
        'insuredYN',
        'policy',
        'policyNumber',
        'dateOfInsurance',
        'dateOfExpiry',
        'insuredValue',
        'insurerName',
        'locationID',
        'buildingNumber',
        'openClosedArea',
        'containerNumber',
        'movingItem',
        'createdByUserID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FixedAssetInsuranceDetail::class;
    }
}
