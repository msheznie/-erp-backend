<?php
/**
 * =============================================
 * -- File Name : QuotationDetailsRefferedbackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  QuotationDetailsRefferedback
 * -- Author : Mohamed Nazir
 * -- Create date : 03-February 2019
 * -- Description : This file contains the all CRUD for Quotation Details Refferedback
 * -- REVISION HISTORY
 * -- Date: 03-February 2019 By: Nazir Description: Added new function getSQHDetailsHistory(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateQuotationDetailsRefferedbackAPIRequest;
use App\Http\Requests\API\UpdateQuotationDetailsRefferedbackAPIRequest;
use App\Models\QuotationDetailsRefferedback;
use App\Repositories\QuotationDetailsRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class QuotationDetailsRefferedbackController
 * @package App\Http\Controllers\API
 */

class QuotationDetailsRefferedbackAPIController extends AppBaseController
{
    /** @var  QuotationDetailsRefferedbackRepository */
    private $quotationDetailsRefferedbackRepository;

    public function __construct(QuotationDetailsRefferedbackRepository $quotationDetailsRefferedbackRepo)
    {
        $this->quotationDetailsRefferedbackRepository = $quotationDetailsRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationDetailsRefferedbacks",
     *      summary="Get a listing of the QuotationDetailsRefferedbacks.",
     *      tags={"QuotationDetailsRefferedback"},
     *      description="Get all QuotationDetailsRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/QuotationDetailsRefferedback")
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
        $this->quotationDetailsRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->quotationDetailsRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $quotationDetailsRefferedbacks = $this->quotationDetailsRefferedbackRepository->all();

        return $this->sendResponse($quotationDetailsRefferedbacks->toArray(), trans('custom.quotation_details_refferedbacks_retrieved_successf'));
    }

    /**
     * @param CreateQuotationDetailsRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/quotationDetailsRefferedbacks",
     *      summary="Store a newly created QuotationDetailsRefferedback in storage",
     *      tags={"QuotationDetailsRefferedback"},
     *      description="Store QuotationDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationDetailsRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationDetailsRefferedback")
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
     *                  ref="#/definitions/QuotationDetailsRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateQuotationDetailsRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $quotationDetailsRefferedbacks = $this->quotationDetailsRefferedbackRepository->create($input);

        return $this->sendResponse($quotationDetailsRefferedbacks->toArray(), trans('custom.quotation_details_refferedback_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationDetailsRefferedbacks/{id}",
     *      summary="Display the specified QuotationDetailsRefferedback",
     *      tags={"QuotationDetailsRefferedback"},
     *      description="Get QuotationDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationDetailsRefferedback",
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
     *                  ref="#/definitions/QuotationDetailsRefferedback"
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
        /** @var QuotationDetailsRefferedback $quotationDetailsRefferedback */
        $quotationDetailsRefferedback = $this->quotationDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($quotationDetailsRefferedback)) {
            return $this->sendError(trans('custom.quotation_details_refferedback_not_found'));
        }

        return $this->sendResponse($quotationDetailsRefferedback->toArray(), trans('custom.quotation_details_refferedback_retrieved_successfu'));
    }

    /**
     * @param int $id
     * @param UpdateQuotationDetailsRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/quotationDetailsRefferedbacks/{id}",
     *      summary="Update the specified QuotationDetailsRefferedback in storage",
     *      tags={"QuotationDetailsRefferedback"},
     *      description="Update QuotationDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationDetailsRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationDetailsRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationDetailsRefferedback")
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
     *                  ref="#/definitions/QuotationDetailsRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateQuotationDetailsRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var QuotationDetailsRefferedback $quotationDetailsRefferedback */
        $quotationDetailsRefferedback = $this->quotationDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($quotationDetailsRefferedback)) {
            return $this->sendError(trans('custom.quotation_details_refferedback_not_found'));
        }

        $quotationDetailsRefferedback = $this->quotationDetailsRefferedbackRepository->update($input, $id);

        return $this->sendResponse($quotationDetailsRefferedback->toArray(), trans('custom.quotationdetailsrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/quotationDetailsRefferedbacks/{id}",
     *      summary="Remove the specified QuotationDetailsRefferedback from storage",
     *      tags={"QuotationDetailsRefferedback"},
     *      description="Delete QuotationDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationDetailsRefferedback",
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
        /** @var QuotationDetailsRefferedback $quotationDetailsRefferedback */
        $quotationDetailsRefferedback = $this->quotationDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($quotationDetailsRefferedback)) {
            return $this->sendError(trans('custom.quotation_details_refferedback_not_found'));
        }

        $quotationDetailsRefferedback->delete();

        return $this->sendResponse($id, trans('custom.quotation_details_refferedback_deleted_successfull'));
    }

    public function getSQHDetailsHistory(Request $request)
    {
        $input = $request->all();
        $quotationMasterID = $input['quotationMasterID'];
        $timesReferred = $input['timesReferred'];

        $items = QuotationDetailsRefferedback::where('quotationMasterID', $quotationMasterID)
            ->where('timesReferred', $timesReferred)
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.quotation_refferedback_details_retrieved_successfu'));
    }
}
