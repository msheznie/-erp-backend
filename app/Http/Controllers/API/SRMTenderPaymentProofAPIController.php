<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSRMTenderPaymentProofAPIRequest;
use App\Http\Requests\API\UpdateSRMTenderPaymentProofAPIRequest;
use App\Models\SRMTenderPaymentProof;
use App\Repositories\SRMTenderPaymentProofRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SRMTenderPaymentProofController
 * @package App\Http\Controllers\API
 */

class SRMTenderPaymentProofAPIController extends AppBaseController
{
    /** @var  SRMTenderPaymentProofRepository */
    private $sRMTenderPaymentProofRepository;

    public function __construct(SRMTenderPaymentProofRepository $sRMTenderPaymentProofRepo)
    {
        $this->sRMTenderPaymentProofRepository = $sRMTenderPaymentProofRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/sRMTenderPaymentProofs",
     *      summary="getSRMTenderPaymentProofList",
     *      tags={"SRMTenderPaymentProof"},
     *      description="Get all SRMTenderPaymentProofs",
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
     *                  @OA\Items(ref="#/definitions/SRMTenderPaymentProof")
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
        $this->sRMTenderPaymentProofRepository->pushCriteria(new RequestCriteria($request));
        $this->sRMTenderPaymentProofRepository->pushCriteria(new LimitOffsetCriteria($request));
        $sRMTenderPaymentProofs = $this->sRMTenderPaymentProofRepository->all();

        return $this->sendResponse($sRMTenderPaymentProofs->toArray(), 'S R M Tender Payment Proofs retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/sRMTenderPaymentProofs",
     *      summary="createSRMTenderPaymentProof",
     *      tags={"SRMTenderPaymentProof"},
     *      description="Create SRMTenderPaymentProof",
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
     *                  ref="#/definitions/SRMTenderPaymentProof"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSRMTenderPaymentProofAPIRequest $request)
    {
        $input = $request->all();

        $sRMTenderPaymentProof = $this->sRMTenderPaymentProofRepository->create($input);

        return $this->sendResponse($sRMTenderPaymentProof->toArray(), 'S R M Tender Payment Proof saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/sRMTenderPaymentProofs/{id}",
     *      summary="getSRMTenderPaymentProofItem",
     *      tags={"SRMTenderPaymentProof"},
     *      description="Get SRMTenderPaymentProof",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMTenderPaymentProof",
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
     *                  ref="#/definitions/SRMTenderPaymentProof"
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
        /** @var SRMTenderPaymentProof $sRMTenderPaymentProof */
        $sRMTenderPaymentProof = $this->sRMTenderPaymentProofRepository->findWithoutFail($id);

        if (empty($sRMTenderPaymentProof)) {
            return $this->sendError('S R M Tender Payment Proof not found');
        }

        return $this->sendResponse($sRMTenderPaymentProof->toArray(), 'S R M Tender Payment Proof retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/sRMTenderPaymentProofs/{id}",
     *      summary="updateSRMTenderPaymentProof",
     *      tags={"SRMTenderPaymentProof"},
     *      description="Update SRMTenderPaymentProof",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMTenderPaymentProof",
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
     *                  ref="#/definitions/SRMTenderPaymentProof"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSRMTenderPaymentProofAPIRequest $request)
    {
        $input = $request->all();

        /** @var SRMTenderPaymentProof $sRMTenderPaymentProof */
        $sRMTenderPaymentProof = $this->sRMTenderPaymentProofRepository->findWithoutFail($id);

        if (empty($sRMTenderPaymentProof)) {
            return $this->sendError('S R M Tender Payment Proof not found');
        }

        $sRMTenderPaymentProof = $this->sRMTenderPaymentProofRepository->update($input, $id);

        return $this->sendResponse($sRMTenderPaymentProof->toArray(), 'SRMTenderPaymentProof updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/sRMTenderPaymentProofs/{id}",
     *      summary="deleteSRMTenderPaymentProof",
     *      tags={"SRMTenderPaymentProof"},
     *      description="Delete SRMTenderPaymentProof",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SRMTenderPaymentProof",
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
        /** @var SRMTenderPaymentProof $sRMTenderPaymentProof */
        $sRMTenderPaymentProof = $this->sRMTenderPaymentProofRepository->findWithoutFail($id);

        if (empty($sRMTenderPaymentProof)) {
            return $this->sendError('S R M Tender Payment Proof not found');
        }

        $sRMTenderPaymentProof->delete();

        return $this->sendSuccess('S R M Tender Payment Proof deleted successfully');
    }
}
