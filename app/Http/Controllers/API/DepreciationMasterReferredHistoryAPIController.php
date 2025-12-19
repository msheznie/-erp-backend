<?php

/**
 * =============================================
 * -- File Name : DepreciationMasterReferredHistoryAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Mohamed Mubashir
 * -- Create date : 26 - Novemeber 2018
 * -- Description : This file contains the all CRUD for Asset Depreciation Master Referback
 * -- REVISION HISTORY
 * -- Date: 26 - Novemeber 2018 By:Mubashir Description: Added new functions named as getAllDepreciationAmendHistory(),assetDepreciationHistoryByID()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDepreciationMasterReferredHistoryAPIRequest;
use App\Http\Requests\API\UpdateDepreciationMasterReferredHistoryAPIRequest;
use App\Models\DepreciationMasterReferredHistory;
use App\Models\DepreciationPeriodsReferredHistory;
use App\Repositories\DepreciationMasterReferredHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DepreciationMasterReferredHistoryController
 * @package App\Http\Controllers\API
 */
class DepreciationMasterReferredHistoryAPIController extends AppBaseController
{
    /** @var  DepreciationMasterReferredHistoryRepository */
    private $depreciationMasterReferredHistoryRepository;

    public function __construct(DepreciationMasterReferredHistoryRepository $depreciationMasterReferredHistoryRepo)
    {
        $this->depreciationMasterReferredHistoryRepository = $depreciationMasterReferredHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/depreciationMasterReferredHistories",
     *      summary="Get a listing of the DepreciationMasterReferredHistories.",
     *      tags={"DepreciationMasterReferredHistory"},
     *      description="Get all DepreciationMasterReferredHistories",
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
     *                  @SWG\Items(ref="#/definitions/DepreciationMasterReferredHistory")
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
        $this->depreciationMasterReferredHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->depreciationMasterReferredHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $depreciationMasterReferredHistories = $this->depreciationMasterReferredHistoryRepository->all();

        return $this->sendResponse($depreciationMasterReferredHistories->toArray(), trans('custom.depreciation_master_referred_histories_retrieved_s'));
    }

    /**
     * @param CreateDepreciationMasterReferredHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/depreciationMasterReferredHistories",
     *      summary="Store a newly created DepreciationMasterReferredHistory in storage",
     *      tags={"DepreciationMasterReferredHistory"},
     *      description="Store DepreciationMasterReferredHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DepreciationMasterReferredHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DepreciationMasterReferredHistory")
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
     *                  ref="#/definitions/DepreciationMasterReferredHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDepreciationMasterReferredHistoryAPIRequest $request)
    {
        $input = $request->all();

        $depreciationMasterReferredHistories = $this->depreciationMasterReferredHistoryRepository->create($input);

        return $this->sendResponse($depreciationMasterReferredHistories->toArray(), trans('custom.depreciation_master_referred_history_saved_success'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/depreciationMasterReferredHistories/{id}",
     *      summary="Display the specified DepreciationMasterReferredHistory",
     *      tags={"DepreciationMasterReferredHistory"},
     *      description="Get DepreciationMasterReferredHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DepreciationMasterReferredHistory",
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
     *                  ref="#/definitions/DepreciationMasterReferredHistory"
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
        /** @var DepreciationMasterReferredHistory $depreciationMasterReferredHistory */
        $depreciationMasterReferredHistory = $this->depreciationMasterReferredHistoryRepository->findWithoutFail($id);

        if (empty($depreciationMasterReferredHistory)) {
            return $this->sendError(trans('custom.depreciation_master_referred_history_not_found'));
        }

        return $this->sendResponse($depreciationMasterReferredHistory->toArray(), trans('custom.depreciation_master_referred_history_retrieved_suc'));
    }

    /**
     * @param int $id
     * @param UpdateDepreciationMasterReferredHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/depreciationMasterReferredHistories/{id}",
     *      summary="Update the specified DepreciationMasterReferredHistory in storage",
     *      tags={"DepreciationMasterReferredHistory"},
     *      description="Update DepreciationMasterReferredHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DepreciationMasterReferredHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DepreciationMasterReferredHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DepreciationMasterReferredHistory")
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
     *                  ref="#/definitions/DepreciationMasterReferredHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDepreciationMasterReferredHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var DepreciationMasterReferredHistory $depreciationMasterReferredHistory */
        $depreciationMasterReferredHistory = $this->depreciationMasterReferredHistoryRepository->findWithoutFail($id);

        if (empty($depreciationMasterReferredHistory)) {
            return $this->sendError(trans('custom.depreciation_master_referred_history_not_found'));
        }

        $depreciationMasterReferredHistory = $this->depreciationMasterReferredHistoryRepository->update($input, $id);

        return $this->sendResponse($depreciationMasterReferredHistory->toArray(), trans('custom.depreciationmasterreferredhistory_updated_successf'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/depreciationMasterReferredHistories/{id}",
     *      summary="Remove the specified DepreciationMasterReferredHistory from storage",
     *      tags={"DepreciationMasterReferredHistory"},
     *      description="Delete DepreciationMasterReferredHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DepreciationMasterReferredHistory",
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
        /** @var DepreciationMasterReferredHistory $depreciationMasterReferredHistory */
        $depreciationMasterReferredHistory = $this->depreciationMasterReferredHistoryRepository->findWithoutFail($id);

        if (empty($depreciationMasterReferredHistory)) {
            return $this->sendError(trans('custom.depreciation_master_referred_history_not_found'));
        }

        $depreciationMasterReferredHistory->delete();

        return $this->sendResponse($id, trans('custom.depreciation_master_referred_history_deleted_succe'));
    }

    public function getAllDepreciationAmendHistory(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $assetCositng = DepreciationMasterReferredHistory::with(['depperiod_by' => function ($query) use ($input) {
            $query->selectRaw('SUM(depAmountRpt) as depAmountRpt,SUM(depAmountLocal) as depAmountLocal,depMasterAutoID');
            $query->groupBy('depMasterAutoID');
        }])->where('depMasterAutoID', $input['depMasterAutoID']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $assetCositng = $assetCositng->where(function ($query) use ($search) {
                $query->where('depCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($assetCositng)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('depMasterReferredID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }


    public function assetDepreciationHistoryByID(Request $request)
    {
        $fixedAssetDepreciationMaster = $this->depreciationMasterReferredHistoryRepository->with(['confirmed_by'])->findWithoutFail($request->depMasterAutoID);
        if (empty($fixedAssetDepreciationMaster)) {
            return $this->sendError(trans('custom.fixed_asset_depreciation_master_not_found'));
        }

        return $this->sendResponse($fixedAssetDepreciationMaster->toArray(), trans('custom.fixed_asset_master_retrieved_successfully'));

    }


}
