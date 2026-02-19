<?php

namespace App\Repositories;

use App\Models\FreeBillingMasterPerforma;
use App\Repositories\BaseRepository;

/**
 * Class FreeBillingMasterPerformaRepository
 * @package App\Repositories
 * @version August 10, 2018, 8:30 am UTC
 *
 * @method FreeBillingMasterPerforma findWithoutFail($id, $columns = ['*'])
 * @method FreeBillingMasterPerforma find($id, $columns = ['*'])
 * @method FreeBillingMasterPerforma first($columns = ['*'])
*/
class FreeBillingMasterPerformaRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'BillProcessNO',
        'PerformaInvoiceNo',
        'PerformaInvoiceText',
        'Ticketno',
        'clientID',
        'contractID',
        'performaDate',
        'performaStatus',
        'BillProcessDate',
        'SelectedForPerformaYN',
        'InvoiceNo',
        'PerformaOpConfirmed',
        'PerformaFinanceConfirmed',
        'performaOpConfirmedBy',
        'performaOpConfirmedDate',
        'performaFinanceConfirmedBy',
        'performaFinanceConfirmedDate',
        'confirmedYN',
        'confirmedBy',
        'confirmedDate',
        'confirmedByName',
        'approvedYN',
        'approvedBy',
        'approvedDate',
        'documentID',
        'companyID',
        'serviceLineCode',
        'serialNo',
        'billingCode',
        'performaSerialNo',
        'performaCode',
        'rentalStartDate',
        'rentalEndDate',
        'rentalType',
        'createdUserID',
        'modifiedUserID',
        'timeStamp',
        'performaMasterID',
        'isTrasportRental',
        'disableRental',
        'IsOpStbDaysFromMIT'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FreeBillingMasterPerforma::class;
    }
}
