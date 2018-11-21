<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDirectPaymentReferbackAPIRequest;
use App\Http\Requests\API\UpdateDirectPaymentReferbackAPIRequest;
use App\Models\DirectPaymentReferback;
use App\Repositories\DirectPaymentReferbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DirectPaymentReferbackController
 * @package App\Http\Controllers\API
 */

class DirectPaymentReferbackAPIController extends AppBaseController
{
    /** @var  DirectPaymentReferbackRepository */
    private $directPaymentReferbackRepository;

    public function __construct(DirectPaymentReferbackRepository $directPaymentReferbackRepo)
    {
        $this->directPaymentReferbackRepository = $directPaymentReferbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/directPaymentReferbacks",
     *      summary="Get a listing of the DirectPaymentReferbacks.",
     *      tags={"DirectPaymentReferback"},
     *      description="Get all DirectPaymentReferbacks",
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
     *                  @SWG\Items(ref="#/definitions/DirectPaymentReferback")
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
        $this->directPaymentReferbackRepository->pushCriteria(new RequestCriteria($request));
        $this->directPaymentReferbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $directPaymentReferbacks = $this->directPaymentReferbackRepository->all();

        return $this->sendResponse($directPaymentReferbacks->toArray(), 'Direct Payment Referbacks retrieved successfully');
    }

    /**
     * @param CreateDirectPaymentReferbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/directPaymentReferbacks",
     *      summary="Store a newly created DirectPaymentReferback in storage",
     *      tags={"DirectPaymentReferback"},
     *      description="Store DirectPaymentReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectPaymentReferback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectPaymentReferback")
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
     *                  ref="#/definitions/DirectPaymentReferback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDirectPaymentReferbackAPIRequest $request)
    {
        $input = $request->all();

        $directPaymentReferbacks = $this->directPaymentReferbackRepository->create($input);

        return $this->sendResponse($directPaymentReferbacks->toArray(), 'Direct Payment Referback saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/directPaymentReferbacks/{id}",
     *      summary="Display the specified DirectPaymentReferback",
     *      tags={"DirectPaymentReferback"},
     *      description="Get DirectPaymentReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectPaymentReferback",
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
     *                  ref="#/definitions/DirectPaymentReferback"
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
        /** @var DirectPaymentReferback $directPaymentReferback */
        $directPaymentReferback = $this->directPaymentReferbackRepository->findWithoutFail($id);

        if (empty($directPaymentReferback)) {
            return $this->sendError('Direct Payment Referback not found');
        }

        return $this->sendResponse($directPaymentReferback->toArray(), 'Direct Payment Referback retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDirectPaymentReferbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/directPaymentReferbacks/{id}",
     *      summary="Update the specified DirectPaymentReferback in storage",
     *      tags={"DirectPaymentReferback"},
     *      description="Update DirectPaymentReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectPaymentReferback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectPaymentReferback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectPaymentReferback")
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
     *                  ref="#/definitions/DirectPaymentReferback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDirectPaymentReferbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var DirectPaymentReferback $directPaymentReferback */
        $directPaymentReferback = $this->directPaymentReferbackRepository->findWithoutFail($id);

        if (empty($directPaymentReferback)) {
            return $this->sendError('Direct Payment Referback not found');
        }

        $directPaymentReferback = $this->directPaymentReferbackRepository->update($input, $id);

        return $this->sendResponse($directPaymentReferback->toArray(), 'DirectPaymentReferback updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/directPaymentReferbacks/{id}",
     *      summary="Remove the specified DirectPaymentReferback from storage",
     *      tags={"DirectPaymentReferback"},
     *      description="Delete DirectPaymentReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectPaymentReferback",
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
        /** @var DirectPaymentReferback $directPaymentReferback */
        $directPaymentReferback = $this->directPaymentReferbackRepository->findWithoutFail($id);

        if (empty($directPaymentReferback)) {
            return $this->sendError('Direct Payment Referback not found');
        }

        $directPaymentReferback->delete();

        return $this->sendResponse($id, 'Direct Payment Referback deleted successfully');
    }
}
