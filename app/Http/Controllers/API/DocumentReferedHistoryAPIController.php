<?php
/**
 * =============================================
 * -- File Name : DocumentReferedHistoryAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Document Master
 * -- Author : Mohamed Fayas
 * -- Create date : 18 - December 2018
 * -- Description : This file contains the all CRUD for Document Master
 * -- REVISION HISTORY
 * -- Date: 18 December 2018 By: Fayas Description: Added new function getReferBackApprovedDetails()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentReferedHistoryAPIRequest;
use App\Http\Requests\API\UpdateDocumentReferedHistoryAPIRequest;
use App\Models\CompanyDocumentAttachment;
use App\Models\DocumentReferedHistory;
use App\Models\EmployeesDepartment;
use App\Repositories\DocumentReferedHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentReferedHistoryController
 * @package App\Http\Controllers\API
 */

class DocumentReferedHistoryAPIController extends AppBaseController
{
    /** @var  DocumentReferedHistoryRepository */
    private $documentReferedHistoryRepository;

    public function __construct(DocumentReferedHistoryRepository $documentReferedHistoryRepo)
    {
        $this->documentReferedHistoryRepository = $documentReferedHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/documentReferedHistories",
     *      summary="Get a listing of the DocumentReferedHistories.",
     *      tags={"DocumentReferedHistory"},
     *      description="Get all DocumentReferedHistories",
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
     *                  @SWG\Items(ref="#/definitions/DocumentReferedHistory")
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
        $this->documentReferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->documentReferedHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentReferedHistories = $this->documentReferedHistoryRepository->all();

        return $this->sendResponse($documentReferedHistories->toArray(), trans('custom.document_refered_histories_retrieved_successfully'));
    }

    /**
     * @param CreateDocumentReferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/documentReferedHistories",
     *      summary="Store a newly created DocumentReferedHistory in storage",
     *      tags={"DocumentReferedHistory"},
     *      description="Store DocumentReferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DocumentReferedHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DocumentReferedHistory")
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
     *                  ref="#/definitions/DocumentReferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentReferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        $documentReferedHistories = $this->documentReferedHistoryRepository->create($input);

        return $this->sendResponse($documentReferedHistories->toArray(), trans('custom.document_refered_history_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/documentReferedHistories/{id}",
     *      summary="Display the specified DocumentReferedHistory",
     *      tags={"DocumentReferedHistory"},
     *      description="Get DocumentReferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentReferedHistory",
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
     *                  ref="#/definitions/DocumentReferedHistory"
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
        /** @var DocumentReferedHistory $documentReferedHistory */
        $documentReferedHistory = $this->documentReferedHistoryRepository->findWithoutFail($id);

        if (empty($documentReferedHistory)) {
            return $this->sendError(trans('custom.document_refered_history_not_found'));
        }

        return $this->sendResponse($documentReferedHistory->toArray(), trans('custom.document_refered_history_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateDocumentReferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/documentReferedHistories/{id}",
     *      summary="Update the specified DocumentReferedHistory in storage",
     *      tags={"DocumentReferedHistory"},
     *      description="Update DocumentReferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentReferedHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DocumentReferedHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DocumentReferedHistory")
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
     *                  ref="#/definitions/DocumentReferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentReferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentReferedHistory $documentReferedHistory */
        $documentReferedHistory = $this->documentReferedHistoryRepository->findWithoutFail($id);

        if (empty($documentReferedHistory)) {
            return $this->sendError(trans('custom.document_refered_history_not_found'));
        }

        $documentReferedHistory = $this->documentReferedHistoryRepository->update($input, $id);

        return $this->sendResponse($documentReferedHistory->toArray(), trans('custom.documentreferedhistory_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/documentReferedHistories/{id}",
     *      summary="Remove the specified DocumentReferedHistory from storage",
     *      tags={"DocumentReferedHistory"},
     *      description="Delete DocumentReferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentReferedHistory",
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
        /** @var DocumentReferedHistory $documentReferedHistory */
        $documentReferedHistory = $this->documentReferedHistoryRepository->findWithoutFail($id);

        if (empty($documentReferedHistory)) {
            return $this->sendError(trans('custom.document_refered_history_not_found'));
        }

        $documentReferedHistory->delete();

        return $this->sendResponse($id, trans('custom.document_refered_history_deleted_successfully'));
    }

    public function getReferBackApprovedDetails(Request $request)
    {
        $input = $request->all();

        $companySystemID = $input['companySystemID'];
        $documentSystemCode = $input['documentSystemCode'];
        $documentSystemID = $input['documentSystemID'];
        $refTimes = $input['refTimes'];

        $approveDetails = DocumentReferedHistory::where('documentSystemID', $documentSystemID)
            ->where('documentSystemCode', $documentSystemCode)
            ->where('companySystemID', $companySystemID)
            ->where('refTimes', $refTimes)
            ->with(['approved_by'])
            ->get();

        foreach ($approveDetails as $value) {

            if ($value['approvedYN'] == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $companySystemID)
                    ->where('documentSystemID', $documentSystemID)
                    ->first();

                if (empty($companyDocument)) {
                    return $this->sendError(trans('custom.policy_not_found'));
                }

                $approvalList = EmployeesDepartment::where('employeeGroupID', $value['approvalGroupID'])
                    ->where('companySystemID', $companySystemID)
                    ->where('documentSystemID', $documentSystemID);
                //->get();

                if ($companyDocument['isServiceLineApproval'] == -1) {
                    $approvalList = $approvalList->where('ServiceLineSystemID', $value['serviceLineSystemID']);
                }

                $approvalList = $approvalList->with(['employee'])
                    ->groupBy('employeeSystemID')
                    ->get();
                $value['approval_list'] = $approvalList;
            }
        }

        return $this->sendResponse($approveDetails, trans('custom.record_retrieved_successfully_1'));
    }
}
