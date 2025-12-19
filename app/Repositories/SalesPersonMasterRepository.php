<?php

namespace App\Repositories;

use App\Models\SalesPersonMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SalesPersonMasterRepository
 * @package App\Repositories
 * @version January 20, 2019, 3:52 pm +04
 *
 * @method SalesPersonMaster findWithoutFail($id, $columns = ['*'])
 * @method SalesPersonMaster find($id, $columns = ['*'])
 * @method SalesPersonMaster first($columns = ['*'])
*/
class SalesPersonMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'empSystemID',
        'SalesPersonCode',
        'SalesPersonName',
        'salesPersonImage',
        'wareHouseAutoID',
        'wareHouseCode',
        'wareHouseDescription',
        'wareHouseLocation',
        'SalesPersonEmail',
        'SecondaryCode',
        'contactNumber',
        'salesPersonTargetType',
        'salesPersonTarget',
        'SalesPersonAddress',
        'receivableAutoID',
        'receivableSystemGLCode',
        'receivableGLAccount',
        'receivableDescription',
        'receivableType',
        'expenseAutoID',
        'expenseSystemGLCode',
        'expenseGLAccount',
        'expenseDescription',
        'expenseType',
        'salesPersonCurrencyID',
        'salesPersonCurrency',
        'salesPersonCurrencyDecimalPlaces',
        'segmentID',
        'segmentCode',
        'isActive',
        'companySystemID',
        'companyID',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'TIMESTAMP'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SalesPersonMaster::class;
    }
}
