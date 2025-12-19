<?php
/**
 * =============================================
 * -- File Name : DepreciationPeriodsReferredHistoryAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Mohamed Mubashir
 * -- Create date : 26 - Novemeber 2018
 * -- Description : This file contains the all CRUD for Asset Depreciation Master Referback
 * -- REVISION HISTORY
 * -- Date: 26 - Novemeber 2018 By:Mubashir Description: Added new functions named as getAssetDepPeriodHistoryByID()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDepreciationPeriodsReferredHistoryAPIRequest;
use App\Http\Requests\API\UpdateDepreciationPeriodsReferredHistoryAPIRequest;
use App\Models\DepreciationPeriodsReferredHistory;
use App\Repositories\DepreciationPeriodsReferredHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DepreciationPeriodsReferredHistoryController
 * @package App\Http\Controllers\API
 */

class DepreciationPeriodsReferredHistoryAPIController extends AppBaseController
{
    /** @var  DepreciationPeriodsReferredHistoryRepository */
    private $depreciationPeriodsReferredHistoryRepository;

    public function __construct(DepreciationPeriodsReferredHistoryRepository $depreciationPeriodsReferredHistoryRepo)
    {
        $this->depreciationPeriodsReferredHistoryRepository = $depreciationPeriodsReferredHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/depreciationPeriodsReferredHistories",
     *      summary="Get a listing of the DepreciationPeriodsReferredHistories.",
     *      tags={"DepreciationPeriodsReferredHistory"},
     *      description="Get all DepreciationPeriodsReferredHistories",
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
     *                  @SWG\Items(ref="#/definitions/DepreciationPeriodsReferredHistory")
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
        $this->depreciationPeriodsReferredHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->depreciationPeriodsReferredHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $depreciationPeriodsReferredHistories = $this->depreciationPeriodsReferredHistoryRepository->all();

        return $this->sendResponse($depreciationPeriodsReferredHistories->toArray(), trans('custom.depreciation_periods_referred_histories_retrieved_'));
    }

    /**
     * @param CreateDepreciationPeriodsReferredHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/depreciationPeriodsReferredHistories",
     *      summary="Store a newly created DepreciationPeriodsReferredHistory in storage",
     *      tags={"DepreciationPeriodsReferredHistory"},
     *      description="Store DepreciationPeriodsReferredHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DepreciationPeriodsReferredHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DepreciationPeriodsReferredHistory")
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
     *                  ref="#/definitions/DepreciationPeriodsReferredHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDepreciationPeriodsReferredHistoryAPIRequest $request)
    {
        $input = $request->all();

        $depreciationPeriodsReferredHistories = $this->depreciationPeriodsReferredHistoryRepository->create($input);

        return $this->sendResponse($depreciationPeriodsReferredHistories->toArray(), trans('custom.depreciation_periods_referred_history_saved_succes'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/depreciationPeriodsReferredHistories/{id}",
     *      summary="Display the specified DepreciationPeriodsReferredHistory",
     *      tags={"DepreciationPeriodsReferredHistory"},
     *      description="Get DepreciationPeriodsReferredHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DepreciationPeriodsReferredHistory",
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
     *                  ref="#/definitions/DepreciationPeriodsReferredHistory"
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
        /** @var DepreciationPeriodsReferredHistory $depreciationPeriodsReferredHistory */
        $depreciationPeriodsReferredHistory = $this->depreciationPeriodsReferredHistoryRepository->findWithoutFail($id);

        if (empty($depreciationPeriodsReferredHistory)) {
            return $this->sendError(trans('custom.depreciation_periods_referred_history_not_found'));
        }

        return $this->sendResponse($depreciationPeriodsReferredHistory->toArray(), trans('custom.depreciation_periods_referred_history_retrieved_su'));
    }

    /**
     * @param int $id
     * @param UpdateDepreciationPeriodsReferredHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/depreciationPeriodsReferredHistories/{id}",
     *      summary="Update the specified DepreciationPeriodsReferredHistory in storage",
     *      tags={"DepreciationPeriodsReferredHistory"},
     *      description="Update DepreciationPeriodsReferredHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DepreciationPeriodsReferredHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DepreciationPeriodsReferredHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DepreciationPeriodsReferredHistory")
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
     *                  ref="#/definitions/DepreciationPeriodsReferredHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDepreciationPeriodsReferredHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var DepreciationPeriodsReferredHistory $depreciationPeriodsReferredHistory */
        $depreciationPeriodsReferredHistory = $this->depreciationPeriodsReferredHistoryRepository->findWithoutFail($id);

        if (empty($depreciationPeriodsReferredHistory)) {
            return $this->sendError(trans('custom.depreciation_periods_referred_history_not_found'));
        }

        $depreciationPeriodsReferredHistory = $this->depreciationPeriodsReferredHistoryRepository->update($input, $id);

        return $this->sendResponse($depreciationPeriodsReferredHistory->toArray(), trans('custom.depreciationperiodsreferredhistory_updated_success'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/depreciationPeriodsReferredHistories/{id}",
     *      summary="Remove the specified DepreciationPeriodsReferredHistory from storage",
     *      tags={"DepreciationPeriodsReferredHistory"},
     *      description="Delete DepreciationPeriodsReferredHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DepreciationPeriodsReferredHistory",
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
        /** @var DepreciationPeriodsReferredHistory $depreciationPeriodsReferredHistory */
        $depreciationPeriodsReferredHistory = $this->depreciationPeriodsReferredHistoryRepository->findWithoutFail($id);

        if (empty($depreciationPeriodsReferredHistory)) {
            return $this->sendError(trans('custom.depreciation_periods_referred_history_not_found'));
        }

        $depreciationPeriodsReferredHistory->delete();

        return $this->sendResponse($id, trans('custom.depreciation_periods_referred_history_deleted_succ'));
    }

    public function getAssetDepPeriodHistoryByID(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $assetDepPeriod = DepreciationPeriodsReferredHistory::with(['maincategory_by', 'financecategory_by', 'serviceline_by'])->ofDepreciation($input['depMasterAutoID'])->where('timesReferred', $input['timesReferred']);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetDepPeriod = $assetDepPeriod->where(function ($query) use ($search) {
                $query->where('faCode', 'LIKE', "%{$search}%");
                $query->orWhere('assetDescription', 'LIKE', "%{$search}%");
            });
        }

        $outputSUM = $assetDepPeriod->get();

        $depAmountLocal = collect($outputSUM)->pluck('depAmountLocal')->toArray();
        $depAmountLocal = array_sum($depAmountLocal);

        $depAmountRpt = collect($outputSUM)->pluck('depAmountRpt')->toArray();
        $depAmountRpt = array_sum($depAmountRpt);

        return \DataTables::eloquent($assetDepPeriod)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('DepreciationPeriodsReferredID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->with('totalAmount', [
                'depAmountLocal' => $depAmountLocal,
                'depAmountRpt' => $depAmountRpt,
            ])
            ->make(true);
    }
}
