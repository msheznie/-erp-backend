<?php

namespace App\Repositories;

use App\Models\CustomerMasterRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CustomerMasterRefferedBackRepository
 * @package App\Repositories
 * @version December 18, 2018, 3:31 am UTC
 *
 * @method CustomerMasterRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method CustomerMasterRefferedBack find($id, $columns = ['*'])
 * @method CustomerMasterRefferedBack first($columns = ['*'])
*/
class CustomerMasterRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'customerCodeSystem',
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
        'companyLinkedToSystemID',
        'companyLinkedTo',
        'isCustomerActive',
        'isAllowedQHSE',
        'vatEligible',
        'vatNumber',
        'vatPercentage',
        'isSupplierForiegn',
        'approvedYN',
        'approvedEmpSystemID',
        'approvedEmpID',
        'approvedDate',
        'approvedComment',
        'confirmedYN',
        'confirmedEmpSystemID',
        'confirmedEmpID',
        'confirmedEmpName',
        'confirmedDate',
        'RollLevForApp_curr',
        'refferedBackYN',
        'timesReferred',
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
        return CustomerMasterRefferedBack::class;
    }
}
