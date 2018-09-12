<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAdvancePaymentDetailsAPIRequest;
use App\Http\Requests\API\UpdateAdvancePaymentDetailsAPIRequest;
use App\Models\AdvancePaymentDetails;
use App\Repositories\AdvancePaymentDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AdvancePaymentDetailsController
 * @package App\Http\Controllers\API
 */
class AdvancePaymentDetailsAPIController extends AppBaseController
{
    /** @var  AdvancePaymentDetailsRepository */
    private $advancePaymentDetailsRepository;

    public function __construct(AdvancePaymentDetailsRepository $advancePaymentDetailsRepo)
    {
        $this->advancePaymentDetailsRepository = $advancePaymentDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/advancePaymentDetails",
     *      summary="Get a listing of the AdvancePaymentDetails.",
     *      tags={"AdvancePaymentDetails"},
     *      description="Get all AdvancePaymentDetails",
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
     *                  @SWG\Items(ref="#/definitions/AdvancePaymentDetails")
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
        $this->advancePaymentDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->advancePaymentDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->all();

        return $this->sendResponse($advancePaymentDetails->toArray(), 'Advance Payment Details retrieved successfully');
    }

    /**
     * @param CreateAdvancePaymentDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/advancePaymentDetails",
     *      summary="Store a newly created AdvancePaymentDetails in storage",
     *      tags={"AdvancePaymentDetails"},
     *      description="Store AdvancePaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AdvancePaymentDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AdvancePaymentDetails")
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
     *                  ref="#/definitions/AdvancePaymentDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAdvancePaymentDetailsAPIRequest $request)
    {
        $input = $request->all();

        $advancePaymentDetails = $this->advancePaymentDetailsRepository->create($input);

        return $this->sendResponse($advancePaymentDetails->toArray(), 'Advance Payment Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/advancePaymentDetails/{id}",
     *      summary="Display the specified AdvancePaymentDetails",
     *      tags={"AdvancePaymentDetails"},
     *      description="Get AdvancePaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AdvancePaymentDetails",
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
     *                  ref="#/definitions/AdvancePaymentDetails"
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
        /** @var AdvancePaymentDetails $advancePaymentDetails */
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->findWithoutFail($id);

        if (empty($advancePaymentDetails)) {
            return $this->sendError('Advance Payment Details not found');
        }

        return $this->sendResponse($advancePaymentDetails->toArray(), 'Advance Payment Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAdvancePaymentDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/advancePaymentDetails/{id}",
     *      summary="Update the specified AdvancePaymentDetails in storage",
     *      tags={"AdvancePaymentDetails"},
     *      description="Update AdvancePaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AdvancePaymentDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AdvancePaymentDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AdvancePaymentDetails")
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
     *                  ref="#/definitions/AdvancePaymentDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAdvancePaymentDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var AdvancePaymentDetails $advancePaymentDetails */
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->findWithoutFail($id);

        if (empty($advancePaymentDetails)) {
            return $this->sendError('Advance Payment Details not found');
        }

        $advancePaymentDetails = $this->advancePaymentDetailsRepository->update($input, $id);

        return $this->sendResponse($advancePaymentDetails->toArray(), 'AdvancePaymentDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/advancePaymentDetails/{id}",
     *      summary="Remove the specified AdvancePaymentDetails from storage",
     *      tags={"AdvancePaymentDetails"},
     *      description="Delete AdvancePaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AdvancePaymentDetails",
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
        /** @var AdvancePaymentDetails $advancePaymentDetails */
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->findWithoutFail($id);

        if (empty($advancePaymentDetails)) {
            return $this->sendError('Advance Payment Details not found');
        }

        $advancePaymentDetails->delete();

        return $this->sendResponse($id, 'Advance Payment Details deleted successfully');
    }

    public function getADVPaymentDetails(Request $request)
    {
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->findWhere(['PayMasterAutoId' => $request->payMasterAutoId]);
        return $this->sendResponse($advancePaymentDetails, 'Payment details saved successfully');
    }
}
