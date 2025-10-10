<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderMasterReferredAPIRequest;
use App\Http\Requests\API\UpdateTenderMasterReferredAPIRequest;
use App\Models\TenderMasterReferred;
use App\Repositories\TenderMasterReferredRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderMasterReferredController
 * @package App\Http\Controllers\API
 */

class TenderMasterReferredAPIController extends AppBaseController
{
    /** @var  TenderMasterReferredRepository */
    private $tenderMasterReferredRepository;

    public function __construct(TenderMasterReferredRepository $tenderMasterReferredRepo)
    {
        $this->tenderMasterReferredRepository = $tenderMasterReferredRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderMasterReferreds",
     *      summary="getTenderMasterReferredList",
     *      tags={"TenderMasterReferred"},
     *      description="Get all TenderMasterReferreds",
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
     *                  @OA\Items(ref="#/definitions/TenderMasterReferred")
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
        $this->tenderMasterReferredRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderMasterReferredRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderMasterReferreds = $this->tenderMasterReferredRepository->all();

        return $this->sendResponse($tenderMasterReferreds->toArray(), trans('custom.tender_master_referreds_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/tenderMasterReferreds",
     *      summary="createTenderMasterReferred",
     *      tags={"TenderMasterReferred"},
     *      description="Create TenderMasterReferred",
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
     *                  ref="#/definitions/TenderMasterReferred"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderMasterReferredAPIRequest $request)
    {
        $input = $request->all();

        $tenderMasterReferred = $this->tenderMasterReferredRepository->create($input);

        return $this->sendResponse($tenderMasterReferred->toArray(), trans('custom.tender_master_referred_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/tenderMasterReferreds/{id}",
     *      summary="getTenderMasterReferredItem",
     *      tags={"TenderMasterReferred"},
     *      description="Get TenderMasterReferred",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderMasterReferred",
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
     *                  ref="#/definitions/TenderMasterReferred"
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
        /** @var TenderMasterReferred $tenderMasterReferred */
        $tenderMasterReferred = $this->tenderMasterReferredRepository->findWithoutFail($id);

        if (empty($tenderMasterReferred)) {
            return $this->sendError(trans('custom.tender_master_referred_not_found'));
        }

        return $this->sendResponse($tenderMasterReferred->toArray(), trans('custom.tender_master_referred_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/tenderMasterReferreds/{id}",
     *      summary="updateTenderMasterReferred",
     *      tags={"TenderMasterReferred"},
     *      description="Update TenderMasterReferred",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderMasterReferred",
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
     *                  ref="#/definitions/TenderMasterReferred"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderMasterReferredAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderMasterReferred $tenderMasterReferred */
        $tenderMasterReferred = $this->tenderMasterReferredRepository->findWithoutFail($id);

        if (empty($tenderMasterReferred)) {
            return $this->sendError(trans('custom.tender_master_referred_not_found'));
        }

        $tenderMasterReferred = $this->tenderMasterReferredRepository->update($input, $id);

        return $this->sendResponse($tenderMasterReferred->toArray(), trans('custom.tendermasterreferred_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/tenderMasterReferreds/{id}",
     *      summary="deleteTenderMasterReferred",
     *      tags={"TenderMasterReferred"},
     *      description="Delete TenderMasterReferred",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of TenderMasterReferred",
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
        /** @var TenderMasterReferred $tenderMasterReferred */
        $tenderMasterReferred = $this->tenderMasterReferredRepository->findWithoutFail($id);

        if (empty($tenderMasterReferred)) {
            return $this->sendError(trans('custom.tender_master_referred_not_found'));
        }

        $tenderMasterReferred->delete();

        return $this->sendSuccess('Tender Master Referred deleted successfully');
    }
}
