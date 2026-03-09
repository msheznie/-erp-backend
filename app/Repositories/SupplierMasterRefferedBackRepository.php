<?php

namespace App\Repositories;

use App\Models\SupplierMasterRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class SupplierMasterRefferedBackRepository
 * @package App\Repositories
 * @version December 17, 2018, 7:10 am UTC
 *
 * @method SupplierMasterRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method SupplierMasterRefferedBack find($id, $columns = ['*'])
 * @method SupplierMasterRefferedBack first($columns = ['*'])
*/
class SupplierMasterRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supplierCodeSystem',
        'uniqueTextcode',
        'primaryCompanySystemID',
        'primaryCompanyID',
        'documentSystemID',
        'documentID',
        'primarySupplierCode',
        'secondarySupplierCode',
        'supplierName',
        'liabilityAccountSysemID',
        'liabilityAccount',
        'UnbilledGRVAccountSystemID',
        'UnbilledGRVAccount',
        'address',
        'countryID',
        'supplierCountryID',
        'telephone',
        'fax',
        'supEmail',
        'webAddress',
        'currency',
        'nameOnPaymentCheque',
        'creditLimit',
        'creditPeriod',
        'supCategoryMasterID',
        'supCategorySubID',
        'registrationNumber',
        'registrationExprity',
        'approvedYN',
        'approvedEmpSystemID',
        'approvedby',
        'approvedDate',
        'approvedComment',
        'isActive',
        'isSupplierForiegn',
        'supplierConfirmedYN',
        'supplierConfirmedEmpID',
        'supplierConfirmedEmpSystemID',
        'supplierConfirmedEmpName',
        'supplierConfirmedDate',
        'isCriticalYN',
        'companyLinkedToSystemID',
        'companyLinkedTo',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'isDirect',
        'supplierImportanceID',
        'supplierNatureID',
        'supplierTypeID',
        'WHTApplicable',
        'vatEligible',
        'vatNumber',
        'vatPercentage',
        'supCategoryICVMasterID',
        'supCategorySubICVID',
        'isLCCYN',
        'RollLevForApp_curr',
        'refferedBackYN',
        'timesReferred',
        'timestamp',
        'createdUserSystemID',
        'modifiedUserSystemID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierMasterRefferedBack::class;
    }
}
