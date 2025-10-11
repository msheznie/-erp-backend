<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTaxMasterAPIRequest;
use App\Http\Requests\API\UpdateTaxMasterAPIRequest;
use App\Models\TaxMaster;
use App\Repositories\TaxMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TaxMasterController
 * @package App\Http\Controllers\API
 */

class TaxMasterAPIController extends AppBaseController
{
    /** @var  TaxMasterRepository */
    private $taxMasterRepository;

    public function __construct(TaxMasterRepository $taxMasterRepo)
    {
        $this->taxMasterRepository = $taxMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/taxMasters",
     *      summary="Get a listing of the TaxMasters.",
     *      tags={"TaxMaster"},
     *      description="Get all TaxMasters",
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
     *                  @SWG\Items(ref="#/definitions/TaxMaster")
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
        $this->taxMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->taxMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $taxMasters = $this->taxMasterRepository->all();

        return $this->sendResponse($taxMasters->toArray(), trans('custom.vat_masters_retrieved_successfully'));
    }

    /**
     * @param CreateTaxMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/taxMasters",
     *      summary="Store a newly created TaxMaster in storage",
     *      tags={"TaxMaster"},
     *      description="Store TaxMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TaxMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TaxMaster")
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
     *                  ref="#/definitions/TaxMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTaxMasterAPIRequest $request)
    {
        $input = $request->all();

        $taxMaster = $this->taxMasterRepository->create($input);

        return $this->sendResponse($taxMaster->toArray(), trans('custom.vat_master_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/taxMasters/{id}",
     *      summary="Display the specified TaxMaster",
     *      tags={"TaxMaster"},
     *      description="Get TaxMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxMaster",
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
     *                  ref="#/definitions/TaxMaster"
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
        /** @var TaxMaster $taxMaster */
        $taxMaster = $this->taxMasterRepository->findWithoutFail($id);

        if (empty($taxMaster)) {
            return $this->sendError(trans('custom.vat_master_not_found'));
        }

        return $this->sendResponse($taxMaster->toArray(), trans('custom.vat_master_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTaxMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/taxMasters/{id}",
     *      summary="Update the specified TaxMaster in storage",
     *      tags={"TaxMaster"},
     *      description="Update TaxMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TaxMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TaxMaster")
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
     *                  ref="#/definitions/TaxMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTaxMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var TaxMaster $taxMaster */
        $taxMaster = $this->taxMasterRepository->findWithoutFail($id);

        if (empty($taxMaster)) {
            return $this->sendError(trans('custom.vat_master_not_found'));
        }

        $taxMaster = $this->taxMasterRepository->update($input, $id);

        return $this->sendResponse($taxMaster->toArray(), trans('custom.taxmaster_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/taxMasters/{id}",
     *      summary="Remove the specified TaxMaster from storage",
     *      tags={"TaxMaster"},
     *      description="Delete TaxMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TaxMaster",
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
        /** @var TaxMaster $taxMaster */
        $taxMaster = $this->taxMasterRepository->findWithoutFail($id);

        if (empty($taxMaster)) {
            return $this->sendError(trans('custom.vat_master_not_found'));
        }

        $taxMaster->delete();

        return $this->sendSuccess('VAT Master deleted successfully');
    }
}
