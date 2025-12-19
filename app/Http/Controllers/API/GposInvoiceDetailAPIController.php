<?php
/**
 * =============================================
 * -- File Name : GposInvoiceDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  General pos invoice detail
 * -- Author : Mohamed Fayas
 * -- Create date : 22 - January 2019
 * -- Description : This file contains the all CRUD for  General pos invoice detail
 * -- REVISION HISTORY
 * -- Date: 22 - January 2019 By: Fayas Description: Added new function
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGposInvoiceDetailAPIRequest;
use App\Http\Requests\API\UpdateGposInvoiceDetailAPIRequest;
use App\Models\GposInvoiceDetail;
use App\Repositories\GposInvoiceDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class GposInvoiceDetailController
 * @package App\Http\Controllers\API
 */

class GposInvoiceDetailAPIController extends AppBaseController
{
    /** @var  GposInvoiceDetailRepository */
    private $gposInvoiceDetailRepository;

    public function __construct(GposInvoiceDetailRepository $gposInvoiceDetailRepo)
    {
        $this->gposInvoiceDetailRepository = $gposInvoiceDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/gposInvoiceDetails",
     *      summary="Get a listing of the GposInvoiceDetails.",
     *      tags={"GposInvoiceDetail"},
     *      description="Get all GposInvoiceDetails",
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
     *                  @SWG\Items(ref="#/definitions/GposInvoiceDetail")
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
        $this->gposInvoiceDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->gposInvoiceDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $gposInvoiceDetails = $this->gposInvoiceDetailRepository->all();

        return $this->sendResponse($gposInvoiceDetails->toArray(), trans('custom.gpos_invoice_details_retrieved_successfully'));
    }

    /**
     * @param CreateGposInvoiceDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/gposInvoiceDetails",
     *      summary="Store a newly created GposInvoiceDetail in storage",
     *      tags={"GposInvoiceDetail"},
     *      description="Store GposInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GposInvoiceDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GposInvoiceDetail")
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
     *                  ref="#/definitions/GposInvoiceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateGposInvoiceDetailAPIRequest $request)
    {
        $input = $request->all();

        $gposInvoiceDetails = $this->gposInvoiceDetailRepository->create($input);

        return $this->sendResponse($gposInvoiceDetails->toArray(), trans('custom.gpos_invoice_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/gposInvoiceDetails/{id}",
     *      summary="Display the specified GposInvoiceDetail",
     *      tags={"GposInvoiceDetail"},
     *      description="Get GposInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GposInvoiceDetail",
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
     *                  ref="#/definitions/GposInvoiceDetail"
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
        /** @var GposInvoiceDetail $gposInvoiceDetail */
        $gposInvoiceDetail = $this->gposInvoiceDetailRepository->findWithoutFail($id);

        if (empty($gposInvoiceDetail)) {
            return $this->sendError(trans('custom.gpos_invoice_detail_not_found'));
        }

        return $this->sendResponse($gposInvoiceDetail->toArray(), trans('custom.gpos_invoice_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateGposInvoiceDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/gposInvoiceDetails/{id}",
     *      summary="Update the specified GposInvoiceDetail in storage",
     *      tags={"GposInvoiceDetail"},
     *      description="Update GposInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GposInvoiceDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GposInvoiceDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GposInvoiceDetail")
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
     *                  ref="#/definitions/GposInvoiceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateGposInvoiceDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var GposInvoiceDetail $gposInvoiceDetail */
        $gposInvoiceDetail = $this->gposInvoiceDetailRepository->findWithoutFail($id);

        if (empty($gposInvoiceDetail)) {
            return $this->sendError(trans('custom.gpos_invoice_detail_not_found'));
        }

        $gposInvoiceDetail = $this->gposInvoiceDetailRepository->update($input, $id);

        return $this->sendResponse($gposInvoiceDetail->toArray(), trans('custom.gposinvoicedetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/gposInvoiceDetails/{id}",
     *      summary="Remove the specified GposInvoiceDetail from storage",
     *      tags={"GposInvoiceDetail"},
     *      description="Delete GposInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GposInvoiceDetail",
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
        /** @var GposInvoiceDetail $gposInvoiceDetail */
        $gposInvoiceDetail = $this->gposInvoiceDetailRepository->findWithoutFail($id);

        if (empty($gposInvoiceDetail)) {
            return $this->sendError(trans('custom.gpos_invoice_detail_not_found'));
        }

        $gposInvoiceDetail->delete();

        return $this->sendResponse($id, trans('custom.gpos_invoice_detail_deleted_successfully'));
    }
}
