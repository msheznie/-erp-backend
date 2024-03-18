<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentModifyRequestAPIRequest;
use App\Http\Requests\API\UpdateDocumentModifyRequestAPIRequest;
use App\Models\DocumentModifyRequest;
use App\Repositories\DocumentModifyRequestRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DocumentMaster;
use App\Models\Company;
use App\Models\TenderMaster;
use Carbon\Carbon;
/**
 * Class DocumentModifyRequestController
 * @package App\Http\Controllers\API
 */

class DocumentModifyRequestAPIController extends AppBaseController
{
    /** @var  DocumentModifyRequestRepository */
    private $documentModifyRequestRepository;

    public function __construct(DocumentModifyRequestRepository $documentModifyRequestRepo)
    {
        $this->documentModifyRequestRepository = $documentModifyRequestRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/documentModifyRequests",
     *      summary="getDocumentModifyRequestList",
     *      tags={"DocumentModifyRequest"},
     *      description="Get all DocumentModifyRequests",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/DocumentModifyRequest")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->documentModifyRequestRepository->pushCriteria(new RequestCriteria($request));
        $this->documentModifyRequestRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentModifyRequests = $this->documentModifyRequestRepository->all();

        return $this->sendResponse($documentModifyRequests->toArray(), 'Document Modify Requests retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/documentModifyRequests",
     *      summary="createDocumentModifyRequest",
     *      tags={"DocumentModifyRequest"},
     *      description="Create DocumentModifyRequest",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/DocumentModifyRequest"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentModifyRequestAPIRequest $request)
    {
        $input = $request->all();

        $documentModifyRequest = $this->documentModifyRequestRepository->create($input);

        return $this->sendResponse($documentModifyRequest->toArray(), 'Document Modify Request saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/documentModifyRequests/{id}",
     *      summary="getDocumentModifyRequestItem",
     *      tags={"DocumentModifyRequest"},
     *      description="Get DocumentModifyRequest",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentModifyRequest",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/DocumentModifyRequest"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var DocumentModifyRequest $documentModifyRequest */
        $documentModifyRequest = $this->documentModifyRequestRepository->findWithoutFail($id);

        if (empty($documentModifyRequest)) {
            return $this->sendError('Document Modify Request not found');
        }

        return $this->sendResponse($documentModifyRequest->toArray(), 'Document Modify Request retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/documentModifyRequests/{id}",
     *      summary="updateDocumentModifyRequest",
     *      tags={"DocumentModifyRequest"},
     *      description="Update DocumentModifyRequest",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentModifyRequest",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/DocumentModifyRequest"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentModifyRequestAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentModifyRequest $documentModifyRequest */
        $documentModifyRequest = $this->documentModifyRequestRepository->findWithoutFail($id);

        if (empty($documentModifyRequest)) {
            return $this->sendError('Document Modify Request not found');
        }

        $documentModifyRequest = $this->documentModifyRequestRepository->update($input, $id);

        return $this->sendResponse($documentModifyRequest->toArray(), 'DocumentModifyRequest updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/documentModifyRequests/{id}",
     *      summary="deleteDocumentModifyRequest",
     *      tags={"DocumentModifyRequest"},
     *      description="Delete DocumentModifyRequest",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DocumentModifyRequest",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var DocumentModifyRequest $documentModifyRequest */
        $documentModifyRequest = $this->documentModifyRequestRepository->findWithoutFail($id);

        if (empty($documentModifyRequest)) {
            return $this->sendError('Document Modify Request not found');
        }

        $documentModifyRequest->delete();

        return $this->sendSuccess('Document Modify Request deleted successfully');
    }

    public function createEditRequest(Request $request)
    {
        DB::beginTransaction();
        try {
                $input = $request->all();
                $version = 1;
                $is_vsersion_exit = DocumentModifyRequest::where('documentSystemCode',$input['documentSystemCode'])->latest('id')->first();
                if(isset($is_vsersion_exit))
                {
                    $version = $is_vsersion_exit->version + 1;
                }
                $document_master_id = $input['document_master_id'];
                $namespacedModel = 'App\Models\\' . $input["modelName"]; 
                $company = Company::where('companySystemID', $input['companySystemID'])->first();
                $documentMaster = DocumentMaster::where('documentSystemID', $document_master_id)->first();
                $lastSerial = DocumentModifyRequest::where('companySystemID', $input['companySystemID'])
                ->orderBy('id', 'desc')
                ->first();
                $lastSerialNumber = 1;
                if ($lastSerial) {
                    $lastSerialNumber = intval($lastSerial->serial_number) + 1;
                }
        
                $code = ($company->CompanyID . '/' . $documentMaster['documentID'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

                $input['version'] = $version;
                $input['requested_employeeSystemID'] =\Helper::getEmployeeSystemID();
                $input['requested_date'] = now();
                $input['RollLevForApp_curr'] = 1;
                $input['code'] = $code;
                $input['serial_number'] = $lastSerialNumber;
                $input['modify_type'] = 1;
                
                $documentModifyRequest = $this->documentModifyRequestRepository->create($input);

                $tender_data['tender_edit_version_id'] = $documentModifyRequest['id'];
                $result = $namespacedModel::where('id', $input['documentSystemCode'])->update($tender_data);
                
                $params = array('autoID' => $documentModifyRequest['id'], 'company' => $input["companySystemID"], 'document' => $input["document_master_id"],'reference_document_id' => $input["requested_document_master_id"]);
                $confirm = \Helper::confirmDocument($params);
           

                DB::commit();
                if (!$confirm["success"]) {
                    DB::rollback();
                    return ['success' => false, 'message' => $confirm["message"]];
                } else {
                    return ['success' => true, 'message' => 'Tender modify request sent successfully'];
                }


            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
                return ['success' => false, 'message' => $e];
            }
    }


    public function failed($exception)
    {
        return $exception->getMessage();
    }

    public function approveEditDocument(Request $request)
    {
        $input = $request->all(); 

        if(isset($input['reference_document_id']) && $input['reference_document_id'])
        {
            $currentDate = Carbon::now()->format('Y-m-d H:i:s');
            $openingDate = Carbon::createFromFormat('Y-m-d H:i:s', $input['bid_submission_closing_date']);

            $result = $openingDate->gt($currentDate);
    
            if(!$result)
            {
                return $this->sendError('Unable to approve this document. Bid submission closing date has already passed');
            }

            $approve = \Helper::approveDocument($request);

            if (!$approve["success"]) {
                return $this->sendError($approve["message"]);
            }

            return $this->sendResponse(array(), $approve["message"]);
        } 

    }
}
