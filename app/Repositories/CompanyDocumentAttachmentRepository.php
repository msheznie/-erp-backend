<?php

namespace App\Repositories;

use App\Models\CompanyDocumentAttachment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CompanyDocumentAttachmentRepository
 * @package App\Repositories
 * @version March 29, 2018, 5:13 am UTC
 *
 * @method CompanyDocumentAttachment findWithoutFail($id, $columns = ['*'])
 * @method CompanyDocumentAttachment find($id, $columns = ['*'])
 * @method CompanyDocumentAttachment first($columns = ['*'])
*/
class CompanyDocumentAttachmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'docRefNumber',
        'isAttachmentYN',
        'sendEmailYN',
        'codeGeneratorFormat',
        'isAmountApproval',
        'isServiceLineApproval',
        'blockYN',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CompanyDocumentAttachment::class;
    }
}
