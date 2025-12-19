<?php

namespace App\Repositories;

use App\Models\BookInvSuppMasterRefferedBack;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BookInvSuppMasterRefferedBackRepository
 * @package App\Repositories
 * @version September 27, 2018, 10:26 am UTC
 *
 * @method BookInvSuppMasterRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method BookInvSuppMasterRefferedBack find($id, $columns = ['*'])
 * @method BookInvSuppMasterRefferedBack first($columns = ['*'])
*/
class BookInvSuppMasterRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bookingSuppMasInvAutoID',
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
        'bookingInvCode',
        'bookingDate',
        'comments',
        'secondaryRefNo',
        'supplierID',
        'supplierGLCodeSystemID',
        'supplierGLCode',
        'UnbilledGRVAccountSystemID',
        'UnbilledGRVAccount',
        'supplierInvoiceNo',
        'supplierInvoiceDate',
        'supplierTransactionCurrencyID',
        'supplierTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'bookingAmountTrans',
        'bookingAmountLocal',
        'bookingAmountRpt',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'postedDate',
        'documentType',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'interCompanyTransferYN',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'createdDateAndTime',
        'cancelYN',
        'cancelComment',
        'cancelDate',
        'canceledByEmpSystemID',
        'canceledByEmpID',
        'canceledByEmpName',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BookInvSuppMasterRefferedBack::class;
    }
}
