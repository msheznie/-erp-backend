<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderMasterSupplierAPIRequest;
use App\Http\Requests\API\UpdateTenderMasterSupplierAPIRequest;
use App\Models\TenderMasterSupplier;
use App\Repositories\TenderMasterSupplierRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderMasterSupplierController
 * @package App\Http\Controllers\API
 */

class TenderMasterSupplierAPIController extends AppBaseController
{
    /** @var  TenderMasterSupplierRepository */
    private $tenderMasterSupplierRepository;

    public function __construct(TenderMasterSupplierRepository $tenderMasterSupplierRepo)
    {
        $this->tenderMasterSupplierRepository = $tenderMasterSupplierRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderMasterSuppliers",
     *      summary="Get a listing of the TenderMasterSuppliers.",
     *      tags={"TenderMasterSupplier"},
     *      description="Get all TenderMasterSuppliers",
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
     *                  @SWG\Items(ref="#/definitions/TenderMasterSupplier")
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
        $this->tenderMasterSupplierRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderMasterSupplierRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderMasterSuppliers = $this->tenderMasterSupplierRepository->all();

        return $this->sendResponse($tenderMasterSuppliers->toArray(), trans('custom.tender_master_suppliers_retrieved_successfully'));
    }

    /**
     * @param CreateTenderMasterSupplierAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderMasterSuppliers",
     *      summary="Store a newly created TenderMasterSupplier in storage",
     *      tags={"TenderMasterSupplier"},
     *      description="Store TenderMasterSupplier",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderMasterSupplier that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderMasterSupplier")
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
     *                  ref="#/definitions/TenderMasterSupplier"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderMasterSupplierAPIRequest $request)
    {
        $input = $request->all();

        $tenderMasterSupplier = $this->tenderMasterSupplierRepository->create($input);

        return $this->sendResponse($tenderMasterSupplier->toArray(), trans('custom.tender_master_supplier_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderMasterSuppliers/{id}",
     *      summary="Display the specified TenderMasterSupplier",
     *      tags={"TenderMasterSupplier"},
     *      description="Get TenderMasterSupplier",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderMasterSupplier",
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
     *                  ref="#/definitions/TenderMasterSupplier"
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
        /** @var TenderMasterSupplier $tenderMasterSupplier */
        $tenderMasterSupplier = $this->tenderMasterSupplierRepository->findWithoutFail($id);

        if (empty($tenderMasterSupplier)) {
            return $this->sendError(trans('custom.tender_master_supplier_not_found'));
        }

        return $this->sendResponse($tenderMasterSupplier->toArray(), trans('custom.tender_master_supplier_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTenderMasterSupplierAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderMasterSuppliers/{id}",
     *      summary="Update the specified TenderMasterSupplier in storage",
     *      tags={"TenderMasterSupplier"},
     *      description="Update TenderMasterSupplier",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderMasterSupplier",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderMasterSupplier that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderMasterSupplier")
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
     *                  ref="#/definitions/TenderMasterSupplier"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderMasterSupplierAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderMasterSupplier $tenderMasterSupplier */
        $tenderMasterSupplier = $this->tenderMasterSupplierRepository->findWithoutFail($id);

        if (empty($tenderMasterSupplier)) {
            return $this->sendError(trans('custom.tender_master_supplier_not_found'));
        }

        $tenderMasterSupplier = $this->tenderMasterSupplierRepository->update($input, $id);

        return $this->sendResponse($tenderMasterSupplier->toArray(), trans('custom.tendermastersupplier_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderMasterSuppliers/{id}",
     *      summary="Remove the specified TenderMasterSupplier from storage",
     *      tags={"TenderMasterSupplier"},
     *      description="Delete TenderMasterSupplier",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderMasterSupplier",
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
        /** @var TenderMasterSupplier $tenderMasterSupplier */
        $tenderMasterSupplier = $this->tenderMasterSupplierRepository->findWithoutFail($id);

        if (empty($tenderMasterSupplier)) {
            return $this->sendError(trans('custom.tender_master_supplier_not_found'));
        }

        $tenderMasterSupplier->delete();

        return $this->sendSuccess('Tender Master Supplier deleted successfully');
    }
}
