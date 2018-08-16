<?php

namespace App\Repositories;

use App\Models\DebitNote;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DebitNoteRepository
 * @package App\Repositories
 * @version August 16, 2018, 10:12 am UTC
 *
 * @method DebitNote findWithoutFail($id, $columns = ['*'])
 * @method DebitNote find($id, $columns = ['*'])
 * @method DebitNote first($columns = ['*'])
*/
class DebitNoteRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'debitNoteCode',
        'debitNoteDate',
        'comments',
        'supplierID',
        'supplierGLCode',
        'supplierTransactionCurrencyID',
        'supplierTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'debitAmountTrans',
        'debitAmountLocal',
        'debitAmountRpt',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'documentType',
        'timesReferred',
        'RollLevForApp_curr',
        'matchInvoice',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpSystemID',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DebitNote::class;
    }
}
