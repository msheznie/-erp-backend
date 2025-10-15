<?php
/**
 * =============================================
 * -- File Name : GRVMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name : GrvMasterRefferedback
 * -- Author : Mohamed Nazir
 * -- Create date : 14-November 2018
 * -- Description : This file contains the all CRUD for Grv Master Reffered back
 * -- REVISION HISTORY
 * -- Date: 14-November 2018 By: Nazir Description: Added new functions named as getGRVMasterAmendHistory()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGrvMasterRefferedbackAPIRequest;
use App\Http\Requests\API\UpdateGrvMasterRefferedbackAPIRequest;
use App\Models\GrvMasterRefferedback;
use App\Repositories\GrvMasterRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class GrvMasterRefferedbackController
 * @package App\Http\Controllers\API
 */

class GrvMasterRefferedbackAPIController extends AppBaseController
{
    /** @var  GrvMasterRefferedbackRepository */
    private $grvMasterRefferedbackRepository;

    public function __construct(GrvMasterRefferedbackRepository $grvMasterRefferedbackRepo)
    {
        $this->grvMasterRefferedbackRepository = $grvMasterRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/grvMasterRefferedbacks",
     *      summary="Get a listing of the GrvMasterRefferedbacks.",
     *      tags={"GrvMasterRefferedback"},
     *      description="Get all GrvMasterRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/GrvMasterRefferedback")
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
        $this->grvMasterRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->grvMasterRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $grvMasterRefferedbacks = $this->grvMasterRefferedbackRepository->all();

        return $this->sendResponse($grvMasterRefferedbacks->toArray(), trans('custom.grv_master_refferedbacks_retrieved_successfully'));
    }

    /**
     * @param CreateGrvMasterRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/grvMasterRefferedbacks",
     *      summary="Store a newly created GrvMasterRefferedback in storage",
     *      tags={"GrvMasterRefferedback"},
     *      description="Store GrvMasterRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GrvMasterRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GrvMasterRefferedback")
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
     *                  ref="#/definitions/GrvMasterRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateGrvMasterRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $grvMasterRefferedbacks = $this->grvMasterRefferedbackRepository->create($input);

        return $this->sendResponse($grvMasterRefferedbacks->toArray(), trans('custom.grv_master_refferedback_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/grvMasterRefferedbacks/{id}",
     *      summary="Display the specified GrvMasterRefferedback",
     *      tags={"GrvMasterRefferedback"},
     *      description="Get GrvMasterRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GrvMasterRefferedback",
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
     *                  ref="#/definitions/GrvMasterRefferedback"
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
        /** @var GrvMasterRefferedback $grvMasterRefferedback */
        $grvMasterRefferedback = $this->grvMasterRefferedbackRepository->with(['created_by', 'confirmed_by', 'segment_by', 'location_by', 'currency_by', 'supplier_by', 'financeperiod_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'financeyear_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'grvtype_by'])->findWithoutFail($id);

        if (empty($grvMasterRefferedback)) {
            return $this->sendError(trans('custom.grv_master_refferedback_not_found'));
        }

        return $this->sendResponse($grvMasterRefferedback->toArray(), trans('custom.grv_master_refferedback_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateGrvMasterRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/grvMasterRefferedbacks/{id}",
     *      summary="Update the specified GrvMasterRefferedback in storage",
     *      tags={"GrvMasterRefferedback"},
     *      description="Update GrvMasterRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GrvMasterRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GrvMasterRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GrvMasterRefferedback")
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
     *                  ref="#/definitions/GrvMasterRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateGrvMasterRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var GrvMasterRefferedback $grvMasterRefferedback */
        $grvMasterRefferedback = $this->grvMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($grvMasterRefferedback)) {
            return $this->sendError(trans('custom.grv_master_refferedback_not_found'));
        }

        $grvMasterRefferedback = $this->grvMasterRefferedbackRepository->update($input, $id);

        return $this->sendResponse($grvMasterRefferedback->toArray(), trans('custom.grvmasterrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/grvMasterRefferedbacks/{id}",
     *      summary="Remove the specified GrvMasterRefferedback from storage",
     *      tags={"GrvMasterRefferedback"},
     *      description="Delete GrvMasterRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GrvMasterRefferedback",
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
        /** @var GrvMasterRefferedback $grvMasterRefferedback */
        $grvMasterRefferedback = $this->grvMasterRefferedbackRepository->findWithoutFail($id);

        if (empty($grvMasterRefferedback)) {
            return $this->sendError(trans('custom.grv_master_refferedback_not_found'));
        }

        $grvMasterRefferedback->delete();

        return $this->sendResponse($id, trans('custom.grv_master_refferedback_deleted_successfully'));
    }

    public function getGRVMasterAmendHistory(Request $request)
    {
        $input = $request->all();

        $grvHistory = GrvMasterRefferedback::where('grvAutoID', $input['grvAutoID'])
            ->with(['created_by','confirmed_by','modified_by','supplier_by','segment_by', 'cancelled_by', 'currency_by', 'grvtype_by', 'location_by'])
            ->get();

        return $this->sendResponse($grvHistory, trans('custom.good_receipt_voucher_detail_retrieved_successfully'));
    }
}
