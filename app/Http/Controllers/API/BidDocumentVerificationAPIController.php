<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBidDocumentVerificationAPIRequest;
use App\Http\Requests\API\UpdateBidDocumentVerificationAPIRequest;
use App\Models\BidDocumentVerification;
use App\Repositories\BidDocumentVerificationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BidDocumentVerificationController
 * @package App\Http\Controllers\API
 */

class BidDocumentVerificationAPIController extends AppBaseController
{
    /** @var  BidDocumentVerificationRepository */
    private $bidDocumentVerificationRepository;

    public function __construct(BidDocumentVerificationRepository $bidDocumentVerificationRepo)
    {
        $this->bidDocumentVerificationRepository = $bidDocumentVerificationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/bidDocumentVerifications",
     *      summary="getBidDocumentVerificationList",
     *      tags={"BidDocumentVerification"},
     *      description="Get all BidDocumentVerifications",
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
     *                  @OA\Items(ref="#/definitions/BidDocumentVerification")
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
        $this->bidDocumentVerificationRepository->pushCriteria(new RequestCriteria($request));
        $this->bidDocumentVerificationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bidDocumentVerifications = $this->bidDocumentVerificationRepository->all();

        return $this->sendResponse($bidDocumentVerifications->toArray(), trans('custom.bid_document_verifications_retrieved_successfully'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/bidDocumentVerifications",
     *      summary="createBidDocumentVerification",
     *      tags={"BidDocumentVerification"},
     *      description="Create BidDocumentVerification",
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
     *                  ref="#/definitions/BidDocumentVerification"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBidDocumentVerificationAPIRequest $request)
    {
        $input = $request->all();

        $bidDocumentVerification = $this->bidDocumentVerificationRepository->create($input);

        return $this->sendResponse($bidDocumentVerification->toArray(), trans('custom.bid_document_verification_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/bidDocumentVerifications/{id}",
     *      summary="getBidDocumentVerificationItem",
     *      tags={"BidDocumentVerification"},
     *      description="Get BidDocumentVerification",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BidDocumentVerification",
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
     *                  ref="#/definitions/BidDocumentVerification"
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
        /** @var BidDocumentVerification $bidDocumentVerification */
        $bidDocumentVerification = $this->bidDocumentVerificationRepository->findWithoutFail($id);

        if (empty($bidDocumentVerification)) {
            return $this->sendError(trans('custom.bid_document_verification_not_found'));
        }

        return $this->sendResponse($bidDocumentVerification->toArray(), trans('custom.bid_document_verification_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/bidDocumentVerifications/{id}",
     *      summary="updateBidDocumentVerification",
     *      tags={"BidDocumentVerification"},
     *      description="Update BidDocumentVerification",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BidDocumentVerification",
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
     *                  ref="#/definitions/BidDocumentVerification"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBidDocumentVerificationAPIRequest $request)
    {
        $input = $request->all();

        /** @var BidDocumentVerification $bidDocumentVerification */
        $bidDocumentVerification = $this->bidDocumentVerificationRepository->findWithoutFail($id);

        if (empty($bidDocumentVerification)) {
            return $this->sendError(trans('custom.bid_document_verification_not_found'));
        }

        $bidDocumentVerification = $this->bidDocumentVerificationRepository->update($input, $id);

        return $this->sendResponse($bidDocumentVerification->toArray(), trans('custom.biddocumentverification_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/bidDocumentVerifications/{id}",
     *      summary="deleteBidDocumentVerification",
     *      tags={"BidDocumentVerification"},
     *      description="Delete BidDocumentVerification",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of BidDocumentVerification",
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
        /** @var BidDocumentVerification $bidDocumentVerification */
        $bidDocumentVerification = $this->bidDocumentVerificationRepository->findWithoutFail($id);

        if (empty($bidDocumentVerification)) {
            return $this->sendError(trans('custom.bid_document_verification_not_found'));
        }

        $bidDocumentVerification->delete();

        return $this->sendSuccess('Bid Document Verification deleted successfully');
    }
}
