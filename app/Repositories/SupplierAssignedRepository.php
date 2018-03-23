<?php

namespace App\Repositories;

use App\Models\SupplierAssigned;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SupplierAssignedRepository
 * @package App\Repositories
 * @version March 2, 2018, 12:33 pm UTC
 *
 * @method SupplierAssigned findWithoutFail($id, $columns = ['*'])
 * @method SupplierAssigned find($id, $columns = ['*'])
 * @method SupplierAssigned first($columns = ['*'])
*/
class SupplierAssignedRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'supplierCodeSytem',
        'companySystemID',
        'companyID',
        'uniqueTextcode',
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
        'supplierImportanceID',
        'supplierNatureID',
        'supplierTypeID',
        'WHTApplicable',
        'isRelatedPartyYN',
        'isCriticalYN',
        'isActive',
        'isAssigned',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierAssigned::class;
    }
}
