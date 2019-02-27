<?php

namespace App\Repositories;

use App\Models\QuotationVersionDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class QuotationVersionDetailsRepository
 * @package App\Repositories
 * @version January 29, 2019, 1:04 pm +04
 *
 * @method QuotationVersionDetails findWithoutFail($id, $columns = ['*'])
 * @method QuotationVersionDetails find($id, $columns = ['*'])
 * @method QuotationVersionDetails first($columns = ['*'])
*/
class QuotationVersionDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'quotationDetailsID',
        'quotationMasterID',
        'versionNo',
        'itemAutoID',
        'itemSystemCode',
        'itemDescription',
        'itemCategory',
        'defaultUOMID',
        'itemReferenceNo',
        'defaultUOM',
        'unitOfMeasureID',
        'unitOfMeasure',
        'conversionRateUOM',
        'requestedQty',
        'invoicedYN',
        'comment',
        'remarks',
        'unittransactionAmount',
        'discountPercentage',
        'discountAmount',
        'discountTotal',
        'transactionAmount',
        'companyLocalAmount',
        'companyReportingAmount',
        'customerAmount',
        'companySystemID',
        'companyID',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timesReferred',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return QuotationVersionDetails::class;
    }
}
