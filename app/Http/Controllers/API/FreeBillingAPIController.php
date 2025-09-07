<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFreeBillingAPIRequest;
use App\Http\Requests\API\UpdateFreeBillingAPIRequest;
use App\Models\FreeBilling;
use App\Repositories\FreeBillingRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class FreeBillingController
 * @package App\Http\Controllers\API
 */

class FreeBillingAPIController extends AppBaseController
{
    /** @var  FreeBillingRepository */
    private $freeBillingRepository;

    public function __construct(FreeBillingRepository $freeBillingRepo)
    {
        $this->freeBillingRepository = $freeBillingRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/freeBillings",
     *      summary="Get a listing of the FreeBillings.",
     *      tags={"FreeBilling"},
     *      description="Get all FreeBillings",
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
     *                  @SWG\Items(ref="#/definitions/FreeBilling")
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
        $this->freeBillingRepository->pushCriteria(new RequestCriteria($request));
        $this->freeBillingRepository->pushCriteria(new LimitOffsetCriteria($request));
        $freeBillings = $this->freeBillingRepository->all();

        return $this->sendResponse($freeBillings->toArray(), trans('custom.free_billings_retrieved_successfully'));
    }

    /**
     * @param CreateFreeBillingAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/freeBillings",
     *      summary="Store a newly created FreeBilling in storage",
     *      tags={"FreeBilling"},
     *      description="Store FreeBilling",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FreeBilling that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FreeBilling")
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
     *                  ref="#/definitions/FreeBilling"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFreeBillingAPIRequest $request)
    {
        $input = $request->all();

        $freeBillings = $this->freeBillingRepository->create($input);

        return $this->sendResponse($freeBillings->toArray(), trans('custom.free_billing_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/freeBillings/{id}",
     *      summary="Display the specified FreeBilling",
     *      tags={"FreeBilling"},
     *      description="Get FreeBilling",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FreeBilling",
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
     *                  ref="#/definitions/FreeBilling"
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
        /** @var FreeBilling $freeBilling */
        $freeBilling = $this->freeBillingRepository->findWithoutFail($id);

        if (empty($freeBilling)) {
            return $this->sendError(trans('custom.free_billing_not_found'));
        }

        return $this->sendResponse($freeBilling->toArray(), trans('custom.free_billing_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateFreeBillingAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/freeBillings/{id}",
     *      summary="Update the specified FreeBilling in storage",
     *      tags={"FreeBilling"},
     *      description="Update FreeBilling",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FreeBilling",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FreeBilling that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FreeBilling")
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
     *                  ref="#/definitions/FreeBilling"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFreeBillingAPIRequest $request)
    {
        $input = $request->all();

        /** @var FreeBilling $freeBilling */
        $freeBilling = $this->freeBillingRepository->findWithoutFail($id);

        if (empty($freeBilling)) {
            return $this->sendError(trans('custom.free_billing_not_found'));
        }

        $freeBilling = $this->freeBillingRepository->update($input, $id);

        return $this->sendResponse($freeBilling->toArray(), trans('custom.freebilling_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/freeBillings/{id}",
     *      summary="Remove the specified FreeBilling from storage",
     *      tags={"FreeBilling"},
     *      description="Delete FreeBilling",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FreeBilling",
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
        /** @var FreeBilling $freeBilling */
        $freeBilling = $this->freeBillingRepository->findWithoutFail($id);

        if (empty($freeBilling)) {
            return $this->sendError(trans('custom.free_billing_not_found'));
        }

        $freeBilling->delete();

        return $this->sendResponse($id, trans('custom.free_billing_deleted_successfully'));
    }
}
