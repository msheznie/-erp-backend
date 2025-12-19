<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierInvoiceItemDetailAPIRequest;
use App\Http\Requests\API\UpdateSupplierInvoiceItemDetailAPIRequest;
use App\Models\SupplierInvoiceItemDetail;
use App\Models\BookInvSuppMaster;
use App\Models\BookInvSuppDet;
use App\Models\UnbilledGrvGroupBy;
use App\Models\PoAdvancePayment;
use App\Models\GRVDetails;
use App\Models\Company;
use App\Models\SupplierAssigned;
use App\Repositories\SupplierInvoiceItemDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use App\helper\TaxService;

/**
 * Class SupplierInvoiceItemDetailController
 * @package App\Http\Controllers\API
 */

class SupplierInvoiceItemDetailAPIController extends AppBaseController
{
    /** @var  SupplierInvoiceItemDetailRepository */
    private $supplierInvoiceItemDetailRepository;

    public function __construct(SupplierInvoiceItemDetailRepository $supplierInvoiceItemDetailRepo)
    {
        $this->supplierInvoiceItemDetailRepository = $supplierInvoiceItemDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/supplierInvoiceItemDetails",
     *      summary="Get a listing of the SupplierInvoiceItemDetails.",
     *      tags={"SupplierInvoiceItemDetail"},
     *      description="Get all SupplierInvoiceItemDetails",
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
     *                  @SWG\Items(ref="#/definitions/SupplierInvoiceItemDetail")
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
        $this->supplierInvoiceItemDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierInvoiceItemDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierInvoiceItemDetails = $this->supplierInvoiceItemDetailRepository->all();

        return $this->sendResponse($supplierInvoiceItemDetails->toArray(), trans('custom.supplier_invoice_item_details_retrieved_successful'));
    }

    /**
     * @param CreateSupplierInvoiceItemDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/supplierInvoiceItemDetails",
     *      summary="Store a newly created SupplierInvoiceItemDetail in storage",
     *      tags={"SupplierInvoiceItemDetail"},
     *      description="Store SupplierInvoiceItemDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupplierInvoiceItemDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupplierInvoiceItemDetail")
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
     *                  ref="#/definitions/SupplierInvoiceItemDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierInvoiceItemDetailAPIRequest $request)
    {
        $input = $request->all();

        $supplierInvoiceItemDetail = $this->supplierInvoiceItemDetailRepository->create($input);

        return $this->sendResponse($supplierInvoiceItemDetail->toArray(), trans('custom.supplier_invoice_item_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/supplierInvoiceItemDetails/{id}",
     *      summary="Display the specified SupplierInvoiceItemDetail",
     *      tags={"SupplierInvoiceItemDetail"},
     *      description="Get SupplierInvoiceItemDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierInvoiceItemDetail",
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
     *                  ref="#/definitions/SupplierInvoiceItemDetail"
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
        /** @var SupplierInvoiceItemDetail $supplierInvoiceItemDetail */
        $supplierInvoiceItemDetail = $this->supplierInvoiceItemDetailRepository->findWithoutFail($id);

        if (empty($supplierInvoiceItemDetail)) {
            return $this->sendError(trans('custom.supplier_invoice_item_detail_not_found'));
        }

        return $this->sendResponse($supplierInvoiceItemDetail->toArray(), trans('custom.supplier_invoice_item_detail_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdateSupplierInvoiceItemDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/supplierInvoiceItemDetails/{id}",
     *      summary="Update the specified SupplierInvoiceItemDetail in storage",
     *      tags={"SupplierInvoiceItemDetail"},
     *      description="Update SupplierInvoiceItemDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierInvoiceItemDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SupplierInvoiceItemDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SupplierInvoiceItemDetail")
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
     *                  ref="#/definitions/SupplierInvoiceItemDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierInvoiceItemDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierInvoiceItemDetail $supplierInvoiceItemDetail */
        $supplierInvoiceItemDetail = $this->supplierInvoiceItemDetailRepository->findWithoutFail($id);

        if (empty($supplierInvoiceItemDetail)) {
            return $this->sendError(trans('custom.supplier_invoice_item_detail_not_found'));
        }

        $supplierInvoiceItemDetail = $this->supplierInvoiceItemDetailRepository->update($input, $id);

        return $this->sendResponse($supplierInvoiceItemDetail->toArray(), trans('custom.supplierinvoiceitemdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/supplierInvoiceItemDetails/{id}",
     *      summary="Remove the specified SupplierInvoiceItemDetail from storage",
     *      tags={"SupplierInvoiceItemDetail"},
     *      description="Delete SupplierInvoiceItemDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SupplierInvoiceItemDetail",
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
        /** @var SupplierInvoiceItemDetail $supplierInvoiceItemDetail */
        $supplierInvoiceItemDetail = $this->supplierInvoiceItemDetailRepository->findWithoutFail($id);

        if (empty($supplierInvoiceItemDetail)) {
            return $this->sendError(trans('custom.supplier_invoice_item_detail_not_found'));
        }

        $supplierInvoiceItemDetail->delete();

        return $this->sendSuccess('Supplier Invoice Item Detail deleted successfully');
    }

    public function getGRVDetailsForSupplierInvoice(Request $request)
    {
        $input = $request->all();

        $supplierInvoiceItemDetail = $this->supplierInvoiceItemDetailRepository->getGRVDetailsForSupplierInvoice($input);

        if ($supplierInvoiceItemDetail['status']) {
            return $this->sendResponse($supplierInvoiceItemDetail['data'], trans('custom.supplier_invoice_item_detail_retrieved_successfull'));
        } else {
            return $this->sendError(trans('custom.error_occured_while_retriving_supplier_item_detail'));
        }
    }
}
