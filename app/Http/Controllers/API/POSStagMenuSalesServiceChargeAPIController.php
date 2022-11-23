<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSStagMenuSalesServiceChargeAPIRequest;
use App\Http\Requests\API\UpdatePOSStagMenuSalesServiceChargeAPIRequest;
use App\Models\POSStagMenuSalesServiceCharge;
use App\Repositories\POSStagMenuSalesServiceChargeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSStagMenuSalesServiceChargeController
 * @package App\Http\Controllers\API
 */

class POSStagMenuSalesServiceChargeAPIController extends AppBaseController
{
    /** @var  POSStagMenuSalesServiceChargeRepository */
    private $pOSStagMenuSalesServiceChargeRepository;

    public function __construct(POSStagMenuSalesServiceChargeRepository $pOSStagMenuSalesServiceChargeRepo)
    {
        $this->pOSStagMenuSalesServiceChargeRepository = $pOSStagMenuSalesServiceChargeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSStagMenuSalesServiceCharges",
     *      summary="Get a listing of the POSStagMenuSalesServiceCharges.",
     *      tags={"POSStagMenuSalesServiceCharge"},
     *      description="Get all POSStagMenuSalesServiceCharges",
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
     *                  @SWG\Items(ref="#/definitions/POSStagMenuSalesServiceCharge")
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
        $this->pOSStagMenuSalesServiceChargeRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSStagMenuSalesServiceChargeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSStagMenuSalesServiceCharges = $this->pOSStagMenuSalesServiceChargeRepository->all();

        return $this->sendResponse($pOSStagMenuSalesServiceCharges->toArray(), 'P O S Stag Menu Sales Service Charges retrieved successfully');
    }

    /**
     * @param CreatePOSStagMenuSalesServiceChargeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSStagMenuSalesServiceCharges",
     *      summary="Store a newly created POSStagMenuSalesServiceCharge in storage",
     *      tags={"POSStagMenuSalesServiceCharge"},
     *      description="Store POSStagMenuSalesServiceCharge",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSStagMenuSalesServiceCharge that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSStagMenuSalesServiceCharge")
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
     *                  ref="#/definitions/POSStagMenuSalesServiceCharge"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSStagMenuSalesServiceChargeAPIRequest $request)
    {
        $input = $request->all();

        $pOSStagMenuSalesServiceCharge = $this->pOSStagMenuSalesServiceChargeRepository->create($input);

        return $this->sendResponse($pOSStagMenuSalesServiceCharge->toArray(), 'P O S Stag Menu Sales Service Charge saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSStagMenuSalesServiceCharges/{id}",
     *      summary="Display the specified POSStagMenuSalesServiceCharge",
     *      tags={"POSStagMenuSalesServiceCharge"},
     *      description="Get POSStagMenuSalesServiceCharge",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagMenuSalesServiceCharge",
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
     *                  ref="#/definitions/POSStagMenuSalesServiceCharge"
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
        /** @var POSStagMenuSalesServiceCharge $pOSStagMenuSalesServiceCharge */
        $pOSStagMenuSalesServiceCharge = $this->pOSStagMenuSalesServiceChargeRepository->findWithoutFail($id);

        if (empty($pOSStagMenuSalesServiceCharge)) {
            return $this->sendError('P O S Stag Menu Sales Service Charge not found');
        }

        return $this->sendResponse($pOSStagMenuSalesServiceCharge->toArray(), 'P O S Stag Menu Sales Service Charge retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePOSStagMenuSalesServiceChargeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSStagMenuSalesServiceCharges/{id}",
     *      summary="Update the specified POSStagMenuSalesServiceCharge in storage",
     *      tags={"POSStagMenuSalesServiceCharge"},
     *      description="Update POSStagMenuSalesServiceCharge",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagMenuSalesServiceCharge",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSStagMenuSalesServiceCharge that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSStagMenuSalesServiceCharge")
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
     *                  ref="#/definitions/POSStagMenuSalesServiceCharge"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSStagMenuSalesServiceChargeAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSStagMenuSalesServiceCharge $pOSStagMenuSalesServiceCharge */
        $pOSStagMenuSalesServiceCharge = $this->pOSStagMenuSalesServiceChargeRepository->findWithoutFail($id);

        if (empty($pOSStagMenuSalesServiceCharge)) {
            return $this->sendError('P O S Stag Menu Sales Service Charge not found');
        }

        $pOSStagMenuSalesServiceCharge = $this->pOSStagMenuSalesServiceChargeRepository->update($input, $id);

        return $this->sendResponse($pOSStagMenuSalesServiceCharge->toArray(), 'POSStagMenuSalesServiceCharge updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSStagMenuSalesServiceCharges/{id}",
     *      summary="Remove the specified POSStagMenuSalesServiceCharge from storage",
     *      tags={"POSStagMenuSalesServiceCharge"},
     *      description="Delete POSStagMenuSalesServiceCharge",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagMenuSalesServiceCharge",
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
        /** @var POSStagMenuSalesServiceCharge $pOSStagMenuSalesServiceCharge */
        $pOSStagMenuSalesServiceCharge = $this->pOSStagMenuSalesServiceChargeRepository->findWithoutFail($id);

        if (empty($pOSStagMenuSalesServiceCharge)) {
            return $this->sendError('P O S Stag Menu Sales Service Charge not found');
        }

        $pOSStagMenuSalesServiceCharge->delete();

        return $this->sendSuccess('P O S Stag Menu Sales Service Charge deleted successfully');
    }
}
