<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\TenderDocumentTypeAssign;
use App\Models\TenderDocumentTypeAssignLog;
use App\Models\TenderDocumentTypes;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;
use App\Services\SrmDocumentModifyService;

/**
 * Class TenderDocumentTypesRepository
 * @package App\Repositories
 * @version June 2, 2022, 10:58 am +04
 *
 * @method TenderDocumentTypes findWithoutFail($id, $columns = ['*'])
 * @method TenderDocumentTypes find($id, $columns = ['*'])
 * @method TenderDocumentTypes first($columns = ['*'])
*/
class TenderDocumentTypesRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'document_type',
        'srm_action',
        'created_by',
        'updated_by',
        'company_id'
    ];
    protected $srmDocumentModifyService;
    public function __construct(
        Application $app,
        SrmDocumentModifyService $srmDocumentModifyService
    ){
        parent::__construct($app);
        $this->srmDocumentModifyService = $srmDocumentModifyService;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderDocumentTypes::class;
    }
    public function assignTenderDocumentType($input){
        try{
            return DB::transaction(function () use ($input){
                $employee = Helper::getEmployeeInfo();
                $documentTypes = $input['document_types'] ?? [];
                $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($input['id']);
                if(!empty($documentTypes)){
                    foreach ($documentTypes as $key => $value) {
                        $docTypeAssign[$key] = [
                            'tender_id' => $input['id'],
                            'document_type_id' => $value['id'],
                            'company_id' => $input['company_id'],
                            'created_at' => now(),
                            'created_by' => $employee->employeeSystemID
                        ];
                        if($requestData['enableRequestChange']){
                            $docTypeAssign[$key]['id'] = null;
                            $docTypeAssign[$key]['level_no'] = 1;
                            $docTypeAssign[$key]['version_id'] = $requestData['versionID'];
                        }
                    }
                    $requestData['enableRequestChange'] ?
                        TenderDocumentTypeAssignLog::insert($docTypeAssign) :
                        TenderDocumentTypeAssign::insert($docTypeAssign);

                    return ['success' => true, 'message' => 'Created successfully'];
                } else {
                    return ['success' => false, 'message' => 'Please select document type'];
                }
            });
        } catch(\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public function getTenderAttachmentTypes($input){
        $tenderMasterId = $input['tenderMasterId'];
        $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($tenderMasterId);
        $assignDocumentTypes = $requestData['enableRequestChange'] ?
            TenderDocumentTypeAssignLog::getAssignedDocs($tenderMasterId, $requestData['versionID'])->pluck('document_type_id')->toArray() :
            TenderDocumentTypeAssign::getTenderDocumentTypeAssign($tenderMasterId)->pluck('document_type_id')->toArray();

        return TenderDocumentTypes::getTenderAttachmentTypes(
            $input['tenderMasterId'],
            $input['companySystemID'],
            $assignDocumentTypes,
            isset($input['rfx']) && $input['rfx'],
            $requestData['enableRequestChange'],
            $requestData['versionID']
        );
    }
    public function deleteAssignDocumentTypes($input){
        try{
            return DB::transaction(function () use ($input){
                $docTypeID = $input['doc_type_id'] ?? 0;
                if($docTypeID > 0){
                    $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($input['tender_id']);
                    $docTypeData = $requestData['enableRequestChange'] ?
                        TenderDocumentTypeAssignLog::getTenderDocumentTypeAssigned($input['tender_id'], $docTypeID, $input['company_id'], $requestData['versionID']) :
                        TenderDocumentTypeAssign::getTenderDocumentTypeAssigned($input['tender_id'], $docTypeID, $input['company_id']);

                    if($requestData['enableRequestChange']){
                        $docTypeData->is_deleted = 1;
                        $docTypeData->save();
                    } else {
                        $result = TenderDocumentTypeAssign::find($docTypeData->id);
                        $result->delete();
                    }
                    return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_deleted')];

                } else {
                    return ['success' => false, 'message' => trans('srm_tender_rfx.tender_document_type_not_found')];
                }
            });
        } catch(\Exception $ex){
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
}
