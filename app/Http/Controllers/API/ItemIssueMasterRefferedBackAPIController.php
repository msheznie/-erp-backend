<?php

/**
 * =============================================
 * -- File Name : ItemIssueMasterRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Item Issue Master RefferedBack
 * -- Author : Mohamed Fayas
 * -- Create date : 03 - December 2018
 * -- Description : This file contains the all CRUD for Item Issue Master RefferedBack
 * -- REVISION HISTORY
 * -- Date: 03-December 2018 By: Fayas Description: Added new functions named as getReferBackHistoryByMaterielIssues()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemIssueMasterRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateItemIssueMasterRefferedBackAPIRequest;
use App\Models\ItemIssueMasterRefferedBack;
use App\Repositories\ItemIssueMasterRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemIssueMasterRefferedBackController
 * @package App\Http\Controllers\API
 */

class ItemIssueMasterRefferedBackAPIController extends AppBaseController
{
    /** @var  ItemIssueMasterRefferedBackRepository */
    private $itemIssueMasterRefferedBackRepository;

    public function __construct(ItemIssueMasterRefferedBackRepository $itemIssueMasterRefferedBackRepo)
    {
        $this->itemIssueMasterRefferedBackRepository = $itemIssueMasterRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemIssueMasterRefferedBacks",
     *      summary="Get a listing of the ItemIssueMasterRefferedBacks.",
     *      tags={"ItemIssueMasterRefferedBack"},
     *      description="Get all ItemIssueMasterRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/ItemIssueMasterRefferedBack")
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
        $this->itemIssueMasterRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->itemIssueMasterRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemIssueMasterRefferedBacks = $this->itemIssueMasterRefferedBackRepository->all();

        return $this->sendResponse($itemIssueMasterRefferedBacks->toArray(), trans('custom.item_issue_master_reffered_backs_retrieved_success'));
    }

    /**
     * @param CreateItemIssueMasterRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemIssueMasterRefferedBacks",
     *      summary="Store a newly created ItemIssueMasterRefferedBack in storage",
     *      tags={"ItemIssueMasterRefferedBack"},
     *      description="Store ItemIssueMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemIssueMasterRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemIssueMasterRefferedBack")
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
     *                  ref="#/definitions/ItemIssueMasterRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemIssueMasterRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $itemIssueMasterRefferedBacks = $this->itemIssueMasterRefferedBackRepository->create($input);

        return $this->sendResponse($itemIssueMasterRefferedBacks->toArray(), trans('custom.item_issue_master_reffered_back_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemIssueMasterRefferedBacks/{id}",
     *      summary="Display the specified ItemIssueMasterRefferedBack",
     *      tags={"ItemIssueMasterRefferedBack"},
     *      description="Get ItemIssueMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueMasterRefferedBack",
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
     *                  ref="#/definitions/ItemIssueMasterRefferedBack"
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
        /** @var ItemIssueMasterRefferedBack $itemIssueMasterRefferedBack */
        $itemIssueMasterRefferedBack = $this->itemIssueMasterRefferedBackRepository->with(['confirmed_by', 'created_by','customer_by','finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }])->findWithoutFail($id);

        if (empty($itemIssueMasterRefferedBack)) {
            return $this->sendError(trans('custom.item_issue_master_reffered_back_not_found'));
        }

        return $this->sendResponse($itemIssueMasterRefferedBack->toArray(), trans('custom.item_issue_master_reffered_back_retrieved_successf'));
    }

    /**
     * @param int $id
     * @param UpdateItemIssueMasterRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemIssueMasterRefferedBacks/{id}",
     *      summary="Update the specified ItemIssueMasterRefferedBack in storage",
     *      tags={"ItemIssueMasterRefferedBack"},
     *      description="Update ItemIssueMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueMasterRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemIssueMasterRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemIssueMasterRefferedBack")
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
     *                  ref="#/definitions/ItemIssueMasterRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemIssueMasterRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemIssueMasterRefferedBack $itemIssueMasterRefferedBack */
        $itemIssueMasterRefferedBack = $this->itemIssueMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemIssueMasterRefferedBack)) {
            return $this->sendError(trans('custom.item_issue_master_reffered_back_not_found'));
        }

        $itemIssueMasterRefferedBack = $this->itemIssueMasterRefferedBackRepository->update($input, $id);

        return $this->sendResponse($itemIssueMasterRefferedBack->toArray(), trans('custom.itemissuemasterrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemIssueMasterRefferedBacks/{id}",
     *      summary="Remove the specified ItemIssueMasterRefferedBack from storage",
     *      tags={"ItemIssueMasterRefferedBack"},
     *      description="Delete ItemIssueMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueMasterRefferedBack",
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
        /** @var ItemIssueMasterRefferedBack $itemIssueMasterRefferedBack */
        $itemIssueMasterRefferedBack = $this->itemIssueMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemIssueMasterRefferedBack)) {
            return $this->sendError(trans('custom.item_issue_master_reffered_back_not_found'));
        }

        $itemIssueMasterRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.item_issue_master_reffered_back_deleted_successful'));
    }


    public function getReferBackHistoryByMaterielIssues(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'wareHouseFrom', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyId'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $itemIssueMaster = ItemIssueMasterRefferedBack::whereIn('companySystemID', $subCompanies)
            ->where('itemIssueAutoID',$input['id'])
            ->with(['created_by', 'warehouse_by', 'segment_by', 'customer_by']);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $itemIssueMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $itemIssueMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $itemIssueMaster->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('wareHouseFrom', $input)) {
            if ($input['wareHouseFrom'] && !is_null($input['wareHouseFrom'])) {
                $itemIssueMaster->where('wareHouseFrom', $input['wareHouseFrom']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $itemIssueMaster->whereMonth('issueDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $itemIssueMaster->whereYear('issueDate', '=', $input['year']);
            }
        }


        $itemIssueMaster = $itemIssueMaster->select(
            ['itemIssueAutoRefferedbackID',
            'itemIssueAutoID',
            'itemIssueCode',
            'comment',
            'issueDate',
            'customerSystemID',
            'confirmedYN',
            'approved',
            'serviceLineSystemID',
            'documentSystemID',
            'confirmedByEmpSystemID',
            'createdUserSystemID',
            'confirmedDate',
            'approvedDate',
            'createdDateTime',
            'issueRefNo',
            'wareHouseFrom',
            'timesReferred'
            ]);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $itemIssueMaster = $itemIssueMaster->where(function ($query) use ($search) {
                $query->where('itemIssueCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%")
                    ->orWhere('issueRefNo', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($itemIssueMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('itemIssueAutoRefferedbackID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
