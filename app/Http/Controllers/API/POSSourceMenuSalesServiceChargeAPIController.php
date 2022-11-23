<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSourceMenuSalesServiceChargeAPIRequest;
use App\Http\Requests\API\UpdatePOSSourceMenuSalesServiceChargeAPIRequest;
use App\Models\POSSourceMenuSalesServiceCharge;
use App\Repositories\POSSourceMenuSalesServiceChargeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSourceMenuSalesServiceChargeController
 * @package App\Http\Controllers\API
 */

class POSSourceMenuSalesServiceChargeAPIController extends AppBaseController
{
    /** @var  POSSourceMenuSalesServiceChargeRepository */
    private $pOSSourceMenuSalesServiceChargeRepository;

    public function __construct(POSSourceMenuSalesServiceChargeRepository $pOSSourceMenuSalesServiceChargeRepo)
    {
        $this->pOSSourceMenuSalesServiceChargeRepository = $pOSSourceMenuSalesServiceChargeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourceMenuSalesServiceCharges",
     *      summary="Get a listing of the POSSourceMenuSalesServiceCharges.",
     *      tags={"POSSourceMenuSalesServiceCharge"},
     *      description="Get all POSSourceMenuSalesServiceCharges",
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
     *                  @SWG\Items(ref="#/definitions/POSSourceMenuSalesServiceCharge")
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
        $this->pOSSourceMenuSalesServiceChargeRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSourceMenuSalesServiceChargeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSourceMenuSalesServiceCharges = $this->pOSSourceMenuSalesServiceChargeRepository->all();

        return $this->sendResponse($pOSSourceMenuSalesServiceCharges->toArray(), 'P O S Source Menu Sales Service Charges retrieved successfully');
    }

    /**
     * @param CreatePOSSourceMenuSalesServiceChargeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSourceMenuSalesServiceCharges",
     *      summary="Store a newly created POSSourceMenuSalesServiceCharge in storage",
     *      tags={"POSSourceMenuSalesServiceCharge"},
     *      description="Store POSSourceMenuSalesServiceCharge",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourceMenuSalesServiceCharge that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourceMenuSalesServiceCharge")
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
     *                  ref="#/definitions/POSSourceMenuSalesServiceCharge"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSourceMenuSalesServiceChargeAPIRequest $request)
    {
        $input = $request->all();

        $pOSSourceMenuSalesServiceCharge = $this->pOSSourceMenuSalesServiceChargeRepository->create($input);

        return $this->sendResponse($pOSSourceMenuSalesServiceCharge->toArray(), 'P O S Source Menu Sales Service Charge saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourceMenuSalesServiceCharges/{id}",
     *      summary="Display the specified POSSourceMenuSalesServiceCharge",
     *      tags={"POSSourceMenuSalesServiceCharge"},
     *      description="Get POSSourceMenuSalesServiceCharge",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceMenuSalesServiceCharge",
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
     *                  ref="#/definitions/POSSourceMenuSalesServiceCharge"
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
        /** @var POSSourceMenuSalesServiceCharge $pOSSourceMenuSalesServiceCharge */
        $pOSSourceMenuSalesServiceCharge = $this->pOSSourceMenuSalesServiceChargeRepository->findWithoutFail($id);

        if (empty($pOSSourceMenuSalesServiceCharge)) {
            return $this->sendError('P O S Source Menu Sales Service Charge not found');
        }

        return $this->sendResponse($pOSSourceMenuSalesServiceCharge->toArray(), 'P O S Source Menu Sales Service Charge retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePOSSourceMenuSalesServiceChargeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSourceMenuSalesServiceCharges/{id}",
     *      summary="Update the specified POSSourceMenuSalesServiceCharge in storage",
     *      tags={"POSSourceMenuSalesServiceCharge"},
     *      description="Update POSSourceMenuSalesServiceCharge",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceMenuSalesServiceCharge",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourceMenuSalesServiceCharge that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourceMenuSalesServiceCharge")
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
     *                  ref="#/definitions/POSSourceMenuSalesServiceCharge"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSourceMenuSalesServiceChargeAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSourceMenuSalesServiceCharge $pOSSourceMenuSalesServiceCharge */
        $pOSSourceMenuSalesServiceCharge = $this->pOSSourceMenuSalesServiceChargeRepository->findWithoutFail($id);

        if (empty($pOSSourceMenuSalesServiceCharge)) {
            return $this->sendError('P O S Source Menu Sales Service Charge not found');
        }

        $pOSSourceMenuSalesServiceCharge = $this->pOSSourceMenuSalesServiceChargeRepository->update($input, $id);

        return $this->sendResponse($pOSSourceMenuSalesServiceCharge->toArray(), 'POSSourceMenuSalesServiceCharge updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSourceMenuSalesServiceCharges/{id}",
     *      summary="Remove the specified POSSourceMenuSalesServiceCharge from storage",
     *      tags={"POSSourceMenuSalesServiceCharge"},
     *      description="Delete POSSourceMenuSalesServiceCharge",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceMenuSalesServiceCharge",
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
        /** @var POSSourceMenuSalesServiceCharge $pOSSourceMenuSalesServiceCharge */
        $pOSSourceMenuSalesServiceCharge = $this->pOSSourceMenuSalesServiceChargeRepository->findWithoutFail($id);

        if (empty($pOSSourceMenuSalesServiceCharge)) {
            return $this->sendError('P O S Source Menu Sales Service Charge not found');
        }

        $pOSSourceMenuSalesServiceCharge->delete();

        return $this->sendSuccess('P O S Source Menu Sales Service Charge deleted successfully');
    }
}
