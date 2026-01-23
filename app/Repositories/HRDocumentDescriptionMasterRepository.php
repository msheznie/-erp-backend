<?php

namespace App\Repositories;

use App\Models\HRDocumentDescriptionMaster;
use App\Repositories\BaseRepository;

/**
 * Class HRDocumentDescriptionMasterRepository
 * @package App\Repositories
 * @version August 29, 2021, 2:31 pm +04
 *
 * @method HRDocumentDescriptionMaster findWithoutFail($id, $columns = ['*'])
 * @method HRDocumentDescriptionMaster find($id, $columns = ['*'])
 * @method HRDocumentDescriptionMaster first($columns = ['*'])
*/
class HRDocumentDescriptionMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'DocDescription',
        'systemTypeID',
        'SchMasterID',
        'BranchID',
        'Erp_companyID',
        'isDeleted',
        'CreatedUserName',
        'createdUserID',
        'CreatedDate',
        'CreatedPC',
        'modifiedUserID',
        'ModifiedUserName',
        'Timestamp',
        'ModifiedPC',
        'SortOrder'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HRDocumentDescriptionMaster::class;
    }
}
