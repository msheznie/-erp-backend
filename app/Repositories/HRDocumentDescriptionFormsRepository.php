<?php

namespace App\Repositories;

use App\Models\HRDocumentDescriptionForms;
use App\Repositories\BaseRepository;

/**
 * Class HRDocumentDescriptionFormsRepository
 * @package App\Repositories
 * @version August 29, 2021, 11:20 am +04
 *
 * @method HRDocumentDescriptionForms findWithoutFail($id, $columns = ['*'])
 * @method HRDocumentDescriptionForms find($id, $columns = ['*'])
 * @method HRDocumentDescriptionForms first($columns = ['*'])
*/
class HRDocumentDescriptionFormsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'DocDesSetID',
        'DocDesID',
        'subDocumentType',
        'FormType',
        'PersonType',
        'PersonID',
        'FileName',
        'UploadedDate',
        'issueDate',
        'expireDate',
        'issuedBy',
        'issuedByText',
        'documentNo',
        'isActive',
        'isDeleted',
        'isExpiryMailSend',
        'SchMasterID',
        'BranchID',
        'Erp_companyID',
        'AcademicYearID',
        'isSubmitted',
        'CreatedUserID',
        'CreatedUserName',
        'CreatedDate',
        'CreatedPC',
        'ModifiedUserID',
        'ModifiedUserName',
        'ModifiedDateTime',
        'ModifiedPC',
        'Timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HRDocumentDescriptionForms::class;
    }
}
