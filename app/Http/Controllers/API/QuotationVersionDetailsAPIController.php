<?php
/**
 * =============================================
 * -- File Name : QuotationVersionDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  QuotationVersionDetails
 * -- Author : Mohamed Nazir
 * -- Create date : 03-February 2019
 * -- Description : This file contains the all CRUD for Sales Quotation Version Details
 * -- REVISION HISTORY
 * -- Date: 03-February 2019 By: Nazir Description: Added new function getSQVDetailsHistory(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateQuotationVersionDetailsAPIRequest;
use App\Http\Requests\API\UpdateQuotationVersionDetailsAPIRequest;
use App\Models\QuotationVersionDetails;
use App\Repositories\QuotationVersionDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class QuotationVersionDetailsController
 * @package App\Http\Controllers\API
 */
class QuotationVersionDetailsAPIController extends AppBaseController
{
    /** @var  QuotationVersionDetailsRepository */
    private $quotationVersionDetailsRepository;

    public function __construct(QuotationVersionDetailsRepository $quotationVersionDetailsRepo)
    {
        $this->quotationVersionDetailsRepository = $quotationVersionDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationVersionDetails",
     *      summary="Get a listing of the QuotationVersionDetails.",
     *      tags={"QuotationVersionDetails"},
     *      description="Get all QuotationVersionDetails",
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
     *                  @SWG\Items(ref="#/definitions/QuotationVersionDetails")
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
        $this->quotationVersionDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->quotationVersionDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $quotationVersionDetails = $this->quotationVersionDetailsRepository->all();

        return $this->sendResponse($quotationVersionDetails->toArray(), trans('custom.quotation_version_details_retrieved_successfully'));
    }

    /**
     * @param CreateQuotationVersionDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/quotationVersionDetails",
     *      summary="Store a newly created QuotationVersionDetails in storage",
     *      tags={"QuotationVersionDetails"},
     *      description="Store QuotationVersionDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationVersionDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationVersionDetails")
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
     *                  ref="#/definitions/QuotationVersionDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateQuotationVersionDetailsAPIRequest $request)
    {
        $input = $request->all();

        $quotationVersionDetails = $this->quotationVersionDetailsRepository->create($input);

        return $this->sendResponse($quotationVersionDetails->toArray(), trans('custom.quotation_version_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationVersionDetails/{id}",
     *      summary="Display the specified QuotationVersionDetails",
     *      tags={"QuotationVersionDetails"},
     *      description="Get QuotationVersionDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationVersionDetails",
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
     *                  ref="#/definitions/QuotationVersionDetails"
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
        /** @var QuotationVersionDetails $quotationVersionDetails */
        $quotationVersionDetails = $this->quotationVersionDetailsRepository->findWithoutFail($id);

        if (empty($quotationVersionDetails)) {
            return $this->sendError(trans('custom.quotation_version_details_not_found'));
        }

        return $this->sendResponse($quotationVersionDetails->toArray(), trans('custom.quotation_version_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateQuotationVersionDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/quotationVersionDetails/{id}",
     *      summary="Update the specified QuotationVersionDetails in storage",
     *      tags={"QuotationVersionDetails"},
     *      description="Update QuotationVersionDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationVersionDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationVersionDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationVersionDetails")
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
     *                  ref="#/definitions/QuotationVersionDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateQuotationVersionDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var QuotationVersionDetails $quotationVersionDetails */
        $quotationVersionDetails = $this->quotationVersionDetailsRepository->findWithoutFail($id);

        if (empty($quotationVersionDetails)) {
            return $this->sendError(trans('custom.quotation_version_details_not_found'));
        }

        $quotationVersionDetails = $this->quotationVersionDetailsRepository->update($input, $id);

        return $this->sendResponse($quotationVersionDetails->toArray(), trans('custom.quotationversiondetails_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/quotationVersionDetails/{id}",
     *      summary="Remove the specified QuotationVersionDetails from storage",
     *      tags={"QuotationVersionDetails"},
     *      description="Delete QuotationVersionDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationVersionDetails",
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
        /** @var QuotationVersionDetails $quotationVersionDetails */
        $quotationVersionDetails = $this->quotationVersionDetailsRepository->findWithoutFail($id);

        if (empty($quotationVersionDetails)) {
            return $this->sendError(trans('custom.quotation_version_details_not_found'));
        }

        $quotationVersionDetails->delete();

        return $this->sendResponse($id, trans('custom.quotation_version_details_deleted_successfully'));
    }

    public function getSQVDetailsHistory(Request $request)
    {
        $input = $request->all();
        $quotationMasterID = $input['quotationMasterID'];
        $versionNo = $input['versionNo'];

        $items = QuotationVersionDetails::where('quotationMasterID', $quotationMasterID)
            ->where('versionNo', $versionNo)
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.quotation_version_details_retrieved_successfully'));
    }
}
