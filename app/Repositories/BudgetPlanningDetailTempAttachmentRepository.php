<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\BudgetPlanningDetailTempAttachment;
use App\Models\DepartmentBudgetPlanningDetail;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BudgetPlanningDetailTempAttachmentRepository
 * @package App\Repositories
 * @version August 26, 2025, 9:50 am +04
 *
 * @method BudgetPlanningDetailTempAttachment findWithoutFail($id, $columns = ['*'])
 * @method BudgetPlanningDetailTempAttachment find($id, $columns = ['*'])
 * @method BudgetPlanningDetailTempAttachment first($columns = ['*'])
*/
class BudgetPlanningDetailTempAttachmentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'entry_id',
        'file_name',
        'original_file_name',
        'file_path',
        'file_type',
        'file_size',
        'attachment_type_id',
        'uploaded_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BudgetPlanningDetailTempAttachment::class;
    }
    public function deleteAttachment($id, $attachmentData){
        try{
            $entryData = BudgetPlanningDetailTempAttachment::getBudgetTempEntryData($attachmentData->entry_id);
            if(empty($entryData)){
                return [
                    'success' => false,
                    'message' => 'Budget planning detail template entry not found'
                ];
            }

            $departmentBudget = DepartmentBudgetPlanningDetail::getBudgetPlaningCompany($entryData->budget_detail_id);
            if(!$departmentBudget){
                return [
                    'success' => false,
                    'message' => 'Budget department not found'
                ];
            }

            $companySystemID = $departmentBudget->departmentBudgetPlanning->masterBudgetPlannings->companySystemID ?? 1;
            $path = $attachmentData->file_path;
            $disk = Helper::policyWiseDisk($companySystemID, 'public');
            if(Storage::disk($disk)->exists($path)){
                $attachmentData->delete();
                Storage::disk($disk)->delete($path);
            } else {
                $attachmentData->delete();
            }

            return [
                'success' => true,
                'message' => 'Document Attachments deleted successfully'
            ];

        } catch (\Exception $ex) {
            return [
                'success' => false,
                'message' => 'Unexpected Error: ' . $ex->getMessage()
            ];
        }
    }
}
