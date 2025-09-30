<?php
/**
 * =============================================
 * -- File Name : DocumentRestrictionAssignAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Document Restriction Assign
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - December 2018
 * -- Description : This file contains the all CRUD for Document Restriction Assign
 * -- REVISION HISTORY
 *  Date: 14 -December 2018 By: Fayas Description: Added new function checkRestrictionByPolicy()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentRestrictionAssignAPIRequest;
use App\Http\Requests\API\UpdateDocumentRestrictionAssignAPIRequest;
use App\Models\DocumentRestrictionAssign;
use App\Models\EmployeeNavigation;
use App\Models\User;
use App\Repositories\DocumentRestrictionAssignRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentRestrictionAssignController
 * @package App\Http\Controllers\API
 */

class DocumentRestrictionAssignAPIController extends AppBaseController
{
    /** @var  DocumentRestrictionAssignRepository */
    private $documentRestrictionAssignRepository;

    public function __construct(DocumentRestrictionAssignRepository $documentRestrictionAssignRepo)
    {
        $this->documentRestrictionAssignRepository = $documentRestrictionAssignRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/documentRestrictionAssigns",
     *      summary="Get a listing of the DocumentRestrictionAssigns.",
     *      tags={"DocumentRestrictionAssign"},
     *      description="Get all DocumentRestrictionAssigns",
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
     *                  @SWG\Items(ref="#/definitions/DocumentRestrictionAssign")
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
        $this->documentRestrictionAssignRepository->pushCriteria(new RequestCriteria($request));
        $this->documentRestrictionAssignRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentRestrictionAssigns = $this->documentRestrictionAssignRepository->all();

        return $this->sendResponse($documentRestrictionAssigns->toArray(), trans('custom.document_restriction_assigns_retrieved_successfull'));
    }

    /**
     * @param CreateDocumentRestrictionAssignAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/documentRestrictionAssigns",
     *      summary="Store a newly created DocumentRestrictionAssign in storage",
     *      tags={"DocumentRestrictionAssign"},
     *      description="Store DocumentRestrictionAssign",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DocumentRestrictionAssign that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DocumentRestrictionAssign")
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
     *                  ref="#/definitions/DocumentRestrictionAssign"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentRestrictionAssignAPIRequest $request)
    {
        $input = $request->all();

        $documentRestrictionAssigns = $this->documentRestrictionAssignRepository->create($input);

        return $this->sendResponse($documentRestrictionAssigns->toArray(), trans('custom.document_restriction_assign_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/documentRestrictionAssigns/{id}",
     *      summary="Display the specified DocumentRestrictionAssign",
     *      tags={"DocumentRestrictionAssign"},
     *      description="Get DocumentRestrictionAssign",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentRestrictionAssign",
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
     *                  ref="#/definitions/DocumentRestrictionAssign"
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
        /** @var DocumentRestrictionAssign $documentRestrictionAssign */
        $documentRestrictionAssign = $this->documentRestrictionAssignRepository->findWithoutFail($id);

        if (empty($documentRestrictionAssign)) {
            return $this->sendError(trans('custom.document_restriction_assign_not_found'));
        }

        return $this->sendResponse($documentRestrictionAssign->toArray(), trans('custom.document_restriction_assign_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateDocumentRestrictionAssignAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/documentRestrictionAssigns/{id}",
     *      summary="Update the specified DocumentRestrictionAssign in storage",
     *      tags={"DocumentRestrictionAssign"},
     *      description="Update DocumentRestrictionAssign",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentRestrictionAssign",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DocumentRestrictionAssign that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DocumentRestrictionAssign")
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
     *                  ref="#/definitions/DocumentRestrictionAssign"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentRestrictionAssignAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentRestrictionAssign $documentRestrictionAssign */
        $documentRestrictionAssign = $this->documentRestrictionAssignRepository->findWithoutFail($id);

        if (empty($documentRestrictionAssign)) {
            return $this->sendError(trans('custom.document_restriction_assign_not_found'));
        }

        $documentRestrictionAssign = $this->documentRestrictionAssignRepository->update($input, $id);

        return $this->sendResponse($documentRestrictionAssign->toArray(), trans('custom.documentrestrictionassign_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/documentRestrictionAssigns/{id}",
     *      summary="Remove the specified DocumentRestrictionAssign from storage",
     *      tags={"DocumentRestrictionAssign"},
     *      description="Delete DocumentRestrictionAssign",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentRestrictionAssign",
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
        /** @var DocumentRestrictionAssign $documentRestrictionAssign */
        $documentRestrictionAssign = $this->documentRestrictionAssignRepository->findWithoutFail($id);

        if (empty($documentRestrictionAssign)) {
            return $this->sendError(trans('custom.document_restriction_assign_not_found'));
        }

        $documentRestrictionAssign->delete();

        return $this->sendResponse($id, trans('custom.document_restriction_assign_deleted_successfully'));
    }

    public function checkRestrictionByPolicy(Request $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'companySystemID' => 'required',
            'documentRestrictionPolicyID' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $id = Auth::id();
        $user = User::with(['employee'])->find($id);
        $empId = $user->employee['employeeSystemID'];
        $permission = false;
        $userGroup = EmployeeNavigation::where('employeeSystemID',$empId)
                                        ->where('companyID',$request['companySystemID'])
                                        ->first();

        if(empty($userGroup)){
            return $this->sendResponse($permission, trans('custom.restriction_assign_permission_retrieve_successfull'));
        }

        $userGroupID = $userGroup->userGroupID;

        $checkCount = DocumentRestrictionAssign::where('companySystemID',$input['companySystemID'])
                                               ->where('documentRestrictionPolicyID',$input['documentRestrictionPolicyID'])
                                                ->where('userGroupID',$userGroupID)
                                               ->count();


        if($checkCount > 0){
            $permission = true;
        }

        return $this->sendResponse($permission, trans('custom.restriction_assign_permission_retrieve_successfull'));
    }
}
