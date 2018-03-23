<?php

namespace App\Repositories;

use App\Models\CustomerAssigned;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomerAssignedRepository
 * @package App\Repositories
 * @version March 20, 2018, 11:55 am UTC
 *
 * @method CustomerAssigned findWithoutFail($id, $columns = ['*'])
 * @method CustomerAssigned find($id, $columns = ['*'])
 * @method CustomerAssigned first($columns = ['*'])
*/
class CustomerAssignedRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'customerCodeSystem',
        'CutomerCode',
        'customerShortCode',
        'custGLAccountSystemID',
        'custGLaccount',
        'CustomerName',
        'ReportTitle',
        'customerAddress1',
        'customerAddress2',
        'customerCity',
        'customerCountry',
        'CustWebsite',
        'creditLimit',
        'creditDays',
        'isRelatedPartyYN',
        'isActive',
        'isAssigned',
        'vatEligible',
        'vatNumber',
        'vatPercentage',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerAssigned::class;
    }
}
