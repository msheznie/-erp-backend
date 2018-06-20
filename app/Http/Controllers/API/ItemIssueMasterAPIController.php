<?php
/**
=============================================
-- File Name : ItemIssueMasterAPIController.php
-- Project Name : ERP
-- Module Name :  Item Issue Master
-- Author : Mohamed Fayas
-- Create date : 20 - June 2018
-- Description : This file contains the all CRUD for Item Issue Master
-- REVISION HISTORY
-- Date: 20-June 2018 By: Fayas Description: Added new functions named as getAllMaterielIssuesByCompany(),getMaterielIssueFormData()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateItemIssueMasterAPIRequest;
use App\Http\Requests\API\UpdateItemIssueMasterAPIRequest;
use App\Models\ItemIssueMaster;
use App\Models\ItemIssueType;
use App\Models\Months;
use App\Models\SegmentMaster;
use App\Models\WarehouseMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\ItemIssueMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ItemIssueMasterController
 * @package App\Http\Controllers\API
 */

class ItemIssueMasterAPIController extends AppBaseController
{
    /** @var  ItemIssueMasterRepository */
    private $itemIssueMasterRepository;

    public function __construct(ItemIssueMasterRepository $itemIssueMasterRepo)
    {
        $this->itemIssueMasterRepository = $itemIssueMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemIssueMasters",
     *      summary="Get a listing of the ItemIssueMasters.",
     *      tags={"ItemIssueMaster"},
     *      description="Get all ItemIssueMasters",
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
     *                  @SWG\Items(ref="#/definitions/ItemIssueMaster")
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
        $this->itemIssueMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->itemIssueMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $itemIssueMasters = $this->itemIssueMasterRepository->all();

        return $this->sendResponse($itemIssueMasters->toArray(), 'Item Issue Masters retrieved successfully');
    }

    /**
     * @param CreateItemIssueMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/itemIssueMasters",
     *      summary="Store a newly created ItemIssueMaster in storage",
     *      tags={"ItemIssueMaster"},
     *      description="Store ItemIssueMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemIssueMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemIssueMaster")
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
     *                  ref="#/definitions/ItemIssueMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateItemIssueMasterAPIRequest $request)
    {
        $input = $request->all();

        $itemIssueMasters = $this->itemIssueMasterRepository->create($input);

        return $this->sendResponse($itemIssueMasters->toArray(), 'Item Issue Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/itemIssueMasters/{id}",
     *      summary="Display the specified ItemIssueMaster",
     *      tags={"ItemIssueMaster"},
     *      description="Get ItemIssueMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueMaster",
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
     *                  ref="#/definitions/ItemIssueMaster"
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
        /** @var ItemIssueMaster $itemIssueMaster */
        $itemIssueMaster = $this->itemIssueMasterRepository->findWithoutFail($id);

        if (empty($itemIssueMaster)) {
            return $this->sendError('Item Issue Master not found');
        }

        return $this->sendResponse($itemIssueMaster->toArray(), 'Item Issue Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateItemIssueMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/itemIssueMasters/{id}",
     *      summary="Update the specified ItemIssueMaster in storage",
     *      tags={"ItemIssueMaster"},
     *      description="Update ItemIssueMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ItemIssueMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ItemIssueMaster")
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
     *                  ref="#/definitions/ItemIssueMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateItemIssueMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var ItemIssueMaster $itemIssueMaster */
        $itemIssueMaster = $this->itemIssueMasterRepository->findWithoutFail($id);

        if (empty($itemIssueMaster)) {
            return $this->sendError('Item Issue Master not found');
        }

        $itemIssueMaster = $this->itemIssueMasterRepository->update($input, $id);

        return $this->sendResponse($itemIssueMaster->toArray(), 'ItemIssueMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/itemIssueMasters/{id}",
     *      summary="Remove the specified ItemIssueMaster from storage",
     *      tags={"ItemIssueMaster"},
     *      description="Delete ItemIssueMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ItemIssueMaster",
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
        /** @var ItemIssueMaster $itemIssueMaster */
        $itemIssueMaster = $this->itemIssueMasterRepository->findWithoutFail($id);

        if (empty($itemIssueMaster)) {
            return $this->sendError('Item Issue Master not found');
        }

        $itemIssueMaster->delete();

        return $this->sendResponse($id, 'Item Issue Master deleted successfully');
    }

    /**
     * get All Materiel Issues By Company
     * POST /getAllMaterielIssuesByCompany
     *
     * @param Request $request
     *
     * @return Response
     */

    public function getAllMaterielIssuesByCompany(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved','wareHouseFrom','month','year'));

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

        $itemIssueMaster = ItemIssueMaster::whereIn('companySystemID', $subCompanies)
                                      ->with(['created_by', 'warehouse_by','segment_by','customer_by']);


        if (array_key_exists('confirmedYN', $input)) {
            if(($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1)  && !is_null($input['confirmedYN'])) {
                $itemIssueMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if(($input['approved'] == 0 || $input['approved'] == -1 ) && !is_null($input['approved'])) {
                $itemIssueMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('serviceLineSystemID', $input)) {
            if($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $itemIssueMaster->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('wareHouseFrom', $input)) {
            if($input['wareHouseFrom'] && !is_null($input['wareHouseFrom'])) {
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
            ['erp_itemissuemaster.itemIssueAutoID',
                'erp_itemissuemaster.itemIssueCode',
                'erp_itemissuemaster.comment',
                'erp_itemissuemaster.issueDate',
                'erp_itemissuemaster.customerSystemID',
                'erp_itemissuemaster.confirmedYN',
                'erp_itemissuemaster.approved',
                'erp_itemissuemaster.serviceLineSystemID',
                'erp_itemissuemaster.documentSystemID',
                'erp_itemissuemaster.confirmedByEmpSystemID',
                'erp_itemissuemaster.createdUserSystemID',
                'erp_itemissuemaster.confirmedDate',
                'erp_itemissuemaster.approvedDate',
                'erp_itemissuemaster.createdDateTime',
                'erp_itemissuemaster.issueRefNo',
                'erp_itemissuemaster.wareHouseFrom'
            ]);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $itemIssueMaster = $itemIssueMaster->where(function ($query) use ($search) {
                $query->where('itemIssueCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($itemIssueMaster)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('itemIssueAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * get Materiel Issue Form Data
     * Get /getMaterielIssueFormData
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getMaterielIssueFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $segments = SegmentMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $segments = $segments->where('isActive', 1);
        }
        $segments = $segments->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = ItemIssueMaster::select(DB::raw("YEAR(createdDateTime) as year"))
                            ->whereNotNull('createdDateTime')
                            ->groupby('year')
                            ->orderby('year', 'desc')
                            ->get();

        /*$supplier = SupplierAssigned::select(DB::raw("supplierCodeSytem,CONCAT(primarySupplierCode, ' | ' ,supplierName) as supplierName"))
            ->where('companySystemID', $companyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();*/

        /*$currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

        $locations = Location::all();*/

        $wareHouseLocation = WarehouseMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $wareHouseLocation = $wareHouseLocation->where('isActive', 1);
        }
        $wareHouseLocation = $wareHouseLocation->get();

        $types = ItemIssueType::all();

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);


        $output = array('segments' => $segments,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            /*'currencies' => $currencies,
            'locations' => $locations,*/
            'wareHouseLocation' => $wareHouseLocation,
            'financialYears' => $financialYears,
            'types' => $types,
            'companyFinanceYear' => $companyFinanceYear
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }
}
