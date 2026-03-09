<?php

namespace App\Repositories;

use App\Models\CustomerMaster;
use App\Repositories\BaseRepository;

/**
 * Class CustomerMasterRepository
 * @package App\Repositories
 * @version March 19, 2018, 12:17 pm UTC
 *
 * @method CustomerMaster findWithoutFail($id, $columns = ['*'])
 * @method CustomerMaster find($id, $columns = ['*'])
 * @method CustomerMaster first($columns = ['*'])
*/
class CustomerMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'primaryCompanySystemID',
        'primaryCompanyID',
        'documentSystemID',
        'documentID',
        'lastSerialOrder',
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
        'customerLogo',
        'companyLinkedTo',
        'isCustomerActive',
        'isAllowedQHSE',
        'vatEligible',
        'vatNumber',
        'vatPercentage',
        'isSupplierForiegn',
        'approvedYN',
        'approvedDate',
        'approvedComment',
        'confirmedYN',
        'confirmedEmpSystemID',
        'confirmedEmpID',
        'confirmedEmpName',
        'confirmedDate',
        'createdUserGroup',
        'createdUserID',
        'createdDateTime',
        'createdPcID',
        'modifiedPc',
        'modifiedUser',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerMaster::class;
    }
}
