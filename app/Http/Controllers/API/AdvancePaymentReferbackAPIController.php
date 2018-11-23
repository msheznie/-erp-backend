<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAdvancePaymentReferbackAPIRequest;
use App\Http\Requests\API\UpdateAdvancePaymentReferbackAPIRequest;
use App\Models\AdvancePaymentReferback;
use App\Repositories\AdvancePaymentReferbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AdvancePaymentReferbackController
 * @package App\Http\Controllers\API
 */

class AdvancePaymentReferbackAPIController extends AppBaseController
{
    /** @var  AdvancePaymentReferbackRepository */
    private $advancePaymentReferbackRepository;

    public function __construct(AdvancePaymentReferbackRepository $advancePaymentReferbackRepo)
    {
        $this->advancePaymentReferbackRepository = $advancePaymentReferbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/advancePaymentReferbacks",
     *      summary="Get a listing of the AdvancePaymentReferbacks.",
     *      tags={"AdvancePaymentReferback"},
     *      description="Get all AdvancePaymentReferbacks",
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
     *                  @SWG\Items(ref="#/definitions/AdvancePaymentReferback")
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
        $this->advancePaymentReferbackRepository->pushCriteria(new RequestCriteria($request));
        $this->advancePaymentReferbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $advancePaymentReferbacks = $this->advancePaymentReferbackRepository->all();

        return $this->sendResponse($advancePaymentReferbacks->toArray(), 'Advance Payment Referbacks retrieved successfully');
    }

    /**
     * @param CreateAdvancePaymentReferbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/advancePaymentReferbacks",
     *      summary="Store a newly created AdvancePaymentReferback in storage",
     *      tags={"AdvancePaymentReferback"},
     *      description="Store AdvancePaymentReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AdvancePaymentReferback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AdvancePaymentReferback")
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
     *                  ref="#/definitions/AdvancePaymentReferback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAdvancePaymentReferbackAPIRequest $request)
    {
        $input = $request->all();

        $advancePaymentReferbacks = $this->advancePaymentReferbackRepository->create($input);

        return $this->sendResponse($advancePaymentReferbacks->toArray(), 'Advance Payment Referback saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/advancePaymentReferbacks/{id}",
     *      summary="Display the specified AdvancePaymentReferback",
     *      tags={"AdvancePaymentReferback"},
     *      description="Get AdvancePaymentReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AdvancePaymentReferback",
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
     *                  ref="#/definitions/AdvancePaymentReferback"
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
        /** @var AdvancePaymentReferback $advancePaymentReferback */
        $advancePaymentReferback = $this->advancePaymentReferbackRepository->findWithoutFail($id);

        if (empty($advancePaymentReferback)) {
            return $this->sendError('Advance Payment Referback not found');
        }

        return $this->sendResponse($advancePaymentReferback->toArray(), 'Advance Payment Referback retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAdvancePaymentReferbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/advancePaymentReferbacks/{id}",
     *      summary="Update the specified AdvancePaymentReferback in storage",
     *      tags={"AdvancePaymentReferback"},
     *      description="Update AdvancePaymentReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AdvancePaymentReferback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AdvancePaymentReferback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AdvancePaymentReferback")
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
     *                  ref="#/definitions/AdvancePaymentReferback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAdvancePaymentReferbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var AdvancePaymentReferback $advancePaymentReferback */
        $advancePaymentReferback = $this->advancePaymentReferbackRepository->findWithoutFail($id);

        if (empty($advancePaymentReferback)) {
            return $this->sendError('Advance Payment Referback not found');
        }

        $advancePaymentReferback = $this->advancePaymentReferbackRepository->update($input, $id);

        return $this->sendResponse($advancePaymentReferback->toArray(), 'AdvancePaymentReferback updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/advancePaymentReferbacks/{id}",
     *      summary="Remove the specified AdvancePaymentReferback from storage",
     *      tags={"AdvancePaymentReferback"},
     *      description="Delete AdvancePaymentReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AdvancePaymentReferback",
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
        /** @var AdvancePaymentReferback $advancePaymentReferback */
        $advancePaymentReferback = $this->advancePaymentReferbackRepository->findWithoutFail($id);

        if (empty($advancePaymentReferback)) {
            return $this->sendError('Advance Payment Referback not found');
        }

        $advancePaymentReferback->delete();

        return $this->sendResponse($id, 'Advance Payment Referback deleted successfully');
    }
}
