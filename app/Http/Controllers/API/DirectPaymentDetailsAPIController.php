<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDirectPaymentDetailsAPIRequest;
use App\Http\Requests\API\UpdateDirectPaymentDetailsAPIRequest;
use App\Models\DirectPaymentDetails;
use App\Repositories\DirectPaymentDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DirectPaymentDetailsController
 * @package App\Http\Controllers\API
 */

class DirectPaymentDetailsAPIController extends AppBaseController
{
    /** @var  DirectPaymentDetailsRepository */
    private $directPaymentDetailsRepository;

    public function __construct(DirectPaymentDetailsRepository $directPaymentDetailsRepo)
    {
        $this->directPaymentDetailsRepository = $directPaymentDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/directPaymentDetails",
     *      summary="Get a listing of the DirectPaymentDetails.",
     *      tags={"DirectPaymentDetails"},
     *      description="Get all DirectPaymentDetails",
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
     *                  @SWG\Items(ref="#/definitions/DirectPaymentDetails")
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
        $this->directPaymentDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->directPaymentDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $directPaymentDetails = $this->directPaymentDetailsRepository->all();

        return $this->sendResponse($directPaymentDetails->toArray(), 'Direct Payment Details retrieved successfully');
    }

    /**
     * @param CreateDirectPaymentDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/directPaymentDetails",
     *      summary="Store a newly created DirectPaymentDetails in storage",
     *      tags={"DirectPaymentDetails"},
     *      description="Store DirectPaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectPaymentDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectPaymentDetails")
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
     *                  ref="#/definitions/DirectPaymentDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDirectPaymentDetailsAPIRequest $request)
    {
        $input = $request->all();

        $directPaymentDetails = $this->directPaymentDetailsRepository->create($input);

        return $this->sendResponse($directPaymentDetails->toArray(), 'Direct Payment Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/directPaymentDetails/{id}",
     *      summary="Display the specified DirectPaymentDetails",
     *      tags={"DirectPaymentDetails"},
     *      description="Get DirectPaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectPaymentDetails",
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
     *                  ref="#/definitions/DirectPaymentDetails"
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
        /** @var DirectPaymentDetails $directPaymentDetails */
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($id);

        if (empty($directPaymentDetails)) {
            return $this->sendError('Direct Payment Details not found');
        }

        return $this->sendResponse($directPaymentDetails->toArray(), 'Direct Payment Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDirectPaymentDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/directPaymentDetails/{id}",
     *      summary="Update the specified DirectPaymentDetails in storage",
     *      tags={"DirectPaymentDetails"},
     *      description="Update DirectPaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectPaymentDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectPaymentDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectPaymentDetails")
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
     *                  ref="#/definitions/DirectPaymentDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDirectPaymentDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var DirectPaymentDetails $directPaymentDetails */
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($id);

        if (empty($directPaymentDetails)) {
            return $this->sendError('Direct Payment Details not found');
        }

        $directPaymentDetails = $this->directPaymentDetailsRepository->update($input, $id);

        return $this->sendResponse($directPaymentDetails->toArray(), 'DirectPaymentDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/directPaymentDetails/{id}",
     *      summary="Remove the specified DirectPaymentDetails from storage",
     *      tags={"DirectPaymentDetails"},
     *      description="Delete DirectPaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectPaymentDetails",
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
        /** @var DirectPaymentDetails $directPaymentDetails */
        $directPaymentDetails = $this->directPaymentDetailsRepository->findWithoutFail($id);

        if (empty($directPaymentDetails)) {
            return $this->sendError('Direct Payment Details not found');
        }

        $directPaymentDetails->delete();

        return $this->sendResponse($id, 'Direct Payment Details deleted successfully');
    }
}
