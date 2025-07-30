<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\AttachmentTypeConfiguration;
use App\Models\DocumentAttachments;
use App\Models\DocumentAttachmentType;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class AttachmentTypeConfigurationRepository
 * @package App\Repositories
 * @version July 28, 2025, 6:06 pm +04
 *
 * @method AttachmentTypeConfiguration findWithoutFail($id, $columns = ['*'])
 * @method AttachmentTypeConfiguration find($id, $columns = ['*'])
 * @method AttachmentTypeConfiguration first($columns = ['*'])
*/
class AttachmentTypeConfigurationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'document_attachment_id',
        'attachment_type_id',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AttachmentTypeConfiguration::class;
    }
    public function getAttachmentTypeConfig($companyDocumentAttachmentID): array{
        try{
            $configuredTypes = AttachmentTypeConfiguration::getAllDocumentAttachmentConfig($companyDocumentAttachmentID);
            $allAttachmentTypes = DocumentAttachmentType::getAllDocumentAttachmentType();

            $result = $allAttachmentTypes->map(function ($type) use ($configuredTypes) {
                return [
                    'id' => $type->travelClaimAttachmentTypeID,
                    'documentID' => $type->documentID,
                    'description' => $type->description,
                    'checked' => in_array($type->travelClaimAttachmentTypeID, $configuredTypes)
                ];
            });

            return [
                'success' => true,
                'data' => $result,
                'message' => 'Data retrieved successfully'
            ];

        } catch (\Exception $exception){
            return [
                'success' => false,
                'message' => 'Failed to load attachment types: ' . $exception->getMessage()
            ];
        }
    }
    public function storeAttachmentConfig($input){
        try{
            return DB::transaction(function () use ($input){
                $companyDocumentAttachmentID = $input['companyDocumentAttachmentID'];
                $documentSystemID = $input['documentSystemID'];
                $companySystemID = $input['companySystemID'];
                $newTypeIDs = $input['selected_type_ids'] ?? [];
                $employeeID = Helper::getEmployeeSystemID();

                $existingTypeIDs = AttachmentTypeConfiguration::getAllDocumentAttachmentConfig($companyDocumentAttachmentID);
                $toInsert = array_diff($newTypeIDs, $existingTypeIDs);
                $toDelete = array_diff($existingTypeIDs, $newTypeIDs);

                if (!empty($toDelete)) {
                    $usedTypes = DocumentAttachments::where('documentSystemID', $documentSystemID)
                        ->where('companySystemID', $companySystemID)
                        ->whereIn('attachmentType', $toDelete)
                        ->pluck('attachmentType')
                        ->toArray();

                    if (!empty($usedTypes)) {
                        $typeDescriptions = DocumentAttachmentType::whereIn('travelClaimAttachmentTypeID', $usedTypes)
                            ->pluck('description')
                            ->toArray();

                        return [
                            'success' => false,
                            'message' => 'Cannot remove the following attachment types because they are already used: ' . implode(', ', $typeDescriptions),
                        ];
                    }
                    AttachmentTypeConfiguration::where('document_attachment_id', $companyDocumentAttachmentID)
                        ->whereIn('attachment_type_id', $toDelete)
                        ->delete();
                }
                foreach ($toInsert as $typeID) {
                    AttachmentTypeConfiguration::create([
                        'document_attachment_id' => $companyDocumentAttachmentID,
                        'attachment_type_id' => $typeID,
                        'created_by' => $employeeID,
                        'created_at' => now()
                    ]);
                }

                return [
                    'success' => true,
                    'message' => 'Attachment configuration saved successfully.'
                ];
            });
        } catch (\Exception $exception){
            return [
                'success' => false,
                'message' => 'Failed to save attachment configuration: ' . $exception->getMessage()
            ];
        }
    }
}
