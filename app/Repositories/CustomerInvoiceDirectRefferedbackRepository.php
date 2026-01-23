<?php

namespace App\Repositories;

use App\Models\CustomerInvoiceDirectRefferedback;
use App\Repositories\BaseRepository;

/**
 * Class CustomerInvoiceDirectRefferedbackRepository
 * @package App\Repositories
 * @version November 28, 2018, 8:13 am UTC
 *
 * @method CustomerInvoiceDirectRefferedback findWithoutFail($id, $columns = ['*'])
 * @method CustomerInvoiceDirectRefferedback find($id, $columns = ['*'])
 * @method CustomerInvoiceDirectRefferedback first($columns = ['*'])
*/
class CustomerInvoiceDirectRefferedbackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'custInvoiceDirectAutoID',
        'transactionMode',
        'companySystemID',
        'companyID',
        'documentSystemiD',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'serviceLineSystemID',
        'serviceLineCode',
        'wareHouseSystemCode',
        'bookingInvCode',
        'bookingDate',
        'comments',
        'invoiceDueDate',
        'customerGRVAutoID',
        'bankID',
        'bankAccountID',
        'performaDate',
        'wanNO',
        'PONumber',
        'rigNo',
        'customerID',
        'customerGLCode',
        'customerGLSystemID',
        'customerInvoiceNo',
        'customerInvoiceDate',
        'custTransactionCurrencyID',
        'custTransactionCurrencyER',
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
        'postedDate',
        'servicePeriod',
        'paymentInDaysForJob',
        'serviceStartDate',
        'serviceEndDate',
        'isPerforma',
        'documentType',
        'secondaryLogoCompanySystemID',
        'secondaryLogoCompID',
        'secondaryLogo',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'selectedForTracking',
        'customerInvoiceTrackingID',
        'interCompanyTransferYN',
        'canceledByEmpSystemID',
        'canceledYN',
        'canceledByEmpID',
        'canceledByEmpName',
        'vatOutputGLCodeSystemID',
        'vatOutputGLCode',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'discountLocalAmount',
        'discountAmount',
        'discountRptAmount',
        'canceledDateTime',
        'canceledComments',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'createdDateAndTime',
        'timestamp',
        'approvedByUserID',
        'approvedByUserSystemID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CustomerInvoiceDirectRefferedback::class;
    }
}
