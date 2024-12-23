<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderDocumentTypesAPIRequest;
use App\Http\Requests\API\UpdateTenderDocumentTypesAPIRequest;
use App\Models\TenderDocumentTypes;
use App\Repositories\TenderDocumentTypesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\TenderDocumentTypeAssign;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderDocumentTypesController
 * @package App\Http\Controllers\API
 */

class TenderDocumentTypesAPIController extends AppBaseController
{
    /** @var  TenderDocumentTypesRepository */
    private $tenderDocumentTypesRepository;

    public function __construct(TenderDocumentTypesRepository $tenderDocumentTypesRepo)
    {
        $this->tenderDocumentTypesRepository = $tenderDocumentTypesRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderDocumentTypes",
     *      summary="Get a listing of the TenderDocumentTypes.",
     *      tags={"TenderDocumentTypes"},
     *      description="Get all TenderDocumentTypes",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/TenderDocumentTypes")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->tenderDocumentTypesRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderDocumentTypesRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderDocumentTypes = $this->tenderDocumentTypesRepository->all();

        return $this->sendResponse($tenderDocumentTypes->toArray(), 'Tender Document Types retrieved successfully');
    }

    /**
     * @param CreateTenderDocumentTypesAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderDocumentTypes",
     *      summary="Store a newly created TenderDocumentTypes in storage",
     *      tags={"TenderDocumentTypes"},
     *      description="Store TenderDocumentTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderDocumentTypes that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderDocumentTypes")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TenderDocumentTypes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderDocumentTypesAPIRequest $request)
    {
        $input = $request->all();

        $tenderDocumentTypes = $this->tenderDocumentTypesRepository->create($input);

        return $this->sendResponse($tenderDocumentTypes->toArray(), 'Tender Document Types saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderDocumentTypes/{id}",
     *      summary="Display the specified TenderDocumentTypes",
     *      tags={"TenderDocumentTypes"},
     *      description="Get TenderDocumentTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderDocumentTypes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TenderDocumentTypes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var TenderDocumentTypes $tenderDocumentTypes */
        $tenderDocumentTypes = $this->tenderDocumentTypesRepository->findWithoutFail($id);

        if (empty($tenderDocumentTypes)) {
            return $this->sendError('Tender Document Types not found');
        }

        return $this->sendResponse($tenderDocumentTypes->toArray(), 'Tender Document Types retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTenderDocumentTypesAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderDocumentTypes/{id}",
     *      summary="Update the specified TenderDocumentTypes in storage",
     *      tags={"TenderDocumentTypes"},
     *      description="Update TenderDocumentTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderDocumentTypes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderDocumentTypes that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderDocumentTypes")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/TenderDocumentTypes"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderDocumentTypesAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderDocumentTypes $tenderDocumentTypes */
        $tenderDocumentTypes = $this->tenderDocumentTypesRepository->findWithoutFail($id);

        if (empty($tenderDocumentTypes)) {
            return $this->sendError('Tender Document Types not found');
        }

        $tenderDocumentTypes = $this->tenderDocumentTypesRepository->update($input, $id);

        return $this->sendResponse($tenderDocumentTypes->toArray(), 'TenderDocumentTypes updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderDocumentTypes/{id}",
     *      summary="Remove the specified TenderDocumentTypes from storage",
     *      tags={"TenderDocumentTypes"},
     *      description="Delete TenderDocumentTypes",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderDocumentTypes",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var TenderDocumentTypes $tenderDocumentTypes */
        $tenderDocumentTypes = $this->tenderDocumentTypesRepository->findWithoutFail($id);

        if (empty($tenderDocumentTypes)) {
            return $this->sendError('Tender Document Types not found');
        }

        $tenderDocumentTypes->delete();

        return $this->sendSuccess('Tender Document Types deleted successfully');
    }

    public function getTenderAttachmentType(Request $request)
    {
        $input = $request->all();
        $assignDocumentTypes = TenderDocumentTypeAssign::where('tender_id',$input['tenderMasterId'])->where('company_id',$input['companySystemID'])->pluck('document_type_id')->toArray();

        if (in_array(3, $assignDocumentTypes))
        {
            return TenderDocumentTypes::select('id', 'document_type', 'system_generated', 'srm_action', 'sort_order', 'company_id')
                ->with(['attachments' => function($query) use($input){
                    $query->select('attachmentType','documentSystemID', 'documentSystemCode','companySystemID')
                    ->where('documentSystemCode', $input['tenderMasterId'])
                    ->where('companySystemID', $input['companySystemID']);
                if(isset($input['rfx']) && $input['rfx']){
                    $query->where('documentSystemID', '113');
                } else{
                    $query->where('documentSystemID', '108');
                }
            }])->where('company_id',$input['companySystemID'])
            ->whereIn('id',$assignDocumentTypes)->orWhere('system_generated', 1)
            ->orderBy('sort_order')->get();
        }
        else
        {
            return TenderDocumentTypes::select('id', 'document_type', 'system_generated', 'srm_action', 'sort_order', 'company_id')
                ->with(['attachments' => function($query) use($input){
                    $query->select('attachmentType','documentSystemID', 'documentSystemCode','companySystemID')
                    ->where('documentSystemCode', $input['tenderMasterId'])
                    ->where('companySystemID', $input['companySystemID']);
                if(isset($input['rfx']) && $input['rfx']){
                    $query->where('documentSystemID', '113');
                } else {
                    $query->where('documentSystemID', '108');
                }
        }])->where('company_id',$input['companySystemID'])
        ->whereIn('id',$assignDocumentTypes)->orWhere('system_generated', 1)
        ->where('id', '!=', 3)->orderBy('sort_order')->get();
        }
    }

    public function assignDocumentTypes(Request $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        try {
            if (isset($input['document_types'])) {
                if (count($input['document_types']) > 0) {
                    //TenderDocumentTypeAssign::where('tender_id', $input['id'])->where('company_id', $input['company_id'])->delete();
                    foreach ($input['document_types'] as $vl) {
                        $docTypeAssign['tender_id'] = $input['id'];
                        $docTypeAssign['document_type_id'] = $vl['id'];
                        $docTypeAssign['company_id'] = $input['company_id'];
                        $docTypeAssign['created_by'] = $employee->employeeSystemID;
                        TenderDocumentTypeAssign::create($docTypeAssign);
                    }
                } else {
                    //TenderDocumentTypeAssign::where('tender_id', $input['id'])->where('company_id', $input['company_id'])->delete();
                }
            }
            return ['success' => true, 'message' => 'Successfully updated'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e];
        }
    }

    public function deleteAssignDocumentTypes(Request $request)
    {
        $input = $request->all();
        try {
            if (isset($input['doc_type_id'])) {
                $docType = TenderDocumentTypeAssign::where('tender_id', $input['tender_id'])->where('document_type_id', $input['doc_type_id'])->where('company_id', $input['company_id'])->first();
                $result = TenderDocumentTypeAssign::find($docType->id);
                $result->delete();

                if($result){
                    return ['success' => true, 'message' => 'Successfully deleted'];
                } else {
                    return ['success' => false, 'message' => 'Tender document type not found'];
                }
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e];
        }
    }
}
