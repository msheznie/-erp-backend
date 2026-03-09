<?php

namespace App\Repositories;

use App\Models\ExpenseClaimDetailsMaster;
use App\Repositories\BaseRepository;

/**
 * Class ExpenseClaimDetailsMasterRepository
 * @package App\Repositories
 * @version January 6, 2022, 1:33 pm +04
 *
 * @method ExpenseClaimDetailsMaster findWithoutFail($id, $columns = ['*'])
 * @method ExpenseClaimDetailsMaster find($id, $columns = ['*'])
 * @method ExpenseClaimDetailsMaster first($columns = ['*'])
*/
class ExpenseClaimDetailsMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'expenseClaimMasterAutoID',
        'expenseClaimCategoriesAutoID',
        'crmDocumentID',
        'crmDocumentDetailAutoID',
        'description',
        'referenceNo',
        'segmentID',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionExchangeRate',
        'transactionAmount',
        'transactionCurrencyDecimalPlaces',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalExchangeRate',
        'companyLocalAmount',
        'companyLocalCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingExchangeRate',
        'companyReportingAmount',
        'companyReportingCurrencyDecimalPlaces',
        'empCurrencyID',
        'empCurrency',
        'empCurrencyExchangeRate',
        'empCurrencyAmount',
        'empCurrencyDecimalPlaces',
        'comments',
        'companyID',
        'companyCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ExpenseClaimDetailsMaster::class;
    }
}
