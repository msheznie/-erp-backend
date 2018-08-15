<?php
/**
 * =============================================
 * -- File Name : InventoryReclassificationAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Inventory
 * -- Author : Mohamed Mubashir
 * -- Create date : 10 - August 2018
 * -- Description : This file contains the all CRUD for Inventory Reclassification
 * -- REVISION HISTORY
 * -- Date: 14-March 2018 By: Description: Added new functions named as checkUser(),userCompanies()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateInventoryReclassificationAPIRequest;
use App\Http\Requests\API\UpdateInventoryReclassificationAPIRequest;
use App\Models\Company;
use App\Models\DocumentMaster;
use App\Models\InventoryReclassification;
use App\Models\SegmentMaster;
use App\Models\WarehouseMaster;
use App\Repositories\InventoryReclassificationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class InventoryReclassificationController
 * @package App\Http\Controllers\API
 */
class InventoryReclassificationAPIController extends AppBaseController
{
    /** @var  InventoryReclassificationRepository */
    private $inventoryReclassificationRepository;

    public function __construct(InventoryReclassificationRepository $inventoryReclassificationRepo)
    {
        $this->inventoryReclassificationRepository = $inventoryReclassificationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/inventoryReclassifications",
     *      summary="Get a listing of the InventoryReclassifications.",
     *      tags={"InventoryReclassification"},
     *      description="Get all InventoryReclassifications",
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
     *                  @SWG\Items(ref="#/definitions/InventoryReclassification")
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
        $this->inventoryReclassificationRepository->pushCriteria(new RequestCriteria($request));
        $this->inventoryReclassificationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $inventoryReclassifications = $this->inventoryReclassificationRepository->all();

        return $this->sendResponse($inventoryReclassifications->toArray(), 'Inventory Reclassifications retrieved successfully');
    }

    /**
     * @param CreateInventoryReclassificationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/inventoryReclassifications",
     *      summary="Store a newly created InventoryReclassification in storage",
     *      tags={"InventoryReclassification"},
     *      description="Store InventoryReclassification",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InventoryReclassification that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InventoryReclassification")
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
     *                  ref="#/definitions/InventoryReclassification"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateInventoryReclassificationAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $validator = \Validator::make($request->all(), [
            'serviceLineSystemID' => 'required',
            'narration' => 'required',
            'inventoryReclassificationDate' => 'required|date',
        ]);

        if ($validator->fails()) {//echo 'in';exit;
            return $this->sendError($validator->messages(), 422);
        }

        $companyFinanceYear = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYear["success"]) {
            return $this->sendError($companyFinanceYear["message"], 500);
        }

        $inputParam = $input;
        $inputParam["departmentSystmeID"] = 10;
        $companyFinancePeriod = \Helper::companyFinancePeriodCheck($inputParam);
        if (!$companyFinancePeriod["success"]) {
            return $this->sendError($companyFinancePeriod["message"], 500);
        } else{
            $input['FYBiggin'] = $companyFinancePeriod["message"]->dateFrom;
            $input['FYEnd'] = $companyFinancePeriod["message"]->dateTo;
        }

        unset($inputParam);
        $input['inventoryReclassificationDate'] = new Carbon($input['inventoryReclassificationDate']);

        $monthBegin = $input['FYBiggin'];
        $monthEnd = $input['FYEnd'];

        if (($input['inventoryReclassificationDate'] >= $monthBegin) && ($input['inventoryReclassificationDate'] <= $monthEnd)) {
        } else {
            return $this->sendError('Reclassification Date not between Financial period !');
        }

        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->companyID;
        }

        $documentMaster = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();
        if ($documentMaster) {
            $input['documentID'] = $company->documentID;
        }

        $input['createdPCid'] = gethostname();
        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();

        $inventoryReclassifications = $this->inventoryReclassificationRepository->create($input);

        return $this->sendResponse($inventoryReclassifications->toArray(), 'Inventory Reclassification saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/inventoryReclassifications/{id}",
     *      summary="Display the specified InventoryReclassification",
     *      tags={"InventoryReclassification"},
     *      description="Get InventoryReclassification",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InventoryReclassification",
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
     *                  ref="#/definitions/InventoryReclassification"
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
        /** @var InventoryReclassification $inventoryReclassification */
        $inventoryReclassification = $this->inventoryReclassificationRepository->findWithoutFail($id);

        if (empty($inventoryReclassification)) {
            return $this->sendError('Inventory Reclassification not found');
        }

        return $this->sendResponse($inventoryReclassification->toArray(), 'Inventory Reclassification retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateInventoryReclassificationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/inventoryReclassifications/{id}",
     *      summary="Update the specified InventoryReclassification in storage",
     *      tags={"InventoryReclassification"},
     *      description="Update InventoryReclassification",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InventoryReclassification",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="InventoryReclassification that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/InventoryReclassification")
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
     *                  ref="#/definitions/InventoryReclassification"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateInventoryReclassificationAPIRequest $request)
    {
        $input = $request->all();

        /** @var InventoryReclassification $inventoryReclassification */
        $inventoryReclassification = $this->inventoryReclassificationRepository->findWithoutFail($id);

        if (empty($inventoryReclassification)) {
            return $this->sendError('Inventory Reclassification not found');
        }

        $inventoryReclassification = $this->inventoryReclassificationRepository->update($input, $id);

        return $this->sendResponse($inventoryReclassification->toArray(), 'InventoryReclassification updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/inventoryReclassifications/{id}",
     *      summary="Remove the specified InventoryReclassification from storage",
     *      tags={"InventoryReclassification"},
     *      description="Delete InventoryReclassification",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of InventoryReclassification",
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
        /** @var InventoryReclassification $inventoryReclassification */
        $inventoryReclassification = $this->inventoryReclassificationRepository->findWithoutFail($id);

        if (empty($inventoryReclassification)) {
            return $this->sendError('Inventory Reclassification not found');
        }

        $inventoryReclassification->delete();

        return $this->sendResponse($id, 'Inventory Reclassification deleted successfully');
    }


    public function getAllInvReclassificationByCompany(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('segment_by', 'created_by'));

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

        $invReclassification = InventoryReclassification::with(['segment_by', 'created_by'])->whereIN('companySystemID', $subCompanies);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $invReclassification = $invReclassification->where(function ($query) use ($search) {
                $query->where('documentCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($invReclassification)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('inventoryreclassificationID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getInvReclassificationFormData(Request $request){

        $companyId = $request['companyId'];

        $segments = SegmentMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $segments = $segments->where('isActive', 1);
        }
        $segments = $segments->get();

        $wareHouseLocation = WarehouseMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $wareHouseLocation = $wareHouseLocation->where('isActive', 1);
        }
        $wareHouseLocation = $wareHouseLocation->get();

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $companyFinanceYear = \Helper::companyFinanceYear($companyId);

        $output = array(
            'segments' => $segments,
            'wareHouseLocation' => $wareHouseLocation,
            'financialYears' => $financialYears,
            'companyFinanceYear' => $companyFinanceYear
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

}
