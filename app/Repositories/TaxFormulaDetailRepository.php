<?php

namespace App\Repositories;

use App\Models\TaxFormulaDetail;
use App\Repositories\BaseRepository;

/**
 * Class TaxFormulaDetailRepository
 * @package App\Repositories
 * @version April 24, 2018, 6:14 am UTC
 *
 * @method TaxFormulaDetail findWithoutFail($id, $columns = ['*'])
 * @method TaxFormulaDetail find($id, $columns = ['*'])
 * @method TaxFormulaDetail first($columns = ['*'])
*/
class TaxFormulaDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'taxCalculationformulaID',
        'taxMasterAutoID',
        'description',
        'taxMasters',
        'sortOrder',
        'formula',
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
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TaxFormulaDetail::class;
    }
}
