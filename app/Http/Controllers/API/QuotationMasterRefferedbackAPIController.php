<?php
/**
 * =============================================
 * -- File Name : QuotationMasterVersionAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  QuotationMasterVersion
 * -- Author : Mohamed Nazir
 * -- Create date : 03 - February 2019
 * -- Description : This file contains the all CRUD for Sales Quotation Master Version
 * -- REVISION HISTORY
 * -- Date: 3-February 2019 By: Nazir Description: Added new function getSalesQuotationAmendHistory(),
 */


namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateQuotationMasterRefferedbackAPIRequest;
use App\Http\Requests\API\UpdateQuotationMasterRefferedbackAPIRequest;
use App\Models\QuotationMasterRefferedback;
use App\Repositories\QuotationMasterRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class QuotationMasterRefferedbackController
 * @package App\Http\Controllers\API
 */
class QuotationMasterRefferedbackAPIController extends AppBaseController
{
    /** @var  QuotationMasterRefferedbackRepository */
    private $quotationMasterRefferedbackRepository;

    public function __construct(QuotationMasterRefferedbackRepository $quotationMasterRefferedbackRepo)
    {
        $this->quotationMasterRefferedbackRepository = $quotationMasterRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationMasterRefferedbacks",
     *      summary="Get a listing of the QuotationMasterRefferedbacks.",
     *      tags={"QuotationMasterRefferedback"},
     *      description="Get all QuotationMasterRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/QuotationMasterRefferedback")
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
        $this->quotationMasterRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->quotationMasterRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $quotationMasterRefferedbacks = $this->quotationMasterRefferedbackRepository->all();

        return $this->sendResponse($quotationMasterRefferedbacks->toArray(), trans('custom.quotation_master_refferedbacks_retrieved_successfu'));
    }

    /**
     * @param CreateQuotationMasterRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/quotationMasterRefferedbacks",
     *      summary="Store a newly created QuotationMasterRefferedback in storage",
     *      tags={"QuotationMasterRefferedback"},
     *      description="Store QuotationMasterRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationMasterRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationMasterRefferedback")
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
     *                  ref="#/definitions/QuotationMasterRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateQuotationMasterRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $quotationMasterRefferedbacks = $this->quotationMasterRefferedbackRepository->create($input);

        return $this->sendResponse($quotationMasterRefferedbacks->toArray(), trans('custom.quotation_master_refferedback_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationMasterRefferedbacks/{id}",
     *      summary="Display the specified QuotationMasterRefferedback",
     *      tags={"QuotationMasterRefferedback"},
     *      description="Get QuotationMasterRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMasterRefferedback",
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
     *                  ref="#/definitions/QuotationMasterRefferedback"
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
        /** @var QuotationMasterRefferedback $quotationMasterRefferedback */
        $quotationMasterRefferedback = $this->quotationMasterRefferedbackRepository->with(['confirmed_by', 'created_by'])->findWithoutFail($id);

        if (empty($quotationMasterRefferedback)) {
            return $this->sendError(trans('custom.quotation_master_refferedback_not_found'));
        }

        return $this->sendResponse($quotationMasterRefferedback->toArray(), trans('custom.quotation_master_refferedback_retrieved_successful'));
    }

    /**
     * @param int $id
     * @param UpdateQuotationMasterRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/quotationMasterRefferedbacks/{id}",
     *      summary="Update the specified QuotationMasterRefferedback in storage",
     *      tags={"QuotationMasterRefferedback"},
     *      description="Update QuotationMasterRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMasterRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationMasterRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationMasterRefferedback")
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
     *                  ref="#/definitions/QuotationMasterRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateQuotationMasterRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var QuotationMasterRefferedback $quotationMasterRefferedback */
        $quotationMasterRefferedback = $this->quotationMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($quotationMasterRefferedback)) {
            return $this->sendError(trans('custom.quotation_master_refferedback_not_found'));
        }

        $quotationMasterRefferedback = $this->quotationMasterRefferedbackRepository->update($input, $id);

        return $this->sendResponse($quotationMasterRefferedback->toArray(), trans('custom.quotationmasterrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/quotationMasterRefferedbacks/{id}",
     *      summary="Remove the specified QuotationMasterRefferedback from storage",
     *      tags={"QuotationMasterRefferedback"},
     *      description="Delete QuotationMasterRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMasterRefferedback",
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
        /** @var QuotationMasterRefferedback $quotationMasterRefferedback */
        $quotationMasterRefferedback = $this->quotationMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($quotationMasterRefferedback)) {
            return $this->sendError(trans('custom.quotation_master_refferedback_not_found'));
        }

        $quotationMasterRefferedback->delete();

        return $this->sendResponse($id, trans('custom.quotation_master_refferedback_deleted_successfully'));
    }

    public function getSalesQuotationAmendHistory(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $quotationMasterID = $request['quotationMasterID'];

        $quotationMasterRefferedbackData = QuotationMasterRefferedback::where('quotationMasterID', $quotationMasterID);

        return \DataTables::eloquent($quotationMasterRefferedbackData)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('quotationMasterRefferedBackID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
