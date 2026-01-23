<?php

namespace App\Repositories;

use App\Models\PaymentBankTransferRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class PaymentBankTransferRefferedBackRepository
 * @package App\Repositories
 * @version December 11, 2018, 5:28 am UTC
 *
 * @method PaymentBankTransferRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method PaymentBankTransferRefferedBack find($id, $columns = ['*'])
 * @method PaymentBankTransferRefferedBack first($columns = ['*'])
*/
class PaymentBankTransferRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'paymentBankTransferID',
        'documentSystemID',
        'documentID',
        'companySystemID',
        'bankTransferDocumentCode',
        'serialNumber',
        'documentDate',
        'narration',
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
        'refferedBackYN',
        'timesReferred',
        'createdPcID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedPc',
        'modifiedUserSystemID',
        'modifiedUser',
        'createdDateTime',
        'timeStamp',
        'exportedYN',
        'exportedUserSystemID',
        'exportedDate'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return PaymentBankTransferRefferedBack::class;
    }
}
