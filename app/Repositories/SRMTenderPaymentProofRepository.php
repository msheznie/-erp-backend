<?php

namespace App\Repositories;

use App\Models\SRMTenderPaymentProof;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SRMTenderPaymentProofRepository
 * @package App\Repositories
 * @version December 3, 2024, 12:35 pm +04
 *
 * @method SRMTenderPaymentProof findWithoutFail($id, $columns = ['*'])
 * @method SRMTenderPaymentProof find($id, $columns = ['*'])
 * @method SRMTenderPaymentProof first($columns = ['*'])
*/
class SRMTenderPaymentProofRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'uuid',
        'serial_no',
        'document_system_id',
        'document_id',
        'document_code',
        'company_id',
        'tender_uuid',
        'srm_supplier_uuid',
        'confirmed_yn',
        'confirmed_by_emp_system_id',
        'confirmed_by_emp_id',
        'confirmed_by_name',
        'confirmed_date',
        'approved_yn',
        'approved_date',
        'approved_emp_system_id',
        'approved_by_emp_id',
        'approved_by_emp_name',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SRMTenderPaymentProof::class;
    }
}
