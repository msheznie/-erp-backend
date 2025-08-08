<?php

namespace App\Repositories;

use App\Models\CompanyDocumentAttachment;
use App\Models\DocumentAttachmentType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocumentAttachmentTypeRepository
 * @package App\Repositories
 * @version April 3, 2018, 12:19 pm UTC
 *
 * @method DocumentAttachmentType findWithoutFail($id, $columns = ['*'])
 * @method DocumentAttachmentType find($id, $columns = ['*'])
 * @method DocumentAttachmentType first($columns = ['*'])
*/
class DocumentAttachmentTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentID',
        'description',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocumentAttachmentType::class;
    }

    public function documentAttachmentTypes($documentSystemID, $companySystemID)
    {
        $attachmentConfig = CompanyDocumentAttachment::getCompanyDocumentAttachmentList(
            $documentSystemID,
            $companySystemID
        );

        if (empty($attachmentConfig)) {
            return DocumentAttachmentType::get();
        }

        $configuredTypes = collect($attachmentConfig->attachmentTypeConfiguration)
            ->pluck('attachment_type_id')
            ->toArray();

        if (empty($configuredTypes)) {
            return collect();
        }

        return DocumentAttachmentType::whereIn('travelClaimAttachmentTypeID', $configuredTypes)->get();
    }

}
