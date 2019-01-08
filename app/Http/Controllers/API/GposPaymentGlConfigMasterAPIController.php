<?php
/**
 * =============================================
 * -- File Name : GposPaymentGlConfigMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  General pos payment gl config master
 * -- Author : Mohamed Fayas
 * -- Create date : 08 - January 2019
 * -- Description : This file contains the all CRUD for general pos payment gl config master
 * -- REVISION HISTORY
 * -- Date: 08 - January 2019 By: Fayas Description: Added new function
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGposPaymentGlConfigMasterAPIRequest;
use App\Http\Requests\API\UpdateGposPaymentGlConfigMasterAPIRequest;
use App\Models\GposPaymentGlConfigMaster;
use App\Repositories\GposPaymentGlConfigMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class GposPaymentGlConfigMasterController
 * @package App\Http\Controllers\API
 */

class GposPaymentGlConfigMasterAPIController extends AppBaseController
{
    /** @var  GposPaymentGlConfigMasterRepository */
    private $gposPaymentGlConfigMasterRepository;

    public function __construct(GposPaymentGlConfigMasterRepository $gposPaymentGlConfigMasterRepo)
    {
        $this->gposPaymentGlConfigMasterRepository = $gposPaymentGlConfigMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/gposPaymentGlConfigMasters",
     *      summary="Get a listing of the GposPaymentGlConfigMasters.",
     *      tags={"GposPaymentGlConfigMaster"},
     *      description="Get all GposPaymentGlConfigMasters",
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
     *                  @SWG\Items(ref="#/definitions/GposPaymentGlConfigMaster")
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
        $this->gposPaymentGlConfigMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->gposPaymentGlConfigMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $gposPaymentGlConfigMasters = $this->gposPaymentGlConfigMasterRepository->all();

        return $this->sendResponse($gposPaymentGlConfigMasters->toArray(), 'Gpos Payment Gl Config Masters retrieved successfully');
    }

    /**
     * @param CreateGposPaymentGlConfigMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/gposPaymentGlConfigMasters",
     *      summary="Store a newly created GposPaymentGlConfigMaster in storage",
     *      tags={"GposPaymentGlConfigMaster"},
     *      description="Store GposPaymentGlConfigMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GposPaymentGlConfigMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GposPaymentGlConfigMaster")
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
     *                  ref="#/definitions/GposPaymentGlConfigMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateGposPaymentGlConfigMasterAPIRequest $request)
    {
        $input = $request->all();

        $gposPaymentGlConfigMasters = $this->gposPaymentGlConfigMasterRepository->create($input);

        return $this->sendResponse($gposPaymentGlConfigMasters->toArray(), 'Gpos Payment Gl Config Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/gposPaymentGlConfigMasters/{id}",
     *      summary="Display the specified GposPaymentGlConfigMaster",
     *      tags={"GposPaymentGlConfigMaster"},
     *      description="Get GposPaymentGlConfigMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GposPaymentGlConfigMaster",
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
     *                  ref="#/definitions/GposPaymentGlConfigMaster"
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
        /** @var GposPaymentGlConfigMaster $gposPaymentGlConfigMaster */
        $gposPaymentGlConfigMaster = $this->gposPaymentGlConfigMasterRepository->findWithoutFail($id);

        if (empty($gposPaymentGlConfigMaster)) {
            return $this->sendError('Gpos Payment Gl Config Master not found');
        }

        return $this->sendResponse($gposPaymentGlConfigMaster->toArray(), 'Gpos Payment Gl Config Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateGposPaymentGlConfigMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/gposPaymentGlConfigMasters/{id}",
     *      summary="Update the specified GposPaymentGlConfigMaster in storage",
     *      tags={"GposPaymentGlConfigMaster"},
     *      description="Update GposPaymentGlConfigMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GposPaymentGlConfigMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GposPaymentGlConfigMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GposPaymentGlConfigMaster")
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
     *                  ref="#/definitions/GposPaymentGlConfigMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateGposPaymentGlConfigMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var GposPaymentGlConfigMaster $gposPaymentGlConfigMaster */
        $gposPaymentGlConfigMaster = $this->gposPaymentGlConfigMasterRepository->findWithoutFail($id);

        if (empty($gposPaymentGlConfigMaster)) {
            return $this->sendError('Gpos Payment Gl Config Master not found');
        }

        $gposPaymentGlConfigMaster = $this->gposPaymentGlConfigMasterRepository->update($input, $id);

        return $this->sendResponse($gposPaymentGlConfigMaster->toArray(), 'GposPaymentGlConfigMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/gposPaymentGlConfigMasters/{id}",
     *      summary="Remove the specified GposPaymentGlConfigMaster from storage",
     *      tags={"GposPaymentGlConfigMaster"},
     *      description="Delete GposPaymentGlConfigMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GposPaymentGlConfigMaster",
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
        /** @var GposPaymentGlConfigMaster $gposPaymentGlConfigMaster */
        $gposPaymentGlConfigMaster = $this->gposPaymentGlConfigMasterRepository->findWithoutFail($id);

        if (empty($gposPaymentGlConfigMaster)) {
            return $this->sendError('Gpos Payment Gl Config Master not found');
        }

        $gposPaymentGlConfigMaster->delete();

        return $this->sendResponse($id, 'Gpos Payment Gl Config Master deleted successfully');
    }
}
