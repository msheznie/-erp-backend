<?php

namespace App\Repositories;

use App\Models\PaymentBankTransfer;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class PaymentBankTransferRepository
 * @package App\Repositories
 * @version October 2, 2018, 10:55 am UTC
 *
 * @method PaymentBankTransfer findWithoutFail($id, $columns = ['*'])
 * @method PaymentBankTransfer find($id, $columns = ['*'])
 * @method PaymentBankTransfer first($columns = ['*'])
*/
class PaymentBankTransferRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemID',
        'documentID',
        'companySystemID',
        'bankTransferDocumentCode',
        'serialNumber',
        'documentDate',
        'bankMasterID',
        'bankAccountAutoID',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approvedYN',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'RollLevForApp_curr',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PaymentBankTransfer::class;
    }
}
