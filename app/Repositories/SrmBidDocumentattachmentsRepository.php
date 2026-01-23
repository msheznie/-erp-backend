<?php

namespace App\Repositories;

use App\Models\SrmBidDocumentattachments;
use App\Repositories\BaseRepository;

/**
 * Class SrmBidDocumentattachmentsRepository
 * @package App\Repositories
 * @version October 24, 2022, 9:04 am +04
 *
 * @method SrmBidDocumentattachments findWithoutFail($id, $columns = ['*'])
 * @method SrmBidDocumentattachments find($id, $columns = ['*'])
 * @method SrmBidDocumentattachments first($columns = ['*'])
*/
class SrmBidDocumentattachmentsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'attachmentDescription',
        'originalFileName',
        'myFileName',
        'path',
        'sizeInKbs'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SrmBidDocumentattachments::class;
    }
}
