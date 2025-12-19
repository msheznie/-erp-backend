<?php

namespace App\Repositories;

use App\Models\MaterielRequestDetails;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class MaterielRequestDetailsRepository
 * @package App\Repositories
 * @version June 13, 2018, 11:02 am UTC
 *
 * @method MaterielRequestDetails findWithoutFail($id, $columns = ['*'])
 * @method MaterielRequestDetails find($id, $columns = ['*'])
 * @method MaterielRequestDetails first($columns = ['*'])
*/
class MaterielRequestDetailsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
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
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MaterielRequestDetails::class;
    }
}
