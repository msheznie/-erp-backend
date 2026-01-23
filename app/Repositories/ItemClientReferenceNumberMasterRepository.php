<?php

namespace App\Repositories;

use App\Models\ItemClientReferenceNumberMaster;
use App\Repositories\BaseRepository;

/**
 * Class ItemClientReferenceNumberMasterRepository
 * @package App\Repositories
 * @version August 13, 2018, 10:21 am UTC
 *
 * @method ItemClientReferenceNumberMaster findWithoutFail($id, $columns = ['*'])
 * @method ItemClientReferenceNumberMaster find($id, $columns = ['*'])
 * @method ItemClientReferenceNumberMaster first($columns = ['*'])
*/
class ItemClientReferenceNumberMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'idItemAssigned',
        'itemSystemCode',
        'itemPrimaryCode',
        'itemDescription',
        'unitOfMeasure',
        'companySystemID',
        'companyID',
        'customerID',
        'contractUIID',
        'contractID',
        'clientReferenceNumber',
        'createdByUserID',
        'createdDateTime',
        'modifiedByUserID',
        'modifiedDateTime',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemClientReferenceNumberMaster::class;
    }
}
