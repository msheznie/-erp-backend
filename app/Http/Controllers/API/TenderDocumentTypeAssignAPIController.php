<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderDocumentTypeAssignAPIRequest;
use App\Http\Requests\API\UpdateTenderDocumentTypeAssignAPIRequest;
use App\Models\TenderDocumentTypeAssign;
use App\Repositories\TenderDocumentTypeAssignRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderDocumentTypeAssignController
 * @package App\Http\Controllers\API
 */

class TenderDocumentTypeAssignAPIController extends AppBaseController
{
    /** @var  TenderDocumentTypeAssignRepository */
    private $tenderDocumentTypeAssignRepository;

    public function __construct(TenderDocumentTypeAssignRepository $tenderDocumentTypeAssignRepo)
    {
        $this->tenderDocumentTypeAssignRepository = $tenderDocumentTypeAssignRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderDocumentTypeAssigns",
     *      summary="Get a listing of the TenderDocumentTypeAssigns.",
     *      tags={"TenderDocumentTypeAssign"},
     *      description="Get all TenderDocumentTypeAssigns",
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
     *                  @SWG\Items(ref="#/definitions/TenderDocumentTypeAssign")
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
        $this->tenderDocumentTypeAssignRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderDocumentTypeAssignRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderDocumentTypeAssigns = $this->tenderDocumentTypeAssignRepository->all();

        return $this->sendResponse($tenderDocumentTypeAssigns->toArray(), trans('custom.tender_document_type_assigns_retrieved_successfull'));
    }

    /**
     * @param CreateTenderDocumentTypeAssignAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderDocumentTypeAssigns",
     *      summary="Store a newly created TenderDocumentTypeAssign in storage",
     *      tags={"TenderDocumentTypeAssign"},
     *      description="Store TenderDocumentTypeAssign",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderDocumentTypeAssign that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderDocumentTypeAssign")
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
     *                  ref="#/definitions/TenderDocumentTypeAssign"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderDocumentTypeAssignAPIRequest $request)
    {
        $input = $request->all();

        $tenderDocumentTypeAssign = $this->tenderDocumentTypeAssignRepository->create($input);

        return $this->sendResponse($tenderDocumentTypeAssign->toArray(), trans('custom.tender_document_type_assign_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderDocumentTypeAssigns/{id}",
     *      summary="Display the specified TenderDocumentTypeAssign",
     *      tags={"TenderDocumentTypeAssign"},
     *      description="Get TenderDocumentTypeAssign",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderDocumentTypeAssign",
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
     *                  ref="#/definitions/TenderDocumentTypeAssign"
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
        /** @var TenderDocumentTypeAssign $tenderDocumentTypeAssign */
        $tenderDocumentTypeAssign = $this->tenderDocumentTypeAssignRepository->findWithoutFail($id);

        if (empty($tenderDocumentTypeAssign)) {
            return $this->sendError(trans('custom.tender_document_type_assign_not_found'));
        }

        return $this->sendResponse($tenderDocumentTypeAssign->toArray(), trans('custom.tender_document_type_assign_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTenderDocumentTypeAssignAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderDocumentTypeAssigns/{id}",
     *      summary="Update the specified TenderDocumentTypeAssign in storage",
     *      tags={"TenderDocumentTypeAssign"},
     *      description="Update TenderDocumentTypeAssign",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderDocumentTypeAssign",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderDocumentTypeAssign that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderDocumentTypeAssign")
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
     *                  ref="#/definitions/TenderDocumentTypeAssign"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderDocumentTypeAssignAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderDocumentTypeAssign $tenderDocumentTypeAssign */
        $tenderDocumentTypeAssign = $this->tenderDocumentTypeAssignRepository->findWithoutFail($id);

        if (empty($tenderDocumentTypeAssign)) {
            return $this->sendError(trans('custom.tender_document_type_assign_not_found'));
        }

        $tenderDocumentTypeAssign = $this->tenderDocumentTypeAssignRepository->update($input, $id);

        return $this->sendResponse($tenderDocumentTypeAssign->toArray(), trans('custom.tenderdocumenttypeassign_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderDocumentTypeAssigns/{id}",
     *      summary="Remove the specified TenderDocumentTypeAssign from storage",
     *      tags={"TenderDocumentTypeAssign"},
     *      description="Delete TenderDocumentTypeAssign",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderDocumentTypeAssign",
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
        /** @var TenderDocumentTypeAssign $tenderDocumentTypeAssign */
        $tenderDocumentTypeAssign = $this->tenderDocumentTypeAssignRepository->findWithoutFail($id);

        if (empty($tenderDocumentTypeAssign)) {
            return $this->sendError(trans('custom.tender_document_type_assign_not_found'));
        }

        $tenderDocumentTypeAssign->delete();

        return $this->sendSuccess('Tender Document Type Assign deleted successfully');
    }
}
