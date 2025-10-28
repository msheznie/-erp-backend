<?php
/**
 * =============================================
 * -- File Name : ItemReturnMasterRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Item Return Master Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 06 - December 2018
 * -- Description : This file contains the all CRUD for Item Return Master Reffered Back
 * -- REVISION HISTORY
 * -- Date: 06-December 2018 By: Fayas Description: Added new functions named as getReferBackHistoryByMaterielReturn()
 *
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemReturnMasterRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateItemReturnMasterRefferedBackAPIRequest;
use App\Models\ItemReturnMasterRefferedBack;
use App\Repositories\ItemReturnMasterRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemReturnMasterRefferedBackController
 * @package App\Http\Controllers\API
 */

class ItemReturnMasterRefferedBackAPIController extends AppBaseController
{
    /** @var  ItemReturnMasterRefferedBackRepository */
    private $itemReturnMasterRefferedBackRepository;

    public function __construct(ItemReturnMasterRefferedBackRepository $itemReturnMasterRefferedBackRepo)
    {
        $this->itemReturnMasterRefferedBackRepository = $itemReturnMasterRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemReturnMasterRefferedBacks",
     *      summary="Get a listing of the ItemReturnMasterRefferedBacks.",
     *      tags={"ItemReturnMasterRefferedBack"},
     *      description="Get all ItemReturnMasterRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/ItemReturnMasterRefferedBack")
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
        $this->itemReturnMasterRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->itemReturnMasterRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemReturnMasterRefferedBacks = $this->itemReturnMasterRefferedBackRepository->all();

        return $this->sendResponse($itemReturnMasterRefferedBacks->toArray(), trans('custom.item_return_master_reffered_backs_retrieved_succes'));
    }

    /**
     * @param CreateItemReturnMasterRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemReturnMasterRefferedBacks",
     *      summary="Store a newly created ItemReturnMasterRefferedBack in storage",
     *      tags={"ItemReturnMasterRefferedBack"},
     *      description="Store ItemReturnMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemReturnMasterRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemReturnMasterRefferedBack")
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
     *                  ref="#/definitions/ItemReturnMasterRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemReturnMasterRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $itemReturnMasterRefferedBacks = $this->itemReturnMasterRefferedBackRepository->create($input);

        return $this->sendResponse($itemReturnMasterRefferedBacks->toArray(), trans('custom.item_return_master_reffered_back_saved_successfull'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemReturnMasterRefferedBacks/{id}",
     *      summary="Display the specified ItemReturnMasterRefferedBack",
     *      tags={"ItemReturnMasterRefferedBack"},
     *      description="Get ItemReturnMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnMasterRefferedBack",
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
     *                  ref="#/definitions/ItemReturnMasterRefferedBack"
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
        /** @var ItemReturnMasterRefferedBack $itemReturnMasterRefferedBack */
        $itemReturnMasterRefferedBack = $this->itemReturnMasterRefferedBackRepository->with(['confirmed_by', 'created_by', 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }])->findWithoutFail($id);

        if (empty($itemReturnMasterRefferedBack)) {
            return $this->sendError(trans('custom.item_return_master_reffered_back_not_found'));
        }

        return $this->sendResponse($itemReturnMasterRefferedBack->toArray(), trans('custom.item_return_master_reffered_back_retrieved_success'));
    }

    /**
     * @param int $id
     * @param UpdateItemReturnMasterRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemReturnMasterRefferedBacks/{id}",
     *      summary="Update the specified ItemReturnMasterRefferedBack in storage",
     *      tags={"ItemReturnMasterRefferedBack"},
     *      description="Update ItemReturnMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnMasterRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemReturnMasterRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemReturnMasterRefferedBack")
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
     *                  ref="#/definitions/ItemReturnMasterRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemReturnMasterRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemReturnMasterRefferedBack $itemReturnMasterRefferedBack */
        $itemReturnMasterRefferedBack = $this->itemReturnMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemReturnMasterRefferedBack)) {
            return $this->sendError(trans('custom.item_return_master_reffered_back_not_found'));
        }

        $itemReturnMasterRefferedBack = $this->itemReturnMasterRefferedBackRepository->update($input, $id);

        return $this->sendResponse($itemReturnMasterRefferedBack->toArray(), trans('custom.itemreturnmasterrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemReturnMasterRefferedBacks/{id}",
     *      summary="Remove the specified ItemReturnMasterRefferedBack from storage",
     *      tags={"ItemReturnMasterRefferedBack"},
     *      description="Delete ItemReturnMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemReturnMasterRefferedBack",
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
        /** @var ItemReturnMasterRefferedBack $itemReturnMasterRefferedBack */
        $itemReturnMasterRefferedBack = $this->itemReturnMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($itemReturnMasterRefferedBack)) {
            return $this->sendError(trans('custom.item_return_master_reffered_back_not_found'));
        }

        $itemReturnMasterRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.item_return_master_reffered_back_deleted_successfu'));
    }

    public function getReferBackHistoryByMaterielReturn(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'wareHouseLocation', 'month', 'year'));

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

        $itemReturnMaster = ItemReturnMasterRefferedBack::whereIn('companySystemID', $subCompanies)
            ->where('itemReturnAutoID',$input['id'])
            ->with(['created_by', 'warehouse_by', 'segment_by', 'customer_by']);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $itemReturnMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $itemReturnMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $itemReturnMaster->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('wareHouseLocation', $input)) {
            if ($input['wareHouseLocation'] && !is_null($input['wareHouseLocation'])) {
                $itemReturnMaster->where('wareHouseLocation', $input['wareHouseLocation']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $itemReturnMaster->whereMonth('ReturnDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $itemReturnMaster->whereYear('ReturnDate', '=', $input['year']);
            }
        }


        $itemReturnMaster = $itemReturnMaster->select(
            ['itemReturnAutoID',
                'itemReturnAutoRefferedbackID',
                'itemReturnCode',
                'comment',
                'ReturnDate',
                'confirmedYN',
                'approved',
                'serviceLineSystemID',
                'documentSystemID',
                'confirmedByEmpSystemID',
                'createdUserSystemID',
                'confirmedDate',
                'approvedDate',
                'createdDateTime',
                'ReturnRefNo',
                'wareHouseLocation',
                'timesReferred'
            ]);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $itemReturnMaster = $itemReturnMaster->where(function ($query) use ($search) {
                $query->where('itemReturnCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($itemReturnMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('itemReturnAutoRefferedbackID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
