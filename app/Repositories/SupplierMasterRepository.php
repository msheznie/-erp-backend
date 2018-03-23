<?php

namespace App\Repositories;

use App\Models\SupplierMaster;
use InfyOm\Generator\Common\BaseRepository;


/**
 * Class SupplierMasterRepository
 * @package App\Repositories
 * @version February 21, 2018, 11:27 am UTC
 *
 * @method SupplierMaster findWithoutFail($id, $columns = ['*'])
 * @method SupplierMaster find($id, $columns = ['*'])
 * @method SupplierMaster first($columns = ['*'])
*/
class SupplierMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        //'uniqueTextcode',
        //'primaryCompanySystemID' => 'like',
        //'primaryCompanyID' => 'like',
        //'primarySupplierCode' => 'like',
        //'secondarySupplierCode' => 'like',
        'supplierName' => 'like',
        /*'liabilityAccountSysemID',
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
        'approvedby',
        'approvedYN',
        'approvedDate',
        'approvedComment',
        'isActive',
        'isSupplierForiegn',
        'supplierConfirmedYN',
        'supplierConfirmedEmpID',
        'supplierConfirmedEmpName',
        'supplierConfirmedDate',
        'isCriticalYN',
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
        'timestamp'*/
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierMaster::class;
    }

    //supCategoryMasterID
}
