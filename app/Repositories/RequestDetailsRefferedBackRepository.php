<?php

namespace App\Repositories;

use App\Models\RequestDetailsRefferedBack;
use App\Repositories\BaseRepository;

/**
 * Class RequestDetailsRefferedBackRepository
 * @package App\Repositories
 * @version December 6, 2018, 11:13 am UTC
 *
 * @method RequestDetailsRefferedBack findWithoutFail($id, $columns = ['*'])
 * @method RequestDetailsRefferedBack find($id, $columns = ['*'])
 * @method RequestDetailsRefferedBack first($columns = ['*'])
*/
class RequestDetailsRefferedBackRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'RequestDetailsID',
        'RequestID',
        'itemCode',
        'itemDescription',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBS',
        'financeGLcodePL',
        'includePLForGRVYN',
        'partNumber',
        'unitOfMeasure',
        'unitOfMeasureIssued',
        'quantityRequested',
        'qtyIssuedDefaultMeasure',
        'convertionMeasureVal',
        'comments',
        'quantityOnOrder',
        'quantityInHand',
        'estimatedCost',
        'minQty',
        'maxQty',
        'selectedForIssue',
        'ClosedYN',
        'allowCreatePR',
        'selectedToCreatePR',
        'timesReferred',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return RequestDetailsRefferedBack::class;
    }
}
