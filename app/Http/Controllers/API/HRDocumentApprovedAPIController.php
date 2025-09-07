<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHRDocumentApprovedAPIRequest;
use App\Http\Requests\API\UpdateHRDocumentApprovedAPIRequest;
use App\Models\HRDocumentApproved;
use App\Repositories\HRDocumentApprovedRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HRDocumentApprovedController
 * @package App\Http\Controllers\API
 */

class HRDocumentApprovedAPIController extends AppBaseController
{
    /** @var  HRDocumentApprovedRepository */
    private $hRDocumentApprovedRepository;

    public function __construct(HRDocumentApprovedRepository $hRDocumentApprovedRepo)
    {
        $this->hRDocumentApprovedRepository = $hRDocumentApprovedRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/hRDocumentApproveds",
     *      summary="getHRDocumentApprovedList",
     *      tags={"HRDocumentApproved"},
     *      description="Get all HRDocumentApproveds",
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
     *                  @OA\Items(ref="#/definitions/HRDocumentApproved")
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
        $this->hRDocumentApprovedRepository->pushCriteria(new RequestCriteria($request));
        $this->hRDocumentApprovedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hRDocumentApproveds = $this->hRDocumentApprovedRepository->all();

        return $this->sendResponse($hRDocumentApproveds->toArray(), trans('custom.h_r_document_approveds_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/hRDocumentApproveds",
     *      summary="createHRDocumentApproved",
     *      tags={"HRDocumentApproved"},
     *      description="Create HRDocumentApproved",
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
     *                  ref="#/definitions/HRDocumentApproved"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHRDocumentApprovedAPIRequest $request)
    {
        $input = $request->all();

        $hRDocumentApproved = $this->hRDocumentApprovedRepository->create($input);

        return $this->sendResponse($hRDocumentApproved->toArray(), trans('custom.h_r_document_approved_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/hRDocumentApproveds/{id}",
     *      summary="getHRDocumentApprovedItem",
     *      tags={"HRDocumentApproved"},
     *      description="Get HRDocumentApproved",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HRDocumentApproved",
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
     *                  ref="#/definitions/HRDocumentApproved"
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
        /** @var HRDocumentApproved $hRDocumentApproved */
        $hRDocumentApproved = $this->hRDocumentApprovedRepository->findWithoutFail($id);

        if (empty($hRDocumentApproved)) {
            return $this->sendError(trans('custom.h_r_document_approved_not_found'));
        }

        return $this->sendResponse($hRDocumentApproved->toArray(), trans('custom.h_r_document_approved_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/hRDocumentApproveds/{id}",
     *      summary="updateHRDocumentApproved",
     *      tags={"HRDocumentApproved"},
     *      description="Update HRDocumentApproved",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HRDocumentApproved",
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
     *                  ref="#/definitions/HRDocumentApproved"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHRDocumentApprovedAPIRequest $request)
    {
        $input = $request->all();

        /** @var HRDocumentApproved $hRDocumentApproved */
        $hRDocumentApproved = $this->hRDocumentApprovedRepository->findWithoutFail($id);

        if (empty($hRDocumentApproved)) {
            return $this->sendError(trans('custom.h_r_document_approved_not_found'));
        }

        $hRDocumentApproved = $this->hRDocumentApprovedRepository->update($input, $id);

        return $this->sendResponse($hRDocumentApproved->toArray(), trans('custom.hrdocumentapproved_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/hRDocumentApproveds/{id}",
     *      summary="deleteHRDocumentApproved",
     *      tags={"HRDocumentApproved"},
     *      description="Delete HRDocumentApproved",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of HRDocumentApproved",
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
        /** @var HRDocumentApproved $hRDocumentApproved */
        $hRDocumentApproved = $this->hRDocumentApprovedRepository->findWithoutFail($id);

        if (empty($hRDocumentApproved)) {
            return $this->sendError(trans('custom.h_r_document_approved_not_found'));
        }

        $hRDocumentApproved->delete();

        return $this->sendSuccess('H R Document Approved deleted successfully');
    }
}
