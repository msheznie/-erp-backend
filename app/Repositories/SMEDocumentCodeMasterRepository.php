<?php

namespace App\Repositories;

use App\Models\SMEDocumentCodeMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SMEDocumentCodeMasterRepository
 * @package App\Repositories
 * @version March 9, 2021, 8:46 am +04
 *
 * @method SMEDocumentCodeMaster findWithoutFail($id, $columns = ['*'])
 * @method SMEDocumentCodeMaster find($id, $columns = ['*'])
 * @method SMEDocumentCodeMaster first($columns = ['*'])
*/
class SMEDocumentCodeMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentID',
        'document',
        'prefix',
        'startSerialNo',
        'serialNo',
        'formatLength',
        'approvalLevel',
        'approvalSignatureLevel',
        'format_1',
        'format_2',
        'format_3',
        'format_4',
        'format_5',
        'format_6',
        'isPushNotifyEnabled',
        'isFYBasedSerialNo',
        'postDate',
        'printHeaderFooterYN',
        'printFooterYN',
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
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SMEDocumentCodeMaster::class;
    }
}
