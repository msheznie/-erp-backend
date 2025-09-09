<?php
/**
 * =============================================
 * -- File Name : DocumentRestrictionPolicyAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Document Restriction Policy
 * -- Author : Mohamed Fayas
 * -- Create date : 14 - December 2018
 * -- Description : This file contains the all CRUD for Document Restriction Policy
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDocumentRestrictionPolicyAPIRequest;
use App\Http\Requests\API\UpdateDocumentRestrictionPolicyAPIRequest;
use App\Models\DocumentRestrictionPolicy;
use App\Repositories\DocumentRestrictionPolicyRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DocumentRestrictionPolicyController
 * @package App\Http\Controllers\API
 */

class DocumentRestrictionPolicyAPIController extends AppBaseController
{
    /** @var  DocumentRestrictionPolicyRepository */
    private $documentRestrictionPolicyRepository;

    public function __construct(DocumentRestrictionPolicyRepository $documentRestrictionPolicyRepo)
    {
        $this->documentRestrictionPolicyRepository = $documentRestrictionPolicyRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/documentRestrictionPolicies",
     *      summary="Get a listing of the DocumentRestrictionPolicies.",
     *      tags={"DocumentRestrictionPolicy"},
     *      description="Get all DocumentRestrictionPolicies",
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
     *                  @SWG\Items(ref="#/definitions/DocumentRestrictionPolicy")
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
        $this->documentRestrictionPolicyRepository->pushCriteria(new RequestCriteria($request));
        $this->documentRestrictionPolicyRepository->pushCriteria(new LimitOffsetCriteria($request));
        $documentRestrictionPolicies = $this->documentRestrictionPolicyRepository->all();

        return $this->sendResponse($documentRestrictionPolicies->toArray(), trans('custom.document_restriction_policies_retrieved_successful'));
    }

    /**
     * @param CreateDocumentRestrictionPolicyAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/documentRestrictionPolicies",
     *      summary="Store a newly created DocumentRestrictionPolicy in storage",
     *      tags={"DocumentRestrictionPolicy"},
     *      description="Store DocumentRestrictionPolicy",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DocumentRestrictionPolicy that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DocumentRestrictionPolicy")
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
     *                  ref="#/definitions/DocumentRestrictionPolicy"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDocumentRestrictionPolicyAPIRequest $request)
    {
        $input = $request->all();

        $documentRestrictionPolicies = $this->documentRestrictionPolicyRepository->create($input);

        return $this->sendResponse($documentRestrictionPolicies->toArray(), trans('custom.document_restriction_policy_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/documentRestrictionPolicies/{id}",
     *      summary="Display the specified DocumentRestrictionPolicy",
     *      tags={"DocumentRestrictionPolicy"},
     *      description="Get DocumentRestrictionPolicy",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentRestrictionPolicy",
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
     *                  ref="#/definitions/DocumentRestrictionPolicy"
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
        /** @var DocumentRestrictionPolicy $documentRestrictionPolicy */
        $documentRestrictionPolicy = $this->documentRestrictionPolicyRepository->findWithoutFail($id);

        if (empty($documentRestrictionPolicy)) {
            return $this->sendError(trans('custom.document_restriction_policy_not_found'));
        }

        return $this->sendResponse($documentRestrictionPolicy->toArray(), trans('custom.document_restriction_policy_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateDocumentRestrictionPolicyAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/documentRestrictionPolicies/{id}",
     *      summary="Update the specified DocumentRestrictionPolicy in storage",
     *      tags={"DocumentRestrictionPolicy"},
     *      description="Update DocumentRestrictionPolicy",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentRestrictionPolicy",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DocumentRestrictionPolicy that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DocumentRestrictionPolicy")
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
     *                  ref="#/definitions/DocumentRestrictionPolicy"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDocumentRestrictionPolicyAPIRequest $request)
    {
        $input = $request->all();

        /** @var DocumentRestrictionPolicy $documentRestrictionPolicy */
        $documentRestrictionPolicy = $this->documentRestrictionPolicyRepository->findWithoutFail($id);

        if (empty($documentRestrictionPolicy)) {
            return $this->sendError(trans('custom.document_restriction_policy_not_found'));
        }

        $documentRestrictionPolicy = $this->documentRestrictionPolicyRepository->update($input, $id);

        return $this->sendResponse($documentRestrictionPolicy->toArray(), trans('custom.documentrestrictionpolicy_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/documentRestrictionPolicies/{id}",
     *      summary="Remove the specified DocumentRestrictionPolicy from storage",
     *      tags={"DocumentRestrictionPolicy"},
     *      description="Delete DocumentRestrictionPolicy",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DocumentRestrictionPolicy",
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
        /** @var DocumentRestrictionPolicy $documentRestrictionPolicy */
        $documentRestrictionPolicy = $this->documentRestrictionPolicyRepository->findWithoutFail($id);

        if (empty($documentRestrictionPolicy)) {
            return $this->sendError(trans('custom.document_restriction_policy_not_found'));
        }

        $documentRestrictionPolicy->delete();

        return $this->sendResponse($id, trans('custom.document_restriction_policy_deleted_successfully'));
    }
}
