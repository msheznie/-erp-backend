<?php

namespace App\Repositories;

use App\Models\HRMSPersonalDocuments;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class HRMSPersonalDocumentsRepository
 * @package App\Repositories
 * @version November 20, 2019, 11:39 am +04
 *
 * @method HRMSPersonalDocuments findWithoutFail($id, $columns = ['*'])
 * @method HRMSPersonalDocuments find($id, $columns = ['*'])
 * @method HRMSPersonalDocuments first($columns = ['*'])
*/
class HRMSPersonalDocumentsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentType',
        'empID',
        'employeeSystemID',
        'documentNo',
        'docIssuedby',
        'issueDate',
        'expireDate',
        'expireDate_O',
        'categoryID',
        'attachmentFileName',
        'isActive',
        'createdUserGroup',
        'createdpc',
        'modifieduser',
        'modifiedpc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return HRMSPersonalDocuments::class;
    }
}
