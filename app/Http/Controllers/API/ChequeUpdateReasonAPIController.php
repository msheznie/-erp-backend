<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateChequeUpdateReasonAPIRequest;
use App\Http\Requests\API\UpdateChequeUpdateReasonAPIRequest;
use App\Models\ChequeUpdateReason;
use App\Repositories\ChequeUpdateReasonRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ChequeUpdateReasonController
 * @package App\Http\Controllers\API
 */

class ChequeUpdateReasonAPIController extends AppBaseController
{
    /** @var  ChequeUpdateReasonRepository */
    private $chequeUpdateReasonRepository;

    public function __construct(ChequeUpdateReasonRepository $chequeUpdateReasonRepo)
    {
        $this->chequeUpdateReasonRepository = $chequeUpdateReasonRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/chequeUpdateReasons",
     *      summary="getChequeUpdateReasonList",
     *      tags={"ChequeUpdateReason"},
     *      description="Get all ChequeUpdateReasons",
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
     *                  @OA\Items(ref="#/definitions/ChequeUpdateReason")
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
        $this->chequeUpdateReasonRepository->pushCriteria(new RequestCriteria($request));
        $this->chequeUpdateReasonRepository->pushCriteria(new LimitOffsetCriteria($request));
        $chequeUpdateReasons = $this->chequeUpdateReasonRepository->all();

        return $this->sendResponse($chequeUpdateReasons->toArray(), trans('custom.cheque_update_reasons_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/chequeUpdateReasons",
     *      summary="createChequeUpdateReason",
     *      tags={"ChequeUpdateReason"},
     *      description="Create ChequeUpdateReason",
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
     *                  ref="#/definitions/ChequeUpdateReason"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateChequeUpdateReasonAPIRequest $request)
    {
        $input = $request->all();

        $chequeUpdateReason = $this->chequeUpdateReasonRepository->create($input);

        return $this->sendResponse($chequeUpdateReason->toArray(), trans('custom.cheque_update_reason_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/chequeUpdateReasons/{id}",
     *      summary="getChequeUpdateReasonItem",
     *      tags={"ChequeUpdateReason"},
     *      description="Get ChequeUpdateReason",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ChequeUpdateReason",
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
     *                  ref="#/definitions/ChequeUpdateReason"
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
        /** @var ChequeUpdateReason $chequeUpdateReason */
        $chequeUpdateReason = $this->chequeUpdateReasonRepository->findWithoutFail($id);

        if (empty($chequeUpdateReason)) {
            return $this->sendError(trans('custom.cheque_update_reason_not_found'));
        }

        return $this->sendResponse($chequeUpdateReason->toArray(), trans('custom.cheque_update_reason_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/chequeUpdateReasons/{id}",
     *      summary="updateChequeUpdateReason",
     *      tags={"ChequeUpdateReason"},
     *      description="Update ChequeUpdateReason",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ChequeUpdateReason",
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
     *                  ref="#/definitions/ChequeUpdateReason"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateChequeUpdateReasonAPIRequest $request)
    {
        $input = $request->all();

        /** @var ChequeUpdateReason $chequeUpdateReason */
        $chequeUpdateReason = $this->chequeUpdateReasonRepository->findWithoutFail($id);

        if (empty($chequeUpdateReason)) {
            return $this->sendError(trans('custom.cheque_update_reason_not_found'));
        }

        $chequeUpdateReason = $this->chequeUpdateReasonRepository->update($input, $id);

        return $this->sendResponse($chequeUpdateReason->toArray(), trans('custom.chequeupdatereason_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/chequeUpdateReasons/{id}",
     *      summary="deleteChequeUpdateReason",
     *      tags={"ChequeUpdateReason"},
     *      description="Delete ChequeUpdateReason",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of ChequeUpdateReason",
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
        /** @var ChequeUpdateReason $chequeUpdateReason */
        $chequeUpdateReason = $this->chequeUpdateReasonRepository->findWithoutFail($id);

        if (empty($chequeUpdateReason)) {
            return $this->sendError(trans('custom.cheque_update_reason_not_found'));
        }

        $chequeUpdateReason->delete();

        return $this->sendSuccess('Cheque Update Reason deleted successfully');
    }
}
