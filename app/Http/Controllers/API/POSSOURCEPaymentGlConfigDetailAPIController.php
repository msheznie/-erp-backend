<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSOURCEPaymentGlConfigDetailAPIRequest;
use App\Http\Requests\API\UpdatePOSSOURCEPaymentGlConfigDetailAPIRequest;
use App\Models\POSSOURCEPaymentGlConfigDetail;
use App\Repositories\POSSOURCEPaymentGlConfigDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSOURCEPaymentGlConfigDetailController
 * @package App\Http\Controllers\API
 */

class POSSOURCEPaymentGlConfigDetailAPIController extends AppBaseController
{
    /** @var  POSSOURCEPaymentGlConfigDetailRepository */
    private $pOSSOURCEPaymentGlConfigDetailRepository;

    public function __construct(POSSOURCEPaymentGlConfigDetailRepository $pOSSOURCEPaymentGlConfigDetailRepo)
    {
        $this->pOSSOURCEPaymentGlConfigDetailRepository = $pOSSOURCEPaymentGlConfigDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSOURCEPaymentGlConfigDetails",
     *      summary="Get a listing of the POSSOURCEPaymentGlConfigDetails.",
     *      tags={"POSSOURCEPaymentGlConfigDetail"},
     *      description="Get all POSSOURCEPaymentGlConfigDetails",
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
     *                  @SWG\Items(ref="#/definitions/POSSOURCEPaymentGlConfigDetail")
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
        $this->pOSSOURCEPaymentGlConfigDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSOURCEPaymentGlConfigDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSOURCEPaymentGlConfigDetails = $this->pOSSOURCEPaymentGlConfigDetailRepository->all();

        return $this->sendResponse($pOSSOURCEPaymentGlConfigDetails->toArray(), 'P O S S O U R C E Payment Gl Config Details retrieved successfully');
    }

    /**
     * @param CreatePOSSOURCEPaymentGlConfigDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSOURCEPaymentGlConfigDetails",
     *      summary="Store a newly created POSSOURCEPaymentGlConfigDetail in storage",
     *      tags={"POSSOURCEPaymentGlConfigDetail"},
     *      description="Store POSSOURCEPaymentGlConfigDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSOURCEPaymentGlConfigDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSOURCEPaymentGlConfigDetail")
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
     *                  ref="#/definitions/POSSOURCEPaymentGlConfigDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSOURCEPaymentGlConfigDetailAPIRequest $request)
    {
        $input = $request->all();

        $pOSSOURCEPaymentGlConfigDetail = $this->pOSSOURCEPaymentGlConfigDetailRepository->create($input);

        return $this->sendResponse($pOSSOURCEPaymentGlConfigDetail->toArray(), 'P O S S O U R C E Payment Gl Config Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSOURCEPaymentGlConfigDetails/{id}",
     *      summary="Display the specified POSSOURCEPaymentGlConfigDetail",
     *      tags={"POSSOURCEPaymentGlConfigDetail"},
     *      description="Get POSSOURCEPaymentGlConfigDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCEPaymentGlConfigDetail",
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
     *                  ref="#/definitions/POSSOURCEPaymentGlConfigDetail"
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
        /** @var POSSOURCEPaymentGlConfigDetail $pOSSOURCEPaymentGlConfigDetail */
        $pOSSOURCEPaymentGlConfigDetail = $this->pOSSOURCEPaymentGlConfigDetailRepository->findWithoutFail($id);

        if (empty($pOSSOURCEPaymentGlConfigDetail)) {
            return $this->sendError('P O S S O U R C E Payment Gl Config Detail not found');
        }

        return $this->sendResponse($pOSSOURCEPaymentGlConfigDetail->toArray(), 'P O S S O U R C E Payment Gl Config Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePOSSOURCEPaymentGlConfigDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSOURCEPaymentGlConfigDetails/{id}",
     *      summary="Update the specified POSSOURCEPaymentGlConfigDetail in storage",
     *      tags={"POSSOURCEPaymentGlConfigDetail"},
     *      description="Update POSSOURCEPaymentGlConfigDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCEPaymentGlConfigDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSOURCEPaymentGlConfigDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSOURCEPaymentGlConfigDetail")
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
     *                  ref="#/definitions/POSSOURCEPaymentGlConfigDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSOURCEPaymentGlConfigDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSOURCEPaymentGlConfigDetail $pOSSOURCEPaymentGlConfigDetail */
        $pOSSOURCEPaymentGlConfigDetail = $this->pOSSOURCEPaymentGlConfigDetailRepository->findWithoutFail($id);

        if (empty($pOSSOURCEPaymentGlConfigDetail)) {
            return $this->sendError('P O S S O U R C E Payment Gl Config Detail not found');
        }

        $pOSSOURCEPaymentGlConfigDetail = $this->pOSSOURCEPaymentGlConfigDetailRepository->update($input, $id);

        return $this->sendResponse($pOSSOURCEPaymentGlConfigDetail->toArray(), 'POSSOURCEPaymentGlConfigDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSOURCEPaymentGlConfigDetails/{id}",
     *      summary="Remove the specified POSSOURCEPaymentGlConfigDetail from storage",
     *      tags={"POSSOURCEPaymentGlConfigDetail"},
     *      description="Delete POSSOURCEPaymentGlConfigDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSOURCEPaymentGlConfigDetail",
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
        /** @var POSSOURCEPaymentGlConfigDetail $pOSSOURCEPaymentGlConfigDetail */
        $pOSSOURCEPaymentGlConfigDetail = $this->pOSSOURCEPaymentGlConfigDetailRepository->findWithoutFail($id);

        if (empty($pOSSOURCEPaymentGlConfigDetail)) {
            return $this->sendError('P O S S O U R C E Payment Gl Config Detail not found');
        }

        $pOSSOURCEPaymentGlConfigDetail->delete();

        return $this->sendSuccess('P O S S O U R C E Payment Gl Config Detail deleted successfully');
    }
}
